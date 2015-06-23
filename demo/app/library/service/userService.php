<?php
/**
 * DEMO的Service测试
 * @author zhuli
 */
class userService extends Service {

	/**
	 * @var userDao
	 */
	private $userDao;

	public function getUser() {
		return "initphp";
	}

	public function getUserInfo($username, $age) {
		return "Username:" . $username . " age:" . $age;
	}

	/**
	 * 创建一个用户
	 */
	public function createUser($user) {
		$this->userDao = InitPHP::getDao("user");
		return $this->userDao->addUser($user);
	}

}