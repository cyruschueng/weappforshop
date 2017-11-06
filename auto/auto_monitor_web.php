<?php
/**
 * Created by PhpStorm.
 * User: ShengYue
 * Date: 2016/7/20
 * Time: 17:24
 */
set_time_limit(0);
require_once __DIR__.'/config.php';
$last_file = APP_PATH.'/logs/web.log';
$html = httpPost('http://hd.kuaiduodian.com/index.php?s=/index/index/type/28/from/4.html');

if(!preg_match("/我的余额/", $html)){
    sleep(3);
    $html = httpPost('http://hd.kuaiduodian.com/index.php?s=/index/index/type/28/from/4.html');
    if(!preg_match("/我的余额/", $html)){
        sleep(3);
        $html = httpPost('http://hd.kuaiduodian.com/index.php?s=/index/index/type/28/from/4.html');
        if(!preg_match("/我的余额/", $html)){
            // 重复三次报错
            if(file_exists($last_file)){
                $last_post = intval(file_get_contents($last_file));
            }
            if($last_post < time()-3600) {
                file_put_contents($last_file, time());
                $config = M('auto_msg')->where(['send_type'=>3, 'status'=>1])->find();
                if($config){
                    $mobile_ids = join(',', explode("\r\n", $config['send_member']));
                    $total = intval($total);
                    send_msgs($mobile_ids, "【美联美客】网站访问异常，请及时处理~", $code='888888',$openid = 'monitor');
                }
            }

        }
    }
}

echo "ok";