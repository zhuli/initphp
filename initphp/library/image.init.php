<?php 
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-图片处理类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class imageInit {
	
	/**
	 * 图像处理-缩略图处理
	 * @param string $source 被缩略的图
	 * @param stirng $newname缩略后的新图 
	 * @param int    $width  宽度
	 * @param int    $height 高度
	 * @param bool   $isauto 缩略方式，true-保持比例缩放|false-超出部分裁剪
	 */
	public function make_thumb($source, $newname, $width = 100, $height = 100, $isauto = true) {
		if (!file_exists($source)) return false;
		$image_info = $this->get_img_info($source); //获取原始图片信息
        if ($image_info['type'] == 'bmp') return false;
		list($imagecreate, $imagecopyre) = $this->get_image_create($image_info['type']); //获取创建图像和拷贝图像的函数
		if (empty($image_info) || !$imagecreate) return false;
		/* 画布和原始图创建 */
		$thumb = $imagecreate($width, $height); //创建一个图片-画布
		$imagecreatefrom = 'imagecreatefrom' .$image_info['type']; 
		$source = $imagecreatefrom($source);
		if(function_exists('ImageColorAllocate') && function_exists('ImageColorTransparent')){
			$black = ImageColorAllocate($thumb,0,0,0); //着色
			$bgTransparent = ImageColorTransparent($thumb,$black);
		}
		/*根据原始图片的长宽比自动缩略*/
		$src_x = $src_y = 0;
		
		//保持比例缩放
		if ($isauto == true) {
			$x_ratio = $width / $image_info['width'];
			$y_ratio = $height / $image_info['height'];
			if(($x_ratio * $image_info['height']) < $height) {
				$h = ceil($x_ratio * $image_info['height']);
				$w = $width;
			} else {
				$w = ceil($y_ratio * $image_info['width']);
				$h = $height;
			}
			$thumb = $imagecreate($w, $h); //创建一个图片-画布
			$imagecopyre($thumb, $source, 0, 0, 0, 0, $w, $h, $image_info['width'], $image_info['height']); //拷贝
		}
		//超出部分裁剪
		else{
			if ($image_info['width'] < $image_info['height']) {
				$src_y = round(($image_info['height'] - $image_info['width']) / 2);
				$image_info['height'] = $image_info['width'];
			} else {
				$src_x = round(($image_info['width'] - $image_info['height']) / 2);
				$image_info['width'] = $image_info['height'];
			}
			/* 拷贝和创建图像 */
			$imagecopyre($thumb, $source, 0, 0, $src_x, $src_y, $width, $height, $image_info['width'], $image_info['height']);
		}
		$result = $this->make($image_info['type'], $thumb, $newname);
		if (!$result) return array(); //缩略图创建失败
		imagedestroy($thumb);
		return array($width, $height);
	}
	
	/**
	 * 图像处理-图片水印
	 * @param string $source_path   需要生成水印的图片
	 * @param string $water_path    水印图片位置
	 * @param string $position      显示的位置：0 随机|1 左上|2 中上|3 右上 |4 左下|5中下|6右下|其它中间
	 * @param string $pct           水印图片透明度
	 * @param string $quality       生成图片的品质，0-100，值越大品质越高
	 * @return bool
	 */
	public function water_mark($source_path, $water_path, $postion, $pct=100, $quality=100) {
		if (!file_exists($source_path) || !file_exists($water_path)) return false;
		/* 图片信息 */
		$source_info = $this->get_img_info($source_path);
		$water_info  = $this->get_img_info($water_path);
		if ($source_info['type'] == 'bmp' || $water_info['type'] == 'bmp') return false;
		if ($source_info['type']) {
			$imagecreatefrom = 'imagecreatefrom' .$source_info['type']; 
			$source = $imagecreatefrom($source_path);
		}
		if ($water_info['type']) {
			$imagecreatefrom = 'imagecreatefrom' .$water_info['type']; 
			$water = $imagecreatefrom($water_path);
		}
		/* 处理 */
		imagealphablending($source, true); //混色模式
		list($x, $y) = $this->get_water_postion($postion, $source_info['width'], $source_info['height'], $water_info['width'], $water_info['height']);
		if ($water_info['type'] == 'png') { 
			$temp = imagecreatetruecolor($source_info['width'], $source_info['height']); //创建着色板
			imagecopy($temp, $source, 0, 0, 0, 0, $source_info['width'], $source_info['height']); //将源图拷贝进着色版
			imagecopy($temp, $water, $x, $y, 0, 0, $water_info['width'], $water_info['height']); //将水印拷贝进着色办
			$source = $temp;
		} else { 
			imagecopymerge($source, $water, $x, $y, 0,0, $water_info['width'], $water_info['height'], $pct); //合并
		}
		$ext = strtolower(substr(strrchr(basename($source_path), '.'), 1));
		$shource_dir = dirname($source_path); //修改水印BUG
		$source_path = $shource_dir . '/' . basename($source_path, '.' . $ext);
		$this->make($source_info['type'], $source, $source_path, $quality);
		imagedestroy($source);
		imagedestroy($water);
		return true;
	}
	
	/**
	 * 图像处理-获取水印位置
	 * @param int $position      水印位置
	 * @param int $source_width 原始图片的宽
	 * @param int $source_height原始图片的高
	 * @param int $water_width  水印图片的宽
	 * @param int $water_height 水印图片的高
	 * @return array(宽，高)
	 */
	private function get_water_postion($position, $source_width, $source_height, $water_width, $water_height) {
		if ($position == 0) {
			$x = rand(0, ($source_width - $water_width));
			$y = rand(0, ($source_height - $water_height));
		} elseif ($position == 1) {
			$x = 5;
			$y = 5;
		} elseif ($position == 2) {
			$x = ($source_width - $water_width) / 2;
			$y = 5;
		} elseif ($position == 3) {
			$x = $source_width - $water_width - 5;
			$y = 5;
		} elseif ($position == 4) {
			$x = 5;
			$y = $source_height - $water_height - 5;
		} elseif ($position == 5) {
			$x = ($source_width - $water_width) / 2;
			$y = $source_height - $water_height - 5;
		} elseif ($position == 6) {
			$x = $source_width - $water_width - 5;
			$y = $source_height - $water_height - 5;
		} else {
			$x = ($source_width - $water_width) / 2;
			$y = ($source_height - $water_height) / 2;
		}
		return array($x, $y);
	}
	
	/*
	 * 图像处理-生成图片
	 * @param string $type     图片类型
	 * @param string $image    图片资源
	 * @param string $filename 图片文件名
	 * @param int    $quality  图片质量,对jpeg有效
	 * @return bool
	 */
	private function make($type, $image, $filename, $quality = '75') {
		$file_type = ($type !== 'jpeg') ? $type : 'jpg';
		$filename = $filename . '.' . $file_type;
		$this->createFolder(dirname($filename)); //创建目录 
		$makeimage = 'image' . $type;  
		if (!function_exists($makeimage)) {
			return false;
		}
		if ($type == 'jpeg') {
			$makeimage($image, $filename, $quality);
		} else {
			$makeimage($image, $filename);
		}
		return true;
	}
	
	/**
	 * 图像处理-获取图像信息
	 * @param string $source 源文件图片
	 * @return array(图片的宽、高、类型)
	 */
	private function get_img_info($source) {
		$imginfo = array();
		$ext = strtolower(substr(strrchr($source, '.'), 1)); //获取图片类型
		$image_type = array(1=>'gif', 2=>'jpeg', 3=>'png', 6=>'bmp');
		if (function_exists('read_exif_data') && in_array($ext, array('jpg','jpeg','jpe','jfif'))) { //jpeg情况
			$temp = @read_exif_data($source);
			$imginfo['width']  = $temp['COMPUTED']['Width'];
			$imginfo['height'] = $temp['COMPUTED']['Height'];
			$imginfo['type'] = 2;
			unset($temp);
		}
		if (empty($imginfo)) { //png,gif,bmp
			list($imginfo['width'], $imginfo['height'], $imginfo['type']) = @getimagesize($source);
		}
		$imginfo['type'] = $image_type[$imginfo['type']];
		return $imginfo;
	}
	
	/**
	 * 图像处理-返回图像创建和图像
	 * 说明：gif和jpeg等函数的图像创建函数不一样
	 * @param string $imagetype 图像类型-gif、jpeg、png、bmp
	 * @return array(图像创建函数， 图像拷贝函数) 
	 */
	private function get_image_create($imagetype) {
		if ($imagetype != 'gif' && function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')) {
			return array('imagecreatetruecolor','imagecopyresampled');
		} elseif (function_exists('imagecreate') && function_exists('imagecopyresized')) {
			return array('imagecreate','imagecopyresized');
		} else {
			return array();
		}
	}
	
	/**
	 * 图像处理-创建目录 如果目录存在，则不创建，不存在则创建 static
	 * @param $path 路径
	 * @return 
	 */
	public static function createFolder($path) {
		if (!is_dir($path)) {
			imageInit::createFolder(dirname($path));
			@mkdir($path);
			@chmod($path, 0777);
			@fclose(@fopen($path . '/index.html', 'w'));
			@chmod($path . '/index.html', 0777);
		}
	}
}