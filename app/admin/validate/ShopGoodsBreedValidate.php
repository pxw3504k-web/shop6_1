<?php

namespace app\admin\validate;

use think\Validate;


/**
    * @AdminModel(
    *     "name"             =>"ShopGoodsBreed",
    *     "name_underline"   =>"shop_goods_breed",
    *     "table_name"       =>"shop_goods_breed",
    *     "validate_name"    =>"ShopGoodsBreedValidate",
    *     "remark"           =>"种场管理",
    *     "author"           =>"",
    *     "create_time"      =>"2025-06-18 18:32:39",
    *     "version"          =>"1.0",
    *     "use"              =>   $this->validate($params, ShopGoodsBreed);
    * )
    */

class ShopGoodsBreedValidate extends Validate
{

protected $rule = ['name'=>'require',
];




protected $message = ['name.require'=>'名称不能为空!',
];




//软删除(delete_time,0)  'action'     => 'require|unique:AdminMenu,app^controller^action,delete_time,0',

//    protected $scene = [
//        'add'  => ['name', 'app', 'controller', 'action', 'parent_id'],
//        'edit' => ['name', 'app', 'controller', 'action', 'id', 'parent_id'],
//    ];


}
