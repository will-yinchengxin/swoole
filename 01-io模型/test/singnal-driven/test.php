<?php
/**
 * Create By: Will Yin
 * Date: 2020/6/29
 * Time: 15:40
 **/
require __DIR__.'/../../vendor/autoload.php';
use Willyin\Io\SingnalDriven\Test;

$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
    (new Test())->index();
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");

});

$http->start();