<?php

namespace App\Common;

use Exception;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoole\Client;

/**
 * Class RpcClient
 * @package App\Common
 * @Bean()
 */
class RpcClient{

    private $rpc_eol = "\r\n\r\n";

    /**
     * @param $host
     * @param $port
     * @param $class
     * @param $method
     * @param $param
     * @param string $version
     * @param array $ext
     * @return mixed
     * @throws Exception
     */
    public function request($host, $port, $class, $method, $param, $version = '1.0', $ext = [])
    {
        $client = new Client(SWOOLE_TCP);
        if (!$client->connect($host, $port, 2))
        {
            throw new Exception("connect failed. Error: {$client->errCode}");
        }
        $req = [
            "jsonrpc" => '2.0',
            "method" => sprintf("%s::%s::%s", $version, $class, $method),
            'params' => $param,
            'id' => '',
            'ext' => $ext,
        ];
        $data = json_encode($req) . $this->rpc_eol;
        $client->send($data);
        $res = json_decode($client->recv(), true);
        $client->close();
        if (isset($res['result'])) {
            return $res['result'];
        }
        return $res;
    }
}
