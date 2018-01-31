<?php
namespace app\home\controller;
use think\Controller;
use think\Db;

class Group extends Base {

	public function index(){
		if(request()->isAjax()){
			$request = input('request.');
			$offset = $request['offset'];
			$limit = $request['limit']? $request['limit']: 20;
			$order = $request['order'];
			$sort = $request['sort'];
			$data['rows'] = db('auth_group')->order($sort.' '.$order)->limit($offset.','.$limit)->select();
			$data['total'] = db('auth_group')->count();
			return $data;
		}
		return $this->fetch();
	}

	public function edit(){
		$id = input('id',0,'intval');
		if( request()->isPost() ){
			if( $id ){
				$request = input('request.');
				unset($request['s']);
				
				
				$result = db('auth_group')->strict(false)->update($request);
				if( $result ){
					$this->success(config('MSG.UPDATE_SUCCESS'));
				}else{
					$this->error(config('MSG.UPDATE_ERROR'));
				}
			}else{
				$request = input('request.');
				$result = db('auth_group')->strict(false)->insertGetId($request);
				if( $result ){
					$this->success(config('MSG.ADD_SUCCESS'));
				}else{
					$this->error(config('MSG.ADD_ERROR'));
				}
			}
			
		}
		if( $id ){
			$data = db('auth_group')->find($id);
			$this->assign('data',$data);
		}
		return $this->fetch();
	}

	public function delete(){
		if( request()->isPost() ){
			$id = input('id',0,'intval');
					
			$result = db('auth_group')->delete($id);
			if( $result ){
				$this->success(config('MSG.DELETE_SUCCESS'));
			}else{
				$this->success(config('MSG.DELETE_ERROR'));
			}
		}
	}

	public function menu(){
		$id = input('request.id',0,'intval');	//分组ID
		$this->assign('id', $id);

		if( request()->isAjax() ){
			$post = input('post.');
			$str = $post['str'];
			$data = array(
					'id' => $id,
					'menu' => implode(',', $str)
				);
			$result = db('auth_group')->update($data);
			if( $result ){
				$this->success(config('MSG.UPDATE_SUCCESS'));
			}else{
				$this->error(config('MSG.UPDATE_ERROR'));
			}
		}
		$all_menu = model('Menu')->getAllMenu(1,1);		//获取所有菜单
		$group = db('auth_group')->find($id);		// 分组权限

		//dump($group);
		
		$group = explode(',', $group['menu']);
		$menu = array();
		foreach ($all_menu as $key => $value) {
			$state = array();
			if( in_array($value['id'], $group) ){
				$state = array(
						'selected' => true,
					);
			}
			$menu[] = array(
					'id' => $value['id'],
					'parent' => $value['parent_id'] ? $value['parent_id'] : '#',
					'text' => $value['title'],
					'state' => $state,
					'icon' => $value['icon'] ? 'fa fa-'.$value['icon'] : false,
				);
		}
		$json = json_encode($menu);
		$this->assign('json', $json);

		return $this->fetch();
	}

	/**
	 * [users 分组用户]
	 * @return [type] [description]
	 */
	public function users(){
		$id = input('id',0,'intval');
		$this->assign('id', $id);
		$users = model('Department')->getUserList(1,1);		//获取所有用户成员

		$access = db('auth_group_access')->field('uid')->where('group_id='.$id)->select();
		$group = array();
		foreach ($access as $key => $value) {
			$group[] = $value['uid'];
		}
		if( request()->isAjax() ){
			$data = array();
			$region = config("CORP.REGION");
			foreach ($users as $key => $value) {
				if( in_array($value['uid'], $group) ){
					$users[$key]['filiale'] = $region[$value['filiale_id']];
					$data[] = $users[$key];
				}
			}

			return $data;
		}
		$department = model('Department')->getList(1,1);	//获取所有部门列表


		$structure = array();
		$structure[] = array( 'id' => 1, 'parent' => '#', 'text' => '广州有限公司', 'icon' => 'fa fa-calendar-minus-o', 'state' => array( 'opened' => true ) );
		foreach ($department as $key => $value) {
			$structure[] = array(
					'id' => $value['id'],
					'parent' => $value['parent_id'],
					'text' => $value['title'],
					'icon' => 'fa fa-user-plus',
				);
		}
		//$users = json_decode($users);
	
		foreach ($users as $key => $value) {
			$structure[] = array(
					'id' => 1000+$value['uid'],
					'parent' => $value['group_id'],
					'text' => $value['username'],
					'state' => in_array($value['uid'], $group) ? array( 'selected' => true ) : array(),
					'icon' => 'fa fa-user',
				);
		}
		$json = json_encode($structure);
		$this->assign('json', $json);

		return $this->fetch();
	}


