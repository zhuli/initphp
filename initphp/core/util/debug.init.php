<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 2.0 国产PHP开发框架  - 工具库-debug调试
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2011-10-09
***********************************************************************************/
class debugInit {

	public static $mark_arr = array(); //存放时间的静态变量
	
	/**
	 * debug-BUG调试工具-打印出信息
	 * 使用方法：$this->getUtil('debug')->dump($data, $isexit = 0)
	 * @param  string  $data   参数
	 * @param  int     $isexit 是否跳出
	 * @return 
	 */
	public function dump($data, $isexit = 0) {
		echo '<pre>';
		if (is_array($data)) {
			print_r($data);
		} else {
			echo $data;
		}
		echo '</pre>';
		if ($isexit) exit;
	}
	
	/**
	 * debug-BUG调试工具-程序标记
	 * 使用方法：$this->getUtil('debug')->mark($name)
	 * @param  string  $name 开始和结束时间的标记名称
	 * @return 
	 */
	public function mark($name) {
		self::$mark_arr['time'][$name][] = microtime(TRUE);
		self::$mark_arr['memory'][$name][] = memory_get_usage();
		return self::$mark_arr;
	}
	
	/**
	 * debug-BUG调试工具-计算程序段使用的时间
	 * 使用方法：$this->getUtil('debug')->use_time($name, $decimal = 6)
	 * @param  string  $name 开始和结束时间的标记名称
	 * @param  string  $decimal 小数位数
	 * @return 
	 */
	public function use_time($name, $decimal = 6) {
		if (!isset(self::$mark_arr['time'][$name][1])) {
			self::$mark_arr['time'][$name][1] = microtime(TRUE);
		}
		return number_format(self::$mark_arr['time'][$name][1] - self::$mark_arr['time'][$name][0], $decimal);
	}
	
	/**
	 * debug-BUG调试工具-计算程序段计算内存使用峰值
	 * 使用方法：$this->getUtil('debug')->use_memory($name)
	 * @param  string  $name 开始和结束时间的标记名称
	 * @return 
	 */
	public function use_memory($name) {
		if (!isset(self::$mark_arr['memory'][$name][1])) {
			self::$mark_arr['memory'][$name][1] = memory_get_usage();
		}
		return number_format(self::$mark_arr['memory'][$name][1] - self::$mark_arr['memory'][$name][0]);
	}
	
}
