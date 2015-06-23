<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-一致性HASH
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/ 
class hashInit {

	private static $hash_table = array(); //hash_table，存放hash对应值
	private static $hash_list = array(); //存放值
	private $is_init = 1; //hash_table初始化-0关闭，1开启，当初始化完毕之后，关闭该功能
	private $filename = 'data/hash_table.php'; //hash_table缓存路径
	
	/**
	 *	一致性hash：添加节点
	 *  只有当且仅当开启is_init初始化hash_table的时候
	 *  hash_table文件才会生成
	 *  @param  string  $node    字符串
	 *  @param  string  $num     虚拟节点
	 *  @return int 
	 */
	public function add_node($node, $num = 0) { 
		if ($this->is_init == 1) {
			if (is_array($node)) { 
				foreach ($node as $string) {
					$key = $this->hash_md5($string);
					self::$hash_table[$this->hash_crc($key)] = array(
						$key, 
						$string
					);
					$this->add_virtual_node($string, $num);
					self::$hash_list[] = $this->hash_crc($key);
				}
				sort(self::$hash_list);
			}
			$table = '$hash_table = ' . var_export(self::$hash_table, TRUE) . ';';
			$list = '$hash_list = ' . var_export(self::$hash_list, TRUE);
			$value = '<?php ' . $table . $list . '?>';
			@file_put_contents($this->filename, $value);
		}
		return true;
	}
	
	/**
	 *	一致性hash：获取hash对应的节点值
	 *  @param  string  $node    字符串
	 *  @return int
	 */
	public function get_node($string) {
		$key     = $this->hash_md5($string);
		$key_val = $this->hash_crc($key);
		$result = $start = 0;
		if (empty(self::$hash_table) && empty(self::$hash_list)) {
			include_once($this->filename);
			self::$hash_table = $hash_table;
			self::$hash_list  = $hash_list;
		}
		foreach (self::$hash_list as $val) {
			if ($start == 0) $result = $val;
			if ($key_val < $val) {
				$result = $val;
				break;
			} 
			$start = 1; 
		}
		return self::$hash_table[$result][1];
	}
	
	/**
	 *	一致性hash：生成虚拟节点
	 *  @param  string  $string  字符串
	 *  @param  string  $num     虚拟节点
	 *  @return int
	 */
	public function add_virtual_node($string, $num) {
		$num = (int) $num;
		if ($num < 1) return false;
		for ($i=0; $i<$num; $i++) {
			$key = $this->hash_md5($string . '#' . $i);
			self::$hash_table[$this->hash_crc($key)] = array($key, $string);
			self::$hash_list[] = $this->hash_crc($key);
		}
	}

	/**
	 *	一致性hash，计算一个字符串的 crc32 多项式
	 *  @param  string  $string  字符串
	 *  @return int
	 */
	private function hash_crc($string) {
		return crc32($string);
	}
	
	/**
	 *	一致性hash：MD5加密得到KEY值
	 *  @param  string  $string  字符串
	 *  @return int
	 */
	private function hash_md5($string) {
		return md5($string);
	}
}

