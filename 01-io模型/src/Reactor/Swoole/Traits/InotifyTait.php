<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/1
 * Time: 17:43
 **/
namespace Willyin\Io\Reactor\Swoole\Traits;

trait InotifyTait{
    //记录inotify
    protected $inotify = null;

    public function watchEvent()
    {
        return function ($event){
            $action = 'file:';
            //以下均为inotify事件(https://php.golaravel.com/inotify.constants.html)
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
            debug('worker reloaded by inotify :'.$action." : ".$event['name']);
            // posix_kill — 向进程发送信号
            // 这是整个方法中最核心的方法 ， 其余的全部是做装饰的,当监控文件发生变化,就重启进程
            posix_kill((pidGet($this->config['masterPidFiles']))[0], SIGUSR1);
        };
    }
}