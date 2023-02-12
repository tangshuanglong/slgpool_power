<?php

namespace App\Http\Controller\Api;

use App\Lib\MyCode;
use App\Lib\MyCommon;
use App\Lib\MyQuit;
use App\Model\Data\CoinData;
use App\Model\Data\PaginationData;
use App\Rpc\Lib\KlineInterface;
use App\Rpc\Lib\UserInterface;
use App\Rpc\Lib\WalletMiningInterface;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use Swoole\Coroutine\System;

/**
 * Class MiningController
 * @package App\Http\Controller\Api
 * @Controller(prefix="/v1/mining")
 */
class MiningController
{

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
     * @Reference(pool="auth.pool")
     * @var UserInterface
     */
    private $userServer;

    /**
     * @Inject()
     * @var MyCommon
     */
    private $myCommon;

    /**
     * @Reference(pool="system.pool")
     * @var KlineInterface
     */
    private $klineService;

    /**
     * 产品列表
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function products(Request $request)
    {
        $params = $request->get();
        validate($params, 'MiningValidator', ['product_type']);
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        $where = [
            'product_type' => $params['product_type'],
            'status_flag'  => 1,
        ];
        $data = PaginationData::table('power_product')
            ->select('id', 'is_pledge', 'work_number', 'product_init_work_day', 'coin_type', 'product_type', 'product_name', 'total_quantity',
                'last_quantity', 'product_hash', 'price', 'period', 'added_time', 'pledge', 'product_tag_ids',
                'is_limit_time', 'manage_fee', 'is_limited', 'start_time', 'end_time', 'order_num')
            ->where($where)
            ->forPage($page, $size)
            ->orderBy('order_num', 'desc')
            ->get();
        $cny_usdt_price = $this->klineService->get_last_close_price('usdt', 'cny');
        foreach ($data["data"] as $k => $v) {
            $data['data'][$k]['usdt_price'] = bcdiv($v['price'], $cny_usdt_price, 4);
            $data['data'][$k]['price'] = bcadd($v['price'], 0, 2);
            $data['data'][$k]['manage_fee'] = 100 - bcmul($v['manage_fee'], 100);
            //限时抢购
            if ($v['is_limit_time'] == 1) {
                $start_time = strtotime($v['start_time']);
                $end_time = strtotime($v['end_time']);
                //未开始
                if ($start_time > time()) {
                    $data['data'][$k]['buy_button'] = false;
                    $data['data'][$k]['limit_time_status'] = 1;
                    $data['data'][$k]['limit_time'] = $start_time - time();
                }elseif($start_time <= time() && time() <= $end_time){
                    //抢购中
                    $data['data'][$k]['buy_button'] = true;
                    $data['data'][$k]['limit_time_status'] = 2;
                    $data['data'][$k]['limit_time'] = $end_time - time();
                }elseif(time() > $end_time){
                    //已结束
                    $data['data'][$k]['buy_button'] = false;
                    $data['data'][$k]['limit_time_status'] = 3;
                    $data['data'][$k]['limit_time'] = 0;
                }
            } else {
                $data['data'][$k]['buy_button'] = true;
                $data['data'][$k]['limit_time_status'] = 0;
                $data['data'][$k]['limit_time'] = 0;
            }
            //币种图标
            $coin_data_info = CoinData::get_coin_info_by_coin_type($v['coin_type']);
            $data['data'][$k]['coin_icon'] = $coin_data_info['coin_icon'];
            //产品标签
            if ($v['product_tag_ids']) {
                $tag_list = DB::table('product_tag')->whereIn('id', explode(',', $v['product_tag_ids']))->orderBy('order_num', 'desc')->get()->toArray();
                if ($tag_list) {
                    foreach ($tag_list as $k1 => $v1) {
                        $tag_list[$k1]['url'] = MyCommon::get_filepath($v1['url']);
                    }
                    $data['data'][$k]['tag_list'] = $tag_list;
                }
            } else {
                $data['data'][$k]['tag_list'] = [];
            }
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 产品详情
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function product_detail(Request $request)
    {
        $params = $request->get();
        validate($params, 'MiningValidator', ['product_id']);
        $product = DB::table('power_product')
            ->where(['id' => $params['product_id'], 'status_flag' => 1])
            ->firstArray();
        if (!$product) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '产品不存在');
        }
        $product["manage_fee"] = 100 - bcmul($product["manage_fee"], 100);
        $cny_usdt_price = $this->klineService->get_last_close_price('usdt', 'cny');
        $product['price'] = bcadd($product['price'], 0, 2);
        $product['usdt_price'] = bcdiv($product['price'], $cny_usdt_price, 4);
        $fil_usdt_price = $this->klineService->get_last_close_price('fil', 'usdt');
        $usdt = bcmul($product['pledge'], $fil_usdt_price, 4);
        $product['cny_pledge_price'] = bcmul($usdt, $cny_usdt_price, 4);
        //限时抢购
        if ($product['is_limit_time'] == 1) {
            $start_time = strtotime($product['start_time']);
            $end_time = strtotime($product['end_time']);
            //未开始
            if ($start_time > time()) {
                $product['buy_button'] = false;
                $product['limit_time_status'] = 1;
                $product['limit_time'] = $start_time - time();
            }elseif($start_time <= time() && time() <= $end_time){
                //抢购中
                $product['buy_button'] = true;
                $product['limit_time_status'] = 2;
                $product['limit_time'] = $end_time - time();
            }elseif(time() > $end_time){
                //已结束
                $product['buy_button'] = false;
                $product['limit_time_status'] = 3;
                $product['limit_time'] = 0;
            }
        } else {
            $product['buy_button'] = true;
            $product['limit_time_status'] = 0;
            $product['limit_time'] = 0;
        }
        //币种图标
        $coin_data_info = CoinData::get_coin_info_by_coin_type($product['coin_type']);
        $product['coin_icon'] = $coin_data_info['coin_icon'];
        //产品标签
        if ($product['product_tag_ids']) {
            $tag_list = DB::table('product_tag')->whereIn('id', explode(',', $product['product_tag_ids']))->orderBy('order_num', 'desc')->get()->toArray();
            if ($tag_list) {
                foreach ($tag_list as $k1 => $v1) {
                    $tag_list[$k1]['url'] = MyCommon::get_filepath($v1['url']);
                }
                $product['tag_list'] = $tag_list;
            }
        } else {
            $product['tag_list'] = [];
        }
        //矿机
        $machine = DB::table('power_machine')->where(['id' => $product["mining_machine_id"]])->firstArray();
        $image = json_decode($machine['image'], true);
        unset($machine["image"]);
        if (is_array($image) && array_key_exists("0", $image)) {
            foreach ($image as $key => $value) {
                $machine['image'][] = MyCommon::get_filepath($value);
            }
        }
        $product["machine"] = $machine;
        return MyQuit::returnSuccess($product, MyCode::SUCCESS, 'success');
    }
}
