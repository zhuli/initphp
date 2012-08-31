<?php
/**
 * DEMO
 * @author zhuli
 */
class indexController extends Controller {
	
	public $initphp_list = array('test'); //Action白名单

	public function run() {    
		$userService = InitPHP::getService("user", "user"); //获取Service对象 
		$userInfo = $userService->getUser(); //调用getUser的方法
		$this->view->assign("userInfo", $userInfo);
		$this->view->set_tpl("index_run");
		$this->view->display();
	}
	
	public function test() {} 

} 