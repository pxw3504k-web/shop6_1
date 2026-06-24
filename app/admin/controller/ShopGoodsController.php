<?php

namespace app\admin\controller;


/**
 * @adminMenuRoot(
 *     "name"                =>"ShopGoods",
 *     "name_underline"      =>"shop_goods",
 *     "controller_name"     =>"ShopGoods",
 *     "table_name"          =>"shop_goods",
 *     "action"              =>"default",
 *     "parent"              =>"",
 *     "display"             => true,
 *     "order"               => 10000,
 *     "icon"                =>"none",
 *     "remark"              =>"商品管理",
 *     "author"              =>"",
 *     "create_time"         =>"2025-06-04 11:00:27",
 *     "version"             =>"1.0",
 *     "use"                 => new \app\admin\controller\ShopGoodsController();
 * )
 */


use cmf\lib\Upload;
use think\facade\Db;
use cmf\controller\AdminBaseController;


class ShopGoodsController extends AdminBaseController
{

    // public function initialize(){
    //	//商品管理
    //	parent::initialize();
    //	}


    /**
     * 首页基础信息
     */
    protected function base_index()
    {
        $ShopGoodsClassInit = new \init\ShopGoodsClassInit();//分类管理     (ps:InitController)
        $class_map          = [];
        $class_map[]        = ['id', '<>', 0];
        $class_map[]        = ['pid', '=', 0];
        $class_map[]        = ['type', '=', 'goods'];
        $this->assign('class_list', $ShopGoodsClassInit->get_list($class_map));

    }

    /**
     * 编辑,添加基础信息
     */
    protected function base_edit()
    {
        $ShopGoodsInit = new \init\ShopGoodsInit();//shop_goods     (ps:InitController)
        $this->assign('type_list', $ShopGoodsInit->type);


        $ShopGoodsClassInit = new \init\ShopGoodsClassInit();//分类管理     (ps:InitController)
        $class_map          = [];
        $class_map[]        = ['id', '<>', 0];
        $class_map[]        = ['pid', '=', 0];
        $class_map[]        = ['type', '=', 'goods'];
        $this->assign('class_list', $ShopGoodsClassInit->get_list($class_map));


        $ShopGoodsBreedModel = new \initmodel\ShopGoodsBreedModel(); //种场管理  (ps:InitModel)
        $breed_map           = [];
        $breed_map[]         = ['id', '<>', 0];
        $breed_map[]         = ['pid', '=', 0];
        /** 查询数据 **/
        $breed_list = $ShopGoodsBreedModel->where($breed_map)
            ->order('list_order asc,id asc')
            ->field('id,pid,name')
            ->select()
            ->each(function ($item, $key) use ($ShopGoodsBreedModel) {
                $map                = [];
                $map[]              = ['pid', '=', $item['id']];
                $map[]              = ['is_show', '=', 1];
                $item['child_list'] = $ShopGoodsBreedModel->where($map)
                    ->order('list_order asc,id asc')
                    ->field('id,pid,name')
                    ->select();
                return $item;
            });
        $this->assign('breed_list', $breed_list);
    }


    //获取二级分类列表
    public function get_class_list2()
    {
        $params = $this->request->param();
        //分类
        $ShopGoodsClassInit = new \init\ShopGoodsClassInit();//shop_goods_class     (ps:InitController)
        $ShopGoodsModel     = new \initmodel\ShopGoodsModel(); //商品管理   (ps:InitModel)


        //查询二级条件.
        $map = [];
        if ($params['one_id']) $map[] = ['pid', '=', $params['one_id']];//查询二级列表
        if (empty(count($map))) $this->success('请求成功');

        //二级列表
        $two_list = $ShopGoodsClassInit->get_list($map);
        //查询数据,方便展示二级下拉框选中值
        if ($params['item_id']) {
            $item_info = $ShopGoodsModel->where(['id' => $params['item_id']])->find();
            if ($item_info) {
                foreach ($two_list as $k => &$v) {
                    $v['selected'] = false;
                    //这里的branch_id 更改为二级,三级id
                    if ($params['one_id'] && $item_info['class_two_id'] == $v['id']) $v['selected'] = 'selected';//选中
                }
            }
        }

        $this->success('请求成功', '', $two_list);
    }


