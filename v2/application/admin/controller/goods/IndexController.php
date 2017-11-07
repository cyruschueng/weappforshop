<?php

namespace app\admin\controller\goods;

use app\admin\controller\BaseController;
use app\common\model\ShopCate;
use app\common\model\ShopGoods;
use app\common\model\File;

class IndexController extends BaseController {

    //商品列表
    public function index() {
        $map = array();

        $map['is_del'] = ['in', '0, 2'];
        $map['uid'] = $this->uid;
        if (!empty(input('param.name'))) {
            $map['name'] = ['like', '%' . input('param.name') . '%'];
        }
        if (!empty(input('param.category_id'))) {
            $map['category_id'] = input('param.category_id');
        }

        $goodslist = ShopGoods::with('category')->where($map)->order('`order` desc')->paginate();

//        dump($goodslist);


//		$goodslist = ShopGoods::with('category')
//                         ->where('is_del', 0) 
//                        ->order('`order` desc')->paginate();
//                

        cookie("prevUrl", request()->url());

        $category = ShopCate::where(array('is_del'=>0,'uid'=>$this->uid))
                ->order('`order` desc')
                ->select()
                ->toArray();
        $this->assign("category", $category);
//        dump($goodslist);die();
        $this->assign('goodslist', $goodslist);
        return view();
    }

    //新增修改商品
    public function add() {
        if (request()->isPost()) {
            $data = input('post.');
            $file_ids = $data['file_id'];
            unset($data['file_id']);
            $pic = '';
            foreach ($file_ids as $value) {
                if ($value) {
                    if($pic!=''){
                        $pic.=',';
                    }
                    $pic.=$value;
                }
            }
            if (empty($pic)) {
                 $this->error('请上传图片', request()->url());
            }
                     $data['update_time'] = time();
            $data['pic'] = $pic;
            if (input('post.id')) {
                $result = ShopGoods::update($data);
            } else {
                  $data['create_time'] = time();
                $data['uid'] = $this->admin['uid'];
                $result = ShopGoods::create($data);
            }

            if ($result) {
                $this->success("保存成功");
            } else {
                $this->error('保存失败', cookie("prevUrl"));
            }
        } else {
            $id = input('param.id');
            if ($id) {
                $goods = ShopGoods::find($id);
                      $arrpic = explode(',', $goods['pic']);
                 
                if(isset( $arrpic[0]))    $goods['file_id'] = $arrpic[0];
                  if(isset( $arrpic[1]))   $goods['file_id1'] = $arrpic[1];
                 if(isset( $arrpic[2]))    $goods['file_id2'] = $arrpic[2];
                $this->assign('goods', $goods);
            }
            
            $category = ShopCate::where(array('is_del'=>0,'uid'=>$this->uid))
                    ->order('`order` desc')
                    ->select()
                    ->toArray();
            $this->assign("category", $category);
            return view();
        }
    }

    //改变商品状态
    public function update() {
        $data = input('param.');
        $result = ShopGoods::where('id', 'in', $data['id'])->update(['is_del' => $data['is_del']]);
        if ($result) {
            $this->success("修改成功", cookie("prevUrl"));
        } else {
            $this->error('修改失败', cookie("prevUrl"));
        }
    }

    //删除商品
    public function del() {
        $ids = input('param.id');

        $result = ShopGoods::destroy($ids);
        if ($result) {
            $this->success("删除成功", cookie("prevUrl"));
        } else {
            $this->error('删除失败', cookie("prevUrl"));
        }
    }

}
