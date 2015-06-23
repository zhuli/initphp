<?php
/**
 * InitPHP开源框架 - DEM
 * @author zhuli
 */
class viewController extends Controller {

	public $initphp_list = array("tpl"); //Action白名单

	/**
	 * 模板赋值
	 */
	public function run() {
		$this->view->assign("username", "initphp");
		$this->view->assign("age", 10);
		$this->view->display("demo/view_run"); //单个模板可以使用 display直接显示模板
	}

	/**
	 * 使用模板
	 */
	public function tpl() {
		$this->view->set_tpl("demo/view_tpl_1");
		$this->view->set_tpl("demo/view_tpl_2");
		$this->view->display();
	}



}