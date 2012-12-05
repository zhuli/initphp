<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2012-12-04 09:21:56, compiled from ../app/web/template/index_run.htm */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Form POST提交数据例子</title>
</head>

<body>
表单提交,需要安装user.sql数据库，并且需要配置conf/comm.conf.php配置文件，比mvc多了一个Service层：<br/><br/>
<form action="?c=index&a=post" method="post">
用户名：<input value="" name="username"><br/><br/>
密码：<input value="" name="password"><br/><br/>
<input type="submit"/>
</form>
</body>
</html>
