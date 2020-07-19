<?php
namespace SwooleWork\Server\Http;

use SwooleWork\Index;
use SwooleWork\Server\Server;
use SwooleWork\Message\Http\Request as HttpRequest;
use Swoole\Http\Server as SwooleServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

Class HttpServer extends Server
{
    public function createServer()
    {
        $this->swooleServer = new SwooleServer($this->host, $this->port);
        echo "\n http_server ".$this->host.":".$this->port . " start , welcome! \n";
    }

    protected function initEvent(){
        $this->setEvent('sub', [
            'request' => 'onRequest'
        ]);
    }
    // onRequest
    public function onRequest($request, $response)
    {
        /*
         * 请求地址: http://192.168.100.153:9000/index
         *
         * 结果:
        ======>>>  start
        /index
        ======>>>  end
        ======>>>  start
        /favicon.ico
        ======>>>  end
        */

        /*
        *Chrome 请求两次问题
        */
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            $response->end();
            return;
        }

        //响应请求的方法和路径
        $http_request = HttpRequest::init($request);

        //dd($http_request->getMethod());
       // dd($http_request->getUriPath());

        //执行控制的方法
        $res = app('route')->setMethod($http_request->getMethod())->match($http_request->getUriPath());
        //响应结果
        $response->end($res);
        //响应结果
       // $response->end("<h1>Hello swostar</h1>");
    }
}
