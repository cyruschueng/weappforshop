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
 * ShopVbanner 
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(100) NOT NULL COMMENT '图片',
`picurl` varchar(100) NOT NULL COMMENT '图片',
`order` tinyint(3) DEFAULT NULL COMMENT '排序',
`url` varchar(256) NOT NULL COMMENT 'url',
`is_del` tinyint(2) DEFAULT '1' COMMENT '状态：0禁用，1启用',
`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(11) DEFAULT '0',
`begin_time` int(11) DEFAULT '0',
`end_time` int(11) DEFAULT '0',
 */
// https://small.kuaiduodian.com//pay/wxnotifyurl?mallname=wxcba01efc22c3fcc6
class PayController extends BasexController
{


    public function wxnotifyurl(){
        vendor("wxpay.wxpay");
        $xml = file_get_contents('php://input');
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
  $this->logdebug('wxnotifyurl  debug  ' . json_encode($arr));



        //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        $out_trade_no   = $arr['out_trade_no'];      //商户订单号
        $trade_no       = $arr['transaction_id'];          //支付宝交易号
        $trade_status   = $arr['result_code'];      //交易状态
        $total_fee      = $arr['total_fee'];         //交易金额
        $notify_time    = strtotime($arr['time_end']); //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
        $buyer_email    = $arr['buyer_email'];       //买家支付宝帐号；
        $parameter = array(
            "total_fee"      => $total_fee,     //支付宝交易号；
            "status"=>1,
        );
        if($arr['result_code'] == 'SUCCESS') {
            //更新订单状态
            $order=M('shop_order');
            $re=$order->where('id="'.trim($arr['out_trade_no']).'"')->save($parameter);
            if($re){
            
                //返回xml格式的通知回去
                $return = array('return_code'=>'SUCCESS','return_msg'=>'OK');
                $re_xml = '<xml>';
                foreach($return as $k=>$v){
                    $re_xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
                }
                $re_xml.='</xml>';

                echo $re_xml;
            }else{
                echo "fail";
            }
        }else{
            echo "fail";
        }
    }

        public function wxapp_get_pay_data(){
        //引入文件
        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');
        vendor("wxpay.wxpay");
        //支付完成后的回调处理页面
        $notify_url = C("wxpay.wx_notify_url")."/mallname/{$this->config['appid']}";
        //$notify_url = "https://kuaiduodian.com/dushiw/index.php/Api/Pay/wxnotifyurl";

        $token = $_REQUEST['token'];
        if (!$token) {

                $this->echomsg('登录状态异常',1);
            exit();
        }

        $order_id = intval($_REQUEST['order_id']);//订单id
        $order = M('shop_order')->where("id=".intval($order_id)." AND `is_del`=0")->find();
        if (!$order) {

     $this->echomsg('订单信息错误',1);


            exit();
        }




        $money = trim($_REQUEST['money']);// money

             if (empty(       $money )) {

     $this->echomsg('订单信息错误(hint:1)',1);


            exit();
        }

    
        // 获取支付金额       
        $total =    $money*100;     // 转成分
        // 商品名称
        $subject = '小程序:'.$_REQUEST['remark'];
        // 订单号，示例代码使用时间值作为唯一的订单ID号
        $out_trade_no = $order_id ;

 
        $openId = M("shop_wxappuser")->where("token='". $token ."'")->getField("openid");
        if(!$openId){
             $this->echomsg('请重新登陆(hint:2)',1);

            exit();
        }


        // $product=M('order_product')->where("`order_id`=".intval($order['id']))->field('name')->select();
        // $body = '';
        // foreach ($product as $key => $val) {
        //     if ($key==0) {
        //         $body .=$val['name'];
        //     }else{
        //         $body .=','.$val['name'];
        //     }
        // }


$body = '';
  $tmpmap = json_decode(   $order['goodsjsonstr'], true);
            $gtmpall = array();
            $sumprice = 0;
            foreach ($tmpmap as $key => $value) {
                $shop_goods = M('ShopGoods')->find($value['goodsId']);

            if ($key==0) {
                $body .=$shop_goods['name'];
            }else{
                $body .=','.$shop_goods['name'];
            }


                // $gtmp = array();
                // $sumprice+=$shop_goods['minprice'] * $value['number'];
                // $gtmp['pic'] = 'https://small.kuaiduodian.com/' . explode(',', $shop_goods['pic'])[0];
                // $gtmp['id'] = $shop_goods['id'];
                // $gtmpall[] = $gtmp;
            }

      error_log(  __FILE__.'['.__LINE__."]");
 if(strlen(      $body )> 100 )
{
    $body  = mb_substr(   $body, 0, 50) .'...' ;
} $this->logdebug('body  debug  oo ==' .     $body );
      error_log(  __FILE__.'['.__LINE__."]");
        //配置支付参数
        define( "_APPID",  $this->config['appid']  );
        define( "_MCHID", $this->config['mchid']  );
        define( "_KEY", $this->config['key']  );
        define( "_APPSECRET", $this->config['secret']    );
         error_log(  __FILE__.'['.__LINE__."]");
        //统一下单




    $arrget =  mt_rand();


       error_log( 'http://v.kuaiduodian.com/fonts/xdebug-trace-tree-master/?file='  ."/home/wwwroot/www/fonts/tmpval/{$arrget}.xt" );
    

    xdebug_start_trace( "/home/wwwroot/www/fonts/tmpval/{$arrget}" );
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body);                        //商品名称
        $input->SetAttach("小程序"); 
         error_log(  __FILE__.'['.__LINE__."]");
        $input->SetOut_trade_no($out_trade_no);  //订单号
        $input->SetTotal_fee($total);                       //订单总金额
        $input->SetTime_start(date("YmdHis"));           //订单生成时间
        $input->SetTime_expire(date("YmdHis", time() + 7200)); //订单失效时间
        $input->SetGoods_tag('');                       //设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
        $input->SetNotify_url($notify_url);             //异步通知地址
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
         error_log(  __FILE__.'['.__LINE__."]");
        $order_data = \WxPayApi::unifiedOrder($input); 
 error_log(  __FILE__.'['.__LINE__."]");
 $this->logdebug('unifiedOrder  debug  oo ==' . json_encode(   $input ));
 $this->logdebug('unifiedOrder  debug  oo ==' . json_encode(   $order_data ));

      error_log(  __FILE__.'['.__LINE__."]");

 if( $order_data[result_code] == 'FAIL'){
      $this->echomsg('订单已经过期,请重新下单 ',1);
 }
       error_log(  __FILE__.'['.__LINE__."]");
         $array=array(
            "appId"    =>$order_data['appid'],
            "timeStamp"=>(string)time(),
            "nonceStr" =>$order_data['nonce_str'],
            "package"  =>"prepay_id=".$order_data['prepay_id'],
            "signType" =>"MD5",
        );
        $str = 'appId='.$array['appId'].'&nonceStr='.$array['nonceStr'].'&package='.$array['package'].'&signType=MD5&timeStamp='.$array['timeStamp'];
        //重新生成签名
        $array['paySign']=strtoupper(md5($str.'&key='.\WxPayConfig::KEY));
    
      error_log(  __FILE__.'['.__LINE__."]");

          $this->echomsg($array);
        //exit();

    }
    public function wxapp_get_pay_data1()
    {
              $this->echomsg('官方示例店铺 暂不让支付',1);
              

















        // $class = I('class', 0, 'intval');
        $ShopVbanner = D('ShopVbanner'); 
      
        $ShopVbanner->where(array('is_del' => 0));
        $count = $ShopVbanner->count();// 查询满足要求的总记录数
        $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //$this->assign('class', $class);
        $list = $ShopVbanner->where(array('is_del' => 0))->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 编辑信息
     */
    public function edit()
    {
        $id = I('request.id', 0, 'intval');

        if (IS_POST) {
            $data = $_POST;
            $data['type'] = join(',', (array)$data['tag_id']);
         

            if ($id) {
                $data['update_time'] = time();
                $data['begin_time'] = strtotime($data['begin_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $res = D('ShopVbanner')->where(array('id' => $id))->save($data);
        
            } else {
                $data['begin_time'] = strtotime($data['begin_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $data['update_time'] = time();
                $data['create_time'] = time();
                $res = D('ShopVbanner')->add($data);
            }

            // 删除缓存
            if ($res) {
                return $this->success('操作成功', U('/shop_vbanner/index'));
            } else {
                return $this->error('操作失败', U('/shop_vbanner/edit', array('id' => $id)));
            }
        }

        $shop_vbanner = [];
        if ($id) {
            $shop_vbanner = M('ShopVbanner')->find($id);
            $shop_vbanner['begin_time'] = date('Y-m-d H:i:s', $shop_vbanner['begin_time']);
            $shop_vbanner['end_time'] = date('Y-m-d H:i:s', $shop_vbanner['end_time']);
        }
        $shop_vbanner_tag = array();
        // '1 banner  2热门推荐  3观点  4幽默   5生活',
        $shop_vbanner_tag = get_video_type();
        
        $this->assign('shop_vbanner_tag', $shop_vbanner_tag);
        $this->assign('shop_vbanner', $shop_vbanner);
        $this->display();
    }

    public function delete()
    {
        $idList = explode(',', $_GET['list']);

        if (empty($idList)) {
            return $this->error('提交的id为空', U('/shop_vbanner/index'));
        }

        $time = time();
        foreach ($idList as $id) {
            D('ShopVbanner')->where(array('id' => $id))->save(array('is_del'=>1,'update_time'=>$time));
        }
        return $this->success('删除成功', U('/shop_vbanner/index'));
    }
}
