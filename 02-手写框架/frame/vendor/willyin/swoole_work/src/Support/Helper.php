<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/8
 * Time: 15:03
 **/
use SwooleWork\Foundation\Application;
use SwooleWork\Console\Input;

//封装成app助手函数
//PHP Fatal error:  Cannot redeclare app() (previously declared in /www/swoole/swoole_work/src/Support/Helper.php:10) in /www/swoole/swoole_work/src/Support/Helper.php on line 16
//这里做一个判断

if (!function_exists('app')) {
    function app($a = null)
    {
        if (empty($a)) {
            return Application::getInstance();
        }
        return Application::getInstance()->make($a);
    }
}


//创建一个快捷的打印方法
if (!function_exists('dd')) {
    function dd($message, $description = null)
    {
        Input::info($message, $description);
    }
}