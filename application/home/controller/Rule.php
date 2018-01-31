<?php
namespace app\home\controller;
use think\Controller;

class Rule extends Base {

	public function index(){
		$all_menu = model('Menu')->getAllMenu(1,1);		//获取所有菜单
		foreach ($all_menu as $key => $value) {
			$rule[] = array(
					'id' => $value['id'],
					'parent' => $value['parent_id'] ? $value['parent_id'] : '#',
					'text' => $value['title'],
					'icon' => $value['icon'] ? 'fa fa-'.$value['icon'] : false,
				);
		}
		$json = json_encode($rule);
		$this->assign('json', $json);
		//$this->json = $json;

		$operation = config('CORP.OPERATION');
		$this->assign('operation', $operation);
		//$this->operation = $operation;
		return $this->fetch();
	}

	public function rule_list(){
		if( request()->isPost() ){
			$menu_id = input('id',0,'intval');
			$rules = db('auth_rule')->where('menu_id='.$menu_id)->select();
			return $rules;
		}
	}

	public function add(){
		$id = input('request.id',0,'intval');
		if( request()->isPost() ){
			$request = input('request.');
			
			
			if( $id ){
				$request['id'] = $id;
				$result = db('auth_rule')->update($request);
				if( $result ){
					$this->success(config('MSG.UPDATE_SUCCESS'));
				}else{
					$this->error(config('MSG.UPDATE_ERROR'));
				}
			}else{
				unset($request['id']);
				$result = db('auth_rule')->add($request);
				if( $result ){
					$this->success(config('MSG.ADD_SUCCESS'));
				}else{
					$this->error(config('MSG.ADD_ERROR'));
				}
			}
		}
		if($id){
			$data = db('auth_rule')->find($id);
			$this->data = $data;
			$this->menu_id = $data['menu_id'];
		}else{
			$menu_id = input('menu_id',0,'intval');
			$this->menu_id = $menu_id;
		}
		return $this->fetch();
	}

	public function delete(){
		if( request()->isPost() ){
			$result = db('auth_rule')->delete(input('id',0,'intval'));
			if( $result ){
				$this->success(config('MSG.DELETE_SUCCESS'));
			}else{
				$this->error(config('MSG.DELETE_ERROR'));
			}
		}
	}
}