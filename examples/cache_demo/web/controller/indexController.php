<?php
/**
 * DEMO
 * @author zhuli
 */
class indexController extends Controller {
	
	public $initphp_list = array('test'); //Action白名单

	public function run() {   
		$this->getCache()->set("title", "woshiHello",  100); 
		echo $this->getCache()->get("title");
	}


} 