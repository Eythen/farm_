<?php
namespace app\home\controller;
use think\Controller;

class Morelist extends Base {

	/**
	 * [index 左栏目测试]
	 * @return [type] [description]
	 */
	public function index(){
		
		return $this->fetch();
	}
}