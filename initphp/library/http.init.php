<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-HTTP
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class httpInit {
	
	private $defaultFlen  = 8192;//fread默认读取最大长度
	private $block        = true;//读取网络流 是否阻塞，true|阻塞，false|不阻塞
	private $defaultPort  = 80;  //默认端口号
	
	/**
 	 * 通过http方式获取数据
 	 * 使用方法：$temp = $obj->httpGet($url, $post, $ip, $timeout=15, $cookie='', $freadLen=0);
 	 * 返回：HTTP获取处理后的信息
 	 * @param  string $url      链接地址
 	 * @param  array  $post     post内容，没有则为空
 	 * @param  string $ip       ip地址
 	 * @param  int    $timeout  超时时间
 	 * @param  string $cookie   cookie
 	 * @param  int    $freadLen 长度
 	 * @return string
	 */
	public function httpGet($url, $post, $ip='', $timeout=15, $cookie='', $freadLen=0) {
		//处理参数
		$data = $this->parseParam($url, $post, $ip, $timeout, $cookie, $freadLen);
		list($url, $post, $ip, $timeout, $port, $cookie, $freadLen) = array($data['url'], $data['post'], $data['ip'], $data['timeout'], $data['port'], $data['cookie'], $data['freadLen']);
		unset($data);
		//Header信息
		$httpReturn = $httpHeader = "";
		$httpHeader = ($post !== '') ? $this->httpPostHeader($url, $post, $ip, $port, $cookie) : $this->httpGetHeader($url, $post, $ip, $port, $cookie);	
		//socket
		$httpFp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
		@stream_set_blocking($httpFp, $this->block);
		@stream_set_timeout($httpFp, $timeout);
		@fwrite($httpFp, $httpHeader);
		$status = @stream_get_meta_data($httpFp);
		if ($httpFp == false || $status['timed_out']) {
			fclose($httpFp);
			return $httpReturn;
		}
		//fread
		$freadLen = ($freadLen == 0) ? $this->defaultFlen : (($freadLen > $this->defaultFlen) ? $this->defaultFlen : $freadLen);
		$isHttpHeader = $stopFread = false;
		while (!feof($httpFp) && !$stopFread) {
			if ((!$isHttpHeader) && ($tempHttpReturn = @fgets($httpFp)) && ($tempHttpReturn == "\r\n" || $tempHttpReturn == "\n")) $isHttpHeader = true; 
			if ($isHttpHeader) {
				$httpReturn =  @fread($httpFp, $freadLen);	
				(strlen($httpReturn) > $freadLen) && $stopFread = true;
			}
		}
		fclose($httpFp);
		return $httpReturn;
	}
	
	/**
	 * HTTP POST方式的header头部信息封装
	 * @param  string $url      链接地址
	 * @param  string $post     post内容，没有则为空
	 * @param  string $ip       ip地址
	 * @param  string $port     端口号
	 * @param  string $cookie   cookie
	 * @return string
	 */
	private function httpPostHeader($url, $post, $ip, $port=80, $cookie='') {
		$httpHeader = '';
		$httpHeader .= "POST $url HTTP/1.0\r\n";
		$httpHeader .= "Accept: */*\r\n";
		$httpHeader .= "Accept-Language: zh-cn\r\n";
		$httpHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$httpHeader .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$httpHeader .= "Content-Length: ".strlen($post)."\r\n";
		$httpHeader .= "Host: $ip:".(int)$port."\r\n";
		$httpHeader .= "Connection: Close\r\n";
		$httpHeader .= "Cache-Control: no-cache\r\n";
		$httpHeader .= "Cookie: $cookie\r\n\r\n";
		$httpHeader .= $post;
		return $httpHeader;
	}
	
	/**
	 * HTTP GET方式的header头部信息封装
	 * @param  string $url      链接地址
	 * @param  string $post     post内容，没有则为空
	 * @param  string $ip       ip地址
	 * @param  string $port     端口号
	 * @param  string $cookie   cookie
	 * @return string
	 */
	private function httpGetHeader($url, $post, $ip, $port=80, $cookie='') {
		$httpHeader = '';
		$httpHeader .= "GET $url HTTP/1.0\r\n";
		$httpHeader .= "Accept: */*\r\n";
		$httpHeader .= "Accept-Language: zh-cn\r\n";
		$httpHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$httpHeader .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$httpHeader .= "Host: $ip:".(int)$port."\r\n";
		$httpHeader .= "Connection: Close\r\n";
		$httpHeader .= "Cookie: $cookie\r\n\r\n";
		return $httpHeader;
	}
	
	/**
	 * 外部接收的数据进行内部处理
 	 * @param  string $url      链接地址
 	 * @param  array  $post     post内容，没有则为空
 	 * @param  string $ip       ip地址
 	 * @param  int    $timeout  超时时间
 	 * @param  string $cookie   cookie
 	 * @param  int    $freadLen 长度
 	 * @return array
	 */
	private function parseParam($url, $post, $ip, $timeout, $cookie, $freadLen) {
		$tempArr            = array();
		$tempArr['url']     = (string)$url;
		$tempArr['post']    = $this->parseArrToUrlstr((array)$post);
		$tempArr['ip']      = (string)$ip;
		$tempArr['timeout'] = (int)$timeout;
		$tempArr['port']    = $this->parsePort($url, $ip);
		$tempArr['cookie']  = (string)$cookie;
		$tempArr['freadLen']= (int)$freadLen;	
		$tempArr['ip']      = ($tempArr['ip'] == '') ? $this->parseUrlToHost($tempArr['url']) : $tempArr['ip'];
		return $tempArr;
	}
	
	/**
	 * 将数组参数转化成URL传输的参数
	 * 数组：array("age"=>"我是test"); 
	 * 转化：age=10&name=test 多维：a[age]=10&a[name]=test
	 * @param  array $arr 数组
	 * @return string
	 */
	private function parseArrToUrlstr($arr) {
		if (!is_array($arr)) return '';
		return http_build_query($arr, 'flags_');
	}
	
	/**
	 * 获取URL中的HOST信息
	 * URL：http://www.xxx.com/index.php
	 * 返回: www.xxx.com
	 * @param  string $url URL链接
	 * @return string
	 */
	private function parseUrlToHost($url) {
		$parse = @parse_url($url);
		$reg = '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/';
		if (empty($parse) || preg_match($reg,trim($url)) == false) return '';
		return str_replace(array('http://','https://'),array('','ssl://'),"$parse[scheme]://").$parse['host'];
	}
	
	/**
	 * 获取端口号,返回INT类型端口，如果端口不存在，为设置的默认端口号
	 * @param  string $url URL链接
	 * @param  string $ip  IP地址
	 * @return int
	 */
	private function parsePort($url, $ip) {
		$temp = array();
		if ($ip !== '') {
			if (strpos($ip, ':') === false) {
				$tempPort = $this->defaultPort;
			} else {
				$temp = explode(":", $ip);
				$tempPort = $temp[1];
			}
		} else {
			$temp = @parse_url($url);
			$tempPort = $temp['port'];
		}
		return ((int)$tempPort == 0) ? $this->defaultPort : $tempPort;
	}
}
