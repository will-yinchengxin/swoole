<?php
/**
 * Create By: Will Yin
 * Date: 2020/6/27
 * Time: 11:06
 **/
namespace Willyin\Io\Asynchronous;
use Swoole\Event;

class Worker{
     //自定义服务的事件注册函数，
     // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    // 连接
    public $socket = null;

    public function __construct($socket_add)
    {
        $this->socket = stream_socket_server($socket_add);
    }
    //当客户端请求时的处理
    public function accept()
    {
        Event::add($this->socket, $this->createSocket());
    }

    //创建连接
    public function createSocket() {
        return function($socket){
            // $client 是不是资源 socket
            $client = stream_socket_accept($this->socket);
            // is_callable判断一个参数是不是闭包
            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }
            // 默认就是循环操作
            Event::add($client, $this->sendClient());
        };
    }

    public function sendClient()
    {
        return function($socket){
            //从连接当中读取客户端的内容
            $buffer=fread($socket,1024);
            //如果数据为空，或者为false,不是资源类型
            if(empty($buffer)){
                if(feof($socket) || !is_resource($socket)){
                    //触发关闭事件
                    swoole_event_del($socket);
                    fclose($socket);
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if(!empty($buffer) && is_callable($this->onReceive)){
                ($this->onReceive)($this, $socket, $buffer);
                swoole_event_del($socket);
                fclose($socket);
            }
        };
    }

    // 启动服务的
    public function start()
    {
        $this->accept();
    }
}