<?php

namespace Admin\Controller;

use Think\Controller;
use Think\Page;
use Redis\MyRedis;

class UserController extends BasexController {

    //	后台获得token 服务器自己保存的

    public function check_token() {
        $shop_wxappuser = M('shop_wxappuser')->where(array('token' => $_GET['token']))->find();
        if ($shop_wxappuser['token_time'] > time()) {
            $this->echomsg($shop_wxappuser, 0);
        }
        $this->echomsg(null, 1);
        die();
    }

    public function shipping_address_detail() {
        $id = I('id');
        $res = D('shop_shipaddress')->find($id);
        $this->echomsg($res, 0);
    }

    public function shipping_address_delete() {
        $id = I('id');
        $time = time();
        $res = D('shop_shipaddress')->where(array('id' => $id, 'wxuid' => $this->wxuid))->save(array('is_del' => 1, 'update_time' => $time));
        $this->echomsg($res, 0);
    }

    public function shipping_address_update() {
        $id = I('id');
        unset($_GET['id']);
        $data = $_GET;
        $data['update_time'] = time();
        $res = D('shop_shipaddress')->where(array('id' => $id, 'wxuid' => $this->wxuid))->save($data);
        $this->echomsg($res, 0);
    }

//mallName=kuaiduodian&token=8fd292a12fa1096fca97aef3be896956&id=0&provinceId=110000&
//cityId=110101&districtId=110101&linkMan=111&address=1&mobile=11&code=1&isDefault=true
    public function shipping_address_add() {
        $data = $_GET;
        $data['wxuid'] = $this->wxuid;

$data['update_time'] = time();
$data['create_time'] = time();
        $res = D('shop_shipaddress')->add($data);

        $this->echomsg($res, 0);
        die();
    }

    public function shipping_address_list() {
        if (empty($this->wxuid)) {
            $this->echomsg('', 1);
        }
        $list = D('shop_shipaddress')->where(array('is_del' => 0, 'wxuid' => $this->wxuid))->order('`update_time` DESC')->select();

        $this->echomsg($list, 0);
        die();
    }

    public function shipping_address_default() {
        if (empty($this->wxuid)) {
            $this->echomsg('', 1);
        }
        $list = D('shop_shipaddress')->where(array('is_del' => 0, 'wxuid' => $this->wxuid))->order('`update_time` DESC')->find();
        $this->echomsg($list, 0);
        die();
    }

    /**
     * 取得用户的openid和session_key值
     * //正常返回的JSON数据包
      {
      "openid": "OPENID",
      "session_key": "SESSIONKEY"
      }
      //错误时返回JSON数据包(示例为Code无效)
      {
      "errcode": 40029,
      "errmsg": "invalid code"
      }
     */
    public function wxapp_login() {

        $code = $_GET["code"];
        $str = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->config['appid']}&secret={$this->config['secret']}&js_code=" . $code . "&grant_type=authorization_code";
        //$str = "https://api.weixin.qq.com/sns/jscode2session?appid=wx053a2454754f77ee&secret=709faa7ccb71fcfd023101f98b637efc&js_code=021OK6Kg1eq4Xx0TmpJg1vv2Kg1OK6Kv&grant_type=authorization_code";
        $info = file_get_contents($str);
        $this->logdebug('jscode2session  debug  oo ==' . $info);
        $info = json_decode($info, true);
        $this->logdebug('jscode2session  debug  ' . json_encode($info));

