<?php
class userService extends Service {
	
	
	public function addUser($user) {
		return $this->_getUserDao()->addUser($user);
	}
	
	public function getUser() {
		return "zhuli";
	}
	
	public function getAdd($username) {
		return array($username, 10);
	}
	
	/**
	 * @return userDao
	 */
	private function _getUserDao() {
		return InitPHP::getDao("user");
	}
}