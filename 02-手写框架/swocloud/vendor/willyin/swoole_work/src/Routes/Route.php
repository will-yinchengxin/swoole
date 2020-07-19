<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/8
 * Time: 15:52
 **/
namespace SwooleWork\Routes;
use SwooleWork\Foundation\Application;

Class Route{
    protected static $instance = null;
    // 路由本质实现是会有一个容器在存储解析之后的路由
    protected $routes = [];
    // 定义了访问的类型
    protected $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];


    // 记录路由的文件地址
    protected $routeMap = [];

    //用于记录请求的方法,作为一个铺垫
    protected $method;

    //设置路由标识
    protected $flag = "";

    protected function __construct( )
    {
        if(app()->getBasePath()){
            $this->routeMap = [
                'Http' => app()->getBasePath().'/route/http.php',
                'Web' => app()->getBasePath().'/route/websocket.php',
            ];
        }else{
            $this->routeMap = [
                'Http' => '/www/swoole/frame/route/http.php',
                'Web' => '/www/swoole/frame/route/websocket.php',
            ];
        }
    }
    //通过循环获取所有的路由
    public function registerRoute()
    {
        foreach ($this->routeMap as $key => $path) {
            /*
            *在这里引用就相当于执行了以下代码
            *  Route::get('index',fuction(){
                    return 'this is a test for route';
                });
                Route::get('index/test',"IndexController@test");
            */
            require_once $path;
        }
        return $this;
    }

    //获取路由方法
    public function getRoutes()
    {
        return $this->routes;
    }

    //给路由添加成为单例模式,构造方法替换成为受保护的
    public static function getInstance()
    {
        if (\is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
//==================================路由请求方式================================>
    public function get($uri, $action)
    {
        return $this->addRoute(['GET'], $uri, $action);
    }

    public function post($uri, $action)
    {
        return $this->addRoute(['POST'], $uri, $action);
    }

    public function any($uri, $action)
    {
        return $this->addRoute(self::$verbs, $uri, $action);
    }
    public function web_socket($uri, $controller)
    {
        $actions = [
            'open',
            'message',
            'close'
        ];
        /*
        *将以上三种情况当成为请求的类型
        */
        foreach ($actions as $key => $action) {
            $this->addRoute([$action], $uri, $controller."@".$action);
        }
    }
//==================================路由请求方式结束================================>
    /**
     * 注册路由
     * @param [type] $methods [description]
     * @param [type] $uri     [description]
     * @param [type] $action  [description]
     */
    protected function addRoute($methods, $uri, $action)
    {
        foreach ($methods as $method ) {
            $this->routes[$method][$uri] = $action;
        }
        return $this;
    }

    /**
     * 根据请求校验路由，并执行方法
     * @return [type] [description]
     */
    public function match($path)
    {
        $action = null;
        foreach ($this->routes[$this->method] as $uri => $value) {
            //对于路由的 '/' 的处理 比如:Route::get('index/test',"IndexController@test");
            //这里就没有 '/' 就要对其处理

            if ($path === ((strpos($uri, "/") != 0 || !(strpos($uri, "/")))? "/".$uri : $uri)) {
                // 匹配到了路由
                $action = $value;
                break;
            }
        }
        if (!empty($action)) {
            // 执行匹配到的方法
            // 1. 判断类型
            // 2.1. 执行闭包
            // 2.2. 执行控制器的方法
            return $this->runAction($action);
        }

        dd("没有找到对应的路由");
        return "<h1>Sorry couldn't find the route</h1>";
        // 失败没有找到路由
    }
    /**
     * 运行路由的方法
     */
    private function runAction($action)
    {
        // 跳过参数解析
        if ($action instanceof \Closure) {
            // 如果是闭包就执行
            return $action();
        } else {
            $namespace = "App\Http\Controller\\";
            // 控制器的方法
            $string = explode("@", $action);
            $controller = $namespace.$string[0];
            $class = new $controller();
            return $class->{$string[1]}();
        }
    }
    public function setMethod($method)
    {
        $this->method = $method;
        //直接返回当前对象,方便后续的链式操作
        return $this;
    }
    //===========================设置路由标识=======================>
    public function setFlag($flag)
    {
        $this->flag = $flag;
        return $this;
    }

}