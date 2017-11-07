<?php

namespace app\admin\controller\order;

use app\admin\controller\BaseController;
use app\common\model\ShopCate;
use app\common\model\ShopGoods;

class CategoryController extends BaseController {

    //商品分类
    public function index() {
        $categorylist = ShopCate::where('is_del', 0) 
                ->order('`order` desc')
                 ->select()
                    ->toArray();
        cookie("prevUrl", request()->url());
        $this->assign('categorylist', $categorylist);
        return view();
    }

    //新增修改商品分类
    public function add() {
        if (request()->isPost()) {
            $data = input('post.');
            $data['update_time'] = time();
            if (input('post.id')) {
                $result = ShopCate::update($data);
            } else {
                $data['uid'] = $this->admin['uid'];
                $data['create_time'] = time();
                $result = ShopCate::create($data);
            }

            if ($result) {
                $this->success("保存成功", cookie("prevUrl"));
            } else {
                $this->error('保存失败', cookie("prevUrl"));
            }
        } else {


            $id = input('param.id');
            if ($id) {
                $category = ShopCate::find($id);
                $this->assign('category', $category);
            }
//
            return view();
        }
    }

    //改变商品类型状态
    public function update() {
        $data = input('param.');
        $result = ShopCate::where('id', 'in', $data['id'])->update(['is_del' => 1]);
//        Article::where('category_id', $data['id'])->update(['status' => $data['status']]);
        if ($result) {
            $this->success("修改成功", cookie("prevUrl"));
        } else {
            $this->error('修改失败', cookie("prevUrl"));
        }
    }

    //删除商品分类
    public function del() {
        $ids = input('param.id');

        $result = ShopCate::destroy($ids);
        if ($result) {
            $this->success("删除成功", cookie("prevUrl"));
        } else {
            $this->error('删除失败', cookie("prevUrl"));
        }
    }

}
