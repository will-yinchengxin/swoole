<?php
namespace Willyin\Io\PcntlModel;

// 这是等会自个要写的服务
class Worker
{

    // 自定义服务的事件注册函数，
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    // 连接
    public $socket = null;

    // 创建多个子进程 -》 是不是可以自定义
    protected $config = [
        'worker_num' => 4
    ];

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
        stream_set_blocking($this->socket, 0);
    }

    // 启动服务的
    public function start()
    {
        $this->fork();
    }
    // 创建多个子进程，并且让子进程可以去运行accept函数
    public function fork()
    {
        for ($i=0; $i < $this->config['worker_num']; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间
            } else if($son11 < 0){
                // 进程创建失败的时候
            } else {

                $this->accept();
                // 处理接收请求
                exit;
            }

        }

        $status = 0;
        $son = pcntl_wait($status);


    }
    // 需要处理事情
    public function accept()
    {
        // 接收连接和处理使用
        while (true) {
            $a =  posix_getpid();
            debug($a); // 阻塞
            debug("准备就绪");
            // 监听的过程是阻塞的
            $client = @stream_socket_accept($this->socket);


            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }
            $data = @fread($client, 65535);
            if (is_callable($this->onReceive)) {
                ($this->onReceive)($this, $client, $data);
                debug($a);
                debug("完成工作");
            }

        }
    }
    public function set($value)
    {
        // ..
    }


    // 发送信息
    public function send($client, $data)
    {
        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
        $response .= "Connection: keep-alive\r\n";
        $response .= "Content-length: ".strlen($data)."\r\n\r\n";
        $response .= $data;
        @fwrite($client, $response);
    }



}