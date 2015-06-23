<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   - 工具库-单元测试
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25
***********************************************************************************/
class unittestingInit {

	//预测值和返回结果的比较 类型比较|值=号比较|不等于比较|大于比较|小于比较
	private $test_type = array('type', '=', '!=', '!==','>', '<');
	private $data_temp = array(); //临时存放预测数据
	private $classname; //临时存放class
	private $testresult= array(); //预测的结果集
	
	/**
	 *	系统自动加载InitPHP类库
	 *  @param  string  $class_name  类名称
	 *  @param  string  $type        类所属类型
	 *  @return object
	 */
	public function run($file = '') {
		$InitPHP_conf = InitPHP::getConfig();
		if ($file == '') {
			$fileArr = $this->get_all_file();
		} elseif (is_array($file)) {
			$fileArr = (count($file) > 0) ? $file : $this->get_all_file();;
		} else {
			$fileArr = array($file);
		}
		foreach ($fileArr as $v) {
			$this->classname = $v;
			$test_file_name = $this->classname . $InitPHP_conf['unittesting']['test_postfix'];
			$test_file_path = $InitPHP_conf['unittesting']['path'] . $test_file_name . '.php';
			if (InitPHP::import($test_file_path)) {
				$obj = InitPHP::loadclass($test_file_name);
				$obj->run($this);
			}
		}
		$this->print_result();
	}
	
	/**
	 *	添加预测数据
	 *  @param  array   $function_param  方法传递的参数
	 *  @param  string  $forecast_result 预测结果的值
	 *  @param  string  $type            预测结果的类型
	 *  @return object
	 */
	public function add_data($function_param, $forecast_result, $type = '=') {
		if (!in_array($type, $this->test_type)) $type = '=';
		return $this->data_temp[] = array($function_param, $forecast_result, $type);
	}
	
	/**
	 *	预测数据
	 *  @param  array   $function 方法名称
	 *  @return object
	 */
	public function test($function) {
		$InitPHP_conf = InitPHP::getConfig();
		$obj = $this->get_service_obj();
		if ($obj) {
			if ($this->data_temp) {
				foreach ($this->data_temp as $v) {
					$result =call_user_func_array(array($obj, $function), $v[0]); 
					if ($v[2] == 'type') {
						$result_test = (strtolower(gettype($result)) == strtolower($v[1])) ? true : false;
					} elseif ($v[2] == '=') {
						$result_test = ($result == $v[1]) ? true : false;
					} elseif ($v[2] == '!=') {
						$result_test = ((int)$result != (int)$v[1]) ? true : false;
					} elseif ($v[2] == '!==') {
						$result_test = ($result !== $v[1])  ? true : false;
					} elseif ($v[2] == '>') {
						$result_test = ((int)$result > (int)$v[1]) ? true : false;
					} elseif ($v[2] == '<') {
						$result_test = ((int)$result < (int)$v[1]) ? true : false;
					}
					$this->testresult[] = array(
						'result' => $result_test, 
						'class'  => $this->classname . $InitPHP_conf['service']['service_postfix'],
						'func'   => $function,
						'data'   => $v[0]
					);
				}
				$this->data_temp = array();
			}
		}
	}
	
	/**
	 *	打印出结果数据
	 *  @return object
	 */
	private function print_result() {
		$tempError = array();
		$tempErrorCount = $tempYesCount = $tempAllCount = 0;
		foreach ($this->testresult as $v) {
			if (!$v['result']) {
				$tempError[] = $v;
				$tempErrorCount++;
			} else {
				$tempYesCount++;
			}
			$tempAllCount++;
		}
		echo '<div style="font-size:12px; margin:0px; background-color:#000000; color:#FFFFFF; border:solid 1px #FF9900; width:600px;  padding:10px;">
		<p>---------------------------------------------------InitPHP单元测试------------------------------------------------------------</p>
		<p>Query : '.$tempAllCount.' 条数据，OK : '.$tempYesCount.' 条数据，Error : '.$tempErrorCount.' 条数据</p>';
		if ($tempErrorCount > 0) {
			echo '<p>----------------------------------------------------------Error列表----------------------------------------------------------</p>';
			foreach ($tempError as $v) {
				echo '<p>类名：'.$v['class'].'&nbsp;&nbsp;&nbsp;&nbsp;方法名:'.$v['func'].'&nbsp;&nbsp;&nbsp;&nbsp;参数：';
				print_r($v['data']);
				echo '</p>';
			}
		}
		echo '</div>';
	}
	
	/**
	 *	获取运行的service
	 *  @return object
	 */
	private function get_service_obj() {
		$InitPHP_conf = InitPHP::getConfig();
		$test_file_name = $this->classname . $InitPHP_conf['service']['service_postfix'];
		if (InitPHP::import("$test_file_name" . ".php", array($InitPHP_conf['service']['path']))) {
			return InitPHP::loadclass($test_file_name);
		}
		return false;
	}
	
	/**
	 *	获取文件目录下所有文件
	 *  @return object
	 */
	private function get_all_file() {
		$InitPHP_conf = InitPHP::getConfig();
		$temp = array();
		$path = InitPHP::getAppPath($InitPHP_conf['unittesting']['path']);
		if (is_dir($path)) {
			if ($dh = opendir($path)){
				while (($file = readdir($dh)) !== false){
					if ($file == '.' || $file == '..') continue;
					$temp[] = str_replace($InitPHP_conf['unittesting']['test_postfix'] . '.php', '', $file);
				}
				closedir($dh);
			}
		}
		return $temp;
	}

}
?>