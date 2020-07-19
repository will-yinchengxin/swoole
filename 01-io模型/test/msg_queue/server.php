<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/4
 * Time: 21:12
 **/
/*//父进程跟子进程实现消息发送

//注意在php创建消息队列，第二个参数会直接转成字符串，可能会导致通讯失败
$msg_key = ftok(__DIR__,'u');
$msg_queue = msg_get_queue($msg_key);

//创建子进程
$pid = pcntl_fork();

if( $pid == 0){
    // 子进程发送消息
    msg_receive($msg_queue,10,$message_type,1024,$message);

    //var_dump($message_type).PHP_EOL; //int(10)

    echo "接收到父进程消息,内容为:".$message.PHP_EOL;
    msg_send($msg_queue,10,"我是子进程发送的消息").PHP_EOL;
    exit();
} else if ($pid){



    echo "向子进程发送消息".PHP_EOL;
    $r = 1234;
    msg_send($msg_queue,10,$r);

    // 父进程接收消息

    msg_receive($msg_queue,10,$message_type,1024,$message);
    echo "接收到子进程消息,内容为:".$message.PHP_EOL;
    pcntl_wait($status);
    msg_remove_queue($msg_queue);
}*/

//注意在php创建消息队列，第二个参数会直接转成字符串，可能会导致通讯失败
$msg_key = ftok(__DIR__,'u');
//var_dump($msg_key);
$msg_queue = msg_get_queue($msg_key);

msg_receive($msg_queue, 10, $message_type, 1024, $message);
var_dump($message);
// 父进程接收消息
msg_remove_queue($msg_queue);