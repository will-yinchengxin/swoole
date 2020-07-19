<?php
/**
 * Create By: Will Yin
 * Date: 2020/7/13
 * Time: 14:51
*/
namespace SwoCloud\Tools;

//模拟轮询方式,进行一个负载均衡的算法(可自行添加其他算法如:hash)
class Arithmetic{

    //定义一个属性用于记忆每个服务端被访问的次数
    protected static $roundLastIndex = 0;

    /**
     * 轮询算法
     */
    public static function round(array $list){
        //当前index
        $currentIndex = self::$roundLastIndex;
        $url = $list[$currentIndex];
        if($currentIndex + 1 > count($list) - 1){
            self::$roundLastIndex=0;
        }else{
            self::$roundLastIndex++;
        }
        return $url; //返回当前url
    }

    /**
     * fun_name/fun_work:随机算法
     * @param
     * @param
     * @return
     */
    public function rand()
    {
        
    }

   //...其他算法
}