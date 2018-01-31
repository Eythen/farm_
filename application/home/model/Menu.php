<?php
/**
 * 系统菜单模型
 */
namespace app\home\model;
use think\Model;

class Menu extends Model {

	//private static $_userMenu = 'user_menu';	//缓存名称
	protected $_userMenu = 'user_menu';	//缓存名称
	//public $_userMenu = 'user_menu';	//缓存名称
	protected $table = 'yq_menu';

	/**
	 * [getAllMenu 获取数据库所有菜单]
	 * @param  [int] $sort	[按序号排序：0-关闭1-打开]
	 * @param  [int] $status	[状态情况:1-获取全部0-返回正常值]
	 * @param  [int] $parent_id	[父级栏目ID]
	 * @return [string] [返回菜单数组]
	 */
	public function getAllMenu($sort = 0, $status = 0, $parent_id = '') {
		if(empty($status)){
			$where['status'] = 1;
		}
		if($parent_id){
			$where['parent_id'] = $parent_id;
		}
		$order = $sort ? 'parent_id ASC,sort ASC,id ASC' : '';
		//$res = $this->field('id,parent_id,title,url,icon,sort')->where($where)->order($order)->select();
		$res = db('menu')->field('id,parent_id,title,url,icon,sort')->where($where)->order($order)->select();

		//$res =  $res->toArray()["data"];  




		$data = array();
		foreach ($res as $k => $v) {
			$data[$v['id']] = $v;
		}

		return $data;
	}

	/**
	 * [updateCache 更新菜单缓存]
	 */
	public function updateCache() {
		$menu =	$this->getAllMenu(1);

		if (is_array($menu) && !empty($menu)) {
			cache($this->_userMenu, $menu);
		}
	}

	/**
	 * [getMenu 获取菜单]
	 * @param  [int] $type	[获取菜单来源：0-缓存1-数据库]
	 * @return [array]      [返回菜单数组]
	 */
	public function getMenu($type = 1) {
		if ($type) {
			$menu = $this->getAllMenu();
		} else {
			$menu = cache($this->_userMenu);
		}
		return $menu;
	}

	/**
	 * [getOneMenu 获取单独菜单]
	 * @param  [int] $id	[菜单ID]
	 * @return [array]      [返回菜单数组]
	 */
	public function getOneMenu($id) {
		$result = $this->where(array('id'=>$id))->find();
		return $result;
	}

	/**
	 * [getUserMenu 获取用户所属菜单]
	 * @param  [int] $gId [用户组ID]
	 * @return [array]    [返回菜单数组]
	 */
	public function getUserMenu($gId) {
		$menu = array();
		$user_menu = explode(',', model('Auth')->getGroupMenu($gId)); 	//获取用户菜单ID
		$all = $this->getMenu();	//获取所有菜单

		//dump($all);


		if(is_array($all) && !empty($all)){
			foreach ($user_menu as $k => $v) {
				if(!isset($all[$v])){
					continue;
				}
				if (!isset($menu[$v]) && $all[$v]['parent_id'] == 0) {
					$menu[$v] = $all[$v];
				}elseif ($all[$v]['parent_id'] != 0) {
					$pid1 = $all[$v]['parent_id'];
					$pid2 = $all[$pid1]['parent_id'];
					if ($pid2 == 0){
						if (!isset($menu[$pid1])){
							$menu[$pid1] = $all[$pid1];
						}
						if (!isset($menu[$pid1]['class2'][$v])) {
							$menu[$pid1]['class2'][$v] = $all[$v];
						}
						$menu[$pid1]['class2'] = array_sort($menu[$pid1]['class2'],'sort');
					} else {
						if (!isset($menu[$pid2])){
							$menu[$pid2] = $all[$pid2];
						}
						if (!isset($menu[$pid2]['class2'][$pid1])){
							$menu[$pid2]['class2'][$pid1] = $all[$pid1];
						}
						$menu[$pid2]['class2'][$pid1]['class3'][$v] = $all[$v];
						$menu[$pid2]['class2'][$pid1]['class3'] = array_sort($menu[$pid2]['class2'][$pid1]['class3'],'sort');
					}
				}
				unset($pid1);
				unset($pid2);
			}
		}
		//dump($menu);
		return array_sort($menu,'sort');
	}
}