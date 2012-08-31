<?php
class userService extends Service {
	
	public function addUser($data) {
		$data = $this->_cookUserData($data);
		$userDao = InitPHP::getDao("user", "user"); //调用DAO
		return $userDao->addUser($data); //调用接口
	}
	
	private function _cookUserData($data) {
		$field = array(
			array('username', ''),
			array('age', 'int')
		);
		return $this->service->parse_data($field, $data);
	}
}