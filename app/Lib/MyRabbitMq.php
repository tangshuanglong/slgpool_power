<?php

namespace App\Lib;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Log\Helper\CLog;
use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Class MyRabbitMq
 * @package App\Lib
 * @Bean("MyRabbitMq")
 */
class MyRabbitMq
{

    /**
     * 主机
     * @var string
     */
    private $host = '127.0.0.1';

    /**
     * 端口
     * @var int
     */
    private $port = 5672;

    /**
     * 用户名
     * @var string
     */
    private $user = 'admin';

    /**
     * 密码
     * @var string
     */
    private $password = 'admin';

    /**
     * 虚拟主机名
     * @var string
     */
    private $vhost = 'my_vhost';

    /**
     * 连接实例
     * @var
     */
    private static $connection;

    /**
     * 通道实例
     * @var AMQPChannel
     */
    private $channel;

    public function __construct()
    {
        //初始化参数
        $this->host = config('rabbitmq.host');
        $this->port = config('rabbitmq.port');
        $this->user = config('rabbitmq.user');
        $this->password = config('rabbitmq.password');
        $this->vhost = config('rabbitmq.vhost');
        try{
            $this->reconnect();
        }catch (\Exception $e){
            CLog::error($e->getMessage());
        }
    }


    public function reconnect()
    {
        if (!self::$connection){
            return new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password, $this->vhost);
        }
        return self::$connection;
    }

    /**
     * @param string $queue_name
     * @param $data
     * @return bool
     */
    public function push(string $queue_name, array $data): bool
    {
        try{
            try {
                self::$connection = $this->reconnect();
//            $myCommon = BeanFactory::getBean("MyCommon");
//            $channel_id = $myCommon->get_hash_id($queue_name, 500);
                $this->channel = self::$connection->channel();
            } catch (\Exception $e) {
                self::$connection = null;
                self::$connection = $this->reconnect();
                $this->channel = self::$connection->channel();
            }
            //设为确认模式
            $this->channel->confirm_select();
            //durable为true,持久化队列，只有数据消费完返回ack才会消除
            $this->channel->queue_declare($queue_name, false, true, false, false);
            $json_data = JsonHelper::encode($data);
            $properties = [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,//消息持久化，重启不会丢失
            ];
            $msg = new AMQPMessage($json_data, $properties);
            $this->channel->basic_publish($msg, '', $queue_name);
            //等待确认
            $this->channel->set_ack_handler(function (AMQPMessage $message){
                return true;
            });
            $this->channel->set_nack_handler(function (AMQPMessage $message){
                return false;
            });
            $this->channel->wait_for_pending_acks();
            $this->channel->close();
            return true;
        }catch (\Exception $e){
            CLog::error($e->getMessage());
            return false;
        }

    }

    /**
     * 延时队列
     * @param string $queue_name
     * @param string $delay_queue_name
     * @param $data
     * @param int $ttl 单位ms
     * @return bool
     */
    public function push_delay_quoue(string $queue_name, string $delay_queue_name, $data, int $ttl = 900000)
    {
        $cache_exchange = $queue_name.'_exchange';
        $delay_exchange = $delay_queue_name.'_exchange';
        try{
            self::$connection = $this->reconnect();
            $this->channel = self::$connection->channel();
            //设为确认模式
            $this->channel->confirm_select();
            $this->channel->exchange_declare($cache_exchange, 'direct', false, false, false);
            $this->channel->exchange_declare($delay_exchange, 'direct', false, false, false);
            //durable为true,持久化队列，只有数据消费完返回ack才会消除
            $tale = new AMQPTable();
            $tale->set('x-dead-letter-exchange', $delay_exchange);
            $tale->set('x-dead-letter-routing-key', $delay_exchange);
            //设置缓存队列，消息在该队列过期会把消息发送到延时队列
            $this->channel->queue_declare($queue_name, false, true, false, false, false,  $tale);
            $this->channel->queue_bind($queue_name, $cache_exchange, $cache_exchange);
            //设置延时队列
            $this->channel->queue_declare($delay_queue_name, false, true, false, false);
            $this->channel->queue_bind($delay_queue_name, $delay_exchange, $delay_exchange);
            $json_data = JsonHelper::encode($data);
            $properties = [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,//消息持久化，重启不会丢失
                'expiration' => $ttl,
            ];
            $msg = new AMQPMessage($json_data, $properties);
            $this->channel->basic_publish($msg, $cache_exchange, $cache_exchange);
            //等待确认
            $this->channel->set_ack_handler(function (AMQPMessage $message){
                return true;
            });
            $this->channel->set_nack_handler(function (AMQPMessage $message){
                return false;
            });
            $this->channel->wait_for_pending_acks();
            $this->channel->close();
            return true;
        }catch (\Exception $e){
            CLog::error($e->getMessage());
            return false;
        }
    }

    /**
     * @param string $queue_name
     * @param $callback
     * @return bool|\PhpAmqpLib\Channel\AMQPChannel
     */
    public function pop(string $queue_name, $callback)
    {
        try{
            self::$connection = $this->reconnect();
//            $myCommon = BeanFactory::getBean("MyCommon");
//            $channel_id = $myCommon->get_hash_id($queue_name, 500);
            $this->channel = self::$connection->channel();
            //durable为true,持久化队列，只有数据消费完返回ack才会消除
            $this->channel->queue_declare($queue_name, false, true, false, false);
            //prefetch_count为1，设置每个进程每次只能收到一个请求
            $this->channel->basic_qos(null, 1, null);
            //定义接收数据的方式和回调函数
            $this->channel->basic_consume($queue_name, '', false, false, false, false, $callback);
            return $this->channel;
        }catch (\Exception $e){
            CLog::error($e->getMessage());
            return false;
        }
    }

    /**
     * 返回ack，表示请求处理完成，可以清除该请求数据
     * @param $msg
     * @return bool
     */
    public function back_ack(AMQPMessage $msg)
    {
        try{
            $this->channel->basic_ack($msg->delivery_info['delivery_tag']);
            return true;
        }catch (\Exception $e){
            CLog::error($e->getMessage());
            return false;
        }

    }

    /**
     * 返回nack，表示请求处理有问题，不清除请求数据
     * @param $msg
     * @return bool
     */
    public function back_nack(AMQPMessage  $msg)
    {
        try{
            $this->channel->basic_nack($msg->delivery_info['delivery_tag']);
            return true;
        }catch (\Exception $e){
            CLog::error($e->getMessage());
            return false;
        }

    }



}
