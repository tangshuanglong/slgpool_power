<?php

namespace App\Rpc\Lib;


interface RewardLogInterface{

    /**
     * @param array $data
     * @return bool
     */
    public function insert_reward_log(array $data);
}
