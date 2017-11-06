<?php

//文件分片信息
class PartFileInfo {
	public $_offset;
	public $_dataSize;
	public $_isSent;
	public $_retryTimes;
	public function __construct($offset, $dataSize, $isSent, $retryTimes) {
		$this->_offset = $offset;
		$this->_dataSize = $dataSize;
		$this->_isSent = $isSent;
		$this->_retryTimes = $retryTimes;
	}
}

class VodApi {
	protected $_serverHost;
	protected $_serverPort;
	protected $_defaultRegion;
	protected $_requestMethod;
	protected $_serverUri;
	protected $_secretId;
	protected $_secretKey;
	protected $_version;

	protected $_filePath;
	protected $_fileName;
	protected $_fileType;
	protected $_fileSize;
	protected $_fileSha;
	protected $_dataSize;
	protected $_isTranscode;
	protected $_isScreenshot;
	protected $_isWatermark;
	protected $_classId;
	protected $_fileTags;
	
	protected $_arrPartFiles;		//文件分片列表
	protected $_concurUploadNum;	//并发上传分片数目
	protected $_retryTimes;			//上传失败时，可重试上传的次数
	protected $_fileId = '-1';		//上传成功时，FinishUpload会设置该值
	
	protected $_usage;
	protected $_uploadServerHost;
	protected $_uploadServerUri;
	protected $_uploadReqMethod;
	
	protected $_restApiServerHost;
	protected $_restApiServerUri;
	protected $_restApiReqMethod;
	
	const USAGE_UPLOAD = 0;
	const USAGE_UGC_UPLOAD = 1;
	const USAGE_VOD_REST_API_CALL = 2;
	
	public function __construct() {
		$this->_uploadServerHost = "vod2.qcloud.com";
		$this->_uploadServerUri = "/v3/index.php";
		
		$this->_restApiServerHost = "vod.api.qcloud.com";
		$this->_restApiServerUri = "/v2/index.php";
		
		$this->_serverPort = 80;
		$this->_version = "SDK_PHP_1.2";
		$this->_uploadReqMethod = "POST";
		$this->_restApiReqMethod = "GET";
		
		$this->_concurUploadNum = 6;
		$this->_retryTimes = 5;
		$this->_fileTags = array();
	}

	public function Init($secretId, $secretKey, $usage, $region) {
		$this->_secretId = $secretId;
		$this->_secretKey = $secretKey;
		$this->_defaultRegion = $region;
		$this->_usage = $usage;
		if($this->_usage == self::USAGE_VOD_REST_API_CALL) {
			$this->_serverHost = $this->_restApiServerHost;
			$this->_serverUri = $this->_restApiServerUri;
			$this->_requestMethod = $this->_restApiReqMethod;
		} else if($this->_usage == self::USAGE_UPLOAD || $this->_usage == self::USAGE_UGC_UPLOAD) {
			$this->_serverHost = $this->_uploadServerHost;
			$this->_serverUri = $this->_uploadServerUri;
			$this->_requestMethod = $this->_uploadReqMethod;
		}
		//$this->_serverHost = "vod2.qcloud.com";
	}
	
	public function InitCommPara(&$paraMap) {
		$paraMap["Region"] = $this->_defaultRegion;
		$paraMap["SecretId"] = $this->_secretId;
		$paraMap["fileSha"] = $this->_fileSha;
		$paraMap["Timestamp"] = time();
		$paraMap["Nonce"] = rand(0, 1000000);
	}
	
	public function CallRestApi($paraMap) {
		if($this->_usage != self::USAGE_VOD_REST_API_CALL) {
			echo "CallRestApi|usage error!\n";
			return -1;
		}
		$paraMap["Region"] = $this->_defaultRegion;
		$paraMap["SecretId"] = $this->_secretId;
		$paraMap["Timestamp"] = time();
		$paraMap["Nonce"] = rand(0, 1000000);
		$this->makeRequest($paraMap["Action"], $paraMap, $request);
		if(!($response = $this->sendRequest($request, ""))) {
			echo "CallRestApi|sendRequest error\n";
			echo "CallRestApi|recv:" . json_encode($response) . "\n";
			return -1;
		} else {
			echo "CallRestApi|recv:" . json_encode($response) . "\n";
			return 0;
		}
		
	}
	
