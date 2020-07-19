<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/9
 * Time: 16:33
 **/
return  [
    'http'=>[
        'host' => '0.0.0.0',
        'port' => 9501,
        'swoole' => [
         ],
        'tcpable' => 0, // 1为开启， 0 为关闭
        'rpc' => [
            'host' => '127.0.0.1',
            'port' => 8000,
            'swoole' => [
                //配置多监听端口的工作进程数
                'worker_num' => 1
            ]
        ]
    ],

];
