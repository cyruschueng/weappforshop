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
 * @shengyue 2016-07-04
 * 刮刮卡管理
 * Class CardController
 * @package Admin\Controller
 */
class SysController extends BaseController
{
    /**
     * 消息列表
     */
    public function msg()
    {
        $list = M('auto_msg')->select();

        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 消息编辑
     */
    public function msg_edit(){
        $id = I('id','');
        if(IS_POST){
            $data = $_POST;
            $msg = M('auto_msg')->where(array('send_type'=>$data['send_type']))->find();
            if($msg && $msg['id'] != $id){
                return $this->error('该消息类型已经存在，请不要重复添加', U('sys/msg_edit',array('id'=>$id)));
            }
            $data['create_time'] = time();
            $data['update_time'] = time();
            if($id){
                $res = M('auto_msg')->where(array('id'=>$id))->save($data);
            }else{
                $res = M('auto_msg')->add($data);
            }
            if($res){
                return $this->success('操作成功',U('sys/msg'));
            }else{
                return $this->error('保存失败', U('sys/msg_edit',array('id'=>$id)));
            }
        }
        if($id){
            $msg = M('auto_msg')->where(array('id'=>$id))->find();
            $this->assign('msg', $msg);
        }
        $this->display();
    }

    /**
     * 系统汇总数据
     */
    public function user_tongji(){
        // 用户相关
        ///////////////////////////////// 昨天数据
        // 微信用户
        $time = strtotime(date("Y-m-d",strtotime("-1 day")));
        $time2 = strtotime(date("Y-m-d"));
        $data['yestory_weixin'] = M()->query("SELECT count(*) as total FROM t_users_1 WHERE create_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_2 WHERE create_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_3 WHERE create_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_4 WHERE create_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_5 WHERE create_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_6 WHERE create_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_7 WHERE create_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_8 WHERE create_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_9 WHERE create_time BETWEEN $time AND $time2
                            ");

        $data['weixin_total'] = M()->query("SELECT count(*) as total FROM t_users_1 WHERE 1 UNION ALL
                            SELECT count(*) as total FROM t_users_2 WHERE 1 UNION ALL
                            SELECT count(*) as total FROM t_users_3 WHERE 1 UNION ALL
                            SELECT count(*) as total FROM t_users_4 WHERE 1 UNION ALL
                            SELECT count(*) as total FROM t_users_5 WHERE 1 UNION ALL
                            SELECT count(*) as total FROM t_users_6 WHERE 1 UNION ALL
                            SELECT count(*) as total FROM t_users_7 WHERE 1 UNION ALL
                            SELECT count(*) as total FROM t_users_8 WHERE 1 UNION ALL
                            SELECT count(*) as total FROM t_users_9 WHERE 1
                            ");

        // 注册用户
        $data['yestory_member'] = M('users_member')->where("create_time BETWEEN $time AND $time2")->count();
        // 注册用户
        $data['member_total'] = M('users_member')->count();

        // 关注用户
        $data['yestory_subcribe'] = M()->query("SELECT count(*) as total FROM t_users_1 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_2 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_3 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_4 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_5 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_6 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_7 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_8 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_9 WHERE is_subscribe=1 AND subcribe_time BETWEEN $time AND $time2
                            ");

        $data['subcribe_total'] = M()->query("SELECT count(*) as total FROM t_users_1 WHERE is_subscribe=1 UNION ALL
                            SELECT count(*) as total FROM t_users_2 WHERE is_subscribe=1 UNION ALL
                            SELECT count(*) as total FROM t_users_3 WHERE is_subscribe=1 UNION ALL
                            SELECT count(*) as total FROM t_users_4 WHERE is_subscribe=1 UNION ALL
                            SELECT count(*) as total FROM t_users_5 WHERE is_subscribe=1 UNION ALL
                            SELECT count(*) as total FROM t_users_6 WHERE is_subscribe=1 UNION ALL
                            SELECT count(*) as total FROM t_users_7 WHERE is_subscribe=1 UNION ALL
                            SELECT count(*) as total FROM t_users_8 WHERE is_subscribe=1 UNION ALL
                            SELECT count(*) as total FROM t_users_9 WHERE is_subscribe=1
                            ");

        // 取消关注
        $data['yestory_unsubcribe'] = M()->query("SELECT count(*) as total FROM t_users_1 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_2 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_3 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_4 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_5 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_6 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_7 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_8 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2 UNION ALL
                            SELECT count(*) as total FROM t_users_9 WHERE is_subscribe=0 AND unsubcribe_time BETWEEN $time AND $time2
                            ");

        $data['unsubcribe_total'] = M()->query("SELECT count(*) as total FROM t_users_1 WHERE is_subscribe=0 UNION ALL
                            SELECT count(*) as total FROM t_users_2 WHERE is_subscribe=0 UNION ALL
                            SELECT count(*) as total FROM t_users_3 WHERE is_subscribe=0 UNION ALL
                            SELECT count(*) as total FROM t_users_4 WHERE is_subscribe=0 UNION ALL
                            SELECT count(*) as total FROM t_users_5 WHERE is_subscribe=0 UNION ALL
                            SELECT count(*) as total FROM t_users_6 WHERE is_subscribe=0 UNION ALL
                            SELECT count(*) as total FROM t_users_7 WHERE is_subscribe=0 UNION ALL
                            SELECT count(*) as total FROM t_users_8 WHERE is_subscribe=0 UNION ALL
                            SELECT count(*) as total FROM t_users_9 WHERE is_subscribe=0
                            ");
        print_r($data);
        //$this->display('sys/user_tontji', $data);
    }

    /**
     * 签到数据
     */
    public function signed(){
        $signed = 1;
        $redis = \Redis\MyRedis::getGameInstance();
        if($redis->exists("set:signed")){
            $signed = intval($redis->get("set:signed"));
        }

        if(IS_POST){
            $signed = I('post.signed',1,'intval');
            $signed = $signed < 1?1:$signed;
            $redis->set("set:signed", $signed);
        }

        $this->assign('signed', $signed);
        $this->display();
    }

    /**
     * 短信发送
     */
    public function sms(){
        // $this

        if(IS_POST){
            $send_member = I('send_member',"","trim");
            $send_msg = I('send_msg','','trim');

            $send_member = explode("\r\n", $send_member);
            $member_list = [];
            foreach($send_member as $member){
                if(trim($member)){
                    $member_list[] = trim($member);
                }
            }
            if($send_member && $send_msg){
                file_put_contents("/data/log/send_sys.log",date("Y-m-d H:i:s=")."【{$this->admin['id']}={$this->admin['uname']}】[".join(',',$member_list)."][{$send_msg}]\r\n",FILE_APPEND);

                $result = send_msgs(join(',',$member_list), $send_msg);

                file_put_contents("/data/log/send_sys.log",date("Y-m-d H:i:s=").$result."\r\n", FILE_APPEND);
                if($result >0){
                    $this->success("发送成功【".$result.'】');
                }else{
                    $this->error("发送失败【".$result.'】');
                }

            }else{
                $this->error("请输入接收用户手机号和短信内容");
            }

            return false;

        }


        $this->display();
    }

    public function index(){
        $modules = array('Admin');  //模块名称
        $i = 0;
        foreach ($modules as $module) {
            $all_controller = $this->getController($module);
            foreach ($all_controller as $controller) {
                $controller_name = $controller;
                $all_action = $this->getAction($module, $controller_name);
                foreach ($all_action as $action) {
                    $data[$i] = array(
                        'name' => $controller . '_' . $action,
                        'status' => 1
                    );
                    $i++;
                }
            }
        }
        echo '<pre>';
        print_r($data);
    }

    //获取所有控制器名称
    protected function getController($module){
        if(empty($module)) return null;
        $module_path = APP_PATH . '/' . $module . '/Controller/';  //控制器路径
        if(!is_dir($module_path)) return null;
        $module_path .= '/*.class.php';
        $ary_files = glob($module_path);
        foreach ($ary_files as $file) {
            if (is_dir($file)) {
                continue;
            }else {
                $files[] = basename($file, C('DEFAULT_C_LAYER').'.class.php');
            }
        }
        return $files;
    }
    //获取所有方法名称
    protected function getAction($module, $controller){
        if(empty($controller)) return null;
        $content = file_get_contents(APP_PATH . '/'.$module.'/Controller/'.$controller.'Controller.class.php');
        preg_match_all("/.*?public.*?function(.*?)\(.*?\)/i", $content, $matches);
        $functions = $matches[1];
        //排除部分方法
        $inherents_functions = array('_initialize','__construct','getActionName','isAjax','display','show','fetch','buildHtml','assign','__set','get','__get','__isset','__call','error','success','ajaxReturn','redirect','__destruct','_empty');
        foreach ($functions as $func){
            $func = trim($func);
            if(!in_array($func, $inherents_functions)){
                $customer_functions[] = $func;
            }
        }
        return $customer_functions;
    }
}
