<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-mysqlcache 数据库缓存
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
 *************************************************************************
 *	CREATE TABLE `initphp_mysqlcache` (
 *	 `id` int(10) NOT NULL auto_increment,
 *	 `k` varchar(255) NOT NULL default '',
 *   `v` text NOT NULL default '',
 *   `dtime` int(10) NOT NULL default '0',
 *   `cachetime` int(10) NOT NULL default '0',
 *   PRIMARY KEY  (`id`),
 *   KEY `k` (`k`)
 *  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
***********************************************************************************/
class mysqlcacheInit {

	private $sql;

	/**
	 * MYSQL缓存-设置缓存
	 * 检查缓存是否已经存在，如果不存在，则设置，存在的话则更新缓存
	 * @param string $key    缓存名 
	 * @param array  $value  缓存数据  
	 * @return
	 */
	public function set_cache($key, $value, $cachetime) {
		$key = $this->get_cache_key($key);
		$time = time();
		$select_sql = sprintf("SELECT * FROM initphp_mysqlcache WHERE k = %s LIMIT 1", $this->sql->build_escape($key));
		$query  = $this->sql->query($select_sql);
		$result = $this->sql->fetch_assoc($query);
		if ($result) {
			$sql = sprintf("UPDATE initphp_mysqlcache set v = %s, dtime = %d, cachetime = %d WHERE k = %s", 
				$this->sql->build_escape(base64_encode(@serialize($value))),
				$time,
				(int) $cachetime,
				$this->sql->build_escape($key)
			);
		} else {
			$sql = sprintf("INSERT INTO initphp_mysqlcache (k, v, dtime, cachetime) VALUES (%s, %s, %d, %d)", 
				$this->sql->build_escape($key), 
				$this->sql->build_escape(base64_encode(@serialize($value))),
				$time,
				(int) $cachetime
			);
		}
		return $this->sql->query($sql);
	}
	
	/**
	 * MYSQL缓存-获取缓存
	 * 从数据库中读取缓存数据，如果缓存数据不存在，则返回false
	 * 缓存数据存在，如果过期则删除缓存，返回false，没过期或者永久，返回值
	 * @param string $key   缓存名
	 * @param array  $value 缓存数据
	 * @return
	 */
	public function get_cache($key) {
		$key = $this->get_cache_key($key);
		$select_sql = sprintf("SELECT * FROM initphp_mysqlcache WHERE k = %s LIMIT 1", $this->sql->build_escape($key));
		$query = $this->sql->query($select_sql);
		$result = $this->sql->fetch_assoc($query);
		if (!$result) return false;
		if ($result['cachetime'] == 0) return @unserialize(base64_decode($result['v']));
		if ((time() > ($result['dtime'] + $result['cachetime']))) {
			$delete_sql = sprintf("DELETE FROM initphp_mysqlcache WHERE k = %s ", $this->sql->build_escape($key));
			$this->sql->query($delete_sql); //缓存过期，则删除缓存
			return false; //过期
		}
		return @unserialize(base64_decode($result['v']));
	}
	
	/**
	 * MYSQL缓存-清除缓存
	 * @param string $key   缓存名
	 * @return
	 */
	public function clear($key) {
		$key = $this->get_cache_key($key);
		$sql = sprintf("DELETE FROM initphp_mysqlcache WHERE k = %s", $this->sql->build_escape($key));
		return $this->sql->query($sql);
	}
	
	/**
	 * MYSQL缓存-清除所有缓存
	 * 一般情况下，请慎用清除所有缓存功能
	 * @return
	 */
	public function clear_all() {
		$sql = "TRUNCATE TABLE initphp_mysqlcache";
		return $this->sql->query($sql);
	}
	
	/**
	 * MYSQL缓存-获取DB handler
	 * DB handler 是根据InitPHP开源框架自带的SQL连接来执行
	 * @return
	 */
	public function set_sql_handler($obj) {
		$this->sql = $obj;
	}
	
	/**
	 * MYSQL缓存-获取缓存KEY值
	 * @param  string $key   缓存名
	 * @return string
	 */
	private function get_cache_key($key) {
		return md5(trim($key));
	}
}
