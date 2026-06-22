<?php

namespace api\wxapp\controller;

/**
 * @ApiController(
 *     "name"                    =>"ShopGoodsBreed",
 *     "name_underline"          =>"shop_goods_breed",
 *     "controller_name"         =>"ShopGoodsBreed",
 *     "table_name"              =>"shop_goods_breed",
 *     "remark"                  =>"种场管理"
 *     "api_url"                 =>"/api/wxapp/shop_goods_breed/index",
 *     "author"                  =>"",
 *     "create_time"             =>"2025-06-18 18:32:39",
 *     "version"                 =>"1.0",
 *     "use"                     => new \api\wxapp\controller\ShopGoodsBreedController();
 *     "test_environment"        =>"http://shop6.ikun:9090/api/wxapp/shop_goods_breed/index",
 *     "official_environment"    =>"http://xcxkf063.aubye.com/api/wxapp/shop_goods_breed/index",
 * )
 */


use think\facade\Db;
use think\facade\Log;
use think\facade\Cache;


error_reporting(0);


class ShopGoodsBreedController extends AuthController
{

    //public function initialize(){
    //	//种场管理
    //	parent::initialize();
    //}


    /**
     * 默认接口
     * /api/wxapp/shop_goods_breed/index
     * http://xcxkf063.aubye.com/api/wxapp/shop_goods_breed/index
     */
    public function index()
    {
        $ShopGoodsBreedInit  = new \init\ShopGoodsBreedInit();//种场管理   (ps:InitController)
        $ShopGoodsBreedModel = new \initmodel\ShopGoodsBreedModel(); //种场管理   (ps:InitModel)

        $result = [];

        $this->success('种场管理-接口请求成功', $result);
    }


    /**
     * 种场管理 列表
     * @OA\Post(
     *     tags={"种场管理"},
     *     path="/wxapp/shop_goods_breed/find_breed_list",
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
     *    @OA\Parameter(
     *         name="pid",
     *         in="query",
     *         description="上级id",
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
     *
     *
     *
     *
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/shop_goods_breed/find_breed_list
     *   official_environment: http://xcxkf063.aubye.com/api/wxapp/shop_goods_breed/find_breed_list
     *   api:  /wxapp/shop_goods_breed/find_breed_list
     *   remark_name: 种场管理 列表
     *
     */
    public function find_breed_list()
    {
        $ShopGoodsBreedInit  = new \init\ShopGoodsBreedInit();//种场管理   (ps:InitController)
        $ShopGoodsBreedModel = new \initmodel\ShopGoodsBreedModel(); //种场管理   (ps:InitModel)

        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ['id', '>', 0];
        $where[] = ['is_show', '=', 1];
        $where[] = ["pid", "=", $params["pid"] ?? 0];
        if ($params["keyword"]) $where[] = ["name", "like", "%{$params['keyword']}%"];
        if ($params["status"]) $where[] = ["status", "=", $params["status"]];
        if ($params['is_index']) $where[] = ['is_index', '=', 1];


        /** 查询数据 **/
        $params["InterfaceType"] = "api";//接口类型
        $params["DataFormat"]    = "list";//数据格式,find详情,list列表
        $params["field"]         = "*";//过滤字段
        $result                  = $ShopGoodsBreedInit->get_list($where, $params);
        if (empty($result)) $this->error("暂无信息!");

        $this->success("请求成功!", $result);
    }


    /**
     * 种场管理 列表   插件格式
     * @OA\Post(
     *     tags={"种场管理"},
     *     path="/wxapp/shop_goods_breed/find_breed_plug_list",
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
     *
     *
     *
     *
     *     @OA\Response(response="200", description="An example resource"),
     *     @OA\Response(response="default", description="An example resource")
     * )
     *
     *
     *   test_environment: http://shop6.ikun:9090/api/wxapp/shop_goods_breed/find_breed_plug_list
     *   official_environment: http://xcxkf063.aubye.com/api/wxapp/shop_goods_breed/find_breed_plug_list
     *   api:  /wxapp/shop_goods_breed/find_breed_plug_list
     *   remark_name: 种场管理 列表  插架格式
     *
     */
    public function find_breed_plug_list()
    {
        $ShopGoodsBreedInit  = new \init\ShopGoodsBreedInit();//种场管理   (ps:InitController)
        $ShopGoodsBreedModel = new \initmodel\ShopGoodsBreedModel(); //种场管理   (ps:InitModel)


        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ['id', '>', 0];
        $where[] = ['pid', '=', 0];
        $where[] = ['is_show', '=', 1];


        /** 查询数据 **/
        $result = $ShopGoodsBreedModel->where($where)
            ->order('list_order asc,id asc')
            ->field('id,pid,name,image')
            ->select()
            ->each(function ($item, $key) use ($params, $ShopGoodsBreedModel) {


                $map                = [];
                $map[]              = ['pid', '=', $item['id']];
                $map[]              = ['is_show', '=', 1];
                $item['child_list'] = $ShopGoodsBreedModel->where($map)
                    ->order('list_order asc,id asc')
                    ->field('id,pid,name,image')
                    ->select()
                    ->each(function ($item2, $key2) use ($ShopGoodsBreedModel) {

                        if ($item2['image']) $item2['image'] = cmf_get_asset_url($item2['image']);

                        return $item2;
                    });


                if ($item['image']) $item['image'] = cmf_get_asset_url($item['image']);


                return $item;
            });


        if (empty($result)) $this->error("暂无信息!");

        $this->success("请求成功!", $result);


    }


    /**
     * 种场管理 详情
     * @OA\Post(
     *     tags={"种场管理"},
     *     path="/wxapp/shop_goods_breed/find_breed",
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
     *   test_environment: http://shop6.ikun:9090/api/wxapp/shop_goods_breed/find_breed
     *   official_environment: http://xcxkf063.aubye.com/api/wxapp/shop_goods_breed/find_breed
     *   api:  /wxapp/shop_goods_breed/find_breed
     *   remark_name: 种场管理 详情
     *
     */
    public function find_breed()
    {
        $ShopGoodsBreedInit  = new \init\ShopGoodsBreedInit();//种场管理    (ps:InitController)
        $ShopGoodsBreedModel = new \initmodel\ShopGoodsBreedModel(); //种场管理   (ps:InitModel)

        /** 获取参数 **/
        $params            = $this->request->param();
        $params["user_id"] = $this->user_id;

        /** 查询条件 **/
        $where   = [];
        $where[] = ["id", "=", $params["id"]];

        /** 查询数据 **/
        $params["InterfaceType"] = "api";//接口类型
        $params["DataFormat"]    = "find";//数据格式,find详情,list列表
        $result                  = $ShopGoodsBreedInit->get_find($where, $params);
        if (empty($result)) $this->error("暂无数据");

        $this->success("详情数据", $result);
    }


}