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
 * ShortVideoAdmin  short_video_admin
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(100) NOT NULL COMMENT '图片',
`image` varchar(100) NOT NULL COMMENT '图片',
`order` tinyint(3) DEFAULT NULL COMMENT '排序',
`url` varchar(256) NOT NULL COMMENT 'url',
`is_del` tinyint(2) DEFAULT '1' COMMENT '状态：0禁用，1启用',
`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(11) DEFAULT '0',
`begin_time` int(11) DEFAULT '0',
`end_time` int(11) DEFAULT '0',
 */
class ShortVideoAdminController extends BaseController
{
    public function index()
    {
        // $class = I('class', 0, 'intval');
        $ShortVideoAdmin = D('ShortVideoAdmin'); 
      
        $ShortVideoAdmin->where(array('is_del' => 0,'status'=>['in','0,1']));
        $count = $ShortVideoAdmin->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //$this->assign('class', $class);
        $list = $ShortVideoAdmin->where(array('is_del' => 0,'status'=>['in','0,1']))->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 审核列表
     */
    public function check(){
        // $class = I('class', 0, 'intval');
        $ShortVideoAdmin = D('ShortVideoAdmin');

        $ShortVideoAdmin->where(array('is_del' => 0,'role'=>['in','2,3']));
        $count = $ShortVideoAdmin->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //$this->assign('class', $class);
        $list = $ShortVideoAdmin->where(array('is_del' => 0,'role'=>['in','2,3']))->order('status ASC , create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出

        $this->display();
    }

    /**
     * 编辑信息
     */
    public function edit()
    {
        $id = I('request.id', 0, 'intval');

        if (IS_POST) {
            $data = $_POST;
            $data['type'] = join(',', (array)$data['tag_id']);

            if ($id) {
                $data['update_time'] = time();
                unset($data['uname']);
                if($data['pwd']){
                    $salt = ar_rand(12);
                    $data['pwd'] = md5($data['pwd'] . md5($salt)) ; //md5($data['pwd'] . md5($salt));
                    $data['salt'] = $salt;
                }else{
                    unset($data['pwd']);
                }

                $res = D('ShortVideoAdmin')->where(array('id' => $id))->save($data);
            } else {
                $data['update_time'] = time();
                $data['create_time'] = time();
                $user = M('short_video_admin')->where(array('uname'=>$data['uname']))->find();

                if(!empty($user )){
                   return $this->success('换个名字试试吧~');
                }

                if(empty($data['pwd'])){
                    return $this->success('请输入登录密码~');
                }

                $salt = ar_rand(12);
                $data['pwd'] = md5($data['pwd'] . md5($salt)) ; //md5($data['pwd'] . md5($salt));
                $data['salt'] = $salt;

                // $password = encrypt_password($pass, $user['salt']);
                // if($password != $user['pwd']){
                $res = D('ShortVideoAdmin')->add($data);
            }

            // 删除缓存
            if ($res) {
                return $this->success('操作成功', U('/short_video_admin/index'));
            } else {
                return $this->error('操作失败', U('/short_video_admin/edit', array('id' => $id)));
            }
        }

        $short_video_admin = [];
        if ($id) {
            $short_video_admin = M('ShortVideoAdmin')->find($id);
            $short_video_admin['begin_time'] = date('Y-m-d H:i:s', $short_video_admin['begin_time']);
            $short_video_admin['end_time'] = date('Y-m-d H:i:s', $short_video_admin['end_time']);
        }

        $tv_list = M('short_tv')->select();
        $this->assign('tv_list', $tv_list);

        $short_video_admin_tag = array();
        // '1 banner  2热门推荐  3观点  4幽默   5生活',
        $short_video_admin_tag = get_video_type();
        
        $this->assign('short_video_admin_tag', $short_video_admin_tag);
        $this->assign('short_video_admin', $short_video_admin);
        $this->display();
    }

    /**
     * 用户审核操作
     */
    public function checkuser(){
        $id = I('id',0,'intval');
        $status = I('status',2,'intval');
        if($id && $status){
            M('short_video_admin')->where(['id'=>$id])->save(['status'=>$status,'update_time'=>time()]);
        }
        return $this->success('操作成功~');
    }

    /**
     * 用户删除操作
     */
    public function delete()
    {
        $idList = explode(',', $_GET['list']);

        if (empty($idList)) {
            return $this->error('提交的id为空');
        }

        $time = time();
        foreach ($idList as $id) {
            if($id<20){
                     return $this->success('不允许删除');
            }
            D('ShortVideoAdmin')->where(array('id' => $id))->save(array('is_del'=>1,'update_time'=>$time));
        }
        return $this->success('删除成功');
    }

    /**
     * 脏词管理
     */
    public function badword(){
        $show = I('show','','trim');
        $kw = I("kw","","trim");
        $this->assign('show', $show);
        $this->assign('kw', $kw);
        $where = [];
        if($show == 'yes'){
            if($kw){
                $where['badword'] = ['like', "%{$kw}%"];
            }
            $count = M('short_video_badword')->where($where)->count();
            $Page = new Page($count, 200);// 实例化分页类 传入总记录数和每页显示的记录数(25)

            $list = M('short_video_badword')->where($where)->order("id DESC")->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $show = $Page->show();// 分页显示输出
        }

        $this->assign('page', $show);
        $this->assign('badword_list', $list);
        $this->display();
    }

    /**
     * 添加脏词
     */
    public function dobadword(){
        $json = $this->ajax_json();
        do{
            $content = I('content','','trim');
            $export = explode("\n", $content);
            foreach($export as $word){
                $word = trim($word);
                if($word){
                    $has = M('short_video_badword')->where(["badword"=>$word])->find();
                    if(!$has){
                        M('short_video_badword')->add(["badword"=>$word]);
                    }
                }
            }
        }while(false);
        $json['state'] = 200;

        $this->ajaxReturn($json);
    }

    /**
     * 添加脏词
     */
    public function delbadword(){
        $json = $this->ajax_json();
        do{
            $id = I('id',0,'intval');
            if($id){
                M('short_video_badword')->where(['id'=>$id])->delete();
            }
        }while(false);
        $json['state'] = 200;

        $this->ajaxReturn($json);
    }

    /**
     * 脏词管理
     */
    public function tag(){
        $count = M('short_video_tag')->count();
        $Page = new Page($count, 100);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $list = M('short_video_tag')->where(['status'=>1])->order("id DESC")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);
        $this->assign('tag_list', $list);
        $this->display();
    }

    /**
     * 添加脏词
     */
    public function dotag(){
        $json = $this->ajax_json();
        do{
            $content = I('content','','trim');
            $export = explode("\n", $content);
            foreach($export as $word){
                $word = trim($word);
                if($word){
                    $has = M('short_video_tag')->where(["tag_name"=>$word])->find();
                    if(!$has){
                        M('short_video_tag')->add(["tag_name"=>$word]);
                    }elseif($has['status'] == '0'){
                        M('short_video_tag')->where(['id'=>$has['id']])->save(["status"=>1]);
                    }
                }
            }
        }while(false);
        $json['state'] = 200;

        $this->ajaxReturn($json);
    }

    /**
     * 添加脏词
     */
    public function deltag(){
        $json = $this->ajax_json();
        do{
            $id = I('id',0,'intval');
            if($id){
                M('short_video_tag')->where(['id'=>$id])->save(['status'=>0]);
            }
        }while(false);
        $json['state'] = 200;

        $this->ajaxReturn($json);
    }

    /**
     * 汇总统计
     */
    public function statistics(){

        // 用户总数， APP总用户, QQ登陆用户，手机登陆用户，微信登陆用户，公众号登陆用户， 已关注用用
        // 视频总量， 视频播放量， 视频点赞量，视频收藏量，评论总量
        // APP设备总数
        // android总量
        // IOS总量
        $this->display();
    }
}
