<?php
require_once "server.php";
use \Event as Event;
use \EventBase as EventBase;
$socket_address = "tcp://0.0.0.0:9000";
$server = stream_socket_server($socket_address);
echo $socket_address."\n";
$eventBase = new EventBase();
// 记录我们所创建的这样事件 让 $eventBase 可以找到这个事件
$count = [];// 变量没有要求 随便
$event = new Event($eventBase, $server, Event::PERSIST | Event::READ | Event::WRITE , function($socket) use ($eventBase, &$count){
// 在闭包中的 function($socket) 的$socket 就是
// 在构造函数中传递的 $server 这个属性
// 也就是 $socket = $server
// 建立与用户的连接
    echo "连接 start \n";
    $client = stream_socket_accept($socket);
    (new Server($eventBase, $client, $count))->handler();
    echo "连接 end \n";
});
$event->add();
$count[(int) $server][Event::PERSIST | Event::READ | Event::WRITE] = $event;
//var_dump($count);
$eventBase->loop();