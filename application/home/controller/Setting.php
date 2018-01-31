<?php
namespace app\home\controller;
use think\Controller;

class Setting extends Base {

	/**
	 * [menu 栏目管理]
	 */
	public function menu() {
		$all_menu = model('Menu')->getAllMenu(1,1);		//获取所有菜单
		$this->assign('allmenu',get_tree($all_menu));
		return $this->fetch();
	}

	/**
	 * [editmenu 添加菜单]
	 */
	public function addmenu() {
		if (request()->isPost()) {
			if (empty(input('post.title'))){
				$this->error(config('MSG.TITLE_REQUIRE'));
			}
			$data = array();
			$data['parent_id'] = input('post.parent_id',0,intval);
			$data['title'] = input('post.title','',trim);
			$data['url'] = input('post.url',0,trim);
			$data['icon'] = input('post.icon','',trim);
			$data['status'] = input('post.status',0,intval);
			$data['sort'] = input('post.sort',255,intval);
			$res = db('menu')->insert($data);
			if ($res !== false) {
				model('Menu')->updateCache();	//更新菜单缓存
				session('umenu',null);
				$umenu = model('Menu')->getUserMenu(session('gid'));
				session('umenu',$umenu);
				$this->success(config('MSG.SAVE_SUCCESS'));
			} else {
				$this->error(config('MSG.SAVE_ERROR'));
			}
		}
	}

	/**
	 * [editmenu 编辑菜单]
	 */
	public function editmenu() {
		if (request()->isPost()) {
			if (!isset($_POST['type'])) {
				$data = array();
				$data['id'] = input('post.id',0,intval);
				$data['parent_id'] = input('post.parent_id',0,intval);
				$data['title'] = input('post.title','',trim);
				$data['url'] = input('post.url',0,trim);
				$data['icon'] = input('post.icon','',trim);
				$data['status'] = input('post.status',0,intval);
				$data['sort'] = input('post.sort',255,intval);
				$res = db('menu')->update($data);
				if ($res !== false) {
					model('Menu')->updateCache();	//更新菜单缓存
					session('umenu',null);
					$umenu = model('Menu')->getUserMenu(session('gid'));
					session('umenu',$umenu);
					//$this->success(config('MSG.SAVE_SUCCESS'));
					return ['code' => 1, 'msg' => '提交成功'];
				} else {
					//$this->error(config('MSG.SAVE_ERROR'));
					//$this->error('提交失败');
					return ['code' => 0, 'msg' => '提交失败'];
				}
			} else {
				$id = input('post.id',0,intval);
				$res = model('menu')->getOneMenu($id);
				if($res){
					return ['code' => 1, 'msg' => $res];
				}else{
					//$this->error(config('MSG.CONTENT_ERROR'));
					//$this->error('内容有误');
					return ['code' => 0, 'msg' => '内容有误'];
				}
			}
		}
	}

	/**
	 * [delmenu 编辑菜单]
	 */
	public function delmenu()
	{
		if (request()->isPost()) {
			if (empty(input('post.id'))){
				$this->error(config('MSG.PARAM_ERROR'));
			}
			$db = db('menu');
			$id = input('post.id',0,intval);
			$res =$db->delete($id);
			if($res){
				$res = model('menu')->getOneMenu($id);
				if($res['parent_id'] == 0){
					$db->where(array('parent_id'=>$id))->delete();
				}
				model('Menu')->updateCache();	//更新菜单缓存
				session('umenu',null);
				$umenu = model('Menu')->getUserMenu(session('gid'));
				session('umenu',$umenu);
				$this->success(config('MSG.DELETE_SUCCESS'));
			}else{
				$this->error(config('MSG.DELETE_ERROR'));
			}
		}
	}
}