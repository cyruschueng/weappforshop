<?php

namespace Admin\Controller;

use Think\Controller;
use Think\Page;
use Redis\MyRedis;

class BannerController extends BasexController {

    public function blist() {
        $this->logdebug(__FILE__ . '[' . __LINE__ . "] " . '[' . __ACTION__ . "] " . json_encode($_GET));
        $ShopVbanner = D('ShopVbanner');
        $list = $ShopVbanner->where(array('is_del' => 0, 'uid' => $this->uid))->limit(7)->select();
        foreach ($list as &$value) {
            $value['picurl'] = 'https://small.kuaiduodian.com' . $value['picurl'];
        }
        $this->echomsg($list);
    }

}
