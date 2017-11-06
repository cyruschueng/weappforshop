<?php
namespace Admin\Model;
use Redis\MyRedis;


/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/6/6
 * Time: 16:15
 */
class UsersModel extends BaseModel
{
    protected $tableName = 'users_all';

    public function add_user($data){
        $openid = $data['openid'];

        if(empty($openid)) return false;

        $table = get_hash_table('users', $openid);
        $data['create_time'] = time();
        return M($table)->add($data);
    }

    /**
     * 获取公众号用户
     * @param int $city_id
     * @return bool|mixed
     */
    public function get_user($openid, $is_cache=true){
        if(empty($openid)) return false;
        $key = 't_users_'.$openid;
        $table = get_hash_table('users', $openid);
        $data = MyRedis::getProInstance()->new_get($key);

        if(!$data || !$is_cache){
            $data = M($table)->where(array('openid'=>$openid))->find();
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
    public function update_user($openid, $data){
        if(empty($openid)) return false;

        $key = 't_users_'.$openid;
        $table = get_hash_table('users', $openid);
        M($table)->where(array('openid'=>$openid))->save($data);
        MyRedis::getProInstance()->delete($key);

        return true;
    }

    /**
     * 初始化用户
     * users 表记录
     * users_union 表记录
     * users_brand 表记录
     * @param $data
     */
    public function init_user($user){
        try{
            $bool = true;
            M()->startTrans();
            if( ! ($user_openid_id = D('users')->add_user($user)) ){
                $bool = false;
            }else{
                /*
               * 操作union用户
               * `id`, `unionid`, `user_open_id`, `total_integral`, `total_amount`, `uid`, `create_time`, `upadte_time`
               **/
                $union = D('UsersUnion')->get_user_union($user['unionid'], false);

                $user_union = array(
                    'unionid' => $user['unionid'],
                    'upadte_time' => time()
                );
                if($user['platform']){
                    $user_union['platform'] = $user['platform'];
                }

                if( ! $union ){
                    $user_union['user_open_id'] = $user_openid_id;
                    $user_union['openid'] = $user['openid'];
                    $user_union['city_id'] = $user['cityid'];
                    $user_union['create_time'] = time();

                    if(! $user_union_id = D('UsersUnion')->add_user_union( $user_union)){
                        $bool = false;
                    }else{
                        $user_brand = array(
                            'union_id' => $user_union_id,
                            'create_time' => time(),
                            'update_time' => time()
                        );
                        if( ! $user_brand_id = D('UsersBank')->add($user_brand)){
                            $bool = false;
                        }
                    }
                }
            }

            if($bool){
                M()->commit();
            }else{
                M()->rollback();
            }

        }catch (\Exception $e){
            print_r($e->getMessage());
        }
        return true;
    }

    /**
     * 初始化银行账户
     * @param $data
     */
    public function initunion($user){
        /*
             * 操作union用户
             * `id`, `unionid`, `user_open_id`, `total_integral`, `total_amount`, `uid`, `create_time`, `upadte_time`
             **/
        $union = D('UsersUnion')->get_user_union($user['unionid'], false);

        $user_union = array(
            'unionid' => $user['unionid'],
            'upadte_time' => time()
        );
        if($user['platform']){
            $user_union['platform'] = $user['platform'];
        }

        if( ! $union ){
            $user_union['user_open_id'] = $user['id'];
            $user_union['openid'] = $user['openid'];
            $user_union['city_id'] = $user['cityid'];
            $user_union['create_time'] = time();

            if(! $user_union_id = D('UsersUnion')->add_user_union( $user_union)){
                $bool = false;
            }else{
                $user_brand = array(
                    'union_id' => $user_union_id,
                    'create_time' => time(),
                    'update_time' => time()
                );
                if( ! $user_brand_id = D('UsersBank')->add($user_brand)){
                    $bool = false;
                }
            }
        }

    }

}