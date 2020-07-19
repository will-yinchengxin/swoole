<?php
namespace Willyin\Io\Reactor\Swoole\MulitPlus;
use Swoole\Event;
use Willyin\Io\Tools\Inotify;
use Willyin\Io\Reactor\Swoole\Traits\InotifyTait;
use Willyin\Io\Reactor\Swoole\Traits\ServerTait;
use Willyin\Io\Reactor\Swoole\Traits\SignalTait;

class Worker
{
    //使用超物类
    use InotifyTait;
    use ServerTait;
    use SignalTait;
    protected $config = [
        'worker_num' => 4,
        // 记录子进程pid地址
        'workerPidFiles' => '/www/io/test/reactor/pid/workerPids.txt',
        //据录父进程pid地址
        'masterPidFiles' => '/www/io/test/reactor/pid/masterPidFiles.txt',
        'opts' =>[
            'socket' => [
                // 设置等待资源的个数
                'backlog' => '102400',
            ],
        ],
        //是否开启文件的额监听
        'watch_file' => false,
        //心跳检测
        'heartbeat_check_interval' => '',


        //设置task进程个数
        // 设置task进程的个数
        'task_worker_num' => 0,
        'message_queue_key' => null,
    ];

    public function __construct($socket_address)
    {
        $this->socket_address = $socket_address;
    }
    // 需要处理事情
    public function accept()
    {
        Event::add($this->initServer(), $this->createSocket());
    }

    //通过信号传递信息,杀死一个子进程就随之创建一个
    public function reloadSig($workerNumber = null)
    {
        $workerNum = (empty($workerNumber)) ? $this->config['worker_num'] : $workerNum ;
        $this->stop(false);
        $this->fork($workerNum);
    }

    public function stop($masterKill = true)
    {
        $workerPids = pidGet($this->config['workerPidFiles']);

        foreach ($workerPids  as $key => $workerPid) {
            posix_kill($workerPid, 9);
        }
        pidPut(null, $this->config['workerPidFiles']);
       if($masterKill){
            //读取父进程pid
            $masterPids = pidGet($this->config['masterPidFiles'])[0];
            posix_kill($masterPids, 9);
           $this->inotify->stop();
        }
    }

    // 启动服务的
    public function start()
    {
        debug('start 开始 访问：'.$this->socket_address);
        pidPut(null, $this->config['workerPidFiles']);

        //记录父进程id,先清空,在记录
        pidPut(null, $this->config['masterPidFiles']);
        pidPut(posix_getpid(), $this->config['masterPidFiles']);

        //判断是否开启监听
        if($this->config['watch_file'] == true){
            $this->inotify = new Inotify(baseDir(),$this->watchEvent());
            $this->inotify->start();
        }

        $this->fork();
        $this->monitorWorkersForLinux();
    }

     //设置配置的变变量,类似swoole中的set功能
    public function set($data)
    {
        foreach ($data as $index => $datum) {
            $this->config[$index] = $datum;
        }
    }
}
