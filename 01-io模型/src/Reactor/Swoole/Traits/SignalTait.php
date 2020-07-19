<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/1
 * Time: 17:54
 **/
namespace Willyin\Io\Reactor\Swoole\Traits;
trait SignalTait{
    //就是把fork方法中的子进程回收提取了出来,下面内容时仿照workerman的写法
    public function monitorWorkersForLinux()
    {
        // 信号安装
        pcntl_signal(SIGUSR1, [$this, 'sigHandler'], false);
        //SIGINT关联ctrl+c
        pcntl_signal(SIGINT, [$this, 'sigHandler'], false);
        while (1) {
            \pcntl_signal_dispatch();
            \pcntl_wait($status);
            \pcntl_signal_dispatch();
        }
    }

    public function sigHandler($sig)
    {
        switch ($sig) {
            case SIGUSR1:
                //重启
                $this->reloadSig();
                break;
            case SIGKILL:
                // 停止
                $this->stop();
                break;

            case SIGINT:
                // 停止
                $this->stop();
                break;
        }
    }
}