<?php
/**
 * 商城模块
 */
namespace app\agent\controller;
use \think\Controller;

class Mall extends Base {
	
	/*
     * 初始化操作
     */
    public function _initialize() {
		if(session('user_id') && session('user')['manager_mobile']){
            //$this->redirect('Users/index');
        }
        else{
            if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
                $this->redirect('Loginweixin/index');
            }
            else{
                $this->redirect('Login/index');
            }
        }
	}
	
    //代理商首页
    public function index(){
    	$goods = db('agent_goods')->order('goods_id desc')->select();
    	$this->assign('goods', $goods);
        return view();
	}






}
?>