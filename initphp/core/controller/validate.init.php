<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   Controller-validate 数据基础验证类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class validateInit extends requestInit {


	/**
	 *	数据基础验证-检测字符串长度
	 *  规则结构:
	 *  array(
	 *  $data数组的key|验证的方法名称(例如is_email)|验证错误后的提示|参数
	 *  ) 
	 *  例子：
	 *		array(
	 *		'email|is_email|邮箱地址不正确',
	 *		'email|is_length|长度在0-111|0|50',
	 *		'phone|is_phone|手机号码格式不正确'
	 *		)
	 *  Controller中使用方法：$this->controller->validate($data, $rule)
	 * 	@param  array $data 数据
	 * 	@param  array $rule 规则模型
	 *  @return bool
	 */
	public function validate($data, $rule) {
		if (!is_array($data) || !is_array($rule)) return false; 
		foreach ($rule as $val) {
			$temp = explode('|', $val);
			$params = $temp;
			unset($params[0], $params[1], $params[2]);
			array_unshift($params, $data[$temp[0]]);
			$r = call_user_func_array(array($this, $temp[1]), $params); 
			if (!$r) return $temp[2];
		}
		return true;
	}

	/**
	 *	数据基础验证-检测字符串长度 
	 *  Controller中使用方法：$this->controller->is_length($value, $min = 0, $max= 0)
	 * 	@param  string $value 需要验证的值
	 * 	@param  int    $min   字符串最小长度
	 * 	@param  int    $max   字符串最大长度
	 *  @return bool
	 */
	public function is_length($value, $min = 0, $max= 0) {
		$value = trim($value);
		if ($min != 0 && strlen($value) < $min) return false;
		if ($max != 0 && strlen($value) > $max) return false;
		return true; 
	}
	
	/**
	 *	数据基础验证-是否必须填写的参数
	 *  Controller中使用方法：$this->controller->is_require($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_require($value) {
		return preg_match('/.+/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是空字符串
	 *  Controller中使用方法：$this->controller->is_empty($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_empty($value) {
		if (empty($value) || $value=="") return true;
		return false;
	}
	
	/**
	 *	数据基础验证-检测数组，数组为空时候也返回FALSH
	 *  Controller中使用方法：$this->controller->is_arr($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_arr($value) {
		if (!is_array($value) || empty($value)) return false;
		return true;
	}
	
	/**
	 *	数据基础验证-是否是Email 验证：xxx@qq.com
	 *  Controller中使用方法：$this->controller->is_email($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_email($value) {
		return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是IP
	 *  Controller中使用方法：$this->controller->is_ip($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_ip($value) {
		return preg_match('/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是数字类型 
	 *  Controller中使用方法：$this->controller->is_number($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_number($value) {
		return preg_match('/^\d{0,}$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是身份证
	 *  Controller中使用方法：$this->controller->is_card($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_card($value){
		return preg_match("/^(\d{15}|\d{17}[\dx])$/i", $value);
	}
	
	/**
	 *	数据基础验证-是否是电话 验证：0571-xxxxxxxx
	 *  Controller中使用方法：$this->controller->is_mobile($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_mobile($value) {
		return preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是移动电话 验证：1385810XXXX
	 *  Controller中使用方法：$this->controller->is_phone($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_phone($value) {
		return preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(13|15)\d{9}$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是URL 验证：http://www.easyphp.cc
	 *  Controller中使用方法：$this->controller->is_url($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_url($value) {
		return preg_match('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是邮政编码 验证：311100
	 *  Controller中使用方法：$this->controller->is_zip($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_zip($value) {
		return preg_match('/^[1-9]\d{5}$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是QQ 
	 *  Controller中使用方法：$this->controller->is_qq($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_qq($value) {
		return preg_match('/^[1-9]\d{4,12}$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是英文字母
	 *  Controller中使用方法：$this->controller->is_english($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_english($value) {
		return preg_match('/^[A-Za-z]+$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是中文
	 *  Controller中使用方法：$this->controller->is_chinese($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_chinese($value) {
		return preg_match("/^([\xE4-\xE9][\x80-\xBF][\x80-\xBF])+$/", trim($value));
	}
	
	/**
	 * 检查对象中是否有可调用函数
	 *  Controller中使用方法：$this->controller->is_method($object, $method)
	 * @param string $object
	 * @param string $method
	 * @return bool
	 */
	public function is_method($object, $method) {
		$method = strtolower ( $method );
		return method_exists($object, $method) && is_callable (array($object, $method));
	}
	
	/**
	 * 检查是否是安全的账号
	 *  Controller中使用方法：$this->controller->is_safe_account($value)
	 * @param string $value
	 * @return bool
	 */
	public function is_safe_account($value) {
		return preg_match ("/^[a-zA-Z]{1}[a-zA-Z0-9_\.]{3,31}$/", $value);
	}
	
	/**
	 * 检查是否是安全的昵称
	 *  Controller中使用方法：$this->controller->is_safe_nickname()
	 * @param string $value
	 * @return bool
	 */
	public function is_safe_nickname($value) {
		return preg_match ("/^[-\x{4e00}-\x{9fa5}a-zA-Z0-9_\.]{2,10}$/u", $value);
	}
	
	/**
	 * 检查是否是安全的密码
	 *  Controller中使用方法：$this->controller->is_safe_password()
	 * @param string $str
	 * @return bool
	 */
	public function is_safe_password($str) {
		if (preg_match('/[\x80-\xff]./', $str) || preg_match('/\'|"|\"/', $str) || strlen($str) < 6 || strlen($str) > 20 ){
			return false;
		}
		return true;
	}
	
	/**
     * 检查是否是正确的标识符  
     * 1. 以字母或下划线开始，后面跟着任何字母，数字或下划线。
     * 2. 使用方法【Controller】：
     *    <code>$this->controller->is_identifier($value)</code> 
     **/ 
   public function is_identifier($value) {
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]+$/', trim($value)); 
   } 
	
}
