<?php
/**
 * 
 * @authors 清月曦 (1604583867@qq.com)
 * @date    2017-05-01 11:27:23
 * @version $Id$
 */
namespace app\admin\controller;
use think\Controller;
   use app\common\model\Article;
use app\common\model\ArticleCategory;
use app\common\model\Config;
use app\common\model\Admin;
class BaseController extends Controller
{
    
    
    public $admin = [];
    public $config = [];
    public function _initialize(){
        // 判断是否登录，没有登录跳转登录页面
        if(!session('user_auth') || !session('user_auth_sign')){
            $this->redirect('public/login');
        }

        $activeRouter = request()->module() . '/' . request()->controller() . '/' . request()->action();
//         dump(strtolower($activeRouter));
        if(!in_array(strtolower($activeRouter), array("admin/help/index","admin/addons/getfiledownload","admin/addons/compare"))){
            $auth = new \com\Auth();
            if(!$auth->check($activeRouter, session('user_auth')['uid'])){
                return $this->error('你没有权限',cookie("prevUrl"));
            }
        }
        
        $this->admin =session('user_auth');
        if(  Admin::find(session('user_auth')['uid'])['status'] == 0){
                  session(null);
//        $this->redirect('index/index');
        return $this->error('等待审核','index/index');
        
        }
$this->uid = $this->admin['uid'];

$this->config = Config::where('uid',$this->uid )->find();
//        $articlelist = Article::with('category')->order('id desc')->limit(7)->select();
        	$articlelist =Article::where(array('status'=>1,'category_id'=>1)) 
                ->order('id desc')
                 ->select()
                    ->toArray();
                     $this->assign("user_auth", session('user_auth'));
                      $this->assign("articlelist", $articlelist);
                     $this->assign("userlogo", $this->config['logo_id']);
                        $this->assign("usertitle", $this->config['title']);
//        dump($articlelist);die(); 
        if ($this->request->isPjax()){
			$this->view->engine->layout(false);
		}else{
			$this->view->engine->layout(true);
		}
    }



}