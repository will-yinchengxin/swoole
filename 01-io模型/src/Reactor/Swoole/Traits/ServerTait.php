<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/1
 * Time: 17:38
 **/
namespace Willyin\Io\Reactor\Swoole\Traits;

use Swoole\Event;

trait ServerTait{
    // 自定义服务的事件注册函数，
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;

    // 连接
    public $socket = null;
    protected $socket_address = null;
    // 以内存的方式存pids
    protected $workerPids = [];

    /**
     * 记录客户端的信息 比如上一次连接的时间
     */
    protected $clients = [];

    /**
     * 记录产生的定时器
     */
    protected $timeIds = [];

    public function initServer()
    {
        $context = stream_context_create($this->config['opts']);
        // 设置端口可以重复监听
        \stream_context_set_option($context, 'socket', 'so_reuseport', 1);

        // 传递一个资源的文本 context
        return $this->socket = stream_socket_server($this->socket_address , $errno , $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
    }

    public function createSocket()
    {
        return function($socket){
            // $client 是不是资源 socket
            $client = @stream_socket_accept($this->socket);
            // is_callable判断一个参数是不是闭包
            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }
            // 默认就是循环操作
            @Event::add($client, $this->sendClient());
        };
    }

    public function sendClient()
    {
        return function($socket){
            // 如果能接收到信息，那么这个程序一定在心跳检测的范围内
            if (!empty($this->timeIds[(int) $socket])) {
                swoole_timer_clear($this->timeIds[(int) $socket]);
                debug("清空: ". $this->timeIds[(int) $socket]. "定时器");
            }
            //从连接当中读取客户端的内容
            $buffer=fread($socket,1024);
            //如果数据为空，或者为false,不是资源类型
            if(empty($buffer)){
                if(feof($socket) || !is_resource($socket)){
                    //触发关闭事件
                    swoole_event_del($socket);
                    fclose($socket);
                    return null;
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if(!empty($buffer) && is_callable($this->onReceive)){
                ($this->onReceive)($this, $socket, $buffer);
                //swoole_event_del($socket);
                //fclose($socket);
            }
            // 定时器
            $this->heartbeatCheck($socket);
        };
    }
    public function fork($workerNum = null)
    {
        $workerNum = (empty($workerNum)) ? $this->config['worker_num'] : $workerNum ;
        for ($i=0; $i < $workerNum; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间
                pidPut($son11, $this->config['workerPidFiles']);
                $this->workerPids[] = $son11;
            } else if($son11 < 0){
                // 进程创建失败的时候
            } else {
                $this->accept();
                exit;
            }
        }
    }

    /**
    * 用于心跳检测 默认不开启
    */
    protected function heartbeatCheck($socket)
    {
        $time = $this->config['heartbeat_check_interval'];
        if (!empty($time)) {
            // 记录客户端上一次信息的发送时间
            $this->clients[(int) $socket] = time();
            // 设置在多久后检测是否还有在连接,是以毫秒为单位,所以要乘以1000
            $timeId = swoole_timer_after($time * 1000, function() use ($time,$socket) {
                // 判断客户端是否，在heartbeat_check_interval 这个时间内是否还有信息的动作
                // 实际上当这个函数执行的时候已经端口连接了 -》超出了心跳检测的时间；原则上说下面的判断实际是无意义的
                if ((time() - ($this->clients[(int)$socket])) >= $time) {
                    swoole_event_del($socket);
                    \fclose($socket);
                    unset($this->clients[(int)$socket]);
                    debug("结束：" . (int)$socket . " 连接");
                }
            });
            $this->timeIds[(int) $socket] = $timeId;
        }
    }
}