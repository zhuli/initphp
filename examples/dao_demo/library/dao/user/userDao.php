<?php
class userDao extends Dao {
	
	public $table_name = 'test';
	
	public function addUser($data) { 
		//$this->dao->db->insert($data, $this->table_name);
		echo $this->init_db()->insert($data, $this->table_name);
		//print_r($this->init_db()->get_all($this->table_name));
	}
	
}