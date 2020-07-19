<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/9
 * Time: 16:35
 **/
namespace SwooleWork\Config;
Class Config{
    protected $itmes = [];
    protected $configPath = '';
    function __construct()
    {
        //获取配置文件路径
        $this->configPath = app()->getBasePath().'/config';
        // 读取配置
        $this->itmes = $this->configread();
        //打印测试
         //dd($this->itmes);
    }
    /**
     * 读取PHP文件类型的配置文件
     * @return [type] [description]
     */
    protected function configread()
    {
        // 1. 找到文件
        // 此处跳过多级的情况
        $files = scandir($this->configPath);
        $data = null;
        // 2. 读取文件信息
        foreach ($files as $key => $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            // 2.1 获取文件名(stristr查找 指定 字符 在 字符串 中的第一次出现，并返回字符串的剩余部分：,加true则返回)
            $filename = \stristr($file, ".php", true);
            // 2.2 读取文件信息
            if($filename){
                $data[$filename] = include $this->configPath."/".$file;
            }

        }
        // 3. 返回
            return $data;
    }
   //获取配置文件的方法
    public function getConfig($keys)
    {   //获取配置信息
        //getConfig('http.server.port');
        $data = $this->itmes;
        foreach (\explode('.', $keys) as $key => $value) {
            $data = $data[$value];
        }
        return $data;
    }
}