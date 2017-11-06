<?php

namespace Admin\Controller;

use Think\Controller;
use Think\Page;
use Org\Util\File;
use Think\Image;

class UploadController extends BaseController
{
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
        echo('<div style="font-size:12px; height:30px; line-height:30px">');
        $uppath = './upload/';
        $sid = trim($_POST['sid']);//模型
         $i= trim($_POST['i']);//模型
         if(empty($i))$i='';
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
                $error .= '，可上传<font color=red>JPEG,JPG,PNG,GIF</font>';
            }
            exit($error . ' [<a href="?s=/admin/upload/show/sid/' . $sid . '/fileback/' . $fileback . '">重新上传</a>]');
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
        echo "<script type='text/javascript'>if(parent.document.getElementsByClassName('pic_view_detail".$i."').length>0){parent.document.getElementsByClassName('pic_view_detail".$i."')[0].setAttribute('src','/upload/" . $sid . "/" . $info['upthumb']['savepath'] . $info['upthumb']['savename'] . "');}if(parent.document.getElementById('" . $fileback . "')){parent.document.getElementById('" . $fileback . "').value='/upload/" . $sid . '/' . $info['upthumb']['savepath'] . $info['upthumb']['savename'] . "';parent.document.getElementById('" . $fileback . "').setAttribute('data-url','/upload/" . $sid . '/' . $info['upthumb']['savepath'] . $info['upthumb']['savename'] . "');}</script>";
        echo '文件上传成功　[<a href="?s=/admin/upload/show/sid/' . $sid . '/fileback/' . $fileback . '">重新上传</a>]';
        echo '</div>';
    }

    public function select()
    {
        $allowFiles = array('.jpg', '.png', '.gif');
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        $listSize = 50;
        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $start = $start < 1 ? 1 : $start;
        $start = ($start - 1) * $listSize;
        $end = $start + $size;

        $uppath = realpath('./upload/') . '/';
        $sid = trim($_REQUEST['sid']);//模型
        $fileback = !empty($_REQUEST['fileback']) ? trim($_REQUEST['fileback']) : 'pic';//回跳input

        $this->assign('sid', $sid);
        $this->assign('fileback', $fileback);

        if ($sid) {
            $uppath .= $sid . '/';
            @mkdir($uppath, 0755, true);
        }

        /* 获取文件列表 */
        $uppath = str_replace('\\', '/', $uppath);
        $files = getfiles($uppath, $allowFiles);

        if (!count($files)) {
            /* 返回数据 */
            $result = array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            );

            $this->assign('data', $result);
            $this->display();
            return true;
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }

        /* 返回数据 */
        $result = array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        );

        $Page = new Page(count($files), $listSize);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);
        $this->assign('data', $result);
        $this->display();
    }

    public function showgood()
    {
        $sid = trim($_REQUEST['sid']);//模型
        $fileback = !empty($_REQUEST['fileback']) ? trim($_REQUEST['fileback']) : 'pic';//回跳input
        $this->assign('sid', $sid);
        $this->assign('fileback', $fileback);
        $this->display();
    }

    /**
     * 计算裁切坐标 以及裁切宽高
     * @param string $source_path
     * @param string $target_width
     * @param string $target_height
     */
    function imageCropper($source_width, $source_height, $target_width, $target_height)
    {
        $source_ratio = $source_height / $source_width;
        $target_ratio = $target_height / $target_width;
        if ($source_ratio > $target_ratio) {
            // image-to-height
            $cropped_width = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            $source_y = ($source_height - $cropped_height) / 2;
        } elseif ($source_ratio < $target_ratio) {
            //image-to-widht
            $cropped_width = $source_height / $target_ratio;
            $cropped_height = $source_height;
            $source_x = ($source_width - $cropped_width) / 2;
            $source_y = 0;
        } else {
            //image-size-ok
            $cropped_width = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }

        return ['x' => intval($source_x), 'y' => intval($source_y), 'c_with' => intval($cropped_width), 'c_height' => intval($cropped_height)];
    }

    public function test()
    {
        $thumbdir = APP_PATH . "../Public/img/p_big1.jpg";
        $img = new Image();
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
        //$img->save($thumbdir."_t.jpg");
        print_r($data);
    }

    public function selectgood()
    {
        $methods = intval($_REQUEST['methods']);//模型
        $goods = D('Goods'); // 实例化User对象

        $count = $goods->where(array('goods_class' => $methods, 'status' => 1, 'visible_platform' => 1))->count();// 查询满足要求的总记录数
        $Page = new Page($count, 50);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出

        $list = $goods->where(array('goods_class' => $methods, 'status' => 1, 'visible_platform' => 1))->order('create_time DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('data', $list);
        $this->display();
    }

}