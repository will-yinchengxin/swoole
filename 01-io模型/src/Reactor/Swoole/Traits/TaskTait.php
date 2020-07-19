<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/5
 * Time: 10:15
 **/
namespace Willyin\Io\Reactor\Swoole\Traits;

trait TaskTait{
    //回调函数
    public $onTask = null;
    public $onFinish = null;
    // 记录task的pid
    protected $taskPids = null;
    // 消息队列
    protected $msgQueue = null;

    /**
     * 创建task进程的函数
     */
    protected function forkTasks()
    {
        // 等到一个key
        $msg_key = (empty($this->config['message_queue_key'])) ? ftok(__FILE__, "u") : $this->config['message_queue_key'];

        $this->msgQueue = \msg_get_queue($msg_key);
        debug("创建一个队列 : ".$this->msgQueue);
        for ($i=0; $i < $this->config['task_worker_num']; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间
                $this->taskPids[] = $son11;
            } else if($son11 < 0){
                // 进程创建失败的时候
            } else {
                // 处理接收请求
                $this->msg_receive();
                exit;// 终止程序的执行
            }
        }
    }
    /**
     * 处理信息
     */
    protected function msg_receive()
    {
        $pid = posix_getpid();
        while (1) {
            msg_receive($this->msgQueue, $pid, $msgtype, 1024, $message);
            debug("task 处理一个新的任务");
            // 接收处理
            ($this->onTask)($this, $message);
        }
    }
    /**
     * 用于task进程的任务投递
     */
    public function task($data, $dst_worker_id = null)
    {
        $pid = 0;
        if (empty($dst_worker_id)) {
            $pid = $this->taskPids[\array_rand($this->taskPids)];
        } else {
            $pid = $dst_worker_id;
        }
        msg_send($this->msgQueue, $pid, $data);
    }
}