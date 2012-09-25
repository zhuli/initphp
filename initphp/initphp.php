<?php
/*********************************************************************************
 * InitPHP 2.0 国产PHP开发框架  框架入口文件 核心框架文件
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2011-10-09
***********************************************************************************/
require_once('initphp.conf.php'); //导入框架配置类
require_once('init/core.init.php'); //导入核心类文件
require_once('init/exception.init.php'); //导入核心类文件
class InitPHP extends coreInit {

	public static $time;

	/**
	 * 【静态】运行InitPHP开发框架 - 框架运行核心函数
	 * 1. 在index.php中实例化InitPHP启动类 InitPHP::init();
	 * 2. 初始化网站路由，运行框架
	 * 3. 全局使用方法：InitPHP::init(); 
	 * @return object
	 */
	public static function init() { 
		try {
			require(INITPHP_PATH . '/init/dispatcher.init.php');
			require(INITPHP_PATH . '/init/run.init.php'); 
			$dispacher = InitPHP::loadclass('dispatcherInit');
			$dispacher->dispatcher();
			$run = InitPHP::loadclass('runInit');
			$run->run();
		} catch (exceptionInit $e) {
			$e->errorMessage();
		}
	}

	/**
	 * 【静态】框架加载文件函数 - 核心加载文件
	 * 1. 自定义文件路径，加载文件
	 * 2. 自定义文件路径数组，自动查询，找到文件返回TRUE，找不到返回false
	 * 全局使用方法：InitPHP::import($filename, $pathArr);
	 * @param $filename 文件名称
	 * @param $pathArr  文件路径
	 * @return file
	 */
	public static function import($filename_old, array $pathArr = array()) {
		$filename = InitPHP::getAppPath($filename_old);
		$temp_name = md5($filename);
		if (isset(parent::$instance['importfile'][$temp_name])) return true; //已经加载该文件，则不重复加载
		if (@is_readable($filename) == true && empty($pathArr)) {
			require($filename);
			parent::$instance['importfile'][$temp_name] = true; //设置已加载
			return true;
		} else {
			/* 自动搜索文件夹 */
			foreach ($pathArr as $val) {
				$new_filename = rtrim($val, '/') . '/' . $filename_old;
				$new_filename = InitPHP::getAppPath($new_filename);
				if (isset(parent::$instance['importfile'][$temp_name])) return true;
				if (@is_readable($new_filename)) {
					require($new_filename);// 载入文件
					parent::$instance['importfile'][$temp_name] = true;
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 【静态】框架实例化php类函数，单例模式
	 * 1. 单例模式-单例 实例化一个类
	 * 2. 可强制重新实例化
	 * 全局使用方法：InitPHP::loadclass($classname, $force = false);
	 * @param string $classname
	 * @return object
	 */
	public static function loadclass($classname, $force = false) {
		if (preg_match('/[^a-z0-9\-_.]/i', $classname)) InitPHP::initError('invalid classname');
		if ($force == true) unset(parent::$instance['loadclass'][$classname]);
		if (!isset(parent::$instance['loadclass'][$classname])) {
			if (!class_exists($classname)) InitPHP::initError($classname . ' is not exist!');
			$Init_class = new $classname;
			parent::$instance['loadclass'][$classname] = $Init_class;
		}
		return parent::$instance['loadclass'][$classname];
	}

	/**
	 * 【静态】框架hook插件机制
	 * 1. 采用钩子挂载机制，一个钩子上可以挂载多个执行
	 * 2. hook机制需要配置框架配置文件运行
	 * 全局使用方法：InitPHP::hook($hookname, $data = '');
	 * @param string $hookname 挂钩名称
	 * @param string $data   传递的参数
	 * @return
	 */
	public static function hook($hookname, $data = '') {
		$InitPHP_conf = InitPHP::getConfig();
		$hookconfig = $InitPHP_conf['hook']['path'] . '/' . $InitPHP_conf['hook']['config']; //配置文件
		$hookconfig = InitPHP::getAppPath($hookconfig);
		if (!isset(parent::$instance['inithookconfig']) && file_exists($hookconfig)) {
			parent::$instance['inithookconfig'] = require_once($hookconfig);
		}
		if (!isset(parent::$instance['inithookconfig'][$hookname])) return false;
		if (!is_array(parent::$instance['inithookconfig'][$hookname])) {
			self::_hook(parent::$instance['inithookconfig'][$hookname][0], parent::$instance['inithookconfig'][$hookname][1], $data);
		} else {
			foreach (parent::$instance['inithookconfig'][$hookname] as $v) {
				self::_hook($v[0], $v[1], $data);
			}
		}
	}

	/**
	 *	【静态】框架hook插件机制-私有
	 *  @param  string $class  钩子的类名
	 *  @param  array  $function  钩子方法名称
	 *  @param  string $data 传递的参数
	 *  @return object
	 */
	private static function _hook($class, $function, $data = '') {
		$InitPHP_conf = InitPHP::getConfig();
		if (preg_match('/[^a-z0-9\-_.]/i', $class)) return false;
		$file_name  = $InitPHP_conf['hook']['path'] . '/' . $class . $InitPHP_conf['hook']['file_postfix'];
		$file_name  = InitPHP::getAppPath($file_name);
		$class_name = $class . $InitPHP_conf['hook']['class_postfix']; //类名
		if (!file_exists($file_name)) return false;
		if (!isset(parent::$instance['inithook'][$class_name])) {
			require_once($file_name);
			if (!class_exists($class_name)) return false;
			$init_class = new $class_name;
			parent::$instance['inithook'][$class_name] = $init_class;
		}
		if (!method_exists($class_name, $function)) return false;
		return parent::$instance['inithook'][$class_name]->$function($data);
	}

	/**
	 * 【静态】XSS过滤，输出内容过滤
	 * 1. 框架支持全局XSS过滤机制-全局开启将消耗PHP运行
	 * 2. 手动添加XSS过滤函数，在模板页面中直接调用
	 * 全局使用方法：InitPHP::output($string, $type = 'encode');
	 * @param string $string  需要过滤的字符串
	 * @param string $type    encode HTML处理 | decode 反处理
	 * @return string
	 */
	public static function output($string, $type = 'encode') {
		$html = array("&", '"', "'", "<", ">", "%3C", "%3E");
		$html_code = array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;", "&lt;", "&gt;");
		if ($type == 'encode') {
			if (function_exists('htmlspecialchars')) return htmlspecialchars($string);
			return str_replace($html, $html_code, $string);
		} else {
			if (function_exists('htmlspecialchars_decode')) return htmlspecialchars_decode($string);
			return str_replace($html_code, $html, $string);
		}
	}

	/**
	 * 【静态】获取Service-实例并且单例模式获取Service
	 * 1.单例模式获取
	 * 2.可以选定对应Service路径path
	 * 3. service需要在配置文件中配置参数，$path对应service目录中的子目录
	 * 全局使用方法：InitPHP::getService($servicename, $path = '')
	 * @param string $servicename 服务名称
	 * @param string $path 路径
	 * @return object
	 */
	public static function getService($servicename, $path = '') {
		$InitPHP_conf = InitPHP::getConfig();
		$path  = ($path == '') ? '' : $path . '/';
		$class = $servicename . $InitPHP_conf['service']['service_postfix'];
		$file  = rtrim($InitPHP_conf['service']['path'], '/') . '/' . $path . $class . '.php';
		if (!InitPHP::import($file)) return false;
		return InitPHP::loadclass($class);
	}

	/**
	 * 【静态】获取Dao-实例并且单例模式获取Dao
	 * 1.单例模式获取
	 * 2.可以选定Dao路径path
	 * 3. dao需要在配置文件中配置参数，$path对应dao目录中的子目录
	 * 全局使用方法：InitPHP::getDao($daoname, $path = '')
	 * @param string $daoname 服务名称
	 * @param string $path 路径
	 * @return object
	 */
	public static function getDao($daoname, $path = '') {
		$InitPHP_conf = InitPHP::getConfig();
		$path  = ($path == '') ? '' : $path . '/';
		$class = $daoname . $InitPHP_conf['dao']['dao_postfix'];
		$file  = rtrim($InitPHP_conf['dao']['path'], '/') . '/' . $path . $class . '.php';
		if (!InitPHP::import($file)) return false;
		$obj = InitPHP::loadclass($class);
		return $obj;
	}

	/**
	 * 【静态】组装URL
	 * 1. url index.php?m=user&c=index&a=run
	 * 2. 如果开启路由转化，url解析后 /user/index/run/?id=1
	 * 全局使用方法：InitPHP::url($url, $baseUrl = '')
	 * @param string $url     index.php?m=user&c=index&a=run
	 * @param string $baseUrl 如果不填写，则自动组装网站url
	 * @return
	 */
	public static function url($url, $baseUrl = '') {
		$InitPHP_conf = InitPHP::getConfig();
		switch ($InitPHP_conf['isuri']) {

			case 'rewrite' :
				$param = $paramArr = array();
				$urlArr = explode('?', $url);
				if (isset($urlArr[1])) {
					$string = $ext_string = '';
					$param = explode('&', $urlArr[1]);
					foreach ($param as $v) {
						$temp = explode('=', $v);
						if ($temp[0] == 'm' || $temp[0] == 'c' || $temp[0] == 'a') {
							$string .=  $temp[1] . '/';
						} else {
							$ext_string .= $temp[0].'='.$temp[1] . '&';
						}
					}
				}
				$ext_string = ($ext_string == '') ? '' : '?' . $ext_string;
				$baseUrl = ($baseUrl == '') ? $InitPHP_conf['url'] : $baseUrl;
				return rtrim($baseUrl, '/') . '/' . $string . $ext_string;
				break;

			case 'path' :
				$param  = $paramArr = array();
				$urlArr = explode('?', $url);
				$file = $urlArr[0];
				if (isset($urlArr[1])) {
					$string = $string . '/';
					$param = explode('&', $urlArr[1]);
					foreach ($param as $v) {
						$temp = explode('=', $v);
						if ($temp[0] == 'm' || $temp[0] == 'c' || $temp[0] == 'a') {
							$string .=  $temp[1] . '/';
						} else {
							$string .=  $temp[0] . '/' . $temp[1] . '/';
						}
					}
				}
				$baseUrl = ($baseUrl == '') ? $InitPHP_conf['url'] : $baseUrl;
				return rtrim($baseUrl, '/') . '/' . ltrim($string, '/');
				break;
			
			case 'html' :
				$param = $paramArr = array();
				$urlArr = explode('?', $url);
				if (isset($urlArr[1])) {
					$string = $ext_string = '';
					$param = explode('&', $urlArr[1]);
					foreach ($param as $v) {
						$temp = explode('=', $v);
						if ($temp[0] == 'm' || $temp[0] == 'c' || $temp[0] == 'a') {
							$string .=  $temp[1] . '-';
						} else {
							$ext_string .= $temp[0].'='.$temp[1] . '&';
						}
					}
				}
				$ext_string = ($ext_string == '') ? '' : '?' . $ext_string;
				$string  =  rtrim($string, '-') . '.htm';
				$baseUrl = ($baseUrl == '') ? $InitPHP_conf['url'] : $baseUrl;
				return rtrim($baseUrl, '/') . '/' . $string . $ext_string;
				break;

			default:
				return $url;
				break;
		}
	}

	/**
	 * 【静态】获取时间戳
	 * 1. 静态时间戳函数
	 * 全局使用方法：InitPHP::getTime();
	 * @param $msg
	 * @return html
	 */
	public static function getTime() {
		if (self::$time > 0) return self::$time;
		self::$time = time();
		return self::$time;
	}

	/**
	 * 框架控制访问器，主要用来控制是否有权限访问该模块
	 * 1. InitPHP 页面访问主要通过3个参数来实现，m=模型，c=控制器，a=Action。m模型需要在应用开启模型的情况下执行
	 * 2. $config是用户具体业务逻辑中，包含的用户访问权限白名单，我们根据白名单列表去判断用户是否具备权限
	 * 3. 具备访问权限，返回true，否则false
	 * 4. 返回false之后的具体业务逻辑需要用户自己做相应处理
	 * 5. 开启$InitPHP_conf['ismodule']配置结构
	 * array(
	 * 	'模型名称' => array(
	 *       '控制器名称' => array('run', 'test', 'Action名称')
	 * 	)
	 * )
	 * 6. 关闭$InitPHP_conf['ismodule']配置结构
	 * array(
	 *    '控制器名称' => array('run', 'test', 'Action名称')
	 * )
	 * 7. 默认为空，则全部有权限
	 * @param array $config
	 */
	public static function acl($config = array()) {
		$InitPHP_conf = InitPHP::getConfig();
		if (!is_array($config) || empty($config)) return true;
		$c = ($_GET['c']) ? $_GET['c'] : $InitPHP_conf['controller']['default_controller'];
		$a = ($_GET['a']) ? $_GET['a'] : $InitPHP_conf['controller']['default_action'];
		if ($InitPHP_conf['ismodule']) {
			$m = $_GET['m'];
			if (isset($config[$m]) && isset($config[$m][$c]) && in_array($a, $config[$c]))
				return true;
			return false;
		} else {
			if (isset($config[$c]) && in_array($a, $config[$c]))
				return true;
			return false;
		}
	}

	/**
	 * 【静态】获取全局配置文件
	 * 全局使用方法：InitPHP::getConfig()
	 * @param $msg
	 * @return Array
	 */
	public static function getConfig() {
		global $InitPHP_conf;
		return $InitPHP_conf;
	}
	
	/**
	 * 设置配置文件，框架意外慎用！
	 * @param $key
	 * @param $value
	 */
	public static function setConfig($key, $value) {
		global $InitPHP_conf;
		$InitPHP_conf[$key] = $value;
		return $InitPHP_conf;
	}
	
	/**
	 * 【静态】获取项目路径
	 * 全局使用方法：InitPHP::getAppPath('data/file.php')
	 * @param $path
	 * @return String
	 */
	public static function getAppPath($path = '') {
		if (!defined('APP_PATH')) return $path;
		return rtrim(APP_PATH, '/') . '/' . $path;
	}

	/**
	 * 【静态】框架错误机制
	 * 1. 框架的错误信息输出函数，尽量不要使用在项目中
	 * 全局使用方法：InitPHP::initError($msg)
	 * @param $msg
	 * @return html
	 */
	public static function initError($msg, $code = 10000) {
		throw new exceptionInit($msg, $code);
	}
	
	/**
	 * 【静态】调用其它Controller中的方法
	 * 1. 一般不建议采用Controller调用另外一个Controller中的方法
	 * 2. 该函数可以用于接口聚集，将各种接口聚集到一个接口中使用
	 * 全局使用方法：InitPHP::getController($controllerName, $functionName)
	 * @param $controllerName 控制器名称
	 * @param $functionName   方法名称
	 * @param $params         方法参数
	 * @param $controllerPath 控制器文件夹名称,例如在控制器文件夹目录中，还有一层目录，user/则，该参数需要填写
	 * @return 
	 */
	public function getController($controllerName, $functionName, $params = array(), $controllerPath = '') {
		$InitPHP_conf = InitPHP::getConfig();
		$controllerPath = ($controllerPath == '') ? '' : rtrim($controllerPath, '/') . '/';
		$path = rtrim($InitPHP_conf['controller']['path'], '/') . '/' . $controllerPath . $controllerName . '.php';
		InitPHP::import($path);
		$controller = InitPHP::loadclass($controllerName);
		if (!$controller) 
			return InitPHP::initError('can not loadclass : ' . $controllerName);
		if (!method_exists($controller, $functionName)) 
			return InitPHP::initError('function is not exists : ' . $controllerName);
		if (!$params) {
			$controller->$functionName();
		} else {
			call_user_func_array(array($controller, $functionName), $params); 
		}
	}

}

/**
 * 控制器Controller基类
 * 1. 每个控制器都需要继承这个基类
 * 2. 通过继承这个基类，就可以直接调用框架中的方法
 * 3. 控制器中可以直接调用$this->contorller 和 $this->view
 * @author zhuli
 */
class Controller extends coreInit {

	/**
	 * @var controllerInit
	 */
	protected $controller;

	/**
	 * @var viewInit
	 */
	protected $view;

	/**
	 * 初始化 
	 */
	public function __construct() {
		parent::__construct();
		$InitPHP_conf = InitPHP::getConfig();
		$this->controller = $this->load('controller', 'c'); //导入Controller
		$this->view       = $this->load('view', 'v'); //导入View
		$this->view->set_template_config($InitPHP_conf['template']); //设置模板
		$this->view->assign('init_token', $this->controller->get_token()); //全局输出init_token标记
		//注册全局变量，这样在Service和Dao中也能调用Controller中的类
		$this->register_global('common', $this->controller); 
	}
}

/**
 * 服务Service基类
 * 1. 每个Service都需要继承这个基类
 * 2. 通过继承这个基类，就可以直接调用框架中的方法
 * 3. Service中可以直接调用$this->service
 * @author zhuli
 */
class Service extends coreInit {

	/**
	 * @var serviceInit
	 */
	protected $service;

	/**
	 * 初始化
	 */
	public function __construct() {
		parent::__construct();
		$this->service = $this->load('service', 's'); //导入Service
	}
}

/**
 * 数据层Dao基类
 * 1. 每个Dao都需要继承这个基类
 * 2. 通过继承这个基类，就可以直接调用框架中的方法
 * 3. Dao中可以直接调用$this->dao
 * 4. $this->dao->db DB方法库
 * 5. $this->dao->cache Cache方法库
 * @author zhuli
 */
class Dao extends coreInit {

	/**
	 * @var daoInit
	 */
	protected $dao;

	/**
	 * 初始化
	 */
	public function __construct() {
		$this->dao = $this->load('dao', 'd'); //导入D
		$this->dao->run_db(); //初始化db
		$this->dao->run_cache(); //初始化cahce
	}

	/**
	 * 分库初始化DB
	 * 如果有多数据库链接的情况下，会调用该函数来自动切换DB link
	 * @param string $db
	 * @return dbInit
	 */
	public function init_db($db = 'default') {
		$this->dao->db->init_db($db);
		return $this->dao->db;
	}
}