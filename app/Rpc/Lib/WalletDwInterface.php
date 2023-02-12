<?php

namespace App\Rpc\Lib;

/**
 * Interface WalletDwInterface
 */
interface WalletDwInterface
{

    /**
     * 获取用户可用余额
     * @param int $uid
     * @param int $coin_id
     * @return mixed
     * @throws \Swoft\Db\Exception\DbException
     */
    public function get_wallet_free(int $uid, int $coin_id);

    /**
     * 扣除可用金额，追加冻结金额
     * @param $uid
     * @param $amount
     * @param $coin_id
     * @param string $trade_type
     * @return bool
     * @throws \Swoft\Db\Exception\DbException
     */
    public function append_wallet_frozen(int $uid, string $amount, int $coin_id, string $trade_type);

    /**
     * 扣除冻结金额,返还可用金额
     * @param $uid
     * @param $amount
     * @param $coin_id
     * @param string $trade_type
     * @return bool
     * @throws \Swoft\Db\Exception\DbException
     */
    public function return_wallet_frozen(int $uid, string $amount, int $coin_id, string $trade_type);

    /**
     * 直接追加可用金额
     * @param $uid
     * @param $amount
     * @param $coin_id
     * @param string $trade_type
     * @return bool
     * @throws \Swoft\Db\Exception\DbException
     */
    public function append_wallet_free(int $uid, string $amount, int $coin_id, string $trade_type);

    /**
     * 直接扣除可用金额
     * @param $uid
     * @param $amount
     * @param $coin_id
     * @param string $trade_type
     * @return bool
     * @throws \Swoft\Db\Exception\DbException
     */
    public function deduct_wallet_free(int $uid, string $amount, int $coin_id, string $trade_type);

    /**
     * 直接扣除冻结金额
     * @param $uid
     * @param $amount
     * @param $coin_id
     * @param string $trade_type
     * @return bool
     * @throws \Swoft\Db\Exception\DbException
     */
    public function deduct_wallet_frozen(int $uid, string $amount, int $coin_id, string $trade_type);

    /**
     * 用户钱包余额是否异常 true异常 false 正常
     * @param int $uid
     * @return bool
     */
    public function user_wallet_abnormal(int $uid);

    /**
     * 直接扣除质押金额
     * @param $uid
     * @param $amount
     * @param $coin_id
     * @param string $trade_type
     * @return bool
     * @throws \Swoft\Db\Exception\DbException
     */
    public function deduct_wallet_pledge(int $uid, string $amount, int $coin_id, string $trade_type);

    /**
     * 直接追加质押金额
     * @param $uid
     * @param $amount
     * @param $coin_id
     * @param string $trade_type
     * @return bool
     * @throws \Swoft\Db\Exception\DbException
     */
    public function append_wallet_pledge(int $uid, string $amount, int $coin_id, string $trade_type);

    /**
     * 扣除可用金额，追加到抵押金额
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @return bool
     */
    public function deduct_wallet_free_to_pledge(int $uid, string $amount, int $coin_id, string $trade_type);

    /**
     * 获取用户体验金
     * @param int $uid
     * @param int $coin_id
     * @return mixed
     * @throws \Swoft\Db\Exception\DbException
     */
    public function get_wallet_experience(int $uid, int $coin_id);

    /**
     * 扣除体验金额，返回到可用金额
     * @param int $uid
     * @param string $amount
     * @param int $coin_id
     * @param string $trade_type
     * @return bool
     */
    public function return_wallet_experience(int $uid, string $amount, int $coin_id, string $trade_type);
}
