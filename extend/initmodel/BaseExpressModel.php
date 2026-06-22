<?php

namespace initmodel;

/**
    * @AdminModel(
    *     "name"             =>"BaseExpress",
    *     "name_underline"   =>"base_express",
    *     "table_name"       =>"base_express",
    *     "model_name"       =>"BaseExpressModel",
    *     "remark"           =>"物流管理",
    *     "author"           =>"",
    *     "create_time"      =>"2025-06-23 16:05:57",
    *     "version"          =>"1.0",
    *     "use"              => new \initmodel\BaseExpressModel();
    * )
    */


use think\facade\Db;
use think\Model;
use think\model\concern\SoftDelete;


class BaseExpressModel extends Model{

	protected $name = 'base_express';//物流管理

	//软删除
	protected $hidden            = ['delete_time'];
	protected $deleteTime        = 'delete_time';
    protected $defaultSoftDelete = 0;
    use SoftDelete;
}
