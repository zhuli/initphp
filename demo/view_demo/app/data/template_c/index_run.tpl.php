<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-03-14 09:18:08, compiled from ../app/web/template/index_run.htm */ ?>

<h1><?php echo $title;?></h1><br/>
以下是加载的模板列表：
<ul>
<?php foreach  ($tpls as $val) { ?>
<li><?php echo $val;?></li>
<?php } ?>
</ul>
详细使用查看：<a href="http://initphp.com/4_5.htm" target="_blank">http://initphp.com/4_5.htm</a>
