<?php
namespace app\admin\controller;


/**
    * @adminMenuRoot(
    *     "name"                =>"BaseExpress",
    *     "name_underline"      =>"base_express",
    *     "controller_name"     =>"BaseExpress",
    *     "table_name"          =>"base_express",
    *     "action"              =>"default",
    *     "parent"              =>"",
    *     "display"             => true,
    *     "order"               => 10000,
    *     "icon"                =>"none",
    *     "remark"              =>"物流管理",
    *     "author"              =>"",
    *     "create_time"         =>"2025-06-23 16:05:57",
    *     "version"             =>"1.0",
    *     "use"                 => new \app\admin\controller\BaseExpressController();
    * )
    */


use think\facade\Db;
use cmf\controller\AdminBaseController;


class BaseExpressController extends AdminBaseController{

	// public function initialize(){
	//	//物流管理
	//	parent::initialize();
	//	}


	   /**
		 * 首页基础信息
		 */
		protected function base_index()
		{

		}

		/**
		* 编辑,添加基础信息
		*/
		protected function base_edit()
		{
			

			
		}


	/**
	 * 首页列表数据
	 * @adminMenu(
	 *     'name'             => 'BaseExpress',
	 *     'name_underline'   => 'base_express',
	 *     'parent'           => 'index',
	 *     'display'          => true,
	 *     'hasView'          => true,
	 *     'order'            => 10000,
	 *     'icon'             => '',
	 *     'remark'           => '物流管理',
	 *     'param'            => ''
	 * )
	 */
	public function index()
	{
		$this->base_index();//处理基础信息


		$BaseExpressInit = new \init\BaseExpressInit();//物流管理    (ps:InitController)
		$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
		$params  =  $this->request->param();

		/** 查询条件 **/
		$where = [];
		if($params["keyword"]) $where[]=["name","like","%{$params["keyword"]}%"];
		if($params["test"]) $where[]=["test","=", $params["test"]];
		//if($params["status"]) $where[]=["status","=", $params["status"]];
		//$where[]=["type","=", 1];


		/** 查询数据 **/
		$params["InterfaceType"] = "admin";//接口类型
		$params["DataFormat"]    = "list";//数据格式,find详情,list列表
		$params["field"]         = "*";//过滤字段



		/** 导出数据 **/
		if ($params["is_export"]) $this->export_excel($where, $params);


		/** 查询数据 **/
		$result = $BaseExpressInit->get_list_paginate($where,  $params);


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


		//添加提交
		public function add_post()
		{
				$BaseExpressInit = new \init\BaseExpressInit();//物流管理   (ps:InitController)
				$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
				$params = $this->request->param();


				/** 检测参数信息 **/
				$validateResult = $this->validate($params, 'BaseExpress');
				if ($validateResult !== true) $this->error($validateResult);


				/** 插入数据 **/
				$result = $BaseExpressInit->admin_edit_post($params);
				if (empty($result)) $this->error("失败请重试");

				$this->success("保存成功", "index{$this->params_url}");
		}



		//查看详情
		public function find()
		{
			$this->base_edit();//处理基础信息

			$BaseExpressInit = new \init\BaseExpressInit();//物流管理    (ps:InitController)
			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params = $this->request->param();

			/** 查询条件 **/
			$where   = [];
			$where[] = ["id", "=", $params["id"]];

			/** 查询数据 **/
			$params["InterfaceType"] = "admin";//接口类型
			$params["DataFormat"]    = "find";//数据格式,find详情,list列表
			$result = $BaseExpressInit->get_find($where,$params);
			if (empty($result)) $this->error("暂无数据");

			/** 数据格式转数组 **/
			$toArray = $result->toArray();
			foreach ($toArray as $k => $v) {
			$this->assign($k, $v);
			}

			return $this->fetch();
		}


		//编辑详情
		public function edit()
		{
			$this->base_edit();//处理基础信息

			$BaseExpressInit = new \init\BaseExpressInit();//物流管理  (ps:InitController)
 			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params     = $this->request->param();

			/** 查询条件 **/
			$where   = [];
			$where[] = ["id", "=", $params["id"]];

			/** 查询数据 **/
			$params["InterfaceType"] = "admin";//接口类型
			$params["DataFormat"]    = "find";//数据格式,find详情,list列表
			$result = $BaseExpressInit->get_find($where, $params);
			if (empty($result)) $this->error("暂无数据");

			/** 数据格式转数组 **/
			$toArray = $result->toArray();
			foreach ($toArray as $k => $v) {
			$this->assign($k, $v);
			}

			return $this->fetch();
		}



		//提交编辑
		public function edit_post()
		{
			$BaseExpressInit = new \init\BaseExpressInit();//物流管理   (ps:InitController)
			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params = $this->request->param();



			/** 检测参数信息 **/
			$validateResult = $this->validate($params, 'BaseExpress');
			if ($validateResult !== true) $this->error($validateResult);




			/** 更改数据条件 && 或$params中存在id本字段可以忽略 **/
			$where   = [];
			if($params['id'])$where[] = ['id', '=', $params['id']];


			/** 提交数据 **/
			$result = $BaseExpressInit->admin_edit_post($params,$where);
			if (empty($result)) $this->error("失败请重试");

			$this->success("保存成功", "index{$this->params_url}");
		}



		//提交(副本,无任何操作) 编辑&添加
		public function edit_post_two()
		{
			$BaseExpressInit = new \init\BaseExpressInit();//物流管理   (ps:InitController)
			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params = $this->request->param();

			/** 更改数据条件 && 或$params中存在id本字段可以忽略 **/
			$where   = [];
			if($params['id'])$where[] = ['id', '=', $params['id']];

			/** 提交数据 **/
			$result = $BaseExpressInit->edit_post_two($params,$where);
			if (empty($result)) $this->error("失败请重试");

			$this->success("保存成功", "index{$this->params_url}");
		}



		//驳回
		public function refuse()
		{
			$BaseExpressInit = new \init\BaseExpressInit();//物流管理  (ps:InitController)
			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params     = $this->request->param();

			/** 查询条件 **/
			$where   = [];
			$where[] = ["id", "=", $params["id"]];


			/** 查询数据 **/
			$params["InterfaceType"] = "admin";//接口类型
			$params["DataFormat"]    = "find";//数据格式,find详情,list列表
			$result = $BaseExpressInit->get_find($where, $params);
			if (empty($result)) $this->error("暂无数据");

			/** 数据格式转数组 **/
			$toArray = $result->toArray();
			foreach ($toArray as $k => $v) {
			$this->assign($k, $v);
			}

			return $this->fetch();
		}


		//驳回,更改状态
		public function audit_post()
		{
			$BaseExpressInit = new \init\BaseExpressInit();//物流管理   (ps:InitController)
			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params = $this->request->param();

			/** 更改数据条件 && 或$params中存在id本字段可以忽略 **/
			$where   = [];
			if($params['id'])$where[] = ['id', '=', $params['id']];


			/** 查询数据 **/
			$params["InterfaceType"] = "admin";//接口类型
			$params["DataFormat"]    = "find";//数据格式,find详情,list列表
			$item = $BaseExpressInit->get_find($where);
			if (empty($item)) $this->error("暂无数据");

			/** 通过&拒绝时间 **/
			if ($params['status'] == 2) $params['pass_time'] = time();
			if ($params['status'] == 3) $params['refuse_time'] = time();

			/** 提交数据 **/
			$result = $BaseExpressInit->edit_post_two($params,$where);
			if (empty($result)) $this->error("失败请重试");

			$this->success("操作成功");
		}

		//删除
		public function delete()
		{
			$BaseExpressInit = new \init\BaseExpressInit();//物流管理   (ps:InitController)
			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params = $this->request->param();

			if ($params["id"]) $id = $params["id"];
			if (empty($params["id"]))  $id = $this->request->param("ids/a");

			/** 删除数据 **/
			$result = $BaseExpressInit->delete_post($id);
			if (empty($result)) $this->error("失败请重试");

			$this->success("删除成功");//   , "index{$this->params_url}"
		}


		//批量操作
		public function batch_post()
		{
			$BaseExpressInit = new \init\BaseExpressInit();//物流管理   (ps:InitController)
			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params = $this->request->param();

			$id = $this->request->param("id/a");
			if (empty($id)) $id = $this->request->param("ids/a");

			//提交编辑
			$result = $BaseExpressInit->batch_post($id, $params);
			if (empty($result)) $this->error("失败请重试");

			$this->success("保存成功");//   , "index{$this->params_url}"
		}


		//更新排序
		public function list_order_post()
		{
			$BaseExpressInit = new \init\BaseExpressInit();//物流管理   (ps:InitController)
			$BaseExpressModel = new \initmodel\BaseExpressModel(); //物流管理   (ps:InitModel)
			$params = $this->request->param("list_order/a");

			//提交更新
			$result = $BaseExpressInit->list_order_post($params);
			if (empty($result)) $this->error("失败请重试");

			$this->success("保存成功"); //   , "index{$this->params_url}"
		}



		/**
		* 导出数据
		* @param array $where 条件
		*/
		public function export_excel($where = [], $params = [])
		{
			$BaseExpressModel = new \initmodel\BaseExpressModel();

			$result = $BaseExpressModel
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
				["rowName" => "名称", "rowVal" => "name", "width" => 30],
				["rowName" => "单价", "rowVal" => "price", "width" => 15],
				["rowName" => "排序", "rowVal" => "list_order", "width" => 10],
				["rowName" => "是否显示", "rowVal" => "is_show", "width" => 10],
				["rowName" => "创建时间", "rowVal" => "create_time", "width" => 25],
			];

			$Excel = new ExcelController();
			$Excel->excelExports($result, $headArrValue, ["fileName" => "物流管理"]);
		}


}
