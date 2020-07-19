<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/5
 * Time: 10:18
 **/
require __DIR__.'/../../../vendor/autoload.php';
use Willyin\Io\Reactor\Swoole\MulitTask\Worker;

$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);

$server->set([
    // 'watch_file' => true,
    'task_worker_num' => 3,
]);
// echo 1;
$server->onReceive = function(Worker $server, $client, $data){
    debug("向task发送数据 ");
    $server->task("hello worker task");
    send($client, "hello world client \n");
};
$server->onTask = function(Worker $server, $data){
    debug("接收到xxx的数据 ".$data);
};
// debug($host);
$server->start();