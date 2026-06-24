<?php

namespace app\admin\controller;


/**
 * @adminMenuRoot(
 *     "name"                =>"ShopOrder",
 *     "controller_name"     =>"ShopOrder",
 *     "table_name"          =>"shop_order",
 *     "action"              =>"default",
 *     "parent"              =>"",
 *     "display"             => true,
 *     "order"               => 10000,
 *     "icon"                =>"none",
 *     "remark"              =>"订单管理",
 *     "author"              =>"",
 *     "create_time"         =>"2023-09-29 09:57:21",
 *     "version"             =>"1.0",
 *     "use"                 => new \app\admin\controller\ShopOrderController();
 * )
 */


use api\wxapp\controller\WxBaseController;
use initmodel\AssetModel;
use plugins\weipay\lib\PayController;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Log;
use cmf\controller\AdminBaseController;


class ShopOrderController extends AdminBaseController
{
    //    public function initialize()
    //    {
    //        parent::initialize();
    //    }


    //检测是否有新订单
    public function order_notification()
    {
        $result = Cache::get('order_notification_admin');
        if (empty($result)) $this->error('无通知');
        Cache::delete('order_notification_admin');
        $this->success('有通知');
    }


    /**
     * 展示
     * @adminMenu(
     *     'name'   => 'ShopOrder',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '订单管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $params         = $this->request->param();
        $ShopOrderInit  = new \init\ShopOrderInit();//订单管理
        $ShopOrderModel = new \initmodel\ShopOrderModel();//订单管理


        $where = [];
        if ($params['keyword']) $where[] = ['phone|username|order_num', 'like', "%{$params['keyword']}%"];
        if ($params['order_num']) $where[] = ['order_num|repair_order_num', 'like', "%{$params['order_num']}%"];
        if ($params['goods_name']) $where[] = ['goods_name', 'like', "%{$params['goods_name']}%"];
        if ($params['user_id']) $where[] = ['user_id', '=', $params['user_id']];


        if ($params['order_date']) {
            $order_date_arr = explode(' - ', $params['order_date']);
            $where[]        = $this->getBetweenTime($order_date_arr[0], $order_date_arr[1]);
        }


        //状态筛选
        $status_where = [];
        if ($params['status']) $status_where[] = ['status', 'in', $ShopOrderInit->admin_status_where[$params['status']]];
        //if (empty($params['status'])) $status_where[] = ['status', 'in', [2, 3]];


        //数据类型
        $params['InterfaceType'] = 'admin';//身份类型,后台


        //导出数据
        if ($params["is_export"]) $this->export_excel(array_merge($where, $status_where), $params);
        $result = $ShopOrderInit->get_list_paginate(array_merge($where, $status_where), $params);


        $this->assign("list", $result);
        $this->assign('pagination', $result->render());//单独提取分页出来
        $this->assign("page", $result->currentPage());

        //全部数量
        $this->assign("total", $ShopOrderModel->where($where)->count());//总数量


        //数据统计
        $status_arr = $ShopOrderInit->status_list;
        $count      = [];
        foreach ($status_arr as $key => $status) {
            $map                    = [];
            $map[]                  = ['status', 'in', $ShopOrderInit->admin_status_where[$key]];
            $map                    = array_merge($map, $where);
            $count[$key]['count']   = $ShopOrderModel->where($map)->count();
            $count[$key]['key']     = $key;
            $count[$key]['name']    = $status;
            $count[$key]['is_ture'] = false;
            if ($params['status'] == $key) $count[$key]['is_ture'] = true;
        }


        $this->assign('count', $count);


        return $this->fetch();
    }


    //编辑详情
    public function edit()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where   = [];
        $where[] = ['id', '=', $params['id']];
        $result  = $ShopOrderInit->get_find($where);


        if (empty($result)) $this->error("暂无数据");

        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        return $this->fetch();
    }


    public function empty_video()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where   = [];
        $where[] = ['id', '=', $params['id']];
        $result  = $ShopOrderInit->get_find($where);


        if (empty($result)) $this->error("暂无数据");

        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        return $this->fetch();
    }


    public function dress_video()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where   = [];
        $where[] = ['id', '=', $params['id']];
        $result  = $ShopOrderInit->get_find($where);


        if (empty($result)) $this->error("暂无数据");

        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        return $this->fetch();
    }


    public function weigh_video()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where   = [];
        $where[] = ['id', '=', $params['id']];
        $result  = $ShopOrderInit->get_find($where);


        if (empty($result)) $this->error("暂无数据");

        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        return $this->fetch();
    }


    public function video_edit_post()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理

        //装猪视频
        if ($params['status'] == 31) {
            if (empty($params['dress_video'])) $this->error('请上传视频');
            $params['dress_time'] = time();
        }

        //过磅视频
        if ($params['status'] == 32) {
            if (empty($params['weigh_video'])) $this->error('请上传视频');
            $params['weigh_time'] = time();
        }

        //空车过磅视频
        if ($params['status'] == 33) {
            if (empty($params['empty_video'])) $this->error('请上传视频');
            $params['empty_time'] = time();
        }


        $result = $ShopOrderInit->admin_edit_post($params);
        if (empty($result)) $this->error('失败请重试');


        $this->success("保存成功", 'index' . $this->params_url);
    }


    //提交编辑
    public function edit_post()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $result = $ShopOrderInit->admin_edit_post($params);
        if (empty($result)) $this->error('失败请重试');


        $this->success("保存成功", 'index' . $this->params_url);
    }


    //修改备注
    public function setRemark()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理

        $result = $ShopOrderInit->admin_edit_post($params);
        if (empty($result)) $this->error('失败请重试');

        $this->success("保存成功", 'index' . $this->params_url);
    }


    //添加
    public function add()
    {
        return $this->fetch();
    }


    //添加提交
    public function add_post()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $result = $ShopOrderInit->admin_edit_post($params);
        if (empty($result)) $this->error('失败请重试');


        $this->success("保存成功", 'index' . $this->params_url);
    }


    //查看详情
    public function details()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where   = [];
        $where[] = ['id', '=', $params['id']];
        $result  = $ShopOrderInit->get_find($where);


        if (empty($result)) $this->error("暂无数据");


        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }


        return $this->fetch();
    }


    //设置过磅
    public function weigh_amount()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where   = [];
        $where[] = ['id', '=', $params['id']];
        $result  = $ShopOrderInit->get_find($where);


        if (empty($result)) $this->error("暂无数据");


        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }


        return $this->fetch();
    }

    //提交过磅信息
    public function weigh_amount_post()
    {
        $params               = $this->request->param();
        $ShopOrderInit        = new \init\ShopOrderInit();//订单管理
        $ShopOrderDetailModel = new \initmodel\ShopOrderDetailModel();//订单详情  (ps:InitModel)
        $ShopOrderModel       = new \initmodel\ShopOrderModel(); //订单管理  (ps:InitModel)
        $WxBaseController     = new WxBaseController();//微信基础类


        //订单信息
        $order_info = $ShopOrderModel->where('id', '=', $params['id'])->find();
        if (empty($order_info)) $this->error('订单信息错误');


        //编辑必须传id
        $total_weigh        = 0;//总过磅
        $total_weigh_amount = 0;//总过磅金额

        foreach ($params['weigh'] as $key => $value) {
            $detail_info = $ShopOrderDetailModel->where('id', '=', $key)->find();

            $update['weigh']         = $value;
            $update['weigh_amount']  = $params['weigh_amount'][$key];
            $update['refund_amount'] = round($detail_info['refund_amount'] + ($detail_info['total_amount'] - $update['weigh_amount']), 2);
            $update['update_time']   = time();

            //更新订单详情信息
            $ShopOrderDetailModel->where('id', '=', $key)->strict(false)->update($update);


            $total_weigh        += $update['weigh'];//总过磅
            $total_weigh_amount += $update['weigh_amount'];//总过磅金额
        }


        //更新订单信息
        $order_update['id']             = $params['id'];
        $order_update['freight_amount'] = $params['freight_amount'];
        $order_update['weigh']          = $total_weigh;
        $order_update['weigh_amount']   = $total_weigh_amount;
        $order_update['refund_amount']  = round($order_info['goods_amount'] - $total_weigh_amount ?? 0, 2);
        $order_update['update_time']    = time();
        $order_update['status']         = 2; //待发货
        if ($order_update['refund_amount'] < 0) $order_update['refund_amount'] = 0;

        //需要补差价
        if ($total_weigh_amount > $order_info['goods_amount']) {
            $order_update['repair_amount']    = round($total_weigh_amount - $order_info['goods_amount'], 2);
            $order_update['repair_order_num'] = $this->get_only_num('shop_order', 'repair_order_num');
            $order_update['status']           = 50;//需要支付差价
        }


        //退钱
        if ($order_update['refund_amount'] > 0) {
            //退款金额
            $refund_amount = $order_update['refund_amount'];

            //退款 && 微信退款
            if ($order_info['pay_type'] == 1) {
                $refund_result = $WxBaseController->wx_refund($order_info['pay_num'], $refund_amount, $order_info['amount']);//后台  退款操作,退款全部金额 &&微信
                if ($refund_result['code'] == 0) $this->error($refund_result['msg']);
            }
            //余额退款
            if ($order_info['pay_type'] == 2) {
                $admin_id_and_name = cmf_get_current_admin_id() . '-' . session('name');//管理员信息
                $remark            = "操作人[{$admin_id_and_name}];操作说明[榜单上传完毕退差价:{$order_info['order_num']};金额:{$order_info['balance']}];操作类型[退差价];";//管理备注
                AssetModel::incAsset('上传榜单,退差价增加 [130]', [
                    'operate_type'  => 'balance',//操作类型，balance|point ...
                    'identity_type' => 'member',//身份类型，member| ...
                    'user_id'       => $order_info['user_id'],
                    'price'         => $refund_amount,
                    'order_num'     => $order_info['order_num'],
                    'order_type'    => 130,
                    'content'       => '订单退差价',
                    'remark'        => $remark,
                    'order_id'      => $order_info['id'],
                ]);
            }
        }
        $result = $ShopOrderModel->where('id', '=', $params['id'])->strict(false)->update($order_update);
        if (empty($result)) $this->error('失败请重试');


        $this->success("保存成功", 'index' . $this->params_url);
    }


    //退款理由
    public function reason()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where   = [];
        $where[] = ['id', '=', $params['id']];
        $result  = $ShopOrderInit->get_find($where);


        if (empty($result)) $this->error("暂无数据");

        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        return $this->fetch();
    }


    //发货
    public function send()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理
        $where         = [];
        $where[]       = ['id', '=', $params['id']];
        $result        = $ShopOrderInit->get_find($where);
        if (empty($result)) $this->error("暂无数据");
        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        //快递公司
        $express = Db::name('base_express')->select();
        $this->assign('express', $express);

        return $this->fetch();
    }


    //发货提交
    public function send_post()
    {
        $ShopOrderInit   = new \init\ShopOrderInit();//订单管理
        $ShopExpLogModel = new \initmodel\ShopExpLogModel(); //物流记录   (ps:InitModel)

        //订单发货后自动完成时间 单位/天
        $order_auto_completion_time = cmf_config('order_auto_completion_time');

        $params     = $this->request->param();
        $order_info = $ShopOrderInit->get_find($params['id']);
        if (empty($order_info)) $this->error('订单信息错误');

        //if (empty($params['exp_num'])) $this->error('快递单号不能为空');


        //更改订单信息
        $params['status']               = 4;
        $params['send_time']            = time();
        $params['auto_accomplish_time'] = time() + $order_auto_completion_time * 86400;//自动完成时间
        $ShopOrderInit->edit_post($params);


        $ShopExpLogModel->strict(false)->insert([
            'order_num'   => $order_info['order_num'],
            'images'      => $this->setParams($params['exp_images']),
            'exp_name'    => $order_info['exp_name'],
            'remark'      => $params['exp_remark'],
            'exp_id'      => $params['exp_id'],
            'exp_num'     => $params['exp_num'],
            'exp_phone'   => $params['exp_phone'],
            'create_time' => time(),
        ]);


        $this->success('发货成功');
    }


    //删除
    public function delete()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where = [];
        if ($params['id']) $where[] = ['id', '=', $params['id']];
        if (empty($params['id'])) {
            $ids     = $this->request->param('ids/a');
            $where[] = ['id', 'in', $ids];
        }


        $result = $ShopOrderInit->delete_post($where);
        if (empty($result)) $this->error('失败请重试');


        $this->success("删除成功", 'index' . $this->params_url);
    }


    //修改状态
    public function status_post()
    {
        $params        = $this->request->param();
        $status        = $this->request->param('status');
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $id = $this->request->param('id/a');


        if (empty($id)) $id = $this->request->param('ids/a');
        if (empty($id) || $status == '') $this->error('参数错误');


        $result = $ShopOrderInit->status_post($id, $status);
        if (empty($result)) $this->error('失败请重试');


        $this->success("保存成功", 'index' . $this->params_url);
    }


    //退款拒绝
    public function refuse()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理


        $where   = [];
        $where[] = ['id', '=', $params['id']];
        $result  = $ShopOrderInit->get_find($where);


        if (empty($result)) $this->error("暂无数据");

        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        return $this->fetch();
    }


    //退款操作,退款全部金额
    public function reject_post()
    {
        $params           = $this->request->param();
        $ShopOrderInit    = new \init\ShopOrderInit();//订单管理
        $WxBaseController = new WxBaseController();//微信基础类


        if ($params['status'] == 14) $params['refund_reject_time'] = time();


        if ($params['status'] == 16) {
            $order_info = $ShopOrderInit->get_find($params['id']);
            //退款金额
            $refund_amount = $order_info['amount'];
            if ($order_info['pay_type'] == 2) $refund_amount = $order_info['balance'];
            //退款通过时间
            $params['refund_pass_time'] = time();

            //退款 && 微信退款
            if ($order_info['pay_type'] == 1) {
                $refund_result = $WxBaseController->wx_refund($order_info['pay_num'], $refund_amount, $order_info['amount']);//后台  退款操作,退款全部金额 &&微信
                if ($refund_result['code'] == 0) $this->error($refund_result['msg']);
            }
            //余额退款
            if ($order_info['pay_type'] == 2) {
                $admin_id_and_name = cmf_get_current_admin_id() . '-' . session('name');//管理员信息
                $remark            = "操作人[{$admin_id_and_name}];操作说明[同意退款订单:{$order_info['order_num']};金额:{$order_info['balance']}];操作类型[管理员同意退款申请];";//管理备注
                AssetModel::incAsset('后台余额,订单退款成功,增加余额,全额退款 [110]', [
                    'operate_type'  => 'balance',//操作类型，balance|point ...
                    'identity_type' => 'member',//身份类型，member| ...
                    'user_id'       => $order_info['user_id'],
                    'price'         => $refund_amount,
                    'order_num'     => $order_info['order_num'],
                    'order_type'    => 110,
                    'content'       => '订单退款成功',
                    'remark'        => $remark,
                    'order_id'      => $order_info['id'],
                ]);
            }
            //组合支付 &&微信+余额
            if ($order_info['pay_type'] == 5) {
                //余额
                $admin_id_and_name = cmf_get_current_admin_id() . '-' . session('name');//管理员信息
                $remark            = "操作人[{$admin_id_and_name}];操作说明[同意退款订单:{$order_info['order_num']};金额:{$order_info['balance']}];操作类型[管理员同意退款申请];";//管理备注

                AssetModel::incAsset('后台余额,订单退款成功,组合支付,部分退款 [110]', [
                    'operate_type'  => 'balance',//操作类型，balance|point ...
                    'identity_type' => 'member',//身份类型，member| ...
                    'user_id'       => $order_info['user_id'],
                    'price'         => $order_info['balance'],
                    'order_num'     => $order_info['order_num'],
                    'order_type'    => 110,
                    'content'       => '订单退款成功',
                    'remark'        => $remark,
                    'order_id'      => $order_info['id'],
                ]);


                //微信
                $refund_result = $WxBaseController->wx_refund($order_info['pay_num'], $refund_amount, $order_info['amount']);//后台  退款操作,退款全部金额 &&微信+余额
                if ($refund_result['code'] == 0) $this->error($refund_result['msg']);
            }
        }


        $result = $ShopOrderInit->api_edit_post($params);
        if (empty($result)) $this->error('失败请重试');


        $this->success("保存成功", 'index' . $this->params_url);
    }


    //部分金额退款
    public function reject_post2()
    {
        $params           = $this->request->param();
        $ShopOrderInit    = new \init\ShopOrderInit();//订单管理
        $WxBaseController = new WxBaseController();//微信基础类

        //退款金额
        $refund_amount              = $params['refund_amount'];
        $order_info                 = $ShopOrderInit->get_find($params['id']);
        $params['refund_pass_time'] = time();//退款通过时间
        $params['status']           = 16;


        if ($refund_amount > $order_info['amount']) $this->error('请输入有效金额!');


        //退款 && 微信退款
        if ($order_info['pay_type'] == 1) {
            $refund_result = $WxBaseController->wx_refund($order_info['pay_num'], $refund_amount, $order_info['amount']);//后台  部分金额退款  &&微信
            if ($refund_result['code'] == 0) $this->error($refund_result['msg']);
        }
        //余额退款
        if ($order_info['pay_type'] == 2) {
            $admin_id_and_name = cmf_get_current_admin_id() . '-' . session('name');//管理员信息
            $remark            = "操作人[{$admin_id_and_name}];操作说明[同意退款订单:{$order_info['order_num']};金额:{$order_info['balance']}];操作类型[管理员同意退款申请];";//管理备注

            AssetModel::incAsset('后台余额,订单退款成功,手动输入金额退款 [110]', [
                'operate_type'  => 'balance',//操作类型，balance|point ...
                'identity_type' => 'member',//身份类型，member| ...
                'user_id'       => $order_info['user_id'],
                'price'         => $refund_amount,
                'order_num'     => $order_info['order_num'],
                'order_type'    => 110,
                'content'       => '订单退款成功',
                'remark'        => $remark,
                'order_id'      => $order_info['id'],
            ]);

        }
        //组合支付 &&微信+余额
        if ($order_info['pay_type'] == 5) {
            //余额
            $admin_id_and_name = cmf_get_current_admin_id() . '-' . session('name');//管理员信息
            $remark            = "操作人[{$admin_id_and_name}];操作说明[同意退款订单:{$order_info['order_num']};金额:{$order_info['balance']}];操作类型[管理员同意退款申请];";//管理备注

            AssetModel::incAsset('后台余额,订单退款成功,组合支付,手动输入金额退款 [110]', [
                'operate_type'  => 'balance',//操作类型，balance|point ...
                'identity_type' => 'member',//身份类型，member| ...
                'user_id'       => $order_info['user_id'],
                'price'         => $order_info['balance'],
                'order_num'     => $order_info['order_num'],
                'order_type'    => 110,
                'content'       => '订单退款成功',
                'remark'        => $remark,
                'order_id'      => $order_info['id'],
            ]);

            //微信
            $refund_amount = $order_info['amount'];
            $refund_result = $WxBaseController->wx_refund($order_info['pay_num'], $refund_amount, $order_info['amount']);//后台  部分金额退款  &&微信+余额
            if ($refund_result['code'] == 0) $this->error($refund_result['msg']);
        }


        $result = $ShopOrderInit->api_edit_post($params);
        if (empty($result)) $this->error('失败请重试');


        $this->success("保存成功", 'index' . $this->params_url);
    }


    //拒绝理由
    public function refund_why()
    {
        $params        = $this->request->param();
        $ShopOrderInit = new \init\ShopOrderInit();//订单管理
        $where         = [];
        $where[]       = ['id', '=', $params['id']];
        $result        = $ShopOrderInit->get_find($where);
        if (empty($result)) $this->error("暂无数据");
        $toArray = $result->toArray();
        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        return $this->fetch();
    }


    /**
     * 导出数据
     * @param array $where 条件
     */
    public function export_excel($where = [], $params = [])
    {
        $ShopOrderModel       = new \initmodel\ShopOrderModel();
        $ShopOrderDetailInit = new \init\ShopOrderDetailInit();//订单详情
        $MemberInit          = new \init\MemberInit();

        $status_list = [
            1  => '待付款',
            2  => '待发货',
            3  => '已发货',
            4  => '已完成',
            10 => '申请退款',
            14 => '已拒绝退款',
            15 => '退款中',
            16 => '已退款',
            30 => '待装猪',
            31 => '装猪中',
            32 => '待过磅',
            33 => '过磅中',
            40 => '待补差价',
            50 => '补差价待支付',
        ];

        $result = $ShopOrderModel
            ->where($where)
            ->order("id desc")
            ->select()
            ->each(function ($item, $key) use ($MemberInit, $status_list, $ShopOrderDetailInit) {
                //时间格式化
                if ($item['create_time']) $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                if ($item['pay_time']) $item['pay_time'] = date('Y-m-d H:i:s', $item['pay_time']);

                //订单号过长问题
                if ($item["order_num"]) $item["order_num"] = $item["order_num"] . "\t";

                //用户信息
                $user_info             = $MemberInit->get_find($item['user_id']);
                $item['userInfo']      = $user_info ? "(ID:{$user_info['id']}) {$user_info['nickname']} {$user_info['phone']}" : '-';
                $item['pay_type_name'] = $item['pay_type'] == 1 ? '微信支付' : ($item['pay_type'] == 2 ? '余额支付' : '其他');
                $item['status_name']   = $status_list[$item['status']] ?? '未知状态';

                //商品信息 - 优先从订单表获取，其次查详情表
                $goodsInfo = '';
                if (!empty($item['goods_name'])) {
                    // 订单表已有商品名称
                    $goodsInfo = "名称:{$item['goods_name']}\n";
                } else {
                    // 查询订单详情表
                    $goods_list = \think\facade\Db::name('shop_order_detail')
                        ->where('order_num', $item['order_num'])
                        ->select();
                    if ($goods_list && count($goods_list) > 0) {
                        foreach ($goods_list as $goods) {
                            $goodsInfo .= "名称:{$goods['goods_name']}\n";
                            if (!empty($goods['sku_name'])) $goodsInfo .= "规格:{$goods['sku_name']}\n";
                            $goodsInfo .= "数量:{$goods['count']}\n";
                            $goodsInfo .= "单价:{$goods['goods_price']}\n\n";
                        }
                    } else {
                        $goodsInfo = '商品信息(调试:订单号=' . $item['order_num'] . ',查到0条)';
                    }
                }
                $item['goodsInfo'] = $goodsInfo;

                //地址信息
                $item['addressInfo'] = "{$item['province']}-{$item['city']}-{$item['county']}{$item['address']}";

                return $item;
            })
            ->toArray();

        $headArrValue = [
            ["rowName" => "ID", "rowVal" => "id", "width" => 10],
            ["rowName" => "用户信息", "rowVal" => "userInfo", "width" => 35],
            ["rowName" => "订单号", "rowVal" => "order_num", "width" => 30],
            ["rowName" => "状态", "rowVal" => "status_name", "width" => 15],
            ["rowName" => "支付方式", "rowVal" => "pay_type_name", "width" => 15],
            ["rowName" => "订单金额", "rowVal" => "amount", "width" => 15],
            ["rowName" => "收货地址", "rowVal" => "addressInfo", "width" => 40],
            ["rowName" => "商品信息", "rowVal" => "goodsInfo", "width" => 40],
            ["rowName" => "创建时间", "rowVal" => "create_time", "width" => 25],
        ];

        $Excel = new ExcelController();
        $Excel->excelExports($result, $headArrValue, ["fileName" => "订单管理"]);
    }

}
