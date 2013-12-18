<?php 
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.3 国产PHP开发框架  扩展类库-CURL
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2013-5-29 
***********************************************************************************/
class curlInit {
	
	private $cookie = '/data/cookie.txt'; //cookie保存路径
	
	/**
	 * CURL-get方式获取数据
	 * @param string $url URL
	 * @param string $proxy 是否代理
	 * @param int    $timeout 请求时间
	 */
	public function get($url, $proxy = null, $timeout = 10) {
		if (!$url) return false;
		$ssl = substr($url, 0, 8) == 'https://' ? true : false;
		$curl = curl_init();
		if (!is_null($proxy)) curl_setopt ($curl, CURLOPT_PROXY, $proxy); 
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($ssl) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		}
		$cookie_file = $this->get_cookie_file();
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file); //连接结束后保存cookie信息的文件。 
    	curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);//包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。 
    	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //在HTTP请求中包含一个"User-Agent: "头的字符串。		
    	curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。 
    	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。 	
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //文件流形式		
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //设置cURL允许执行的最长秒数。 
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		curl_close($curl);
		if ($curl_errno > 0) {
			throw new Exception($curl_errno);
			return false;
		}
		return $content;
	}
	
	/**
	 * CURL-post方式获取数据
	 * @param string $url URL
	 * @param array  $data POST数据
	 * @param string $proxy 是否代理
	 * @param int    $timeout 请求时间
	 */
	public function post($url, $data, $proxy = null, $timeout = 10) {
		if (!$url) return false;
		if ($data) {
			$data = http_build_query($data);
		}
		$ssl = substr($url, 0, 8) == 'https://' ? true : false;
		$curl = curl_init();
		if (!is_null($proxy)) curl_setopt ($curl, CURLOPT_PROXY, $proxy); 
		curl_setopt($curl, CURLOPT_URL, $url);	
		if ($ssl) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		}
		$cookie_file = $this->get_cookie_file();
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file); //连接结束后保存cookie信息的文件。 
    	curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);//包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。 
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //在HTTP请求中包含一个"User-Agent: "头的字符串。		
    	curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。 
    	curl_setopt($curl, CURLOPT_POST, true); //发送一个常规的Post请求
    	curl_setopt($curl,  CURLOPT_POSTFIELDS, $data);//Post提交的数据包
    	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。 	
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //文件流形式		
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //设置cURL允许执行的最长秒数。 
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		curl_close($curl);
		if ($curl_errno > 0) {
			throw new Exception($curl_errno);
			return false;
		}
		return $content;
	}
	
	/**
	 * CURL-put方式获取数据
	 * @param string $url URL
	 * @param array  $data POST数据
	 * @param string $proxy 是否代理
	 * @param int    $timeout 请求时间
	 */
	public function put($url, $data, $proxy = null, $timeout = 10) {
		if (!$url) return false;
		if ($data) {
			$data = http_build_query($data);
		}
		$ssl = substr($url, 0, 8) == 'https://' ? true : false;
		$curl = curl_init();
		if (!is_null($proxy)) curl_setopt ($curl, CURLOPT_PROXY, $proxy); 
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($ssl) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		}
		$cookie_file = $this->get_cookie_file();
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file); //连接结束后保存cookie信息的文件。 
    	curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);//包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。 
    	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //在HTTP请求中包含一个"User-Agent: "头的字符串。		
    	curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。 
    	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。 	
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //文件流形式		
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //设置cURL允许执行的最长秒数
		$data = (is_array($data)) ? http_build_query($data) : $data; 
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT'); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($data))); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
 
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		curl_close($curl);
		if ($curl_errno > 0) {
			throw new Exception($curl_errno);
			return false;
		}
		return $content;
	}
	
	/**
	 * CURL-DEL方式获取数据
	 * @param string $url URL
	 * @param array  $data POST数据
	 * @param string $proxy 是否代理
	 * @param int    $timeout 请求时间
	 */
	public function del($url, $data, $proxy = null, $timeout = 10) {
		if (!$url) return false;
		if ($data) {
			$data = http_build_query($data);
		}
		$ssl = substr($url, 0, 8) == 'https://' ? true : false;
		$curl = curl_init();
		if (!is_null($proxy)) curl_setopt ($curl, CURLOPT_PROXY, $proxy); 
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($ssl) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		}
		$cookie_file = $this->get_cookie_file();
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file); //连接结束后保存cookie信息的文件。 
    	curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);//包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。 
    	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //在HTTP请求中包含一个"User-Agent: "头的字符串。		
    	curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。 
    	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。 	
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //文件流形式		
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //设置cURL允许执行的最长秒数
		$data = (is_array($data)) ? http_build_query($data) : $data; 
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DEL'); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($data))); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
 
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		curl_close($curl);
		if ($curl_errno > 0) {
			throw new Exception($curl_errno);
			return false;
		}
		return $content;
	}
	
	/**
	 * 获取COOKIE存放文件的地址
	 */
	private function get_cookie_file() {
		return APP_PATH . $this->cookie;
	}
	
 }