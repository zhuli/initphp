<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   View-view 模板核心文件类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *------------------------------------------------------------------------------- 
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class templateInit {

	private $template_path      = 'template'; //模板目录
	private $template_c_path    = 'template_c'; //编译目录
	private $template_type      = 'htm'; //模板文件类型
	private $template_c_type    = 'tpl.php'; //模板编译文件类型
	private $template_tag_left  = '<!--{'; //左标签
	private $template_tag_right = '}-->'; //右标签
	private $is_compile 		= true; //是否需要每次编译
	private $driver_config;
	private static $driver      = NULL; //定义默认的一个模板编译驱动模型
	
	/**
	 * 模板编译-设置模板信息
	 * Controller中使用方法：$this->view->set_template_config($config)
	 * @param  array $config 设置参数
	 * @return bool
	 */
	public function set_template_config($config) {
		if (!is_array($config)) return false;
		$config['template_path']   = InitPHP::getAppPath($config['template_path']);
		$config['template_c_path'] = InitPHP::getAppPath($config['template_c_path']);
		if (isset($config['theme']) && !empty($config['theme'])) { //模板主题实现
			$config['template_path'] = $config['template_path'] . '/' . $config['theme'];
			$config['template_c_path'] = $config['template_c_path'] . '/' . $config['theme'];
			if ($config['is_compile'] == true) $this->create_dir($config['template_c_path']); //创建主题文件夹
		}
		if (isset($config['template_path'])) 
			$this->template_path = $config['template_path'];
		if (isset($config['template_c_path'])) 
			$this->template_c_path = $config['template_c_path'];
		if (isset($config['template_type'])) 
			$this->template_type = $config['template_type'];
		if (isset($config['template_c_type'])) 
			$this->template_c_type = $config['template_c_type'];
		if (isset($config['template_tag_left'])) 
			$this->template_tag_left = $config['template_tag_left'];
		if (isset($config['template_tag_right'])) 
			$this->template_tag_right = $config['template_tag_right'];
		if (isset($config['is_compile'])) 
			$this->is_compile = $config['is_compile'];
		$this->driver_config = $config['driver'];
		return true;
	}
	
	/**
	 * 模板编译-模板类入口函数
	 * 1. 获取模板，如果模板未编译，则编译
	 * @param  string $filename 文件名称，例如：test，不带文件.htm类型
	 * @return string
	 */
	protected function template_run($file_name) {
		$this->check_path(); //检测模板目录和编译目录
		list($template_file_name, $compile_file_name) = $this->get_file_name($file_name);
		if (($this->is_compile == true) || ($this->is_compile == false && !file_exists($compile_file_name))) { //是否强制编译
			$str = $this->read_template($template_file_name);
			$str = $this->layout($str); //layout模板页面中加载模板页
			$str = $this->replace_tag($str);
			$str = $this->compile_version($str, $template_file_name);
			$this->compile_template($compile_file_name, $str);
		}
		return $compile_file_name;
	}
	
	/**
	 * 模板编译-读取静态模板
	 * @param  string $filename 文件名称，例如：test，不带文件.htm类型
	 * @return 
	 */
	private function read_template($template_file_name) {
		if (!file_exists($template_file_name)) InitPHP::initError($template_file_name. ' is not exist!');
		return @file_get_contents($template_file_name);
	}
	
	/**
	 * 模板编译-编译模板
	 * @param  string $filename 文件名称，例如：test，不带文件.htm类型
	 * @param  string $str 写入编译文件的数据
	 * @return 
	 */
	private function compile_template($compile_file_name, $str) {
		if (($path = dirname($compile_file_name)) !== $this->template_c_path) { //自动创建文件夹
			$this->create_dir($path); 
		}
		$ret = @file_put_contents($compile_file_name, $str);
		if ($ret == false) {
			InitPHP::initError("Please check the Directory have read/write permissions. If it's not, please set 777 limits. Can not write " . $compile_file_name);
		}
	}
	
	/**
	 * 模板编译-通过传入的filename，获取要编译的静态页面和生成编译文件的文件名
	 * @param  string $filename 文件名称，例如：test，不带文件.htm类型
	 * @return 
	 */
	private function get_file_name($file_name) {
		return array(
			$this->template_path .'/'. $file_name . '.' . $this->template_type, //组装模板文件路径
			$this->template_c_path .'/'. $file_name . '.' . $this->template_c_type //模板编译路径
		);
	}
	
	/**
	 * 模板编译-检测模板目录和编译目录是否可写
	 * @return 
	 */
	private function check_path() {
		if (!is_dir($this->template_path) || !is_readable($this->template_path)) InitPHP::initError('template path is unread!');
		if (!is_dir($this->template_c_path) || !is_readable($this->template_c_path)) InitPHP::initError('compiled path is unread!');
		return true;
	}
	
	/**
	 * 模板编译-编译文件-头部版本信息
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	private function compile_version($str, $template_file_name) {
		$version_str = '<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on ' .date('Y-m-d H:i:s');
		$version_str .= ', compiled from '. $template_file_name . ' */ ?>' . "\r\n";
		return $version_str . $str;
	}
	
	/**
	 * 模板编译-标签正则替换
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	private function replace_tag($str) {
		$this->get_driver($this->driver_config);
		return self::$driver->init($str, $this->template_tag_left, $this->template_tag_right); //编译
	}
	
	/**
	 * 模板编译-layout 模板layout加载机制
	 * 1. 在HTML模板中直接使用<!--{layout:user/version}-->就可以调用模板
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	private function layout($str) {
		preg_match_all("/(".$this->template_tag_left."layout:)(.*)(".$this->template_tag_right.")/", $str, $matches);
		$matches[2] = array_unique($matches[2]); //重复值移除
		$matches[0] = array_unique($matches[0]);
		foreach ($matches[2] as $val) $this->template_run($val);
		foreach ($matches[0] as $k => $v) {
			$str = str_replace($v, $this->layout_path($matches[2][$k]), $str);
		}
		return $str; 
	}
	
	/**
	 * 模板编译-layout路径
	 * @param  string $template_name 模板名称
	 * @return string
	 */
	private function layout_path($template_name) {
		return "<?php include('".$this->template_c_path.'/'.$template_name.'.'.$this->template_c_type."'); ?>";
	}
	
	/**
	 * 模板编译-获取不同
	 * @param  string $template_name 模板名称
	 * @return string
	 */
	private function get_driver($driver) {
		$diver_path = 'driver/' . $driver . '.init.php';
		if (self::$driver === NULL) {
			require_once($diver_path);
			$class = $driver . 'Init';
			if (!class_exists($class)) InitPHP::initError('class' . $class . ' is not exist!');
			$init_class = new $class;
			self::$driver = $init_class;
		}
		return self::$driver;
	}
	
	/**
	 *	创建目录
	 * 	@param  string  $path   目录
	 *  @return 
	 */
	private function create_dir($path) {
		if (is_dir($path)) return false;
		$this->create_dir(dirname($path));
		@mkdir($path);
		@chmod($path, 0777);
		return true;
	}

}
class viewInit extends templateInit {

