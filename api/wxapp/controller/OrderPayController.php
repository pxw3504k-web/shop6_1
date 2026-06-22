<?php

namespace api\wxapp\controller;

use initmodel\AssetModel;
use plugins\weipay\lib\PayController;
use think\facade\Db;
use think\facade\Log;

class OrderPayController extends AuthController
{

    //    public function initialize()
    //    {
    //        parent::initialize();//初始化方法
    //    }


    /**
     * 微信小程序支付
     * @OA\Post(
     *     tags={"订单支付"},
     *     path="/wxapp/order_pay/wx_pay_mini",
     *
     *
     * 	   @OA\Parameter(
     *         name="openid",
     *         in="query",
     *         description="openid",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *    @OA\Parameter(
     *         name="order_type",
     *         in="query",
     *         description="10商城,90充值余额",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     * 	   @OA\Parameter(
     *         name="order_num",
     *         in="query",
     *         description="order_num",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/order_pay/wx_pay_mini
     *   official_environment: https://xcxkf063.aubye.com/api/wxapp/order_pay/wx_pay_mini
     *   api: /wxapp/order_pay/wx_pay_mini
     *   remark_name: 微信小程序支付
     *
     */
    public function wx_pay_mini()
    {
        $this->checkAuth();

        $params = $this->request->param();
        $openid = $this->user_info['mini_openid'];

        $Pay                      = new PayController();
        $OrderPayModel            = new \initmodel\OrderPayModel();
        $ShopOrderModel           = new \initmodel\ShopOrderModel(); //订单管理   (ps:InitModel)
        $MemberRechargeOrderModel = new \initmodel\MemberRechargeOrderModel(); //充值订单   (ps:InitModel)

        $map   = [];
        $map[] = ['order_num', '=', $params['order_num']];


        //订单支付
        if ($params['order_type'] == 10) {
            //订单详情
            $order_info = $ShopOrderModel->where($map)->find();


            //第一次支付
            if ($order_info['status'] == 1) {
                //修改订单,支付类型
                $ShopOrderModel->where($map)->strict(false)->update([
                    'pay_type'    => 1,
                    'update_time' => time(),
                ]);
            }

            //补差价支付
            if ($order_info['status'] == 50) {
                //修改订单,支付类型
                $ShopOrderModel->where($map)->strict(false)->update([
                    'pay_type2'   => 1,
                    'update_time' => time(),
                ]);

                $order_info = $ShopOrderModel->where($map)->find();
                if ($order_info['status'] != 50) $this->error('订单状态错误');

                //重新生成订单信息
                $order_info['amount']    = $order_info['repair_amount'];
                $order_info['status']    = 1;
                $order_info['order_num'] = $order_info['repair_order_num'];
                $params['order_type']    = 20;//不要和普通支付类型一样
            }


        }


        //充值余额
        if ($params['order_type'] == 90) {
            //修改订单,支付类型
            $MemberRechargeOrderModel->where($map)->strict(false)->update([
                'pay_type'    => 1,
                'update_time' => time(),
            ]);
            $order_info = $MemberRechargeOrderModel->where($map)->find();
        }

        if (empty($order_info)) $this->error('订单不存在');
        if ($order_info['amount'] < 0.01) $this->error('订单错误');
        if ($order_info['status'] != 1) $this->error('订单状态错误');


        //订单金额&&订单号
        $amount    = $order_info['amount'] ?? 0.01;
        $order_num = $order_info['order_num'] ?? cmf_order_sn();

        //支付记录插入一条记录
        $pay_num = $OrderPayModel->add($openid, $order_num, $amount, $params['order_type'], 1, $order_info['id']);
        $result  = $Pay->wx_pay_mini($pay_num, $amount, $openid);


        if ($result['code'] != 1) {
            if (strstr($result['msg'], '此商家的收款功能已被限制')) $this->error('支付失败,请联系客服!错误码:pay_limit');
            $this->error($result['msg']);
        }


        //将订单号,支付单号返回给前端
        $result['data']['order_num'] = $order_num;
        $result['data']['pay_num']   = $pay_num;

        $this->success('请求成功', $result['data']);
    }


