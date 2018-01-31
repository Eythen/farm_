<?php
/**
 * 权限组模型
 */
namespace app\home\Model;
use think\Model;

class Auth extends Model {

	protected $table = 'yq_auth_group';

	/**
	 * [getAuthMenu 获取所属权限组菜单]
	 * @param  [int] $authId [权限组id]
	 * @return [array]		 [返回菜单数组]
	 */
	public function getGroupMenu($authId){
		$where['id'] = $authId;
		$where['status'] = 1;
		$res = db('auth_group')->field('menu')->where($where)->find();
		return $res['menu'];
	}
}