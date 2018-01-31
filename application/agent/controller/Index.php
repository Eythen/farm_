<?php
/**
 * 首页
 */
namespace app\agent\controller;

class Index extends Base{

    public function index(){
        if(session('user_id') && session('user')['manager_mobile']){
            $this->redirect('Users/index');
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

}
?>