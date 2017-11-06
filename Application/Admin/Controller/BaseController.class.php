<?php
namespace Admin\Controller;
use Think\Controller;
use Weixin\MyWechat;
class BaseController extends Controller {
	public $admin = [];
	public $wechat = null;
    public function _initialize(){

        header("Location: /v2/"); die('404');
        /**
         * 验证登陆
         */
		// echo json_encode(session_get_cookie_params());
	
        if(! session('is_login')){
            return redirect(U('/login/index'));
        }

        $this->admin = M('short_video_admin')->find(session('login_user_id'));
        if(!$this->admin){
            return redirect(U('/login/index'));
        }

		$this->assign('role', $this->admin['role']);
        $this->assign('admin', $this->admin);

		$time = time();
		$sign = md5("AUTOLOGIN~!@#$%"."&autologin=duanxiu&time={$time}&uid=".$this->admin['id']."&role=".$this->admin['role']);
		if(strpos($_SERVER['HTTP_HOST'],'kuaiduodian.com') != false){
			$host = 'http://dadicinema.kuaiduodian.com';
		}else{
			$host = 'http://ddyy.hotwifibox.com';
		}
		// 发起我要上电视活动,
		$this->assign('jump_ipm_url', $host . '/activity/index?time='.time().'&sign='.$sign.'&r='.md5(microtime(true)).'&autologin=duanxiu&sid='.uniqid().'&tvid='.$this->admin['tv_id'].'&uid='.$this->admin['id'].'&role='.$this->admin['role']);
    }

	/**
	 * 调用微信类返回 access_token
	 * @param  int $type 城市ID
	 * @return object 微信公共类的对象
	 */
	protected function initWechat($type)
	{
		if ($this->wechat) {
			return $this->wechat;
		}

		$cityInfo = M('City')->where(['city_id'=>$type])->find();

		if(empty($cityInfo)){
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
    public function ajax_json()
    {
        $json = array(
            'state' => 4,
            'msg' => '',
            'data' => null
        );

        return $json;
    }

    public function index1(){
        $this->display();
    }
    
    /**
     * 导出csv文件
     * @param $head = array ('订单号','应付结算','是否已回款','是否已提批次','备注');
     * @param $data=array();  输出文件内容
     * @param $filename   文件名称
     */
    public function exportCsv($head,$data,$filename){
    	// 输出Excel文件头
    	//header ( 'Content-Type: application/vnd.ms-excel' );
    	header("Content-type:text/csv");
    	header ( "Content-Disposition: attachment;filename=$filename.csv");
    	header ( 'Cache-Control: max-age=0' );
    	// 打开PHP文件句柄，php://output 表示直接输出到浏览器
    	$fp = fopen ( 'php://output', 'a' );
    	//文件的标题头部
    	foreach ( $head as $i => $v ) {
    		// CSV的Excel支持GBK编码，一定要转换，否则乱码
    		$head [$i] = iconv ( 'utf-8', 'gbk', $v );
    	}
    	// 将数据通过fputcsv写到文件句柄
    	fputcsv ( $fp, $head );
    	 
    	//文件的内容
    	$cnt = 0;// 计数器
    	// 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
    	$limit = 1000;
    	foreach ( $data as $rows ) {
    		$cnt ++;
    		if ($limit == $cnt) { // 刷新一下输出buffer，防止由于数据过多造成问题
    			ob_flush ();
    			flush ();
    			$cnt = 0;
    		}
    		// 读取表数据
    		$content = array ();
    		foreach($rows as $keyName=>$value){// 列写入
    			$content [] = iconv ( 'utf-8', 'gbk', $value);
    			//     			$a = @iconv("utf-8","gbk",$res);$b = @iconv("gbk","utf-8",$a);
    		}
    		fputcsv ( $fp, $content );
    
    
    	}
    	fclose($fp);
    	 
    }
}