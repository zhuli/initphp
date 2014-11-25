<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-11-25 08:05:50, compiled from ../app/web/template/demo/hello_run.htm */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>InitPHP框架 PHP框架 - A PHP Framework - 配置文件</title>
<link href="static/common.css" type="text/css" rel="stylesheet" />
<meta name="keywords" content="php框架，国产php框架, initphp框架，MVC，分层体系" />
<meta name="description" content="initphp框架是一款国产php框架。initphp框架主要基于MVC模式，具备代码清晰，操作简单，功能齐全，开发快速，高效安全等特点，是您选择php框架的首选。" />
</head>
<body>
<h3>相关代码</h3>
<pre id="PHP" class="prettyprint">
/**
 * InitPHP开源框架 - DEM
 * @author zhuli
 */
class helloController extends Controller {

	public $initphp_list = array(); //Action白名单

	/**
	 * Hello World DEMO
	 * 每个Controller都需要继承Controller这个框架基类
	 */
	public function run() {
		echo "&lt;br/&gt;&lt;h1&gt;Hello World!This is InitPHP FrameWork&lt;/h1&gt;";
		$this->view->display("demo/hello_run"); //使用模板
	}

}
</pre>
<script src="static/jquery.js" type="text/javascript"></script>
<link href="static/prettify/prettify.css" rel="stylesheet" type="text/css">
<script src="static/prettify/prettify.js" type="text/javascript"></script>
<script type="text/javascript" src="static/comm.js"></script>
</body>
</html>
