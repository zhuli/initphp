<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-文件操作类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class fileInit {
	
	/**
	 *	创建空文件
	 * 	@param  string  $filename  需要创建的文件
	 *  @return 
	 */
	public function create_file($filename) {
		if (file_exists($filename)) return false;
		$this->create_dir(dirname($filename)); //创建目录
		return @file_put_contents($filename,'');
	}
	
	/**
	 *	写文件
	 * 	@param  string  $filename  文件名称
	 * 	@param  string  $content   写入文件的内容
	 * 	@param  string  $type      类型，1=清空文件内容，写入新内容，2=再内容后街上新内容
	 *  @return 
	 */
	public function write_file($filename, $content, $type = 1) {
		if ($type == 1) {
			if (file_exists($filename)) $this->del_file($filename); //删除文件
			$this->create_file($filename);
			return $this->write_file($filename, $content, 2);
		} else {
			if (!is_writable($filename)) return false;
			$handle = @fopen($filename, 'a');
			if (!$handle) return false;
			$result = @fwrite($handle, $content);
			if (!$result) return false;
			@fclose($handle);
			return true;
		}
	}
	
	/**
	 *	拷贝一个新文件
	 * 	@param  string  $filename    文件名称
	 * 	@param  string  $newfilename 新文件名称
	 *  @return 
	 */
	public function copy_file($filename, $newfilename) {
		if (!file_exists($filename) || !is_writable($filename)) return false;
		$this->create_dir(dirname($newfilename)); //创建目录
		return @copy($filename, $newfilename);
	}
	
	/**
	 *	移动文件
	 * 	@param  string  $filename    文件名称
	 * 	@param  string  $newfilename 新文件名称
	 *  @return 
	 */
	public function move_file($filename, $newfilename) {
		if (!file_exists($filename) || !is_writable($filename)) return false;
		$this->create_dir(dirname($newfilename)); //创建目录
		return @rename($filename, $newfilename);
	}
	
	/**
	 *	删除文件
	 * 	@param  string  $filename  文件名称
	 *  @return bool
	 */
	public function del_file($filename) {
		if (!file_exists($filename) || !is_writable($filename)) return true;
		return @unlink($filename);
	}
	
	/**
	 *	获取文件信息
	 * 	@param  string  $filename  文件名称
	 *  @return array('上次访问时间','inode 修改时间','取得文件修改时间','大小'，'类型')
	 */
	public function get_file_info($filename) {
		if (!file_exists($filename)) return false;
		return array(
			'atime' => date("Y-m-d H:i:s", fileatime($filename)),
			'ctime' => date("Y-m-d H:i:s", filectime($filename)),
			'mtime' => date("Y-m-d H:i:s", filemtime($filename)),
			'size'  => filesize($filename),
			'type'  => filetype($filename)	
		);
	}
	
	/**
	 *	创建目录
	 * 	@param  string  $path   目录
	 *  @return 
	 */
	public function create_dir($path) {
		if (is_dir($path)) return false;
		fileInit::create_dir(dirname($path));
		@mkdir($path);
		@chmod($path, 0777);
		return true;
	}
	
	/**
	 *	删除目录
	 * 	@param  string  $path   目录
	 *  @return 
	 */
	public function del_dir($path) {
		$succeed = true;
		if(file_exists($path)){
			$objDir = opendir($path);
			while(false !== ($fileName = readdir($objDir))){
				if(($fileName != '.') && ($fileName != '..')){
					chmod("$path/$fileName", 0777);
					if(!is_dir("$path/$fileName")){
						if(!@unlink("$path/$fileName")){
							$succeed = false;
							break;
						}
					}
					else{
						self::del_dir("$path/$fileName");
					}
				}
			}
			if(!readdir($objDir)){
				@closedir($objDir);
				if(!@rmdir($path)){
					$succeed = false;
				}
			}
		}
		return $succeed;
	}
}	
?>