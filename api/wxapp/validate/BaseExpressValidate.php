<?php

namespace api\wxapp\validate;

use think\Validate;


/**
    * @AdminModel(
    *     "name"             =>"BaseExpress",
    *     "name_underline"   =>"base_express",
    *     "table_name"       =>"base_express",
    *     "validate_name"    =>"BaseExpressValidate",
    *     "remark"           =>"物流管理",
    *     "author"           =>"",
    *     "create_time"      =>"2025-06-23 16:05:57",
    *     "version"          =>"1.0",
    *     "use"              =>   $this->validate($params, BaseExpress);
    * )
    */

class BaseExpressValidate extends Validate
{

protected $rule = ['name'=>'require',
'price'=>'require',
];




protected $message = ['name.require'=>'名称不能为空!',
'price.require'=>'单价不能为空!',
];





//软删除(delete_time,0)  'action'     => 'require|unique:AdminMenu,app^controller^action,delete_time,0',


//    protected $scene = [
//        'add'  => ['name', 'app', 'controller', 'action', 'parent_id'],
//        'edit' => ['name', 'app', 'controller', 'action', 'id', 'parent_id'],
//    ];


}
