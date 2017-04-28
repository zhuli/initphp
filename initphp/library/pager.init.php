<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-分页类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/ 
class pagerInit { 

	private $show_num = 9; //分页中显示多少个
	private $conf = array(
		'first_last'=> 1, //首页-尾页 0-关闭 1-开启
		'back_next' => 1, //上一页-下一页 0-关闭 1-开启
		'total_num' => 1, //是否显示总页数 0-关闭 1-开启
		'page_num'  => 1, //翻页数 0-关闭 1-开启
		'select'    => 1  //下拉列表选择 0-关闭 1-开启
	);
	private $style_config = '<style type="text/css">
	.InitPHP_pages {font:12px/1.6em Helvetica, Arial, sans-serif;overflow:hidden; text-align:center; font-family:Verdana;margin-bottom:5px;  }
	.InitPHP_pages a, .pages{ margin:0 1px; padding:1px 6px; border:1px solid #E4E4E4; text-decoration:none!important; }
	.InitPHP_pages a:hover { border-color:#369; }
	.InitPHP_pages strong { margin:0 1px; padding:2px 6px; border-color:#369; background:#369; color:#FFF; text-decoration:none!important; }
	.InitPHP_pages .back { padding:4px 6px 1px 20px!important; padding:4px 6px 2 20px;  font-family:simsun; }
	.InitPHP_pages .next { padding:4px 20px 1px 6px!important; padding:4px 20px 2 6px; font-family:simsun; }
	.InitPHP_pages .first { padding:4px 6px 1px 4px!important; padding:4px 6px 2 4px;  font-family:simsun; }
	.InitPHP_pages .last { padding:4px 4px 1px 6px!important; padding:4px 4px 2 6px; font-family:simsun; }
	</style>';
	
	/**
	 *	分页-分页入口
	 * 	@param int  $count   总共多少数据
	 * 	@param int  $prepage 每页显示多少条 
	 * 	@param int  $url     URL 
	 *  @return string
	 */
	public function pager($count, $prepage, $url, $default_style = false) {
		$count    = (int) $count;
		$prepage  = (int) $prepage; 
		$page_num = ceil($count / $prepage); //总共多少页
		$page     = (int) $_GET['page'];
		$page     = ($page > $page_num) ? $page_num : ($page = ($page < 1) ? 1 : $page);
		$url      = (strpos($url, '?') === false) ? $url . '?' : $url;
		return $this->pager_html($page_num, $url, $page, $default_style);
	}
	
	/**
	 *	分页-获取分页HTML显示
	 * 	@param int    $page_num 页数
	 * 	@param string $url      URL 
	 * 	@param int    $page     当前页
	 *  @return string
	 */
	private function pager_html($page_num, $url, $page, $default_style) {
		list($start, $end) = $this->get_satrt_and_end($page, $page_num);
		list($back, $next) = $this->get_pager_next_back_html($url, $page, $page_num);
		list($first, $last) = $this->get_first_last_html($page_num, $url);
		if ($default_style == true) {
			$html = $this->style_config . "<div class='InitPHP_pages'>";
		} else {
			$html = "<div class='InitPHP_pages'>";
		}
		$html .= $back;
		$html .= $first;
		$html .= $this->get_pager_num_html($start, $end, $url, $page);
		$html .= $last;
		$html .= $next;
		$html .= $this->get_total_num_html($page_num);
		$html .= $this->get_select_html($page_num, $url, $page);
		$html .= '</div>';
		return $html;
	}
		
	/**
	 *	分页-获取分页数字的列表
	 * 	@param int     $start  开始数
	 * 	@param int     $end    结束数
	 * 	@param string  $url    URL地址
	 * 	@param int    $page     当前页
	 *  @return string
	 */
	private function get_pager_num_html($start, $end, $url, $page) {
		if ($this->conf['page_num'] == 0) return ''; //是否开启
		$html = '';
		for ($i=$start; $i<=$end; $i++) {
			if ($i == $page) {
				$html .= "<strong>{$i}</strong>";
			} else {
				$html .= "<a href='{$url}&page={$i}'>{$i}</a>";
			}
		}
		return $html;
	}
	
	/**
	 *	分页-分页总页数显示
	 * 	@param int  $page_num 页数
	 *  @return string
	 */
	private function get_total_num_html($page_num) {
		if ($this->conf['total_num'] == 0) return ''; //是否开启
		return "&nbsp;&nbsp;共{$page_num}页";
	}
	
	/**
	 *	分页-分页首页和尾页显示
	 * 	@param int  $page_num 页数
	 * 	@param string  $url    URL地址
	 *  @return string
	 */
	private function get_first_last_html($page_num, $url) {
		if ($this->conf['first_last'] == 0) return array('', ''); //是否开启
		$first = "<a href='{$url}&page=1' class='first'>首页</a>";
		$last  = "<a href='{$url}&page={$page_num}' class='last'>尾页</a>";
		return array($first, $last);
	}
	
	/**
	 *	分页-获取分页上一页-下一页HTML
	 * 	@param string  $url      URL地址
	 * 	@param int     $page     当前页
	 * 	@param int     $page_num 页数
	 *  @return string
	 */
	private function get_pager_next_back_html($url, $page, $page_num) {
		if ($this->conf['back_next'] == 0) return array('', ''); //是否开启
		$next_page = $page + 1;
		$next = "<a href='{$url}&page={$next_page}' class='next'>下一条</a>";
		if ($page == $page_num) $next = '';
		$back_page = $page - 1;
		$back = "<a href='{$url}&page={$back_page}' class='back'>上一条</a>";
		if ($page == 1) $back = '';
		return array($back, $next);
	}
	
	/**
	 *	分页-Select选择器
	 * 	@param int     $page_num 页数
	 * 	@param string  $url      URL地址
	 * 	@param int     $page     当前页
	 *  @return string
	 */
	private function get_select_html($page_num, $url, $page) {
		if ($this->conf['select'] == 0) return '';
		$html = '&nbsp;&nbsp;<select name="select" onchange="javascript:window.location.href=this.options[this.selectedIndex].value">';
		for ($i=1; $i<=$page_num; $i++){
            if ($page == $i) {
				$selected = ' selected';
			} else {
				$selected = '';
			}
            $html.="<option value='{$url}&page={$i}' {$selected}>{$i}</option>";
        }
		$html .= '</select>';
		return $html;
	}
	
	/**
	 *	分页-获取分页显示数字
	 * 	@param int  $page     当前页
	 * 	@param int  $page_num 页数
	 *  @return array(start, end)
	 */
	private function get_satrt_and_end($page, $page_num) {
		$temp = floor($this->show_num / 2);
		if ($page_num < $this->show_num) return array(1, $page_num);
		if ($page <= $temp) {
			$start = 1;
			$end = $this->show_num;
		} elseif (($page_num - $temp) < $page) {
			$start = $page_num - $this->show_num + 1;
			$end  = $page_num;
		} else {
			$start = $page - $temp;
			$end   = $page - $temp + $this->show_num - 1;
		}
		return array($start, $end);
	}
}
