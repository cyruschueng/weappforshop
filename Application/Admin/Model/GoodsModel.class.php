<?php
namespace Admin\Model;
use Redis\MyRedis;


/**
 * Created by PhpStorm.
 * User: @ShengYue
 * Date: 2016/7/4
 * Time: 16:15
 */
class GoodsModel extends BaseModel
{
    protected $tableName = 'goods';

    /**
     * 获取公众号响起
     * @param int $city_id
     * @return bool|mixed
     */
    public function get_goods($goods_id=0){
        $key = 't_goods_'.$goods_id;
        $data = MyRedis::getProInstance()->new_get($key);
        if(!$data){
            $data = $this->where(array('id'=>$goods_id))->find();
            if($data){
                MyRedis::getProInstance()->new_set($key, $data);
            }
        }

        return $data;
    }
}