        if ($info['openid']) {
            $data['session_key'] = $info['session_key'];
            $data['openid'] = $info['openid'];
            $data['create_time'] = time()+864000;
            $data['code'] = $code;

            $res = D('WeappSession')->add($data);
            $shop_wxappuser = M('shop_wxappuser')->where(array('openid' => $data['openid']))->find();

            
            if ($shop_wxappuser['token_time'] > time()) {

               $this->echomsg(array('token' => $shop_wxappuser['token']), 0);
                
            }

            $this->echomsg(array('openid' => $info['openid']), 10000);
        } else {

            $this->echomsg(array('openid' => $info['openid']), 10000);
        }
    }

    public function getSession() {
        $charid = md5(uniqid(mt_rand(), true));
        return $charid;
    }

    public function wxapp_registercomplex() {

        $code = I('code');
        $openid = I('openid');
        $code = json_decode(htmlspecialchars_decode($code), true);
        $userInfo = $code['userInfo'];




        $data['nickName'] = $userInfo['nickName'];
        $data['gender'] = $userInfo['gender'];
        $data['language'] = $userInfo['language'];
        $data['city'] = $userInfo['city'];
        $data['province'] = $userInfo['province'];
        $data['country'] = $userInfo['country'];
        $data['avatarUrl'] = $userInfo['avatarUrl'];
        $data['signature'] = $userInfo['signature'];
        $data['encryptedData'] = $userInfo['encryptedData'];
        $data['iv'] = $userInfo['iv'];
        $data['token'] = $this->getSession();
        $data['token_time'] = time() + 30 * 24 * 60 * 60;
   
        $data['shopid'] = $this->uid;
$data['create_time'] =  time();
        $shop_wxappuser = M('shop_wxappuser')->where(array('openid' =>  $openid))->find();
if($shop_wxappuser)
{

                 $res = M('shop_wxappuser')->where(array('id' =>  $shop_wxappuser['id']))->save($data);

}else{        

            $data['openid'] = $openid;

 $res = D('ShopWxappuser')->add($data);
}
        $this->echomsg($res);
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

    /**
     * 文案配置
     */
    public function set() {
        $goods = M('config'); // 实例化User对象
        $count = $goods->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show(); // 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $goods->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 文案编辑
     * @return mixed
     */
    public function set_edit() {
        $id = I('id', 0, 'intval');
        if (IS_POST) {
            $data = $_POST;
            $cate = explode('|', $data['category_name']);
            $data['category'] = $cate[0];
            $data['config_name'] = $cate[1];
            if ($id) {
                $updateData = [
                    'config_data' => $data['config_data'],
                    'update_time' => time(),
                ];
                $res = M('config')->where(array('id' => $id))->save($updateData);
            } else {
                $data['create_time'] = time();
                $data['update_time'] = time();
                $res = M('config')->add($data);
            }
            MyRedis::getProInstance()->delete('t_config_' . $data['config_name']);
            if ($res) {
                return $this->success('编辑成功', U('home/set'));
            } else {
                return $this->success('编辑失败', U('home/set_edit', array('id' => $id)));
            }
        }
        $config = array();
        if ($id) {
            $config = M('config')->where(array('id' => $id))->find();
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
    function redset() {
        $goods = M('red_config'); // 实例化User对象
        $count = $goods->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出

        $list = $goods->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        $city = D('City'); // 实例化User对象
        $citylist = $city->publicUserList();
        $this->assign('citylist', $citylist); // 公众号

        $this->display();
    }

    /**
     * 红包文案logo编辑
     * add by Qinmj 2016-11-22
     */
    function redset_edit() {

        $id = I('id', 0, 'intval');
        if (IS_POST) {
            $data = $_POST;

            $utype = M('red_config')->where(array('utype' => $data['utype']))->find();
            if ($utype && $utype['id'] != $id) {
                return $this->error('该公众号已经做过配置，请直接编辑！', U('home/redset'));
            }

            if ($id) {
                $data['update_time'] = time();
                $res = M('red_config')->where(array('id' => $id))->save($data);
            } else {
                $data['create_time'] = time();
                $data['update_time'] = time();
                $res = M('red_config')->add($data);
            }
            MyRedis::getProInstance()->delete('t_red_config_' . $data['utype']);
            if ($res) {
                return $this->success('编辑成功', U('home/redset'));
            } else {
                return $this->success('编辑失败', U('home/redset_edit', array('id' => $id)));
            }
        }

        //用户公众号 下拉框
        $city = D('City'); // 实例化User对象
        $public_city = $city->publicUserList();
        $this->assign('public_city', $public_city);

        $config = array();
        if ($id) {
            $config = M('red_config')->where(array('id' => $id))->find();
        }
        $this->assign('config', $config);

        $this->display();
    }

}
