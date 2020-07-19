<?php
namespace Willyin\Io\Multiplexing;

Class Worker{
    // 自定义服务的事件注册函数，
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    //新的客户端建立，将socket保留到监听数组当中
    protected $sockets = [];
    // 连接
    public $socket = null;

    public function __construct($socket_add)
    {
        $this->socket = stream_socket_server($socket_add);
        stream_set_blocking($this->socket, 0);

        // 咋们的server也有忙的时候
        $this->sockets[(int) $this->socket] = $this->socket;
    }

    //当客户端请求时的处理
    public function accept()
    {
        // 接收连接和处理使用
        while (true) {
            $read = $this->sockets;
        // 校验池子是否有可用的连接 -》 校验传递的数组中是否有可以用的连接 socket
        // 把连接放到$read
        // 它返回值其实并不是特别可靠

            debug('stream_select检测  start 的 $read');
             debug($read, true);

            stream_select($read, $w, $e, 1);
// stream_select
            debug('stream_select检测 end 的 $read');
             debug($read, true);

            foreach ($read as $socket) {
                // $socket 可能为 1. worker 主连接  2. 也可能是通过stream_socket_accept()创建的
                     if ($socket === $this->socket) {
                     // 创建与客户端的连接
                         $this->createSocket();
                     } else {
                     // 发送信息
                      $this->sendMessage($socket);
                     }
                 }
        }
    }

    public function createSocket()
    {
        $client = stream_socket_accept($this->socket);
        if(is_callable($this->onConnect)){
            ($this->onConnect)($this,$client);
        }
        //把创建的客户端的socket资源放置的sockets数组中
        $this->sockets[(int)$client] = $client;
   }
    public function sendMessage($client) {
        //读取客户端数据
        $data = fread($client, 65535);

        if ($data === '' || $data == false) {
                //关闭连接
                 fclose($client);
                 //销毁使用完的客户端记录
                 unset($this->sockets[(int)$client]);
                 //这里需要给一个返回值
                 return null;
             }

            if(is_callable($this->onReceive)) {
              ($this->onReceive)($this, $client, $data);
             }
    }

    //启动服务
    public function start()
    {
        $this->accept();
    }
}
