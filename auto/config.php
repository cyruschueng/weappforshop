<?php
/**
 * Created by PhpStorm.
 * User: ShengYue
 * Date: 2016/7/20
 * Time: 17:24
 */

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

function httpPost($url, $data = null)
{
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1;SV1)");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $temp=curl_exec ($ch);
    curl_close ($ch);
    return $temp;
}

/**
 * @param $mobile
 * @param $content
 */
function  send_msgs($mobile, $message, $code='',$openid = '') {
    $uid = "200117"; $pwd = strtoupper(md5('634131')); $encode = "utf8";$content = base64_encode($message);

    $data = "uid={$uid}&password={$pwd}&encode={$encode}&encodeType=base64&content={$content}&mobile=$mobile";
    $res = httpPost('http://119.90.36.56:8090/jtdsms/smsSend.do',$data);

    return($res);
}


function yue(){
    $uid = "200117"; $pwd = strtoupper(md5('634131'));

    $data = "uid={$uid}&password={$pwd}";
    $res = httpPost('http://119.90.36.56:8090/jtdsms/balance.do',$data);
    // var_dump($res);

    return ($res);
}

// 定义应用目录
define('APP_PATH',__DIR__.'/../Application/');

// 引入ThinkPHP入口文件
require __DIR__.'/../ThinkPHP/ThinkPHP.php';


