<?php

namespace App\Rpc\Lib;

/**
 * Interface VerifyCodeInterface
 */
interface MiningOrderInterface
{

    /**
     * 订单未支付 取消
     * @param int $order_id
     * @param int $order_status
     * @return mixed
     */
    public function order_not_pay(int $order_id);

}
