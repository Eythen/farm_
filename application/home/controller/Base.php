<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Loader;

class Base extends Controller {

	//public $user;

	public function _initialize() 
	{
		session_start();
		$uid = session('uid');
		/********** 处理uploadfidy session丢失问题 start  *************/
		/*if(!empty(input('post.session_id'))){
		  $uid = input('post.session_id');
		}*/
	    /********** 处理uploadfidy session丢失问题 end  ******************/
	    if (empty($uid)) 
	    {
			$this->redirect('Login/index');
		}

		
		if (empty($uid)) {
			$this->redirect('login/index');
		} else {
			$request = Request::instance();
			$name = $request->module() . '/' . $request->controller() . '/' . $request->action(); //规则名称
			$where = array(
				'url' => $name,
				'status' => 1,
			);
			//echo $name;
			$result = db('menu')->where($where)->count();

			if ($result) {
				Loader::import('Auth');
				$auth = new \Auth();
				//$auth = new \think\Auth();
				$flag = $auth->check($name, $uid);
				if (!$flag) {
					echo '您无权进行此项操作！';
					exit;
				}
			}
		}
		//$this->user = db('Users')->info();
	}
}