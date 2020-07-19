<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/11
 * Time: 15:19
 **/
namespace SwoCloud;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Redis;
/**
 * 1. 检测IM-server的存活状态
 * 2. 支持权限认证
 * 3. 根据服务器的状态，按照一定的算法，计算出该客户端连接到哪台IM-server，返回给客户端，客户端再去连接到对应的服务端,保存客户端与IM-server的路由关系
 * 4. 如果 IM-server宕机，会自动从Redis中当中剔除
 * 5. IM-server上线后连接到Route，自动加 入Redis(im-server ip:port)
 * 6. 可以接受来自PHP代码、C++程序、Java程序的消息请求，转发给用户所在的IM-server
 * 7. 缓存服务器地址，多次查询redis
 *
 * 是一个websocket
 */
class Route extends Server
{
    protected $serverKey = 'im_server';
    protected $redis = null;
    protected $dispatcher = null;

    protected $arithmetic = 'round';

    public function onWorkerStart(SwooleServer $server, $worker_id)
    {
        $this->redis = new Redis;
        $this->redis->pconnect("172.10.0.2", 6379);
    }

    public function onOpen(SwooleServer $server, $request) {

    }
    public function onMessage(SwooleServer $server, $frame) {
       //  if(($frame->fd)!=1){
       //      echo "第".(($frame->fd)-1)."连接进来\n";
       //  }
        //dd($frame->fd);
        $data = \json_decode($frame->data, true);
        $fd = $frame->fd;
        $this->getDispatcher()->{$data['method']}($this, $server, ...[$fd, $data]);
    }
    public function onClose(SwooleServer $ser, $fd) {
        echo ("\n客户端已关闭连接\n");
    }
    public function onRequest(SwooleRequest $swooleRequest, SwooleResponse $swooleResponse){
        //解决Chrome两次请求的问题
        if ($swooleRequest->server['path_info'] == '/favicon.ico' || $swooleRequest->server['request_uri'] == '/favicon.ico') {
            $swooleResponse->end();
            return;
        }
        //解决跨域请求的问题\
        //允许请求的地址
        $swooleResponse->header('Access-Control-Allow-Origin',"*");
        //允许的请求方式
        $swooleResponse->header('Access-Control-Allow-Methods',"GET,POST,OPTIONS");

        // 根据方法类型分发处理业务
        $this->getDispatcher()->{$swooleRequest->post['method']}($this, $swooleRequest, $swooleResponse);

    }
    protected function initEvent(){
        $this->setEvent('sub', [
            'request' => 'onRequest',
            'open' => "onOpen",
            'message' => "onMessage",
            'close' => "onClose",
        ]);
    }
    public function createServer()
    {
        $this->swooleServer = new SwooleWebSocketServer($this->host, $this->port);
        echo "Welcome to my server,the address is : ".$this->host.":".$this->port."\n";
    }

    public function getDispatcher()
    {
        if (empty($this->dispatcher)) {
            $this->dispatcher = new Dispatcher;
        }
        return $this->dispatcher;
    }

    public function getRedis()
    {
        return $this->redis;
    }
    public function getServerKey()
    {
        return $this->serverKey;
    }

    /**
     * fun_name/fun_work:获取负载均衡的算法
     * @param
     * @param
     * @return
     */
    public function getArithmetic()
    {
      return $this->arithmetic;
     }

    /**
     * fun_name/fun_work:设置负载负载均衡的算法
     * @param
     * @param
     * @return
     */
    public function setArithmetic($arithmetic)
    {
        if($arithmetic){
          $this->arithmetic = $arithmetic;
        }
         return $this;
    }
}
