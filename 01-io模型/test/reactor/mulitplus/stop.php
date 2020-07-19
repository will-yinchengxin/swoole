<?php
require __DIR__.'/../../../vendor/autoload.php';
use Willyin\Io\Reactor\Swoole\MulitPlus\Worker;
$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);
$server->stop();
