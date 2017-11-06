<?php
/**
 * Created by PhpStorm.
 * User: ShengYue
 * Date: 2016/7/20
 * Time: 17:24
 */
set_time_limit(0);
require_once __DIR__.'/config.php';
$last_file = APP_PATH.'/logs/cash.log';
$time = time() - 1800;
$list = M('users_cash_record')->where("created_at<$time AND status=0")->select();
$bool = false;

foreach($list as $t){
    if($t['remark'] && !$t['is_send']){
        M('users_cash_record')->where(['id'=>$t['id']])->save(['is_send'=>1]);
        $bool = true;
    }
}

if($bool){
    $config = M('auto_msg')->where(['send_type'=>2, 'status'=>1])->find();
    if($config){
        $mobile_ids = join(',', explode("\r\n", $config['send_member']));
        $total = intval($total);
        send_msgs($mobile_ids, "【美联美客】提示您，有提现失败记录，请去后台查看原因,并赶紧处理~", $code='888888',$openid = 'monitor');
    }

}
echo "ok";