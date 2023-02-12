<?php

namespace App\Model\Logic;

use App\Common\WalletMiningClient;
use App\Lib\MyCode;
use App\Lib\MyCommon;
use App\Lib\MyQuit;
use App\Lib\MyRabbitMq;
use App\Lib\MyRedisHelper;
use App\Model\Data\CoinData;
use App\Model\Data\PowerData;
use App\Model\Entity\PayMethod;
use App\Model\Entity\PowerOrder;
use App\Model\Entity\PowerOrderPay;
use App\Model\Entity\PowerProduct;
use App\Rpc\Lib\CoinInterface;
use App\Rpc\Lib\KlineInterface;
use App\Rpc\Lib\WalletDwInterface;
use App\Rpc\Lib\WalletMiningInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Http\Message\Request;
use Swoft\Redis\Redis;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Class PowerOrderLogic
 * @package App\Model\Logic
 * @Bean()
 */
class PowerOrderLogic
{
    //-1待抵押 0-待上架，1-服务中，2-未付款，3-已完成，4-转让中，5-已转让, 6-取消，7-转让中待支付
    const PAY_COMPLETE_BUT_NOTPAY_PLEDGE = -1;
    const TO_BE_WORK = 0;
    const PAY_COMPLETE = 1;
    const NOT_PAY = 2;
    const COMPLETE = 3;
    const RESELL = 4;
    const RESELLED = 5;
    const CANCEL = 6;
    const PENDING_PAY_IN_RESELL = 7;

    /**
     * @Inject()
     * @var MyRabbitMq
     */
    private $myRabbitMq;

    /**
     * @Reference(pool="system.pool")
     * @var KlineInterface
     */
    private $klineServer;

    /**
     * @Reference(pool="user.pool")
     * @var WalletMiningInterface
     */
    private $walletMiningServer;

    /**
     * @Reference(pool="user.pool")
     * @var WalletDwInterface
     */
    private $walletDwServer;

    /**
     * @Reference(pool="system.pool")
     * @var CoinInterface
     */
    private $coinServer;

    /**
     * @Inject()
     * @var PayMethodLogic
     */
    private $payMethodLogic;

    /**
     * @Inject()
     * @var WalletMiningClient
     */
    private $walletMiningClient;

