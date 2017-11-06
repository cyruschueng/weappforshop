<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/6/6
 * Time: 17:03
 */

use Helpers\Helper;
use Helpers\Presenter;
use Redis\MyRedis;



/**
 * 前端picurl
 * @param $pic
 */
function pic_url($pic, $type='',$source=0){
    if($type == 'ipm'){
        if(strstr($pic,'pnggaussian') || strstr($pic,',')){
            $pic = explode(',',$pic)[0];
        }
        if($source === 0){
            return MLMK_IPM_PIC_CDN .$pic;
        }elseif($source == 1){
            return MLMK_QDIPM_PIC_CDN .$pic;
        }elseif($source == 2){
            return MLMK_IPM_PIC_CDN .$pic;
        }
        return MLMK_IPM_PIC_CDN .$pic;
    }elseif($type == 'hd'){
        $ext = "";
        if(strlen($source)>1){
            $ext = $source;
        }
        return MLMK_HD_PIC_CDN. $pic.$ext;
    }else{
        return MLMK_PIC_CDN. $pic;
    }

}

function tans_human_minutes($min){
    if($min<60){
    
        return '0:'.str_pad($min,2,'0',STR_PAD_LEFT);
    }else{
        $min1 = intval($min/60);
        $min2 = intval($min%60);
        return $min1.":".str_pad($min2,2,'0',STR_PAD_LEFT);

    }
}

/**
 * @param $tv_id
 * @return mixed|string
 */
function getActiveName($tv_id)
{
    if(empty($tv_id)){return '';}
    return M('active_dadi')->where(['id'=>$tv_id])->find();
}

/**
 * 举报类型处理
 * @param $report
 * @return string
 */
function getReportMsg($report){
    //投诉类型：1低俗，2标题夸张，3封面令人反感，4含有广告，5疑似抄袭，7其他
    if($report['report_type'] == 1){
        return '低俗';
    }elseif($report['report_type'] == 2){
        return '标题夸张';
    }elseif($report['report_type'] == 3){
        return '封面令人反感';
    }elseif($report['report_type'] == 4){
        return '含有广告';
    }elseif($report['report_type'] == 5){
        return '疑似抄袭';
    }elseif($report['report_type'] == 6){
        return $report['report_content'];
    }
}

/**
 * @param $tv_id
 * @return mixed|string
 */
function getTvName($tv_id)
{
    if(empty($tv_id)){return '';}
    return M('short_tv')->where(['id'=>$tv_id])->find();
}

function getAccessToken($city_id)
{
    $appId = D('city')->where(['city_id' => $city_id])->getField('appid');
    $key = 'wechat_access_token' . $appId;
    $access_token = MyRedis::getTokenInstance()->new_get($key);
    return $access_token;
}

/**
 * 时间戳转日期
 *
 * created by 胡倍玮
 *
 * @param int $timestamp 时间戳
 * @param string $format 默认Y-m-d H:i:s
 * @return null|string 时间戳为空或0返回null，不填返回当前日期
 */
function timestampToDate($timestamp = -1, $format = 'Y-m-d H:i:s')
{
    if (!$timestamp) {
        return null;
    }
    if ($timestamp == -1) {
        return date($format, time());
    } else {
        return date($format, $timestamp);
    }
}

/**
 * http请求
 *
 * @param $url
 * @param mixed $data
 * @return mixed
 */
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    if (class_exists('CURLFile')) {
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
    } else {
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
        }
    }

    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * 展示临时素材
 *
 * @param $type
 * @param $fileName
 * @return string
 */
function getMediaHtml($type, $fileName)
{
    $html = '';
    $filePath = '/upload/' . Helper::MEDIA_PATH . $fileName;
    if ($type == Presenter::MEDIA_TYPE_IMAGE || $type == Presenter::MEDIA_TYPE_THUMB) {
        $html = "<img src=\"{$filePath}\" height=\"200\">";
    } else if ($type == Presenter::MEDIA_TYPE_VOICE) {
        $html = "<audio src=\"{$filePath}\" controls>你的浏览器版本太旧，不支持audio</audio>";
    } else if ($type == Presenter::MEDIA_TYPE_VIDEO) {
        $html = "<video src=\"{$filePath}\" controls style=\"width: 400px !important;\">你的浏览器版本太旧，不支持video</video>";
    }
    return $html;
}

/**
 * 获取城市数组
 * 因为本方法会获取所有城市，不建议在视图的循环里调用，在控制器调用后再传递到视图
 *
 * @return array 数组形如['城市id1' => '城市名称1', ...]
 */
function cityMap()
{
    $city_map = D('City')->getField('city_id,city_name');
    return $city_map;
}

/**
 * 验证手机号
 * @param $mobile
 * @return bool
 */
function is_mobile($mobile){
    if(! preg_match("/^1[34578]\d{9}$/", $mobile)){
        return false;
    }else{
        return true;
    }
}

// 随机字符
function ar_rand($n = 12) {
	$str = '0123456789abcdefghijklmnopqrstuvwxyz';
	$len = strlen($str);
	$return = '';
	for($i=0; $i<$n; $i++) {
		$r = rand(1, $len);
		$return .= $str[$r - 1];
	}
	return $return;
}