<?php
/**
 * Date: 2015-12-11
 */
namespace app\home\controller;
use \think\Db;

class Coupon extends Base {
    /**----------------------------------------------*/
     /*                优惠券控制器                  */
    /**----------------------------------------------*/
    /*
     * 优惠券类型列表
     */
    public function index(){
        //获取优惠券列表

        $lists = db('coupon')->order('add_time desc')->paginate(10);
        $page = $lists->render();

        $this->assign('lists',$lists);
        $this->assign('page',$page);// 赋值分页输出   
        $this->assign('coupons',config('COUPON_TYPE'));
        return $this->fetch();
    }

    /*
     * 添加编辑一个优惠券类型
     */
    public function coupon_info(){
        if(request()->isPost()){
        	$data = input('post.');

            $data['send_start_time'] = strtotime($data['send_start_time']);
            $data['send_end_time'] = strtotime($data['send_end_time']);
            $data['use_end_time'] = strtotime($data['use_end_time']);
            $data['use_start_time'] = strtotime($data['use_start_time']);
            if($data['send_start_time'] > $data['send_end_time']){
                $this->error('发放日期填写有误');
            }
            if(empty($data['id'])){
            	$data['add_time'] = time();
            	$row = db('coupon')->insert($data);
            }else{
            	$row =  db('coupon')->where(array('id'=>$data['id']))->update($data);
            }
            if(!$row)
                $this->error('编辑代金券失败');
            $this->success('编辑代金券成功',url('home/Coupon/index'));
            exit;
        }
        $cid = input('id');
        //dump($cid);
        if($cid){
        	$coupon = db('coupon')->where(array('id'=>$cid))->find();
        	$this->assign('coupon',$coupon);
        }else{
             $def = [           
                'id' => '',
                'name' => '',
                'type' => '',
                'money' => '',
                'condition' => '',
                'createnum' => '',
                'send_num' => '',
                'use_num' => ''
                ];
        	$def['send_start_time'] = strtotime("+1 day");
        	$def['send_end_time'] = strtotime("+1 month");
        	$def['use_start_time'] = strtotime("+1 day");
        	$def['use_end_time'] = strtotime("+2 month");
        	$this->assign('coupon',$def);
        }     
        return $this->fetch();
    }

    /*
    * 优惠券发放
    */
    public function make_coupon(){
        //获取优惠券ID
        $cid = input('id');
        $type = input('type');
        //查询是否存在优惠券
        $data = db('coupon')->where(array('id'=>$cid))->find();
        $remain = $data['createnum'] - $data['send_num'];//剩余派发量
    	if($remain<=0) $this->error($data['name'].'已经发放完了');
        if(!$data) $this->error("优惠券类型不存在");
        if($type != 4) $this->error("该优惠券类型不支持发放");
        if(request()->isPost()){
            $num  = input('post.num');
            if($num>$remain) $this->error($data['name'].'发放量不够了');
            if(!$num > 0) $this->error("发放数量不能小于0");
            $add['cid'] = $cid;
            $add['type'] = $type;
            $add['send_time'] = time();
            for($i=0;$i<$num; $i++){
                do{
                    $code = get_rand_str(8,0,1);//获取随机8位字符串
                    $check_exist = db('coupon_list')->where(array('code'=>$code))->find();
                }while($check_exist);
                $add['code'] = $code;
                db('coupon_list')->insert($add);
            }
            db('coupon')->where("id=$cid")->setInc('send_num',$num);
            adminLog("发放".$num.'张'.$data['name']);
            $this->success("发放成功",url('home/Coupon/index'));
            exit;
        }
        $this->assign('coupon',$data);
        return $this->fetch();
    }
    
    public function ajax_get_user(){
    	//搜索条件
    	$condition = array();
    	input('mobile') ? $condition['mobile'] = input('mobile') : false;
    	input('email') ? $condition['email'] = input('email') : false;
    	$nickname = input('nickname');
    	if(!empty($nickname)){
    		$condition['nickname'] = array('like',"%$nickname%");
    	}
    	$model = db('users');
        $userList = $model->where($condition)->order("user_id desc")->paginate(10);
        $page = $userList->render();

        $user_level = db('user_level')->column('level_id,level_name');       
        $this->assign('user_level',$user_level);
    	$this->assign('userList',$userList);
    	$this->assign('page',$page);
    	return $this->fetch();
    }
    
