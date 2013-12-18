<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.3 国产PHP开发框架  Dao-Nosql
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
class nosqlInit {

	private static $instance = array();  //单例模式获取nosql类
	private $nosql_type = array('MONGO', 'REDIS');
	
	/**
	 * 获取Nosql对象
	 * @param string $type
	 */
	public function init($type = 'MONGO', $server = 'default') {
		$InitPHP_conf = InitPHP::getConfig(); //需要设置文件缓存目录
		$type = strtoupper($type); 
		$type = (in_array($type, $this->nosql_type)) ? $type : 'MONGO';
		switch ($type) { 
			case 'MONGO' :
				$instance_name = 'mongo_' . $server;
				if (isset(nosqlInit::$instance[$instance_name])) return nosqlInit::$instance['mongo'];
				$mongo = $this->load_nosql('mongo.init.php', 'mongoInit');
				$mongo->init($InitPHP_conf['mongo'][$server]);
				nosqlInit::$instance[$instance_name] = $mongo;
				return $mongo;
				break;
			
			case 'REDIS' :
				$instance_name = 'redis_' . $server;
				if (isset(nosqlInit::$instance[$instance_name])) return nosqlInit::$instance['redis'];
				$redis = $this->load_nosql('redis.init.php', 'redisInit');
				$redis->init($InitPHP_conf['redis'][$server]);
				nosqlInit::$instance[$instance_name] = $redis;
				return $redis;
				break;
		}
	}
	
	/**
	 * 加载不同NOSQL类文件
	 * @param  string $file  缓存文件名 
	 * @param  string $class 缓存类名
	 * @return obj
	 */
	private function load_nosql($file, $class) {
		if (nosqlInit::$instance['require'][$file] !== TRUE) {
			require('driver/' . $file);
			nosqlInit::$instance['require'][$file] = TRUE;
		}
		if (nosqlInit::$instance['class'][$class] !== TRUE) {
			nosqlInit::$instance['class'][$class] = TRUE;
			return new $class;
		} else {
			return nosqlInit::$instance['class'][$class];
		}
	}
}