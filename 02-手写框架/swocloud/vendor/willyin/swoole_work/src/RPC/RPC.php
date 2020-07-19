<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/9
 * Time: 21:30
 **/
namespace SwooleWork\RPC;
use Swoole\Server;
use SwooleWork\Console\Input;

Class RPC{
    protected $host ;
    protected $port;
    public function __construct(Server $server, $config)
    {
        $listen = $server->listen($config['host'], $config['port'], SWOOLE_SOCK_TCP);
        $listen->set($config['swoole']);
        $listen->on('connect', [$this, 'connect']);
        $listen->on('receive', [$this, 'receive']);
        $listen->on('close', [$this, 'close']);
        Input::info('tcp监听的地址: '.$config['host'].':'.$config['port'] );
    }
    public function connect($serv, $fd){
        dd("开启了多端口监听,此端口为".$this->port);
    }
    public function receive($serv, $fd, $from_id, $data) {
        $serv->send($fd, 'Swoole: '.$data);
        $serv->close($fd);
    }
    public function close($serv, $fd) {
        echo "Client: Close.\n";
    }
}