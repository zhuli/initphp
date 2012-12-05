<?php
class userService extends Service {
	
	
	public function addUser($user) {
		return $this->_getUserDao()->addUser($user);
	}
	
	/**
	 * @return userDao
	 */
	private function _getUserDao() {
		return InitPHP::getDao("user");
	}
}