<?php
require __DIR__.'/../../vendor/autoload.php';
use Willyin\Io\NoBlocking\Worker;

$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);





$server->onConnect = function($socket, $conn=null){

    echo "有一个连接进来了\n";
   var_dump($conn);
    // $read = $write = $except = [];
    //$read[] = $conn;
   // var_dump(stream_select($read, $write, $except, 1));

};
// 接收和处理信息

$server->onReceive = function($socket,$conn, $data){
    //echo "接受到了客户端的连接信息\n";
    // fwrite($conn, "server hellow");
   send($conn, "hello world client \n");
};
$server->start();



