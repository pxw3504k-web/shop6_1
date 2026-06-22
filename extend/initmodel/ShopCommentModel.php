<?php

namespace initmodel;

/**
    * @AdminModel(
    *     "name"             =>"ShopComment",
    *     "name_underline"   =>"shop_comment",
    *     "table_name"       =>"shop_comment",
    *     "model_name"       =>"ShopCommentModel",
    *     "remark"           =>"商品评价",
    *     "author"           =>"",
    *     "create_time"      =>"2025-06-05 17:18:01",
    *     "version"          =>"1.0",
    *     "use"              => new \initmodel\ShopCommentModel();
    * )
    */


use think\facade\Db;
use think\Model;
use think\model\concern\SoftDelete;


class ShopCommentModel extends Model{

	protected $name = 'shop_comment';//商品评价

	//软删除
	protected $hidden            = ['delete_time'];
	protected $deleteTime        = 'delete_time';
    protected $defaultSoftDelete = 0;
    use SoftDelete;
}
