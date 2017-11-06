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
 * ShopVbanner 
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(100) NOT NULL COMMENT '图片',
`picurl` varchar(100) NOT NULL COMMENT '图片',
`order` tinyint(3) DEFAULT NULL COMMENT '排序',
`url` varchar(256) NOT NULL COMMENT 'url',
`is_del` tinyint(2) DEFAULT '1' COMMENT '状态：0禁用，1启用',
`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(11) DEFAULT '0',
`begin_time` int(11) DEFAULT '0',
`end_time` int(11) DEFAULT '0',
 */
class ShopVbannerController extends BaseController
{
    
    public function index()
    {
        // $class = I('class', 0, 'intval');
        $ShopVbanner = D('ShopVbanner'); 
      
        $ShopVbanner->where(array('is_del' => 0));
        $count = $ShopVbanner->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //$this->assign('class', $class);
        $list = $ShopVbanner->where(array('is_del' => 0))->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
                $data['begin_time'] = strtotime($data['begin_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $res = D('ShopVbanner')->where(array('id' => $id))->save($data);
        
            } else {
                $data['begin_time'] = strtotime($data['begin_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $data['update_time'] = time();
                $data['create_time'] = time();
                $res = D('ShopVbanner')->add($data);
            }

            // 删除缓存
            if ($res) {
                return $this->success('操作成功', U('/shop_vbanner/index'));
            } else {
                return $this->error('操作失败', U('/shop_vbanner/edit', array('id' => $id)));
            }
        }

        $shop_vbanner = [];
        if ($id) {
            $shop_vbanner = M('ShopVbanner')->find($id);
            $shop_vbanner['begin_time'] = date('Y-m-d H:i:s', $shop_vbanner['begin_time']);
            $shop_vbanner['end_time'] = date('Y-m-d H:i:s', $shop_vbanner['end_time']);
        }
        $shop_vbanner_tag = array();
        // '1 banner  2热门推荐  3观点  4幽默   5生活',
        $shop_vbanner_tag = get_video_type();
        
        $this->assign('shop_vbanner_tag', $shop_vbanner_tag);
        $this->assign('shop_vbanner', $shop_vbanner);
        $this->display();
    }

    public function delete()
    {
        $idList = explode(',', $_GET['list']);

        if (empty($idList)) {
            return $this->error('提交的id为空', U('/shop_vbanner/index'));
        }

        $time = time();
        foreach ($idList as $id) {
            D('ShopVbanner')->where(array('id' => $id))->save(array('is_del'=>1,'update_time'=>$time));
        }
        return $this->success('删除成功', U('/shop_vbanner/index'));
    }
}
