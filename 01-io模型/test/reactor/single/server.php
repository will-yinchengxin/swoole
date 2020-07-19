<?php
// echo 1;
require __DIR__.'/../../../../vendor/autoload.php';
use Willyin\Io\Reactor\Swoole\Single\Worker;
$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);
$server->onReceive = function($socket, $client, $data){
    //sleep(5);
    send($client, "hello world client \n");
};
debug($host);
$server->start();