	/**
	 * [update_user 修改分组成员]
	 * @return [type] [description]
	 */
	public function update_user(){
		if( request()->isPost() ){
			$group_id = input('request.id',0,'intval');
			$post = input('post.');

			dump($post);
			$nodes = $post['nodes'];
			if(!empty($nodes)){
				$uid = array();
				foreach ($nodes as $key => $value) {
					if( $value > 1000 ){
						$uid[] = $value-1000;
					}
				}
			}

			// 启动事务
			Db::startTrans();
			if(!empty($uid)){
				try{
					$where['group_id'] = $group_id;
				    Db::table('yq_auth_group_access')->where($where)->delete();

				    $data = array();

			    	foreach ($uid as $key => $value) {
						$data[] = array('uid'=>$value,'group_id'=>$group_id);
					}
			    	Db::table('yq_auth_group_access')->insertAll($data);
					
				    // 提交事务
				    Db::commit();    
				} catch (\Exception $e) {
				    // 回滚事务
				    Db::rollback();
				}
			}
			else{
				// 启动事务
				try{
					$where['group_id'] = $group_id;
				    Db::table('auth_group_access')->where($where)->delete();					
				    // 提交事务
				    Db::commit();    
				} catch (\Exception $e) {
				    // 回滚事务
				    Db::rollback();
				}

			}
			
			/*
			
			$model = db('auth_group_access');
			$model->startTrans();	//开启事务
			$where['group_id'] = $group_id;
			$result = $model->where($where)->delete();
			$data = array();
			foreach ($uid as $key => $value) {
				$data[] = array('uid'=>$value,'group_id'=>$group_id);
			}
			$result = $model->addAll($data);
			if($result){
				$model->commit();		//事务提交
				$this->success(config('MSG.UPDATE_SUCCESS'));
			}else{
				$model->rollback();		//事务回滚
				$this->error(config('MSG.UPDATE_ERROR'));
			}*/
		}
	}

	/**
	 * [rule 分组权限]
	 * @return [type] [description]
	 */
	public function rule(){
		$id = input('request.id',0,'intval');
		if( request()->isPost() ){
			$group_id = $id;
			$nodes = input('nodes');
			$data['id'] = $group_id;
			$rules = array();
			foreach ($nodes as $key => $value) {
				if( $value>1000 ){
					$rules[] = $value-1000;
				}
			}
			$data['rules'] = implode(',', $rules);
			$result = db('auth_group')->save($data);
			if( $result ){
				$this->success(config('MSG.UPDATE_SUCCESS'));
			}else{
				$this->error(config('MSG.UPDATE_ERROR'));
			}
		}
		$this->id = $id;
		$all_menu = model('Menu')->getAllMenu(1,1);		//获取所有菜单
		$all_rule = db('auth_rule')->where('status=1')->select();		//获取所有权限
		$rules = db('auth_group')->where('id='.$id)->field('rules')->find();
		$rules = explode(',', $rules['rules']);
		$rule = array();
		foreach ($all_menu as $key => $value) {
			$rule[] = array(
					'id' => $value['id'],
					'parent' => $value['parent_id'] ? $value['parent_id'] : '#',
					'text' => $value['title'],
					'icon' => $value['icon'] ? 'fa fa-'.$value['icon'] : false,
				);
		}
		foreach ($all_rule as $key => $value) {
			$state = array();
			if( in_array($value['id'], $rules) ){
				$state = array( 'selected' => true );
			}
			$rule[] = array(
					'id' => 1000+$value['id'],
					'parent' => $value['menu_id'],
					'text' => $value['title'],
					'icon' => false,
					'state' => $state,
				);
		}
		$json = json_encode($rule);
		$this->assign('json', $json);
		return $this->fetch();
	}
}