<?php
namespace app\common\model;

use think\Model;

class ShopVbanner extends Model
{
	protected $resultSetType = 'collection';
//	protected $autoWriteTimestamp = 'timestamp';
//	// 定义时间戳字段名
//    protected $createTime = 'create_time';
//    protected $updateTime = 'update_time';
    
	public function file()
    {
        return $this->hasOne('File','id','file_id');
    }

  
    	public function business()
    {
        return $this->hasOne('ShopGoods','id','businessid');
    }
}