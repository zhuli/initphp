<?php
/**
 * DEMO
 * @author zhuli
 */
class indexController extends Controller {
	
	public $initphp_list = array('test'); //Action白名单

	public function run() {  
		echo "Hello World";
		echo "<br/><br/>";
		echo "<a href='index.php?c=index&a=test'>跳转到test这个Action!</a>";	
	}
	
	public function test() {
		echo "this is test Action!"; 
	} 
	
	public function before() {
		echo "前置Action，会在正常Action运行前执行<br/><br/>";
	}
	
	public function after() {
		echo "<br/><br/>后置Action，会在正常Action运行后运行<br/><br/>";
	}

} 