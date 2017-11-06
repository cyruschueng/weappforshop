<?php

namespace Admin\Controller;

use Helpers\Helper;
use Helpers\HtmlHelper;
use Helpers\Presenter;
use Redis\MyRedis;
use Think\Controller;
use Think\Page;
use Think\Upload;

class ShopGoodsController extends BaseController {

    public function index() {
        // $class = I('class', 0, 'intval');
        $ShopGoods = D('ShopGoods');

        $ShopGoods->where(array('is_del' => 0));
        $count = $ShopGoods->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show(); // 分页显示输出

        $list = $ShopGoods->where(array('is_del' => 0))->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as &$value) {
            $value['pic'] = explode(',', $value['pic'])[0];
        }

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }

    /**
     * 编辑信息
     */
    public function edit() {
        $id = I('request.id', 0, 'intval');

        if (IS_POST) {
            $data = $_POST;
            $data['type'] = join(',', (array) $data['tag_id']);
            if (!empty($data['pic'])) {

                if (!empty($data['pic1'])) {
                    $data['pic'] .= "," . $data['pic1'];
                }
                if (!empty($_POST['pic2'])) {
                    $data['pic'] .= "," . $data['pic2'];
                }
            } else if (!empty($data['pic1'])) {
                $data['pic'] .= "," . $data['pic1'];
                if (!empty($_POST['pic2'])) {
                    $data['pic'] .= "," . $data['pic2'];
                }
            } else if (!empty($_POST['pic2'])) {
                $data['pic'] .= "," . $data['pic2'];
            }

            if ($id) {
                $data['update_time'] = time();
                $res = D('ShopGoods')->where(array('id' => $id))->save($data);
            } else {

                $data['update_time'] = time();
                $data['create_time'] = time();
                $res = D('ShopGoods')->add($data);
            }

            // 删除缓存
            if ($res) {
                return $this->success('操作成功', U('/shop_goods/index'));
            } else {
                return $this->error('操作失败', U('/shop_goods/edit', array('id' => $id)));
            }
        }

        $shop_goods = [];
        if ($id) {
            $shop_goods = M('ShopGoods')->find($id);

            $arrpic = explode(',', $shop_goods['pic']);
            $shop_goods['pic'] = $arrpic[0];
            $shop_goods['pic1'] = $arrpic[1];
            $shop_goods['pic2'] = $arrpic[2];
        }

        $shop_goods_tag = array();
        // '1 banner  2热门推荐  3观点  4幽默   5生活',
        $shop_goods_tag = $this->get_goods_type($this->admin['id']);

        $this->assign('shop_goods_tag', $shop_goods_tag);
        $this->assign('shop_goods', $shop_goods);
        $this->display();
    }

    public function get_goods_type($uid = 0) {
        return M('ShopCate')->where(array('uid' => $uid , 'is_del' => 0))->order('`order` DESC')->select();
    }

    public function delete() {
        $idList = explode(',', $_GET['list']);

        if (empty($idList)) {
            return $this->error('提交的id为空', U('/shop_goods/index'));
        }

        $time = time();
        foreach ($idList as $id) {
            D('ShopGoods')->where(array('id' => $id))->save(array('is_del' => 1, 'update_time' => $time));
        }
        return $this->success('删除成功', U('/shop_goods/index'));
    }

}
