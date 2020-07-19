<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/12
 * Time: 20:55
 **/
namespace SwoCloud;
use SwoCloud\Tools\Arithmetic;
use Swoole\Server as SwooleServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Redis;

class  Dispatcher
{
    public function register(Route $route,SwooleServer $server, $fd, $data)
    {
        echo "\n开始注册IM服务端信息\n";
        $serverKey = $route->getServerKey();
        // 把服务端的信息记录到redis中
        $redis = $route->getRedis();
        $value = json_encode([
            'ip' => $data['ip'],
            'port' => $data['port'],
        ]);
       $res =  $redis->sadd($serverKey, $value);
       if($res){
           echo "\nIM服务端信息注册成功!\n";
       }
        // 这里是通过触发定时判断，不用heartbeat_check_interval 的方式检测
        // 是因为我们还需要主动清空，redis 数据

        // $timer_id 定时器id
        $server->tick(3000, function($timer_id, Redis $redis,SwooleServer $server, $serverKey, $fd, $value){
        // 判断服务器是否正常运行，如果不是就主动清空
        // 并把信息从redis中移除
            if (!$server->exist($fd)) {
                $redis->srem($serverKey, $value);
                $server->clearTimer($timer_id);
                echo PHP_EOL;
                echo('im server关闭连接(情况不明)缓存数据清空!');
                echo PHP_EOL;
            }
        }, $redis, $server, $serverKey, $fd, $value);
    }

    /**
     * 连接请求登入
     * @param Route $route
     * @param SwooleRequest $swooleRequest
     * @param SwooleResponse $swooleResponse
     */
    public function login(Route $route, SwooleRequest $swooleRequest, SwooleResponse $swooleResponse)
    {
        //这里写死成post请求的方式
        $data = $swooleRequest->post;
        // 用户账号和密码校验
        // 获取连接的服务器
        $server = \json_decode($this->getIMServer($route), true);

       echo(PHP_EOL.'获取的server的 ip 以及 port'.PHP_EOL);

        $url = $server['ip'].':'.$server['port'];
        // 生成token
        $token = $this->getJwtToken($server['ip'], $data['id'], $url);
        dd($token, '生成的token');
        $swooleResponse->end(\json_encode(['token' => $token,'url' => $url]));
    }

    /**
     * 根据算法获取连接服务
     * @param Route $route
     * @return 返回通过负载均衡算法得到的服务端
     */
    protected function getIMServer(Route $route){
        // 从redis中读取信息
        $arr = $route->getRedis()->smembers($route->getServerKey());
        dd($arr, '从redis中获取的请求列表');
        if (!empty($arr)) {
        // 通过算法从中获取到连接的im-server
            return Arithmetic::{$route->getArithmetic()}($arr);
        }
        dd('获取服务器信息失败', 'getIMServer');
        return false;
    }

    /**
     * fun_name/fun_work:用户请求获取token字段
     * @param $sid 服务端id
     * @param $uid 用户id
     * @param $url 服务端地址
     * @return 用户表信息经过token的加密字段
     */
    protected function getJwtToken($sid = null, $uid = null, $url = null){
        // iss: jwt签发者
        // sub: jwt所面向的用户
        // aud: 接收jwt的一方
        // exp: jwt的过期时间，这个过期时间必须要大于签发时间
        // nbf: 定义在什么时间之前，该jwt都是不可用的
        // iat: jwt的签发时间
        // jti: jwt的唯一身份标识，主要用来作为一次性token,从而回避重放攻击
        $key = "swocloud";
        $time = time();
        $token = [
            //"iss" => "http://192.168.100.153",// 可选参数
            //"aud" => "http://192.168.100.146",// 可选参数
            "iat" => $time, //签发时间
            "nbf" => $time, //生效时间
            "exp" => $time + 7200, //过期时间2个小时
            'data' => [
                'uid' => $uid, //用户的id
                'name' => 'client'.$time.$sid,// 用户名
                'service_url' => $url //服务器的地址
            ]
        ];

        /*
         *这里省略了数据库的信息校验过程
        */

        return \Firebase\JWT\JWT::encode($token, $key);
    }
}