    /**
     * 获取二分类,回显使用
     */
    public function get_class_two_list()
    {
        $ShopGoodsClassInit = new \init\ShopGoodsClassInit();//shop_goods_class     (ps:InitController)

        $params = $this->request->param();
        $map    = [];
        $map[]  = ['pid', '=', $params['one_id']];

        $result = $ShopGoodsClassInit->get_list($map);

        foreach ($result as $k => &$v) {
            if ($v['id'] == $params['two_id']) $v['selected'] = 'selected';
        }

        $this->success('成功', '', $result);
    }


    //获取分类列表
    public function get_class_list()
    {
        $ShopGoodsClassModel = new \initmodel\ShopGoodsClassModel(); //分类管理  (ps:InitModel)
        $ShopGoodsModel      = new \initmodel\ShopGoodsModel(); //商品管理   (ps:InitModel)

        $params = $this->request->param();


        //商品详情
        $info = $ShopGoodsModel->where('id', '=', $params['item_id'])->field('class_two_id,id')->find();


        //分类列表
        $where   = [];
        $where[] = ['pid', '=', $params['one_id']];

        $result = $ShopGoodsClassModel
            ->where($where)
            ->select()
            ->each(function ($item, $key) use ($info) {
                //如果详情中,分类存在,回显
                if ($info['class_two_id'] == $item['id']) $item['selected'] = 'selected';
                return $item;
            });


        if (empty(count($result))) $this->error('暂无数据!');


        $this->success("list", '', $result);
    }


    /**
     * 首页列表数据
     * @adminMenu(
     *     'name'             => 'ShopGoods',
     *     'name_underline'   => 'shop_goods',
     *     'parent'           => 'index',
     *     'display'          => true,
     *     'hasView'          => true,
     *     'order'            => 10000,
     *     'icon'             => '',
     *     'remark'           => '商品管理',
     *     'param'            => ''
     * )
     */
    public function index()
    {
        $this->base_index();//处理基础信息


        $ShopGoodsInit  = new \init\ShopGoodsInit();//商品管理    (ps:InitController)
        $ShopGoodsModel = new \initmodel\ShopGoodsModel(); //商品管理   (ps:InitModel)
        $params         = $this->request->param();

        /** 查询条件 **/
        $where = [];
        if ($params["keyword"]) $where[] = ["goods_name", "like", "%{$params["keyword"]}%"];
        $where[] = ["type", "=", $params["type"] ?? 'goods'];
        if ($params["class_id"]) $where[] = ["class_id", "=", $params["class_id"]];
        if ($params["class_two_id"]) $where[] = ["class_two_id", "=", $params["class_two_id"]];
        if ($params["test"]) $where[] = ["test", "=", $params["test"]];
        //if($params["status"]) $where[]=["status","=", $params["status"]];
        //$where[]=["type","=", 1];


        /** 查询数据 **/
        $params["InterfaceType"] = "admin";//接口类型
        $params["DataFormat"]    = "list";//数据格式,find详情,list列表
        $params["field"]         = "*";//过滤字段


        /** 导出数据 **/
        if ($params["is_export"]) $this->export_excel($where, $params);


        /** 查询数据 **/
        $result = $ShopGoodsInit->get_list_paginate($where, $params);


        /** 数据渲染 **/
        $this->assign("list", $result);
        $this->assign("pagination", $result->render());//单独提取分页出来
        $this->assign("page", $result->currentPage());//当前页码


        return $this->fetch();
    }


    //添加
    public function add()
    {
        $this->base_edit();//处理基础信息


        return $this->fetch();
    }


