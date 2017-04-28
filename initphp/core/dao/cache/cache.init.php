<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-cache 数据缓存工厂类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *------------------------------------------------------------------------------- 
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
class cacheInit{
	//缓存类型 FILE-文件缓存类型 MEM-内存缓存类型 MYSQL-数据库缓存类 APC-APC缓存
	private $cache_type = array('FILE','MEM', 'MYSQL', 'APC', 'XCACHE', 'WINCACHE'); 
	private static $instance = array();  //单例模式获取缓存类
	public $db_handle; //数据库连接对象，MYSQL缓存用到
	private $page_cache_key, $page_cache_time, $page_cache_type;
	
	/**
	 * 缓存工厂-设置缓存 
	 * 1. 设置缓存的key值，value值,缓存时间和缓存类型 
	 * 2. 缓存时间设置为0，则是永久缓存
	 * 3. 缓存类型 暂时只支持memcache,mysql,文件缓存  
	 * DAO中使用方法：$this->dao->cache->set_cache($key, $value, $time = 0, $type = 'FILE')
	 * @param  string $key   缓存键值
	 * @param  string $value 缓存数据
	 * @param  string $time  缓存时间
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function set($key, $value, $time = 0, $type = 'FILE') { 
		$cache = $this->get_cache_handle($type);
		return $cache->set_cache($key, $value, $time);
	}
	
	/**
	 * 缓存工厂-获取缓存
	 * 缓存的KEY值和缓存类型
	 * DAO中使用方法：$this->dao->cache->get_cache($key,$type = 'FILE')
	 * @param  string $key   缓存键值
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function get($key, $type = 'FILE') {
		$cache = $this->get_cache_handle($type);
		return $cache->get_cache($key);
	}
	
	/**
	 * 缓存工厂-清除单个缓存
	 * 缓存的KEY值和缓存类型
	 * DAO中使用方法：$this->dao->clear($key, $type = 'FILE')
	 * @param  string $key   缓存键值
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function clear($key, $type = 'FILE') {
		$cache = $this->get_cache_handle($type);
		return $cache->clear($key);
	}
	
	/**
	 * 缓存工厂-清除所有缓存
	 * 缓存类型
	 * DAO中使用方法：$this->dao->clear_all($type = 'FILE')
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function clear_all($type = 'FILE') {
		$cache = $this->get_cache_handle($type);
		return $cache->clear_all();
	}
	
	/**
	 * 该接口获取缓存类接口
	 * 支持通过该函数直接调用缓存中的私用方法（除了set get clear clear_all之外）
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function get_cache($type = 'MEM') {
		return $this->get_cache_handle($type);
	}
	
	/**
	 * 缓存工厂-页面缓存开始标记
	 * 1. 设置缓存的key值，value值,缓存时间和缓存类型 
	 * 2. 缓存时间设置为0，则是永久缓存
	 * 3. 缓存类型 暂时只支持memcache,mysql,文件缓存  
	 * @param  string $key   缓存键值
	 * @param  string $value 缓存数据
	 * @param  string $time  缓存时间
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function page_cache_start($key, $time = 0, $type = 'FILE') {
		$this->page_cache_key 	= 'initphp_page_cache_' . $key;
		$this->page_cache_time 	= $time;
		$this->page_cache_type 	= $type;
		$page = $this->get($this->page_cache_key, $this->page_cache_type);
		if (!$page) {
			ob_start();
		} else {
			echo $page;
			exit;
		}
	}
	
	/**
	 * 缓存工厂-页面缓存结束标记
	 * @return 
	 */
	public function page_cache_end() {
		$this->set($this->page_cache_key, ob_get_contents(), $this->page_cache_time, $this->page_cache_type);
		$page = $this->get($this->page_cache_key, $this->page_cache_type);
		ob_end_clean(); //清空缓冲
		echo $page;
	}
	
	/**
	 * 缓存工厂-获取不同缓存类型的对象句柄
	 * @param  string $type  缓存类型
	 * @return obj
	 */
	private function get_cache_handle($type) {
		$InitPHP_conf = InitPHP::getConfig(); //需要设置文件缓存目录
		$type = strtoupper($type);
		$type = (in_array($type, $this->cache_type)) ? $type : 'FILE';
		switch ($type) {
			
			case 'FILE' :
				if (isset(cacheInit::$instance['filecache'])) return cacheInit::$instance['filecache'];
				$filecache = $this->load_cache('filecache.init.php', 'filecacheInit');
				$filepath = InitPHP::getAppPath($InitPHP_conf['cache']['filepath']);
				$filecache->set_cache_path($filepath);
				cacheInit::$instance['filecache'] = $filecache;
				return $filecache;
				break;
			
			case 'MEM' :
				if (isset(cacheInit::$instance['memcache'])) return cacheInit::$instance['memcache'];
				$mem = $this->load_cache('memcached.init.php', 'memcachedInit');
				$mem->add_server($InitPHP_conf['memcache']); //添加服务器
				cacheInit::$instance['memcache'] = $mem;
				return $mem;
				break;
			
			case 'MYSQL' :
				if (isset(cacheInit::$instance['mysqlcache'])) return cacheInit::$instance['mysqlcache'];
				$mysqlcache = $this->load_cache('mysqlcache.init.php', 'mysqlcacheInit');
				$mysqlcache->set_sql_handler($this->db_handle);
				cacheInit::$instance['mysqlcache'] = $mysqlcache;
				return $mysqlcache;
				break;
			
			case 'APC' :
				if (isset(cacheInit::$instance['apc'])) return cacheInit::$instance['apc'];
				$filecache = $this->load_cache('apc.init.php', 'apcInit');
				break;
			
			case 'XCACHE' :
				if (isset(cacheInit::$instance['xcache'])) return cacheInit::$instance['xcache'];
				$filecache = $this->load_cache('xcache.init.php', 'xcacheInit');
				break;
				
			case 'WINCACHE' :
				if (isset(cacheInit::$instance['wincache'])) return cacheInit::$instance['wincache'];
				$filecache = $this->load_cache('wincache.init.php', 'wincacheInit');
				break;
		}
	}
	
	/**
	 * 缓存工厂-加载不同缓存类文件
	 * @param  string $file  缓存文件名
	 * @param  string $class 缓存类名
	 * @return obj
	 */
	private function load_cache($file, $class) {
		if (cacheInit::$instance['require'][$file] !== TRUE) {
			require('driver/' . $file);
			cacheInit::$instance['require'][$file] = TRUE;
		}
		if (cacheInit::$instance['class'][$class] !== TRUE) {
			cacheInit::$instance['class'][$class] = TRUE;
			return new $class;
		} else {
			return cacheInit::$instance['class'][$class];
		}
	}
}
