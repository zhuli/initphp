<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   - 工具库-队列
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25
***********************************************************************************/
class queueInit {
	
	private static $queue = array(); //存放队列数据
	
	/**
 	 * 队列-设置值
 	 * 使用方法：$this->getUtil('queue')->set('ccccccc');
 	 * @return string   
	 */
	public function set($val) {
		array_unshift(self::$queue, $val);
		return true;
	}
	
	/**
 	 * 队列-从队列中获取一个最早放进队列的值
  	 * 使用方法：$this->getUtil('queue')->get();
 	 * @return string   
	 */
	public function get() {
		return array_pop(self::$queue);
	}
	
	/**
 	 * 队列-队列中总共有多少值
   	 * 使用方法：$this->getUtil('queue')->count();
 	 * @return string   
	 */
	public function count() {
		return count(self::$queue);
	}
	
	/**
 	 * 队列-清空队列数据
     * 使用方法：$this->getUtil('queue')->clear();
 	 * @return string   
	 */
	public function clear() {
		return self::$queue = array();
	}
}