    public function send_coupon(){
        $cid = input('cid');        
    	$type = input('type');    	
    	if(request()->isPost()){
            $request = input('post.');
            $user_id = $request['user_id'];
            $level_id = input('level_id');

            $insert = '';
            $coupon = db('coupon')->where("id=$cid")->find();
            if($coupon['createnum']>0){
                $remain = $coupon['createnum'] - $coupon['send_num'];//剩余派发量
                if($remain<=0) $this->error($coupon['name'].'已经发放完了');
            }
    		if(empty($user_id) && $level_id>=0){
    			if($level_id==0){
    				$user = db('users')->where("is_lock=0")->select();
    			}else{
    				$user = db('users')->where("is_lock=0 and level_id=$level_id")->select();
    			}
    			if($user){
    				$able = count($user);//本次发送量
    				if($coupon['createnum']>0 && $remain<$able){
    					$this->error($coupon['name'].'派发量只剩'.$remain.'张');
    				}
    				foreach ($user as $k=>$val){
    					$user_id = $val['user_id'];
    					$time = time();
    					$gap = ($k+1) == $able ? '' : ',';
    					$insert .= "($cid, $type,$user_id,$time)$gap";
    				}
    			}
    		}else{
    			$able = count($user_id);//本次发送量
    			if($coupon['createnum']>0 && $remain<$able){
    				$this->error($coupon['name'].'派发量只剩'.$remain.'张');
    			}
    			foreach ($user_id as $k=>$v){
    				$time = time();
    				$gap = ($k+1) == $able ? '' : ',';
    				$insert .= "($cid, $type,$v,$time)$gap";
    			}
    		}
			$sql = "insert into ".config('database.prefix')."coupon_list (`cid`,`type`,`uid`,`send_time`) VALUES $insert";
			db()->execute($sql);
			db('coupon')->where("id=$cid")->setInc('send_num',$able);
			adminLog("发放".$able.'张'.$coupon['name']);
			$this->success("发放成功");
			exit;
    	}
    	$level = db('user_level')->select();
    	$this->assign('level',$level);
        $this->assign('cid',$cid);
    	$this->assign('type',$type);
    	return $this->fetch();
    }
    
    public function send_cancel(){
    	
    }

    /*
     * 删除优惠券类型
     */
    public function del_coupon(){
        //获取优惠券ID
        $cid = input('get.id');
        //查询是否存在优惠券
        $row = db('coupon')->where(array('id'=>$cid))->delete();
        if($row){
            //删除此类型下的优惠券
            db('coupon_list')->where(array('cid'=>$cid))->delete();
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }


    /*
     * 优惠券详细查看
     */
    public function coupon_list(){
        //获取优惠券ID
        $cid = input('id');
        //查询是否存在优惠券
        $check_coupon = db('coupon')->field('id,type')->where(array('id'=>$cid))->find();
        if(!$check_coupon['id'] > 0)
            $this->error('不存在该类型优惠券');
       
        //查询该优惠券的列表
        $lists = Db::table(config('database.prefix')."coupon_list")
                    ->alias('l')
                    ->join(config('database.prefix')."coupon c", 'c.id = l.cid', 'LEFT')
                    ->join(config('database.prefix')."order o", 'o.order_id = l.order_id', 'LEFT')
                    ->join(config('database.prefix')."users u", 'u.user_id = l.uid', 'LEFT')
                    ->where("l.cid = ".$cid)
                    ->paginate(10);
        $page = $lists->render();

        $this->assign('coupon_type',config('COUPON_TYPE'));
        $this->assign('type',$check_coupon['type']);       
        //$this->assign('lists',$coupon_list);                
        $this->assign('lists',$lists);            	
    	$this->assign('page',$page);        
        return $this->fetch();
    }
    
    /*
     * 删除一张优惠券
     */
    public function coupon_list_del(){
        //获取优惠券ID
        $cid = input('id');
        if(!$cid)
            $this->error("缺少参数值");
        //查询是否存在优惠券
         $row = db('coupon_list')->where(array('id'=>$cid))->delete();
        if(!$row)
            $this->error('删除失败');
        $this->success('删除成功');
    }
}