<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-日期处理
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class dateInit {  
	
	private $year, $month, $day;  //定义年 月 日
	
	/**
	 *	日期-设置日期
	 * 	@param string   $date   日期格式2010-10-10
	 *  @return
	 */
	public function set_date($date = '') { 
		if ($date !== '') {
			list($year, $month, $day) = explode('-', $date);
			$this->set_year($year);
			$this->set_month($month);
			$this->set_day($day); 
		} else {
			$this->set_year(date('Y'));
			$this->set_month(date('m'));
			$this->set_day(date('d'));
		}
	} 
	
	/**
	 *	日期-增加天数
	 * 	@param  int  $day_num  多少天
	 *  @return int
	 */
	public function add_day($day_num = 1) {
		$day_num = (int) $day_num;
		$day_num = $day_num * 86400;
		$time = $this->get_time() + $day_num;
		$this->set_year(date('Y', $time));
		$this->set_month(date('m', $time));
		$this->set_day(date('d', $time));
		return $this->get_date();
	}

	/**
	 *	日期-获取当月最后一天
	 *  @return int
	 */
	public function get_lastday() {
		if($this->month==2) {
			$lastday = $this->is_leapyear($this->year) ? 29 : 28;
		} elseif($this->month==4 || $this->month==6 || $this->month==9 || $this->month==11) {
			$lastday = 30;
		} else {
			$lastday = 31;
		}
		return $lastday;
	}
	
	/**
	 *	日期-获取星期几
	 *  @return int
	 */
	public function get_week() {
		return date('w', $this->get_time());
	}
	
	/**
	 *	日期-是否是闰年
	 *  @return int
	 */
	public function is_leapyear($year) {
		return date('L', $year);
	}
	
	/**
	 *	日期-获取当前日期
	 *  @return string 返回：2010-10-10
	 */
	public function get_date() {
		return $this->year.'-'.$this->month.'-'.$this->day;
	}
	
	/**
	 *	日期-获取当前日期-不包含年-一般用户获取生日
	 *  @return string 返回：10-10
	 */
	public function get_birthday() {
		return $this->month.'-'.$this->day;
	}

	/**
	 *	日期-返回时间戳
	 *  @return int
	 */
	public function get_time() {
		return strtotime($this->get_date().' 23:59:59');
	}
	
	/**
	 *	日期-计算2个日期的差值
	 *  @return int
	 */
	public function get_difference($date, $new_date) {
		$date = strtotime($date);
		$new_date = strtotime($new_date);
		return abs(ceil(($date - $new_date)/86400));
	}
	
	/**
	 * 获取星期几
	 * @param int $week 处国人的星期，是一个数值，默认为null则使用当前时间
	 * @return string
	 */
	public static function getChinaWeek($week = null) {
		$week = $week ? $week : (int) date('w', time());
		$weekArr = array("星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
		return $weekArr[$week];
	}
	
	/**
	 *	日期-设置年
	 * 	@param string  $year   年
	 *  @return
	 */
	private function set_year($year) {
		$year = (int) $year;
		$this->year = ($year <= 2100 && $year >= 1970) ? $year : date('Y');
	}
	
	/**
	 *	日期-设置月
	 * 	@param string  month  月
	 *  @return
	 */
	private function set_month($month) {
		$month = ltrim((int) $month, '0');
		$this->month = ($month < 13 && $month > 0) ? $month : date('m');
	}
	
	/**
	 *	日期-设置日
	 * 	@param string  day  天
	 *  @return
	 */
	private function set_day($day) {
		$day = ltrim((int) $day, '0');
		$this->day = ($this->year && $this->month && checkdate($this->month, $day, $this->year)) ? $day : date('d');
	}
	
}
