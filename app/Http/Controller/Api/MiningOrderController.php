<?php

namespace App\Http\Controller\Api;

use App\Http\Middleware\AuthMiddleware;
use App\Lib\MyCode;
use App\Lib\MyCommon;
use App\Lib\MyQuit;
use App\Model\Data\ConfigData;
use App\Model\Data\PaginationData;
use App\Model\Entity\Coin;
use App\Model\Entity\PayMethod;
use App\Model\Entity\PowerOrder;
use App\Model\Entity\PowerOrderPay;
use App\Model\Logic\PowerOrderLogic;
use App\Rpc\Lib\KlineInterface;
use App\Rpc\Lib\UserInterface;
use App\Rpc\Lib\WalletDwInterface;
use App\Rpc\Lib\WalletMiningInterface;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;

/**
 * Class MiningOrderController
 * @package App\Http\Controller\Api
 * @Controller(prefix="/v1/mining_order")
 * @Middleware(AuthMiddleware::class)
 */
class MiningOrderController
{
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
     * @Inject()
     * @var PowerOrderLogic
     */
    private $powerOrderLogic;

    /**
     * @Reference(pool="auth.pool")
     * @var UserInterface
     */
    private $userServer;

    /**
     * @Reference(pool="system.pool")
     * @var KlineInterface
     */
    private $klineServer;

    /**
     * 下单
     * @param Request $request
     * @return array
     * @throws DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::POST})
     */
    public function place(Request $request)
    {
        $params = $request->post();
        validate($params, 'MiningValidator', ['product_id', 'quantity']);
        $abnormal = $this->walletMiningServer->user_wallet_abnormal($request->uid);
        if ($abnormal) {
            return MyQuit::returnMessage(MyCode::WALLET_ABNORMAL, '钱包金额异常，请联系客服处理');
        }
        //验证资金密码
        if ($request->user_info['trade_pwd'] === '') {
            return MyQuit::returnMessage(MyCode::TRADE_PWD_NOT_SET, '请设置资金密码');
        }
        //验证是否有未支付的订单
        $exists_order = PowerOrder::where(['uid' => $request->uid, 'product_id' => $params['product_id'], 'order_status' => PowerOrderLogic::NOT_PAY])->exists();
        if ($exists_order) {
            return MyQuit::returnMessage(MyCode::EXISTS_TO_BY_PAY_ORDER, '您有未支付的订单，请支付后再重新下单');
        }
        return $this->powerOrderLogic->order_operate($request, $params['product_id'], $params['quantity']);
    }

