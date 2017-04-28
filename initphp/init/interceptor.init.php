<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架  - 框架拦截器
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25
 ***********************************************************************************/
class interceptorInit {

	private $m;

	private $c;

	private $a;

	public function __construct() {
		$this->m = coreInit::getM();
		$this->c = coreInit::getC();
		$this->a = coreInit::getA();
	}

	/**
	 * 前置框架拦截器，在业务逻辑处理的最顶端对业务进行拦截
	 * 前置拦截器返回boolean类型，如果返回false，则中断
	 * 可以根据配置文件配置多个拦截器，拦截器按照顺序执行
	 */
	public function preHandle() {
		return $this->parse(true);
	}

	/**
	 * 后置拦截器
	 */
	public function postHandle() {
		return $this->parse(false);
	}

	/**
	 * 具体解析
	 * @param unknown_type $isPre
	 */
	private function parse($isPre = true) {
		$InitPHP_conf = InitPHP::getConfig();
		$interceptor = $InitPHP_conf['interceptor'];
		$filePath = $interceptor['path']; //文件路径
		$return = true;
		if (is_array($interceptor['rule']) && count($interceptor['rule']) > 0) {
			foreach ($interceptor['rule'] as $k => $v) {
				$file = ltrim($filePath, "/") . "/" . $v['file'] . $interceptor['postfix'] . '.php';
				$class = $v['file'] . $interceptor['postfix'];
				//处理正则匹配
				$regular = $v['regular'];
				if ($regular['m'] != "" && $regular['m'] != '*') {
					if (!preg_match($regular['m'], $this->m)) {
						continue;
					}
				}
				if ($regular['c'] != "" && $regular['c'] != '*') {
					if (!preg_match($regular['c'], $this->c)) {
						continue;
					}
				}
				if ($regular['a'] != "" && $regular['a'] != '*') {
					if (!preg_match($regular['a'], $this->a)) {
						continue;
					}
				}
				if (file_exists(InitPHP::getAppPath($file))) {
					InitPHP::import($file);
					$obj = InitPHP::loadclass($class);
					if ($isPre == true) {
						$ret = $obj->preHandle();
						if ($ret == false) {
							$return = false;
							break;
						}
					} else {
						$obj->postHandle();
					}
				}

			}
		}
		return $return;
	}

}