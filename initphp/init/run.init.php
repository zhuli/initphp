<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架  - 框架运行器，所有的框架运行都需要通过此控制器
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25
 ***********************************************************************************/
class runInit {

	private $controller_postfix     = 'Controller'; //控制器后缀
	private $action_postfix         = ''; //动作后缀
	private $default_controller     = 'index'; //默认执行的控制器名称
	private $default_action         = 'run'; //默认执行动作名称
	private $default_module         = 'index';
	private $module_list            = array('index');
	private $default_before_action  = 'before';//默认的前置Action
	private $default_after_action   = 'after'; //默认的后置Action

	/**
	 * @var interceptorInit
	 */
	private $interceptor;


	/**
	 * 框架运行核心函数
	 * 1. 设置参数
	 * 2. 获取controller
	 * 3. 运行前置Action
	 * 4. 运行正常Action
	 * 5. 运行后置Action
	 * @return file
	 */
	public function run() {
		$InitPHP_conf = InitPHP::getConfig(); //全局配置
		$this->init2Ehandle(); //收集错误信息，并记录
		$this->filter();
		$this->set_params($InitPHP_conf['controller']);
		//验证方法是否合法，如果请求参数不正确，则直接返回404
		$controllerObj = $this->checkRequest();
		$this->interceptor = InitPHP::loadclass('interceptorInit');
		$ret = $this->interceptor->preHandle(); //拦截器前置
		if ($ret == false) {
			return;
		}
		$this->run_before_action($controllerObj);//前置Action
		$this->run_action($controllerObj); //正常流程Action
		$this->run_after_action($controllerObj); //后置Action
		$this->interceptor->postHandle(); //拦截器后置
	}

	/**
	 * 验证请求是否合法
	 * 1. 如果请求参数m,c,a都为空，则走默认的
	 */
	private function checkRequest() {
		$InitPHP_conf = InitPHP::getConfig();
		$controller  = isset($_GET['c']) ? $_GET['c'] : '';
		$action = isset($_GET['a']) ? $_GET['a'] : '';
		if ($InitPHP_conf['ismodule'] == true) {
			$module  = isset($_GET['m']) ? $_GET['m'] : '';
			if ($module == "" && $controller == "" && $action == "") {
				$module = $_GET['m'] = $this->default_module;
				$controller = $_GET['c'] = $this->default_controller;
				$action = $_GET['a'] = $this->default_action;
			}
			//如果module不在白名单中，则直接返回404
			if (!in_array($module, $this->module_list) || empty($module)) {
				return InitPHP::return404();
			}
			$module = $module . '/';
		} else {
			if ($controller == "" && $action == "") {
				$controller = $_GET['c'] = $this->default_controller;
				$action = $_GET['a'] = $this->default_action;
			}
			$module = '';
		}
		//controller处理，如果导入Controller文件失败，则返回404
		$path = rtrim($InitPHP_conf['controller']['path'], '/') . '/';
		$controllerClass = $controller . $this->controller_postfix;
		$controllerFilePath = $path . $module . $controllerClass . '.php';
		if (!InitPHP::import($controllerFilePath)) {
			//如果开启了首字母大写,例如请求c=my,则类名为MyController
			$controllerClass = ucfirst($controller) . $this->controller_postfix; //改成大写
			$controllerFilePath = $path . $module . $controllerClass . '.php';
			if (!InitPHP::import($controllerFilePath)) {
				return InitPHP::return404();
			}
		}
		$controllerObj = InitPHP::loadclass($controllerClass);
		//处理Action，如果方法不存在，则直接返回404
		list($whiteList, $methodList) = $this->parseWhiteList($controllerObj->initphp_list);
		if ($action != $this->default_action) {
			if (!in_array($action, $whiteList)) {
				return InitPHP::return404();//如果Action不在白名单中
			} else {
				if ($methodList[$action]) {
					$method = strtolower($_SERVER['REQUEST_METHOD']);
					if (!in_array($method, $methodList[$action])) { //检查提交的HTTP METHOD
						return InitPHP::return405(); //如果请求Method不正确，则返回405
					}
				}
			}
		}
		return $controllerObj;
	}

