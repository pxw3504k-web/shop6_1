<?php

namespace api\wxapp\controller;

/**
 * @ApiController(
 *     "name"                    =>"MemberRecharge",
 *     "name_underline"          =>"member_recharge",
 *     "controller_name"         =>"MemberRecharge",
 *     "table_name"              =>"member_recharge",
 *     "remark"                  =>"充值管理"
 *     "api_url"                 =>"/api/wxapp/member_recharge/index",
 *     "author"                  =>"",
 *     "create_time"             =>"2025-03-14 17:54:40",
 *     "version"                 =>"1.0",
 *     "use"                     => new \api\wxapp\controller\MemberRechargeController();
 *     "test_environment"        =>"http://shop6.ikun:9090/api/wxapp/member_recharge/index",
 *     "official_environment"    =>"https://xcxkf063.aubye.com/api/wxapp/member_recharge/index",
 * )
 */


use think\facade\Db;
use think\facade\Log;
use think\facade\Cache;


error_reporting(0);


class MemberRechargeController extends AuthController
{


    public function initialize()
    {
        //充值管理

        parent::initialize();
    }


    /**
     * 默认接口
     * /api/wxapp/member_recharge/index
     * https://xcxkf063.aubye.com/api/wxapp/member_recharge/index
     */
    public function index()
    {
        $MemberRechargeInit  = new \init\MemberRechargeInit();//充值管理   (ps:InitController)
        $MemberRechargeModel = new \initmodel\MemberRechargeModel(); //充值管理   (ps:InitModel)

        $result = [];

        $this->success('充值管理-接口请求成功', $result);
    }


    /**
     * 充值规则 列表
     * @OA\Post(
     *     tags={"充值管理"},
     *     path="/wxapp/member_recharge/find_recharge_list",
     *
     *
     *
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="(选填)关键字搜索",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *     @OA\Parameter(
     *         name="is_paginate",
     *         in="query",
     *         description="true=分页,false=不分页",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *
     *
     *
     *    @OA\Parameter(
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
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/member_recharge/find_recharge_list
     *   official_environment: https://xcxkf063.aubye.com/api/wxapp/member_recharge/find_recharge_list
     *   api:  /wxapp/member_recharge/find_recharge_list
     *   remark_name: 充值管理 列表
     *
     */
    public function find_recharge_list()
    {
        $MemberRechargeInit = new \init\MemberRechargeInit();//充值管理   (ps:InitController)

        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ['id', '>', 0];
        if ($params["keyword"]) $where[] = ["name|price", "like", "%{$params['keyword']}%"];
        if ($params["status"]) $where[] = ["status", "=", $params["status"]];

        /** 查询数据 **/
        $params["InterfaceType"] = "api";//接口类型
        $params["DataFormat"]    = "list";//数据格式,find详情,list列表
        if ($params['is_paginate']) $result = $MemberRechargeInit->get_list_paginate($where, $params);
        if (empty($params['is_paginate'])) $result = $MemberRechargeInit->get_list($where, $params);
        if (empty($result)) $this->error("暂无信息!");

        $this->success("请求成功!", $result);
    }


    /**
     * 下单
     * @OA\Post(
     *     tags={"充值管理"},
     *     path="/wxapp/member_recharge/add_order",
     *
     *
     *
     *    @OA\Parameter(
     *         name="recharge_id",
     *         in="query",
     *         description="充值id",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *    @OA\Parameter(
     *         name="balance",
     *         in="query",
     *         description="充值金额",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *
     *    @OA\Parameter(
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
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/member_recharge/add_order
     *   official_environment: https://xcxkf063.aubye.com/api/wxapp/member_recharge/add_order
     *   api:  /wxapp/member_recharge/add_order
     *   remark_name: 下单
     *
     */
    public function add_order()
    {
        $this->checkAuth();
        $MemberRechargeOrderInit  = new \init\MemberRechargeOrderInit();//充值订单    (ps:InitController)
        $MemberRechargeInit       = new \init\MemberRechargeInit();//充值管理   (ps:InitController)
        $MemberRechargeOrderModel = new \initmodel\MemberRechargeOrderModel(); //充值订单   (ps:InitModel)


        //参数
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        //查询条件
        $where    = [];
        $where[]  = ['id', '=', $params["recharge_id"]];
        $recharge = $MemberRechargeInit->get_find($where);
        if (empty($recharge)) {
            if (empty($params["balance"])) $this->error('充值金额不能为空');
            $recharge['price']        = $params["balance"];
            $recharge['balance']      = $params["balance"];
            $recharge['give_balance'] = 0;
        }

        $order_num               = $this->get_only_num('member_recharge_order');
        $params['openid']        = $this->openid;
        $params['user_id']       = $this->user_id;
        $params['order_num']     = $order_num;
        $params['amount']        = $recharge['price'];
        $params['balance']       = $recharge['balance'];
        $params['give_balance']  = $recharge['give_balance'];
        $params['total_balance'] = $recharge['balance'] + $recharge['give_balance'];

        $result = $MemberRechargeOrderInit->api_edit_post($params);
        if (empty($result)) $this->error('失败请重试');

        $this->success('请支付', ['order_num' => $order_num, 'order_type' => 90]);
    }


    /**
     * 充值记录 (账户资产变动记录也会出现充值记录)
     * @OA\Post(
     *     tags={"充值管理"},
     *     path="/wxapp/member_recharge/find_order_list",
     *
     *
     *
     *
     *     @OA\Parameter(
     *         name="is_paginate",
     *         in="query",
     *         description="true=分页,false=不分页",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *
     *
     *
     *    @OA\Parameter(
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
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/member_recharge/find_order_list
     *   official_environment: https://xcxkf063.aubye.com/api/wxapp/member_recharge/find_order_list
     *   api:  /wxapp/member_recharge/find_order_list
     *   remark_name: 充值记录 (账户资产变动记录也会出现充值记录)
     *
     */
    public function find_order_list()
    {
        $this->checkAuth();
        $MemberRechargeOrderInit = new \init\MemberRechargeOrderInit();//充值订单    (ps:InitController)

        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ['id', '>', 0];
        $where[] = ['status', '=', 2];//已支付
        if ($params["keyword"]) $where[] = ["name|price", "like", "%{$params['keyword']}%"];
        $where[] = ["user_id", "=", $this->user_id];

        /** 查询数据 **/
        $params["InterfaceType"] = "api";//接口类型
        $params["DataFormat"]    = "list";//数据格式,find详情,list列表
        if ($params['is_paginate']) $result = $MemberRechargeOrderInit->get_list_paginate($where, $params);
        if (empty($params['is_paginate'])) $result = $MemberRechargeOrderInit->get_list($where, $params);
        if (empty($result)) $this->error("暂无信息!");

        $this->success("请求成功!", $result);
    }


}
