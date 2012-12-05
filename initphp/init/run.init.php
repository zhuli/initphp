<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.2.2 国产PHP开发框架 - 框架运行器，所有的框架运行都需要通过此控制器
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2012-11-27
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
	 * 【私有】框架运行核心函数
	 * 1. 设置参数
	 * 2. 获取controller
	 * 3. 运行前置Action
	 * 4. 运行正常Action
	 * 5. 运行后置Action
	 * @return file 
	 */
	public function run() {
		$InitPHP_conf = InitPHP::getConfig(); //全局配置
		$this->filter();
		$this->set_params($InitPHP_conf['controller']);
		$controller = $this->run_controller();
		$this->run_before_action($controller);//前置Action
		$this->run_action($controller); //正常流程Action
		$this->run_after_action($controller); //后置Action
	}

	/**
	 * 【私有】框架运行Controller控制器
	 * 1. 获取module
	 * 2. 获取controller
	 * 3. 加载控制器类,如果不存在，则选择默认的
	 * 4. 实例化类
	 * @return file
	 */
	private function run_controller() {
		$InitPHP_conf = InitPHP::getConfig();
		$controller  = $_GET['c'];
		$controller  = (empty($controller)) ? $this->default_controller : trim($controller);
		$controller  = $controller . $this->controller_postfix;
		$path        = rtrim($InitPHP_conf['controller']['path'], '/') . '/';
		if ($InitPHP_conf['ismodule'] !== true) {
			$module = '';
		} else {
			$module      = $_GET['m'];
			if (!in_array($module, $this->module_list) || empty($module)) {
				$module    = $this->default_module;
				$controller= $this->default_controller . $this->controller_postfix;
				$_GET['a'] = $this->default_action;
			}
			$module      = $module . '/';
		}
		/* 如果加载失败 - 加载默认的controller */
		if (!InitPHP::import($path . $module . $controller . '.php')) {
			if ($InitPHP_conf['ismodule'] == true) $module    = $this->default_module . '/';
			$controller = $this->default_controller . $this->controller_postfix;
			$_GET['a']  = $this->default_action;
		 	if (!InitPHP::import($path . $module . $controller . '.php'))
				InitPHP::import($path . $controller . '.php');
		}
		return InitPHP::loadclass($controller);
	}

	/**
	 * 【私有】框架运行控制器中的Action函数
	 * 1. 获取Action中的a参数
	 * 2. 检测是否在白名单中，不在则选择默认的
	 * 3. 检测方法是否存在，不存在则运行默认的
	 * 4. 运行函数
	 * @param object $controller 控制器对象
	 * @return file
	 */
	private function run_action($controller) {
		$action = trim($_GET['a']);
		if (!in_array($action, $controller->initphp_list)) $action = $this->default_action; //白名单
		$action = $action . $this->action_postfix;
		/* REST 模式*/
		$action = $this->run_rest($controller, $action);
		if (!method_exists($controller, $action)) {
			InitPHP::initError('Can not find default method : ' . $action);
		}
		/* REST形式访问 */
		$controller->$action();
	}
	
	/**
	 * 【私有】REST方式访问
	 *  1. 控制器中需要定义 public $isRest变量
	 *  2. 并且Action在rest数组列表中
	 *  3. 程序就会走REST模式
	 * @param object $controller 控制器对象
	 * @return file
	 */
	private function run_rest($controller, $action) {
		if (isset($controller->isRest) && in_array($action, $controller->isRest)) {
			$rest_action = '';
			$method = $_SERVER['REQUEST_METHOD'];
			if ($method == 'POST') {
				$rest_action = $action . '_post';
			} elseif ($method == 'GET') {
				$rest_action = $action . '_get';
			} elseif ($method == 'PUT') {
				$rest_action = $action . '_put';
			} elseif ($method == 'DEL') {
				$rest_action = $action . '_del';
			} else {
				return $action;
			}
			return $rest_action;
		} else {
			return $action;
		}
	}

	/**
	 * 【私有】运行框架前置类
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
	 * 【私有】运行框架后置类
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
	 *	【私有】设置框架运行参数
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
	 *	【私有】m-c-a数据处理
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
}