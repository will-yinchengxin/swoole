<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/6
 * Time: 16:18
 **/

require __DIR__.'/../vendor/autoload.php';
use SwooleWork\Index;
use SwooleWork\Foundation\Application;
//echo Application::getInstance()->make('index')->test();
//dd(1);
//echo (new Index())->test()."\n";
//
echo app('index')->test();

//dd(app()->bind());
//echo dd(1);
//use App\App;
//echo 1;
//echo (new App())->index();
//use SwooleWork\Index;



