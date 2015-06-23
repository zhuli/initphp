<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-db 常用SQL方法封装
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
require_once("sqlbuild.init.php");  
class dbInit extends sqlbuildInit {   

	/**
	 * 重写MYSQL中的QUERY，对SQL语句进行监控
	 * @param string $sql
	 */
	public function query($sql, $is_set_default = true) {
		$this->get_link_id($sql); //link_id获取
		$InitPHP_conf = InitPHP::getConfig();
		if($InitPHP_conf['is_debug']==true) $start   =   microtime();
		$query = $this->db->query($sql);
		if($InitPHP_conf['is_debug']==true) $end   =   microtime();
		//sql query debug		
		if($InitPHP_conf['is_debug']==true){
			$k= isset($InitPHP_conf['sqlcontrolarr']) ? count($InitPHP_conf['sqlcontrolarr']) : 0;
			if ($k < 50) {
				$InitPHP_conf['sqlcontrolarr'][$k]['sql']=$sql;
				$costTime=substr(($end-$start),0,7);
				$InitPHP_conf['sqlcontrolarr'][$k]['queryTime']=$costTime;
				$InitPHP_conf['sqlcontrolarr'][$k]['affectedRows']=$this->affected_rows();
				InitPHP::setConfig('sqlcontrolarr', $InitPHP_conf['sqlcontrolarr']);
			}
		}
	    if ($this->db->error()) {
            InitPHP::initError($this->db->error());
        }
		if ($is_set_default) $this->set_default_link_id(); //设置默认的link_id
		return $query;
	}
	
	/**
	 * 结果集中的行数
	 * DAO中使用方法：$this->dao->db->result($result, $num=1)
	 * @param $result 结果集
	 * @return array
	 */
	public function result($result, $num=1) {
		return $this->db->result($result, $num);
	}
	
	/**
	 * 从结果集中取得一行作为关联数组
	 * DAO中使用方法：$this->dao->db->fetch_assoc($result)
	 * @param $result 结果集
	 * @return array
	 */
	public function fetch_assoc($result) {
		return $this->db->fetch_assoc($result);
	}
	
	/**
	 * 从结果集中取得列信息并作为对象返回
	 * DAO中使用方法：$this->dao->db->fetch_fields($result)
	 * @param  $result 结果集
	 * @return array
	 */
	public function fetch_fields($result) {
		return $this->db->fetch_fields($result);
	}
	

	/**
	 * 结果集中的行数
	 * DAO中使用方法：$this->dao->db->num_rows($result)
	 * @param $result 结果集
	 * @return int
	 */
	public function num_rows($result) {
		return $this->db->num_rows($result);
	}
	
	/**
	 * 结果集中的字段数量
     * DAO中使用方法：$this->dao->db->num_fields($result)
	 * @param $result 结果集
	 * @return int
	 */
	public function num_fields($result) {
		return $this->db->num_fields($result);
	}
	
	/**
	 * 释放结果内存
	 * DAO中使用方法：$this->dao->db->free_result($result)
	 * @param obj $result 需要释放的对象
	 */
	public function free_result($result) {
		return $this->db->free_result($result);
	}
	
	/**
	 * 获取上一INSERT的ID值
     * DAO中使用方法：$this->dao->db->insert_id()
     * @param String $db 如果在多库连接的情况下，因为多库连接会自动切换db_link,所以
     * insert_id方法需要指定特定的DB，才能正常使用
	 * @return Int
	 */
	public function insert_id($db = "") {
		if ($db != "") {
			$this->get_link_id();
		}
		return $this->db->insert_id();
	}
	
	/**
	 * 前一次操作影响的记录数
	 * DAO中使用方法：$this->dao->db->affected_rows()
     * @param String $db 如果在多库连接的情况下，因为多库连接会自动切换db_link,所以
     * affected_rows方法需要指定特定的DB，才能正常使用
	 * @return int
	 */
	public function affected_rows($db = "") {
		if ($db != "") {
			$this->get_link_id();
		}
		return $this->db->affected_rows();
	}
	
	/**
	 * 关闭连接
	 * DAO中使用方法：$this->dao->db->close()
     * @param String $db 如果在多库连接的情况下，因为多库连接会自动切换db_link,所以
     * affected_rows方法需要指定特定的DB，才能正常使用
	 * @return bool
	 */
	public function close($db = "") {
		if ($db != "") {
			$this->get_link_id();
		}
		return $this->db->close();
	}
	
