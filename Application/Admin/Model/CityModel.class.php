<?php
namespace Admin\Model;
use Redis\MyRedis;


/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/6/6
 * Time: 16:15
 */
class CityModel extends BaseModel
{
    protected $tableName = 'city';

    /**
     * 获取公众号响起
     * @param int $city_id
     * @return bool|mixed
     */
    public function get_city($city_id=0){
        $key = 't_city_'.$city_id;
        $data = MyRedis::getProInstance()->new_get($key);
        if(!$data){
            $data = $this->where(array('city_id'=>$city_id))->find();
            if($data){
                MyRedis::getProInstance()->new_set($key, $data);
            }
        }

        return $data;
    }

    /**
     *
     */
    public function get_city_list(){
       return $this->where(['status'=>1])->order('city_id ASC')->select();
    }
    
    
    /**
     * 公众号用户列表 用于下拉列表
     * add by Qinmj 2016-11-22
     */
    function publicUserList(){
    
    	$city = D('City'); // 实例化User对象
    
    	$list = $city->order('create_time')->select();
    
    	$cityArray = array();
    	foreach ($list as $val){
    		$cityArray[$val['city_id']]=$val['city_name'];
    	}
    
    	return $cityArray;
    
    }
}