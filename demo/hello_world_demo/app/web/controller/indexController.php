<?php
/**
 * DEMO
 * @author zhuli 
 */
class indexController extends Controller {
	
	public $initphp_list = array('test', 'getAc|get', 'postAc|post', 'putAc|put', 'delAc|del'); //Action白名单

	public function run() { 
		echo "Hello World";
	}
	
	public function test() {
		echo "一个测试类。<br/>";
		echo "Action 可以指定HTTP的请求方式，指定单个 例如：getAc|get 通过|符号分隔<br/>";
		echo "如果指定多个方法，方法之间用-符号分隔 例如：getAc|get-post-del 这个允许 GET POST 和 DEL操作 <br/>";
		echo "操作方式一共四种：get,post,del,put";
		$curl = $this->getLibrary("curl");
		echo "<br/><br/><br/>GET:<br/><br/><br/>";
		echo $curl->get("http://127.0.0.1/initphp/trunk/demo/hello_world_demo/www/?a=getAc");
		echo "<br/><br/><br/>POST:<br/><br/><br/>";
		echo $curl->post("http://127.0.0.1/initphp/trunk/demo/hello_world_demo/www/?a=postAc");
		echo "<br/><br/><br/>PUT:<br/><br/><br/>";
		echo $curl->put("http://127.0.0.1/initphp/trunk/demo/hello_world_demo/www/?a=putAc");
		echo "<br/><br/><br/>DEL:<br/><br/><br/>";
		echo $curl->del("http://127.0.0.1/initphp/trunk/demo/hello_world_demo/www/?a=delAc");
	}
	
	public function getAc() {
		echo "只能是HTTP GET请求的时候才能访问到";
	}
	
	public function postAc() {
		echo "只能是HTTP POST请求的时候才能访问到";
	}
	
	public function putAc() {
		echo "只能是HTTP PUT请求的时候才能访问到";
	}
	
	public function delAc() {
		echo "只能是HTTP DEL请求的时候才能访问到";
	}
	
} 