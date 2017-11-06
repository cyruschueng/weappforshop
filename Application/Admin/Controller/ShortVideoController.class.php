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
class ShortVideoController extends BaseController
{
    public function index()
    {
        $show_botton = I('show',0,'intval');
        $class = I('class', 0, 'intval');
        $status = I('status', 0, 'intval');
        $platform = I('platform',0,'intval');
        $kw = I('kw', '', 'trim');

        $goods = D('ShortVideo'); // 实例化User对象
        $goods->where(['is_show'=>0]);
        if($platform){
            $goods->where(['platform'=>$platform]);
        }
        if ($class) {
            if($class == 2){
                $goods->where(array('is_hot' => 1));
            }else{
                $goods->where(array('type' => $class));
            }
        }

        if($status){
            if($status == 9){
                $goods->where(['status'=>0]);
            }else{
                $goods->where(['status'=>$status]);
            }
        }

        if($kw){
            $goods->where("title like '%s'", '%'.$kw.'%');
        }

        if($this->admin['role'] > 1){
            $goods->where(array('uid' => $this->admin['id']));
        }
        $count = $goods->where(array('is_del' => 0))->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $goods->where(['is_show'=>0]);

        if($platform){
            $goods->where(['platform'=>$platform]);
        }

        if ($class) {
            if($class == 2){
                $goods->where(array('is_hot' => 1));
            }else{
                $goods->where(array('type' => $class));
            }
        }

        if($status){
            if($status == 9){
                $goods->where(['status'=>0]);
            }else{
                $goods->where(['status'=>$status]);
            }
        }

        if($kw){
            $goods->where("title like '%s'", '%'.$kw.'%');
        }

        if($this->admin['role'] > 1){
            $goods->where(array('uid' => $this->admin['id']));
        }
        $this->assign('class', $class);
        $this->assign('kw', $kw);
        $this->assign('status', $status);
        $this->assign('platform', $platform);

        $this->assign('short_video_tag', get_video_type());
        
        $list = $goods->where(array('is_del' => 0))->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('show', $show_botton);
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

        $this->assign('user', $this->admin);
        if(strpos($_SERVER['HTTP_REFERER'], '/short_video/edit') === false){
            $_SESSION['jump_url'] = $_SERVER['HTTP_REFERER']."&show=1";
        }

        if (IS_POST) {
            $data = $_POST;
//            $data['type'] = join(',', (array)$data['tag_id']);
            $data['tag_list'] = join(',', (array)$data['tag_id']);
            unset($data['tag_id']);

            $data['title'] = badword($data['title']);
            $data['subtitle'] = badword($data['subtitle']);

            $data['original_title'] = $data['title'];
            $data['original_subtitle'] = $data['subtitle'];
            $data['is_hot'] = intval($data['is_hot']);

            if ($id) {
                $info = M('ShortVideo')->where(['id'=>$id])->find();
                if(empty($info['url'])){
                    if($data['url']){
                        $data['status'] = 0;
                    }
                }else{
                    unset($data['url']); //不让修改视频
                }
                 unset($data['uid']);// 不可修改上传平台
                 $data['update_time'] = time();
                 $res = D('ShortVideo')->where(array('id' => $id))->save($data);
        
            } else {
                $data['update_time'] = time();
                $data['create_time'] = time();
                // if($this->admin['role'] == 0)
                {
                    $data['uid'] = $this->admin['id'];
                }
                $res = D('ShortVideo')->add($data);
            }
            //
            // 删除缓存
            if ($res) {
                return $this->success('操作成功',$_SESSION['jump_url']?$_SESSION['jump_url']:U('/short_video/index',['show'=>1]));
            } else {
                return $this->error('操作失败', U('/short_video/edit', array('id' => $id)));
            }
        }

        $short_video = [];
        if ($id) {
            $short_video = M('ShortVideo')->find($id);
        }
        $city_list = D('City')->get_city_list();
        $this->assign('city_list', $city_list);
        $this->assign('goods_tags', M('short_video_tag')->where(['status'=>1])->select());
        $short_video_tag = array();
        // '1 banner  2热门推荐  3观点  4幽默   5生活',
        // $short_video_tag[] =array('id'=>2,'tag_name'=>'热门推荐');
        $short_video_tag = get_video_type();

        $use_agreement = M('short_video')->where(['from_platform'=>0, 'uid'=>$this->admin['id']])->find();
        if(!$use_agreement){
            $this->assign('use_agreement', 1);
        }else{
            $this->assign('use_agreement', 0);
        }

        $this->assign('short_video_tag', $short_video_tag);
        $this->assign('short_video', $short_video);
        $this->display();
    }

