<?php
namespace Willyin\Io\NoBlocking;
class Worker{
  //自定义服务的事件注册函数
    //这里接受时是三个闭包
    public $onConnect = null;
    public $onReceive = null;
    public $onColose = null;

    //socket资源
    public $socket = null;

    public function __construct($socket_add)
    {
        $this->socket = stream_socket_server($socket_add);
        echo "已创建socket服务".$socket_add."\n";
        @stream_set_blocking($this->socket , 0);
    }

    public function on()
    {
        //
    }

    //接收客户端连接的请求
    public function accept()
    {
        while (true) {
            //建立客户端的连接
            $conn = @stream_socket_accept($this->socket);
            //stream_set_blocking( $conn , 0);

            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this,$conn);
            }

            // tcp 处理 大数据 重复多发几次
            //  $buffer = "";
            // while (!feof($client)) {
            // $buffer = $buffer.fread($client, 65535);
            //  }

            $data = @fread($conn, 65535);

            if (is_callable($this->onReceive)) {
                ($this->onReceive)($this,$conn, $data);
            }

            //为了保持长连接,注释以下内容,关闭连接
            //fclose($conn);
        }
    }



    //启动服务
    public function start()
    {
        $this->accept();
    }
}