    /**
     * 订单操作
     * @param Request $request
     * @param int $product_id
     * @param int $quantity
     * @return array
     * @throws DbException
     */
    public function order_operate(Request $request, int $product_id, int $quantity)
    {
        //加锁
        $order_key = config('app.power_product_lock') . $product_id;
        $lock_token = MyRedisHelper::lock($order_key, 2);
        if ($lock_token === false) {
            return MyQuit::returnMessage(MyCode::TOO_HOT, '该产品太火爆了，请稍后重试！');
        }

        $product = PowerProduct::find($product_id);
        //判断产品是否存在
        if (!$product) {
            MyRedisHelper::unLock($order_key, $lock_token);//解锁
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '产品不存在');
        }
        //判断库存
        if ($product->getLastQuantity() < $quantity) {
            MyRedisHelper::unLock($order_key, $lock_token); //解锁
            return MyQuit::returnMessage(MyCode::STOCK_SHORTAGE, '库存不足');
        }
        //判断限购
        if ($product->getIsLimited() === 1) {
            if ($quantity > $product->getLimited()) {
                MyRedisHelper::unLock($order_key, $lock_token);//解锁
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '超过限购数量');
            }
            $field = (string)$request->uid;
            $already_buy_quantity = round(Redis::hGet(config('app.power_product_limited') . $product->getId(), $field));
            if ($product->getLimited() - $already_buy_quantity < $quantity) {
                MyRedisHelper::unLock($order_key, $lock_token);//解锁
                return MyQuit::returnMessage(MyCode::LIMITED, '超过限购数量');
            }
        }
        //判断限时抢购
        if ($product->getIsLimitTime() == 1) {
            $start_time = strtotime($product->getStartTime());
            $end_time = strtotime($product->getEndTime());
            if ($start_time > time() || time() > $end_time) {
                MyRedisHelper::unLock($order_key, $lock_token);//解锁
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '不在限时抢购时间范围内');
            }
        }
        //判断体验产品是否购买
        if ($product->getProductType() == 2 && $product->getIsExperience() == 1) {
            //限购为1
            if($quantity > 1){
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '体验产品限购一份');
            }
            $is_buy = PowerData::is_buy($request->uid);
            if ($is_buy == true) {
                MyRedisHelper::unLock($order_key, $lock_token); //解锁
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '你已不是新用户');
            }
        }
        //购买总算力
        $total_hash = bcmul($product->getProductHash(), $quantity, 4);
        //真实算力
        $real_hash = bcmul($product->getRealHash(), $quantity);
        //订单总价格
        $total_price = bcmul($product->getPrice(), $quantity, 2);
        //计算抵押金额
        $pledge_price = bcmul($product->getPledge(), $quantity, 4);
        //生成订单号
        $order_number = MyCommon::generate_order_number($request->uid);
        $data = [
            'order_number'  => $order_number,
            'uid'           => $request->uid,
            'product_id'    => $product->getId(),
            'product_name'  => $product->getProductName(),
            'coin_type'     => $product->getCoinType(),
            'buy_quantity'  => $quantity,
            'total_hash'    => $total_hash,
            'price'         => $product->getPrice(),
            'total_price'   => $total_price,
            'period'        => $product->getPeriod(),
            'manage_fee'    => $product->getManageFee(),
            'added_time'    => $product->getAddedTime(),
            'is_resell'     => $product->getIsResell(),
            'is_experience' => $product->getIsExperience(),
            'product_type'  => $product->getProductType(),
            'is_pledge'     => $product->getIsPledge(),
            'pledge_price'  => $pledge_price,
            'work_number'   => $product->getWorkNumber(),
            'order_status'  => self::NOT_PAY,
            'real_hash'     => $real_hash
        ];
        DB::beginTransaction();
        try {
            $id = PowerOrder::insertGetId($data);
            if (!$id) {
                throw new DbException('insert order error');
            }
            //修改库存
            $last_quantity = $product->getLastQuantity() - $quantity;
            $product->setLastQuantity($last_quantity);
            $save_res = $product->save();
            if (!$save_res) {
                throw new DbException('update product error');
            }
            //添加订单超时未付款延时队列
            $res = $this->myRabbitMq->push_delay_quoue(
                config('app.power_order_expire_cancel_cache'),
                config('app.power_order_expire_cancel'),
                ['id' => $id],
                config('app.power_order_expire_time')
            );
            if ($res === false) {
                throw new DbException('push delay queue error');
            }
            DB::commit();
            MyRedisHelper::unLock($order_key, $lock_token); //解锁
            Redis::hIncrBy(config('app.power_product_limited') . $product->getId(), (string)$request->uid, $quantity);
            return MyQuit::returnSuccess(['id' => $id], MyCode::SUCCESS, 'success');
        } catch (DbException $e) {
            MyCommon::write_log($e->getMessage(), config('log_path'));
            DB::rollBack();
            MyRedisHelper::unLock($order_key, $lock_token);//解锁
            return MyQuit::returnMessage(MyCode::SERVER_ERROR, '服务器错误');
        }
    }

    /**
     * 订单支付
     * @param Request $request
     * @param array $params 参数
     * @param PayMethod $pay_method
     * @return array
     * @throws DbException
     */
    public function order_pay(Request $request, array $params, PayMethod $pay_method)
    {
        //加锁
        $order_key = config('app.power_order_lock') . $params['order_id'];
        $lock_token = MyRedisHelper::lock($order_key);
        if (!$lock_token) {
            return MyQuit::returnMessage(MyCode::SERVER_BUSY, '服务器繁忙，请稍后重试！');
        }

        $order = PowerOrder::find($params['order_id']);
        //判断订单是否存在
        if (!$order) {
            MyRedisHelper::unLock($order_key, $lock_token);
            DB::rollBack();
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '订单不存在');
        }
        //判断订单所属者
        if ($order->getUid() !== $request->uid) {
            MyRedisHelper::unLock($order_key, $lock_token);
            DB::rollBack();
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '订单错误');
        }
        //判断订单状态
        if ($order->getOrderStatus() !== self::NOT_PAY) {
            MyRedisHelper::unLock($order_key, $lock_token);
            return MyQuit::returnMessage(MyCode::ORDER_ALREADY_PAY, '订单已经支付或已经取消');
        }
        //存币送算力产品支付方式必须为fil
        if ($order->getProductType() == 3) {
            if ($pay_method['pay_name'] != 'fil') {
                MyRedisHelper::unLock($order_key, $lock_token);
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '支付方式错误');
            }
        }
        //判断体验产品是否购买
        if ($order->getProductType() == 2 && $order->getIsExperience()) {
            $is_buy = PowerData::is_buy($order->getUid());
            if ($is_buy == true) {
                MyRedisHelper::unLock($order_key, $lock_token);
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '你已不是新用户');
            }
        }
        // 计算订单金额
        $amount = $this->payMethodLogic->calc_pay_amount($order->getTotalPrice(), $pay_method);
        if ($amount === false) {
            MyRedisHelper::unLock($order_key, $lock_token);
            DB::rollBack();
            return MyQuit::returnMessage(MyCode::SERVER_ERROR, '系统繁忙');
        }

        DB::beginTransaction();
        try {
            //判断订单是否需要抵押
            if ($order->getIsPledge()) {
                //立即抵押
                if ($params['pledge'] == 1) {
                    $pledge_price = $order->getPledgePrice();
                    if ($pledge_price > 0) {
                        // 查找FIL 钱包id
                        $pay_method_fil = DB::table('coin')->where(['coin_name_en' => 'fil'])->firstArray();
                        // 处理用户抵押处理 转移到抵押额度钱包
                        $fil_wallet = $this->walletDwServer->get_wallet_free($request->uid, $pay_method_fil["id"]);
                        if ($fil_wallet < $pledge_price) {
                            throw new DbException('Fil币不足|' . MyCode::BALANCE_ERROR);
                        }
                        $trade_type = 'pledge';
                        if ($order->getProductType() == 1) {
                            $trade_type = 'miner_pledge';
                        }
                        $wallet_frozen_res = $this->walletDwServer->deduct_wallet_free_to_pledge($request->uid, $pledge_price, $pay_method_fil["id"], $trade_type);
                        if (!$wallet_frozen_res) {
                            throw new DbException('用户抵押FIL钱包余额不足|' . MyCode::BALANCE_ERROR);
                        }
                    } else {
                        throw new DbException('未找到需要抵押的金额|' . MyCode::SERVER_ERROR);
                    }
                    //修改订单状态为“待上架”
                    $order->setOrderStatus(self::TO_BE_WORK);
                } else {
                    //修改订单状态为“待抵押”
                    $order->setOrderStatus(self::PAY_COMPLETE_BUT_NOTPAY_PLEDGE);
                }
            } else {
                //修改订单状态为“待上架”
                $order->setOrderStatus(self::TO_BE_WORK);
            }
            $res = $order->save();
            if (!$res) {
                throw new DbException('保存订单状态记录错误|' . MyCode::SERVER_ERROR);
            }

            // 添加支付记录
            $order_pay = PowerOrderPay::new();
            $order_pay->setOrderId($order->getId());
            $order_pay->setPayMethodId($pay_method->getId());
            $order_pay->setPayAmount($amount);
            $order_pay->setType(1); //购买
            $res = $order_pay->save();
            if (!$res) {
                throw new DbException('保存支付记录错误|' . MyCode::SERVER_ERROR);
            }

            if ($order->getProductType() == 3) {
                //添加余额变化
                $wallet_res = $this->walletDwServer->append_wallet_frozen($order->getUid(), $amount, $pay_method['coin_id'], 'store_coin');
                if ($wallet_res === false) {
                    throw new DbException('钱包余额不足|' . MyCode::BALANCE_ERROR);
                }
                //添加存币冻结表
                $coin_store_log_data = [
                    'uid'         => $order->getUid(),
                    'order_id'    => $order->getId(),
                    'coin_id'     => $pay_method['coin_id'],
                    'coin_name'   => $pay_method['pay_name'],
                    'coin_amount' => $amount,
                    'send_hash'   => $order->getTotalHash(),
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s')
                ];
                $coin_store_log_res = DB::table('coin_store_log')->insert($coin_store_log_data);
                if ($coin_store_log_res === false) {
                    throw new DbException('添加存币冻结表失败|' . MyCode::SERVER_ERROR);
                }
            } else {
                if ($amount > 0) {
                    //扣除用户余额
                    $wallet_res = $this->walletDwServer->deduct_wallet_free($request->uid, $amount, $pay_method->getCoinId(), 'pay');
                    if ($wallet_res === false) {
                        throw new DbException('钱包余额不足|' . MyCode::BALANCE_ERROR);
                    }
                }
            }
            //处理体验金
            $coin_id = CoinData::get_coin_id('fil');
            $wallet = $this->walletDwServer->get_wallet_experience($request->uid, $coin_id);
            if ($wallet > 0) {
                $wallet_res = $this->walletDwServer->return_wallet_experience($request->uid, $wallet, $coin_id, 'mining_income_return');
                if ($wallet_res === false) {
                    throw new DbException('钱包体验金不足|' . MyCode::BALANCE_ERROR);
                }
            }
            DB::commit();

            //添加异步统计信息等操作
            $res = $this->myRabbitMq->push(config('app.power_order_pay'), ['order_id' => $order->getId()]);
            if (!$res) {
                MyCommon::write_log('添加至订单支付成功后处理队列失败 order_id = ' . $order->getId(), config('log_path'));
            }

            MyRedisHelper::unLock($order_key, $lock_token);
            // 获取订单信息返回给前端
            return MyQuit::returnSuccess(['pay_price' => $amount, 'coin' => $pay_method->getPayName()], MyCode::SUCCESS, 'success');
        } catch (DbException $e) {
            MyCommon::write_log($e->getMessage(), config('log_path'));
            DB::rollBack();
            MyRedisHelper::unLock($order_key, $lock_token);

            $err = explode('|', $e->getMessage());
            if (!isset($err[1])) {
                return MyQuit::returnMessage($e->getMessage(), $e->getCode());
            } else {
                return MyQuit::returnMessage(trim($err[1], ' '), trim($err[0], ' '));
            }
        }
    }

    /**
     * 订单 超时未支付 或 用户取消
     * @param int $id
     * @param int $uid
     * @param int $order_status
     * @return array
     */
    public function order_cancel(int $id, int $uid = 0, $order_status = self::CANCEL)
    {
        $order_key = config('app.power_order_lock') . $id;
        $lock_token = MyRedisHelper::lock($order_key);
        if (!$lock_token) {
            return ['res' => 0, 'code' => MyCode::SERVER_BUSY, 'message' => '服务器繁忙，请稍后重试！'];
        }
        //判断订单是否存在
        $order = PowerOrder::find($id);
        if (!$order) {
            MyRedisHelper::unLock($order_key, $lock_token);
            return ['res' => 0, 'code' => MyCode::PARAM_ERROR, 'message' => '订单不存在'];
        }
        //判断订单所属者
        if ($uid !== 0 && $order->getUid() !== $uid) {
            MyRedisHelper::unLock($order_key, $lock_token);
            return ['res' => 0, 'code' => MyCode::PARAM_ERROR, 'message' => '订单所属者错误'];
        }
        //只能是待付款状态
        if ($order->getOrderStatus() !== self::NOT_PAY) {
            MyRedisHelper::unLock($order_key, $lock_token);
            return ['res' => 0, 'code' => MyCode::ORDER_ALREADY_PAY, 'message' => '订单已经支付或已经取消'];
        }
        //获取商品信息
        $product_key = config('app.power_product_lock') . $order->getProductId();
        $product_lock_token = MyRedisHelper::lock($product_key);
        if (!$product_lock_token) {
            MyRedisHelper::unLock($order_key, $lock_token);
            return ['res' => 0, 'code' => MyCode::SERVER_BUSY, 'message' => '服务器繁忙，请稍后重试！'];
        }
        $product = PowerProduct::find($order->getProductId());
        DB::beginTransaction();
        try {
            //更新订单状态
            $order->setOrderStatus($order_status);
            $res = $order->save();
            if (!$res) {
                MyRedisHelper::unLock($order_key, $lock_token);
                MyRedisHelper::unLock($product_key, $product_lock_token);
                return ['res' => 0, 'code' => MyCode::SERVER_ERROR, 'message' => '更新订单状态失败'];
            }
            //是原始订单 通过产品下单
            if ($order->getOrderType() === 1) {
                //退回库存
                $last_quantity = $product->getLastQuantity() + $order->getBuyQuantity();
                $product->setLastQuantity($last_quantity);
                $product_res = $product->save();
                if (!$product_res) {
                    MyRedisHelper::unLock($order_key, $lock_token);
                    MyRedisHelper::unLock($product_key, $product_lock_token);
                    return ['res' => 0, 'code' => MyCode::SERVER_ERROR, 'message' => '更新产品剩余数量失败'];
                }
            }
            DB::commit();
            //解锁
            MyRedisHelper::unLock($order_key, $lock_token);
            MyRedisHelper::unLock($product_key, $product_lock_token);
            return ['res' => 1, 'code' => MyCode::SUCCESS, 'message' => '成功'];
        } catch (DbException $e) {
            MyCommon::write_log($e->getMessage(), config('log_path'));
            DB::rollBack();
            return ['res' => 0, 'code' => MyCode::SERVER_ERROR, 'message' => $e->getMessage()];
        }
    }

    /**
     * 订单列表数据处理
     * @param array $data
     * @param string $can_resell_days
     * @param string $usdt_cny
     * @return array
     * @throws DbException
     */
    public function order_logic(array $data, string $can_resell_days, string $usdt_cny)
    {
        $coin_usdt = $this->klineServer->get_last_close_price($data['coin_type'], 'usdt');
        $data['total_price_cny'] = bcadd($data['total_price'], 0, 2);
        return $data;
    }

}
