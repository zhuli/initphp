<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   - 工具库-sql监控
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25  
***********************************************************************************/
class sqlcontrolInit { 
	
	/**
 	 * 数据库语句监控器-开始点
 	 * 使用方法：$this->getUtil('sqlcontrol')->start();
 	 * @return string   
	 */
	public function start() {
		InitPHP::setConfig('issqlcontrol', 1);		
	}
	
	/**
 	 * 数据库语句监控器-结束点
  	 * 使用方法：$this->getUtil('sqlcontrol')->end();
 	 * @return string   
	 */
	public function end() {
		$InitPHP_conf = InitPHP::getConfig();
		if (isset($InitPHP_conf['sqlcontrolarr']) && is_array($InitPHP_conf['sqlcontrolarr'])) {
			$i = 1;
			echo '<div style=" border:1px #000000 dotted; width:100%; background-color:#EEEEFF">';
			foreach ($InitPHP_conf['sqlcontrolarr'] as $k => $v) {
				echo '<div style=" height:20px; text-align:left; font-size:14px; margin-left:10px;margin-top:5px;"><span>'.$i.'.&nbsp;&nbsp;&nbsp;&nbsp;</span>' . $v . '</div>';
				$i++;
			}
			echo '</div>';
		}
	}
}