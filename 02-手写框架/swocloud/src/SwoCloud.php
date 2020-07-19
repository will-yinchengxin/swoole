<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/11
 * Time: 15:23
 **/
namespace SwoCloud;
class SwoCloud
{
    public function run()
    {
        $routeServer = new Route();
        $routeServer->start();
    }
}