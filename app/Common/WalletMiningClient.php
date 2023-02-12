<?php

namespace App\Common;

use App\Rpc\Lib\WalletMiningInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use App\Common\RpcClient;
use Swoft\Db\Exception\DbException;

/**
 * Class WalletMiningClient
 * @package App\Common
 * @Bean()
 */
class WalletMiningClient implements WalletMiningInterface {


    private $host = '127.0.0.1';

    private $port = 18311;

    /**
     * @Inject()
     * @var RpcClient
     */
    private $rpcClient;

    public function __construct()
    {
        $this->host = config('user_rpc.host');
        $this->port = config('user_rpc.port');
    }

    /**
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @return bool|mixed
     * @throws \Exception
     */
    public function append_wallet_frozen(int $uid, string $amount, int $coin_id, string $trade_type)
    {
        return $this->send_request($uid, $amount, $coin_id, $trade_type, __FUNCTION__);
    }

    /**
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @return bool|mixed
     * @throws \Exception
     */
    public function return_wallet_frozen(int $uid, string $amount, int $coin_id, string $trade_type)
    {
        return $this->send_request($uid, $amount, $coin_id, $trade_type, __FUNCTION__);
    }

    /**
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @return bool|mixed
     * @throws \Exception
     */
    /**
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @return bool|mixed
     * @throws \Exception
     */
    public function append_wallet_free(int $uid, string $amount, int $coin_id, string $trade_type)
    {
        return $this->send_request($uid, $amount, $coin_id, $trade_type, __FUNCTION__);
    }

    /**
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @return bool|mixed
     * @throws \Exception
     */
    public function deduct_wallet_free(int $uid, string $amount, int $coin_id, string $trade_type)
    {
        return $this->send_request($uid, $amount, $coin_id, $trade_type, __FUNCTION__);
    }

    /**
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @return bool|mixed
     * @throws \Exception
     */
    public function deduct_wallet_frozen(int $uid, string $amount, int $coin_id, string $trade_type)
    {
        return $this->send_request($uid, $amount, $coin_id, $trade_type, __FUNCTION__);
    }

    /**
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @param string $method
     * @return mixed
     * @throws \Exception
     */
    private function send_request(int $uid, string $amount, int $coin_id, string $trade_type, string $method)
    {
        $class = \App\Rpc\Lib\WalletMiningInterface::class;
        $param = [
            $uid,
            $amount,
            $coin_id,
            $trade_type
        ];
        return $this->rpcClient->request($this->host, $this->port, $class, $method, $param);
    }


    public function get_wallet_free(int $uid, int $coin_id)
    {
        // TODO: Implement get_wallet_free() method.
    }


    public function deduct_wallet_pledge(int $uid, string $amount, int $coin_id, string $trade_type)
    {
        // TODO: Implement deduct_wallet_pledge() method.
    }


    public function append_wallet_pledge(int $uid, string $amount, int $coin_id, string $trade_type)
    {
        // TODO: Implement append_wallet_pledge() method.
    }


    public function user_wallet_abnormal(int $uid)
    {
        // TODO: Implement user_wallet_abnormal() method.
    }
}
