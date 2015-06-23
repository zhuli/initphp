<?php
class testInterceptor implements interceptorInterface {

	/**
	 * 前置拦截器，在所有Action运行全会进行拦截
	 * 如果返回true，则拦截通过;如果返回false，则拦截
	 * @return boolean 返回布尔类型，如果返回false，则截断
	 */
	public function preHandle() {
		echo "preHandle";
		return true;
	}

	/**
	 * 后置拦截器，在所有操作进行完毕之后进行拦截
	 */
	public function postHandle() {
		echo "postHandle";
	}

}