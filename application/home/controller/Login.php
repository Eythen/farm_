<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Db;
//use topthink\Captcha;
//use topthink\captcha\Captcha;
use think\captcha\Captcha;


class Login extends Controller
{
	//初始化
	public function _initialize() {
		session_start();
	}

	/**
	 * [index 登录首页]
	 */
	public function index() 
	{
		/*session_start();
		dump($_SESSION);*/
		if (Request::instance()->isPost()) {
		    $r=array("status"=>0,"msg"=>"登录失败");

			//判断验证码
			$captcha = input('post.captcha', '', 'trim'); 
			if(!captcha_check($captcha, 'user_login')){
				$r['status'] = 0;
			    $r['msg'] = '验证码错误';
			    //$r['msg']=config('MSG.CAPTCHA_ERROR');
			    return $r;
	        }

			$data = array();
			$data['phone'] = input('post.mobile','','trim');
			if(!$data['phone']){
			    $r['status'] = 0;
			    $r['msg'] = '手机号码错误';
			    //$r['msg'] = config('MSG.MOBILE_ERROR');
			    return $r;
			}
			$name = $data['phone'];  //用户名
			$pw = input('post.password','','trim'); //密码
			$map['phone'] = $name;
			$map['status'] = 1;
			$res = db('admin')->where($map)->find();
			$pw = md5(md5($pw).config('MD5_KEY'));

            if($pw!=$res['password']){
                   $r['status'] = 0;
                   $r['msg'] = '密码错误';
                   //$r['msg'] = config('MSG.LOGIN_ERROR');
                   //$this->error($r['msg']);
            }else{
                session('uid', $res['uid']);
                session('admin_id', $res['uid']);
                session('uname', $res['username']);
                if($res['group_id']== 2){
                	session('administrator', 2);                         //设置超级管理员
                }

              
                $r['uid'] = $res['uid'];
                $r['status'] = 1;
				
				$userInfo = db('admin')->find(session('uid'));
		        session('uname', $userInfo['username']);                		//保存用户名
		        session('phone', $userInfo['phone']);                   		//保存手机号
		        session('avatar', format_avatar($userInfo['avatar']));		//设置用户信息
				model('users')->login_log();                                	//设置登陆日志
				unset($map);
				$map['uid'] = $res['uid'];
				
				$group_info = db('auth_group_access')->where($map)->find();
                // $group_info['group_id'] = 1;									//菜单
                session('gid', $group_info['group_id']);
                $r['msg'] = '登陆成功';
                //return $r;
                //$this->success('登陆成功');
            }
		    return $r;
		}
		/*echo "123";
		die;*/
		//return $this->fetch();
		return $this->fetch();
	}

	public function ssoin($code){
		$uid = authcode($code,'DECODE','09BA6D7FC20774402EF41FFFEB2F50B4');
		session('uid',$uid);
		exit;
	}

	public function ssoout(){
		session('uid',NULL);
		exit;
	}

	/**
	 * [verify_code 验证码]
	 */
	public function verify_code(){

		//验证码类型
        $type = input('type') ? input('type') : 'user_login';
        $fontSize = input('fontSize') ? input('fontSize') : '20';
        $length = input('length') ? input('length') : '3';
        
        $captcha_config = [
            // 验证码字符集合
            'codeSet'  => '023456789', 
            // 验证码字体大小(px)
            'fontSize' => $fontSize, 
            // 是否画混淆曲线
            'useCurve' => false, 
             // 验证码图片高度
            'imageH'   => 40,
            // 验证码图片宽度
            'imageW'   => 120, 
            // 验证码位数
            'length'   => $length, 
            // 验证成功后是否重置        
            'reset'    => true,
        	// 验证码字体，不设置随机获取
            'fontttf'  => '1.ttf',
            ];

        $captcha  = new Captcha($captcha_config);
        return $captcha->entry($type);
	}

	/**
	 * [loginout 退出登录]
	 */
	public function loginout() {
		session(null);
		$this->redirect('Login/index');
	}

	/**
	 * [sso ehr登录]
	 */
	public function sso(){
		$token = input('request.token');
		if (!empty($token)) {
			$uid = authcode($token);
			if ($uid) {
                session('uid', $uid);
                $group_info=db('auth_group_access')->where(array('uid'=>$uid))->find();
                // $group_info['group_id'] = 1;															//测试
                session('gid', $group_info['group_id']);
				db('Users')->setInfo();																	//设置用户信息
				db('users')->login_log();                                           						//设置登陆日志
				$this->redirect('Index/index');
			}
		}
	}
}