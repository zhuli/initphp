<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-filecahce 文件缓存
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
class filecacheInit {
	
	private $cache_path = '.'; //缓存路径
	
	/**
	 * 文件缓存-设置缓存
	 * 设置缓存名称，数据，和缓存时间
	 * @param string $filename 缓存名
	 * @param array  $data     缓存数据
	 */
	public function set_cache($filename, $data, $time = 0) {
		 $filename = $this->get_cache_filename($filename);
		 @file_put_contents($filename, '<?php exit;?>' . time() .'('.$time.')' .  serialize($data));
		 clearstatcache();
		 return true;
	}
	
	/**
	 * 文件缓存-获取缓存
	 * 获取缓存文件，分离出缓存开始时间和缓存时间
	 * 返回分离后的缓存数据，解序列化
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function get_cache($filename) {
		$filename = $this->get_cache_filename($filename);
		/* 缓存不存在的情况 */
		if (!file_exists($filename)) return false; 
		$data = file_get_contents($filename); //获取缓存
		/* 缓存过期的情况 */
		$filetime = substr($data, 13, 10);
		$pos = strpos($data, ')');
		$cachetime = substr($data, 24, $pos - 24);
		$data  = substr($data, $pos +1);
		if ($cachetime == 0) return @unserialize($data);
		if (time() > ($filetime + $cachetime)) {
			@unlink($filename);
			return false; //缓存过期
		}
        return @unserialize($data);
	}
	
	/**
	 * 文件缓存-清除缓存
	 * 删除缓存文件
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function clear($filename) {
		$filename = $this->get_cache_filename($filename);
		if (!file_exists($filename)) return true;
		@unlink($filename);
		return true;
	}
	
	/**
	 * 文件缓存-清除全部缓存
	 * 删除整个缓存文件夹文件，一般情况下不建议使用
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function clear_all() {
		@set_time_limit(3600);
		$path = opendir($this->cache_path);		
		while (false !== ($filename = readdir($path))) {
			if ($filename !== '.' && $filename !== '..') {
   				@unlink($this->cache_path . '/' .$filename);
			}
		}
		closedir($path);
		return true;
	}
	
	/**
	 * 设置文件缓存路径
	 * 在配置文件中配置了该缓存文件
	 * $InitPHP_conf['cache']['filepath'] = 'data/filecache';
	 * @param  string $path 路径
	 * @return string
	 */
	public function set_cache_path($path) {
		return $this->cache_path = $path;
	}
	
	/**
	 * 获取缓存文件名
	 * @param  string $filename 缓存名
	 * @return string
	 */
	private function get_cache_filename($filename) {
		$filename = md5($filename); //文件名MD5加密
		$filename = $this->cache_path .'/'. $filename . '.php';
		return $filename;
	}
}
