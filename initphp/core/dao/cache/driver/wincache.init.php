<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-WINCACHE缓存
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
class wincacheInit {
	
	/**
	 * Xcache缓存-设置缓存
	 * 设置缓存key，value和缓存时间
	 * @param  string $key   KEY值
	 * @param  string $value 值
	 * @param  string $time  缓存时间
	 */
	public function set_cache($key, $value, $time = 0) { 
		return wincache_ucache_set($key, $value, $time);
	}
	
	/**
	 * Xcache缓存-获取缓存
	 * 通过KEY获取缓存数据
	 * @param  string $key   KEY值
	 */
	public function get_cache($key) {
		return wincache_ucache_get($key);
	}
	
	/**
	 * Xcache缓存-清除一个缓存
	 * 从memcache中删除一条缓存
	 * @param  string $key   KEY值
	 */
	public function clear($key) {
		return wincache_ucache_delete($key);
	}
	
	/**
	 * Xcache缓存-清空所有缓存
	 * 不建议使用该功能
	 * @return
	 */
	public function clear_all() {
		return wincache_ucache_clear();
	}
	
}