<?php

namespace initmodel;

/**
    * @AdminModel(
    *     "name"             =>"ShopGoodsBreed",
    *     "name_underline"   =>"shop_goods_breed",
    *     "table_name"       =>"shop_goods_breed",
    *     "model_name"       =>"ShopGoodsBreedModel",
    *     "remark"           =>"种场管理",
    *     "author"           =>"",
    *     "create_time"      =>"2025-06-18 18:32:39",
    *     "version"          =>"1.0",
    *     "use"              => new \initmodel\ShopGoodsBreedModel();
    * )
    */


use think\facade\Db;
use think\Model;
use think\model\concern\SoftDelete;


class ShopGoodsBreedModel extends Model{

	protected $name = 'shop_goods_breed';//种场管理

	//软删除
	protected $hidden            = ['delete_time'];
	protected $deleteTime        = 'delete_time';
    protected $defaultSoftDelete = 0;
    use SoftDelete;
}
