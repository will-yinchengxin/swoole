<?php
namespace SwooleWork\Server;
/**
 * 所有服务的父类， 写一个公共的操作
 */

use SwooleWork\Support\Inotify;
use Swoole\Server as SwooleServer;
use SwooleWork\Foundation\Application;
use SwooleWork\RPC\RPC;

abstract class Server
{
    // 属性
    protected $swooleServer;

    //记录Application传递内容
    protected $app ;

    //判断是否热加载,使用第三方类
    protected $watchFile = false;
    protected $inotify = null;

    //记录task进程数
    protected $config = [
        'task_worker_num' => 0,
    ];

    //记录进程id,方便后期的管理
    protected $pidMap = [
        'masterPid'  => 0,
        'managerPid' => 0,
        'workerPids' => [],
        'taskPids'   => []
    ];



    protected $port = 9000;

    protected $host = "0.0.0.0";

    /**
     * 因为服务需要自定义回调函数
     *
     * @var array
     */
    protected $events = [

        // 这是所有服务均会注册的的回调事件
        "server" => [
            // 事件名      => 事件函数
            "start"        => "onStart",
            "close"        => "onClose",
            "connect"      => "onConnect",
            "managerStart" => "onManagerStart",
            "managerStop"  => "onManagerStop",
            "shutdown"     => "onShutdown",
            "workerStart"  => "onWorkerStart",
            "workerStop"   => "onWorkerStop",
            "workerError"  => "onWorkerError",
        ],

        //记录继承类(子类服务)
        'sub' => [

        ],

        //记录额外的回调函数
        'ext'=>[]
    ];

    public function __construct(Application $app)
    {
        //就录了Application中传递的必要变量
        $this->app = $app;
        //配置文件信息的读取
        $this->configInit();
        //启动服务需要执行的事件
        $this->createServer();
        //设置配置信息
        $this->swooleServer->set($this->config);
        //设置需要注册的回调事件
        $this->initEvent();
        //设置swoole的回调函数
        $this->setSwooleEvent();
    }

    public function start()
    {
        //判断是否开启多监听端口
        if (app('config')->getConfig('http_server.http.tcpable')) {
            new RPC($this->swooleServer, app('config')->getConfig('http_server.http.rpc'));
        }

        //启动服务
        $this->swooleServer->start();

    }

    //创建服务
    protected abstract function createServer();

    //初始化监听事件
    protected abstract function initEvent();

    // 设置swoole的回调事件
    protected function setSwooleEvent()
    {
        foreach ($this->events as $type => $events) {
            foreach ($events as $event => $func) {
               // var_dump($func);
                $this->swooleServer->on($event, [$this,$func]);
            }
        }
    }

    // ===================>通用的方法开始=============================>
    public function onStart(SwooleServer $server)
    {
        $this->pidMap['masterPid'] = $server->master_pid;
        $this->pidMap['managerPid'] = $server->manager_pid;
        //var_dump($this->pidMap);
        //判断是否开启文件监听
        if($this->watchFile){
            //通过Application中的方法获取项目的根目录
            $this->inotify = new Inotify($this->app->getBasePath(), $this->watchEvent());
            $this->inotify = new Inotify($this->app->getBasePath(), $this->watchEvent());
            $this->inotify->start();
        }

        $this->app->make('event')->trigger('starelisten');
    }

    public function onClose(SwooleServer $server)
    {

    }
    public function onShutdown(SwooleServer $server)
    {

    }
    public function onWorkerStart(SwooleServer $server)
    {
        $this->pidMap['workerPids'] = [
            'id'  => $worker_id,
            'pid' => $server->worker_id
        ];
    }
    public function onWorkerStop(SwooleServer $server, int $worker_id)
    {

    }

    public function onWorkerError(SwooleServer $server, int $workerId, int $workerPid, int $exitCode, int $signal)
    {

    }

    public function onConnect(SwooleServer $server)
    {

    }

    public function onManagerStart(SwooleServer $server)
    {

    }
    public function onManagerStop(SwooleServer $server)
    {

    }
    // ===================>通用的方法结束=============================>

    //设置子类的回调方法
    public function setEvent($type, $event)
    {
        // 暂时不支持直接设置系统的回调事件
        if ($type == "server") {
            return $this;
        }
        $this->events[$type] = $event;
        return $this;
    }

    //设置config内容
    public function setConfig($config)
    {
        //array_map(必需 用户自定义函数的名称,数组1,数组2...)
        $this->config = array_map($this->config, $config);
        return $this;
    }

    //获取config内容
    public function getConfig()
    {
        return $this->config;
    }


    //inotify的回电函数
    protected function watchEvent()
    {
        return function($event){
            $action = 'file:';
            switch ($event['mask']) {
                case IN_CREATE:
                    $action = 'IN_CREATE';
                    break;

                case IN_DELETE:
                    $action = 'IN_DELETE';
                    break;
                case \IN_MODIFY:
                    $action = 'IN_MODIF';
                    break;
                case \IN_MOVE:
                    $action = 'IN_MOVE';
                    break;
            }
            $this->swooleServer->reload();
        };
    }
    // ===================>配置文件内容的获取=============================>
    /**初试配置文件
     * [方法名: ]
     * @param
     * @return
     */
    public function configInit()
    {
        $config = app('config');
        $this->port = $config->getConfig('http_server.http.host');
        $this->port = $config->getConfig('http_server.http.port');
    }
}
