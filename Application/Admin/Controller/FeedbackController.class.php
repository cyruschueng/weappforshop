<?php
namespace Admin\Controller;

use Helpers\Helper;
use Helpers\HtmlHelper;
use Helpers\Presenter;
use Redis\MyRedis;
use Think\Controller;
use Think\Page;
use Think\Upload;

/**
 * @shengyue 2016-07-04
 * 视频管理
 * Class CityController
 * @package Admin\Controller
 */
class FeedbackController extends BaseController
{
    public function index()
    {

        $feedback = M('short_video_feedback'); // 实例化User对象
        $count = $feedback->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        $list = $feedback->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $ardb = M('user','t_',C('AR_HERE_DB'));
        foreach($list as $i=>$item){
            if($item['platform'] == 1){
//                $user = M('short_video_admin')->where(['uid'=>$item['uid']])->find();
                $list[$i]['username'] = "未知";//$user['uname'];
                // 用户来源APP
            }elseif($item['platform'] == 2){
                if($item['uid']){
                    $user = $ardb->where(['uid'=>$item['uid']])->find();
                    $list[$i]['username'] = $user['username'];
                }
            }
        }
        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }
}
