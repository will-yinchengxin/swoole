<?php
function debug($data, $flag = false)
{
    if ($flag) {
        var_dump($data);
    } else {
        echo "==== >>>> : ".$data." \n";
    }
}
// 发送信息
function send($client, $data, $flag = false)
{
    if ($flag) {
        fwrite($client, $data);
    } else {
        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
        $response .= "Connection: keep-alive\r\n";
        $response .= "Content-length: ".strlen($data)."\r\n\r\n";
        $response .= $data;
        fwrite($client, $response);
    }
}
/**
 * 用来写入 pid的函数
 */
function pidPut($data, $path){
    (empty($data)) ? file_put_contents($path, null) : file_put_contents($path, $data.'|', 8) ;
}
/**
 * 获取pid的函数
 */
function pidGet($path){
    $string = file_get_contents($path);
    return explode("|",  substr($string, 0 , strlen($string) - 1));
}

function baseDir($dir = null){
    if($dir){
        return $dir ;
    }
    return __DIR__;
}