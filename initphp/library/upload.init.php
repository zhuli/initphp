<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-文件上传
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class uploadInit {
	
	const UPLOAD_ERR_INI_SIZE   = 1;
	const INPUT_MAX_FILE_SIZE   = 2;
	const UPLOAD_HALF           = 3;
	const UPLOAD_ERR_NO_TMP_DIR = 4;
	
	private $params; //参数
	private $defaultMaxSize = 2048; //上传文件默认最大值
	private $defaultAllowFileType = array('gif','jpeg','jpg','png','bmp','swf', 'txt');
	private $errorCodeArr = array(
							'upload_error'         => -1, //上传失败
							'not_upload_files'     => -2, //不是通过HTTP POST方法上传
							'not_an_allowed_type'  => -3, //不允许的上传类型
							'file_size_is_large'   => -4, //文件太大
							'upload_err_ini_size'  => -5, //上传文件超过服务器上传限制
							'input_max_file_size'  => -6, //上传文件超过表达最大上传限制
							'upload_half'          => -7, //只上传了一半文件
							'upload_err_no_tmp_dir'=> -8, //上传的临时目录出错
							'illegal_file_type'    => -9,  //新的文件名，命名不合法
							'upload_content_error' => -10  //上传的内容不合法
							); //错误码
			
	/**
	 * 上传文件 主函数
	 * @param $name    上传文件名
	 * @param $newName 新的文件名  不需要类型
	 * @param $path    目录
	 * @param $params  参数配置
	 * @return array
	 */
	public function upload($name, $newName, $path, $params = array()) {
		$this->params = $this->parseParams($params);
		$uploadInfo = $this->init($name, $newName, $path);
		if (!$uploadInfo) return $this->error('upload_error'); //是否正常上传
		$errorVal = $this->checkUpload($uploadInfo['error']);
		if ($errorVal !== true) return $this->error($errorVal); //检测上传错误码
		if (!$this->checkIsUploadFile($uploadInfo['tmp_name'])) return $this->error('not_upload_files'); //是否通过HTTP POST上传
		if (!$this->checkType($uploadInfo['ext'])) return $this->error('not_an_allowed_type'); //是否允许上传的类型
		if (!$this->checkSize($uploadInfo['size'])) return $this->error('file_size_is_large'); //文件大小	
		if (!$this->checkNewName($newName)) return $this->error('illegal_file_type'); //新文件名是否合法
		
		$result = $this->save($uploadInfo['tmp_name'], $uploadInfo['source'], $uploadInfo['path']);
		if ($result == false) {
			return $this->error('upload_error'); 
		} else {
			$checkContentResult = $this->checkContent($uploadInfo);
		    if ($checkContentResult !== true) return $this->error($checkContentResult); //检测上传错误码
		    return $uploadInfo;
		}
	}
	
	/**
	 * 参数设置
	 * @param array $params array('maxSize' => 文件上传最大,'allowFileType' => 允许上传的文件类型)
	 * @return 
	 */
	public function setParams($params) {
		$this->params = $this->parseParams($params);
	}
	
	/**
	 * 装载上传文件的信息
	 * @param  string $name 上传文件名
	 * @return array
	 */
	private function init($name, $newName, $path) {
		$newName = $this->escapeStr($newName);
		$path    = $this->escapeDir($path);
		$file    = $_FILES[$name];
		if (!$file['tmp_name'] || $file['tmp_name'] == '') return false;
		$file['name']     = $this->escapeStr($file['name']);
		$file['ext']      = strtolower(substr(strrchr($file['name'], '.'), 1));
		$file['size']     = intval($file['size']);
		$file['type']     = $file['type'];
		$file['tmp_name'] = $file['tmp_name'];
		$file['source']   = $path .'/'. $newName . '.' . $file['ext']; //路径
		$file['path']     = $path;      //目录
		$file['newName']  = $newName. '.' . $file['ext'];   //文件名
		return $file;
	}
	
	/**
	 * 参数处理
	 * @param array $params 文件上传配置参数
	 * @return 
	 */
	private function parseParams(array $params) {
		$temp = array();
		$temp['maxSize']       = (isset($params['maxSize'])) ? (int)$params['maxSize'] : $this->defaultMaxSize;
		$temp['allowFileType'] = (is_array($params['allowFileType'])) ? $params['allowFileType'] : $this->defaultAllowFileType;
		return $temp;
	}
	
	/**
	 * 保存文件
	 * @param $name    上传文件名
	 * @param $newName 新的文件名  1
	 * @param $path    目录
	 * @return bool
	 */
	private function save($tmpName, $filename, $path) {
		$this->createFolder($path); //创建目录
		if (function_exists("move_uploaded_file") && @move_uploaded_file($tmpName, $filename)) {
			@chmod($filename, 0777);
			return true;
		} elseif (@copy($tmpName, $filename)) {
			@chmod($filename, 0777);
			return true;
		}
		return false;
	}
		
	/**
	 * 错误码检测
	 * @param int $error 错误状态
	 * @return string
	 */
	private function checkUpload($error) {
		if ($error == uploadInit::UPLOAD_ERR_INI_SIZE) { //上传是否超过ini设置
			return 'upload_err_ini_size';
		} elseif ($error == uploadInit::INPUT_MAX_FILE_SIZE) { //上传是否超过表单设置
			return 'input_max_file_size';
		} elseif ($error == uploadInit::UPLOAD_HALF) { //上传一半
			return 'upload_half';
		} elseif ($error == uploadInit::UPLOAD_ERR_NO_TMP_DIR) { //上传临时目录创建错误
			return 'upload_err_no_tmp_dir';
		} else {
			return true;
		}
	}
	
	/**
	 * 文件类型检测
	 * @param string $uploadType 类型
	 * @return bool
	 */
	private function checkType($uploadType) {
		return (empty($uploadType) || !in_array($uploadType, $this->params['allowFileType'])) ? false : true;	
	}
	
	/**
	 * 文件大小检测
	 * @param int $uploadSize 大小
	 * @return bool
	 */
	private function checkSize($uploadSize) {
		return ($uploadSize < 1 || $uploadSize > ($this->params['maxSize'] * 1024)) ? false : true;
	}
	
	/**
	 * 检测新的文件名
	 * @param string $newName 新文件名
	 * @return bool
	 */
	private function checkNewName($newName) {
		$newName = strtolower($newName);
		return (strpos($newName, '..') !== false || strpos($newName, '.php.') !== false || eregi("\.php$", $newName)) ? false : true;
	}
	
	/**
	 * 检测是否是上传的文件
	 * @param $tmpName 临时文件名
	 * @return bool
	 */
	private function checkIsUploadFile($tmpName) {
		if (!$tmpName || $tmpName == 'none') {
			return false;
		} elseif (function_exists('is_uploaded_file') && !is_uploaded_file($tmpName) && !is_uploaded_file(str_replace('\\\\', '\\', $tmpName))) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * 文件上传后检测文件内容是否合法
	 * @param string $uploadInfo 文件信息
	 * @param string $source 文件源目录
	 * @return bool
	 */
	private function checkContent($uploadInfo) {
		if ($uploadInfo['ext'] == 'txt') {
			if (preg_match('/(onload|submit|post|form)/i', $this->readover($uploadInfo['source']))) {
				@unlink($uploadInfo['source']);
				return 'upload_content_error';
			}
		} elseif (in_array($uploadInfo['ext'], array('gif','jpg','jpeg','png','bmp','swf'))) {
			if (!$img_size = $this->getImgSize($uploadInfo['source'], $uploadInfo['ext'])) {
				@unlink($uploadInfo['source']);
				return 'upload_content_error';
			}
		}
		return true;
	}
	
	/**
	 * 创建目录 如果目录存在，则不创建，不存在则创建 static
	 * @param $path 路径
	 * @return 
	 */
	public static function createFolder($path) {
		if (!is_dir($path)) {
			mkdir($path, 0755, true);
		}
		//下面这段容易造成递归死循环
		// if (!is_dir($path)) {
			// uploadInit::createFolder(dirname($path));
			// @mkdir($path);
			// @chmod($path, 0777);
			// @fclose(@fopen($path . '/index.html', 'w'));
			// @chmod($path . '/index.html', 0777);
		// }
	}
	
	/**
	 * 读取文件
	 * @param string $fileName 文件绝对路径
	 * @param string $method 读取模式
	 */
	private function readover($fileName, $method = 'rb') {
		$fileName = $this->escapePath($fileName);
		$data = '';
		if ($handle = @fopen($fileName, $method)) {
			flock($handle, LOCK_SH);
			$data = @fread($handle, filesize($fileName));
			fclose($handle);
		}
		return $data;
	}
	
	/**
	 * 获取图片的大小
	 * @param string $srcFile 图片地址
	 * @param string $srcExt  图片类型
	 * @return 
	 */
	private function getImgSize($srcFile, $srcExt = null) {
		empty($srcExt) && $srcExt = strtolower(substr(strrchr($srcFile, '.'), 1));
		$srcdata = array();
		if (function_exists('read_exif_data') && in_array($srcExt, array(
			'jpg',
			'jpeg',
			'jpe',
			'jfif'
		))) {
			$datatemp = @read_exif_data($srcFile);
			$srcdata['width'] = $datatemp['COMPUTED']['Width'];
			$srcdata['height'] = $datatemp['COMPUTED']['Height'];
			$srcdata['type'] = 2;
			unset($datatemp);
		}
		!$srcdata['width'] && list($srcdata['width'], $srcdata['height'], $srcdata['type']) = @getimagesize($srcFile);
		if (!$srcdata['type'] || ($srcdata['type'] == 1 && in_array($srcExt, array(
			'jpg',
			'jpeg',
			'jpe',
			'jfif'
		)))) { //noizy fix
			return false;
		}
		return $srcdata;
	}
	
	/**
	 * 字符转换
	 * @param  string  $string  转换的字符串
	 * @return string  返回转换后的字符串
	 */
	private function escapeStr($string) {
		$string = str_replace(array("\0","%00","\r"), '', $string); 
		$string = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $string);
		$string = str_replace(array("%3C",'<'), '&lt;', $string);
		$string = str_replace(array("%3E",'>'), '&gt;', $string);
		$string = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $string);
		return $string;
	}
	
	/**
	 * 目录转换
	 * @param string $dir
	 * @return string
	 */
	private function escapeDir($dir) {
		$dir = str_replace(array("'",'#','=','`','$','%','&',';'), '', $dir);
		return rtrim(preg_replace('/(\/){2,}|(\\\){1,}/', '/', $dir), '/');
	}
	
	/**
	 * 私用路径转换
	 * @param $fileName
	 * @param $ifCheck
	 * @return boolean
	 */
	private function escapePath($fileName, $ifCheck = true) {
		$tmpname = strtolower($fileName);
		$tmparray = array('://',"\0");
		$ifCheck && $tmparray[] = '..';
		if (str_replace($tmparray, '', $tmpname) != $tmpname) {
			return false;
		}
		return true;
	}
	
	/**
	 * 上传错误提示
	 * @param unknown_type $msgType
	 */
	private function error($errorCode) {
		return $this->errorCodeArr[$errorCode];
	}
}
