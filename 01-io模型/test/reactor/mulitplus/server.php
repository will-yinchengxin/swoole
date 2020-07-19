<?php
require __DIR__.'/../../../vendor/autoload.php';
use Willyin\Io\Reactor\Swoole\MulitPlus\Worker;
//use Willyin\Io\Test;
$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);
$server->set([
    'watch_file' => true,
    'heartbeat_check_interval' =>3
]);

$server->onConnect = function($socket, $conn=null){
    echo "有一个连接进来了\n";
};
$server->onReceive = function($socket, $client, $data){
    //(new Test())->index().PHP_EOL;
    send($client, "hello world client \n");
};
$server->start();

