<?php
return [
    //consul注册配置
    'consul'                          => [
        'address' => '127.0.0.1',
        'port'    => 18320,
        'name'    => 'pw',
        'id'      => 'pw',
    ],
    'apollo'                          => require_once __DIR__ . "/../../../apollo.php",

    //区块信息缓存key
    'block_stats_key'                 => 'coin:block:stats',
    //币最后价格key
    'coin_last_price_key'             => 'coin:last:price:info',
    //发放奖励后的统计队列
    'mining_income_stats'             => 'mining:income:stats:queue',
    //每天挖矿发放缓存
    'mining_income_give_cache'        => 'mining:income:give:cache',

    /**
     * power模块
     * */
    //订单过期取消队列key
    'power_order_expire_cancel_cache' => 'power:order:expire:cancel:cache',
    'power_order_expire_cancel'       => 'power:order:expire:cancel',
    'power_order_expire_time'         => 1800000, //半小时：1800000
    //订单支付成功后处理队列
    'power_order_pay'                 => 'power:order:pay:success',
    //产品锁
    'power_product_lock'              => 'power:product:lock:product_id:',
    //订单锁
    'power_order_lock'                => 'power:product:lock:order_id:',
    //购买记录缓存key
    'power_buy_record'                => 'power:order:buy:record:',
    //限购数量缓存key
    'power_product_limited'           => 'power:product:limited:',

];
