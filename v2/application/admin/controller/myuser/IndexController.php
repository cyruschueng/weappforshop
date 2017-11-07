<?php

namespace app\admin\controller\myuser;

use app\admin\controller\BaseController;
use app\common\model\ShopWxappuser;

class IndexController extends BaseController {

    //用户列表
    public function index() {
        $map = array();
        if (input('param.id') != '') {
            $map['id'] = input('param.id');
        }
        if (input('param.name') != '') {
            $map['nickName'] = ['like', '%' . input('param.name') . '%'];
        }

 
        //FIXED
        $map['shopid'] = $this->uid;
        if (input('param.day') != '') {
            $userlist = ShopWxappuser::where($map)->whereTime('create_time', input('param.day'))->order('token_time desc')->paginate();
        } else {
            $userlist = ShopWxappuser::where($map)->order('token_time desc')->paginate();
        }

        cookie("prevUrl", $this->request->url());
// var_dump($userlist);exit;
        $this->assign('userlist', $userlist);
        return view();
    }

    //新增修改用户
    public function add() {
        if (request()->isPost()) {
            $data = input('post.');
            if ($data['password']) {
                $data['password'] = md5($data['password']);
            } else {
                unset($data['password']);
            }
            if ($data['id']) {
                $result = ShopWxappuser::update($data);
            } else {
                $result = ShopWxappuser::create($data);
            }

            if ($result) {
                $this->success("保存成功", cookie("prevUrl"));
            } else {
                $this->error('保存失败', cookie("prevUrl"));
            }
        } else {
            $id = input('param.id');
            if ($id) {
                $user = ShopWxappuser::find($id);
                $this->assign('user', $user);
            }
            return view();
        }
    }

    //删除用户
    public function del() {
        $ids = input('param.id');
        if ($ids == 1) {
            $this->error('默认用户组允许删除', cookie("prevUrl"));
        }
        $result = ShopWxappuser::destroy($ids);
        if ($result) {
            $this->success("删除成功", cookie("prevUrl"));
        } else {
            $this->error('删除失败', cookie("prevUrl"));
        }
    }

    //改变用户状态
    public function update() {
        $data = input('param.');
        if ($data['id'] == 1) {
            $this->error('测试用户不允许操作', cookie("prevUrl"));
        }
        $result = ShopWxappuser::where('id', $data['id'])->update(['status' => $data['status']]);



        if ($result) {
            $this->success("修改成功", cookie("prevUrl"));
        } else {
            $this->error('修改失败', cookie("prevUrl"));
        }
    }

    //导出用户
    public function export() {
        
               $map['shopid'] = $this->uid;
        $userlist = //ShopWxappuser::all()->toArray();
 ShopWxappuser::where(array( 'shopid' => $this->uid))
                ->select()
                ->toArray();
        $data = array(
            '0' => array(
                '1' => '编号',
                '2' => '微信昵称',
                '3' => 'openid',
          
                '11' => '状态',
                '12' => '购买量',
  
                '14' => '注册时间',
            ),
        );
        foreach ($userlist as &$v) {
            switch ($v['status']) {
                case '0':
                    $v['status'] = '禁用';
                    break;
                case '1':
                    $v['status'] = '启用';
                    break;
                default:
                    $v['status'] = '未知状态';
                    break;
            }
            array_push($data, array(
                '1' => $v['id'],
                '2' => $v['nickName'],
                '3' => $v['openid'],
            

                '11' => $v['status'],
                '12' => 0,
     
                '14' => $v['create_time'],
            ));
        }
        export_to($data, '用户列表'); //导出excle
    }

}
