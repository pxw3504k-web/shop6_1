<?php

namespace api\wxapp\controller;

/**
 * @ApiController(
 *     "name"                    =>"ShopExpLog",
 *     "name_underline"          =>"shop_exp_log",
 *     "controller_name"         =>"ShopExpLog",
 *     "table_name"              =>"shop_exp_log",
 *     "remark"                  =>"物流记录"
 *     "api_url"                 =>"/api/wxapp/shop_exp_log/index",
 *     "author"                  =>"",
 *     "create_time"             =>"2025-08-04 15:12:06",
 *     "version"                 =>"1.0",
 *     "use"                     => new \api\wxapp\controller\ShopExpLogController();
 *     "test_environment"        =>"http://shop6.ikun:9090/api/wxapp/shop_exp_log/index",
 *     "official_environment"    =>"http://xcxkf063.aubye.com/api/wxapp/shop_exp_log/index",
 * )
 */


use think\facade\Db;
use think\facade\Log;
use think\facade\Cache;


error_reporting(0);


class ShopExpLogController extends AuthController
{

    //public function initialize(){
    //	//物流记录
    //	parent::initialize();
    //}


    /**
     * 默认接口
     * /api/wxapp/shop_exp_log/index
     * http://xcxkf063.aubye.com/api/wxapp/shop_exp_log/index
     */
    public function index()
    {
        $ShopExpLogInit  = new \init\ShopExpLogInit();//物流记录   (ps:InitController)
        $ShopExpLogModel = new \initmodel\ShopExpLogModel(); //物流记录   (ps:InitModel)

        $result = [];

        $this->success('物流记录-接口请求成功', $result);
    }


    /**
     * 物流记录 列表
     * @OA\Post(
     *     tags={"物流记录"},
     *     path="/wxapp/shop_exp_log/find_exp_log_list",
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
     *
     *
     *
     *
     *
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
     *         description="false=分页(不传默认分页),true=不分页",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *
     *
     *
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/shop_exp_log/find_exp_log_list
     *   official_environment: http://xcxkf063.aubye.com/api/wxapp/shop_exp_log/find_exp_log_list
     *   api:  /wxapp/shop_exp_log/find_exp_log_list
     *   remark_name: 物流记录 列表
     *
     */
    public function find_exp_log_list()
    {
        $ShopExpLogInit  = new \init\ShopExpLogInit();//物流记录   (ps:InitController)
        $ShopExpLogModel = new \initmodel\ShopExpLogModel(); //物流记录   (ps:InitModel)

        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ['id', '>', 0];

        if ($params["keyword"]) $where[] = ["order_num|remark", "like", "%{$params['keyword']}%"];
        if ($params["status"]) $where[] = ["status", "=", $params["status"]];


        /** 查询数据 **/
        $params["InterfaceType"] = "api";//接口类型
        $params["DataFormat"]    = "list";//数据格式,find详情,list列表
        $params["field"]         = "*";//过滤字段
        if ($params['is_paginate']) $result = $ShopExpLogInit->get_list($where, $params);
        if (empty($params['is_paginate'])) $result = $ShopExpLogInit->get_list_paginate($where, $params);
        if (empty($result)) $this->error("暂无信息!");

        $this->success("请求成功!", $result);
    }


    /**
     * 物流记录 详情
     * @OA\Post(
     *     tags={"物流记录"},
     *     path="/wxapp/shop_exp_log/find_exp_log",
     *
     *
     *
     *    @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="id",
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
     *   test_environment: http://shop6.ikun:9090/api/wxapp/shop_exp_log/find_exp_log
     *   official_environment: http://xcxkf063.aubye.com/api/wxapp/shop_exp_log/find_exp_log
     *   api:  /wxapp/shop_exp_log/find_exp_log
     *   remark_name: 物流记录 详情
     *
     */
    public function find_exp_log()
    {
        $ShopExpLogInit  = new \init\ShopExpLogInit();//物流记录    (ps:InitController)
        $ShopExpLogModel = new \initmodel\ShopExpLogModel(); //物流记录   (ps:InitModel)

        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ["id", "=", $params["id"]];

        /** 查询数据 **/
        $params["InterfaceType"] = "api";//接口类型
        $params["DataFormat"]    = "find";//数据格式,find详情,list列表
        $result                  = $ShopExpLogInit->get_find($where, $params);
        if (empty($result)) $this->error("暂无数据");

        $this->success("详情数据", $result);
    }


}
