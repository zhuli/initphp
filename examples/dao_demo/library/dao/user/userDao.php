<?php
class userDao extends Dao {
	
	public $table_name = 'test';
	
	public function addUser($data) { 
		print_r($this->init_db('test2')->get_one_sql("select * from test2"));
		print_r($this->dao->db->get_one_sql("select * from test"));
			print_r($this->init_db('test2')->get_one_sql("select * from test2"));
		print_r($this->dao->db->get_one_sql("select * from test"));
			print_r($this->init_db('test2')->get_one_sql("select * from test2"));
		print_r($this->dao->db->get_one_sql("select * from test"));
		print_r($this->dao->db->get_one_sql("select * from test"));
			print_r($this->init_db('test2')->get_one_sql("select * from test2"));
		print_r($this->dao->db->get_one_sql("select * from test"));
	}
	
	
}