<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function index(){
        /**
         * modify by maofei for 1057优乐广播朗读比赛后台 at 2017-3-23
         */
        $uname=$this->admin['uname'];


       $this->display();
    }

    public function report_data($ret=true) { /*活动数据导出*/
        $callback = $_GET['callback'];
        $class = I('class',0,'intval');

        $a = array();
        $b = array();
        $c = array();

        if (!$ret) {
            return [];
        }
        if($class == 1){
            $time = strtotime(date("Y-m-d",strtotime("-30 day")));
            $ShortVideoTj = D('ShortVideoTj')->where("day > '$time'")->order('id asc')->select();
            $activity_pvuv = $ShortVideoTj;
            $sum_playnum = 0;
            foreach ($activity_pvuv as $key => $value) {
                $day = $value['day']*1000;
                $pv =  intval($value['pv']);
                $a[] = [$day,$pv];
                $uv =   intval($value['uv']);
                $b[] = [$day,$uv];
                $pvex =   intval($value['playnum']-$sum_playnum ); //注意播放量要累减
                $sum_playnum = $value['playnum'];
                $c[] = [$day,$pvex];
            }
            $data =array($a,$b,$c);
        }elseif($class == 2){
            $time = strtotime(date("Y-m-d",strtotime("-30 day")));
            $ShortVideoTj = D('ShortVideoTjApp')->where("day > '$time'")->order('id asc')->select();
            $activity_pvuv = $ShortVideoTj;
            $sum_playnum = 0;
            foreach ($activity_pvuv as $key => $value) {
                $day = $value['day']*1000;
                $pv =  intval($value['pv']);
                $a[$day] = [$day,$pv+intval($a[$day][1])];
                $uv =   intval($value['uv']);
                $b[$day] = [$day,$uv+intval($b[$day][1])];
                $pvex =   intval($value['playnum']-$sum_playnum ); //注意播放量要累减
                $sum_playnum = $value['playnum'];
                $c[$day] = [$day,$pvex+intval($c[$day][1])];
            }
            $new_a = $new_b = $new_c = [];
            foreach($a as $item){
                $new_a[] = $item;
            }
            foreach($b as $item){
                $new_b[] = $item;
            }
            foreach($c as $item){
                $new_c[] = $item;
            }
            $data =array($new_a,$new_b,$new_c);
        }else{
            $time = strtotime(date("Y-m-d",strtotime("-30 day")));
            $ShortVideoTj = D('ShortVideoTj')->where("day > '$time'")->order('id asc')->select();
            $activity_pvuv = $ShortVideoTj;
            $sum_playnum = 0;
            foreach ($activity_pvuv as $key => $value) {
                $day = $value['day']*1000;
                $pv =  intval($value['pv']);
                $a[$day] = [$day,$pv];
                $uv =   intval($value['uv']);
                $b[$day] = [$day,$uv];
                $pvex =   intval($value['playnum']-$sum_playnum ); //注意播放量要累减
                $sum_playnum = $value['playnum'];
                $c[$day] = [$day,$pvex];
            }

//            $time = strtotime(date("Y-m-d",strtotime("-30 day")));
//            $ShortVideoTj = D('ShortVideoTjApp')->where("day > '$time'")->order('id asc')->select();
//            $activity_pvuv = $ShortVideoTj;
//            $sum_playnum = 0;
//            foreach ($activity_pvuv as $key => $value) {
//                $day = $value['day']*1000;
//                $pv =  intval($value['pv']);
//                $a[$day] = [$day,$pv+intval($a[$day][1])];
//                $uv =   intval($value['uv']);
//                $b[$day] = [$day,$uv+intval($b[$day][1])];
//                $pvex =   intval($value['playnum']-$sum_playnum ); //注意播放量要累减
//                $sum_playnum = $value['playnum'];
//                $c[$day] = [$day,$pvex+intval($c[$day][1])];
//            }
            $new_a = $new_b = $new_c = [];
            foreach($a as $item){
                $new_a[] = $item;
            }
            foreach($b as $item){
                $new_b[] = $item;
            }
            foreach($c as $item){
                $new_c[] = $item;
            }
            $data =array($new_a,$new_b,$new_c);

        }


        echo $callback.'('.json_encode($data).');';
    }

    public function index1(){

        // 所有H5用户数
        $total_user = 0;
        for($i=1; $i<10;$i++){
            $total_user += M("users_{$i}")->where(['cityid'=>95])->count();
        }
        
  
        // 所有播放量
        $total_play = 0;
        if($this->admin['role'] > 1){
            $total_play = intval(M('short_video')->where(['uid'=>$this->admin['id']])->sum('play_num'));
        }else{
            $item = M("short_video_tj")->order("id DESC")->find();
            $total_play += $item['playnum'];

            $item = M("short_video_tj_app")->where(['apptype'=>1])->order("id DESC")->find();
            $total_play += $item['playnum'];

            $item = M("short_video_tj_app")->where(['apptype'=>2])->order("id DESC")->find();
            $total_play += $item['playnum'];
        }

        $this->assign('total_play', $total_play);

        $where = ['is_del'=>0,'status'=>1];
        if($this->admin['role'] > 1){
            $where['uid'] = $this->admin['id'];
        }
        // 所有视频
        $item = M('short_video')->where($where)->count();

        $this->assign('total_video', $item);

        // 时间
        $this->assign("from_date", date("Y-m-d",strtotime("-10 day")));
        $this->assign("to_date", date("Y-m-d"));

        $this->assign('update_date', date("Y-m-d 00:00"));
        $this->display();
    }

    /**
     * 获取首页数据
     */
    public function ajax_index_data(){
        $from_date = I("request.from_date","","trim");
        if(empty($from_date)){$from_date = date("Y-m-d",strtotime("-10 day"));}
        $to_date = I("request.to_date","","trim");
        if(empty($to_date)){$to_date = date("Y-m-d");}
        $ardb = M('user','t_',C('AR_HERE_DB'));
        $total_user_new_data = [];
        $total_user_all_data = [];
        $total_user_all_play = [];
        $total_user_new_play = [];
        $total_user_new_video= [];
        $total_user_all_video= [];

        $new_date = strtotime($from_date);
        $new_date_str = $from_date;
        $last_pay = $last_h5_pay = $last_app_pay = 0;
        $bool = true;
        do{
            // 用户新增数统计
            $total_user_new = 0;
            for($i=1; $i<10;$i++){
                $total_user_new += M("users_{$i}")->where("cityid=95 AND create_time>'{$new_date}' AND create_time < '".($new_date+86400)."'")->count();
            }
            $total_user_new += $ardb->where("create_date>'{$new_date}' AND create_date < '".($new_date+86400)."'")->count();
            $total_user_new_data[] = $total_user_new;

            // 用户所有数统计
            $total_user_all = 0;
            for($i=1; $i<10;$i++){

                $total_user_all += M("users_{$i}")->where("cityid = 95 AND create_time < '".($new_date+86400)."'")->count();
            }
            $total_user_all += $ardb->where("create_date < '".($new_date+86400)."'")->count();
            $total_user_all_data[] = $total_user_all;

            // 所有新增播放量
            $total_new_play = 0;
            $item = M("short_video_tj")->where(['day'=>$new_date])->find();
            $play_num =  $item['playnum'];

            if($bool){
                $item = M("short_video_tj")->where(['day'=>$new_date-86400])->find();
                $play_num = $play_num - $item['playnum'];
            }else{
                $play_num = $play_num - $last_pay;
            }
            $last_pay = $item['playnum'];
            $total_new_play += $play_num;

//            $item = M("short_video_tj_app")->where(['day'=>$new_date,'apptype'=>1])->find();
//            $play_h5_num =  $item['playnum'];
//            if($i == 0){
//                $item = M("short_video_tj_app")->where(['day'=>$new_date-86400,'apptype'=>1])->find();
//                $play_h5_num = $play_h5_num - $item['playnum'];
//
//            }else{
//                $play_h5_num = $play_h5_num - $last_h5_pay;
//            }
//            $last_h5_pay = $item['playnum'];
//            $total_new_play += $play_h5_num;
//
//            $item = M("short_video_tj_app")->where(['day'=>$new_date,'apptype'=>2])->find();
//            $play_app_num = $item['playnum'];
//            if($i == 0){
//                $item = M("short_video_tj_app")->where(['day'=>$new_date-86400,'apptype'=>2])->find();
//                $play_app_num = $play_app_num - $item['playnum'];
//            }else{
//                $play_app_num = $play_app_num - $last_app_pay;
//            }
//            $last_app_pay = $item['playnum'];
//            $total_new_play += $play_app_num;
            $total_user_new_play[] = $total_new_play;

            // 所有播放量
            $total_play = 0;
            $item = M("short_video_tj")->where(['day'=>$new_date])->find();
            $total_play += $item['playnum'];
//            $item = M("short_video_tj_app")->where(['day'=>$new_date,'apptype'=>1])->find();
//            $total_play += $item['playnum'];
//            $item = M("short_video_tj_app")->where(['day'=>$new_date,'apptype'=>2])->find();
//            $total_play += $item['playnum'];
            $total_user_all_play[] = $total_play;

            // 视频量统计
            $total_user_new_video[] = M('short_video')->where("is_del=0 AND status=1 AND FROM_UNIXTIME( create_time, '%Y-%m-%d' )='{$new_date_str}'")->count();
            $total_user_all_video[] = M('short_video')->where("is_del=0 AND status=1 AND create_time<'".($new_date+86400)."'")->count();

            $date_data[] = date("m/d",$new_date+86400);
            $new_date = $new_date + 86400;
            $new_date_str = date("Y-m-d", $new_date + 86400);
            if($new_date_str > $to_date){
                break;
            }
            $bool = false;
        }while(true);

        $this->ajaxReturn(["total_date"=>$date_data,"total_user_all"=>$total_user_all_data, "total_user_new"=>$total_user_new_data,
            "total_play_all"=>$total_user_all_play,"total_play_new"=>$total_user_new_play,"total_video_all"=>$total_user_all_video,"total_video_new"=>$total_user_new_video]);

    }

    /**
     * 用户退出登录
     */
    public function logout(){
        session('is_login', 0);
        session('login_user_id', 0);
        $this->success('安全退出',U('/login/index'));
    }

    /**
     * 手机用户数：55， 微信登录用户数：106，QQ登录用户数：41, 公众号用户数：19764 ，公众号关注数：4252, 视频总数:
    342，播放总数:
    16019，点赞总数:465，评论总数:339，收藏总数:90
     */
    public function tongji(){
        $arhere = M('user','t_',"mysql://qd:Qd888@10.104.144.191:3306/arhere");
        $data['total_mobile_user'] = $arhere->where("status = 1 AND mobile<>''")->count();

        $data['total_weixin_user'] = $arhere->where("status = 1 AND openid<>''")->count();

        $data['total_qq_user'] = $arhere->where("status = 1 AND qq<>''")->count();

        $data['total_app_user'] = $arhere->where("status = 1")->count();

        $data['total_mp_user'] = 0;
        $data['total_subscribe_user'] = 0;
        for($i=1; $i<10; $i++){
            $data['total_mp_user']  += M("users_".$i)->where(['cityid'=>95])->count();
            $data['total_subscribe_user']  += M("users_".$i)->where(['cityid'=>95, "is_subscribe"=>1])->count();
        }

        $data['total_video'] = M('short_video')->where(['is_del'=>0, 'status'=>1])->count();

        $data['total_video_play'] = M('short_video')->where(['is_del'=>0, 'status'=>1])->sum('play_num');
        $data['total_video_favorite'] = M('short_video')->where(['is_del'=>0, 'status'=>1])->sum('favorite_num');
        $data['total_comment'] = M('short_video_comment')->count();
        $data['total_collection'] = M('short_video_collection')->count();


        $this->assign('data', $data);

        $this->display();

    }
}