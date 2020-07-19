<?php
// 是建立连接
$client = stream_socket_client("tcp://127.0.0.1:9000");

stream_set_blocking($client, 0);

fwrite($client, "hello world");// 创建订单


$read = $write = $except = [];
// stream_select
// 检测的方式根据数组 -》 去进行检测socket状态

    while (!feof($client)) {
        $read[] = $client;
        fread($client, 65535);
        sleep(1);
        echo "检查socket :\n";
        // 返回一个结果 0 可用 1，正忙
        //var_dump(stream_select($read, $write, $except, 1));
        stream_select($read, $write, $except, 1);
        foreach ($read as $index => $item) {
            var_dump($item);
        }
    }