	/**
	 * SetConcurrentNum
	 * 设置并发上传分片数目
	 * @param int $concurNum 并发上传分片数目
	 */
	public function SetConcurrentNum($concurNum) {
		$this->_concurUploadNum = (int)$concurNum;
	}

	/**
	 * SetRetryTimes 
	 * 设置并发上传分片数目
	 * @param int $retryTimes 每个分片可重传的次数
	 */
	public function SetRetryTimes($retryTimes) {
		$this->_retryTimes = (int)$retryTimes;
	}

	public function AddFileTag($fileTag) {
		$this->_fileTags[] = $fileTag;
	}
	
	public function getFileId() {
		return $this->_fileId;
	}
	
	/**
	 * CheckParams
	 * 校验参数合法性
	 * @param array $params API参数
	 * @return	bool 校验通过返回true，否则返回false
	 */
	public function CheckParams($params) {
		//设置API请求参数
		if(!isset($params['fileName'])) {
			echo "API fileName参数为空，为必填选项\n";
			return false;
		}
		if(empty($params['fileName'])) {
			echo "fileName为空，请检测参数\n";
			return false;
		}
		if(!is_file($params['fileName'])) {
			echo $params['fileName']."  不存在，请检测\n";
			return false;
		}
		return true;
	}
	
	/**
	 * UploadVideo 
	 * 上传文件
	 * @param array $package API参数
	 * @return 0表示上传成功，负数表示上传失败
	 */
	public function UploadVideo($package) {
		if($this->CheckParams($package) == false)
			return -1;
		
		if($this->InitUpload($package) == false) {
			echo "\n[UploadVodFile] InitUpload failed!\n";
			return -2;
		}
		if($this->UploadPart() == false) {
			echo "\n[UploadVodFile] upload request failed!\n";
			return -3;
		}
		if($this->FinishUpload() == false) {
			echo "\n[UploadVodFile] finish upload request failed!\n";
			return -4;
		}
		return 0;
		
	}

	/**
	 * GeneratePartInfo
	 * 生成文件分片信息
	 * @param int $fileSize 文件大小，单位Bytes
	 * @param int $dataSize 每个分片的大小，单位Bytes
	 * @param int $retryTimes 每个分片的可重试上传次数
	 * @return 无返回
	 */
	public function GeneratePartInfo($fileSize, $dataSize, $retryTimes) {
		$partNum = floor($fileSize / $dataSize);
		for($i = 0; $i < $partNum; ++$i) {
			$offset  = $dataSize * $i;
			$partFileInfo = new PartFileInfo($offset, $dataSize, 0, $retryTimes);
			$this->_arrPartFiles[] = $partFileInfo;
		}
		if($partNum * $dataSize < $fileSize) {
			$offset = $partNum * $dataSize;
			$dataSize = $fileSize - $partNum * $dataSize;
			$partFileInfo = new PartFileInfo($offset, $dataSize, 0, $retryTimes);
			$this->_arrPartFiles[] = $partFileInfo;
		}
	}
	
