<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-验证码
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class codeInit {

	private $width  = 62;
	private $height = 20;
	
	/**
	 * 获取随机数值
	 * @return string  返回转换后的字符串
	 */
	private function get_random_val($key) {
		srand((double)microtime()*1000000);
		while(($authnum=rand()%100000)<10000);
		session_start();
		$_SESSION[$key . 'initphp_code'] = $authnum;
		return $authnum;
	}
	
	/**
	 * 获取验证码图片
	 * @return string  
	 */
	public function getcode($key = 'user_') {
		Header("Content-type: image/PNG");  
		$im = imagecreate($this->width, $this->height); //制定图片背景大小
		$black = imagecolorallocate($im, 0,0,0); //设定三种颜色
		$white = imagecolorallocate($im, 255,255,255); 
		$gray = imagecolorallocate($im, 200,200,200); 
		imagefill($im,0,0,$gray); //采用区域填充法，设定（0,0）
		$authnum = $this->get_random_val($key);
		imagestring($im, 5, 10, 3, $authnum, $black);
		// 用 col 颜色将字符串 s 画到 image 所代表的图像的 x，y 座标处（图像的左上角为 0, 0）。
		//如果 font 是 1，2，3，4 或 5，则使用内置字体
		for($i=0; $i<200; $i++) { 
     		$randcolor = imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
     		imagesetpixel($im, rand()%70 , rand()%30 , $randcolor); 
		}
		$a = imagepng($im); 
		imagedestroy($im);
		return $a;
	}
	
	public function checkCode($code, $key = 'user_') {
		session_start();
		if ($_SESSION[$key . 'initphp_code'] == $code) return true;
		return false;
	}
}