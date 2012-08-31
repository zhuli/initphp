<?php
/**
 * DEMO
 * @author zhuli
 */
class indexController extends Controller {
	
	public $initphp_list = array('test'); //Action白名单

	public function run() {    
		$this->view->assign("title", "this is view");
		$this->view->set_tpl("header", "F");
		$this->view->set_tpl("footer", "L");
		$this->view->set_tpl("index_run");
		$this->view->display();
	}
	
	public function test() {} 

} 