<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/4
 * Time: 21:33
 **/
//第一个参数也可以是__FILE__
//$msg_key = ftok(__DIR__,'u');
////var_dump($msg_key);

//为了方便测试连接,使用和服务端一样的key,即指定发送数据的队列
$msg_queue = msg_get_queue(1963150487);

msg_send($msg_queue, 10, "this is will  ");