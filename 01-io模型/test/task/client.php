<?php
$client = new swoole_client(SWOOLE_SOCK_TCP|SWOOLE_KEEP );
//连接到服务器
$client->connect('127.0.0.1', 9501, 0.5);

//向服务器发送数据
$body = 'a';
$send = pack('N', strlen($body)) . $body;
//for ($i=0; $i < 100; $i++) {
    $client->send($send);
//}

//从服务器接收数据
$data = $client->recv();
echo $data."\n";

//关闭连接
//$client->close();

//echo "其他事情\n";