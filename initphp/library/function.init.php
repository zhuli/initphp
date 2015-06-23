<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-方法库
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class functionInit {

	/**
	 * 方法库-sign签名方法
	 * @param $array 需要加密的参数 
	 * @param $secret 秘钥
	 * @param $signName sign的名称，sign不会进行加密
	 */
	public function sign($array, $secret, $signName = "sign") {
		if (count($array) == 0) {
			return "";
		}
		ksort($array); //按照升序排序
		$str = "";
		foreach ($array as $key => $value) {
			if ($signName == $key) continue;
			$str .= $key . "=" . $value . "&";
		}
		$str = rtrim($str, "&");
		return md5($str . $secret);
	}

	/**
	 * 方法库-获取随机值
	 * @return string  
	 */
	public function get_rand($str, $len) {
		return substr(md5(uniqid(rand()*strval($str))),0, (int) $len);
	}
	
	/**
	 * 方法库-获取随机Hash值 
	 * @return string
	 */
	public function get_hash($length = 13) { 
		$chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    	$max = strlen($chars) - 1;
    	mt_srand((double)microtime() * 1000000);
		for ($i=0; $i<$length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}
	
	/**
	 * 方法库-截取字符串-【该函数作者未知】
	 * @param string  $string 字符串  
	 * @param int     $length 字符长度
	 * @param string  $dot    截取后是否添加...
	 * @param string  $charset编码
	 * @return string
	 */
	public function cutstr($string, $length, $dot = ' ...', $charset = 'utf-8') {
		if (strlen($string) <= $length) {
			return $string;
		}
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
		$strcut = '';
		if (strtolower($charset) == 'utf-8') {
			$n = $tn = $noc = 0;
			while ($n < strlen($string)) {
				$t = ord($string[$n]);				//ASCIIֵ
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif (194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif (224 <= $t && $t < 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif (240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif (248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif ($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if ($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);
		} else {
			for ($i = 0; $i < $length; $i++) {
				$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			}
		}
		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
		return $strcut.$dot;
	}
	
	/**
	 * 方法库-字符串是否存在
	 * @param string $str :字符或字符串
	 * @param string $string :字符串
	 * @return string 例子: $str='34' $string='1234' 返回 TRUE
	 */
	public function is_str_exist($str, $string) {
		$string = (string) $string;
		$str = (string) $str;
		return strstr($string,$str)===false ? false : true;
	}
	
	/**
	 * 方法库-token使用
	 * @param string $type :encode-加密方法|decode-解密方法
	 * @return string|bool
	 */
	public function token($type = 'encode') {
		session_start();
		if ($type == 'encode') {
			$key = $this->get_hash(5);
			$_SESSION['init_token'] = $key;
			return '<input name="init_token" type="hidden" value="'.$_SESSION['init_token'].'"/>';
		} else {
			$value = trim($_POST['init_token']);
			if ($value !== $_SESSION['init_token']) return false;
			return true;
		}
	}
	
	/**
	 * 方法库-压缩函数，主要是发送http页面内容过大的时候应用
	 * @param string $content 内容
	 * @return string
	 */
	public function gzip(&$content) {
		if(!headers_sent()&&extension_loaded("zlib")&&strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip")){
			$content = gzencode($content,2);
			header("Content-Encoding: gzip");
			header("Vary: Accept-Encoding");
			header("Content-Length: ".strlen($content));
		}
		return $content;
	}
	
	/**
	 * 方法库-向父串中插入子串
	 * @param string $string  : 父串
	 * @param number $sublen  : 长度
	 * @param string $str     : 子串
	 * @param string $code    : 编码
	 * @return string
	 */
	public function insert_str($string, $sublen=10, $str="<br/>", $code='UTF-8'){
		if ($code == 'UTF-8') {
			$pa ="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all($pa, $string, $t_string);
			$n = count($t_string[0]);
			$floor = ceil($n / $sublen);
			if ($n > $sublen) {
				for ($i=0; $i < $floor; $i++) {
					array_splice($t_string[0], ($sublen * ($i+1))-1, 0, $str);
				}
				return implode('',  $t_string[0]);
			} else {
				array_splice($t_string[0], $sublen, 0);
				return implode('', $t_string[0]);
			}
		}
	}
	
	/**
	 * 方法库-加密解密函数
	 * @param string $string  加密的字符串
	 * @param number $key     加密的密钥
	 * @param string $type    加密的方法-ENCODE|加密 DECODE|解密
	 * @return string
	 */
	public function str_code($string, $key, $type = 'ENCODE') {
		$string = ($type == 'DECODE') ? base64_decode($string) : $string;
		$key_len = strlen($key);
		$key     = md5($key);
		$string_len = strlen($string);
		for ($i=0; $i<$string_len; $i++) {
			$j = ($i * $key_len) % 32;
			$code .= $string[$i] ^ $key[$j];
		}
		return ($type == 'ENCODE') ? base64_encode($code) : $code;
	}
	
	/**
	 * 方法库-输出钱的格式
	 * @param string $num  数值
	 * @return string
	 */
	public function format_number($num){
   		return number_format($num, 2, ".", ",");
	}
	
	/**
	 * 方法库-字节转换-转换成MB格式等
	 * @param string $num  数值
	 * @return string
	 */
	public function bitsize($num) {
		if(!preg_match("/^[0-9]+$/", $num)) return 0;
		$type = array( "B", "KB", "MB", "GB", "TB", "PB" );
		$j = 0;
		while($num >= 1024) {
    		if( $j >= 5 ) return $num.$type[$j];
    		$num = $num / 1024;
    		$j++;
   		}
   		return $num.$type[$j];
	}
	
	/**
	 * 方法库-数组去除空值
	 * @param string $num  数值
	 * @return string
	 */
	public function array_remove_empty(&$arr, $trim = true) {
		if (!is_array($arr)) return false;
		foreach($arr as $key => $value){
			if (is_array($value)) {
				self::array_remove_empty($arr[$key]);
			} else {
				$value = ($trim == true) ? trim($value) : $value;
				if ($value == "") {
					unset($arr[$key]);
				} else {
					$arr[$key] = $value;
				}
			}
		}
	}
	
   /**
	* 生成 options html 代码
	* @param array  $arr 健值数组
	* @param string $default 默认值
	* @return string htmlcode
	*/
	function generateHtmlOptions($arr, $default = null){
		$string = '';
		if (!is_array($arr) && count($arr)) return $string;
		foreach ($arr as $key => $val) {
			$selected = ($default == $key) ? 'selected' : '';
			$string .= '<option value="'.$key.'" '.$selected.'>';
			$string .=  $val;
			$string .= '</option>';
		}
		return $string;
	}
	
	/**
	 * 清空数组中的空值
	 * @param array $array
	 * @return array
	 */
	public function clear_array_null(&$array) {
		foreach($array as $k => $v){
			if(empty($v)) unset($array[$k]);
		}
		return $array;
	}
	
	/**
	 * 左边清空
	 * @param string $string
	 * @return string
	 */
	public function rltrim($string) {
		if (is_string($string)) return trim($string);
		foreach($string as $k => $v){
			$string[$k] = trim($v);
		}
		return $string;
	}
	
	/**
	 * 生成唯一的订单号 20110809111259232312
	 * 2011-年日期
	 * 08-月份
	 * 09-日期
	 * 11-小时
	 * 12-分
	 * 59-秒
	 * 2323-微秒
	 * 12-随机值
	 * @return string
	 */
	public function trade_no() {
		list($usec, $sec) = explode(" ", microtime());
		$usec = substr(str_replace('0.', '', $usec), 0 ,4);
		$str  = rand(10,99);
		return date("YmdHis").$usec.$str;
	}
	
	/**
     * 获取接受JS传递中文编码函数
     * 作者：Min
     * @param string $str
     * @return string 
     */
    public function js_unescape($str){  
        $ret = '';  
        $len = strlen($str);  
      
        for ($i = 0; $i < $len; $i++) {  
            if ($str[$i] == '%' && $str[$i+1] == 'u') {  
                $val = hexdec(substr($str, $i+2, 4));  
                if ($val < 0x7f) $ret .= chr($val);  
                else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));  
                else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));  
                $i += 5;  
            } else if ($str[$i] == '%') {  
                $ret .= urldecode(substr($str, $i, 3));  
                $i += 2;  
            }  
            else $ret .= $str[$i];  
        }  
        return $ret;  
    }
	
}