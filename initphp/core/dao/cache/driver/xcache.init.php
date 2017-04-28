<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-XCACHE缓存
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
class xcacheInit {
	
	/**
	 * Xcache缓存-设置缓存
	 * 设置缓存key，value和缓存时间
	 * @param  string $key   KEY值
	 * @param  string $value 值
	 * @param  string $time  缓存时间
	 */
	public function set_cache($key, $value, $time = 0) { 
		return xcache_set($key, $value, $time);;
	}
	
	/**
	 * Xcache缓存-获取缓存
	 * 通过KEY获取缓存数据
	 * @param  string $key   KEY值
	 */
	public function get_cache($key) {
		return xcache_get($key);
	}
	
	/**
	 * Xcache缓存-清除一个缓存
	 * 从memcache中删除一条缓存
	 * @param  string $key   KEY值
	 */
	public function clear($key) {
		return xcache_unset($key);
	}
	
	/**
	 * Xcache缓存-清空所有缓存
	 * 不建议使用该功能
	 * @return
	 */
	public function clear_all() {
		$tmp['user'] = isset($_SERVER['PHP_AUTH_USER']) ? null : $_SERVER['PHP_AUTH_USER'];
		$tmp['pwd'] = isset($_SERVER['PHP_AUTH_PW']) ? null : $_SERVER['PHP_AUTH_PW'];
		$_SERVER['PHP_AUTH_USER'] = $this->authUser;
		$_SERVER['PHP_AUTH_PW'] = $this->authPwd;
		$max = xcache_count(XC_TYPE_VAR);
		for ($i = 0; $i < $max; $i++) {
			xcache_clear_cache(XC_TYPE_VAR, $i);
		}
		$_SERVER['PHP_AUTH_USER'] = $tmp['user'];
		$_SERVER['PHP_AUTH_PW'] = $tmp['pwd'];
		return true;
	}
	
	/**
	 * Xcache验证是否存在
	 * @param  string $key   KEY值
	 */
	public function exists($key) {
		return xcache_isset($key);
	}
}