	public  $view = array(); //视图变量 
	private $template_arr = array(); //视图存放器
	private $remove_tpl_arr = array(); //待移除
	
	/**
	 * 模板-设置模板变量-将PHP中是变量输出到模板上，都需要经过此函数
	 * 1. 模板赋值核心函数，$key => $value , 模板变量名称 => 控制器中变量
	 * 2. 也可以直接用$this->view->view['key'] = $value
	 * 3. 模板中对应$key
	 * Controller中使用方法：$this->view->assign($key, $value);
	 * @param  string  $key   KEY值-模板中的变量名称
	 * @param  string  $value value值
	 * @return array
	 */
	public function assign($key, $value) {
		$this->view[$key] = $value;
	}
	
	/**
	 * 模板-设置模板 设置HTML模板
	 * 1. 设置模板，模板名称和类型，类型选择F和L的时候，是最先显示和最后显示的模板
	 * 2. 比如设置 user目录下的userinfo.htm目录，则 set_tpl('user/userinfo') 不需要填写.htm
	 * Controller中使用方法：$this->view->set_tpl($template_name, $type = '');
	 * @param  string  $template_name 模板名称
	 * @param  string  $type 类型，F-头模板，L-脚步模板
	 * @return array
	 */
	public function set_tpl($template_name, $type = '') {
		if ($type == 'F') {
			$this->template_arr['F'] = $template_name;
		} elseif ($type == 'L') {
			$this->template_arr['L'] = $template_name;
		} else {
			$this->template_arr[] = $template_name;
		}
	}
	
