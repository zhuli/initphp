<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Dao-Nosql-Mongo 
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
class mongoInit {

	private $mongo; //mongo对象
	private $db; //db mongodb对象数据库
	private $collection; //集合，相当于数据表 
	
	/**
	 * 初始化Mongo
	 * $config = array(
	 * 'server' => ‘127.0.0.1' 服务器地址
	 * ‘port’   => '27017' 端口地址
	 * ‘option’ => array('connect' => true) 参数
	 * 'db_name'=> 'test' 数据库名称
	 * ‘username’=> 'zhuli' 数据库用户名
	 * ‘password’=> '123456' 数据库密码
	 * )
	 * Enter description here ...
	 * @param unknown_type $config
	 */
	public function init($config = array()) {
		if ($config['server'] == '')  $config['server'] = '127.0.0.1';
		if ($config['port'] == '')  $config['port'] = '27017';
		if (!$config['option']) $config['option'] = array('connect' => true);
		$server = 'mongodb://' . $config['server'] . ':' . $config['port'];
		$this->mongo = new Mongo($server, $config['option']);
		if ($config['db_name'] == '') $config['db_name'] = 'test';
		$this->db = $this->mongo->selectDB($config['db_name']);
		if ($config['username'] != '' && $config['password'] != '') 
			$this->db->authenticate($config['username'], $config['password']);
	}
	
	/**
	 * 选择一个集合，相当于选择一个数据表
	 * @param string $collection 集合名称
	 */
	public function selectCollection($collection) {
		return $this->collection = $this->db->selectCollection($collection);
	}
	
	/**
	 * 新增数据
	 * @param array $data 需要新增的数据 例如：array('title' => '1000', 'username' => 'xcxx')
	 * @param array $option 参数
	 */
	public function insert($data, $option = array()) {
		return $this->collection->insert($data, $option);
	}
	
	/**
	 * 批量新增数据
 	 * @param array $data 需要新增的数据 例如：array(0=>array('title' => '1000', 'username' => 'xcxx'))
	 * @param array $option 参数
	 */
	public function batchInsert($data, $option = array()) {
		return $this->collection->batchInsert($data, $option);
	}
	
	/**
	 * 保存数据，如果已经存在在库中，则更新，不存在，则新增
 	 * @param array $data 需要新增的数据 例如：array(0=>array('title' => '1000', 'username' => 'xcxx'))
	 * @param array $option 参数
	 */
	public function save($data, $option = array()) {
		return $this->collection->save($data, $option);
	}
	
	/**
	 * 根据条件移除
 	 * @param array $query  条件 例如：array(('title' => '1000'))
	 * @param array $option 参数
	 */
	public function remove($query, $option = array()) {
		return $this->collection->remove($query, $option);
	}
	
	/**
	 * 根据条件更新数据
 	 * @param array $query  条件 例如：array(('title' => '1000'))
 	 * @param array $data   需要更新的数据 例如：array(0=>array('title' => '1000', 'username' => 'xcxx'))
	 * @param array $option 参数
	 */
	public function update($query, $data, $option = array()) {
		return $this->collection->update($query, $data, $option);
	}
	
	/**
	 * 根据条件查找一条数据
 	 * @param array $query  条件 例如：array(('title' => '1000'))
	 * @param array $fields 参数
	 */
	public function findOne($query, $fields = array()) {
		return $this->collection->findOne($query, $fields);
	}
	
	/**
	 * 根据条件查找多条数据
	 * @param array $query 查询条件
	 * @param array $sort  排序条件 array('age' => -1, 'username' => 1)
	 * @param int   $limit 页面
	 * @param int   $limit 查询到的数据条数
	 * @param array $fields返回的字段
	 */
	public function find($query, $sort = array(), $skip = 0, $limit = 0, $fields = array()) {
		$cursor = $this->collection->find($query, $fields);
		if ($sort)  $cursor->sort($sort);
		if ($skip)  $cursor->skip($skip);
        if ($limit) $cursor->limit($limit);
		return iterator_to_array($cursor);
	}
	
	/**
	 * 数据统计
	 */
	public function count() {
		return $this->collection->count();
	}
	
	/**
	 * 错误信息
	 */
	public function error() {
		return $this->db->lastError();
	}
	
	/**
	 * 获取集合对象
	 */
	public function getCollection() {
		return $this->collection;
	}
	
	/**
	 * 获取DB对象
	 */
	public function getDb() {
		return $this->db;
	}
	
	
}