    //编辑详情
    public function edit()
    {
        $this->base_edit();//处理基础信息

        $ShopGoodsInit  = new \init\ShopGoodsInit();//商品管理  (ps:InitController)
        $ShopGoodsModel = new \initmodel\ShopGoodsModel(); //商品管理   (ps:InitModel)
        $params         = $this->request->param();

        /** 查询条件 **/
        $where   = [];
        $where[] = ["id", "=", $params["goods_id"]];

        /** 查询数据 **/
        $params["InterfaceType"] = "admin";//接口类型
        $params["DataFormat"]    = "find";//数据格式,find详情,list列表
        $result                  = $ShopGoodsInit->get_find($where, $params);
        if (empty($result)) $this->error("暂无数据");

        /** 数据格式转数组 **/
        $toArray = $result->toArray();

        foreach ($toArray as $k => $v) {
            $this->assign($k, $v);
        }

        return $this->fetch();
    }


    //添加&&编辑 提交
    public function edit_post()
    {
        $ShopGoodsInit = new \init\ShopGoodsInit();//商品管理  (ps:InitController)


        // 获取请求参数
        $params = $this->request->param();
        unset($params['id']);
        $goods_id = $params['goods_id'] ?? 0;

        // 基础验证
        if (empty($params['images'])) $this->error('请上传商品图片');
        if (empty($params['image'])) $this->error('请上传商品图片');

        // 处理图片数据
        $params['images']      = $this->setParams($params['images']);
        $params['update_time'] = time();

        //处理分类
        if ($params['class_id']) $params['search_class_id'] = $params['class_id'];
        if ($params['class_two_id']) $params['search_class_id'] = $params['class_two_id'];

        //处理种场
        if ($params['breed_ids']) {
            $breed_ids           = array_keys($params['breed_ids']);//提取key
            $params['breed_ids'] = $this->setParams($breed_ids);
        }

        //处理规格数据
        $ShopGoodsInit->edit_sku_post($params, $goods_id);//普通商品-规格


        $this->success('保存成功', "index{$this->params_url}");
    }


    public function uploadImage()
    {
        $uploader = new Upload();

        $result = $uploader->upload();

        if ($result === false) {
            $this->error($uploader->getError());
        } else {
            $result['url'] = cmf_get_asset_url($result['filepath']);
            $this->success('上传成功', '', $result);
        }
    }


    //删除
    public function delete()
    {
        $ShopGoodsInit  = new \init\ShopGoodsInit();//商品管理   (ps:InitController)
        $ShopGoodsModel = new \initmodel\ShopGoodsModel(); //商品管理   (ps:InitModel)
        $params         = $this->request->param();

        if ($params["id"]) $id = $params["id"];
        if (empty($params["id"])) $id = $this->request->param("ids/a");

        /** 删除数据 **/
        $result = $ShopGoodsInit->delete_post($id);
        if (empty($result)) $this->error("失败请重试");

        $this->success("删除成功");//   , "index{$this->params_url}"
    }


    //批量操作
    public function batch_post()
    {
        $ShopGoodsInit  = new \init\ShopGoodsInit();//商品管理   (ps:InitController)
        $ShopGoodsModel = new \initmodel\ShopGoodsModel(); //商品管理   (ps:InitModel)
        $params         = $this->request->param();

        $id = $this->request->param("id/a");
        if (empty($id)) $id = $this->request->param("ids/a");

        //提交编辑
        $result = $ShopGoodsInit->batch_post($id, $params);
        if (empty($result)) $this->error("失败请重试");

        $this->success("保存成功");//   , "index{$this->params_url}"
    }


    //更新排序
    public function list_order_post()
    {
        $ShopGoodsInit  = new \init\ShopGoodsInit();//商品管理   (ps:InitController)
        $ShopGoodsModel = new \initmodel\ShopGoodsModel(); //商品管理   (ps:InitModel)
        $params         = $this->request->param("list_order/a");

        //提交更新
        $result = $ShopGoodsInit->list_order_post($params);
        if (empty($result)) $this->error("失败请重试");

        $this->success("保存成功"); //   , "index{$this->params_url}"
    }


