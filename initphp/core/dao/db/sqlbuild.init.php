<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-sqlbuild 数据字段安全封装类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
require_once("dbhandler.init.php");
class sqlbuildInit extends dbhandlerInit {

	/**
	 * SQL组装-组装INSERT语句 
	 * 返回：('key') VALUES ('value')
	 * DAO中使用方法：$this->dao->db->build_insert($val)
	 * @param  array $val 参数  array('key' => 'value')
	 * @return string
	 */
	public function build_insert($val) {
		if (!is_array($val) || empty($val)) return '';
		$temp_v = '(' . $this->build_implode($val). ')';
		$val = array_keys($val);
		$temp_k = '(' . $this->build_implode($val, 1). ')';
		return $temp_k . ' VALUES ' . $temp_v;
	}
	
	/**
	 * SQL组装-组装多条语句插入
	 * 返回：('key') VALUES ('value'),('value2')
	 * DAO中使用方法：$this->dao->db->build_insertmore($field, $data)
	 * @param array $field 字段
	 * @param array $data  对应的值，array(array('test1'),array('test2'))
	 * @return string
	 */
	public function build_insertmore($field, $data) {
		$field = ' (' . $this->build_implode($field, 1) . ') '; //字段组装
		$temp_data = array();
		$data = (array) $data;
		foreach ($data as $val) {
			$temp_data[] = '(' . $this->build_implode($val) . ')';
		}
		$temp_data = implode(',', $temp_data);
		return $field . ' VALUES ' . $temp_data;
	}
	
	/**
	 * SQL组装-组装UPDATE语句 
	 * 返回：SET name = 'aaaaa'
	 * DAO中使用方法：$this->dao->db->build_update($val)
	 * @param  array $val  array('key' => 'value')
	 * @return string `key` = 'value' 
	 */
	public function build_update($val) {
		if (!is_array($val) || empty($val)) return '';
		$temp = array();
		foreach ($val as $k => $v) {
			$temp[] = $this->build_kv($k, $v);
		}
		return 'SET ' . implode(',', $temp);
	}
	
	/**
	 * SQL组装-组装LIMIT语句
	 * 返回：LIMIT 0,10 
	 * DAO中使用方法：$this->dao->db->build_limit($start, $num = NULL)
	 * @param  int $start 开始
	 * @param  int $num   条数
	 * @return string
	 */
	public function build_limit($start, $num = NULL) {
		$start = (int) $start;
		$start = ($start < 0) ? 0 : $start;
		if ($num === NULL) {
			return 'LIMIT ' . $start;
		} else {
			$num = abs((int) $num);
			return 'LIMIT ' . $start .' ,'. $num;
		}
	}
	
	/**
	 * SQL组装-组装IN语句
	 * 返回：('1','2','3')
	 * DAO中使用方法：$this->dao->db->build_in($val)
	 * @param  array $val 数组值  例如：ID:array(1,2,3)
	 * @return string
	 */
	public function build_in($val) {
		$val = $this->build_implode($val);
		return ' IN (' . $val . ')';
	}
	
	/**
	 * SQL组装-组装AND符号的WHERE语句
	 * 支持复杂类型：例如:$val = array("name" => array("like" => "sdasd"));
	 * 返回：WHERE a = 'a' AND b = 'b'
	 * DAO中使用方法：$this->dao->db->build_where($val)
	 * @param array $val array('key' => 'val')
	 * @return string
	 */
	public function build_where($val) {
		if (!is_array($val) || empty($val)) return '';
		$temp = array();
		foreach ($val as $k => $v) {
			if (is_array($v)) {
				$ktmp = $this->build_escape($k, 1);
				if (array_keys($v) !== range(0, count($v) - 1)) {
					foreach($v as $op => $value) {
						$temp[] = $ktmp .' '. $op .' '. $this->build_escape($value);
					}	
				} else {
					$temp[] = $ktmp . $this->build_in($v);
				}
			}else{
				$temp[] = $this->build_kv($k, $v);
			}
		}
		return ' WHERE ' . implode(' AND ', $temp);
	}
	
	/**
	 * SQL组装-单个或数组参数过滤
	 * DAO中使用方法：$this->dao->db->build_escape($val, $iskey = 0)
	 * @param  string|array $val
	 * @param  int          $iskey 0-过滤value值，1-过滤字段
	 * @return string
	 */
	public function build_escape($val, $iskey = 0) {
		if (is_array($val)) {
			foreach ($val as $k => $v) {
				$val[$k] = trim($this->build_escape_single($v, $iskey));
			}
			return $val;
		} 
		return $this->build_escape_single($val, $iskey);
	}
	
	/**
	 * SQL组装-组装KEY=VALUE形式
	 * 返回：a = 'a'
	 * DAO中使用方法：$this->dao->db->build_kv($k, $v)
	 * @param  string $k KEY值
	 * @param  string $v VALUE值
	 * @return string
	 */
	public function build_kv($k, $v) {
		return $this->build_escape($k, 1) . ' = ' . $this->build_escape($v);
	}
	
	/**
	 * SQL组装-将数组值通过，隔开
	 * 返回：'1','2','3'
     * DAO中使用方法：$this->dao->db->build_implode($val, $iskey = 0)
	 * @param  array $val   值
	 * @param  int   $iskey 0-过滤value值，1-过滤字段
	 * @return string 
	 */
	public function build_implode($val, $iskey = 0) {
		if (!is_array($val) || empty($val)) return '';
		return implode(',', $this->build_escape($val, $iskey));
	}
	
	/**
	 * SQL组装-检查DAO中进来的数组参数是否key键存在
	 * DAO中使用方法：$this->dao->db->build_key($data, $fields)
	 * @param array $data  例如：array("username" => 'asdasd')
	 * @param string $fields  例如："username,password"
	 */
	public function build_key($data, $fields) {
		$fields = explode(',', $fields);
		$temp = array();
		foreach ($data as $key => $value) {
			if (in_array($key, $fields)) {
				$temp[$key] = $value;
			}
		}
		return $temp;
	}
	
	/**
	 * SQL组装-私有SQL过滤
	 * 
	 * @param  string $val 过滤的值
	 * @param  int    $iskey 0-过滤value值，1-过滤字段
	 * @return string
	 */
	private function build_escape_single($val, $iskey = 0) {
		if ($iskey === 0) {
			if (is_numeric($val)) {
				return " '" . $val . "' ";
			} else {
				return " '" . addslashes(stripslashes($val)) . "' ";
			}
		} else {
			$val = str_replace(array('`', ' '), '', $val);
			return ' `'.addslashes(stripslashes($val)).'` ';
		}
	}
}
