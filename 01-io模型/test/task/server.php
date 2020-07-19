<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/2
 * Time: 10:50
 **/
$serv = new Swoole\Server("127.0.0.1", 9501);
//$php_script_file 第一个参数是php文件的路径
$key = ftok(__DIR__, 1);
$serv->set(array(
    'worker_num' => 2,
    'task_worker_num' => 4,
    'task_ipc_mode' => 2,
    'message_queue_key' => $key
));

$serv->on('Receive', function(Swoole\Server $serv, $fd, $from_id, $data) {
    $r = str_repeat("a", 10 * 1024 * 1024);

    $task_id = $serv->task($r);
    //echo "测试阻塞 \n";
    $serv->send($fd, "分发任务，任务id为$task_id\n");
});

$serv->on('Task', function (Swoole\Server $serv, $task_id, $from_id, $data) {
    var_dump("Tasker进程接收到数据");
    //echo "#{$serv->worker_id}\tonTask: [PID={$serv->worker_pid}]: task_id=$task_id, data_len=".strlen($data).".".PHP_EOL;
    sleep(5);
    $serv->finish($data);
});

$serv->on('Finish', function (Swoole\Server $serv, $task_id, $data) {
    echo "Task#$task_id finished, data_len=".strlen($data).PHP_EOL;
});

//$serv->on('workerStart', function($serv, $worker_id) {
//    global $argv;
//    if($worker_id >= $serv->setting['worker_num']) {
//        swoole_set_process_name("php {$argv[0]}: task_worker");
//    } else {
//        swoole_set_process_name("php {$argv[0]}: worker");
//    }
//});
echo "服务启动: 127.0.0.1:9501";
$serv->start();