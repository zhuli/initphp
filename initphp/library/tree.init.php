<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 3.8.2 国产PHP开发框架   扩展类库-无限极分类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * Author:zhuli Dtime:2014-11-25 
***********************************************************************************/
class treeInit {
	
	private $parentid = 'parentid';
	private $id       = 'id';
	private $name     = 'name';
	
	/**
	 * 无限级分类树-初始化配置
	 * @param  array $config array('parentid'=>'', 'id' => '', 'name' =>'name')
	 * @return string|array
	 */
	public function init($config = array()) {
		if (!is_array($config)) return false;
		$this->parentid = (isset($config['parentid'])) ? $config['parentid'] : $this->parentid;
		$this->id = (isset($config['id'])) ? $config['id'] : $this->id;
		$this->name = (isset($config['name'])) ? $config['name'] : $this->name;
		return true;
	}
	
	/**
	 * 无限级分类树-获取树
	 * @param  array $tree 树的数组
	 * @param  int   $mid  初始化树时候，代表ID下的所有子集
	 * @param  int   $selectid  选中的ID值
	 * @param  string  $code  代码
	 * @param  string  $prefix  前缀
	 * @param  string  $selected  选中
	 * @return string|array
	 */
	public function get_tree($tree, $mid = 0, $selectid = 5, $code = "<option value='\$id' \$selecteds>\$prefix\$name</option>", $prefix = '|-', $selected = 'selected') {
		if (!is_array($tree)) return '';
		$temp = array();
		$string = '';
		foreach ($tree as $k => $v) {
			if ($v[$this->parentid] == $mid) {
				$id = $v[$this->id];
				$name = $v[$this->name];
				$selecteds = ($id == $selectid) ? $selected : '';
				eval("\$temp_code = \"$code\";");//转化
				$string .=  $temp_code;
				$string .=  $this->get_tree($tree, $v[$this->id], $selectid, $code, '&nbsp;&nbsp;' . $prefix);
			}
		}
		return $string ;
	}
	
	/**
	 * 无限级分类树-获取子类
	 * @param  array $tree 树的数组
	 * @param  int   $id   父类ID
	 * @return string|array
	 */
	public function get_child($tree, $id) {
		if (!is_array($tree)) return array();
		$temp = array();
		foreach ($tree as $k => $v) {
			if ($v[$this->parentid] == $id) {
				$temp[] = $v;
			}
		}
		return $temp;
	}
	
	/**
	 * 无限级分类树-获取父类
	 * @param  array $tree 树的数组
	 * @param  int   $id   子类ID
	 * @return string|array
	 */
	public function get_parent($tree, $id) {
		if (!is_array($tree)) return array();
		$temp = array();
		foreach ($tree as $k => $v) {
			$temp[$v[$this->id]] = $v;
		}
		$parentid = $temp[$id][$this->parentid];
		return $temp[$parentid];
	}
	
}
