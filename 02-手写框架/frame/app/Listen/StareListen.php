<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/11
 * Time: 21:36
 **/
namespace App\Listen;
use SwooleWork\Event\Listen;
use Swoole\Coroutine;

class StareListen extends Listen{
    protected $name = "StareListen";

//    public function handler(){
//
//      dd('this is listen test');
//
//        Coroutine::create(function(){
//            $cli = new \Swoole\Coroutine\Http\Client('0.0.0.0', 9500);
//            $ret = $cli->upgrade("/");
//            //向 WebSocket 服务器推送消息。
//            /*
//             * PHP Warning:  Swoole\Coroutine\Http\Client::push(): websocket handshake failed, cannot push data in /www/swoole/frame/app/Listen/StareListen.php on line 28
//            Warning: Swoole\Coroutine\Http\Client::push(): websocket handshake failed, cannot push data in /www/swoole/frame/app/Listen/StareListen.php on line 28
//
//            出现以上报错信息,是因为传递参数格式不正确,这里我们直接传递一个常量
//            */
//            define("GREETING", "wiilyin");
//            $cli->push(GREETING);
//            $cli->close();
//        });
//    }
    /**
     * [方法名: ]
     * @param
     * @return
     */
    public function handler()
    {
        Coroutine::create(function(){
            $cli = new \Swoole\Coroutine\Http\Client('0.0.0.0', 9500);
            $ret = $cli->upgrade("/"); //升级的websockt
            if ($ret) {
                $data=[
                    'method' =>'register', //方法
                    'serviceName'=>'IM_1',
                    'ip' =>'192.168.100.153',
                    'port' => 9501
                ];
                $cli->push(json_encode($data));
                //心跳处理
                swoole_timer_tick(3000,function ()use($cli){
                    if($cli->errCode==0){
                        $cli->push('',WEBSOCKET_OPCODE_PING); //
                    }
                });
            }
        });
    }
}