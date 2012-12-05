<?php
/**
 * DEMO
 * @author zhuli 
 */
class indexController extends Controller {
	
	public $initphp_list = array('post'); //Action白名单

	public function run() {    
		$this->view->display("index_run"); //展示模板页面
	}
	
	public function post() {
		$info = $this->controller->get_gp(array('username', 'password'));
		print_r($info);
	}
} 