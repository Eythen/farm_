<?php
/**
 * 微信登陆绑定手机号码模块
 */
namespace app\agent\controller;
use \think\Controller;

class Loginweixin extends Base {
	
	/*
     * 初始化操作
     */
    public function _initialize() {
		
			
        dump(session('openid'));
        dump($_SESSION);
		if(!strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') || !session('openid')){
			die('请在微信登陆');
		}
		if(session('user')['manager_mobile'] && session('user')['manager_id']){
			$this->redirect('Users/index');
		}
	}
	
    //绑定用户手机号码页面
    public function index(){
    	$manager_mobile = '';
    	if(session('manager_mobile')){
    		$manager_mobile = session('manager_mobile');
    	}
		$this->assign('user_mobile', $manager_mobile);
		$this->assign('user_mobile', session('user')['mobile']);
        return view();
	}

    //绑定用户手机号码
	public function add(){
        if (request()->isPost()){
			$this->user = session('user');


            //短信验证
			 if(input('code')<> cookie('sendcode') || empty(cookie('sendcode'))){
                $this->error("手机验证码不正确！");
            }  
            $password = md5(md5(trim(input('password'))).config('md5_key'));
            $data = array(
                'mobile'=>input('mobile'),
                'password'=> $password,
                'reg_time'=>time(),
            );
   

            $user = db('users')->where('mobile', input('mobile'))->find();
            if($user['manager_id']){
            	$this->error("这个手机号已经被绑定了业务员，请换另一个手机号！");
            }


			if(!input('manager_mobile')){
				$this->error("业务员手机不能为空！");
			}
			//查找业务员信息
			$manager = db('admin')->where('phone', input('manager_mobile'))->find();
			if(!$manager){
				$this->error("业务员不存在！");
			}

            if($user){
				//1 [old有m  this只有微信] old更新微信 this 删除用户 
				if(empty($this->user['mobile'])){

				$weixin = [
						'openid' => $this->user['openid'],
						'head_pic' => $this->user['head_pic'],
						'unionid' => $this->user['unionid'],
						'nickname' => $this->user['nickname'],
						'oauth' => 'weixin',
						'manager_mobile' => $manager['phone'],
						'manager_id' => $manager['uid'],
						'manager_name' => $manager['username'],
						'is_agent' => 1,
						];
				  
					$res = db('users')->where('mobile', input('mobile'))->update($weixin);
					$map['user_id'] = $this->user['user_id'];
					$r = db('users')->where($map)->delete();
				}
				//2[old有m  this有微信与m]， old更新微信, this 删除微信
				if( !empty($this->user['mobile']) && ($this->user['mobile'] <> input('mobile'))){

					$weixin = [
						'openid' => $this->user['openid'],
						'head_pic' => $this->user['head_pic'],
						'unionid' => $this->user['unionid'],
						'nickname' => $this->user['nickname'],
						'oauth' => 'weixin',
						'manager_mobile' => $manager['phone'],
						'manager_id' => $manager['uid'],
						'manager_name' => $manager['username'],
						'is_agent' => 1,
						];
					$r = db('users')->where('mobile', input('mobile'))->update($weixin);
					$map['user_id'] = $this->user['user_id'];
					$deleteweixin = [
						'openid' => '',
						'head_pic' => '',
						];
					$res = db('users')->where($map)->update($deleteweixin);
				}
				//3[old有m  this有微信与m]， 同一个用户绑定业务员
				if( !empty($this->user['mobile']) && ($this->user['mobile'] == input('mobile'))){

					$weixin = [
						'openid' => $this->user['openid'],
						'head_pic' => $this->user['head_pic'],
						'unionid' => $this->user['unionid'],
						'nickname' => $this->user['nickname'],
						'oauth' => 'weixin',
						'manager_mobile' => $manager['phone'],
						'manager_id' => $manager['uid'],
						'manager_name' => $manager['username'],
						'is_agent' => 1,
						];
					$res = db('users')->where('mobile', input('mobile'))->update($weixin);
				}
				logWrite('有用户'.$this->user['mobile']);
			}
            //3 [old没有m  this只有微信]  this 添加手机与密码
            else{

                $map3['openid'] = session('openid');
	
				$data['manager_mobile'] = $manager['phone'];
				$data['manager_id'] = $manager['uid'];
				$data['manager_name'] = $manager['username'];
				$data['is_agent'] = 1;
                $res = db('users')->where($map3)->update($data);
				
            }
            if ($res !== false){
                $user = db('users')->where('mobile',$data['mobile'])->find();
                session('user',$user);
				
                session('user_id',$user['user_id']);
				session('openid', $this->user['openid']);

                $this->success("绑定成功",url('Users/index'));
            }else{
                $this->error("绑定失败！");
            }
            
        }
    }
	
	//验证码
    public function sendcode($mobile=''){
        $post = input('post.');
        if(empty($post['mobile'])){
            $post['mobile'] = $mobile;
        }


        controller('wap/Alidayu')->sendcode($mobile);
    }

    public function protocol(){
        return view();
    }




}
?>