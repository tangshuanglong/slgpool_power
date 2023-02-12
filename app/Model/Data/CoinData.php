<?php declare(strict_types=1);


namespace App\Model\Data;

use App\Lib\MyCommon;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Redis\Redis;
use Swoft\Stdlib\Helper\JsonHelper;

class CoinData
{

    /**
     * 获取币种最新价格
     * @param string $coin_type
     * @param string $price_type
     * @return string
     * @throws \Swoft\Db\Exception\DbException
     */
    public static function get_coin_last_price(string $coin_type, string $price_type)
    {
        $field = strtolower($coin_type.$price_type);
        $res = Redis::hGet(config('app.coin_last_price_key'), $field);
        if (!$res) {
            $table_name = 'kline_'.$field.'_86400';
            $data = DB::table($table_name)->orderByDesc('group_id')->limit(1)->firstArray();
            if (!$data) {
                return '0';
            }
            return $data['close_price'];
        }
        $data = JsonHelper::decode($res, true);
        return MyCommon::decimalValidate($data['close_price']);
    }

    /**
     * 获取币种id
     * @param string $coin_type
     * @return bool|mixed
     * @throws \Swoft\Db\Exception\DbException
     */
    public static function get_coin_id(string $coin_type)
    {
        $data = DB::table('coin')->select('id')->where(['coin_name_en' => strtolower($coin_type), 'show_flag' => 1])->firstArray();
        if ($data) {
            return $data['id'];
        }
        return false;
    }

    /**
     * 获取单条币种信息
     * @param string $coin_type
     * @return array
     * @throws DbException
     */
    public static function get_coin_info_by_coin_type(string $coin_type)
    {
        return DB::table('coin')
            ->where(array(
                ['show_flag', '!=', 0],
                ['coin_name_en', '=', strtolower($coin_type)]
            ))->firstArray();
    }

}
