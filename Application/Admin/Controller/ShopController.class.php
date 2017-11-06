<?php

namespace Admin\Controller;

use Think\Controller;
use Think\Page;
use Redis\MyRedis;

class ShopController extends BasexController {

    public function goods_categoryall() {
        
        $ShopVbanner = D('ShopCate');
        $list = $ShopVbanner->where(array('is_del' => 0,'uid'=>$this->uid))->order('`order` DESC')->select();
        
        if(empty($list)) {$this->echomsgerr(" {$this->mallname}| {$this->shopname} 店铺没有设置类别");}
        $this->echomsg($list);
    }

    public function goods_price() {
        
    }
    public function goods_detail() {
        $id = I('id');
        $shop_goods = M('ShopGoods')->where(array('is_del' => 0,'uid'=>$this->uid, 'id'=> $id))->find();
      if(empty($shop_goods)) { $this->echomsgerr("{$id} 号商品找不到了");}
        $arrpic = explode(',', $shop_goods['pic']);

        $pics = array();

        $shop_goods['pic'] = 'https://small.kuaiduodian.com/' . $arrpic[0];
        $shop_goods['pic1'] = 'https://small.kuaiduodian.com/' . $arrpic[1];
        $shop_goods['pic2'] = 'https://small.kuaiduodian.com/' . $arrpic[2];
        if (!empty($arrpic[0])) {
            $pics[] = array('pic'=>'https://small.kuaiduodian.com/' . $arrpic[0]);
        }
        if (!empty($arrpic[1])) {
            $pics[] =  array('pic'=>'https://small.kuaiduodian.com/' . $arrpic[1]);
        }
        if (!empty($arrpic[2])) {
            $pics[] =  array('pic'=>'https://small.kuaiduodian.com/' . $arrpic[2]);
        }
        $arrbasicInfo = array();
        $arrbasicInfo['minprice'] = $shop_goods['minprice'];
        $arrbasicInfo['stores'] = $shop_goods['stores'];
        $arrbasicInfo['name'] = $shop_goods['name'];
        $arrbasicInfo['pic'] = $shop_goods['pic'];
        //是否需要物流
        $arrbasicInfo['logisticsId'] =1; 
        $arrbasicInfo['id'] = $shop_goods['id'];
        $shop_goods['pics'] = $pics;
        $shop_goods['basicInfo'] = $arrbasicInfo;
        $arrproperties =  array();
        $arrproperties['id'] ='1';
        $arrproperties['name'] ='无规格';
         
         $childsCurGoods =  array();
                 $childsCurGoods['id'] ='2';
         $childsCurGoods['name'] ='默认';
         $arrproperties['childsCurGoods']=[$childsCurGoods];
        $shop_goods['properties'] = [$arrproperties];
        $this->echomsg($shop_goods);
    }

    public function goods_list() {

        $categoryid = I('categoryid');

        $ShopGoods = D('ShopGoods');
        if (empty($categoryid)) {
            $list = $ShopGoods->where(array('is_del' => 0,'uid'=>$this->uid))->select();
        } else {
            $list = $ShopGoods->where(array('is_del' => 0, 'category_id' => $categoryid,'uid'=>$this->uid))->select();
        }
        foreach ($list as &$value) {
            $value['pic'] = 'https://small.kuaiduodian.com/' . explode(',', $value['pic'])[0];
        }
        $this->echomsg($list);
    }

    public function edit() {
        $id = I('id', 0, 'intval');
        if (IS_POST) {
            $data = $_POST;
            if ($id) {
                $data['update_time'] = time();
                $res = M('home_config')->where(array('id' => $id))->save($data);
            } else {
                $data['create_time'] = time();
                $data['update_time'] = time();
                $res = M('home_config')->add($data);
            }
            MyRedis::getProInstance()->delete('t_home_config_' . $data['category']);
            if ($res) {

                return $this->success('编辑成功', U('home/index'));
            } else {
                return $this->success('编辑失败', U('home/edit', array('id' => $id)));
            }
        }
        $config = array();
        if ($id) {
            $config = M('home_config')->where(array('id' => $id))->find();
        }
        $this->assign('config', $config);

        global $home_category_config;
        $this->assign('home_category', $home_category_config);
        $this->display();
    }


  

}
