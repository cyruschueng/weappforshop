<?php
namespace Admin\Model;
use Think\Model;
use Redis\MyRedis;

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/6/6
 * Time: 16:15
 */
class BaseModel extends Model
{
    /**
     *
     * @param $id
     * @param $data
     */
    public function edit_item($id, $data){
        $this->where(array('id'=>$id))->save($data);
    }

    /**
     * @param $id
     */
    public function get_item($id){
        return $this->find($id);
    }
}