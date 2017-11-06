<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once './VodUpload.php';

$vod = new VodApi();
$vod->Init("AKIDvzvn***", "EHLKDE4***", VodApi::USAGE_UPLOAD, "gz");

$vod->SetConcurrentNum(10);	//设置并发上传的分片数目，不调用此函数时默认并发上传数为6
$vod->SetRetryTimes(10);	//设置每个分片可重传的次数，不调用此函数时默认值为5

// $package: 上传的文件配置参数
$package = array(
    'fileName' => $argv[1],				//文件的绝对路径，包含文件名
    'dataSize' => 1024*1024,			//分片大小，单位Bytes
    'isTranscode' => 0,					//是否转码
    'isScreenshot' => 0,				//是否截图
    'isWatermark' => 0,					//是否添加水印
	'classId' => 0						//分类
);

$vod->AddFileTag("测试1");
$vod->AddFileTag("测试2");
$ret = $vod->UploadVideo($package);
if($ret !== 0)
{
	echo "upload error\n";
}

