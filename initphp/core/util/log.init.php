<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   - 工具库-日
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25
 ***********************************************************************************/
class logInit {

	private $default_file_size = '1024000'; //默认日志文件大小

	/**
	 * 写日志-直接写入日志文件或者邮件
	 * 使用方法：$this->getUtil('log')->write('日志内容');
	 * @param  string  $message  日志信息
	 * @param  string  $log_type 日志类型   ERROR  WARN  DEBUG  INFO
	 * @return
	 */
	public function write($message, $log_type = 'DEBUG') {
		$log_path = $this->get_file_log_name();
		if(is_file($log_path) && ($this->default_file_size < filesize($log_path)) ) {
			rename($log_path, dirname($log_path).'/'.time().'-Bak-'.basename($log_path));
		}
		$message = $this->get_message($message, $log_type);
		error_log($message, 3, $log_path, '');
	}

	/**
	 * 写日志-获取文件日志名称
	 * @return string
	 */
	private function get_file_log_name() {
		$config = InitPHP::getConfig();
		return $config['log_dir'] .  $this->_errorLogFileName();
	}

	/**
	 * 写日志-组装message信息
	 * @param  string  $message  日志信息
	 * @param  string  $log_type 日志类型
	 * @return string
	 */
	private function get_message($message, $log_type) {
		return  date("Y-m-d H:i:s") . " [{$log_type}] : {$message}\r\n";
	}

	/**
	 *
	 * @return string
	 */
	private function _errorLogFileName(){
		return "initphp_log_" . date('Y-m-d').'.log';
	}
}