    /**
     * 导出数据
     * @param array $where 条件
     */
    public function export_excel($where = [], $params = [])
    {
        $ShopGoodsModel = new \initmodel\ShopGoodsModel();

        $result = $ShopGoodsModel
            ->where($where)
            ->order("id desc")
            ->select()
            ->each(function ($item, $key) {
                if ($item['create_time']) $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                if ($item['update_time']) $item['update_time'] = date('Y-m-d H:i:s', $item['update_time']);
                return $item;
            })
            ->toArray();

        $headArrValue = [
            ["rowName" => "ID", "rowVal" => "id", "width" => 10],
            ["rowName" => "分类", "rowVal" => "class_name", "width" => 20],
            ["rowName" => "商品名称", "rowVal" => "goods_name", "width" => 40],
            ["rowName" => "价格", "rowVal" => "price", "width" => 15],
            ["rowName" => "库存", "rowVal" => "stock", "width" => 10],
            ["rowName" => "排序", "rowVal" => "list_order", "width" => 10],
            ["rowName" => "是否显示", "rowVal" => "is_show", "width" => 10],
            ["rowName" => "创建时间", "rowVal" => "create_time", "width" => 25],
        ];

        $Excel = new ExcelController();
        $Excel->excelExports($result, $headArrValue, ["fileName" => "商品管理"]);
    }


    /*************************      规格信息       ******************************/

    /**
     * 获取商品编辑数据
     * 包括商品基本信息、SKU数据、规格属性数据等
     *
     * @return void 返回JSON格式数据
     */
    public function getEditData()
    {
        // 获取请求参数中的商品ID
        $goods_id = $this->request->param('goods_id');

        // 初始化各模型
        $ShopGoodsSkuModel       = new \initmodel\sku\ShopGoodsSkuModel();
        $ShopGoodsAttrModel      = new \initmodel\sku\ShopGoodsAttrModel();
        $ShopGoodsAttrValueModel = new \initmodel\sku\ShopGoodsAttrValueModel();
        $ShopGoodsSkuAttrModel   = new \initmodel\sku\ShopGoodsSkuAttrModel();
        $ShopGoodsModel          = new \initmodel\ShopGoodsModel();

        // 获取商品基本信息
        $goods_info   = $ShopGoodsModel->where(['id' => $goods_id])->find();
        $is_attribute = $goods_info['is_attribute']; // 是否是多规格商品

        // 需要获取的字段数组
        $field_arr = ['image', 'price', 'unit_price', 'stock'];

        // 初始化返回数据
        $result = [
            'skuData'      => [],
            'specData'     => [],
            'is_attribute' => $is_attribute
        ];

        // 多规格商品处理
        if ($is_attribute == 1) {
            // 1. 获取规格属性列表
            $attr_list = $ShopGoodsAttrModel->field('id,attr_name title')
                ->where(['status' => 1, 'goods_id' => $goods_id])
                ->order('list_order')
                ->select();

            // 2. 获取每个规格属性下的属性值
            foreach ($attr_list as &$item) {
                $item['child'] = $ShopGoodsAttrValueModel->field('id,attr_value_name title')
                    ->where(['attr_id' => $item['id'], 'status' => 1])
                    ->order('list_order')
                    ->select();
            }

            // 3. 获取SKU列表
            $sku_list = $ShopGoodsSkuModel->where(['goods_id' => $goods_id, 'status' => 1])->select();

            // 4. 处理SKU数据
            foreach ($sku_list as $sku) {
                // 处理SKU图片路径
                $sku['image'] = $sku['image'] ? cmf_get_asset_url($sku['image']) : '';

                // 获取该SKU对应的属性值ID
                $map               = [
                    ['sku_id', '=', $sku['id']],
                    ['status', '=', 1]
                ];
                $attr_value_id_arr = $ShopGoodsSkuAttrModel->where($map)->column('attr_value_id');
                $attr_value_id_str = implode('-', $attr_value_id_arr);

                // 组织SKU数据格式
                foreach ($field_arr as $field) {
                    $result['skuData']['skus[' . $attr_value_id_str . '][' . $field . ']'] = $sku[$field];
                }
            }

            $result['specData'] = $attr_list;
        } // 单规格商品处理
        else {
            // 直接从商品信息中获取基本字段
            foreach ($field_arr as $field) {
                $result['skuData'][$field] = $goods_info[$field] ?? null;
            }
        }

        // 返回成功响应
        $this->success('', '', $result);
    }

}
