<?php
namespace SwooleWork\Foundation;

use SwooleWork\Container\Container;
use SwooleWork\Event\Event;
use SwooleWork\Server\Http\HttpServer;
use SwooleWork\Server\WebSocket\WebSocket;
use SwooleWork\Routes\Route;
/**
 *
 */
class Application extends Container
{

    const SWOSTAR_WELCOME = "
      _____                     _____     ___
     /  __/             ____   /  __/  __/  /__   ___ __    __  __
     \__ \  | | /| / / / __ \  \__ \  /_   ___/  /  _`  |  |  \/ /
     __/ /  | |/ |/ / / /_/ /  __/ /   /  /_    |  (_|  |  |   _/
    /___/   |__/\__/  \____/  /___/    \___/     \___/\_|  |__|
    ";
    //仿照laravel方式记录项目根目录位置并传递
    protected $basePath = "";

    public function __construct($path = null)
    {
        if (!empty($path)) {
            $this->setBasePath($path);
        }
        //进行常用实例类的容器绑定
        $this->registerBaseBindings();

        //进行初始化方法init(路由的初始化)
        $this->init();

        echo self::SWOSTAR_WELCOME."\n";
    }

    //初始化(路由)
    public function init()
    {
        //将注册的路由绑定进来
        $this->bind('route', Route::getInstance()->registerRoute());
        $this->bind('event', $this->registeEvent());
        //进行打印测试
        //dd(Route::getInstance()->registerRoute()->getRoutes());

        //event事件的注册测试
        //dd($this->make('event')->getEvents());
    }

    //服务的情动
    public function run()
    {
        $httpServer = new HttpServer($this);
        $httpServer->start();
        //$server = new WebSocket($this);
        //$server->start();
    }

    public function registerBaseBindings()
    {
        // 设置单列
        self::setInstance($this);
        //确定绑定的所有内容
        $bind = [
            //需要绑定的内容写入其中
            // 标识 => 对象或闭包
            'index' => (new \SwooleWork\Index()),
            'httpRequest' =>(new \SwooleWork\Message\Http\Request()),
            'config' => (new \SwooleWork\Config\Config()),

        ];

        //通过循环遍历
        foreach ($bind as $key => $val) {
            $this->bind($key,$val);
        }

    }


    //设置项目根目录
    public function setBasePath($path)
    {
        $this->basePath = rtrim($path,"\/");
    }
    //用户获取项目根目录
    public function getBasePath()
    {
       return $this->basePath;
    }


    /**设置event事件
     * [方法名: ]
     * @param
     * @return
     */
    public function registeEvent()
    {
        $event = new Event();
        $file = scandir($this->getBasePath()."/app/Listen");
        foreach ($file as $key => $val) {
            if($val === '.' || $val === '..'){
                  continue;
            }
            //$val = StareListen.php
            $class = 'App\\Listen\\'.explode('.',$val)[0];
            if(class_exists($class)){
                $listen = new $class;
                $event->register($listen->getName(),[$listen,'handler']);
            }

        }
        return $event;
    }
}
