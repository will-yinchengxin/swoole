<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/5
 * Time: 10:19
 **/
// 是建立连接
$client = stream_socket_client("tcp://127.0.0.1:9000");
$new = time();
// 第一次信息
fwrite($client, "hello world");
echo "第一次信息\n";
var_dump(fread($client, 65535));