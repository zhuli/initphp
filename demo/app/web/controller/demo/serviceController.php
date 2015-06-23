<?php
/**
 * InitPHP开源框架 - DEM
 * @author zhuli
 */
class serviceController extends Controller {

	public $initphp_list = array("create", "create_post|post"); //Action白名单

	/**
	 * @var userService
	 */
	private $userService;

	public function __construct() {
		parent::__construct();
		//可以在构造函数中初始化
		//如果追求性能的话，可以在具体使用到的Action中调用
		$this->userService = InitPHP::getService("user");
	}

	/**
	 * 使用Service
	 */
	public function run() {
		$username = $this->userService->getUser();
		echo $username;
	}

	/**
	 * 模板
	 */
	public function create() {
		$this->view->display("demo/service_create");
	}

	public function create_post() {
		$user = $this->controller->get_gp(array('username', 'password'));
		echo "ID:" . $this->userService->createUser($user);
	}

}