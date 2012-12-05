<?php
/**
 * DEMO
 * @author zhuli 
 */
class indexController extends Controller {
	
	public $initphp_list = array('test'); //Action白名单

	public function run() {    
		echo "<h1>Hello World，This is A InitPHP FrameWork Demo</h1>";
	}
} 