	/**
	 * 模板-移除模板
	 * 1. 如果在控制器的基类中已经导入头部和脚步模板，应用中需要替换头部模板
	 * 2. 移除模板需要在display() 模板显示前使用
	 * Controller中使用方法：$this->view->remove_tpl($remove_tpl);
	 * @param string $remove_tpl 需要移除模板名称
	 * @return array
	 */
	public function remove_tpl($remove_tpl) {
		$this->remove_tpl_arr[] = $remove_tpl;
	}
	
	/**
	 * 模板-获取模板数组
	 * Controller中使用方法：$this->view->get_tpl();
	 * @return array
	 */
	public function get_tpl() {
		return $this->template_arr;
	}
	
	/**
	 * 模板-显示视图
	 * 1. 在Controller中需要显示模板，就必须调用该函数
	 * 2. 模板解析可以设置 $InitPHP_conf['isviewfilter'] 值,对变量进行过滤
	 * Controller中使用方法：$this->view->display();
	 * @return array
	 */
	public function display($template = '') {
		if ($template != '') {
			$this->set_tpl($template);
		}
		$InitPHP_conf = InitPHP::getConfig();
		if (is_array($this->view)) {
			if ($InitPHP_conf['isviewfilter']) $this->out_put($this->view);
			foreach ($this->view as $key => $val) {
				$$key = $val;
			}
		}
		$this->template_arr = $this->parse_template_arr($this->template_arr); //模板设置
		foreach ($this->template_arr as $file_name) {
			if (in_array($file_name, $this->remove_tpl_arr)) continue;
			$complie_file_name = $this->template_run($file_name); //模板编译
			if (!file_exists($complie_file_name)) InitPHP::initError($complie_file_name. ' is not exist!');
			include_once($complie_file_name);
		}
	}
	
	/**
	 * 模板-处理视图存放器数组，分离头模板和脚模板顺序
	 * @param  array  $arr 视图存放器数组
	 * @return array
	 */
	private function parse_template_arr(array $arr) {
		$temp = $arr;
		unset($temp['F'], $temp['L']);
		if (isset($this->template_arr['F'])) { //头模板
			array_unshift($temp, $this->template_arr['F']);
		}
		if (isset($this->template_arr['L'])) {
			array_push($temp, $this->template_arr['L']);
		}
		return $temp;	
	}
	
	/**
	 * 模板-模板变量输出过滤
	 * @param  array  $arr 视图存放器数组
	 * @return array
	 */
	private function out_put(&$value) {
		$value = (array) $value;
		foreach ($value as $key => $val) {
			if (is_array($val)) {
				self::out_put($value[$key]);
			} elseif (is_object($val)) {
				$value[$key] = $val;
			} else {
				if (function_exists('htmlspecialchars')) {
					$value[$key] =  htmlspecialchars($val);
				} else {
					$value[$key] =  str_replace(array("&", '"', "'", "<", ">", "%3C", "%3E"), array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;", "&lt;", "&gt;"), $val);
				}
			}
		}
	}
	
}
