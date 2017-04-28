<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-APC缓存 不适合频繁写入的缓存数据
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
class apcInit {
	
	/**
	 * Apc缓存-设置缓存
	 * 设置缓存key，value和缓存时间
	 * @param  string $key   KEY值
	 * @param  string $value 值
	 * @param  string $time  缓存时间
	 */
	public function set_cache($key, $value, $time = 0) { 
		if ($time == 0) $time = null; //null情况下永久缓存
		return apc_store($key, $value, $time);;
	}
	
	
	/**
	 * Apc缓存-获取缓存
	 * 通过KEY获取缓存数据
	 * @param  string $key   KEY值
	 */
	public function get_cache($key) {
		return apc_fetch($key);
	}
	
	/**
	 * Apc缓存-清除一个缓存
	 * 从memcache中删除一条缓存
	 * @param  string $key   KEY值
	 */
	public function clear($key) {
		return apc_delete($key);
	}
	
	/**
	 * Apc缓存-清空所有缓存
	 * 不建议使用该功能
	 * @return
	 */
	public function clear_all() {
		apc_clear_cache('user'); //清除用户缓存
		return apc_clear_cache(); //清楚缓存
	}
	
	/**
	 * 检查APC缓存是否存在
	 * @param  string $key   KEY值
	 */
	public function exists($key) {
		return apc_exists($key);
	}
	
	/**
	 * 字段自增-用于记数
	 * @param string $key  KEY值
	 * @param int    $step 新增的step值
	 */
	public function inc($key, $step) {
		return apc_inc($key, (int) $step);
	}
	
	/**
	 * 字段自减-用于记数
	 * @param string $key  KEY值
	 * @param int    $step 新增的step值
	 */
	public function dec($key, $step) {
		return apc_dec($key, (int) $step);
	}
	
	/**
	 * 返回APC缓存信息
	 */
	public function info() {
		return apc_cache_info();
	}
}