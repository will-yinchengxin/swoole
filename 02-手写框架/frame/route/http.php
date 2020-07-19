<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/8
 * Time: 15:49
 **/
use SwooleWork\Routes\Route;
Route::get('/',function(){
    return "<h1>Weolcome</h1>";
});
Route::get('/index',function(){
    return 'this is a test for route';
});

Route::get('index/test',"IndexController@test");

