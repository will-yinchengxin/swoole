<?php
/**
 * Create By: Will Yin
 * Date: 2020/6/27
 * Time: 11:18
 **/

require __DIR__.'/../../vendor/autoload.php';
use Willyin\Io\Asynchronous\Worker;
$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);
$server->onReceive = function($socket, $client, $data){
   debug($data);
    // sleep(3);
    // echo "给连接发送信息\n";
    send($client, "hello world client \n");
};
debug($host);
$server->start();