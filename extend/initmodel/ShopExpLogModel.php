<?php

namespace initmodel;

/**
    * @AdminModel(
    *     "name"             =>"ShopExpLog",
    *     "name_underline"   =>"shop_exp_log",
    *     "table_name"       =>"shop_exp_log",
    *     "model_name"       =>"ShopExpLogModel",
    *     "remark"           =>"物流记录",
    *     "author"           =>"",
    *     "create_time"      =>"2025-08-04 15:12:06",
    *     "version"          =>"1.0",
    *     "use"              => new \initmodel\ShopExpLogModel();
    * )
    */


use think\facade\Db;
use think\Model;
use think\model\concern\SoftDelete;


class ShopExpLogModel extends Model{

	protected $name = 'shop_exp_log';//物流记录

	//软删除
	protected $hidden            = ['delete_time'];
	protected $deleteTime        = 'delete_time';
    protected $defaultSoftDelete = 0;
    use SoftDelete;
}
