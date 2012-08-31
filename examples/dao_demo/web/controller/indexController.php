<?php
/**
 * DEMO
 * @author zhuli
 */
class indexController extends Controller {
	
	public $initphp_list = array('test'); //Action白名单

	public function run() {
		$data = array(
			'username' => 'hello',
			'age' => 100
		);
		$userService = InitPHP::getService("user", "user"); //获取Service对象 \
		$userInfo = $userService->addUser($data); //调用getUser的方法
		echo '新增用户,请查看数据表';
	}
	
	public function test() {} 

} 