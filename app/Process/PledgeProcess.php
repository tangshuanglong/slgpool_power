<?php

namespace App\Process;

use App\Lib\MyCommon;
use App\Lib\MyRabbitMq;

use App\Model\Logic\PowerOrderLogic;
use App\Rpc\Lib\CoinInterface;
use App\Rpc\Lib\KlineInterface;
use App\Rpc\Lib\WalletMiningInterface;
use GuzzleHttp\Client;
use Swoft\Apollo\Config;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Co;
use Swoft\Db\DB;
use Swoft\Exception\SwoftException;
use Swoft\Http\Server\HttpServer;
use Swoft\Log\Helper\CLog;
use Swoft\Process\Process;
use Swoft\Process\UserProcess;
use Swoft\Redis\Redis;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use Swoft\Rpc\Server\ServiceServer;
use Swoft\Task\Task;
use Swoft\WebSocket\Server\WebSocketServer;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

/**
 * Class PledgeProcess
 * @package App\Process
 * @Bean()
 */
class PledgeProcess extends UserProcess
{

    private  $redis_token = "fil:pledge";
    /**
     * Run
     *
     * @param Process $process
     * @throws \Swoft\Db\Exception\DbException
     */
    public function run(Process $process) : void
    {
        $url = "https://fgas.io/api/v1/getFil";
        sleep(1);
        while (true) {
            $http = new Client();
            $data = json_decode($http->post($url)->getBody()->getContents(), true);
            if (empty($data)) {
                sleep(10);
                continue;
            }

            // 此处用于动态抵押。
            $max_data = Redis::hGet($this->redis_token.":max","total_1T");
            if ($max_data){
                if ($max_data <= $data['total_1T']){
                    Redis::hMSet($this->redis_token.":max", $data);
                }
            } else {
                Redis::hMSet($this->redis_token.":max", $data);
            }
            /*
              $data =
                {
                    "payment_32": 0.2746,
                    "payment_1T": 8.7872,
                    "payment_1P": 8998.0928,
                    "preGas_32": 0.28743120805464295,
                    "preGas_1T": 9.197798657748574,
                    "preGas_1P": 9418.54582553454,
                    "total_32": 0.562031208054643,
                    "total_1T": 17.984998657748577,
                    "total_1P": 18416.638625534542,
                    "latest_height": 436085,
                    "latest_block_reward": "19.1544064834369",
                    "total_blocks": 1868700,
                    "total_rewards": "26950709.8856665",
                    "power_ratio": "1141153613718869.3333",
                    "fil_per_tera": "0.125493442840174",
                    "total_quality_power": "2347251892297760768",
                    "active_miners": 1211
                }
             */
            Redis::hMSet($this->redis_token, $data);

            sleep(5);
        }
    }
}


