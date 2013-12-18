<?php
/**
 * DEMO
 * @author zhuli 
 */
class indexController extends Controller {
	
	public $initphp_list = array('assign'); //Action白名单

	public function run() {     
		$this->view->assign('title', 'InitPHP模板使用方法'); //赋值
		$this->view->set_tpl("index_run"); //中间的模板
		$this->view->set_tpl("index_header", "F"); //头部模板
		$this->view->set_tpl("index_footer", "L"); //脚步模板
		$tpls = $this->view->get_tpl(); //统计输出的模板
		$this->view->assign("tpls", $tpls);
		$this->view->display(); //模板展示
	}

 } 