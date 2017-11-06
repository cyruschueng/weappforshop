<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once './VodUpload.php';

//for REST API调用，$paraMap中需指定接口名（Action）+业务参数
//以官网视频拼接接口（ConcatVideo）为例
//ConcatVideo接口文档地址：https://www.qcloud.com/document/product/266/7821

//步骤一：初始化
$vod = new VodApi();
$vod->Init("AKIDRo1AB0i6MQIpU2ACWebMoSySkqkSCqiR", "jIfq0xkNrETWYO3Z0SBnFVZS9dx51lM2", VodApi::USAGE_VOD_REST_API_CALL, "gz");

//步骤二：对照文档"参数说明"、"请求示例"，拼接参数数组$paraMap
$paraMap = array(
	'Action' => "ConcatVideo",							//接口名
	'srcFileList.0.fileId' => "16092504232103571364",	//业务参数
	'srcFileList.1.fileId' => "16092504232103571365",	//业务参数
	'name' => "testfile",								//业务参数
	'dstType.0' => "m3u8"								//业务参数
);

//步骤三：调用REST API
$ret = $vod->GetReqSign($paraMap);
if($ret !== 0)
{
	echo "CallRestApi error\n";
}

