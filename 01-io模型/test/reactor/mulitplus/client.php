<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/1
 * Time: 21:32
 **/
$client = stream_socket_client("tcp://127.0.0.1:9000");
$new = time();
// 第一次信息
fwrite($client, "hello world");
echo "第一次信息\n";
var_dump(fread($client, 65535));

// 第二次信息
sleep(4);
fwrite($client, "hello world");
echo "第二次的信息\n";
var_dump(fread($client, 65535));

// 第三次信息
// sleep(4);
fwrite($client, "hello world");
echo "第三次信息\n";
var_dump(fread($client, 65535));