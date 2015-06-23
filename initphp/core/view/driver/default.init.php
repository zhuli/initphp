<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   View-default 默认模板驱动规则模型
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class defaultInit {

	/**
	 * 模板驱动-默认的驱动
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	 public function init($str, $left, $right) {
	 	$pattern = array('/'.$left.'/', '/'.$right.'/');
		$replacement = array('<?php ', ' ?>');
		return preg_replace($pattern, $replacement, $str);
	 }
}