	/**
	 * 错误信息
	 * DAO中使用方法：$this->dao->db->error()
     * @param String $db 如果在多库连接的情况下，因为多库连接会自动切换db_link,所以
     * affected_rows方法需要指定特定的DB，才能正常使用
	 * @return string
	 */
	public function error($db = "") {
		if ($db != "") {
			$this->get_link_id();
		}
		return $this->db->error();
	}
	
	/**
	 * 开始事务操作
 	 * DAO中使用方法：$this->dao->db->transaction_start()
	 */
	public function transaction_start() {
		$this->query("START TRANSACTION");
		return true;
	}
	
	/**
	 * 提交事务
 	 * DAO中使用方法：$this->dao->db->transaction_commit()
	 */
	public function transaction_commit() {
		$this->query("COMMIT");
		return true;
	}
	
	/**
	 * 回滚事务
	 * DAO中使用方法：$this->dao->db->transaction_rollback()
	 */
	public function transaction_rollback() {
		$this->query("ROLLBACK"); 
		return true;
	}
	
	/** 
	 * SQL操作-插入一条数据
	 * DAO中使用方法：$this->dao->db->insert($data, $table_name)
	 * @param array  $data array('key值'=>'值')
	 * @param string $table_name 表名
	 * @return id
	 */
	public function insert($data, $table_name) {
		if (!is_array($data) || empty($data)) return 0;
		$data = $this->build_insert($data);
		$sql = sprintf("INSERT INTO %s %s", $table_name, $data);
		$result = $this->query($sql, false);
		if (!$result) return 0;
		$id = $this->insert_id();
		$this->set_default_link_id(); //设置默认的link_id
		return $id;
	}
	
	/**
	 * SQL操作-插入多条数据
	 * DAO中使用方法：$this->dao->db->insert_more($field, $data, $table_name)
	 * @param array $field 字段
	 * @param array $data  对应的值，array(array('test1'),array('test2'))
	 * @param string $table_name 表名
	 * @return id
	 */
	public function insert_more($field, $data, $table_name) {
		if (!is_array($data) || empty($data)) return false;
		if (!is_array($field) || empty($field)) return false;
		$sql = $this->build_insertmore($field, $data);
		$sql = sprintf("INSERT INTO %s %s", $table_name, $sql);
		return $this->query($sql);
	}
	
	/**
	 * SQL操作-根据主键id更新数据
	 * DAO中使用方法：$this->dao->db->update($id, $data, $table_name, $id_key = 'id')
	 * @param  int    $id 主键ID
	 * @param  array  $data 参数
	 * @param  string $table_name 表名
	 * @param  string $id_key 主键名
	 * @return bool
	 */
	public function update($id, $data, $table_name, $id_key = 'id') {
		$id = (int) $id;
		if ($id < 1) return false;
		$data = $this->build_update($data);
		$where = $this->build_where(array($id_key=>$id));
		$sql = sprintf("UPDATE %s %s %s", $table_name, $data, $where);
		return $this->query($sql);
	}
	
	/**
	 * SQL操作-根据字段更新数据
	 * DAO中使用方法：$this->dao->db->update_by_field($data, $field, $table_name)
	 * @param  array  $data 参数
	 * @param  array  $field 字段参数
	 * @param  string $table_name 表名
	 * @return bool
	 */
	public function update_by_field($data, $field, $table_name) {
		if (!is_array($data) || empty($data)) return false;
		if (!is_array($field) || empty($field)) return false;
		$data = $this->build_update($data);
		$field = $this->build_where($field);
		$sql = sprintf("UPDATE %s %s %s", $table_name, $data, $field);
		return $this->query($sql);
	}
	
	/**
	 * SQL操作-删除数据
	 * DAO中使用方法：$this->dao->db->delete($ids, $table_name, $id_key = 'id')
	 * @param  int|array $ids 单个id或者多个id
	 * @param  string $table_name 表名
	 * @param  string $id_key 主键名
	 * @return bool
	 */
	public function delete($ids, $table_name, $id_key = 'id') {
		if (is_array($ids)) {
			$ids = $this->build_in($ids);
			$sql = sprintf("DELETE FROM %s WHERE %s %s", $table_name, $id_key, $ids);
		} else {
			$where = $this->build_where(array($id_key=>$ids));
			$sql = sprintf("DELETE FROM %s %s", $table_name, $where);
		}
		return $this->query($sql);
	}
	
