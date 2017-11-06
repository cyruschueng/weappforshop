<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once __DIR__.'/config.php';

error_reporting(0);
$currday = $_GET['date'];

$redis = new Redis();
$redis->connect('10.104.144.191', 6380); 
$cp = $redis->lrange("p:{$currday}:list", 0, -1);
/* under_or_above
  {"openid":"obZe1uHPb_NlNtXrFdRjfi0VfpPI","unionid":"oxDqHs8T6Cp9EYbCKgK3C3_gygg8",
 * "uid":"1","pkid":"26","time":"2016-07-20 17:24:32","money":6.99,"index":"0","uniqid":"26","cityid":1}        */
$aaa = array();
$aaaa = array(); //线上
$uaaa = array(); //线下


$sumarr = array();
$sumarr['city_name'] = '汇总';
$sumarr['city_id'] = 0;
$sumarr['type'] = 0;
$sumarr['ccc'] = 0;
$sumarr['rb'] = 0;
$sumarr['rbmoney'] = 0;
$sumarr['rs'] = 0;
$sumarr['rsmoney'] = 0;
$sumarr['mb'] = 0;
$sumarr['mbmoney'] = 0;
$sumarr['ms'] = 0;
$sumarr['msmoney'] = 0;
$sumarr['ths'] = 0;
$asumarr = array();
$asumarr['city_name'] = '汇总(线上)';
$asumarr['city_id'] = 0;
$asumarr['ccc'] = 0;
$asumarr['rb'] = 0;
$asumarr['rbmoney'] = 0;
$asumarr['rs'] = 0;
$asumarr['rsmoney'] = 0;
$asumarr['mb'] = 0;
$asumarr['mbmoney'] = 0;
$asumarr['ms'] = 0;
$asumarr['msmoney'] = 0;
$asumarr['ths'] = 0;
$asumarr['type'] = 1;
$usumarr = array();
$usumarr['city_name'] = '汇总(线下)';
$usumarr['city_id'] = 0;
$usumarr['ccc'] = 0;
$usumarr['rb'] = 0;
$usumarr['rbmoney'] = 0;
$usumarr['rs'] = 0;
$usumarr['rsmoney'] = 0;
$usumarr['mb'] = 0;
$usumarr['mbmoney'] = 0;
$usumarr['ms'] = 0;
$usumarr['msmoney'] = 0;
$usumarr['ths'] = 0;
$usumarr['type'] = 2;
foreach ($cp as $key => $value) {
    $value = json_decode($value, true);
    $cityid = $value['cityid'];
    $index = $value['index'];
    $money = $value['money'];
    $under_or_above = $value['under_or_above'];
    $aaa[$cityid]['ccc'] = (isset($aaa[$cityid]['ccc']) ? $aaa[$cityid]['ccc'] : 0 ) + 1;
    $sumarr['ccc'] = $sumarr['ccc'] + 1;
    if ($index == 0) {
        $aaa[$cityid]['rb'] = (isset($aaa[$cityid]['rb']) ? $aaa[$cityid]['rb'] : 0 ) + 1;
        $aaa[$cityid]['rbmoney'] = (isset($aaa[$cityid]['rbmoney']) ? $aaa[$cityid]['rbmoney'] : 0) + $money;
        $sumarr['rb'] = $sumarr['rb'] + 1;
        $sumarr['rbmoney'] = $sumarr['rbmoney'] + $money;
        //线上
        if ($under_or_above == 0) {
            $aaaa[$cityid]['ccc'] = (isset($aaaa[$cityid]['ccc']) ? $aaaa[$cityid]['ccc'] : 0 ) + 1;
            $aaaa[$cityid]['rb'] = (isset($aaaa[$cityid]['rb']) ? $aaaa[$cityid]['rb'] : 0 ) + 1;
            $aaaa[$cityid]['rbmoney'] = (isset($aaaa[$cityid]['rbmoney']) ? $aaaa[$cityid]['rbmoney'] : 0) + $money;
            $asumarr['ccc'] = $asumarr['ccc'] + 1;
            $asumarr['rb'] = $asumarr['rb'] + 1;
            $asumarr['rbmoney'] = $asumarr['rbmoney'] + $money;
        } else {// 线下
            $uaaa[$cityid]['ccc'] = (isset($uaaa[$cityid]['ccc']) ? $uaaa[$cityid]['ccc'] : 0 ) + 1;
            $uaaa[$cityid]['rb'] = (isset($uaaa[$cityid]['rb']) ? $uaaa[$cityid]['rb'] : 0 ) + 1;
            $uaaa[$cityid]['rbmoney'] = (isset($uaaa[$cityid]['rbmoney']) ? $uaaa[$cityid]['rbmoney'] : 0) + $money;
            $usumarr['ccc'] = $usumarr['ccc'] + 1;
            $usumarr['rb'] = $usumarr['rb'] + 1;
            $usumarr['rbmoney'] = $usumarr['rbmoney'] + $money;
        }
    } elseif ($index == 1) {
        $aaa[$cityid]['rs'] = (isset($aaa[$cityid]['rs']) ? $aaa[$cityid]['rs'] : 0 ) + 1;
        $aaa[$cityid]['rsmoney'] = (isset($aaa[$cityid]['rsmoney']) ? $aaa[$cityid]['rsmoney'] : 0 ) + $money;
        $sumarr['rs'] = $sumarr['rs'] + 1;
        $sumarr['rsmoney'] = $sumarr['rsmoney'] + $money;
        if ($under_or_above == 0) {
            $aaaa[$cityid]['ccc'] = (isset($aaaa[$cityid]['ccc']) ? $aaaa[$cityid]['ccc'] : 0 ) + 1;
            $aaaa[$cityid]['rs'] = (isset($aaaa[$cityid]['rs']) ? $aaaa[$cityid]['rs'] : 0 ) + 1;
            $aaaa[$cityid]['rsmoney'] = (isset($aaaa[$cityid]['rsmoney']) ? $aaaa[$cityid]['rsmoney'] : 0) + $money;
            $asumarr['ccc'] = $asumarr['ccc'] + 1;
            $asumarr['rs'] = $asumarr['rs'] + 1;
            $asumarr['rsmoney'] = $asumarr['rsmoney'] + $money;
        } else {// 线下
            $uaaa[$cityid]['ccc'] = (isset($uaaa[$cityid]['ccc']) ? $uaaa[$cityid]['ccc'] : 0 ) + 1;
            $uaaa[$cityid]['rs'] = (isset($uaaa[$cityid]['rs']) ? $uaaa[$cityid]['rs'] : 0 ) + 1;
            $uaaa[$cityid]['rsmoney'] = (isset($uaaa[$cityid]['rsmoney']) ? $uaaa[$cityid]['rsmoney'] : 0) + $money;
            $usumarr['ccc'] = $usumarr['ccc'] + 1;
            $usumarr['rs'] = $usumarr['rs'] + 1;
            $usumarr['rsmoney'] = $usumarr['rsmoney'] + $money;
        }
    } elseif ($index == 2) {
        $aaa[$cityid]['mb'] = (isset($aaa[$cityid]['mb']) ? $aaa[$cityid]['mb'] : 0 ) + 1;
        $aaa[$cityid]['mbmoney'] = (isset($aaa[$cityid]['mbmoney']) ? $aaa[$cityid]['mbmoney'] : 0 ) + $money;
        $sumarr['mb'] = $sumarr['mb'] + 1;
        $sumarr['mbmoney'] = $sumarr['mbmoney'] + $money;
        if ($under_or_above == 0) {
            $aaaa[$cityid]['ccc'] = (isset($aaaa[$cityid]['ccc']) ? $aaaa[$cityid]['ccc'] : 0 ) + 1;
            $aaaa[$cityid]['mb'] = (isset($aaaa[$cityid]['mb']) ? $aaaa[$cityid]['mb'] : 0 ) + 1;
            $aaaa[$cityid]['mbmoney'] = (isset($aaaa[$cityid]['mbmoney']) ? $aaaa[$cityid]['mbmoney'] : 0) + $money;
            $asumarr['ccc'] = $asumarr['ccc'] + 1;
            $asumarr['mb'] = $asumarr['mb'] + 1;
            $asumarr['mbmoney'] = $asumarr['mbmoney'] + $money;
        } else {// 线下
            $uaaa[$cityid]['ccc'] = (isset($uaaa[$cityid]['ccc']) ? $uaaa[$cityid]['ccc'] : 0 ) + 1;
            $uaaa[$cityid]['mb'] = (isset($uaaa[$cityid]['mb']) ? $uaaa[$cityid]['mb'] : 0 ) + 1;
            $uaaa[$cityid]['mbmoney'] = (isset($uaaa[$cityid]['mbmoney']) ? $uaaa[$cityid]['mbmoney'] : 0) + $money;
            $usumarr['ccc'] = $usumarr['ccc'] + 1;
            $usumarr['mb'] = $usumarr['mb'] + 1;
            $usumarr['mbmoney'] = $usumarr['mbmoney'] + $money;
        }
    } elseif ($index == 3) {
        $aaa[$cityid]['ms'] = (isset($aaa[$cityid]['ms']) ? $aaa[$cityid]['ms'] : 0 ) + 1;
        $aaa[$cityid]['msmoney'] = (isset($aaa[$cityid]['msmoney']) ? $aaa[$cityid]['msmoney'] : 0) + $money;
        $sumarr['ms'] = $sumarr['ms'] + 1;
        $sumarr['msmoney'] = $sumarr['msmoney'] + $money;
        if ($under_or_above == 0) {
            $aaaa[$cityid]['ccc'] = (isset($aaaa[$cityid]['ccc']) ? $aaaa[$cityid]['ccc'] : 0 ) + 1;
            $aaaa[$cityid]['ms'] = (isset($aaaa[$cityid]['ms']) ? $aaaa[$cityid]['ms'] : 0 ) + 1;
            $aaaa[$cityid]['msmoney'] = (isset($aaaa[$cityid]['msmoney']) ? $aaaa[$cityid]['msmoney'] : 0) + $money;
            $asumarr['ccc'] = $asumarr['ccc'] + 1;
            $asumarr['ms'] = $asumarr['ms'] + 1;
            $asumarr['msmoney'] = $asumarr['msmoney'] + $money;
        } else {// 线下
            $uaaa[$cityid]['ccc'] = (isset($uaaa[$cityid]['ccc']) ? $uaaa[$cityid]['ccc'] : 0 ) + 1;
            $uaaa[$cityid]['ms'] = (isset($uaaa[$cityid]['ms']) ? $uaaa[$cityid]['ms'] : 0 ) + 1;
            $uaaa[$cityid]['msmoney'] = (isset($uaaa[$cityid]['msmoney']) ? $uaaa[$cityid]['msmoney'] : 0) + $money;
            $usumarr['ccc'] = $usumarr['ccc'] + 1;
            $usumarr['ms'] = $usumarr['ms'] + 1;
            $usumarr['msmoney'] = $usumarr['msmoney'] + $money;
        }
    } else {
        if ($under_or_above == 0) {
            $aaaa[$cityid]['ccc'] = (isset($aaaa[$cityid]['ccc']) ? $aaaa[$cityid]['ccc'] : 0 ) + 1;
            $aaaa[$cityid]['ths'] = (isset($aaaa[$cityid]['ths']) ? $aaaa[$cityid]['ths'] : 0 ) + 1;
            $asumarr['ccc'] = $asumarr['ccc'] + 1;
            $asumarr['ths'] = $asumarr['ths'] + 1;
        } else {// 线下
            $uaaa[$cityid]['ccc'] = (isset($uaaa[$cityid]['ccc']) ? $uaaa[$cityid]['ccc'] : 0 ) + 1;
            $uaaa[$cityid]['ths'] = (isset($uaaa[$cityid]['ths']) ? $uaaa[$cityid]['ths'] : 0 ) + 1;

            $usumarr['ccc'] = $usumarr['ccc'] + 1;
            $usumarr['ths'] = $usumarr['ths'] + 1;
        }
        $aaa[$cityid]['ths'] = (isset($aaa[$cityid]['ths']) ? $aaa[$cityid]['ths'] : 0 ) + 1;
        $sumarr['ths'] = $sumarr['ths'] + 1;
    }
}