	/**
     * InitUpload
     * 文件启动上传
     * @param array $params API请求参数
     * @return bool 成功返回true，失败返回false
     */
	public function InitUpload($params) {
		echo "\n===InitUpload begin===\n";
		$name = 'InitUpload';
		
		$retry = $this->_retryTimes;
		while(true) {
			$this->_filePath = $params['fileName'];
			$this->_fileSha = sha1_file($params['fileName']);
			$this->_fileSize = filesize($params['fileName']);
			//防止中文文件名中有空格
			$len_dir = strlen(dirname($params['fileName']));
			if(dirname($params['fileName']) == '.')
				$this->_fileName = $params['fileName'];
			else
				$this->_fileName = substr($params['fileName'], $len_dir + 1);
			
			//不包含路径且无后缀的文件名
			$pos_dot = (int)strrpos($this->_fileName, '.');

			$this->_fileType = substr($this->_fileName, $pos_dot + 1);
			$fileName_NoSurfix = substr($this->_fileName, 0, $pos_dot);
			
			//分片大小 未设置选择512KB
			$this->_dataSize = isset($params['dataSize']) ? $params['dataSize'] : (1024 * 512);
			$Nonce = rand(0, 1000000);
			$timestamp = time();
			$data = "";
			//封装API参数
			$this->_isTranscode = isset($params['isTranscode']) ? $params['isTranscode'] : 0;
			$this->_isScreenshot = isset($params['isScreenshot']) ? $params['isScreenshot'] : 0;
			$this->_isWatermark = isset($params['isWatermark']) ? $params['isWatermark'] : 0;
			$this->_classId = isset($params['classId']) ? $params['classId'] : 0;
			$arguments = array(
				'Action' => $name,
				'Nonce' => $Nonce,
				'Region' => $this->_defaultRegion,
				'SecretId' => $this->_secretId,
				'Timestamp' => $timestamp,
				'classId' => $this->_classId,
				'contentLen' => strlen($data),
				'dataSize' => $this->_dataSize,
				'fileName' => $fileName_NoSurfix,
				'fileSha' => $this->_fileSha,
				'fileSize' => $this->_fileSize,
				'fileType' => $this->_fileType,
				'isTranscode' => $this->_isTranscode,
				'isScreenshot' => $this->_isScreenshot,
				'isWatermark' => $this->_isWatermark,
				'name' => $this->_fileName
			);
			$sizeFileTags = count($this->_fileTags);
			for($i = 0; $i < $sizeFileTags; ++$i) {
				$arguments['tag.'.(string)($i+1)] = $this->_fileTags[$i];
			}
			$send_retry_times = 0;
			$this->makeRequest($name, $arguments, $request);
			while(!($response = $this->sendRequest($request, $data))) {
				if($send_retry_times > 3) {
					echo "[InitUpload] send retry times reach MAX 3,failed!\n";
					return false;
				}	
				++$send_retry_times;
				echo "[InitUpload] send retry " . $retry_times. " times\n";
			}
			echo "[InitUpload] recv:" . json_encode($response) . "\n";
	
			if($response['code'] == 1) {//文件部分分片已经上传到服务器
				echo "[InitUpload] file part existed!\n";
				$this->GeneratePartInfo($this->_fileSize, $this->_dataSize, $this->_retryTimes);
				foreach($response['listParts'] as $val) {	
					$index = floor($val["offset"] / $this->_dataSize);
					$this->_arrPartFiles[$index]->_isSent = 1;
				}
				return true;
			} else if($response['code'] == 0) {
				$this->GeneratePartInfo($this->_fileSize, $this->_dataSize, $this->_retryTimes);
				return true;
			} else if($response['code'] == 2) {//文件已经存在于服务器
				echo "[InitUpload] file existed!\n";				
				$this->_fileId = $response['fileId'];
				return true;
			} else {
				if($retry-- && $response['canRetry'] == 1)
					continue;
				return false;
			}
		}
	}
	
	/**
     * UploadPart
     * 文件分片上传(并发)
     * @return bool 成功返回true，失败返回false
     */
	public function UploadPart() {
		echo "\n===UploadPart begin===\n";
		$name = 'UploadPart';
		
		$partNum = count($this->_arrPartFiles);
		$nextIndex = 0;
		$fp = fopen($this->_filePath, "rb");
		while(true) {
			$index = $nextIndex;
			$count = 0;
			$arr_request = array();
			$arr_data = array();
			$arr_index = array();
			while(true) {
				if($index > $partNum - 1 || $count > $this->_concurUploadNum - 1) {
					break;
				}
				if($this->_arrPartFiles[$i]->_isSent === 1) {
					++$index;
					continue;
				}
				fseek($fp, $this->_arrPartFiles[$index]->_offset);
				$data = fread($fp, $this->_arrPartFiles[$index]->_dataSize);
				$arr_data[] = $data;
				
				$Nonce = rand(0, 1000000);
				$timestamp = time();
				$dataMd5= md5($data);
				$arguments = array(
					'Action' => $name,
					'Nonce' => $Nonce,
					'Region' => $this->_defaultRegion,
					'SecretId' => $this->_secretId,
					'Timestamp' => $timestamp,
					'contentLen' => $this->_arrPartFiles[$index]->_dataSize,
					'dataMd5' => $dataMd5,
					'dataSize' => $this->_arrPartFiles[$index]->_dataSize,
					'fileSha' => $this->_fileSha,
					'isTranscode' => $this->_isTranscode,
					'isScreenshot' => $this->_isScreenshot,
					'isWatermark' => $this->_isWatermark,
					'name' => $this->_fileName,
					'offset' => $this->_arrPartFiles[$index]->_offset
				);
				$this->makeRequest($name, $arguments, $request);
				$arr_request[] = $request;
				$arr_index[] = $index;
				++$index;
				++$count;
			}
			
			if($this->httpReqMulti($arr_request, $arr_data, $arr_index) === false) {
				echo "[UploadPart] httpReqMulti return false\n";
				var_dump($arr_request);
				return false;
			}
			$nextIndex = $index;
			if($nextIndex >= $partNum)
				break;
		}
		fclose($fp);
		return true;
	}
	