    /**
     * 订单支付
     * @param Request $request
     * @return array
     * @throws DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::POST})
     */
    public function pay(Request $request)
    {
        $params = $request->post();
        validate($params, 'MiningValidator', ['order_id', 'trade_pwd', 'pay_method_id']);
        $params["pledge"] = $params["pledge"] ?? 0;//用户是否抵押参数

        $pay_method = PayMethod::find($params['pay_method_id']);
        if (!$pay_method) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '支付方式不存在');
        }
        //验证钱包
        $abnormal = $this->walletDwServer->user_wallet_abnormal($request->uid);
        if ($abnormal) {
            return MyQuit::returnMessage(MyCode::WALLET_ABNORMAL, '钱包金额异常，请联系客服处理');
        }
        //验证资金密码
        $verify_res = $this->userServer->verify_trade_pwd($request->uid, $params['trade_pwd']);
        if (!$verify_res) {
            return MyQuit::returnMessage(MyCode::TRADE_PWD_ERROR, '资金密码错误');
        }
        return $this->powerOrderLogic->order_pay($request, $params, $pay_method);
    }

    /**
     * 获取订单列表
     * @param Request $request
     * @return array
     * @throws DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function list(Request $request)
    {
        $params = $request->get();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        $where = [
            'uid' => $request->uid,
        ];
        if (isset($params['product_type']) && !empty($params['product_type'])) {
            $where['product_type'] = $params['product_type'];
        }
        $order_status = [-1, 0, 1, 2, 3, 4, 5, 6, 7];//-1等待抵押 0-等待矿机上架，1-服务中，2-未付款，3-已完成，4-转让中，5-已转让, 6-取消，7-转让中待支付
        if (isset($params['order_status'])) {
            validate($params, 'MiningValidator', ['order_status']);
            if ($params['order_status'] === 3) {
                $order_status = [3, 5];
            } elseif ($params['order_status'] === 4) {
                $order_status = [4, 7];
            } else {
                $order_status = [$params['order_status']];
            }
        }

        $data = PaginationData::table('power_order as po')
            ->select('id', 'order_number', 'product_type', 'product_name', 'buy_quantity', 'total_hash', 'total_price', 'period', 'manage_fee', 'shelf_date', 'order_status', 'created_at')
            ->where($where)
            ->whereIn('order_status', $order_status)
            ->forPage($page, $size)
            ->orderBy('id', 'desc')
            ->get();
        foreach ($data['data'] as &$item) {
            $item["manage_fee"] = bcmul(1 - $item["manage_fee"], 100, 0);
            $item['expiry_date'] = $item['shelf_date'] ? date('Y-m-d', strtotime($item['shelf_date']) + $item['period'] * 86400) : '';//到期日期
            //处理订单剩余支付时间
            $order_last_pay_time = (config('app.power_order_expire_time') / 1000) - (time() - strtotime($item['created_at']));
            $item['order_last_pay_time'] = $order_last_pay_time < 0 ? 0 : $order_last_pay_time;
            //合约周期-180天
            if($item['product_type'] == 3){
                $item['period'] = $item['period'] - 180;
            }
            unset($item);
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 获取订单详情
     * @param Request $request
     * @return array
     * @throws DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function detail(Request $request)
    {
        $params = $request->get();
        validate($params, 'MiningValidator', ['order_id']);

        $data = DB::table('power_order as po')
            ->select('po.*', 'pop.pay_amount', 'pm.pay_name')
            ->leftJoin('power_order_pay as pop', 'pop.order_id', '=', 'po.id')
            ->leftJoin('pay_method as pm', 'pop.pay_method_id', '=', 'pm.id')
            ->where(['po.id' => $params['order_id']])
            ->firstArray();
        //判断订单是否存在
        if (!$data) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '订单不存在');
        }
        //判断订单所属者
        if ($data['uid'] !== $request->uid) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '订单错误');
        }

        //处理usdt
        $usdt_cny = $this->klineServer->get_last_close_price('usdt', 'cny');
        $data['total_price_usdt'] = bcdiv($data['total_price'], $usdt_cny, 4);
        $data['expiry_date'] = $data['shelf_date'] ? date('Y-m-d', strtotime($data['shelf_date']) + $data['period'] * 86400) : '';//到期日期
        //处理订单剩余支付时间
        $order_last_pay_time = (config('app.power_order_expire_time') / 1000) - (time() - strtotime($data['created_at']));
        $data['order_last_pay_time'] = $order_last_pay_time < 0 ? 0 : $order_last_pay_time;
        //合约周期-180天
        if($data['product_type'] == 3){
            $data['period'] = $data['period'] - 180;
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 取消订单
     * @param Request $request
     * @return array
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::POST})
     */
    public function cancel(Request $request)
    {
        $params = $request->post();
        validate($params, 'MiningValidator', ['order_id']);
        $res = $this->powerOrderLogic->order_cancel($params['order_id'], $request->uid, powerOrderLogic::CANCEL);
        if ($res['res'] != 1) {
            return MyQuit::returnMessage($res['code'], $res['message']);
        }
        return MyQuit::returnMessage(MyCode::SUCCESS, 'success');
    }

    /**
     * 抵押
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::POST})
     */
    public function plede_pay(Request $request)
    {
        $params = $request->post();
        validate($params, 'MiningValidator', ['order_id', 'trade_pwd']);
        //验证资金密码
        $verify_res = $this->userServer->verify_trade_pwd($request->uid, $params['trade_pwd']);
        if (!$verify_res) {
            return MyQuit::returnMessage(MyCode::TRADE_PWD_ERROR, '资金密码错误');
        }

        $abnormal = $this->walletDwServer->user_wallet_abnormal($request->uid);
        if ($abnormal) {
            return MyQuit::returnMessage(MyCode::WALLET_ABNORMAL, '钱包金额异常，请联系客服处理');
        }

        $order = PowerOrder::find($params["order_id"]);
        if (!$order) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '订单不存在');
        }

        if ($order->getUid() !== $request->uid) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '订单错误');
        }
        $coin = Coin::find(25);
        // 计算订单金额
        $pledge_price = $order->getPledgePrice();
        if ($pledge_price === false) {
            return MyQuit::returnMessage(MyCode::SERVER_ERROR, '系统繁忙');
        }
        if ($order->getOrderStatus() != -1) {
            return MyQuit::returnMessage(MyCode::SERVER_ERROR, '订单状态不正确');
        }

        DB::beginTransaction();
        try {
            //修改订单状态
            $order->setOrderStatus(PowerOrderLogic::TO_BE_WORK);
            $res = $order->save();
            if (!$res) {
                throw new DbException('保存订单状态记录错误|' . MyCode::BALANCE_ERROR);
            }
            //添加支付记录
            $order_pay = PowerOrderPay::new();
            $order_pay->setOrderId($order->getId());
            $order_pay->setPayMethodId($coin->getId());
            $order_pay->setPayAmount($pledge_price);
            $order_pay->setType(3); //购买
            $res = $order_pay->save();
            if (!$res) {
                throw new DbException('保存支付记录错误|' . MyCode::BALANCE_ERROR);
            }

            if ($pledge_price > 0) {
                // 处理用户抵押处理 转移到抵押额度钱包
                $fil_wallet = $this->walletDwServer->get_wallet_free($request->uid, $coin->getId());
                if ($fil_wallet < $pledge_price) {
                    throw new DbException('用户抵押FIL钱包余额不足|' . MyCode::BALANCE_ERROR);
                }

                $wallet_frozen_res = $this->walletDwServer->deduct_wallet_free_to_pledge($request->uid, $pledge_price, $coin->getId(), 'pledge');
                if (!$wallet_frozen_res) {
                    throw new DbException('用户抵押FIL钱包余额不足|' . MyCode::BALANCE_ERROR);
                }

                //TODO 记录日志
            }
            DB::commit();

            return MyQuit::returnSuccess(['pay_price' => $pledge_price, 'coin' => $coin->getCoinNameEn()], MyCode::SUCCESS, 'success');
        } catch (DbException $e) {
            MyCommon::write_log($e->getMessage(), config('log_path'));
            DB::rollBack();

            $err = explode('|', $e->getMessage());
            return MyQuit::returnMessage(trim($err[1], ' '), trim($err[0], ' '));
        }

    }

    /**
     * 产品购买金额换算
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function price(Request $request)
    {
        $params = $request->get();
        validate($params, 'MiningValidator', ['product_id']);
        $product = DB::table('power_product')
            ->where(['id' => $params['product_id'], 'status_flag' => 1])
            ->firstArray();
        if (!$product) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '产品不存在');
        }

        $pay_method = PayMethod::find($params['pay_method_id']);
        if (!$pay_method) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '支付方式不存在');
        }
        $abnormal = $this->walletDwServer->user_wallet_abnormal($request->uid);
        if ($abnormal) {
            return MyQuit::returnMessage(MyCode::WALLET_ABNORMAL, '钱包金额异常，请联系客服处理');
        }
        $price = 0;
        $pledge_price = 0;

        switch ($pay_method->getPayName()) {
            case 'usdt':
                $cny_usdt_price = $this->klineServer->get_last_close_price('usdt', 'cny');
                $price = bcdiv($product['price'], $cny_usdt_price, 4);
                //FIL转Usdt
                $fil_usdt_price = $this->klineServer->get_last_close_price('fil', 'usdt');
                $pledge_price = bcdiv($product['pledge'], $fil_usdt_price, 4);

                break;
            case 'cny':
                $price = $product['price'];
                $cny_usdt_price = $this->klineServer->get_last_close_price('usdt', 'cny');

                //FIL转Usdt
                $fil_usdt_price = $this->klineServer->get_last_close_price('fil', 'usdt');
                $usdt = bcdiv($product['pledge'], $fil_usdt_price, 4);
                $pledge_price = bcdiv($usdt, $cny_usdt_price, 4);

                break;
            case 'fil':
                break;
        }

        $data = [
            "price"        => $price,
            "pledge_price" => $pledge_price,
            "coin"         => $pay_method->getPayName(),
        ];
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 我的算力/矿机列表
     * @param Request $request
     * @return array
     * @throws DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function my_hash(Request $request)
    {
        $params = $request->get();
        validate($params, 'MiningValidator', ['product_type']);
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        $where = [
            'uid'          => $request->uid,
            'order_status' => 1,
            'product_type' => $params['product_type'],
        ];

        $data = PaginationData::table('power_order')
            ->where($where)
            ->forPage($page, $size)
            ->orderBy('id', 'desc')
            ->get();

        $config = ConfigData::config_info('can_resale_days', 'mining');
        $usdt_cny = $this->klineServer->get_last_close_price('usdt', 'cny');
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key] = $this->powerOrderLogic->order_logic($val, $config['value'], $usdt_cny);
            $data['data'][$key]['manage_fee'] = 100 - bcmul($val['manage_fee'], 100);
            $data['data'][$key]['expiry_date'] = $val['shelf_date'] ? date('Y-m-d', strtotime($val['shelf_date']) + $val['period'] * 86400) : '';//到期日期
            //合约周期-180天
            if($val['product_type'] == 3){
                $data['data'][$key]['period'] = $val['period'] - 180;
            }
            unset($data['data'][$key]['uid']);
        }
        //统计总算力
        $total_hash = DB::table('power_order')->where($where)->sum('valid_hash');
        $data['total_hash'] = bcadd(round($total_hash, 4), 0, 4);

        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }
}
