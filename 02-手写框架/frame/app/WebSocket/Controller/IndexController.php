<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/10
 * Time: 15:48
 **/
namespace App\WebSocket\Controller;
Class IndexController{
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