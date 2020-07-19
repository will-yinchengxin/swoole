<?php
/**
 * Create By: Will Yin
 * Date: 2020/6/29
 * Time: 15:08
 **/
namespace Willyin\Io\Reactor\Swoole\Mulit;
use Swoole\Event;

class WorkerMulit
{
//监听socket
    protected $sockets = NULL;
//连接事件回调
    public $onConnect = NULL;
//接收消息事件回调
    public $onReceive = NULL;

    protected $config = [
        'workerNum' => 4,
    ];

    protected $socket_address;

    public function __construct($socket_address) {
        // $this->socket = stream_socket_server($socket_address);
        $this->socket_address = $socket_address;
    }
    public function set($data){
        $this->config = $data;
    }
    public function start(){
        $this->fork();
    }
    /**
     * 创建进程完成事情
     */
    public function fork() {
        $son_pid = pcntl_fork();
        for ($i=0; $i < $this->config['workerNum']; $i++) {
            if ($son_pid > 0) {
                $son_pid = pcntl_fork();
            } else if($son_pid < 0){
                // 异常
            } else {
                $this->accept();
                break;
            }
        }
        // 父进程监听子进程情况并回收进程
        for ($i=0; $i < $this->config['workerNum']; $i++) {
            $status = 0;
            $sop = pcntl_wait($status);
        }
    }
// 接收连接，并处理连接
    public function accept(){
        // $this->sockets[(int) $socket] = $socket;
        // 第一个需要监听的事件(服务端socket的事件),一旦监听到可读事件之后会触发
        Event::add($this->initServer(), $this->createSocket());
    }
    /**
     * 初始化话server
     */
    public function initServer()
    {
        $opts = [
            'socket' => [
                // 连接成功之后的等待个数
                'backlog' => '102400',
            ]
        ];
        $context = stream_context_create($opts);
        // 设置端口可以被多个进程重复的监听
        stream_context_set_option($context, 'socket', 'so_reuseport', 1);
        return stream_socket_server($this->socket_address, $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
    }

    /**
     * 建立与客户端的连接
     */
    public function createSocket(){
        return function($socket){
            // 测试端口监听的效果
             debug(posix_getpid());
            $client=stream_socket_accept($socket);
            //触发事件的连接的回调
            if(!empty($client) && is_callable($this->onConnect)){
                call_user_func($this->onConnect, $client);
            }
            Event::add($client, $this->sendMessage());
        };
    }
    public function sendMessage(){
        return function($socket){
            //从连接当中读取客户端的内容
            $buffer=fread($socket,1024);
            //如果数据为空，或者为false,不是资源类型
            if(empty($buffer)){
                if(feof($socket) || !is_resource($socket)){
                    // 触发关闭事件
                     swoole_event_del($socket);
                     fclose($socket);
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if(!empty($buffer) && is_callable($this->onReceive)){
                call_user_func($this->onReceive,$this,$socket,$buffer);
                //swoole_event_del($socket);
                //fclose($socket);
            }
        };
    }
}