$city = D('city'); // 实例化User对象
$citylist = $city->select(); // 查询满足要求的总记录数
$fromat_list = array();
foreach ($citylist as $key => $value) {
    $t = array();
    $city_id = $value['city_id'];
    $t['city_name'] = $value['city_name'];
    $t['city_id'] = $city_id;





    $t1 = array_merge($t, $aaa[$city_id]);
    if ($t1) {
        $t1['type'] = 0;
        $fromat_list[] = $t1;
    }
    $t2 = array_merge($t, $aaaa[$city_id]);
    if ($t2) {
        $t2['type'] = 1;
        $t2['city_name'] = $value['city_name'] . " (线上)";
        $fromat_list[] = $t2;
    }
    $t3 = array_merge($t, $uaaa[$city_id]);
    if ($t3) {
        $t3['type'] = 2;
        $t3['city_name'] = $value['city_name'] . " (线下)";
        $fromat_list[] = $t3;
    }
}

$fromat_list[] = $sumarr;
$fromat_list[] = $asumarr;
$fromat_list[] = $usumarr;


//        "p:2016-07-20:list"
echo json_encode($fromat_list);
//        die();
//        @file_put_contents('/data/www/singedpack/' . $currday, json_encode($fromat_list));
//if (file_exists($filename)) {
//    echo @file_get_contents($filename);
//} else {
//    die('no data');
//}
//@file_get_contents('/data/www/singedpack/' . $currday, json_encode($fromat_list));
