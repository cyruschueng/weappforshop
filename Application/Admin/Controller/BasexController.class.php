<?php

namespace Admin\Controller;
use Redis\MyRedis;
use Think\Controller;
use Weixin\MyWechat;

class BasexController extends Controller {

    public $admin = [];
    public $config= null;
    public $wechat = null;
    public $mallname = null;
    public $uid = null;
    public $wxuid = null;

    public function _initialize() {
        $this->mallname = I('mallname')?I('mallname'):$_GET['mallname'];
        $this->logdebug('---------------------------++++++++++++++++++++++++++++++----' . $_SERVER["REQUEST_URI"]);
        $this->logdebug('^^^^p----' . json_encode($_POST));
        $this->logdebug('g----' . json_encode($_GET));
        $this->admin = M('admin')->where(array('mallname' => $this->mallname))->find();
         $this->uid = $this->admin['id'];
        if(empty($this->admin) || empty($this->uid )){
            $this->echomsgerr("找不到店家的管理员 {$this->mallname}");
        }
        
    	$statics = M('statics')->where(array('day'=>strtotime(date("Y-m-d")) , 'shopid'=> $this->uid ))->find();

    	if ($statics) {
    
            M('statics')->where(array('day'=>strtotime(date("Y-m-d")) , 'shopid'=> $this->uid ))->setInc("pv", 1);
            // M('statics')->where(array('day'=>strtotime(date("Y-m-d")) , 'shopid'=> $this->uid ))->setInc("uv", 1);


    	}else{ 
            $res = D('statics')->add([
                'day'  	 => strtotime(date("Y-m-d")) ,
               'shopid'=> $this->uid ,
                'pv'  	 =>  1,
                'uv' 	 =>  1,
                'uvex'  	 =>  1,
                'pvex'  	 =>  1
            ]);
    	}
  
       
        $this->config = M('config')->where(array('uid' => $this->admin['id']))->find();


         $this->logdebug('-----config+----' .  json_encode(  $this->config  ));
        $this->shopname  = $this->config['title']?$this->config['title']:'快多店';
       MyRedis::getADInstance()->hset('statics_'.strtotime(date("Y-m-d")),   $this->wxuid  ,1);
                            $this->logdebug('^^MyRedis ' .I('token'));
        if (!empty(I('token'))) {

 M('shop_wxappuser')->where(array('token' => I('token')))->save(  array('token_time' => time() + 30 * 24 * 60 * 60));
                                $this->logdebug('^^MyRedis ' . $this->wxuid );
            $shop_wxappuser = M('shop_wxappuser')->where(array('token' => I('token')))->find();
            $this->wxuid = $shop_wxappuser['id'];
   MyRedis::getADInstance()->hset('statics_'.strtotime(date("Y-m-d")),   $this->wxuid  ,1);
                    $this->logdebug('^^MyRedis ' . $this->wxuid );


        } else {
            $this->wxuid = 0;//重走授权
        }
    }

    protected function logdebug($text) {
        if (PATH_SEPARATOR == ':') {
            if (APP_DEBUG) {
                if (!is_string($text))
                    $text = json_encode($text);

                file_put_contents('/home/www/weapp.log', strftime("%Y-%m-%d %H:%M:%S", time()) . $text . "\n", FILE_APPEND);
            }
        } else {
            file_put_contents('c:/weapp.log', strftime("%Y-%m-%d %H:%M:%S", time()) . " " . $text . "\n", FILE_APPEND);
        }
    }

    protected function echomsg($data = null, $code = 0) {
        $arr['code'] = $code;
        $arr['data'] = $data;
        $ret = json_encode($arr);
        $this->logdebug($ret);
        echo $ret;
        die();
    }
    protected function echomsgerr($data = null, $code = 1) {
        $arr['code'] = $code;
        $arr['data'] = $data;
        $ret = json_encode($arr);
        $this->logdebug(' !!!!!!!!!!!!! error '.$ret);
        echo $ret;
        die();
    }

    /**
     * 调用微信类返回 access_token
     * @param  int $type 城市ID
     * @return object 微信公共类的对象
     */
    protected function initWechat($type) {
        if ($this->wechat) {
            return $this->wechat;
        }

        $cityInfo = M('City')->where(['city_id' => $type])->find();

        if (empty($cityInfo)) {
            die('No Found Weixin Option.');
        }
        $options = array(
            'token' => $cityInfo['red_token'], //填写你设定的key
            'encodingaeskey' => $cityInfo['encodingaeskey'], //填写加密用的EncodingAESKey
            'appid' => $cityInfo['appid'], //填写高级调用功能的app id
            'appsecret' => $cityInfo['appsecret'] //填写高级调用功能的密钥
        );


        return $this->wechat = new MyWechat($options);
    }

    /**
     * 前端接口返回信息
     * @return array
     */
    public function ajax_json() {
        $json = array(
            'state' => 4,
            'msg' => '',
            'data' => null
        );

        return $json;
    }

    public function index1() {
        $this->display();
    }

    /**
     * 导出csv文件
     * @param $head = array ('订单号','应付结算','是否已回款','是否已提批次','备注');
     * @param $data=array();  输出文件内容
     * @param $filename   文件名称
     */
    public function exportCsv($head, $data, $filename) {
        // 输出Excel文件头
        //header ( 'Content-Type: application/vnd.ms-excel' );
        header("Content-type:text/csv");
        header("Content-Disposition: attachment;filename=$filename.csv");
        header('Cache-Control: max-age=0');
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        //文件的标题头部
        foreach ($head as $i => $v) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head [$i] = iconv('utf-8', 'gbk', $v);
        }
        // 将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);

        //文件的内容
        $cnt = 0; // 计数器
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 1000;
        foreach ($data as $rows) {
            $cnt ++;
            if ($limit == $cnt) { // 刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }
            // 读取表数据
            $content = array();
            foreach ($rows as $keyName => $value) {// 列写入
                $content [] = iconv('utf-8', 'gbk', $value);
                //     			$a = @iconv("utf-8","gbk",$res);$b = @iconv("gbk","utf-8",$a);
            }
            fputcsv($fp, $content);
        }
        fclose($fp);
    }

}
