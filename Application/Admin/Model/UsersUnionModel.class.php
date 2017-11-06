<?php
namespace Admin\Model;
use Redis\MyRedis;


/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/6/6
 * Time: 16:15
 */
class UsersUnionModel extends BaseModel
{
    protected $tableName = 'users_union';

    public function add_user_union($data){
        $unionid = $data['unionid'];

        if(empty($unionid)) return false;

        $data['create_time'] = time();
        return $this->add($data);
    }

    /**
     * 获取公众号用户
     * @param int $city_id
     * @return bool|mixed
     */
    public function get_user_union($unionid, $is_cache=true){
        if(empty($unionid)) return false;
        $key = 't_users_union_'.$unionid;
        $data = MyRedis::getProInstance()->new_get($key);
        if(!$data || !$is_cache){
            $data = $this->where(array('unionid'=>$unionid))->find();
            if($data){
                MyRedis::getProInstance()->new_set($key, $data);
            }
       }

        return $data;
    }

    /**
     * 获取公众号用户
     * @param int $city_id
     * @return bool|mixed
     */
    public function get_user_union_by_id($uid, $is_cache=false){
        if(empty($uid)) return false;
        $key = 't_users_union_id_'.$uid;
        $data = MyRedis::getProInstance()->new_get($key);
        if(!$data || !$is_cache){
            $data = $this->where(array('id'=>$uid))->find();
            if($data){
                MyRedis::getProInstance()->new_set($key, $data);
            }
        }

        return $data;
    }

    /**
     * 获取公众号用户
     * @param int $city_id
     * @return bool|mixed
     */
    public function get_user_union_by_uid($uid, $is_cache=false){
        if(empty($uid)) return false;
        $key = 't_users_union_uid_'.$uid;
        $data = MyRedis::getProInstance()->new_get($key);
        if(!$data || !$is_cache){
            $data = $this->where(array('uid'=>$uid))->select();
            if($data){
                MyRedis::getProInstance()->new_set($key, $data);
            }
        }

        return $data;
    }

    /**
     * 更新用户信息
     * @param $openid
     * @param $data
     */
    public function update_user_union_id($id, $data){
        $key = 't_users_union_id_'.$id;
        if(empty($id)) return false;

        $data['update_time'] = time();

        $this->where(array('id'=>$id))->save($data);
        $fid = $this->find($id);
        if($fid){
            $key2 = 't_users_union_'.$fid['unionid'];

            MyRedis::getProInstance()->delete($key2);
        }
        MyRedis::getProInstance()->delete($key);

        return true;
    }

    /**
     * 更新用户信息
     * @param $openid
     * @param $data
     */
    public function update_user_union($unionid, $data){
        $key = 't_users_union_'.$unionid;
        if(empty($unionid)) return false;

        $data['update_time'] = time();
        $this->where(array('unionid'=>$unionid))->save($data);
        MyRedis::getProInstance()->delete($key);

        $fid = $this->where(array('unionid'=>$unionid))->find();
        if($fid){
            $key2 = 't_users_union_id_'.$fid['id'];

            MyRedis::getProInstance()->delete($key2);
        }

        return true;
    }

    /**
     * @param $uid
     * @param string $fid
     */
    public function get_union_by_uid_platform($uid, $platform){
        return $this->where(array('uid'=>$uid,'platform'=>$platform))->find();
    }
}