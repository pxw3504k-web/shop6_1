<?php

namespace api\wxapp\controller;

/**
 * @ApiController(
 *     "name"                    =>"BaseExpress",
 *     "name_underline"          =>"base_express",
 *     "controller_name"         =>"BaseExpress",
 *     "table_name"              =>"base_express",
 *     "remark"                  =>"物流管理"
 *     "api_url"                 =>"/api/wxapp/base_express/index",
 *     "author"                  =>"",
 *     "create_time"             =>"2025-06-23 16:05:57",
 *     "version"                 =>"1.0",
 *     "use"                     => new \api\wxapp\controller\BaseExpressController();
 *     "test_environment"        =>"http://shop6.ikun:9090/api/wxapp/base_express/index",
 *     "official_environment"    =>"http://xcxkf063.aubye.com/api/wxapp/base_express/index",
 * )
 */


use think\facade\Db;
use think\facade\Log;
use think\facade\Cache;


error_reporting(0);


class BaseExpressController extends AuthController
{

    //public function initialize(){
    //	//物流管理
    //	parent::initialize();
    //}


    /**
     * 默认接口
     * /api/wxapp/base_express/index
     * http://xcxkf063.aubye.com/api/wxapp/base_express/index
     */
    public function index()
    {
        $BaseExpressInit  = new \init\BaseExpressInit();//物流管理   (ps:InitController)
        $BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)

        $result = [];

        $this->success('物流管理-接口请求成功', $result);
    }


    /**
     * 物流管理 列表
     * @OA\Post(
     *     tags={"物流管理"},
     *     path="/wxapp/base_express/find_express_list",
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
     *   test_environment: http://shop6.ikun:9090/api/wxapp/base_express/find_express_list
     *   official_environment: http://xcxkf063.aubye.com/api/wxapp/base_express/find_express_list
     *   api:  /wxapp/base_express/find_express_list
     *   remark_name: 物流管理 列表
     *
     */
    public function find_express_list()
    {
        $BaseExpressInit  = new \init\BaseExpressInit();//物流管理   (ps:InitController)
        $BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)

        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ['id', '>', 0];
        $where[] = ['is_show', '=', 1];
        if ($params["keyword"]) $where[] = ["name", "like", "%{$params['keyword']}%"];
        if ($params["status"]) $where[] = ["status", "=", $params["status"]];


        /** 查询数据 **/
        $params["InterfaceType"] = "api";//接口类型
        $params["DataFormat"]    = "list";//数据格式,find详情,list列表
        $params["field"]         = "*";//过滤字段
        if ($params['is_paginate']) $result = $BaseExpressInit->get_list($where, $params);
        if (empty($params['is_paginate'])) $result = $BaseExpressInit->get_list_paginate($where, $params);
        if (empty($result)) $this->error("暂无信息!");

        $this->success("请求成功!", $result);
    }


    /**
     * 物流管理 详情
     * @OA\Post(
     *     tags={"物流管理"},
     *     path="/wxapp/base_express/find_express",
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
     *   test_environment: http://shop6.ikun:9090/api/wxapp/base_express/find_express
     *   official_environment: http://xcxkf063.aubye.com/api/wxapp/base_express/find_express
     *   api:  /wxapp/base_express/find_express
     *   remark_name: 物流管理 详情
     *
     */
    public function find_express()
    {
        $BaseExpressInit  = new \init\BaseExpressInit();//物流管理    (ps:InitController)
        $BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)

        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ["id", "=", $params["id"]];

        /** 查询数据 **/
        $params["InterfaceType"] = "api";//接口类型
        $params["DataFormat"]    = "find";//数据格式,find详情,list列表
        $result                  = $BaseExpressInit->get_find($where, $params);
        if (empty($result)) $this->error("暂无数据");

        $this->success("详情数据", $result);
    }

 


}
