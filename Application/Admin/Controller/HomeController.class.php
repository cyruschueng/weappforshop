<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Redis\MyRedis;
class HomeController extends BaseController {
    public function index(){
        $category = I('class','','strval');
        $goods = M('home_config'); // 实例化User对象
        if($category){
            $goods->where(array('category'=>$category));
        }
        global $home_category_config;
        $this->assign('category_list', $home_category_config);
        $this->assign('class', $category);
        $count = $goods->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        if($category){
            $goods->where(array('category'=>$category));
        }
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $goods->order('status DESC, weight DESC, create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    public function edit(){
        $id = I('id', 0 ,'intval');
        if(IS_POST){
            $data = $_POST;
            if($id){
                $data['update_time'] = time();
                $res = M('home_config')->where(array('id'=>$id))->save($data);
            }else{
                $data['create_time'] = time();
                $data['update_time'] = time();
                $res = M('home_config')->add($data);
            }
            MyRedis::getProInstance()->delete('t_home_config_'.$data['category']);
            if($res){

                return $this->success('编辑成功',U('home/index'));
            }else{
                return $this->success('编辑失败',U('home/edit', array('id'=>$id)));
            }
        }
        $config = array();
        if($id){
            $config = M('home_config')->where(array('id'=>$id))->find();

        }
        $this->assign('config', $config);

        global $home_category_config;
        $this->assign('home_category', $home_category_config);
        $this->display();
    }

    /**
     * 文案配置
     */
    public function set(){
        $goods = M('config'); // 实例化User对象
        $count = $goods->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $goods->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 文案编辑
     * @return mixed
     */
    public function set_edit(){
        $id = I('id', 0 ,'intval');
        if(IS_POST){
            $data = $_POST;
            $cate = explode('|', $data['category_name']);
            $data['category'] = $cate[0];
            $data['config_name'] = $cate[1];
            if($id){
                $updateData = [
                    'config_data' => $data['config_data'],
                    'update_time' => time(),
                ];
                $res = M('config')->where(array('id'=>$id))->save($updateData);
            }else{
                $data['create_time'] = time();
                $data['update_time'] = time();
                $res = M('config')->add($data);
            }
            MyRedis::getProInstance()->delete('t_config_'.$data['config_name']);
            if($res){
                return $this->success('编辑成功',U('home/set'));
            }else{
                return $this->success('编辑失败',U('home/set_edit', array('id'=>$id)));
            }
        }
        $config = array();
        if($id){
            $config = M('config')->where(array('id'=>$id))->find();

        }
        $this->assign('config', $config);

        global $msg_category_config;
        $this->assign('msg_config', $msg_category_config);
        $this->display();
    }
    
    
		
	/**
     * 红包文案logo列表
     * add by Qinmj 2016-11-22
     */
    function redset(){
    	$goods = M('red_config'); // 实例化User对象
    	$count = $goods->count();// 查询满足要求的总记录数
    	$Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	
    	$list = $goods->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    	$this->assign('list', $list);// 赋值数据集
    	$this->assign('page', $show);// 赋值分页输出
    	
    	$city = D('City'); // 实例化User对象
    	$citylist = $city->publicUserList();
    	$this->assign('citylist', $citylist);// 公众号
    	
    	$this->display();
    }
    
    
    /**
     * 红包文案logo编辑
     * add by Qinmj 2016-11-22
     */
	function redset_edit(){
	
		$id = I('id', 0 ,'intval');
        if(IS_POST){
            $data = $_POST;
            
            $utype = M('red_config')->where(array('utype'=>$data['utype']))->find();
            if($utype && $utype['id'] != $id){
            	return $this->error('该公众号已经做过配置，请直接编辑！', U('home/redset'));
            }
            
            if($id){
                $data['update_time'] = time();
				$res = M('red_config')->where(array('id'=>$id))->save($data);
            }else{
                $data['create_time'] = time();
                $data['update_time'] = time();
                $res = M('red_config')->add($data);
            }
            MyRedis::getProInstance()->delete('t_red_config_'.$data['utype']);
            if($res){
                return $this->success('编辑成功',U('home/redset'));
            }else{
                return $this->success('编辑失败',U('home/redset_edit', array('id'=>$id)));
            }
        }
	
        //用户公众号 下拉框
        $city = D('City'); // 实例化User对象
    	$public_city = $city->publicUserList();
		$this->assign('public_city', $public_city);
		
		$config = array();
		if($id){
			$config = M('red_config')->where(array('id'=>$id))->find();
		
		}
		$this->assign('config', $config);
		
		$this->display();
    }
    
}