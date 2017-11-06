<?php
$config = array(
    //'配置项'=>'配置值'
    'URL_MODEL'            =>3,    //2是去除index.php
    'DB_FIELDTYPE_CHECK'   =>true,
    'TMPL_STRIP_SPACE'     =>true,
    'OUTPUT_ENCODE'        =>true, // 页面压缩输出

    'MODULE_ALLOW_LIST'    =>    array('Admin'),
    'DEFAULT_MODULE'       =>    'Admin',  // 默认模块

    //加密混合值
    'AUTH_CODE' => 'dx',
    //数据库配置
    'URL_CASE_INSENSITIVE' => true,
    'URL_HTML_SUFFIX' => 'html',

//    'SESSION_OPTIONS'=>array(
//        'type'=> 'db',//session采用数据库保存
//        'expire'=>604800,//session过期时间，如果不设就是php.ini中设置的默认值
//        ),

    //
    'AD_REDIS' => array('127.0.0.1', 6379, 5, ''), //6380
    'PRO_REDIS' => array('127.0.0.1', 6381, 5, ''), //产品
    'TOKEN_REDIS' => array('127.0.0.1', 6382, 5, ''), //微信token 及相关
    'TOKEN_GAME' => array('127.0.0.1', 6390, 5, ''), //游戏

    'SESSION_TABLE'=>'hd_sess', //必须设置成这样，如果不加前缀就找不到数据表，这个需要注意
    'TAGLIB_BUILD_IN' => 'cx',//标签库
    //'TAGLIB_PRE_LOAD' => '',//命名范围


    'wxpay'  =>array(
        'applyshop_notify_url'=>"https://small.kuaiduodian.com/pay/wxnotifyurl",
        'wx_notify_url'       =>"https://small.kuaiduodian.com/pay/wxnotifyurl",
        'wx_rznotify_url'       =>"https://small.kuaiduodian.com/pay/wxrznotifyurl"
    ),
      'weixin' => array(
        'appid'  => 'wxcba01efc22c3fcc6', 
        'secret' => '14b02c89574c676eca5d5f3b0795ff59',
        'key'    => 'b64afd9fcf2ec3eb6116682e973bj822',
        'mchid'  => '1487703472'
    ),      

);


$db_config = dirname(__FILE__).'/db_config.php';
$db_config = file_exists($db_config) ? include "$db_config" : array();

return array_merge($db_config,$config);
