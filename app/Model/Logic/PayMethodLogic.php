<?php

namespace App\Model\Logic;

use App\Lib\MyCode;
use App\Model\Entity\PayMethod;
use App\Rpc\Lib\KlineInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;

/**
 * Class PayMethodLogic
 * @package App\Model\Logic
 * @Bean()
 */
class PayMethodLogic {


    /**
     * @Reference(pool="system.pool")
     * @var KlineInterface
     */
    private $klineServer;

    /**
     * 计算支付金额
     * @param string $price
     * @param PayMethod $pay_method
     * @return array|string
     */
    public function calc_pay_amount(string $price, PayMethod $pay_method)
    {
        $amount=0;
        if ($pay_method->getPayName() == 'cny') {
            $amount = $price;
        } elseif ($pay_method->getPayName() == 'usdt'){
            $coin_usdt_price = $this->klineServer->get_last_close_price('usdt', 'cny');
            if ($coin_usdt_price <= 0) {
                return false;
            }
            $amount = bcdiv($price, $coin_usdt_price, 4);
        } elseif ($pay_method->getPayName() == 'fil'){
            $amount = $price;
        }
        return $amount;
    }

}
