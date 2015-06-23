<?php 
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   - 路由分发核心类，将m-c-a URL重写
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25
***********************************************************************************/
class dispatcherInit {
	
	/**
	 * 路由分发-路由分发核心函数 
	 * 1. 判断是否开启分发
	 * 2. 获取request信息
	 * 3. 解析URI
	 */
	public function dispatcher() {
		$InitPHP_conf = InitPHP::getConfig();
		switch ($InitPHP_conf['isuri']) {
			case 'path' :
				$request = $this->getRequest();
				$this->parsePathUri($request);
				break;
			
			case 'rewrite' :
				$request = $this->getRequest();
				$this->parseRewriteUri($request);
				break;
			
			case 'html' :
				$request = $this->getRequest();
				$this->parseHtmlUri($request);
				break;
			
			default :
				return false;
				break;
		}
		return true;
	}
	
	/**
	 * 路由分发，获取Uri数据参数
	 * 1. 对Service变量中的uri进行过滤
	 * 2. 配合全局站点url处理request
	 * @return string
	 */
	private function getRequest() {
		$InitPHP_conf = InitPHP::getConfig();
		$filter_param = array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e');
		$uri = str_replace($filter_param, '', $_SERVER['REQUEST_URI']);
	    $posi = strpos($uri, '?');
    	if ($posi) $uri = substr($uri,0,$posi);
    	$urlArr = parse_url($InitPHP_conf['url']);
		$request = str_replace(trim($urlArr['path'], '/'),'', $uri);
		if (strpos($request, '.php')) {
			$request = explode('.php', $request);
			$request = $request[1];
		}
		return $request;
	}
	
	/**
	 * 解析Path Uri 
	 * 1. 解析index.php/user/new/username
	 * 2. 解析成数组，array()
	 * @param string $request
	 */
	private function parsePathUri($request) {
		$InitPHP_conf = InitPHP::getConfig();
		if (!$request) return false;
		$request =  trim($request, '/');
		if ($request == '') return false;
		$request =  explode('/', $request);
		if (!is_array($request) || count($request) == 0) return false;
		if ($InitPHP_conf['ismodule'] == true) { //是否开启模型模式
			if (isset($request[0])) $_GET['m'] = $request[0];
			if (isset($request[1])) $_GET['c'] = $request[1];
			if (isset($request[2])) $_GET['a'] = $request[2];
			unset($request[0], $request[1], $request[2]);
		} else {
			if (isset($request[0])) $_GET['c'] = $request[0];
			if (isset($request[1])) $_GET['a'] = $request[1];
			unset($request[0], $request[1]);
		}
		if (count($request) > 1) {
			$mark = 0;
			$val = $key = array();
			foreach($request as $value){
				$mark++;
				if ($mark % 2 == 0) {
					$val[] = $value;
				} else {
					$key[] = $value;
				}
			}
			if(count($key) !== count($val)) $val[] = NULL;
			$get = array_combine($key,$val);
			foreach($get as $key=>$value) $_GET[$key] = $value;
		}
		return $request;
	}
	
	/**
	 * 解析rewrite方式的路由
	 * 1. 解析index.php/user/new/username/?id=100
	 * 2. 解析成数组，array()
	 * @param string $request
	 */
	private function parseRewriteUri($request) {
		$InitPHP_conf = InitPHP::getConfig();
		if (!$request) return false;
		$request =  trim($request, '/');
		if ($request == '') return false;
		$request =  explode('/', $request);
		if (!is_array($request) || count($request) == 0) return false;
		if ($InitPHP_conf['ismodule'] == true) { //是否开启模型模式
			if (isset($request[0])) $_GET['m'] = $request[0];
			if (isset($request[1])) $_GET['c'] = $request[1];
			if (isset($request[2])) $_GET['a'] = $request[2];
		} else {
			if (isset($request[0])) $_GET['c'] = $request[0];
			if (isset($request[1])) $_GET['a'] = $request[1];
		}
		return $request;
	}
	
	/**
	 * 解析html方式的路由
	 * 1. 解析user-add.htm?uid=100
	 * 2. 解析成数组，array()
	 * @param string $request
	 */
	private function parseHtmlUri($request) {
		$InitPHP_conf = InitPHP::getConfig();
		if (!$request) return false;
		$request = trim($request, '/');
		$request = str_replace('.htm', '', $request);
		if ($request == '') return false;
		$request = explode('-', $request);
		if (!is_array($request) || count($request) == 0) return false;
		if ($InitPHP_conf['ismodule'] == true) { //是否开启模型模式
			if (isset($request[0])) $_GET['m'] = $request[0];
			if (isset($request[1])) $_GET['c'] = $request[1];
			if (isset($request[2])) $_GET['a'] = $request[2];
		} else {
			if (isset($request[0])) $_GET['c'] = $request[0];
			if (isset($request[1])) $_GET['a'] = $request[1];
		}
		return $request;		
	}
}