<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Controller-request Http请求类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class requestInit {
	
	/**
	 * Request-获取POST信息
	 *  Controller中使用方法：$this->controller->get_post($name = '')
	 * @param  string $name POST的键值名称
	 * @return string
	 */
	public function get_post($name = '') {
		if (empty($name)) return $_POST;
		return (isset($_POST[$name])) ? $_POST[$name] : '';
	}
	
	/**
	 * Request-获取GET方法的值
	 *  Controller中使用方法：$this->controller->get_get($name = '')
	 * @param  string $name GET的键值名称
	 * @return string
	 */
	public function get_get($name = '') {
		if (empty($name)) return $_GET;
		return (isset($_GET[$name])) ? $_GET[$name] : '';
	}
	
	/**
	 * Request-获取COOKIE信息
	 *  Controller中使用方法：$this->controller->get_cookie($name = '')
	 * @param  string $name COOKIE的键值名称
	 * @return string
	 */
	public function get_cookie($name = '') {
		if ($name == '') return $_COOKIE;
		return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : '';
	}
	
	/**
	 * Request-获取SESSION信息
	 *  Controller中使用方法：$this->controller->get_session
	 * @param  string $name SESSION的键值名称
	 * @return string
	 */
	public function get_session($name = '') {
		if ($name == '') return $_SESSION;
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : '';
	}
	
	/**
	 * Request-获取ENV信息
	 *  Controller中使用方法：$this->controller->get_env($name = '')
	 * @param  string $name ENV的键值名称
	 * @return string
	 */
	public function get_env($name = '') {
		if ($name == '') return $_ENV;
		return (isset($_ENV[$name])) ? $_ENV[$name] : '';
	}
	
	/**
	 * Request-获取SERVICE信息
	 *  Controller中使用方法：$this->controller->get_service
	 * @param  string $name SERVER的键值名称
	 * @return string
	 */
	public function get_service($name = '') {
		if ($name == '') return $_SERVER;
		return (isset($_SERVER[$name])) ? $_SERVER[$name] : '';
	}
	
	/**
	 *	Request-获取当前正在执行脚本的文件名
	 *  Controller中使用方法：$this->controller->get_php_self()
	 *  @return string
	 */
	public function get_php_self() {
		return $this->get_service('PHP_SELF');
	}
	
	/**
	 *	Request-获取当前正在执行脚本的文件
	 *  Controller中使用方法：$this->controller->get_service_name()
	 *  @return string
	 */
	public function get_service_name() {
		return $this->get_service('SERVER_NAME');
	}
	
	/**
	 *	Request-获取请求时间
	 *  Controller中使用方法：$this->controller->get_request_time()
	 *  @return int
	 */
	public function get_request_time() {
		return $this->get_service('REQUEST_TIME');
	}
	
	/**
	 * Request-获取useragent信息
	 *  Controller中使用方法：$this->controller->get_useragent()
	 * @return string
	 */
	public function get_useragent() {
		return $this->get_service('HTTP_USER_AGENT');	
	}	
	
	/**
	 * Request-获取URI信息
	 *  Controller中使用方法：$this->controller->get_uri()
	 * @return string
	 */
	public function get_uri() {
		return $this->get_service('REQUEST_URI');
	}
	
	/**
	 * Request-判断是否为POST方法提交
	 *  Controller中使用方法：$this->controller->is_post()
	 * @return bool
	 */
	public function is_post() {
		return (strtolower($this->get_service('REQUEST_METHOD')) == 'post') ? true : false;
	}
	
	/**
	 * Request-判断是否为GET方法提交
	 *  Controller中使用方法：$this->controller->is_get()
	 * @return bool
	 */
	public function is_get() {
		return (strtolower($this->get_service('REQUEST_METHOD')) == 'get') ? true : false;
	}
	
	/**
	 * Request-判断是否为AJAX方式提交
	 *  Controller中使用方法：$this->controller->is_ajax()
	 * @return bool
	 */
	public function is_ajax() {
		if ($this->get_service('HTTP_X_REQUESTED_WITH') && strtolower($this->get_service('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') return true;
		if ($this->get_post('initphp_ajax') || $this->get_get('initphp_ajax')) return true; //程序中自定义AJAX标识
		return false;
	}

	/**
     * HTTP请求类-获取用户IP
     * 
     * 1. 使用方法【Controller】：<code>$this->controller->get_ip()</code>
     * @return string
     */
    public function get_ip() {
        static $realip = null;
        if (null !== $realip) {
            return $realip;
        }
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        // 处理多层代理的情况
        if (false !== strpos($realip, ',')) {
            $realip = reset(explode(',', $realip));
        }
        // IP地址合法验证
        $realip = filter_var($realip, FILTER_VALIDATE_IP, null);
        if (false === $realip) {
            return '0.0.0.0';   // unknown
        }
        return $realip;
    }
	
}
