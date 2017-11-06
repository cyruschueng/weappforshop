<?php
/**
 * Created by PhpStorm.
 * User: ShengYue
 * Date: 2016/7/20
 * Time: 17:24
 */
set_time_limit(0);
require_once __DIR__.'/config.php';
$list = M('active_dadi')->where(" active_type=3 AND end_time < ".time()." AND status = 1 AND is_send_sms = 0")->select();
if($list){
    foreach($list as $active){
        // 找出最高中奖排名
        $max_limit = M('active_dadi_prize')->where(['active_id'=>$active['id'], 'status'=>1])->order("top_to DESC")->find();
        $max_limit = intval($max_limit['top_to']);
        // 获取所有排名记录
        $join_list = M()->query("select * from (select *,(@rowno:=@rowno+1) as rowno from t_active_dadi_join,
              (select (@rowno:=0)) b WHERE active_id='{$active['id']}' AND status = 1 ORDER BY total_vote DESC , update_time ASC )
              c where 1 order by rowno ASC LIMIT $max_limit");
        foreach($join_list as $join){
            $city = M('city')->where(['city_id'=>$join['city_id']])->find();
            // 按名次 发送短信
            $rowno = intval($join['rowno']);
            $prize = M('active_dadi_prize')->where("active_id='{$active['id']}' AND status=1 AND top_from <=$rowno AND top_to>=$rowno")->find();
            if($prize){
                $str = "【美联美客】恭喜您在参加{$city['city_name']}公众号举办的{$active['active_name']}晒图活动中最终排名{$rowno}，获得礼品{$prize['prize_name']}，请进入{$city['city_name']}公众号领取您的晒图大礼吧。";
                echo $join['mobile']."===". $str."\r\n";
                $res = send_msgs($join['mobile'],$str);
            }
        }
        M('active_dadi')->where(['id'=>$active['id']])->save(['is_send_sms'=>1]);
    }
}

// send_msgs($mobile_ids, "【美联美客】网站访问异常，请及时处理~", $code='888888',$openid = 'monitor');

echo "ok";