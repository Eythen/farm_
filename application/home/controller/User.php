<?php
/**
 * tpshop
 * ============================================================================

 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 当燃      
 * Date: 2015-09-09
 */

namespace app\home\controller;

/*use Think\AjaxPage;
use Think\Page;*/
use app\admin\logic\Users;

class User extends Base {

    //define('__PREFIX__', config('database.prefix'));
    public function selectuser(){
        if(request()->isAjax()){
            // 搜索条件
            $map = [];
            $request = input('request.');


            $sort = $request['sort']? $request['sort']:'user_id';
            $order = $request['order']? $request['order']:'desc';
            $limit = $request['limit']? $request['limit']:'10';
            $offset = $request['offset']? $request['offset']:'0';
            
            $timegap = input('timegap');
            if($timegap){
                $time_r = explode('-', $timegap);
                $map['add_time'] = ['between', [strtotime(trim($time_r[0])), strtotime('+ 1 day', strtotime(trim($time_r[1]))) ] ];
            }

            if(input('nickname')){
                $map['nickname'] = input('nickname');
            }
            if(input('mobile')){
                $map['mobile'] = input('mobile');
            }
            if(input('level_id')){
                $map['level'] = input('level_id');
            }
            
            $is_lock = input('is_lock');
            if($is_lock === '0'){
                $map['is_lock'] = 0;
            }
            if($is_lock === '1'){
                $map['is_lock'] = 1;
            }


                   
            $data['total'] = db('users')->where($map)->count();
            if($data['total']>0){
                $data['rows'] = db('users')->where($map)->order($sort, $order)->limit($offset, $limit)->select();

                //halt($data);

                foreach ($data['rows'] as $k => $v) {
                    if(!session('administrator')){
                        $data['rows'][$k]['mobile'] = nophone($v['mobile']);
                    }
                    if($v['is_lock']){
                        $data['rows'][$k]['is_lock'] = '<font color="red">锁定</font>';
                    }
                    else{
                        $data['rows'][$k]['is_lock'] = '正常';
                    }
                    $data['rows'][$k]['reg_time'] = formatTime($v['reg_time'], 'Y-m-d H:i:s');
                    $data['rows'][$k]['level'] = getUserLevel($v['level']);
                }

            } 
            else{
                $data['rows'] = [];
            }      
            return $data;    

        }

        $level = db('user_level')->column('level_name', 'level_id');

        $this->assign([
            'level' => $level,
           
            ]);


        return $this->fetch();
    }


    public function index(){
        return $this->fetch();
    }

