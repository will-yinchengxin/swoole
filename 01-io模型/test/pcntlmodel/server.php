<?php
/**
 * Create By: Will Yin
 * Date: 2020/6/28
 * Time: 22:43
 **/

require __DIR__ . '/../../vendor/autoload.php';

use Willyin\Io\PcntlModel\Worker;

$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);
$server->onConnect = function($socket, $client){
    echo "有一个连接进来了\n";
};
// 接收和处理信息
$server->onReceive = function($socket, $client, $data){
    $socket->send($client, "hello world client \n");
};
$server->start();