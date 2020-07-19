<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/8
 * Time: 15:49
 **/
use SwooleWork\Routes\Route;

//直接调用route中注册的web_socket方式,为websocket独有的路由方式
Route::web_socket('index', 'IndexController');
Route::web_socket('demo', 'DemoController');