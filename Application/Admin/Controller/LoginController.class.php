<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
        $this->display();
    }

    /**
     * 前端接口返回信息
     * @return array
     */
    public function ajax_json()
    {
        $json = array(
            'state' => 4,
            'msg' => '',
            'data' => null
        );

        return $json;
    }

    /**
     * 账户登陆
     */
    public function post(){
        $uname = I('request.uname','','htmlspecialchars');
        $pass = I('request.pwd','','htmlspecialchars');

        if(empty($uname) || empty($pass)){
            return $this->error('请先提交登陆信息',U('/login/index'));
        }

        $user = M('short_video_admin')->where(array('uname'=>$uname))->where(array('is_del' => 0))->find();
        if(!$user){
            return $this->error('登陆失败',U('/login/index'));
        }

        if($user['status'] != 1){
            return $this->error('账户正在审核中',U('/login/index'));
        }

        $password = encrypt_password($pass, $user['salt']);

        if($password != $user['pwd'] && $password !== true){
            return $this->error('登陆失败',U('/login/index'));
        }

        session('is_login', 1);
        session('login_user_id',$user['id']);
        // echo session_id();
        // echo json_encode($_SESSION);die();
        redirect(U('/index/index'));
    }
   protected function logdebug($text) {
 
        if (PATH_SEPARATOR == ':') {
            if (APP_DEBUG) {
                if (!is_string($text))
                    $text = json_encode($text);

                file_put_contents('/home/www/weapp.log', strftime("%Y-%m-%d %H:%M:%S", time())   . $text . "\n", FILE_APPEND);
            }
        } else {
            file_put_contents('c:/weapp.log', strftime("%Y-%m-%d %H:%M:%S", time()) . " " . $text . "\n", FILE_APPEND);
        }
    }
    /**
     * 用户注册
     */
    public function reg(){
        header("Content-type: application/json");
        $json = $this->ajax_json();
        if(empty($_POST)){
        $_POST = json_decode(file_get_contents('php://input'),true);}
$this->logdebug(json_encode($_POST));
        do{
            $type = I('post.type','','trim');


//             echo  $type;
//             if($type == 'personal'){
//                 $uname = I('post.uname','','trim');
//                 if(strlen($uname)<6 || strlen($uname)>26){
//                     $json['msg'] = "用户名在6-26个字符之间~";
//                     break;
//                 }

//                 $user = M('short_video_admin')->where(array('uname'=>$uname))->find();

//                 if($user ){
//                     $json['msg'] = '该用户名已被注册, 请换个名字注册吧~';
//                     break;

//                 }

//                 $password = I('post.password','','trim');
//                 if(strlen($password)<6 || strlen($password)>26){
//                     $json['msg'] = "登陆密码在6-26个字符之间~";
//                     break;
//                 }

//                 $password2 = I('post.password2','','trim');
//                 if($password != $password2){
//                     $json['msg'] = "两次输入密码不一致~";
//                     break;
//                 }
//                 $mobile = I('post.mobile','','trim');
//                 if(!is_mobile($mobile)){
//                     $json['msg'] = "手机号码不正确~";
//                     break;
//                 }

//                 $user = M('short_video_admin')->where(array('mobile'=>$mobile))->find();

//                 if($user ){
//                     $json['msg'] = '该手机号已被注册~';
//                     break;
//                 }
//                 $code = I('post.verification','','trim');
//                 if($code != '888666'){
//                     $res = M('sms_log ')->where(array('mobile'=>$mobile,'send_status'=>1,'status'=>0))->order("id DESC")->find();
//                     if($code && $code != $res['code']){
//                         $json['msg'] = "验证码错误~{$code}";
//                         break;
//                     }else{
//                         if($res){
//                             M('sms_log ')->where(['id'=>$res['id']])->save(['status'=>2]);
//                         }
//                     }

//                     $url = I('post.url','','trim');
//                     if(!$url){
// //                    return $this->error("请输入作品地址~");
//                     }
//                 }

//                 $identity = I('post.identity','','trim');
//                 if(!$identity){
//                     $json['msg'] = "请上传营业执照信息~";
//                     break;
//                 }
//                 $salt = random(12);
//                 $data = [
//                     'uname' => $uname,
//                     'pwd' => encrypt_password($password, $salt),
//                     'salt' => $salt,
//                     'mobile' => $mobile,
//                     'url' => $url,
//                     'identity' => $identity,
//                     'create_time' => time(),
//                     'role' => 2,
//                     'update_time' => time(),
//                     'status' => 0,
//                 ];

//                 $res = M('short_video_admin')->add($data);
//                 if($res){
//                     $json['state'] = 1;
//                     $data['msg'] = "注册成功~";
//                     break;
//                 }else{
//                     $data['msg'] = "注册失败~";
//                     break;
//                 }
//             }else
            
            if(true){
                $uname = I('post.uname','','trim');
                if(strlen($uname)<6 || strlen($uname)>26){
                    $json['msg'] = "用户名在6-26个字符之间~";
                    break;
                }
                    $user = M('short_video_admin')->where(array('uname'=>$uname))->find();
                if($user){
                    $json['msg'] = '该用户名已被注册, 请换个名字注册吧~';
                    break;
                }
                $password = I('post.password','','trim');
                if(strlen($password)<6 || strlen($password)>26){
                    $json['msg'] = "登陆密码在6-26个字符之间~";
                    break;
                }

                // $password2 = I('post.password2','','trim');
                // if($password != $password2){
                //     $json['msg'] = "两次输入密码不一致~";
                //     break;
                // }
                // $mobile = I('post.mobile','','trim');
                // if(!is_mobile($mobile)){
                //     $json['msg'] = "手机号码不正确~";
                //     break;
                // }
                // $user = M('short_video_admin')->where(array('mobile'=>$mobile))->find();

                // if($user){
                //     $json['msg'] = '该手机号已被注册~';
                //     break;
                // }
                $email = I('post.email','','trim');
                $user = M('short_video_admin')->where(array('email'=>$email))->find();
$this->logdebug(M('short_video_admin')->getLastSql());
$this->logdebug(json_encode($user));
                if($user){
                    $json['msg'] = '该email已被注册~';
                    break;
                }
                // $code = I('post.verification','','trim');
                // if($code != '888666') {
                //     $res = M('sms_log ')->where(array('mobile' => $mobile, 'send_status' => 1, 'status' => 0))->order("id DESC")->find();
                //     if ($code && $code != $res['code']) {
                //         $json['msg'] = "验证码错误~";
                //         break;
                //     } else {
                //         if ($res) {
                //             M('sms_log ')->where(['id' => $res['id']])->save(['status' => 2]);
                //         }
                //     }
                // }
                // $licence = I('post.licence','','trim');
                // if(!$licence){
                //     $json['msg'] = "请上传营业执照信息~";
                //     break;
                // }

                // $identity = I('post.identity','','trim');
                // if(!$identity){
                //     $json['msg'] = "请上传身份证信息~";
                //     break;
                // }

                $salt = random(12);
                $data = [
                    'uname' => $uname,
                    'pwd' => encrypt_password($password, $salt),
                    'salt' => $salt,
                    'mobile' => $mobile,
                    // 'url' => $url,
                    'licence' => $licence,
                    'identity' => $identity,
                    'create_time' => time(),
                    'role' => 3,
                    'update_time' => time(),
                    'status' => 0,
                ];

                $res = M('short_video_admin')->add($data);
                if($res){
                    $json['state'] = 1;
                    $json['msg'] = "注册成功~";
                    break;
                }else{
                    $json['msg'] = "注册失败~";
                    break;
                }
            }
            
            // else{
            //     $json['msg'] = ("请选择注册账户类型~");
            //     break;
            // }
        }while(false);
        echo json_encode($json);die();
    }

    /**
     * 获取验证码
     */
    public function vercode(){
        $json = $this->ajax_json();
        do{
            $mobile = I('request.mobile','','strval');

            if(!$mobile){
                $json['msg'] = '请正确数据手机号码';
                break;
            }

            if(! preg_match("/^1[34578]\d{9}$/", $mobile)){
                //手机格式不通过
                $json['msg'] = '请正确输入手机号码';
                break;
            }

            $member = D('short_video_admin')->where(['mobile'=>$mobile])->find();
            if($member){
                $json['msg'] = '该手机已经被注册了~';
                break;
            }

            $code = create_code();
            $bool = send_msg($mobile, "【美联美客】您的验证码是".$code, $code, "");
            if(!$bool){
                $json['msg'] = '短信发送失败，请不要频繁提交';
                break;
            }

            $json['state'] = 1;
            $json['msg'] = "";
            $json['data'] = "";

        }while(false);
        echo json_encode($json);die();
    }

    // 列表
    public function show()
    {
        $sid = trim($_REQUEST['sid']);//模型
        $fileback = !empty($_REQUEST['fileback']) ? trim($_REQUEST['fileback']) : 'pic';//回跳input
        $this->assign('sid', $sid);
        $this->assign('fileback', $fileback);
        $this->display();
    }

    // 本地图片上传
    public function upload()
    {
        echo('<div style="font-size:12px; width: 150px; height:24px; line-height:24px; background: #fff;">');
        $uppath = './upload/';
        $sid = trim($_POST['sid']);//模型
        $fileback = !empty($_POST['fileback']) ? trim($_POST['fileback']) : 'pic';//回跳input
        if ($sid) {
            $uppath .= $sid . '/';
            @mkdir($uppath, 0755, true);
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = $uppath; // 设置附件上传根目录
        $upload->subName = array('date', 'Y/m');
        $upload->savePath = ''; // 设置附件上传（子）目录
        // 上传文件
        if (!$info = $upload->upload()) {
            $error = $upload->getError();
            if ($error == '上传文件类型不允许') {
               // $error .= '，可上传<font color=red>JPEG,JPG,PNG,GIF</font>';
                echo "<script type='text/javascript'>alert('请上传<font color=red>JPEG,JPG,PNG,GIF</font>格式图片');</script>";
            }
            echo '<script type="text/javascript">setTimeout(function(){window.location.href = "?s=/login/show/sid/' . $sid . '/fileback/' . $fileback . '";},100);</script>';
            die();
            //dump($up->getErrorMsg());
        }

        // 处理缩略图
        if ($sid == 'short_video') {

            $thumbdir = $uppath . $info['upthumb']['savepath'] . $info['upthumb']['savename'];
            $img = new Image(2);
            $img->open($thumbdir);
            $size = $img->size();
            if ($size[0] > $size[1]) {
                //$img->thumb(366,184);
                $data = $this->imageCropper($size[0], $size[1], 366, 184);
                $img->crop($data['c_with'], $data['c_height'], $data['x'], $data['y'], 366, 184)->save($thumbdir . "_t.jpg");
            } else {
                //$img->thumb(366,488);
                $data = $this->imageCropper($size[0], $size[1], 366, 488);
                $img->crop($data['c_with'], $data['c_height'], $data['x'], $data['y'], 366, 488)->save($thumbdir . "_t.jpg");
            }

            $info['upthumb']['savename'] = $info['upthumb']['savename'] . '_t.jpg';

        }
        echo "<script type='text/javascript'>if(parent.document.getElementsByClassName('pic_view_detail').length>0){parent.document.getElementsByClassName('pic_view_detail')[0].setAttribute('src','/upload/" . $sid . "/" . $info['upthumb']['savepath'] . $info['upthumb']['savename'] . "');}if(parent.document.getElementById('" . $fileback . "')){parent.document.getElementById('" . $fileback . "').value='/upload/" . $sid . '/' . $info['upthumb']['savepath'] . $info['upthumb']['savename'] . "';parent.document.getElementById('" . $fileback . "').setAttribute('data-url','/upload/" . $sid . '/' . $info['upthumb']['savepath'] . $info['upthumb']['savename'] . "');}</script>";
        echo '<script type="text/javascript">setTimeout(function(){window.location.href = "?s=/login/show/sid/' . $sid . '/fileback/' . $fileback . '";},100);</script>';
        echo '</div>';
    }

    /**
     * 注册协议
     */
    public function agreement(){
        $this->display();
    }

}