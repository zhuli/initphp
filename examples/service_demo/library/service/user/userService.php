<?php
class userService extends Service {
	public function getUser() {
		return array("username" => 'hello', "age" => 10);
	}
}