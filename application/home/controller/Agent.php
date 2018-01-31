<?php
/**
 * 代理模块
 * 2018.01.10
 */
namespace app\home\controller;

use think\Controller;
use think\Db;
use think\Loader;

class Agent extends Base {
	//业绩
	public function achievement(){
		if(session('gid') == 2){  //管理员组
			$map['manager_id'] = session('uid');
			$map['is_agent'] = 1;
			$users = db('users')->where($map)->column('user_id');
		}
		if($users){
			$map1['user_id'] = ['in', $users];
		}
		else{
			$map1 = [];
		}

		$goods_num = db('agent_goods')->count();
		//更新业务员申请
		$apply_num = db('agent_apply')->where($map1)->count();
		//发货申请
		$order_apply_num = db('agent_order_apply')->where($map1)->count();

		//订单数据
		$order_num = db('agent_order')->where($map1)->count();
		$map1['pay_status'] = 1;
		$order_amount = db('agent_order')->where($map1)->sum('order_amount');
		//订单等待处理
		$map1['order_status'] = 0;
		$map1['pay_status'] = 0;
		$order_wait_num = db('agent_order')->where($map1)->count();

		if(session('gid') <> 2){  //管理员组
			$where['manager_id'] = session('uid');
		}
		$where['is_agent'] = 1;
		$agent_num = db('users')->where($where)->count();
		$this->assign([
			'agent_num' => $agent_num,
			'order_num' => $order_num,
			'apply_num' => $apply_num,
			'order_apply_num' => $order_apply_num,
			'order_wait_num' => $order_wait_num,
			'goods_num' => $goods_num,
			'order_amount' => $order_amount,
			]);
		return view();
	}

	//我的二维码
	public function mycode(){
       //用户信息
		$admin = db('admin')->find(session('uid'));
		//生成二维码
		$map['uid'] = session('uid');
		$mobile = db('admin')->where($map)->value('phone');
        $host = request()->domain();
        $url = $host.'/index.php/agent/index/index.html'."?manager_mobile=".$mobile;

        $src = ROOT_PATH . "public/public/admincode/qrcode".$uid.".png";
        
        Loader::import('phpqrcode.phpqrcode');
        error_reporting(E_ERROR);
        $img = new \QRcode();

        $file_name = "public/admincode/qrcode".$uid.".png";
        $img->png($url, $file_name, '', '8', '4', 'true');

        $src = "/public/admincode/qrcode".$uid.".png";
        //生成二维码end
        $admin['qrcode'] = $src;

		$this->assign([
			'admin' => $admin,
			]);
		return view();
	}

	public function index(){
		$order = $sort ? 'parent_id ASC,sort ASC,id ASC' : '';
		$all = db('menu')->order($order)->column('id,parent_id,title,url,icon,sort', 'id');
		foreach ($all as $k => $v) {
			$all[$v['parent_id']]['son'] = [];
		}
		//二级
		foreach ($all as $k => $v) {
			if($v['parent_id']>0){
				array_push($all[$v['parent_id']]['son'] , $all[$k]);
			}
		}
		//三级
		foreach ($all['265']['son'] as $kk => $vv) {
			if(empty($vv['url'])){
				$all['265']['son'][$kk]['son'] = $all[$vv['id']]['son'];
			}
		}
		$menu = $all['265'];
		
		$this->assign('menu', $menu);
		return $this->fetch();
	}

	//代理商城产品
	public function goodslist(){
		if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('is_on_sale')) ;             
            if($str){
                $where['is_on_sale'] = input('is_on_sale') ;
            }
              
            // 关键词搜索               
            $goods_name = input('key_word') ? trim(input('key_word')) : '';
            if($goods_name)
            {
                $where['goods_name'] = ['like', "%$goods_name%" ] ;
            }
            $shop_price = input('shop_price') ? trim(input('shop_price')) : '';
            if($shop_price)
            {
                $where['shop_price'] = $shop_price;
            }

            $model = db('agent_goods');
           
