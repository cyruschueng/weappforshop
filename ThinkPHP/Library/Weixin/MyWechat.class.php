<?php
namespace Weixin;
use Redis\MyRedis;

class MyWechat extends Wechat{

    /**
	 * 设置缓存，按需重载
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		return MyRedis::getTokenInstance()->new_set($cachename, $value, $expired);
	}

	/**
	 * 获取缓存，按需重载
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		return MyRedis::getTokenInstance()->new_get($cachename);
	}

	/**
	 * 清除缓存，按需重载
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		return MyRedis::getTokenInstance()->delete($cachename);
	}
}