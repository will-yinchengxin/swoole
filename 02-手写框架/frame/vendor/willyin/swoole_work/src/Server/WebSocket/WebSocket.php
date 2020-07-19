<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/10
 * Time: 15:17
 **/
namespace SwooleWork\Server\WebSocket;
//引入swoole的websocket服务
use Swoole\WebSocket\Server as SwooleServer;
//以为websocket是继承于httpserver的这里我们可以直接继承httpserver对其进行再一次封装
use SwooleWork\Server\Http\HttpServer;

class WebSocket extends HttpServer
{
    public function createServer()
    {
        $this->swooleServer = new SwooleServer($this->host, $this->port);

        echo ('WebSocket server 开启 : ws://192.168.186.130:'.$this->port );
    }

    protected function initEvent(){
        $this->setEvent('sub', [
            'request' => 'onRequest',
            'open' => "onOpen",
            'message' => "onMessage",
            'close' => "onClose",
        ]);
    }

    public function onOpen(SwooleServer $server, $request) {
        // 需要获取访问的地址？
        Connections::init($request->fd, $request->server['path_info']);

        $return = app('route')->setFlag('Web')->setMethod('open')->match($request->server['path_info'], [$server, $request]);
    }

    public function onMessage(SwooleServer $server, $frame) {
        $path = (Connections::get($frame->fd))['path'];

        $return = app('route')->setFlag('Web')->setMethod('message')->match($path, [$server, $frame]);
    }

   // public function onClose($ser, $fd) {
        //$path = (Connections::get($fd))['path'];

       // $return = app('route')->setFlag('WebSocket')->setMethod('close')->match($path, [$server, $fd]);

        //Connections::del($fd);
   // }

}
