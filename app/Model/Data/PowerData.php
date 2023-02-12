<?php declare(strict_types=1);


namespace App\Model\Data;

use App\Lib\MyCommon;
use App\Model\Entity\PowerOrder;
use App\Model\Entity\ChiaOrder;
use App\Model\Entity\BzzOrder;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Redis\Redis;
use Swoft\Stdlib\Helper\JsonHelper;

class PowerData
{

    /**
     * 判断用户是否有购买记录
     * @param string $uid 用户id
     * @return boolean
     * @throws \Swoft\Db\Exception\DbException
     */
    public static function is_buy(int $uid)
    {
        $bool = false;
        $power_order = PowerOrder::where(['uid' => $uid])->whereNotIn('order_status', [2, 6])->exists();
        if($power_order){
            $bool = true;
        }
        $chia_order = ChiaOrder::where(['uid' => $uid])->whereNotIn('order_status', [2, 6])->exists();
        if($chia_order){
            $bool = true;
        }
        $bzz_order = BzzOrder::where(['uid' => $uid])->whereNotIn('order_status', [2, 6])->exists();
        if($bzz_order){
            $bool = true;
        }
        return $bool;
    }

}
