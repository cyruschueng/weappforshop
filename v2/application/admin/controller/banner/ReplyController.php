<?php

namespace app\admin\controller\banner;

use app\admin\controller\BaseController;
use app\common\model\ShopGoods;

class ReplyController extends BaseController {

    //微信自定义回复
    public function index() {
        $replylist = model('ShopVbanner')->with('file')->with('business')->where('is_del', 0)->where('uid', $this->uid)
                    ->order('`order` desc')->select()->toArray();
        cookie("prevUrl", request()->url());
        $this->assign('replylist', $replylist);
        return view();
    }

    //新增修改自定义回复
    public function add() {
        if (request()->isPost()) {
            $data = input('post.');
            if (empty(input('param.picurl'))) {
                $this->error('请上传图片', request()->url());
            }
            if (empty(input('param.businessid'))) {
                $this->error('请指定一个跳转商品', request()->url());
            }
            $data['update_time'] = time();
            if (input('post.id')) {
                $result = model('ShopVbanner')->update($data);
            } else {
                  $data['uid'] = $this->admin['uid'];
                $data['create_time'] = time();
                $result = model('ShopVbanner')->create($data);
            }

            if ($result) {
                $this->success("保存成功", cookie("prevUrl"));
            } else {
                $this->error('保存失败', cookie("prevUrl"));
            }
        } else {
            $id = input('param.id');
            if ($id) {
                $reply = model('ShopVbanner')->with('file')->find($id);
                $this->assign('reply', $reply);
            }
            $allshopgoods = ShopGoods::where(array('is_del'=>0,'uid'=>$this->uid))
                    ->order('id desc')
                    ->select()
                    ->toArray();
            $this->assign("allshopgoods", $allshopgoods);
            return view();
        }
    }

    //删除自定义回复
    public function del() {
        $ids = input('param.id');

        $result = model('ShopVbanner')->destroy($ids);
        if ($result) {
            $this->success("删除成功", cookie("prevUrl"));
        } else {
            $this->error('删除失败', cookie("prevUrl"));
        }
    }

}