	/**
	 * SQL操作-通过条件语句删除数据
	 * DAO中使用方法：$this->dao->db->delete_by_field($field, $table_name)
	 * @param  array  $field 条件数组
	 * @param  string $table_name 表名
	 * @return bool
	 */
	public function delete_by_field($field, $table_name) {
		if (!is_array($field) || empty($field)) return false;
		$where = $this->build_where($field);
		$sql = sprintf("DELETE FROM %s %s", $table_name, $where);
		return $this->query($sql);
	}
	
	/**
	 * SQL操作-获取单条信息
	 * DAO中使用方法：$this->dao->db->get_one($id, $table_name, $id_key = 'id')
	 * @param int    $id 主键ID
	 * @param string $table_name 表名
	 * @param string $id_key 主键名称，默认id
	 * @return array
	 */
	public function get_one($id, $table_name, $id_key = 'id') {
		$id = (int) $id;
		if ($id < 1) return array(); 
		$where = $this->build_where(array($id_key=>$id));
		$sql = sprintf("SELECT * FROM %s %s LIMIT 1", $table_name, $where);
		$result = $this->query($sql, false);
		if (!$result) return false;
		$r = $this->fetch_assoc($result);
		$this->set_default_link_id(); //设置默认的link_id
		return $r;
	}
	
	/**
	 * SQL操作-通过条件语句获取一条信息
	 * DAO中使用方法：$this->dao->db->get_one_by_field($field, $table_name)
	 * @param  array  $field 条件数组 array('username' => 'username')
	 * @param  string $table_name 表名
	 * @return bool
	 */
	public function get_one_by_field($field, $table_name) {
		if (!is_array($field) || empty($field)) return array();
		$where = $this->build_where($field);
		$sql = sprintf("SELECT * FROM %s %s LIMIT 1", $table_name, $where);
		$result = $this->query($sql, false);
		if (!$result) return false;
		$r = $this->fetch_assoc($result);
		$this->set_default_link_id(); //设置默认的link_id
		return $r;
	}
	
	/**
	 * SQL操作-获取单条信息-sql语句方式
	 * DAO中使用方法：$this->dao->db->get_one_sql($sql)
	 * @param  string $sql 数据库语句
	 * @return array
	 */
	public function get_one_sql($sql) {
		$sql = trim($sql . ' ' .$this->build_limit(1));
		$result = $this->query($sql, false);
		if (!$result) return false;
		$r = $this->fetch_assoc($result);
		$this->set_default_link_id(); //设置默认的link_id
		return $r;
	}
	
	/**
	 * SQL操作-获取全部数据
	 * DAO中使用方法：$this->dao->db->get_all()
	 * @param string $table_name 表名
	 * @param array  $field 条件语句
	 * @param int    $num 分页参数
	 * @param int    $offest 获取总条数
	 * @param int    $key_id KEY值
	 * @param string $sort 排序键
	 * @return array array(数组数据，统计数)
	 */
	public function get_all($table_name, $num = 20, $offest = 0, $field = array(), $id_key = 'id', $sort = 'DESC') {
		$where = $this->build_where($field);
		$limit = $this->build_limit($offest, $num);
		$sql = sprintf("SELECT * FROM %s %s ORDER BY %s %s %s", $table_name, $where, $id_key, $sort, $limit);
		$result = $this->query($sql, false);
		if (!$result) return false;
		$temp = array();
		while ($row = $this->fetch_assoc($result)) {
			$temp[] = $row;
		}
		$count = $this->get_count($table_name, $field);
		$this->set_default_link_id(); //设置默认的link_id
		return array($temp, $count);
	}
	
	/**
	 * SQL操作-获取所有数据
	 * DAO中使用方法：$this->dao->db->get_all_sql($sql)
	 * @param string $sql SQL语句
	 * @return array
	 */
	public function get_all_sql($sql) {
		$sql = trim($sql);
		$result = $this->query($sql, false);
		if (!$result) return false;
		while ($row = $this->fetch_assoc($result)) {
			$temp[] = $row;
		}
		$this->set_default_link_id(); //设置默认的link_id
		return $temp;
	}
	
	/**
	 * SQL操作-获取数据总数
	 * DAO中使用方法：$this->dao->db->get_count($table_name, $field = array())
	 * @param  string $table_name 表名
	 * @param  array  $field 条件语句
	 * @return int
	 */
	public function get_count($table_name, $field = array()) {
		$where = $this->build_where($field);
		$sql = sprintf("SELECT COUNT(*) as count FROM %s %s LIMIT 1", $table_name, $where);
		$result = $this->query($sql, false);
		$result =  $this->fetch_assoc($result);
		$this->set_default_link_id(); //设置默认的link_id
		return $result['count'];
	}
	
}
