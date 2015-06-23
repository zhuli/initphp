<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Controller-controller 控制器基类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
require_once("request.init.php");
require_once("validate.init.php");
require_once("filter.init.php");
class controllerInit extends filterInit{

	public $v; //视图模型对象
	
	/**
	 * 初始化控制器，
	 */
	public function __construct() {
		$this->filter(); //全局过滤函数，对GET POST数据自动过，InitPHP采取非常严格的数据过滤机制
		$this->set_token(); //生成全局TOKEN值，防止CRsf攻击
	}
	
	/**
	 *	控制器 AJAX脚本输出
	 *  Controller中使用方法：$this->controller->ajax_return()
	 * 	@param  int     $status  0:错误信息|1:正确信息
	 * 	@param  string  $message  显示的信息
	 * 	@param  array   $data    传输的信息
	 * 	@param  array   $type    返回数据类型，json|xml|eval|jsonp
	 *  @return object
	 */
	public function ajax_return($status, $message = '', $data = array(), $type = 'json') {
		$return_data = array('status' => $status, 'message' => $message, 'data' => $data);
		$type = strtolower($type);
		if ($type == 'json') {
			header("Content-type: application/json");
			exit(json_encode($return_data));
		} elseif ($type == 'xml') {
			header('Content-type: text/xml'); 
			$xml = '<?xml version="1.0" encoding="utf-8"?>';
			$xml .= '<return>';
				$xml .= '<status>' .$status. '</status>';
				$xml .= '<message>' .$message. '</message>';
				$xml .= '<data>' .serialize($data). '</data>';
			$xml .= '</return>';
		 	exit($xml);
		} elseif ($type == "jsonp"){
			$callback = $this->get_gp('callback');
            $json_data = json_encode($return_data);
		    if (is_string($callback) && isset($callback[0])) {
            	exit("{$callback}({$json_data});");
            } else {
                exit($json_data);
            }
		} elseif ($type == 'eval') {
			exit($return_data);
		} else {
		
		}	
	}
	
	/**
	 *	控制器 重定向
	 *  Controller中使用方法：$this->controller->redirect($url, $time = 0)
	 * 	@param  string  $url   跳转的URL路径
	 * 	@param  int     $time  多少秒后跳转
	 *  @return 
	 */
	public function redirect($url, $time = 0) {
		if (!headers_sent()) {
			if ($time === 0) header("Location: ".$url);
			header("refresh:" . $time . ";url=" .$url. "");
		} else {
			exit("<meta http-equiv='Refresh' content='" . $time . ";URL=" .$url. "'>");
		}
	}
	
	/**
	 *	返回404
	 *  Controller中使用方法：$this->controller->return404()
	 *  @return 
	 */
	public function return404() {
   		header('HTTP/1.1 404 Not Found');
    	header("status: 404 Not Found"); 
		return;
	}
	
	/**
	 *	返回404
	 *  Controller中使用方法：$this->controller->return200()
	 *  @return 
	 */
	public function return200() {
		header("HTTP/1.1 200 OK"); 
		return;
	}
	
	/**
	 *	返回500
	 *  Controller中使用方法：$this->controller->return500()
	 *  @return 
	 */
	public function return500() {
		header('HTTP/1.1 500 Internal Server Error');
		return;
	}
	
	/**
	 *	返回403
	 *  Controller中使用方法：$this->controller->return403()
	 *  @return 
	 */
	public function return403() {
		header('HTTP/1.1 403 Forbidden');
		return;
	}
	
	/**
	 *	返回405
	 *  Controller中使用方法：$this->controller->return405()
	 *  @return 
	 */
	public function return405() {
		header('HTTP/1.1 405 Method Not Allowed');
		return;
	}
	
	/**
	 * 验证Service中$this->service->return_msg返回的结构
	 * 是否正确。
	 * Controller中使用方法：$this->controller->check_service_return()
	 * @param array $data
	 * @return boolean true|false
	 */
	public function check_service_return($data) {
		if ($data[0] == true || $data[0] == 1) {
			return true;
		}
		return false;
	}
	
	/**
	 *	类加载-获取全局TOKEN，防止CSRF攻击
	 *  Controller中使用方法：$this->controller->get_token()
	 *  @return 
	 */
	public function get_token() {
		return $_COOKIE['init_token'];
	}
	
	/**
	 *	类加载-检测token值
	 *  Controller中使用方法：$this->controller->check_token($ispost = true)
	 *  @return 
	 */
	public function check_token($ispost = true) {
		if ($ispost && !$this->is_post()) return false;
		if ($this->get_gp('init_token') != $this->get_token()) return false;
		return true;
	}
	
	/**
	 *	类加载-设置全局TOKEN，防止CSRF攻击
	 *  Controller中使用方法：$this->controller->set_token()
	 *  @return 
	 */
	private function set_token() {
		if (!$_COOKIE['init_token']) {
			$str = substr(md5(time(). $this->get_useragent()), 5, 8);
			setcookie("init_token", $str, NULL, '/');
			$_COOKIE['init_token'] = $str;	
		}
	}
}