    /**
     * 会员列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();
        input('mobile') ? $condition['mobile'] = input('mobile') : false;
        input('email') ? $condition['email'] = input('email') : false;
        $sort_order = input('order_by','user_id').' '.input('sort','desc');
               
        $model = db('users');
                
        $userList = $model->where($condition)->order($sort_order)->paginate(10);
        $page = $userList->render();

        $userList = $userList->all();
        foreach ($userList as $k => $v) {
            if(!session('administrator')){
                $userList[$k]['mobile'] = nophone($v['mobile']);
            }
            $userList[$k]['level'] = getUserLevel($v['level']);
        }

                        
        $user_id_arr = get_arr_column($userList, 'user_id');
        if(!empty($user_id_arr))
        {
            $tp_prefix = config('database.prefix');
            $first_leader = db('users')->query("select first_leader,count(1) as count  from ".$tp_prefix."users where first_leader in(".  implode(',', $user_id_arr).")  group by first_leader");
            $first_leader = convert_arr_key($first_leader,'first_leader');
            
            $second_leader = db('users')->query("select second_leader,count(1) as count  from ".$tp_prefix."users where second_leader in(".  implode(',', $user_id_arr).")  group by second_leader");
            $second_leader = convert_arr_key($second_leader,'second_leader');            
            
            $third_leader = db('users')->query("select third_leader,count(1) as count  from ".$tp_prefix."users where third_leader in(".  implode(',', $user_id_arr).")  group by third_leader");
            $third_leader = convert_arr_key($third_leader,'third_leader');            
        }
        $this->assign('first_leader',$first_leader);
        $this->assign('second_leader',$second_leader);
        $this->assign('third_leader',$third_leader);
                                
        $this->assign('userList',$userList);
        $this->assign('page', $page);// 赋值分页输出
        return $this->fetch();
    }

    /**
     * 会员详细信息查看
     */
    public function detail(){
        $uid = input('id/d');
        $user = db('users')->where(array('user_id'=>$uid))->find();
        if(!$user)
            exit($this->error('会员不存在'));
        if(request()->isPost()){
            if(input('act' == 'is_lock')){
                $data = [
                    'is_lock' => input('post.is_lock'),
                ];
                $row = db('users')->where(array('user_id'=>$uid))->strict(false)->update($data);
                if($row)
                    exit($this->success('修改成功'));
                exit($this->error('修改失败'));
            }
            //  会员信息编辑
            $password = input('post.password');
            $password2 = input('post.password2');
            if($password != '' && $password != $password2){
                exit($this->error('两次输入密码不同'));
            }

            $data = [
                    'nickname' => input('post.nickname'),
                    'email' => input('post.email'),
                    'password' => encrypt($_POST['password']),
                    'sex' => input('post.sex'),
                    'mobile' => input('post.mobile'),
                    'is_lock' => input('post.is_lock'),
                    'is_distribut' => input('post.is_distribut'),
                    ];

            //密码不修改
            if($password == '' && $password2 == ''){
                unset($data['password']);
            }


            $row = db('users')->where(array('user_id'=>$uid))->strict(false)->update($data);
            if($row)
                exit($this->success('修改成功'));
            exit($this->error('未作内容修改或修改失败'));
        }
        
//        $user['first_lower'] = db('users')->where("first_leader = {$user['user_id']}")->count();
//        $user['second_lower'] = db('users')->where("second_leader = {$user['user_id']}")->count();
//        $user['third_lower'] = db('users')->where("third_leader = {$user['user_id']}")->count();
        if(!session('administrator')){
            $user['mobile'] = nophone($user['mobile']);   //不是超级管理员加密手机号码
        }
        $this->assign('user',$user);
        return $this->fetch();
    }
    
    public function add_user(){
    	if(request()->isPost()){
    		$data = input('post.');
			$user_obj = model('Users', 'logic');
			$res = $user_obj->addUser($data);
			if($res['status'] == 1){
				$this->success('添加成功',url('User/index'));exit;
			}else{
				$this->error('添加失败,'.$res['msg'],url('User/index'));
			}
    	}
        $this->assign('user',null);
    	return $this->fetch();
    }

