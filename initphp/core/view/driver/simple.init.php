<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   View-simple 简单模板驱动规则模型
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class simpleInit {

	/**
	 * 模板驱动-简单的驱动
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	 public function init($str, $left, $right) {
	 	//if操作
	 	$str = preg_replace( "/".$left."if([^{]+?)".$right."/", "<?php if \\1 { ?>", $str );
		$str = preg_replace( "/".$left."else".$right."/", "<?php } else { ?>", $str );
		$str = preg_replace( "/".$left."elseif([^{]+?)".$right."/", "<?php } elseif \\1 { ?>", $str );
		//foreach操作
		$str = preg_replace("/".$left."foreach([^{]+?)".$right."/","<?php foreach \\1 { ?>",$str);
		$str = preg_replace("/".$left."\/foreach".$right."/","<?php } ?>",$str);
		//for操作
		$str = preg_replace("/".$left."for([^{]+?)".$right."/","<?php for \\1 { ?>",$str);
		$str = preg_replace("/".$left."\/for".$right."/","<?php } ?>",$str);
		//输出变量
		$str = preg_replace( "/".$left."(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_$\x7f-\xff\[\]\'\'\"]*)".$right."/", "<?php echo \\1;?>", $str );
		//常量输出
		$str = preg_replace( "/".$left."([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)".$right."/s", "<?php echo \\1;?>", $str );
		//标签解析
		$str = preg_replace ( "/".$left."\/if".$right."/", "<?php } ?>", $str );
	 	$pattern = array('/'.$left.'/', '/'.$right.'/');
		$replacement = array('<?php ', ' ?>');
		return preg_replace($pattern, $replacement, $str);
	 }
}