    /**
     * 余额支付
     * @OA\Post(
     *     tags={"订单支付"},
     *     path="/wxapp/order_pay/balance_pay",
     *
     *
     * 	   @OA\Parameter(
     *         name="openid",
     *         in="query",
     *         description="openid",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *
     *    @OA\Parameter(
     *         name="order_type",
     *         in="query",
     *         description="10商城,90充值余额",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     * 	   @OA\Parameter(
     *         name="order_num",
     *         in="query",
     *         description="order_num",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/order_pay/balance_pay
     *   official_environment: https://xcxkf063.aubye.com/api/wxapp/order_pay/balance_pay
     *   api: /wxapp/order_pay/balance_pay
     *   remark_name: 余额支付
     *
     */
    public function balance_pay()
    {
        $this->checkAuth();

        $params = $this->request->param();
        $openid = $this->user_info['openid'];

        $Pay                      = new PayController();
        $OrderPayModel            = new \initmodel\OrderPayModel();
        $ShopOrderModel           = new \initmodel\ShopOrderModel(); //订单管理   (ps:InitModel)
        $NotifyController         = new NotifyController();
        $MemberRechargeOrderModel = new \initmodel\MemberRechargeOrderModel(); //充值订单   (ps:InitModel)

        $map   = [];
        $map[] = ['order_num', '=', $params['order_num']];


        //订单支付
        if ($params['order_type'] == 10) {
            //订单详情
            $order_info = $ShopOrderModel->where($map)->find();


            //第一次支付
            if ($order_info['status'] == 1) {
                //修改订单,支付类型
                $ShopOrderModel->where($map)->strict(false)->update([
                    'pay_type'    => 2,
                    'update_time' => time(),
                ]);

                $dec_content = '订单支付';
            }

            //补差价支付
            if ($order_info['status'] == 50) {
                //修改订单,支付类型
                $ShopOrderModel->where($map)->strict(false)->update([
                    'pay_type2'   => 2,
                    'update_time' => time(),
                ]);

                $order_info = $ShopOrderModel->where($map)->find();
                if ($order_info['status'] != 50) $this->error('订单状态错误');

                //重新生成订单信息
                $order_info['amount']    = $order_info['repair_amount'];
                $order_info['status']    = 1;
                $order_info['order_num'] = $order_info['repair_order_num'];
                $params['order_type']    = 20;//不要和普通支付类型一样

                $dec_content = '补差价';
            }

        }


        if (empty($order_info)) $this->error('订单不存在');
        if ($order_info['amount'] < 0.01) $this->error('订单错误');
        if ($order_info['status'] != 1) $this->error('订单状态错误');


        //订单金额&&订单号
        $amount    = $order_info['amount'] ?? 0.01;
        $order_num = $order_info['order_num'] ?? cmf_order_sn();

        //检测余额是否充足
        if ($this->user_info['balance'] < $amount) $this->error('余额不足');


        //支付记录插入一条记录
        $pay_num = $OrderPayModel->add($openid, $order_num, $amount, $params['order_type'], 2, $order_info['id']);


        $remark = "操作人[用户ID:{$this->user_info['id']};昵称:{$this->user_info['nickname']};手机号:{$this->user_info['phone']}];操作说明[支付订单:{$order_num};金额:{$amount}];操作类型[下单扣除余额];";//备注
        AssetModel::decAsset('用户扣除余额,支付订单 [100]', [
            'operate_type'  => 'balance',//操作类型，balance|point ...
            'identity_type' => 'member',//身份类型，member| ...
            'user_id'       => $this->user_id,
            'price'         => $amount,
            'order_num'     => $order_num,
            'order_type'    => 100,
            'content'       => $dec_content,
            'remark'        => $remark,
            'order_id'      => $order_info['id'],
        ]);


        //余额 支付回调
        $NotifyController->balancePayNotify($pay_num);


        //将订单号,支付单号返回给前端
        $result['order_num'] = $order_num;
        $result['pay_num']   = $pay_num;

        $this->success('支付成功', $result);
    }


    /**
     * 免费兑换
     * @OA\Post(
     *     tags={"订单支付"},
     *     path="/wxapp/order_pay/free_pay",
     *
     *
     * 	   @OA\Parameter(
     *         name="openid",
     *         in="query",
     *         description="openid",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *    @OA\Parameter(
     *         name="order_type",
     *         in="query",
     *         description="10商城,90充值余额",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     * 	   @OA\Parameter(
     *         name="order_num",
     *         in="query",
     *         description="order_num",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/order_pay/free_pay
     *   official_environment: https://xcxkf063.aubye.com/api/wxapp/order_pay/free_pay
     *   api: /wxapp/order_pay/free_pay
     *   remark_name: 免费兑换
     *
     */
    public function free_pay()
    {
        $this->checkAuth();

        $params = $this->request->param();
        $openid = $this->user_info['openid'];

        $Pay                      = new PayController();
        $OrderPayModel            = new \initmodel\OrderPayModel();
        $ShopOrderModel           = new \initmodel\ShopOrderModel(); //订单管理   (ps:InitModel)
        $NotifyController         = new NotifyController();
        $MemberRechargeOrderModel = new \initmodel\MemberRechargeOrderModel(); //充值订单   (ps:InitModel)

        $map   = [];
        $map[] = ['order_num', '=', $params['order_num']];


        //订单支付
        if ($params['order_type'] == 10) {
            //订单详情
            $order_info = $ShopOrderModel->where($map)->find();


            //第一次支付
            if ($order_info['status'] == 1) {
                //修改订单,支付类型
                $ShopOrderModel->where($map)->strict(false)->update([
                    'pay_type'    => 6,
                    'update_time' => time(),
                ]);
            }

            //补差价支付
            if ($order_info['status'] == 50) {
                //修改订单,支付类型
                $ShopOrderModel->where($map)->strict(false)->update([
                    'pay_type2'   => 6,
                    'update_time' => time(),
                ]);

                $order_info = $ShopOrderModel->where($map)->find();
                if ($order_info['status'] != 50) $this->error('订单状态错误');

                //重新生成订单信息
                $order_info['amount']    = $order_info['repair_amount'];
                $order_info['status']    = 1;
                $order_info['order_num'] = $order_info['repair_order_num'];
                $params['order_type']    = 20;//不要和普通支付类型一样
            }


        }


        //充值余额
        if ($params['order_type'] == 90) {
            //修改订单,支付类型
            $MemberRechargeOrderModel->where($map)->strict(false)->update([
                'pay_type'    => 6,
                'update_time' => time(),
            ]);
            $order_info = $MemberRechargeOrderModel->where($map)->find();
        }


        if (empty($order_info)) $this->error('订单不存在');
        if ($order_info['amount'] < 0.01) $this->error('订单错误');
        if ($order_info['status'] != 1) $this->error('订单状态错误');


        //订单金额&&订单号
        $amount    = $order_info['amount'] ?? 0.01;
        $order_num = $order_info['order_num'] ?? cmf_order_sn();


        //支付记录插入一条记录
        $pay_num = $OrderPayModel->add($openid, $order_num, $amount, $params['order_type'], 6, $order_info['id']);


        //积分 支付回调
        $NotifyController->freePayNotify($pay_num);


        //将订单号,支付单号返回给前端
        $result['order_num'] = $order_num;
        $result['pay_num']   = $pay_num;

        $this->success('支付成功', $result);
    }


}