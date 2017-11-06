<?php
/**
 * Created by PhpStorm.
 * User: ShengYue
 * Date: 2016/7/20
 * Time: 17:24
 */
set_time_limit(0);
require_once __DIR__.'/config.php';



//echo encrypt_password('MlMk2016','adflernsk=');die();
echo date("Y-m-d H:i:s")."==\r\n";
$total = yue();
$last_post = 0;
$last_file = APP_PATH.'/logs/monitor.log';
$last_total = APP_PATH.'/logs/monitor_total.log';
file_put_contents($last_total, date("Y-m-d H:i:s==").$total);
if(intval($total) < 5000)
{
    sleep(3);
    $total = yue();

    if(intval($total) < 5000){
        sleep(3);
        $total = yue();
        if(intval($total) < 5000){
            if(file_exists($last_file)){
                $last_post = intval(file_get_contents($last_file));
            }
            if($last_post <time()-3600){
                file_put_contents($last_file, time());
                $config = M('auto_msg')->where(['send_type'=>1, 'status'=>1])->find();
                if($config){
                    $mobile_ids = join(',', explode("\r\n", $config['send_member']));
                    $total = intval($total);
                    send_msgs($mobile_ids, "【美联美客】您的账户短信余额不足(还剩：{$total}条)，请及时充值", $code='888888',$openid = 'monitor');
                }

            }
        }
    }

}
echo "ok";