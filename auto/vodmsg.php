

<?php
/**
 * Created by PhpStorm.
 * User: ShengYue
 * Date: 2016/7/20
 * Time: 17:24
 */
set_time_limit(0);
require_once __DIR__.'/config.php';


$last_total = APP_PATH.'/logs/monitor.log';
//echo encrypt_password('MlMk2016','adflernsk=');die();
$raw_post_data = file_get_contents('php://input', 'r'); 
file_put_contents( $last_total, date("Y-m-d H:i:s")."==\r\n",FILE_APPEND);
file_put_contents($last_total,  json_encode($_GET)."==\r\n",FILE_APPEND);
file_put_contents($last_total,  json_encode($_POST)."==\r\n",FILE_APPEND);
file_put_contents($last_total,  $raw_post_data,FILE_APPEND); 

$event = json_decode($raw_post_data,true);
$eventType = $event['eventType'];
//保存原始数据
$theArray['tanstatus'] = $raw_post_data;
$theArray['create_time'] = time();
$result = D('ShortTranscode')->add($theArray);

if($eventType == 'TranscodeComplete'){
    $data['tanstatus'] = $raw_post_data;
    $data['update_time'] = time();
    $data['create_time'] = time();
    $res = D('ShortVtans')->add($data);
    //$res = M('short_vtans_log')->add($data);
    if(!$res){
        file_put_contents($last_total,  "shortVtans添加失败:".M('ShortVtans')->getLastSql(),FILE_APPEND);
    }
    die();
    $event_data = $event['data'];
    $event_data_playSet = $event_data['playSet'];
    $fileId = $event_data['fileId'];
    $data = array();
    $ShortVideo = M('ShortVideo')->where(['url'=>$fileId, 'is_del'=>0])->find();
    if(empty($ShortVideo['pic'])){
        $data['pic'] = $event_data['coverUrl'];
    }
    $data['duration'] = $event_data['duration'];
    $data['definition'] = 10000;
    $data['status'] = 1;
    foreach ($event_data_playSet as $value) {
//      "url":"http://1252144174.vod2.myqcloud.com/8e3436c6vodtransgzp1252144174/2ceaaf0a9031868222904796702/f0.f210.m3u8",
//                "definition":210,
//                "vbitrate":265830,
//                "vheight":180,
//                "vwidth":320
//        http://1252144174.vod2.myqcloud.com/8e3436c6vodtransgzp1252144174/2ceaaf0a9031868222904796702/f0.f10.mp4
        if(empty($value['vheight']) || empty($value['vwidth'])){
            continue;
        }
        if((strripos($value['url'],".mp4") + 4) == strlen($value['url'])){
            if($value['definition'] < $data['definition'] ){
                  $data['vbitrate'] = $value['vbitrate'];
                $data['duration'] = $value['definition'];
                $data['vheight'] = $value['vheight'];
                $data['vwidth'] = $value['vwidth'];
                $data['definition'] = $value['definition'];
            }
            
        }
    }
        $data['update_time'] = time();
                $res = D('ShortVideo')->where(array('url'=>$fileId))->save($data);
                file_put_contents($last_total,  $res,FILE_APPEND); 
                
}


    


echo 'success';


exit();
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