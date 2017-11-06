<?php
/**
 * Created by PhpStorm.
 * User: ShengYue
 * Date: 2016/7/20
 * Time: 17:24
 */
require_once __DIR__.'/config.php';

//save_img($pic, APP_PATH.'../Public/static/head/'.md5($pic).'.jpg');
//$pic = '/Public/static/head/'.md5($pic).'.jpg';

$user_list = M('users_1')->where(array('wx_head'=>'1'))->select();

foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        save_img($user['wx_pic'], THINK_PATH.'/../'.$pic_url);
        if(is_file(THINK_PATH.'/../'.$pic_url)){
            M('users_1')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}
$user_list = M('users_2')->where(array('wx_head'=>'1'))->select();
foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        save_img($user['wx_pic'], THINK_PATH.'/../'.$pic_url);
        if(is_file(THINK_PATH.'/../'.$pic_url)){
            M('users_2')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}
$user_list = M('users_3')->where(array('wx_head'=>'1'))->select();
foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        save_img($user['wx_pic'], THINK_PATH.'/../'.$pic_url);
        if(is_file(THINK_PATH.'/../'.$pic_url)){
            M('users_3')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}
$user_list = M('users_4')->where(array('wx_head'=>'1'))->select();
foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        save_img($user['wx_pic'], THINK_PATH.'/../'.$pic_url);
        if(is_file(THINK_PATH.'/../'.$pic_url)){
            M('users_4')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}
$user_list = M('users_5')->where(array('wx_head'=>'1'))->select();
foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        save_img($user['wx_pic'], THINK_PATH.'/../'.$pic_url);
        if(is_file(THINK_PATH.'/../'.$pic_url)){
            M('users_5')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}
$user_list = M('users_6')->where(array('wx_head'=>'1'))->select();
foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        save_img($user['wx_pic'], THINK_PATH.'/../'.$pic_url);
        if(is_file(THINK_PATH.'/../'.$pic_url)){
            M('users_6')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}
$user_list = M('users_7')->where(array('wx_head'=>'1'))->select();
foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        save_img($user['wx_pic'], THINK_PATH.'/../'.$pic_url);
        if(is_file(THINK_PATH.'/../'.$pic_url)){
            M('users_7')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}
$user_list = M('users_8')->where(array('wx_head'=>'1'))->select();
foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        save_img($user['wx_pic'], THINK_PATH.'/../'.$pic_url);
        if(is_file(THINK_PATH.'/../'.$pic_url)){
            M('users_8')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}
$user_list = M('users_9')->where(array('wx_head'=>'1'))->select();
foreach($user_list as $user){
    echo $user['wx_pic']."\r\n";
    // down
    if($user['wx_pic']){
        $md5 = md5($user['wx_pic']);
        $path = substr($md5,0,2);
        $pic_url = "/upload/wx/{$path}";
        mkdir(THINK_PATH.'/../'.$pic_url,0755,true);
        $pic_url = "/upload/wx/{$path}/{$md5}.jpg";
        $real_path = (THINK_PATH.'/../'.$pic_url);
        save_img($user['wx_pic'], $real_path);
        if(is_file($real_path)){
            M('users_9')->where(array('id'=>$user['id']))->save(['wx_head'=>$pic_url]);
        }
    }

}


echo "ok";