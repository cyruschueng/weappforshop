<?php
/**
 * Created by PhpStorm.
 * User: ShengYue
 * Date: 2016/7/20
 * Time: 17:24
 */
set_time_limit(0);
require_once __DIR__.'/config.php';

//连接测试数据库
$connectArray=['host'=>'115.159.215.105','username'=>'qd','passwd'=>'Qd888','db'=>'db_hd_v4'];
$conTest = new mysqli($connectArray['host'],$connectArray['username'],$connectArray['passwd'],$connectArray['db']);

if ($conTest->connect_error){
    file_put_contents("/data/log/short.log", date("Y-m-d H:i:s")."回调转码连接测试数据库失败: ".$conTest->connect_error,FILE_APPEND);

}

$last_total = APP_PATH.'/logs/monitor.log';
//echo encrypt_password('MlMk2016','adflernsk=');die();
//$raw_post_data = file_get_contents('php://input', 'r'); 
//file_put_contents( $last_total, date("Y-m-d H:i:s")."==\r\n",FILE_APPEND);
//file_put_contents($last_total,  json_encode($_GET)."==\r\n",FILE_APPEND);
//file_put_contents($last_total,  json_encode($_POST)."==\r\n",FILE_APPEND);
//file_put_contents($last_total,  $raw_post_data,FILE_APPEND); 
//
//$event = json_decode($raw_post_data,true);
$ShortVtans = D('ShortVtans')->select();
print count()."\r\n";
$ii=1;
foreach ($ShortVtans as $keyVtans  => $valueVtans ) {
    $event = json_decode($valueVtans['tanstatus'],true);

$eventType = $event['eventType'];
if($eventType == 'TranscodeComplete'){
 
    $event_data = $event['data'];
    $event_data_playSet = $event_data['playSet'];
    $fileId = $event_data['fileId'];
    
    print "fileId ======== {$fileId}\r\n";
    $data = array();
    $ShortVideo = M('ShortVideo')->where(['url'=>$fileId, 'is_del'=>0])->find();
    if(empty($ShortVideo)){
        file_put_contents( $last_total, date("Y-m-d H:i:s")."cron  正式库还未有数据 {$fileId}"."\r\n",FILE_APPEND);
        //continue;
    }
    if(empty($ShortVideo['pic'])){
        $data['pic'] = $event_data['coverUrl'];

    }
    $data['original_pic'] = $event_data['coverUrl'];
    $data['duration'] = $event_data['duration'];
    $data['fileName'] = $event_data['fileName'];
    $data['definition'] = 10000;
    $data['transfer_url'] = json_encode($event_data_playSet);//转码后地址
    //转码失败
    if($event_data['status'] <0 || $event_data['message'] =="convert fail"){
        $data['status'] = 5;
    }else{
        $data['status'] = 1;
    }
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
        if(strpos($value['url'],"f0.f20.mp4") !==false){
            $data['vbitrate'] = $value['vbitrate'];
            $data['vheight'] = $value['vheight'];
            $data['vwidth'] = $value['vwidth'];
            $data['definition'] = $value['definition'];
            $data['ourl'] = $value['url'];
        }elseif((strripos($value['url'],".mp4") + 4) == strlen($value['url'])){
            if($value['definition'] < $data['definition'] ){
                $data['vbitrate'] = $value['vbitrate'];

                $data['vheight'] = $value['vheight'];
                $data['vwidth'] = $value['vwidth'];
                $data['definition'] = $value['definition'];
                $data['ourl'] = $value['url'];
            }
        }

    }
    $data['direction'] ='horizontal';
    if($data['vheight'] > $data['vwidth']){
        $data['direction']  = 'vertical';
    }
   
        $data['update_time'] = time();
         print json_encode($data)."\r\n";

        $res = D('ShortVideo')->where(array('url'=>$fileId))->save($data);
    print '更改正式结果  ---'." $res  \r\n";
        //begin 正式库更新失败，去测试库更新
        if(!$res){
        	$transfer_url = json_encode($event_data_playSet);//转码地址
            $videoPic=$event_data['coverUrl'];//默认第一帧
            //是否有上传封面图
            $thesql0="select * from t_short_video where url='{$fileId}'";
            $sqlres0=$conTest->query($thesql0);
            while($row = $sqlres0->fetch_array()) {
                if(!empty($row["pic"])){
                    $videoPic=$row["pic"];//封面图
                }
            }

            $SQL="UPDATE `t_short_video` SET status=1, pic='{$videoPic}',original_pic='{$event_data['coverUrl']}', duration='{$event_data['duration']}',fileName='{$event_data['fileName']}',definition=10000, vbitrate='{$data['vbitrate']}',vheight='{$data['vheight']}',vwidth='{$data['vwidth']}', definition='{$data['definition']}', transfer_url='{$transfer_url}', ourl='{$data['ourl']}', direction='{$data['direction']}', update_time='{$data['update_time']}'    WHERE url='{$fileId}'";
            print '试着更新测试环境  ---'." $SQL  \r\n";
            $sqlres=$conTest->query($SQL);
            print 'affected_rows   ---'." {$conTest->affected_rows} ----  {$sqlres} \r\n";
            if($conTest->affected_rows >0){
                //执行成功
                M('ShortVtans')->where("id='{$valueVtans['id']}'")->delete();

            }
        }
        //end

        if($res){
            $res=M('ShortVtans')->where("id='{$valueVtans['id']}'")->delete();
            file_put_contents($last_total, "cron  ". $res."fileId:".$fileId."\r\n",FILE_APPEND);
        }

                
}
    if(count($ShortVtans) <=$ii){
        $conTest->close();
    }
    $ii++;

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
