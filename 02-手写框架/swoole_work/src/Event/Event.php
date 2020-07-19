<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/11
 * Time: 17:16
 **/
namespace SwooleWork\Event;
class Event
{
    /**
     * 事件的记录参数
     * [
     * 'event.flag' => [
     * 'callback' => Closure
     * ]
     * ]
     * @var array
     */
    protected $events=[];
    /**
     * 事件注册
     * @param string $event 事件标识
     * @param \Closure $callback 事件回调
     */
    public function register($event, $callback){
        //不区分大小写
        $event=strtolower($event);
        /*
         *判断事件是否存在
         * if(){}
        */
        //var_dump($event,'注册');
        if(!isset($this->events[$event])){
            $this->events[$event]=[];
        }
        $this->events[$event]=['callback' => $callback];
    }
    /**
     * 事件的触发
     * @param string $event 事件标识
     * @param array $params 事件参数
     * @return boolean 执行结果
     */
    public function trigger($event, $params=[]){
        $event=strtolower($event);
        if(isset($this->events[$event])){
            ($this->events[$event]['callback'])(...$params);
            return true;
        }
        return false;
    }
    /*
    * 事件的调试
    */
    public function getEvents($event = null)
    {
        return empty($event) ? $this->events : $this->events[$event];
    }

}