            $order = input('orderby1')?input('orderby1'):'goods_id';
            $sort = input('orderby2')?input('orderby2'):'desc';
            $page = input('page')?input('page'):1;
            $limit = input('limit')?input('limit'):10;
            $offset = input('offset')?input('offset'):0;
            $order_str = $order." ".$sort;
            $data['total'] = $model->where($where)->order($order_str)->count();
            //halt(db()->getLastSql());
            if($data['total']){
               $data['rows'] = $model->where($where)->order($order_str)->limit($offset, $limit)->select();
               foreach ($data['rows'] as $k => $v) {
                   $data['rows'][$k]['last_update'] = date('Y-m-d H:i:s', $v['last_update']);
                   $data['rows'][$k]['last_update_uid'] = get_admin_name($v['last_update_uid']);

               }
            }
            else{
                $data['rows'] = [];
            }
            //$page = $goodsList->render(); //分布
            return $data;

        } 
		return view();
	}

	/**
     * 添加修改商品
     */
    public function addEditGoods(){
        if(input('goods_id')){
            $goods = db('agent_goods')->where('goods_id', input('goods_id'))->find(); 
            $this->assign('goodsInfo', $goods);
        }
        //ajax提交
        if (request()->isPost()) {
            $request = input('request.');
            if($request['goods_id']){
                $data = [
                    'goods_name' => $request['goods_name'],
                    'goods_remark' => $request['goods_remark'],
                    'shop_price' => $request['shop_price'],
                    'original_img' => $request['original_img'],
                    'goods_id' => $request['goods_id'],
                    'last_update_uid' => session('uid'),
                    'last_update' => time()
                ];
                $result = db('agent_goods')->where('goods_id', $request['goods_id'])->update($data);
            }
            else{
                $time = time();
                $data = [
                    'goods_name' => $request['goods_name'],
                    'goods_remark' => $request['goods_remark'],
                    'shop_price' => $request['shop_price'],
                    'original_img' => $request['original_img'],
                    'goods_id' => $request['goods_id'],
                    'uid' => session('uid'),
                    'add_time' => $time,
                    'last_update_uid' => session('uid'),
                    'last_update' => $time
                ];
                $result = db('agent_goods')->insert($data);
            }
            if($result){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');

            }
            
        }
        return $this->fetch('_goods');
    }

    /**
     * 删除单品
     */
    public function delGoods()
    {
		$goods_id = input('param.id');
        $ids = explode(',', $goods_id);
        $ids = array_filter($ids);

        foreach ($ids as $k => $goods_id) {
    
            $error = '';
            
            // 判断此商品是否有订单
            $c1 = db('agent_order_goods')->where("goods_id = $goods_id")->count('1');
            $c1 && $error .= $goods_id.'此商品有订单,不得删除! <br/>';
          
            
            if($error)
            {
                $return_arr = array('status' => -1,'msg' =>$error,'data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
                return $return_arr;           
            }
            // 删除此商品        
            db("agent_goods")->where('goods_id ='.$goods_id)->delete();  //商品表
            //db("book_cart")->where('goods_id ='.$goods_id)->delete();  // 购物车
                     
                     
        }

        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
        return $return_arr;
    }

	//代理商 业务员指派
	public function agentmanager(){
		if(request()->isAjax()){
			$request = input('request.');
			$limit = $request['limit']? $request['limit']:10;
			$offset = $request['offset']? $request['offset']:0;
			$order = 'user_id desc';
			//注册时间
			if(input('reg_time')){
				$time_r = explode('-', input('reg_time/s'));
				$start_time = strtotime(trim($time_r[0]));
				$end_time = strtotime('+ 1 day', strtotime(trim($time_r[1])));
				$where['reg_time'] = ['between', [$start_time, $end_time]];
			}
			//代理商名字
			if(input('key_word')){
				$where['nickname'] = ['like', '%'.input('key_word/s').'%'];
			}
			//
			if(input('mobile')){
				$where['mobile'] = input('mobile');
			}
			//锁定状态
			if(strlen(input('is_lock'))){
				$where['is_lock'] = input('is_lock');
			}
			//代理商的业务员
			if(strlen(input('manager_name'))){
				$where['manager_name'] = input('manager_name');
			}
			if(strlen(input('level_id'))){
				$where['level'] = input('level_id/d');
			}
			$where['is_agent'] = 0;
			//$where['manager_id'] = 0;
			$data['total'] = db('users')->where($where)->count();
			if($data['total']){
				$data['rows'] = db('users')->where($where)->order($order)->limit($offset, $limit)->select();
				foreach ($data['rows'] as $k => $v) {
					$data['rows'][$k]['reg_time'] = date('Y-m-d H:i:s', $v['reg_time']);
					$data['rows'][$k]['level'] = getUserLevel($v['level']);
					$data['rows'][$k]['manager_name'] = get_admin_name($v['manager_id']);
				}
			}
			else{
				$data['rows'] = [];
			}
			return $data;
		}
		$level = db('user_level')->column('level_id, level_name');
		if(input('mobile')){
			$url = url('agentmanager',['mobile' => input('mobile')]);
		}
		else{
			$url = url('agentmanager');
		}
		$this->assign([
			'level' => $level,
			'manager' => get_manager(),
			'url' => $url,
			]);
		return view();
	}

	//代理商列表
	public function userslist(){
		if(request()->isAjax()){
			$request = input('request.');
			$limit = $request['limit']? $request['limit']:10;
			$offset = $request['offset']? $request['offset']:0;
			$order = 'user_id desc';
			//注册时间
			if(input('reg_time')){
				$time_r = explode('-', input('reg_time/s'));
				$start_time = strtotime(trim($time_r[0]));
				$end_time = strtotime('+ 1 day', strtotime(trim($time_r[1])));
				$where['reg_time'] = ['between', [$start_time, $end_time]];
			}
			//代理商名字
			if(input('key_word')){
				$where['nickname'] = ['like', '%'.input('key_word/s').'%'];
			}
			//
			if(input('mobile')){
				$where['mobile'] = input('mobile');
			}
			//锁定状态
			if(strlen(input('is_lock'))){
				$where['is_lock'] = input('is_lock');
			}
			//代理商的业务员
			if(strlen(input('manager_name'))){
				$where['manager_name'] = input('manager_name');
			}
			if(strlen(input('level_id'))){
				$where['level'] = input('level_id/d');
			}
			//$where['is_agent'] = 0;
			//$where['manager_id'] = 0;
			$data['total'] = db('users')->where($where)->count();
			if($data['total']){
				$data['rows'] = db('users')->where($where)->order($order)->limit($offset, $limit)->select();
				foreach ($data['rows'] as $k => $v) {
					$data['rows'][$k]['reg_time'] = date('Y-m-d H:i:s', $v['reg_time']);
					$data['rows'][$k]['level'] = getUserLevel($v['level']);
					//$data['rows'][$k]['manager_name'] = get_admin_name($v['manager_id']);
				}
			}
			else{
				$data['rows'] = [];
			}
			return $data;
		}
		$level = db('user_level')->column('level_id, level_name');
		$this->assign('level', $level);
		return view();
	}

	//代理商更换业务员日志列表
	public function agentmanagerlog(){
		if(request()->isAjax()){
			$request = input('request.');
			$limit = $request['limit']? $request['limit']:10;
			$offset = $request['offset']? $request['offset']:0;
			$order = 'add_time desc';
		
			$user_id = input('user_id/d');
			if(!$user_id){
				$this->error('请选择代理商');
			}
			$where['user_id'] = $user_id;

			$data['total'] = db('agent_change_manager')->where($where)->count();
			if($data['total']){
				$data['rows'] = db('agent_change_manager')->where($where)->order($order)->limit($offset, $limit)->select();
				foreach ($data['rows'] as $k => $v) {
					$data['rows'][$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
					$data['rows'][$k]['admin'] = get_admin_name($v['uid']);
				}
			}
			else{
				$data['rows'] = [];
			}
			return $data;
		}
		$user_id = input('user_id/d');
		if(!$user_id){
			$this->error('请选择代理商');
		}
		$this->assign('user_id', $user_id);
		return view();
	}

	//代理商更换业务员申请
	public function apply(){
		if(request()->isAjax()){
			$request = input('request.');
			$limit = $request['limit']? $request['limit']:10;
			$offset = $request['offset']? $request['offset']:0;
			$order = 'apply_id desc';
			//注册时间
			if(input('add_time')){
				$time_r = explode('-', input('add_time/s'));
				$start_time = strtotime(trim($time_r[0]));
				$end_time = strtotime('+ 1 day', strtotime(trim($time_r[1])));
				$where['add_time'] = ['between', [$start_time, $end_time]];
			}
			//代理商名字
			if(input('key_word')){
				$where['user_name'] = ['like', '%'.input('key_word/s').'%'];
			}
			//
			if(input('mobile')){
				$where['mobile'] = input('mobile');
			}

			//代理商的业务员
			if(strlen(input('manager_name'))){
				$where['old_manager_name'] = input('manager_name');
			}
			if(strlen(input('status'))){
				$where['status'] = input('status/d');
			}
			
			$data['total'] = db('agent_apply')->where($where)->count();
			if($data['total']){
				$data['rows'] = db('agent_apply')->where($where)->order($order)->limit($offset, $limit)->select();
				foreach ($data['rows'] as $k => $v) {
					$data['rows'][$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
					$data['rows'][$k]['status_txt'] = config('agent_apply_status')[$v['status']];
					$data['rows'][$k]['last_update_admin'] = get_admin_name($v['last_update_uid']);
					$data['rows'][$k]['last_update'] = date('Y-m-d H:i:s', $v['last_update']);
				}
			}
			else{
				$data['rows'] = [];
			}
			return $data;
		}
		$agent_apply_status = config('agent_apply_status');
		$this->assign('agent_apply_status', $agent_apply_status);
		return view();
	}

	public function applyHandle(){
		if(request()->isAjax()){
			$map['apply_id'] = input('apply_id');
			$data['last_update'] = time();
			$data['last_update_uid'] = session('uid');
			if(input('act') == 'refuse'){
				$data['status'] = 2;
				$r = db('agent_apply')->where($map)->update($data);
			}
			elseif(input('act') == 'change'){
				//更新业务员
				$apply = db('agent_apply')->where($map)->find();
				$manager = db('admin')->where('phone', $apply['new_manager_mobile'])->find();
				if(!$manager){
					$this->error('新业务员不存在');
				}
				$update = [
					'manager_id' => $manager['uid'],
					'manager_name' => $manager['username'],
					'manager_mobile' => $manager['phone'],
					];
				db('users')->where('user_id', $apply['user_id'])->update($update);
				//写日志
				$log = [
					'old_manager_name' => $apply['old_manager_name'],
					'old_manager_id' => $apply['old_manager_id'],
					'old_manager_mobile' => $apply['old_manager_mobile'],
					'new_manager_name' => $manager['username'],
					'new_manager_id' => $manager['uid'],
					'new_manager_mobile' => $manager['phone'],
					'user_id' => $apply['user_id'],
					'uid' => session('uid'),
					'add_time' => time()
					];
				db('agent_change_manager')->insert($log);
				//更新业务员end
				$data['status'] = 1;
				$r = db('agent_apply')->where($map)->update($data);
			}
			else{
				$data['remark'] = input('remark');
				$r = db('agent_apply')->where($map)->update($data);
			}
			if($r){
				$this->success('提交完成');
			}
			else{
				$this->error('提交失败');
			}
		}
		$map['apply_id'] = input('apply_id/d');
		$data = db('agent_apply')->where($map)->find();
		$data['status_txt'] = config('agent_apply_status')[$data['status']];
		$this->assign([
			'data' => $data,
			'agent_apply_status' => config('agent_apply_status'),
			]);
		return view();
	}

	//更换业务员
	public function changeManager(){
		if(request()->isAjax()){
			$ids = trim(input('ids'), ',');
			$manager_id = input('uid');
			if(!$ids || !$manager_id){
				$this->error('要更换的代理商与业务员不能为空');
			}
			$manager = db('admin')->where('uid',$manager_id)->find();
			$data['manager_id'] = $manager_id;
			$data['manager_name'] = $manager['username'];
			$data['manager_mobile'] = $manager['phone'];
			$map['user_id'] = ['in', $ids];
			$old = db('users')->field('user_id, nickname, manager_id, manager_name, manager_mobile')->where($map)->select();
			db()->startTrans();
			try{
				foreach ($old as $k => $v) {
					db('users')->where('user_id', $v['user_id'])->update($data);
					//写日志
					$log[$k] = [
						'old_manager_name' => $v['manager_name'],
						'old_manager_id' => $v['manager_id'],
						'old_manager_mobile' => $v['manager_mobile'],
						'new_manager_name' => $manager['username'],
						'new_manager_id' => $manager_id,
						'new_manager_mobile' => $manager['phone'],
						'user_id' => $v['user_id'],
						'uid' => session('uid'),
						'add_time' => time()
						];
					db('agent_change_manager')->insert($log[$k]);
				}
				db()->commit();
				$this->success('提交成功');
			}
			catch (Exception $e){
				db()->rollback();
				$this->error('提交失败');
			}
		}
	}

	//订单列表
	public function orderlist(){
		if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('user_id')) ;             
            if($str){
                $where['user_id'] = input('user_id') ;
            }
              
            // 关键词搜索               
            $name = input('key_word') ? trim(input('key_word')) : '';
            if($name)
            {
                $where['name'] = ['like', "%$name%" ] ;
            }

            $pay_status = input('pay_status') ? trim(input('pay_status')) : '';
            if(is_numeric($pay_status)){
                $where['pay_status'] = $pay_status;
            }
            //订单状态
            $order_status = input('order_status') ? trim(input('order_status')) : '';
            if(is_numeric($order_status)){
                $where['order_status'] = $order_status;
            }
             //订单id
            $order_id = input('order_id') ? trim(input('order_id')) : '';
            if(is_numeric($order_id)){
                $where['order_id'] = $order_id;
            }

            //下单时间
            $add_time = input('add_time') ? trim(input('add_time')) : '';
            if($add_time)
            {
                $add_time = explode('-', $add_time);
                $start_time = strtotime(trim($add_time[0]));
                $end_time = strtotime('+1 day', strtotime(trim($add_time[1])));
                $where['add_time'] = ['between', [$start_time, $end_time] ] ;
            }
  
           

            $offset = input('offset')?input('offset'):0;
            $limit = input('limit')?input('limit'):10;
  
            //$model = new AgentModel();
            $data = model('agent/Agent')->orderlist($where, $offset, $limit, 1);
            if($data['rows']){
	            foreach ($data['rows'] as $k => $v) {
	                $data['rows'][$k]['name'] = $v['name'] . "(" . $v['user_id'] . ")";
	                $data['rows'][$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
	                $data['rows'][$k]['order_status'] =  config('agent_order_status')[$v['order_status']];
	                $data['rows'][$k]['pay_status'] =  config('agent_order_pay_status')[$v['pay_status']];
	            }
            }

            return $data;
        }  

        $order_status = config('agent_order_status');
	    $pay_status = config('agent_order_pay_status');
        $this->assign([
            'pay_status' => $pay_status,
            'order_status' => $order_status,
            ]);
		return view();
	}

	//订单详情
    public function editOrder(){
        $act = input('act');
        if($act == 'edit'){
            $template = 'orderdetail';
        }
        if($act == 'view'){
            $template = 'orderdetail';
        }
        if($act == 'success'){
            $template = 'orderdetail';
        }
        if($act == 'refund'){
            $template = 'orderdetail';
        }
        if($act == 'print'){
            $template = 'print';
        }
        $order_id = input('order_id/d');
        if(!$order_id){
            $this->error('请选择订单');
        }
        $map['order_id'] = $order_id;

        $order = model('agent/Agent')->orderDetail($map);
        if($order['pay_status'] ==1){
            $order['pay_money'] = $order['order_amount'];
            $order['pay_money_spare'] = 0;
        }
        else{
            $order['pay_money'] = 0;
            $order['pay_money_spare'] = $order['order_amount'];
        }

        //取操作日志
        $action_log = db('agent_order_action')->where('order_id', $order_id)->order('action_id desc')->select();
        foreach ($action_log as $k => $v) {
            $action_log[$k]['action_user'] = get_admin_name($v['action_user']);
        }

        $order_status = config('agent_order_status');
        $pay_status = config('agent_order_pay_status');


        $this->assign([
            'order' => $order,
            'order_status' => $order_status,
            'pay_status' => $pay_status,
            'act' => $act,
            'action_log' => $action_log,
            ]);

        return $this->fetch($template);
    }

	//提货单列表
	public function orderapply(){
		if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('mobile')) ;             
            if($str){
                $where['mobile'] = input('mobile') ;
            }
              
            // 关键词搜索               
            $name = input('key_word') ? trim(input('key_word')) : '';
            if($name)
            {
                $where['name'] = ['like', "%$name%" ] ;
            }

            $pay_status = input('pay_status') ? trim(input('pay_status')) : '';
            if(is_numeric($pay_status)){
                $where['pay_status'] = $pay_status;
            }
            //订单状态
            $order_status = input('order_status') ? trim(input('order_status')) : '';
            if(is_numeric($order_status)){
                $where['order_status'] = $order_status;
            }
             //订单id
            $order_id = input('order_id') ? trim(input('order_id')) : '';
            if(is_numeric($order_id)){
                $where['order_id'] = $order_id;
            }
            //下单时间
            $add_time = input('add_time') ? trim(input('add_time')) : '';
            if($add_time)
            {
                $add_time = explode('-', $add_time);
                $start_time = strtotime(trim($add_time[0]));
                $end_time = strtotime('+1 day', strtotime(trim($add_time[1])));
                $where['add_time'] = ['between', [$start_time, $end_time] ] ;
            }
  
           

            $offset = input('offset')?input('offset'):0;
            $limit = input('limit')?input('limit'):10;
  

            
            $data = model('agent/Agent')->orderApply($where, $offset, $limit, 1);
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $data['rows'][$k]['order_status'] =  config('agent_order_apply_status')[$v['order_status']];
            }

            return $data;
        }  

        if(input('order_id')){
        	$url = url('orderapply', ['order_id' => input('order_id')]);
        }
        else{
        	$url = url('orderapply');
        }
        $order_status = config('agent_order_apply_status');
        $this->assign([
            'pay_status' => $pay_status,
            'order_status' => $order_status,
            'url' => $url,
            ]);

		return view();
	}

	//申请发货订单详情
    public function editApplyOrder(){
        $act = input('act');
        if($act == 'edit'){
            $template = 'applydetail';
        }
        if($act == 'view'){
            $template = 'applydetail';
        }
        if($act == 'success'){
            $template = 'applydetail';
        }
        if($act == 'refund'){
            $template = 'applydetail';
        }
        if($act == 'print'){
            $template = 'printapply';
        }
        $apply_id = input('apply_id/d');
        if(!$apply_id){
            $this->error('请选择订单');
        }
        $map['apply_id'] = $apply_id;

        $order = model('agent/Agent')->orderApplyDetail($map);
        if($order['pay_status'] ==1){
            $order['pay_money'] = $order['order_amount'];
            $order['pay_money_spare'] = 0;
        }
        else{
            $order['pay_money'] = 0;
            $order['pay_money_spare'] = $order['order_amount'];
        }
        $user = db('users')->field('mobile,nickname')->where('user_id', $order['user_id'])->find();

        $order['user_name'] = $user['nickname'];
        $order['user_mobile'] = $user['mobile'];

        //取快递信息
        $logisticsMap['apply_id'] = $apply_id;
        $logistics = model('agent/Agent')->logistics($logisticsMap);


        $order_status = config('agent_order_apply_status');


        $this->assign([
            'order' => $order,
            'order_status' => $order_status,
            'logistics' => $logistics,
            'act' => $act,
            ]);

        return $this->fetch($template);
    }


	//发货
    public function orderShipping(){
        $map['apply_id'] = input('apply_id/d');
        $order = model('agent/Agent')->orderApplyDetail($map);

        if(request()->isAjax()){
            $post = input('post.');
            $act = input('act');
            if(($act == 'del') && input('logistics_id/d')){
                $r = db('agent_order_logistics')->where('logistics_id', input('logistics_id/d'))->delete();
            }
            else{
                $shipping_sn = $post['shipping_sn'];
                $shipping_sn = str_replace('，', ',', $shipping_sn); //中文逗号转成英文

                $shipping_sn = explode(',', $shipping_sn);
                $shipping_sn = array_filter($shipping_sn);//过滤空的

                foreach ($shipping_sn as $k => $v) {
                    $kuaidi_name = model('Kuaidi')->getName($post['shipping_name']);
                    $data[$k] = [
                        'code' => $post['shipping_name'],
                        'sn' => $v,
                        'name' => $kuaidi_name,
                        'uid' => session('uid'),
                        'admin' => get_admin_name(session('uid')),
                        'add_time' => time(),
                        'apply_id' => input('apply_id/d'),
                        'order_id' => $order['order_id'],
                        'user_id' => $order['user_id'],
                        ];
                }
                //发送消息
                $url = url('agent/Order/logistics',['apply_id' => input('apply_id/d')]);
                model('home/Article')->addExpress($url, $kuaidi_name, $post['shipping_sn'], $order['user_id']);
                //更新订单状态
                db('agent_order_apply')->where('apply_id', input('apply_id/d'))->setField('order_status', 5);
                $r = db('agent_order_apply_logistics')->insertAll($data);
                //写日志
                $this->orderApplyActionLog($order['apply_id'], $order['order_id'], '5', '', "发货".$kuaidi_name."：".$post['shipping_sn']);

                
            }

            if($r){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }
        }

        $kuaidi = model('Kuaidi');
        $kuaidi_cat = $kuaidi->getCat(['status'=>1]);



        $this->assign([
            'order' => $order,
            'kuaidi_cat' => $kuaidi_cat,
            ]);
        return view();
    }

    /**
     * [orderApplyActionLog 发货订单操作日志]
     * @param  [type] $apply_id     [申请发货订单ID]
     * @param  [type] $order_id     [总订单ID]
     * @param  string $order_status [订单状态]
     * @param  string $pay_status   [订单支付状态]
     * @param  string $action_note  [操作留言]
     * @return [type]               [description]
     */
    public function orderApplyActionLog($apply_id ,$order_id , $order_status='', $pay_status='', $action_note=''){
        if($order_status){
             $data['order_status'] = $order_status;
        }
        if($pay_status){
             $data['pay_status'] = $pay_status;
        }
        if($action_note){
             $data['action_note'] = $action_note;
        }
        $data['order_id'] = $order_id;
        $data['log_time'] = time();
        $data['action_user'] = session('uid');

        db('agent_order_apply_action')->insert($data);    

    }

    /**
     * [orderActionLog 操作日志]
     * @param  [type] $order_id     [订单ID]
     * @param  string $order_status [订单状态]
     * @param  string $pay_status   [订单支付状态]
     * @param  string $action_note  [操作留言]
     * @return [type]               [description]
     */
    public function orderActionLog($order_id , $order_status='', $pay_status='', $action_note=''){
        if($order_status){
             $data['order_status'] = $order_status;
        }
        if($pay_status){
             $data['pay_status'] = $pay_status;
        }
        if($action_note){
             $data['action_note'] = $action_note;
        }
        $data['order_id'] = $order_id;
        $data['log_time'] = time();
        $data['action_user'] = session('uid');

        db('pig_order_action')->insert($data);    

    }

    //操作
    public function orderAction(){
        $act = input('act');
        $order_id = input('order_id/d');
        if($act == 'edit'){
            $template = 'detail';
        }
        if($act == 'view'){
            $template = 'detail';
        }
        if($act == 'success'){
            $order_status = input('order_status');
            if(input('note/s')){
                $action_note = input('note/s');
            }
            $data = [
                'order_status' => $order_status,
                ];
            //订单无效时要设置花猪上线
            if($order_status == 3){
                $ids = db('pig_order_pigs')->where('order_id', $order_id)->column('pig_id');
                $idsmap['pig_id'] = ['in', $ids];
                db('pig')->where($idsmap)->setField('user_id', 0);
            }    
            $r = db('pig_order')->where('order_id', $order_id)->update($data);
            //dump(db()->getLastSql());
            //记录日志
            $this->orderActionLog($order_id, $order_status, '', "$action_note");
            if($r !== false){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }
        }
        if($act == 'refund'){
            $order_status = 5;//退款完成
            $action_note = input('note/s');
            $data = [
                'order_id' => $order_id,
                'order_status' => $order_status,
                ];
            $r = db('pig_order')->where('order_id', $order_id)->update($data);

            $this->orderActionLog($order_id, $order_status, '', "$action_note");
            if($r){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }

        }
        
    }

}