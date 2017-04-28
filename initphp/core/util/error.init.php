<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   - 工具库-错误处理
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25
***********************************************************************************/
class errorInit {
	
	private $error_data = array(); //error容器
	private $error_type = array('html', 'text', 'json', 'xml', 'array'); //error类型数组
	
	/**
	 *	Error机制 添加一个error
	 *  添加错误信息，不会直接输出，直到调用send_error的时候才会输出所有的错误信息
	 *  使用方法：$this->getUtil('error')->add_error('aaaa');
	 * 	@param  string   $error_message  错误信息
	 *  @return object
	 */
	public function add_error($error_message) {
		$this->error_data[] = $error_message;
	}
	
	/**
	 *	Error机制 输出一个error
	 *  输出错误信息，可以选择各种输出方式，json，xml，json,html
	 *  add_error的错误信息也会被一起输出
	 *	使用方法：$this->getUtil('error')->send_error();
	 * 	@param  string   $error_message  错误信息
	 * 	@param  string   $error_type     错误类型
	 *  @return object
	 */
	public function send_error($error_message, $error_type = 'json') {
		$this->error_data[] = $error_message;
		$error_type = strtolower($error_type);
		if (!in_array($error_type, $this->error_type)) $error_type = 'json';
		$this->display($error_type);
	}
	
	/**
	 *	Error机制 私有函数，error输出
	 * 	@param  string   $error_type     错误类型
	 *  @return object
	 */
	private function display($error_type) {
		$InitPHP_conf = InitPHP::getConfig();
		if ($error_type == 'text') {
			$error = implode("\r\t", $this->error_data);
			exit($error);
		} elseif ($error_type == 'json') {
			exit(json_encode($this->error_data));
		} elseif ($error_type == 'xml') {
			$xml = '<?xml version="1.0" encoding="utf-8"?>';
			$xml .= '<return>';
				foreach ($this->error_data as $v) {
					$xml .= '<error>' .$v. '</error>';
				}
			$xml .= '</return>';
		 	exit($xml);
		} elseif ($error_type == 'array') {
			exit(var_export($this->error_data));
		} elseif ($error_type == 'html') {
			$error = $this->error_data;
			$template = InitPHP::getAppPath($InitPHP_conf['error']['template']);
			if ($template) {
				if (!file_exists($template)) InitPHP::initError('error template is not exist');
				@include $template;
			} else {
				InitPHP::initError('please set error template in initphp.conf.php');
			}
			//扩展HTML错误输出
			exit;
		}
	}
}