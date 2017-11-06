<?php
namespace Redis;

class MyRedis extends \Redis{

	private static $adinstance = NULL;
	private static $proinstance = NULL;
	private static $tokeninstance = NULL;
	private static $gameinstance = NULL;

	/**
	 * 架构函数
	 * @param array  广告缓存
	 * @access public
	 */
	public static  function getAdInstance() {
		if (empty(self::$adinstance)) {
			$redis = new self;
			$arr = C("AD_REDIS");
			if(empty($arr)){
				die('NO DEFINE REDIS OPTION');
			}
			$redis->connect($arr[0], $arr[1], $arr[2]);
			if (isset($arr[3]) && !empty($arr[3])) {
				$redis->auth($arr[3]);
			}
			self::$adinstance = $redis;
		}
		return self::$adinstance;
	}

	/**
	 * 架构函数
	 * @param array  产品缓存
	 * @access public
	 */
	public static  function getProInstance() {
		if (empty(self::$proinstance)) {
			$redis = new self();
			$arr = C("PRO_REDIS");
			if(empty($arr)){
				die('NO DEFINE REDIS OPTION');
			}
			$redis->connect($arr[0], $arr[1], $arr[2]);
			if (isset($arr[3]) && !empty($arr[3])) {
				$redis->auth($arr[3]);
			}
			self::$proinstance = $redis;
		}
		return self::$proinstance;
	}

	/**
	 * 架构函数
	 * @param array  token缓存 微信相关
	 * @access public
	 */
	public static  function getTokenInstance() {
		if (empty(self::$tokeninstance)) {
			$redis = new self();
			$arr = C("TOKEN_REDIS");
			if(empty($arr)){
				die('NO DEFINE REDIS OPTION');
			}
			$redis->connect($arr[0], $arr[1], $arr[2]);
			if (isset($arr[3]) && !empty($arr[3])) {
				$redis->auth($arr[3]);
			}
			self::$tokeninstance = $redis;
		}
		return self::$tokeninstance;
	}

            /**
	 * 架构函数
	 * @param array  game
	 * @access public
	 */
	public static  function getGameInstance() {
		if (empty(self::$gameinstance)) {
			$redis = new self();
			$arr = C("TOKEN_GAME");
			if(empty($arr)){
				die('NO DEFINE REDIS OPTION');
			}
			$redis->connect($arr[0], $arr[1], $arr[2]);
			if (isset($arr[3]) && !empty($arr[3])) {
				$redis->auth($arr[3]);
			}
			self::$gameinstance = $redis;
		}
		return self::$gameinstance;
	}
	/**
	 * 重写redis
	 * @param $key
	 * @param $value
	 * @param int $expire
	 * @return mixed
	 */
	function new_set($key, $value,$expire=0){
		$value = serialize($value);
		$ret = $this->set($key, $value);
		if($expire>0){
			$this->expire($key,$expire);
		}
		return $ret;
	}

	/**
	 * 重写
	 * @param $name
	 * @return bool
	 */
	function new_get($name,$default=false){
		if($this->exists($name)){
			return unserialize($this->get($name));
		}else{
			return $default;
		}
	}
}