    /**
     * 我要上电视
     */
    public function tvlist(){
        $status = I('status', 0, 'intval');
        $tv_id = I('tv_id', 0, 'intval');
        $kw = I('kw', '', 'trim');

        $goods = D('ShortVideo'); // 实例化User对象

        if($tv_id){
            $goods->where(['tv_id'=>$tv_id]);
        }
        $goods->where(['is_show'=>1]);
        $goods->where(['active_id'=>['GT', 0]]);

        if($status){
            if($status == 9){
                $goods->where(['status' => 0]);
            }else{
                $goods->where(['status'=>$status]);
            }
        }

        // PGC
        if($this->admin['role'] == 2 || $this->admin['role'] == 3){

        // OGC
        }elseif($this->admin['role'] == 4 OR $this->admin['role'] == 5){
            $goods->where(['tv_id'=>$this->admin['tv_id']]);
        }

        if($kw){
            $goods->where("title like '%s'", '%'.$kw.'%');
        }

        $count = $goods->where(array('is_del' => 0))->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        if($tv_id){
            $goods->where(['tv_id'=>$tv_id]);
        }
        $goods->where(['is_show'=>1]);
        $goods->where([ 'active_id'=>['GT', 0]]);

        if($status){
            if($status == 9){
                $goods->where(['status'=>0]);
            }else{
                $goods->where(['status'=>$status]);
            }
        }

        if($kw){
            $goods->where("title like '%s'", '%'.$kw.'%');
        }

        $this->assign('kw', $kw);
        $this->assign('status', $status);

        $this->assign('short_video_tag', get_video_type());

        $list = $goods->where(array('is_del' => 0))->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $tv_list = D('ShortTv')->select();
        $ardb = M('user','t_',C('AR_HERE_DB'));
        // 获取视频用户
        foreach($list as $i=>$item){
            // 用户来源后台
            if($item['from_platform'] == 0){
                if($item['uid']){
                    $user = M('short_video_admin')->where(['id'=>$item['uid']])->find();
                    $list[$i]['username'] = $user['uname'];
                }

                // 用户来源H5
            }elseif($item['from_platform'] == 1){
//                $user = M('short_video_admin')->where(['uid'=>$item['uid']])->find();
                $list[$i]['username'] = "未知";//$user['uname'];
                // 用户来源APP
            }elseif($item['from_platform'] == 2){
                if($item['uid']){
                    $user = $ardb->where(['uid'=>$item['uid']])->find();
                    $list[$i]['username'] = $user['username'];
                }

            }
        }
        $this->assign('tv_list', $tv_list);

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 秀场
     */
    public function showvideo(){
        $status = I('status', 0, 'intval');
        $kw = I('kw', '', 'trim');

        $goods = D('ShortVideo'); // 实例化User对象

        $goods->where(['is_show'=>1]);
        //$goods->where([ 'active_id'=> 0]);

        if($status){
            if($status == 9){
                $goods->where(['status' => 0]);
            }elseif($status == 8){
                $goods->where(['is_hot'=>1]);
            }else{
                $goods->where(['status'=>$status]);
            }
        }

        if($kw){
            $goods->where("title like '%s'", '%'.$kw.'%');
        }

        $count = $goods->where(array('is_del' => 0))->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $goods->where(['is_show'=>1]);
        //$goods->where([ 'active_id'=> 0]);

        if($status){
            if($status == 9){
                $goods->where(['status'=>0]);
            }elseif($status == 8){
            $goods->where(['is_hot'=>1]);
            }else{
                $goods->where(['status'=>$status]);
            }
        }

        if($kw){
            $goods->where("title like '%s'", '%'.$kw.'%');
        }

        $this->assign('kw', $kw);
        $this->assign('status', $status);

        $this->assign('short_video_tag', get_video_type());

        $list = $goods->where(array('is_del' => 0))->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        if($_GET['test']){

            echo $goods->getLastSql();
        }
        $ardb = M('user','t_',C('AR_HERE_DB'));
        // 获取视频用户
        foreach($list as $i=>$item){
            // 用户来源后台
            if($item['from_platform'] == 0){
                if($item['uid']){
                    $user = M('short_video_admin')->where(['id'=>$item['uid']])->find();
                    $list[$i]['username'] = $user['uname'];
                }
                // 用户来源H5
            }elseif($item['from_platform'] == 1){
//                $user = M('short_video_admin')->where(['uid'=>$item['uid']])->find();
                $list[$i]['username'] = "未知";//$user['uname'];
                // 用户来源APP
            }elseif($item['from_platform'] == 2){
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

    /**
     * 下载视频
     */
    public function downvideo(){
        $id = I('request.id',0,'intval');
        if($id){
            $info = M("short_video")->where(['id'=>$id])->find();
            if($info['original_url']){
                M('short_video')->where(['id'=>$id])->save(['is_down'=>1]);
                return header("Location:".$info['original_url']);
                // return false;
            }
        }

        return $this->error("没找到下载信息~");
    }

    /**
     * 征用视频
     */
    public function usevideo(){
        $id = I('request.id',0,'intval');
        if($id){
            $info = M("short_video")->where(['id'=>$id])->find();
            if($info){
                M('short_video')->where(['id'=>$id])->save(['is_used'=>1]);
                // 发送系统消息
                // `msg_type`, `msg_content`, `platform`, `to_uid`, `to_openid`, `from_id`, `status`, `create_time`
                $active = M('active_dadi')->where(['id'=>$info['active_id']])->find();
                $data = [
                    'msg_type' => 1,
                    'msg_content' => "恭喜您，您参加的{$active['active_name']}素材被录用。",
                    'platform' => $info['from_platform'],
                    'to_uid' => $info['uid'],
                    'to_openid' => '',
                    'from_id' => $info['id'],
                    'create_time'=>time()
                ];
                M("short_message")->add($data);
                return $this->success("征用操作完成");
            }
        }
        return $this->error("操作失败~");
    }

    /**
     * 举报审核列表
     */
    public function report(){
        $count = M()->query("SELECT COUNT(*) as total FROM (SELECT count( * ) AS total_report, video_id FROM t_short_report WHERE STATUS =0 GROUP BY video_id ) t");
        $Page = new Page(intval($count[0]['total']), 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $list = M('short_video')->query("SELECT r . * , v . * FROM t_short_video v, (SELECT count( * ) AS total_report, video_id
            FROM t_short_report WHERE STATUS =0 GROUP BY video_id )r WHERE v.id = r.video_id ORDER BY r.total_report DESC LIMIT {$Page->firstRow},{$Page->listRows}");

        $ardb = M('user','t_',C('AR_HERE_DB'));
        // 获取视频用户
        foreach($list as $i=>$item){
            // 用户来源后台
            if($item['from_platform'] == 0){
                if($item['uid']){
                    $user = M('short_video_admin')->where(['id'=>$item['uid']])->find();
                    $list[$i]['username'] = $user['uname'];
                }

            // 用户来源H5
            }elseif($item['from_platform'] == 1){
//                $user = M('short_video_admin')->where(['uid'=>$item['uid']])->find();
                $list[$i]['username'] = "未知";//$user['uname'];
            // 用户来源APP
            }elseif($item['from_platform'] == 2){
                if($item['uid']){
                    $user = $ardb->where(['uid'=>$item['uid']])->find();
                    $list[$i]['username'] = $user['username'];
                }

            }
        }

        $show = $Page->show();// 分页显示输出
        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 视频标签
     */
    public function tag()
    {
        $goods = M('goods_tag'); // 实例化User对象
        $count = $goods->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $goods->order('id DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 标签编辑
     * //
     */
    public function tag_edit()
    {
        $id = I('request.id', 0, 'intval');

        if (IS_POST) {
            $data = $_POST;
            if ($id) {
                $res = M('goods_tag')->where(array('id' => $id))->save($data);

            } else {
                $res = M('goods_tag')->add($data);
            }

            if ($res) {
                return $this->success('操作成功', U('/short_video/tag'));
            } else {
                return $this->error('操作失败', U('/short_video/tag_edit', array('id' => $id)));
            }
        }

        $tag = [];
        if ($id) {
            $tag = M('goods_tag')->find($id);
        }

        $this->assign('tag', $tag);
        $this->display();
    }

    /**
     *
     */
    public function copy()
    {
        $idList = explode(',', $_GET['list']);

        if (empty($idList)) {
            return $this->error('提交的id为空', U('/short_video/index'));
        }

        $time = time();
        $successCount = 0;
        $failCount = 0;
        foreach ($idList as $id) {
            $good = D('goods')->where(['id' => $id])->find();
            if (!empty($good)) {
                unset($good['id']);
                if ($good['visible_platform'] == 1) {
                    $good['visible_platform'] = 2;
                } elseif ($good['visible_platform'] == 2) {
                    $good['visible_platform'] = 1;
                }
                $good['create_time'] = $time;
                $good['update_time'] = $time;
                if (D('goods')->add($good)) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }
        }
        return $this->success('删除成功' . $successCount . '个，失败' . $failCount . '个', U('/short_video/index'));
    }

    /**
     * 视频下架
     */
    public function drop(){
        $id = I('id',0,'intval');
        $status = I('status',1,'intval');
        D('ShortVideo')->where(array('id' => $id))->save(array('status'=>$status));
        if($status ==4){
            $this->del_video_comment_favorite($id);
        }
        return $this->success('操作成功');
    }

    /**
     * 视频举报审核
     */
    public function check_report(){
        $id = I('id',0,'intval');
        $status = I('status',1,'intval');
        // 对视频审核下架
        if($status == 7){
            D('ShortVideo')->where(array('id' => $id))->save(array('status'=>$status));
            // 举报消息
            // xxx视频被举报下架了

            // 发送系统消息
            // `msg_type`, `msg_content`, `platform`, `to_uid`, `to_openid`, `from_id`, `status`, `create_time`
            $info = D('ShortVideo')->where(['id'=>$id])->find();
            $data = [
                'msg_type' => 1,
                'msg_content' => "您上传的【{$info['title']}】视频被举报下架了。",
                'platform' => $info['from_platform'],
                'to_uid' => $info['uid'],
                'to_openid' => '',
                'from_id' => $info['id'],
                'create_time'=>time()
            ];
            M("short_message")->add($data);

            $this->del_video_comment_favorite($id);
        }

        // 视频下架 且发布者屏蔽
        if($status == 9){
            $video = D('ShortVideo')->where(array('id' => $id))->find();
            D('ShortVideo')->where(array('id' => $id))->save(array('status'=>7));

            // 对视频所有者屏蔽处理
            if($video['from_platform'] == '0'){
                // 屏蔽用户
                M('short_video_admin')->where(['id'=>$video['uid']])->save(['status'=>2]);
            // APP 端
            }elseif($video['from_platform'] == 2){

                $ardb = M('user','t_',C('AR_HERE_DB'));
                // 退出登陆及屏蔽用户使用APP
                $ardb->where(['uid'=>$video['uid']])->save(['status'=>2, 'token'=>'']);
            }
            // 发送系统消息
            // `msg_type`, `msg_content`, `platform`, `to_uid`, `to_openid`, `from_id`, `status`, `create_time`
            $info = D('ShortVideo')->where(['id'=>$id])->find();
            $data = [
                'msg_type' => 1,
                'msg_content' => "您上传的【{$info['title']}】视频被举报下架了。",
                'platform' => $info['from_platform'],
                'to_uid' => $info['uid'],
                'to_openid' => '',
                'from_id' => $info['id'],
                'create_time'=>time()
            ];
            M("short_message")->add($data);

            $this->del_video_comment_favorite($id);
        }

        // 对以往举报处理
        D('ShortReport')->where(['video_id'=>$id,'status'=>0])->save(['status'=>1]);
        return $this->success('操作成功');
    }

    /**
     * 视频删除
     */
    public function del()
    {
        $id = I('id',0,'intval');

        if (empty($id)) {
            return $this->error('提交的id为空', U('/short_video/index'));
        }

        $time = time();

        D('ShortVideo')->where(array('id' => $id))->save(array('is_del'=>1,'update_time'=>$time));
        $this->del_video_comment_favorite($id);

        return $this->success('删除成功');
    }

    /**
     * 视频删除
     */
    public function delete()
    {
        $idList = explode(',', $_GET['list']);

        if (empty($idList)) {
            return $this->error('提交的id为空', U('/short_video/index'));
        }

        $time = time();
        foreach ($idList as $id) {
            D('ShortVideo')->where(array('id' => $id))->save(array('is_del'=>1,'update_time'=>$time));
            $this->del_video_comment_favorite($id);
        }
        return $this->success('删除成功', U('/short_video/index'));
    }

    /**
     * 更新下架、删除视频的评论和点赞记录
     */
    protected function del_video_comment_favorite($id){
        if(empty($id)){
            return;
        }
        //点赞操作
        $res1 = M('short_video_favorite')->where("video_id='{$id}'")->save(array("status"=>9));
        //视频评论
        $res2=M('short_video_comment')->where(['video_id' => $id])->save(array("status"=>99));
        $commlist=M('short_video_comment')->where(['video_id' => $id])->select();
        foreach ($commlist as $k=>$val){
            M('short_video_favorite')->where("comment_id='{$val['id']}'")->save(array("status"=>9));
        }

        //历史记录
        M('short_video_history')->where("video_id='{$id}'")->save(array("status"=>9));
        M('short_video_history_nologin')->where("video_id='{$id}'")->save(array("status"=>9));
        M('short_video_collection')->where("video_id='{$id}'")->save(array("status"=>9));
    }
}