    /**
     * 用户收货地址查看
     */
    public function address(){
        $uid = input('id/d');
        $lists = db('user_address')->where(array('user_id'=>$uid))->select();
       /* $regionList = db('Region')->column('id,name');
        $this->assign('regionList',$regionList);*/
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 删除会员
     */
    public function delete(){
        if(!session('administrator')){
            $this->error('没有administrator权限！不能删除用户！');
        }
        $uid = input('get.id');
        $row = db('users')->where(array('user_id'=>$uid))->delete();
        if($row){
            $this->success('成功删除会员');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 账户资金记录
     */
    public function account_log(){
        $user_id = input('id');
        //获取类型
        $type = input('type');
        //获取记录总数
        
        $lists  = db('account_log')->where(array('user_id'=>$user_id))->order('change_time desc')->paginate(10);

        $this->assign('user_id',$user_id);
        $this->assign('page',$lists->render());
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 账户资金消费记录
     */
    public function pay_log(){
        $user_id = input('id');
        $type = input('type');
        $where['user_id'] = $user_id;
        $where['status'] = 1;
        $where['type'] = ['in', [1,2,3, 5]];

        
        $time = input('timegap');
        if(request()->isPost()){
            $page = 0;
            $time = input('post.timegap');
        }
        
        if($time){
            $time_r = explode(' - ', $time);
            $start_time = strtotime($time_r[0]);
            $end_time = strtotime($time_r[1]." 23:59:59");
            $where['add_time'] = ['between', [$start_time, $end_time] ];
            $this->assign('timegap',$time);
            $timegap = "/timegap/".$time;
        }
        else{
            $timegap = '';
        }

        $lists  = db('points_log')->where($where)->order('add_time desc')->paginate(10, '', [
                                                                                    'page'     => $page,
                                                                                    'path'     => url('pay_log', '', '')."/id/".$user_id.$timegap
                                                                                ]);

        $page = $lists->render();


        $this->assign('user_id',$user_id);
        $this->assign('page', $page);
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 账户资金调节
     */
    public function account_edit(){
        $user_id = input('id');
        if(!$user_id > 0)
            $this->error("参数有误");
        if(IS_POST){
            //获取操作类型
            $m_op_type = input('post.money_act_type');
            $user_money = input('post.user_money');
            $user_money =  $m_op_type ? $user_money : 0-$user_money;

            $p_op_type = input('post.point_act_type');
            $pay_points = input('post.pay_points');
            $pay_points =  $p_op_type ? $pay_points : 0-$pay_points;

            $f_op_type = input('post.frozen_act_type');
            $frozen_money = input('post.frozen_money');
            $frozen_money =  $f_op_type ? $frozen_money : 0-$frozen_money;

            $desc = input('post.desc');
            if(!$desc)
                $this->error("请填写操作说明");
            if(accountLog($user_id,$user_money,$pay_points,$desc)){
                $this->success("操作成功",U("Admin/User/account_log",array('id'=>$user_id)));
            }else{
                $this->error("操作失败");
            }
            exit;
        }
        $this->assign('user_id',$user_id);
        return $this->fetch();
    }
    
    public function recharge(){
    	$timegap = input('timegap')?input('timegap'):'';
    	$nickname = input('nickname');
    	$map = array();
    	if($timegap){
    		$gap = explode(' - ', $timegap);
    		$begin = $gap[0];
    		$end = $gap[1];
    		$map['ctime'] = array('between',array(strtotime($begin),strtotime($end)));
    	}
    	if($nickname){
    		$map['nickname'] = array('like',"%$nickname%");
    	}  	
    	/*$count = db('recharge')->where($map)->count();
    	$page = new Page($count);
    	$lists  = db('recharge')->where($map)->order('ctime desc')->limit($page->firstRow.','.$page->listRows)->select();
    	$this->assign('page',$page->show());*/
        $lists  = db('recharge')->where($map)->order('ctime desc')->paginate(12);
        $page = $lists->render();
        $this->assign('lists',$lists);
        $this->assign('page',$page);
    	$this->assign('timegap',$timegap);
    	return $this->fetch();
    }
    
    public function level(){
    	$act = input('act','add');
    	$this->assign('act',$act);
    	$level_id = input('level_id');
    	$level_info = array();
    	if($level_id){
    		$level_info = db('user_level')->where('level_id='.$level_id)->find();
    		$this->assign('info',$level_info);
    	}
    	return $this->fetch();
    }
    
    public function levelList(){
    	$Ad =  db('user_level');
        //$res = $Ad->where('1=1')->order('level_id')->page($_GET['p'].',10')->select();
    	$res = $Ad->where('1=1')->order('level_id')->paginate(10);
    	if($res){
    		foreach ($res as $val){
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);
        $show = $res->render();
    	/*$count = $Ad->where('1=1')->count();
    	$Page = new \Think\Page($count,10);
    	$show = $Page->show();*/
    	$this->assign('page',$show);
    	return $this->fetch();
    }
    
    public function levelHandle(){
    	$data = input('post.');
    	if($data['act'] == 'add'){
    		$r = db('user_level')->insert($data);
    	}
    	if($data['act'] == 'edit'){
    		$r = db('user_level')->where('level_id='.$data['level_id'])->strict(false)->update($data);
    	}
    	 
    	if($data['act'] == 'del'){
    		$r = db('user_level')->where('level_id='.$data['level_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	 
    	if($r){
    		$this->success("操作成功",url('home/User/levelList'));
    	}else{
    		$this->error("操作失败",url('home/User/levelList'));
    	}
    }

    /**
     * 搜索用户名
     */
    public function search_user()
    {
        $search_key = trim(input('search_key'));        
        if(strstr($search_key,'@'))    
        {
            $list = db('users')->where(" email like '%$search_key%' ")->select();        
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['email']}</option>";
            }                        
        }
        else
        {
            $list = db('users')->where(" mobile like '%$search_key%' ")->select();        
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['mobile']}</option>";
            }            
        } 
        exit;
    }
    
    /**
     * 分销树状关系
     */
    public function ajax_distribut_tree()
    {
          $list = db('users')->where("first_leader = 1")->select();
          return $this->fetch();
    }

    /**
     *
     * @time 2016/08/31
     * @author dyr
     * 发送站内信
     */
    public function sendMessage()
    {
        $user_id_array = input('user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $users = db('users')->field('user_id,nickname')->where(array('user_id' => array('IN', $user_id_array)))->select();
        }
        $this->assign('users',$users);
        return $this->fetch();
    }

    /**
     * 发送系统消息
     * @author dyr
     * @time  2016/09/01
     */
    public function doSendMessage()
    {
        $call_back = input('call_back');//回调方法
        $message = input('post.text');//内容
        $type = input('post.type', 0);//个体or全体
        $admin_id = session('admin_id');
        $users = input('post.user');//个体id
        $message = array(
            'admin_id' => $admin_id,
            'message' => $message,
            'category' => 0,
            'send_time' => array('exp', 'NOW()')
        );
        if ($type == 1) {
            //全体用户系统消息
            $message['type'] = 1;
            db('Message')->insert($message);
        } else {
            //个体消息
            $message['type'] = 0;
            if (!empty($users)) {
               // $create_message_id = db('Message')->data($message)->add();
                $create_message_id = db('Message')->insert($message);
                foreach ($users as $key) {
                    db('user_message')->insert(array('user_id' => $key, 'message_id' => $create_message_id, 'status' => 0, 'category' => 0));
                }
            }
        }
        echo "<script>parent.{$call_back}(1);</script>";
        exit();
    }

    /**
     *
     * @time 2016/09/03
     * @author dyr
     * 发送邮件
     */
    public function sendMail()
    {
        $user_id_array = input('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $user_where = array(
                'user_id' => array('IN', $user_id_array),
                'email' => array('neq', '')
            );
            $users = db('users')->field('user_id,nickname,email')->where($user_where)->select();
        }
        $this->assign('smtp', tpCache('smtp'));
        $this->assign('users', $users);
        return $this->fetch();
    }

    /**
     * 发送邮箱
     * @author dyr
     * @time  2016/09/03
     */
    public function doSendMail()
    {
        $call_back = input('call_back');//回调方法
        $message = input('post.text');//内容
        $title = input('post.title');//标题
        $users = input('post.user');
        if (!empty($users)) {
            $user_id_array = implode(',', $users);
            $users = db('users')->field('email')->where(array('user_id' => array('IN', $user_id_array)))->select();
            $to = array();
            foreach ($users as $user) {
                if (check_email($user['email'])) {
                    $to[] = $user['email'];
                }
            }
            $res = send_email($to, $title, $message);
            echo "<script>parent.{$call_back}({$res});</script>";
            exit();
        }
    }
}