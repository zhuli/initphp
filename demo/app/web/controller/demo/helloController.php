<?php
/**
 * InitPHP开源框架 - DEM
 * @author zhuli
 */
class helloController extends Controller {

	//rest_post|post 方法名称 + | + HTTP方法名
	public $initphp_list = array("white_list", "to_json", "rest_post|post", "rest_get|get", "get_info", "interceptor"); //Action白名单
	
	/**
	 * Hello World DEMO
	 * 每个Controller都需要继承Controller这个框架基类
	 */
	public function run() {
		echo "<br/><h1>Hello World!This is InitPHP FrameWork</h1>";
		$this->view->display("demo/hello_run"); //使用模板
	}

	/**
	 * 白名单使用 white_list方法名称需要放置到$initphp_list这个变量中
	 */
	public function white_list() {
		echo "<br/><h1>白名单使用</h1>";
		$this->view->display("demo/hello_white_list"); //使用模板
	}

	/**
	 * 直接输出JSON格式数据
	 */
	public function to_json() {
		$array = array(
			"username" => "initphp",
			"age" => "10"
			);
		$this->controller->ajax_return(200, "SUCCESS", $array, "json");
	}

	/**
	 * 这个是rest get方法请求
	 */
	public function rest_get() {
		$curl = $this->getLibrary("curl");
		echo "<br/><h1>只能通过GET方法请求</h1>";
		$this->view->display("demo/hello_rest_get"); //使用模板
	}

	/**
	 * rest请求方式，这个方法必须使用 HTTP post来请求
	 */
	public function rest_post() {
		echo "<br/><h1>只能通过POST方法请求</h1>";
	}

	/**
	 * GET方法获取URL中请求参数
	 */
	public function get_info() {
		$username = $this->controller->get_gp("username");
		echo "<br/><h1>GET方法获取URL中请求参数:" .$username. "</h1>";
		$this->view->display("demo/hello_get_info"); //使用模板
	}

	public function interceptor() {
		echo "<br/><h1>拦截器例子</h1>";
	}
}