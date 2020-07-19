<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/11
 * Time: 21:38
 **/
namespace SwooleWork\Event;
abstract class Listen{
    //监听事件的标识
    protected $name = "listen";
    public abstract function handler();

    /**
     * [方法名: ]
     * @param
     * @return
     */
    public function getName()
    {
        return $this->name;
    }
}