<?php

namespace app\admin\validate;

use think\Validate;


/**
    * @AdminModel(
    *     "name"             =>"ShopExpLog",
    *     "name_underline"   =>"shop_exp_log",
    *     "table_name"       =>"shop_exp_log",
    *     "validate_name"    =>"ShopExpLogValidate",
    *     "remark"           =>"物流记录",
    *     "author"           =>"",
    *     "create_time"      =>"2025-08-04 15:12:06",
    *     "version"          =>"1.0",
    *     "use"              =>   $this->validate($params, ShopExpLog);
    * )
    */

class ShopExpLogValidate extends Validate
{

protected $rule = [];




protected $message = [];




//软删除(delete_time,0)  'action'     => 'require|unique:AdminMenu,app^controller^action,delete_time,0',

//    protected $scene = [
//        'add'  => ['name', 'app', 'controller', 'action', 'parent_id'],
//        'edit' => ['name', 'app', 'controller', 'action', 'id', 'parent_id'],
//    ];


}
