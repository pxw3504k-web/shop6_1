<?php

namespace api\wxapp\controller;

use initmodel\AssetModel;
use initmodel\MemberModel;

/**
 * @ApiController(
 *     "name"                    =>"Init",
 *     "name_underline"          =>"init",
 *     "controller_name"         =>"Init",
 *     "table_name"              =>"无",
 *     "remark"                  =>"基础接口,封装的接口"
 *     "api_url"                 =>"/api/wxapp/init/index",
 *     "author"                  =>"",
 *     "create_time"             =>"2024-04-24 17:16:22",
 *     "version"                 =>"1.0",
 *     "use"                     => new \api\wxapp\controller\InitController();
 *     "test_environment"        =>"http://shop6.ikun:9090/api/wxapp/init/index",
 *     "official_environment"    =>"https://xcxkf063.aubye.com/api/wxapp/init/index",
 * )
 */
class InitController
{
    /**
     * 本模块,用于封装常用方法,复用方法
     */


    /**
     * 给上级发放佣金
     * @param $p_user_id 上级id
     * @param $child_id  子级id
     *                   https://xcxkf063.aubye.com/api/wxapp/init/send_invitation_commission?p_user_id=1
     */
    public function send_invitation_commission($p_user_id = 0, $child_id = 0)
    {
        //邀请佣金
        $price  = cmf_config('invitation_rewards');
        $remark = "操作人[邀请奖励];操作说明[邀请好友得佣金];操作类型[佣金奖励];";//管理备注

        AssetModel::incAsset('邀请注册奖励,给上级发放佣金 [120]', [
            'operate_type'  => 'balance',//操作类型，balance|point ...
            'identity_type' => 'member',//身份类型，member| ...
            'user_id'       => $p_user_id,
            'price'         => $price,
            'order_num'     => cmf_order_sn(),
            'order_type'    => 120,
            'content'       => '邀请奖励',
            'remark'        => $remark,
            'order_id'      => 0,
            'child_id'      => $child_id
        ]);

        return "true";
    }


    /**
     * 订单完成,赠送积分
     * @param $order_num
     */
    public function sendShopOrderAccomplish($order_num)
    {
        $ShopOrderModel = new \initmodel\ShopOrderModel();//订单管理


        $map        = [];
        $map[]      = ['order_num', '=', $order_num];
        $order_info = $ShopOrderModel->where($map)->find();
        if (empty($order_info)) return false;


        //订单完成赠送积分比例(%)     订单金额*比例=实际到账积分
        $order_completion_reward_points = cmf_config('order_completion_reward_points');


        $points = $order_info['amount'] * ($order_completion_reward_points / 100);


        $remark = "操作人[下单得积分];操作说明[下单得积分];操作类型[下单得积分];";//管理备注

        AssetModel::incAsset('下单得积分,给上级发放佣金 [220]', [
            'operate_type'  => 'point',//操作类型，balance|point ...
            'identity_type' => 'member',//身份类型，member| ...
            'user_id'       => $order_info['user_id'],
            'price'         => $points,
            'order_num'     => $order_num,
            'order_type'    => 220,
            'content'       => '下单奖励',
            'remark'        => $remark,
            'order_id'      => 0,
        ]);

        return true;
    }

}