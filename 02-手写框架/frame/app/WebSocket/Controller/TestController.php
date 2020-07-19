<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/10
 * Time: 15:51
 **/
namespace App\WebSocket\Controller;
/*
*所有的控制器都因该拥有以下的方法,方便所有的websocket请求
*/
Class TestController{
    public function open($server, $request)
    {
        dd('indexController open');
    }
    public function message($server, $frame)
    {
        $server->push($frame->fd, "this is server");
    }
    public function close($ser, $fd)
    {

    }
}