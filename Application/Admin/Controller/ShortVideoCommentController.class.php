<?php
namespace Admin\Controller;

use Think\Controller;
use Think\Page;

/**
 * @shengyue 2016-07-04
 * 视频管理
 * @package Admin\Controller
 */
class ShortVideoCommentController extends BaseController
{
    public function index()
    {
        $id = I('id', 0, 'intval');
        $kw = I('kw','','trim');
        $class = I('class','', 'trim');
        $platform = I('platform',0,'intval');
        $from_platform = I('from_platform',0,'intval');

        $ShortVideoComment = D('ShortVideoComment');

        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //$this->assign('class', $class);
        if ($id > 0) {
            $ShortVideoComment->where(array('c.video_id' => $id));
        }

        $uidvideo = '';
        if ( $this->admin['role'] > 1){
            $uidvideo = " AND v.uid = {$this->admin['id']}";
        }

        if($from_platform){
            if($from_platform == 3){
                $ShortVideoComment->where(['v.from_platform'=>0]);
            }else{
                $ShortVideoComment->where(['v.from_platform'=>$from_platform]);
            }
        }

        if($platform){
            $ShortVideoComment->where(['c.platform'=>$platform]);
        }

        $ShortVideoComment->table('t_short_video_comment c')->join('t_short_video v on c.video_id=v.id')->field('c.id as id,
        c.content as content,c.video_id as video_id,v.title as title,v.type as type,c.create_time as create_time,c.is_top as is_top');

        if($kw){
            $ShortVideoComment->where("c.status = 1 and v.is_del = 0 $uidvideo AND v.title like '%s'","%{$kw}%");
        }else{
            $ShortVideoComment->where('c.status = 1 and v.is_del = 0 '.$uidvideo);
        }

        if($class){
            if($class == 2){
                $ShortVideoComment->where(array('v.is_hot' => 1));
            }else{
                $ShortVideoComment->where(array('v.type' => $class));
            }
        }

        $count = $ShortVideoComment->order('c.create_time DESC')->count();

        if ($id > 0) {
            $ShortVideoComment->where(array('c.video_id' => $id));
        }

        if($from_platform){
            if($from_platform == 3){
                $ShortVideoComment->where(['v.from_platform'=>0]);
            }else{
                $ShortVideoComment->where(['v.from_platform'=>$from_platform]);
            }
        }

        if($platform){
            $ShortVideoComment->where(['c.platform'=>$platform]);
        }

        $uidvideo = '';
        if ( $this->admin['role'] > 1  ){
            $uidvideo = "and v.uid = {$this->admin['id']}";
        }

        $Page = new Page($count, 20);

        $ShortVideoComment->table('t_short_video_comment c')->join('t_short_video v on c.video_id=v.id')->field('c.id, c.reply_id,
        c.content,c.video_id,v.title,v.type,c.create_time,c.is_top, v.is_show, v.from_platform,c.platform');

        if($kw){
            $ShortVideoComment->where("c.status = 1 AND v.is_del = 0 $uidvideo AND v.title like '%s'","%{$kw}%");
        }else{
            $ShortVideoComment->where('c.status = 1 and v.is_del = 0 '.$uidvideo);
        }

        if($class){
            if($class == 2){
                $ShortVideoComment->where(array('is_hot' => 1));
            }else{
                $ShortVideoComment->where(array('type' => $class));
            }
        }

        $list = $ShortVideoComment->order('c.create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach($list as $i=>$row){
            $list[$i]['reply'] = '';
            if($row['reply_id']){
                $list[$i]['reply'] = M('ShortVideoComment')->find($row['reply_id']);
            }
        }

        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show(); // 分页显示输出
        $this->assign('short_video_tag', get_video_type());

        $this->assign('class', $class);
        $this->assign('kw', $kw);
        $this->assign('platform', $platform);
        $this->assign('from_platform', $from_platform);

        $this->assign('list', $list); // 赋值数据集
        $this->assign('id', $id); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
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
            $data['type'] = join(',', (array) $data['tag_id']);

            if ($id) {
                $data['update_time'] = time();
                $data['begin_time'] = strtotime($data['begin_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $res = D('ShortVideoComment')->where(array('id' => $id))->save($data);

            } else {
                $data['begin_time'] = strtotime($data['begin_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $data['update_time'] = time();
                $data['create_time'] = time();
                $res = D('ShortVideoComment')->add($data);
            }

            // 删除缓存
            if ($res) {
                return $this->success('操作成功', U('/short_video_comment/index'));
            } else {
                return $this->error('操作失败', U('/short_video_comment/edit', array('id' => $id)));
            }
        }

        $short_video_comment = [];
        if ($id) {
            $short_video_comment = M('ShortVideoComment')->find($id);
            $short_video_comment['begin_time'] = date('Y-m-d H:i:s', $short_video_comment['begin_time']);
            $short_video_comment['end_time'] = date('Y-m-d H:i:s', $short_video_comment['end_time']);
        }
        $short_video_comment_tag = array();
        // '1 banner  2热门推荐  3观点  4幽默   5生活',
        $short_video_comment_tag = get_video_type();

        $this->assign('short_video_comment_tag', $short_video_comment_tag);
        $this->assign('short_video_comment', $short_video_comment);
        $this->display();
    }

    public function delete()
    {
        $idList = explode(',', $_GET['list']);
        $video_id = I('request.id', 0, 'intval');
//  die($id);
        if (empty($idList)) {
            return $this->error('提交的id为空', U('/short_video_comment/index'));
        }

        $time = time();
        foreach ($idList as $id) {
            D('ShortVideoComment')->where(array('id' => $id))->save(array('status' => 99, 'update_time' => $time));
        }
        return $this->success('删除成功', U('/short_video_comment/index', array('id' => $video_id)));
    }

    /**
     * 单条删除
     */
    public function del(){
        $video_id = I('request.id', 0, 'intval');

        $time = time();
        D('ShortVideoComment')->where(array('id' => $video_id))->save(array('status' => 99, 'update_time' => $time));

        return $this->success('删除成功');
    }

    /**
     * 回复
     */
    public function reply(){
        $json = $this->ajax_json();
        do{
            $id = I('id',0,'intval');
            $text = I('text','','trim,htmlspecialchars');
            if(!$text){
                $json['msg'] = "请输入回复信息~";
                break;
            }
            $comment = D('ShortVideoComment')->where(['id'=>$id])->find();
            if(!$comment){
                $json['msg'] = "没找到评论信息~";
                break;
            }
            $data = [
                'video_id' => $comment['video_id'],
                'reply_id' => $id,
                'nickname' => $this->admin['uname'],
                'pic'=>$this->admin['pic'],
                'content' => $text,
                'openid' => '',
                'status' => 1,
                'union_id' => $this->admin['id'],
                'platform' => 3,
                'create_time' => time(),
                'update_time' => time()
            ];
            $res = D('ShortVideoComment')->add($data);
            if($res){
                $json['state'] = 200;
                $json['msg'] = "回复成功~";
                break;
            }else{
                $json['msg'] = "回复失败~";
                break;
            }

        }while(false);
        $this->ajaxReturn($json);
    }

    /**
     * 测试上传2
     */
    function edit2(){
        $this->display();
    }

    /**
     * 置顶
     */
    public function top()
    {
        $id = I('request.id', 0, 'intval');
        $checked = I('request.checked', 0, 'intval');
        D('ShortVideoComment')->where(array('id' => $id))->save(array('is_top' => $checked, 'update_time' => time()));
        return $this->success('操作成功~');
    }

}