	/**
	 * httpReqMulti
	 * 向上传服务器发送多个请求(并发)
	 * @param array $arr_request 请求的数组
	 * @param array $arr_data 请求的POST数据
	 * @param array $arr_index 索引的数组
	 * @return bool 成功返回true，失败返回false
	 */
	public function httpReqMulti($arr_request, $arr_data, $arr_index) {
		while(true) {
			$mh = curl_multi_init();
			$ch_list = array();
			$reqNum = count($arr_data);
			$arr_retry_index = array();
			for($i = 0; $i < $reqNum; ++ $i) {
				$request = $arr_request[$i];
				$data = $arr_data[$i];
				$header = array(
					"POST {$request['uri']}?{$request['query']} HTTP/1/1",
					"HOST:{$request['host']}",
					"Content-Length:".$request['contentLen'],
					"Content-type:application/octet-stream",
					"Accept:*/*",
					"User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
						
				);

				$ch = curl_init($request['url']);
				curl_setopt($ch,CURLOPT_POST,1);
				curl_setopt($ch,CURLOPT_HEADER,0);
				curl_setopt($ch,CURLOPT_FRESH_CONNECT,1);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch,CURLOPT_FORBID_REUSE,1);
				curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
					
				if (false !== strpos($request['url'], "https")) {
					// 证书
					// curl_setopt($ch,CURLOPT_CAINFO,"ca.crt");
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,  false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
				}
				curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
				//curl_setopt($ch, CURLOPT_TIMEOUT,100000);//超时时间
				curl_multi_add_handle($mh, $ch);
				$ch_list[] = $ch;
			}
			
			do {
				$mret = curl_multi_exec($mh, $active);
			}while($mret == CURLM_CALL_MULTI_PERFORM);

			while($active and $mret == CURLM_OK) {
				if(curl_multi_select($mh) === -1) {
					usleep(100);
				}
				do {
					$mret = curl_multi_exec($mh, $active);
				}while($mret == CURLM_CALL_MULTI_PERFORM);
			}
			
			
			for($i = 0; $i < $reqNum; $i++) {
				$ret = curl_multi_getcontent($ch_list[$i]);
				$result = json_decode($ret, true);
				if($result['code'] < 0) {
					if($result['canRetry'] == 1 && $this->_arrPartFiles[$arr_index[$i]]->_retryTimes-->0)
						$arr_retry_index[] = $i;
					else
						return false;
				}
				curl_multi_remove_handle($mh, $ch_list[$i]);
			}
			curl_multi_close($mh);
			if(empty($arr_retry_index))
				break;
			
			foreach($arr_retry_index as $k => $v) {
				array_splice($arr_request, $v, 1);
				array_splice($arr_data, $v, 1);
				array_splice($arr_index, $v, 1);
			}	
		}
		return true;
	}
	
	 
	/**
     * FinishUpload
     * 完成上传协议
     * @return bool 成功返回true，失败返回false
     */
	public function FinishUpload() {
		echo "\n===FinishUpload begin===\n";
		$name = 'FinishUpload';
		$retry = $this->_retryTimes;
		while(true) {
			$Nonce = rand(0,1000000);
			$timestamp = time();
			$data = "";
			//封装API参数，arguments参数只支持GET
			$arguments = array(
				'Action' => $name,
				'Nonce' => $Nonce,
				'Region' => $this->_defaultRegion,
				'SecretId' => $this->_secretId,
				'Timestamp' => $timestamp,
				'contentLen' => strlen($data),
				'fileSha' => $this->_fileSha,
			);
			$send_retry_times = 0;
			
			$this->makeRequest($name, $arguments, $request);
			while(!($response = $this->sendRequest($request, $data))) {
				if($send_retry_times > 3) {
					//$this->setError("", 'request falied!');
					echo "[FinishUpload] send retry times reach MAX 3,failed!\n";
					return false;
				}
					
				++$send_retry_times;
				echo "[FinishUpload] send retry ".$retry_times." times\n";
			}
			
			echo "[FinishUpload] recv:" . json_encode($response) . "\n";
			if($response['code'] == 0)
				$this->_fileId = $response['fileId'];
			else if($response['code'] < 0) {
				if($retry-- && $response['canRetry'] == 1)
					continue;
				echo "[FinishUpload]response error,message: " . $response['message'] . "\n";
				return false;
			}
			return true;
		}
	}
	
	/**
     * GetReqSign
     * 生成请求的签名字符串
     * @param array 	$paraMap  请求参数
     * @return
     */
	public function GetReqSign($paraMap) {
		if($this->_usage == self::USAGE_UPLOAD)
			$this->InitCommPara($paraMap);
		$paramStr = "";
		ksort($paraMap);
		$i = 0;
		foreach ($paraMap as $key => $value) {
			if ($key == 'Signature')
				continue;
			// 排除上传文件的参数
			if ($this->_requestMethod == 'POST' && substr($value, 0, 1) == '@')
				continue;
			// 把 参数中的 _ 替换成 .
			if (strpos($key, '_'))
				$key = str_replace('_', '.', $key);
			if ($i == 0)
				$paramStr .= '?';
			else
				$paramStr .= '&';
			$paramStr .= $key . '=' . $value;
			++$i;
		}
		$plainText = $this->_requestMethod . $this->_serverHost . $this->_serverUri . $paramStr;
		$cipherText = base64_encode(hash_hmac('sha1', $plainText, $this->_secretKey, true));
		echo "GetReqSign|plainText:" . $plainText . "\n";
		echo "GetReqSign|cipherText:" . $cipherText . "\n";
		return $cipherText;
	}
	
	/**
	 * makeRequest
	 * 生成请求结构
	 * @param string	$name 		协议命令字
	 * @param array 	$arguments 	API参数数组
	 * @param array 	&$request 	待返回的请求结构
	 * @return 无返回
	 */
	protected function makeRequest($name, $arguments, &$request) {
		$action = ucfirst($name);
		$params = $arguments;
		$params['Action'] = $action;
		$params['RequestClient'] = $this->_version;
		ksort($params);
		$params['Signature'] = $this->GetReqSign($params);
		
		$request['uri'] = $this->_serverUri;
		$request['host'] = $this->_serverHost;
		$request['query'] = http_build_query($params);
		$request['query'] = str_replace('+','%20',$request['query']);
		$url = $request['host'] . $request['uri'];

		if($this->_serverPort != '' && $this->_serverPort != 80)
			$url = $request['host'] . ":" . $this->_serverPort . $request['uri'];

		$url = $url.'?'.$request['query'];
		$url = 'https://'.$url;

		$request['url'] = $url;
		echo "makeRequest|request url:" . $request['url'] . "\n";
		$request['contentLen'] = $arguments['contentLen'];
	}
	
	/**
     	* sendRequest
     	* @param array  $request    http请求参数
     	* @param string $data       发送的数据
     	* @return
     	*/
	protected function sendRequest($request, $data) {  
		$url = $request['url'];
		$ch = curl_init($url);
		if($this->_requestMethod == "GET") {
			$MethodLine = "GET {$request['uri']}?{$request['query']} HTTP/1/1";
		} else if($this->_requestMethod == "POST") {
			curl_setopt($ch, CURLOPT_POST, 1);
			$MethodLine = "POST {$request['uri']}?{$request['query']} HTTP/1/1";
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		
		$header = array(
			$MethodLine,
			"HOST:{$request['host']}",
			"Content-Length:".$request['contentLen'],
			"Content-type:application/octet-stream",
			"Accept:*/*",
			"User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
				
		);
		
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		
		// 证书
		// curl_setopt($ch,CURLOPT_CAINFO,"ca.crt");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		
		
		$response = curl_exec($ch);

		curl_close($ch);

		$result = json_decode($response, true);
		if (!$result) {
			echo "[sendRequest] 请求发送失败，请检查URL:\n";
			echo $url . "\n";
			return $response;
		}
		return $result;
	}
}
