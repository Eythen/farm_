<?php
/**
 * 代理商页面
 */
namespace app\agent\controller;
use \think\Controller;

class Users extends Base {
	
	/*
     * 初始化操作
     */
    public function _initialize() {
    	if(session('user_id') && session('user')['manager_mobile']){
    		$this->user = session('user');
    	}
    	else{
    		$this->redirect('Login/index');
    	}
    	//halt(session('user'));
		
	}
	
    //代理商首页
    public function index(){
    	//用户代理消费
    	$map['user_id'] = session('user_id');
    	$map['pay_status'] = 1;
        $total_amount = db('agent_order')->where($map)->sum('order_amount');

        $user = db('users')->field('agent_amount, level')->where("user_id", session('user_id'))->find();
        //升级start
        $level_info = db('user_level')->order('level_id')->select();

        if($level_info){
            foreach($level_info as $k=>$v){
                if($total_amount >= $v['amount']){
                    $up_level = $level_info[$k]['level_id'];
                }
            }
            if($up_level>$user['level']){
                $updata = array('level'=>$up_level,'agent_amount'=>$total_amount);
                db('users')->where("user_id", session('user_id'))->update($updata);        
            }
        }
        //升级end
    	$level_name = getUserLevel($this->user['level']);
    	$level = db('user_level')->order('level_id desc')->select();
    	$user = db('users')->where('user_id', session('user_id'))->find();
    	session('user', $user);
    	$this->user = session('user');
    	$this->assign([
    		'user' => $this->user,
    		'level_name' => $level_name,
    		'level' => $level,
    		]);
        return view();
	}


	//设置
    public function set(){
    	$level_name = getUserLevel($this->user['level']);
    	$level = db('user_level')->order('level_id desc')->select();

    	$where['cat_id'] = 4;
    	$total_count = db('article')->where($where)->count();
    	$where2['category'] = 4;
    	$where2['user_id'] = session('user_id');
    	$read_count = db('user_message')->where($where2)->count();
    	$has_read = $total_count - $read_count;

    	$map = [
    		'status' => 0,
    		'user_id' => session('user_id'),
    		];
    	$has_apply = db('agent_apply')->where($map)->find();
    	$this->assign([
    		'user' => $this->user,
    		'level_name' => $level_name,
    		'level' => $level,
    		'has_apply' => $has_apply,
    		'has_read' => $has_read,
    		]);
        return view();
	}


	//修改手机号
    public function modify(){
    	if(request()->isAjax()){
    		//短信验证
            /*if(input('code')<> cookie('sendcode') || empty(cookie('sendcode'))){
                $this->error("手机验证码不正确！");
            }*/
            $data = array(
                'mobile'=>input('mobile')
            );

            $user = db('users')->where('mobile',$data['mobile'])->find();
            if($user){
            	$this->error("手机号码已经存在，修改失败！");
            }
            else{
            	$res = db('users')->where('user_id', session('user')['user_id'])->update($data);
            }

            
            if ($res){
                $user = db('users')->where('user_id', session('user')['user_id'])->find();
                session('user',$user);
                session('user_id',$user['user_id']);
                $this->success("修改成功");
            }else{
                $this->error("修改失败！");
            }
            return;
        }

        $this->assign([
    		'mobile' => $this->user['mobile'],
    		]);
        return view();
	}

	//修改业务员手机号
    public function modifytwo(){
    	if(request()->isAjax()){
    		$map['phone'] = input('mobile');
    		$new = db('admin')->where($map)->find();

    		if(!$new){
    			$this->error("新业务员不存在");
    		}
			//写入申请更新业务员表
			$data = [
				'old_manager_name' => $this->user['manager_name'],
				'old_manager_id' => $this->user['manager_id'],
				'old_manager_mobile' => $this->user['manager_mobile'],
				'new_manager_name' => $new['username'],
				'new_manager_id' => $new['uid'],
				'new_manager_mobile' => $new['phone'],
				'user_id' =>session('user_id'),
				'user_name' =>$this->user['nickname'],
				'mobile' =>$this->user['mobile'],
				'add_time' => time(),
				'ip' => request()->ip(),
				];
			$res = db('agent_apply')->insert($data);

            
            if ($res){
            	$user = db('users')->where('user_id', session('user_id'))->find();
            	session('user', $user);
                $this->success("提交成功");
            }else{
                $this->error("提交失败！");
            }
            return;
        }
        $map = [
    		'user_id' => session('user_id'),
    		];
    	$has_apply = db('agent_apply')->where($map)->order('apply_id desc')->find();

    	$this->assign([
    		'has_apply' => $has_apply,
    		'user' => $this->user,
    		]);
        return view();
	}

    //验证码
    public function sendcode($mobile=''){
        $post = input('post.');
        if(empty($post['mobile'])){
            $post['mobile'] = $mobile;
        }

        controller('wap/Alidayu')->sendcode($mobile);
    }

    //退出登录
    public function logout(){
        session(null);
        $this->success('退出成功！',url('Login/index',array("isurl"=>1)));
    }



}
?>