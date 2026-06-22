<?php

namespace app\admin\validate;

use think\Validate;


/**
 * @AdminModel(
 *     "name"             =>"MemberRecharge",
 *     "name_underline"   =>"member_recharge",
 *     "table_name"       =>"member_recharge",
 *     "validate_name"    =>"MemberRechargeValidate",
 *     "remark"           =>"充值管理",
 *     "author"           =>"",
 *     "create_time"      =>"2025-03-14 17:54:40",
 *     "version"          =>"1.0",
 *     "use"              =>   $this->validate($params, MemberRecharge);
 * )
 */
class MemberRechargeValidate extends Validate
{

    protected $rule = [
        'name'  => 'require',
        'price' => 'require',
    ];


    protected $message = [
        'name.require'  => '名称不能为空!',
        'price.require' => '价格不能为空!',
    ];


    //软删除(delete_time,0)  'action'     => 'require|unique:AdminMenu,app^controller^action,delete_time,0',

    //    protected $scene = [
    //        'add'  => ['name', 'app', 'controller', 'action', 'parent_id'],
    //        'edit' => ['name', 'app', 'controller', 'action', 'id', 'parent_id'],
    //    ];


}
