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
		$user = $this->controller->get_gp(array('username', 'password'));
		$result = $this->_getUserService()->addUser($user);
		if ($result > 0) {
			echo '新增用户成功 ID:' . $result;
		} else {
			echo '新增失败';
		}
		
	}
	
	/**
	 * @return userDao
	 */
	private function _getUserService() {
		return InitPHP::getService("user");
	}
} 