	/**
	 * 框架运行控制器中的Action函数
	 * 1. 获取Action中的a参数
	 * 2. 检测是否在白名单中，不在则选择默认的
	 * 3. 检测方法是否存在，不存在则运行默认的
	 * 4. 运行函数
	 * @param object $controller 控制器对象
	 * @return file
	 */
	private function run_action($controller) {
		$action = trim($_GET['a']);
		$action = $action . $this->action_postfix;
		if (!method_exists($controller, $action)) {
			InitPHP::initError('Can not find default method : ' . $action);
		}
		/* REST形式访问 */
		$controller->$action();
	}

	/**
	 * 解析白名单
	 * 白名单参数支持指定GET POST PUT DEL 等HTTP METHOD操作
	 * 白名单参数：array('test', 'user|post')
	 * @param object $controller 控制器对象
	 * @return file
	 */
	private function parseWhiteList($initphp_list) {
		$whiteList = $methodList = array();
		foreach ($initphp_list as  $value) {
			if (strpos($value, "|") == false) {
				$whiteList[] = $value;
			} else {
				$temp = explode('|', $value);
				$whiteList[] = $temp[0];
				$methodTemp = explode('-', $temp[1]);
				foreach ($methodTemp as $v) {
					$methodList[$temp[0]][] = $v;
				}
			}
		}
		return array($whiteList, $methodList);
	}

	/**
	 * 运行框架前置类
	 * 1. 检测方法是否存在，不存在则运行默认的
	 * 2. 运行函数
	 * @param object $controller 控制器对象
	 * @return file
	 */
	private function run_before_action($controller) {
		$before_action = $this->default_before_action . $this->action_postfix;
		if (!method_exists($controller, $before_action)) return false;
		$controller->$before_action();
	}

	/**
	 * 运行框架后置类
	 * 1. 检测方法是否存在，不存在则运行默认的
	 * 2. 运行函数
	 * @param object $controller 控制器对象
	 * @return file
	 */
	private function run_after_action($controller) {
		$after_action = $this->default_after_action . $this->action_postfix;
		if (!method_exists($controller, $after_action)) return false;
		$controller->$after_action();
	}

	/**
	 *	设置框架运行参数
	 *  @param  string  $params
	 *  @return string
	 */
	private function set_params($params) {
		if (isset($params['controller_postfix']))
		$this->controller_postfix = $params['controller_postfix'];
		if (isset($params['action_postfix']))
		$this->action_postfix = $params['action_postfix'];
		if (isset($params['default_controller']))
		$this->default_controller = $params['default_controller'];
		if (isset($params['default_module']))
		$this->default_module = $params['default_module'];
		if (isset($params['module_list']))
		$this->module_list = $params['module_list'];
		if (isset($params['default_action']))
		$this->default_action = $params['default_action'];
		if (isset($params['default_before_action']))
		$this->default_before_action = $params['default_before_action'];
		if (isset($params['default_after_action']))
		$this->default_after_action = $params['default_after_action'];
	}

	/**
	 *	m-c-a数据处理
	 *  @return string
	 */
	private function filter() {
		if (isset($_GET['m'])) {
			if (!$this->_filter($_GET['m'])) unset($_GET['m']);
		}
		if (isset($_GET['c'])) {
			if (!$this->_filter($_GET['c'])) unset($_GET['c']);
		}
		if (isset($_GET['a'])) {
			if (!$this->_filter($_GET['a'])) unset($_GET['a']);
		}
	}

	private function _filter($str) {
		return preg_match('/^[A-Za-z0-9_]+$/', trim($str));
	}

	/*
	 * 初始化异常和错误处理函数
	 * */
	public function init2Ehandle(){
		set_exception_handler(array($this,'handleException'));
		set_error_handler(array($this,'handleError'),error_reporting());
	}

	/*
	 *设置异常处理函数,写入日志文件
	 */
	public function handleException($exception){
		restore_exception_handler();
		exceptionInit::errorTpl($exception);
	}

	/*
	 *设置PHP错误处理回调函数,写入日志文件
	 */
	public function handleError($errorCode, $msg = '', $errorFile = 'unkwon', $errorLine = 0){
		$InitPHP_conf = InitPHP::getConfig();
		restore_error_handler();
		if($errorCode & error_reporting()){
			InitPHP::log("[error_code]:" . $errorCode . " [msg]:" . $msg, ERROR);
		}
		if($InitPHP_conf['is_debug'] == true) {
			var_dump($msg);
		} else {
			return InitPHP::return500();
		}
	}
}