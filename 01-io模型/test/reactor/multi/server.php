<?php
require __DIR__.'/../../../vendor/autoload.php';
use Willyin\Io\Reactor\Swoole\Mulit\Worker;
$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);

$server->onConnect = function($socket, $conn=null){
    echo "有一个连接进来了\n";
    //var_dump($conn);
};
$server->onReceive = function($socket, $client, $data){
   send($client, "hello world client \n",false);
};
// debug($host);
$server->start();

// require __DIR__.'/../../../../vendor/autoload.php';
//
// $host = "0.0.0.0"; // 0.0.0.0 代表接听所有
// $serv = new Swoole\Server($host, 9000);
// $serv->on('Receive', function ($serv, $fd, $from_id, $data) {
//     $serv->send($fd, "Server: ".$data);
// });
// $serv->start();
