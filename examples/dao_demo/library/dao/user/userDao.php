<?php
class userDao extends Dao {
	
	public $table_name = 'test';
	
	public function addUser($data) {
		//$this->dao->db->insert($data, $this->table_name);
		$this->init_db()->insert($data, $this->table_name);
	}
	
}