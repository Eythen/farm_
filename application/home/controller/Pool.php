<?php
namespace app\home\controller;
use think\Controller;

class Pool extends Base {

	/**
	 * [index 充值列表]
	 * @return [type] [description]
	 */
	public function index(){
		if( request()->isAjax() ){
			$offset = input('offset')?input('offset'):0;
			$limit = input('limit')?input('limit'):10;
			$order = input('order')?input('order'):'desc';
			$sort = input('sort')?input('sort'):'id';
			$search = input('search');
			//$where['user_id_pusher'] = 0;
			$where['type'] = 4;

			$timegap = input('timegap');
			if($timegap){
	            $this->assign('timegap', $timegap);
	            $time_r = explode('-', $timegap);
	            $where['add_time'] = ['between', [strtotime(trim($time_r[0])), strtotime('+ 1 day', strtotime(trim($time_r[1]))) ] ];
	        }
	        if(input('uid')){
	        	$where['update_admin_id'] = input('uid');
	        }
	        if(input('status')){
	        	$where['status'] = input('status');
	        }
	        if(input('user_id_pusher')){
	        	$where['user_id_pusher'] = input('user_id_pusher');
	        }
	        if(input('user_id')){
	        	$where['user_id'] = input('user_id');
	        }


			$data['total'] = db('points_log')->where($where)->whereor($whereor)->count();
			//halt(db()->getLastSql());
			if($data['total']){
				$data['rows'] = db('points_log')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
				foreach ($data['rows'] as $k => $v) {
					$data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
					$data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
					$data['rows'][$k]['status_text'] = config('points_status')[$v['status']];
					$data['rows'][$k]['nickname'] = find_username($v['user_id']);

					//审核人
					$admin = '';
					if(!empty($v['user_id_pusher'])){
						$admin = find_username($v['user_id_pusher']);
					}
					if(!empty($v['update_admin_id'])){
						$admin = get_admin_name($v['update_admin_id']).'<font color="red">(后台管理员)</font>';
					}
					$data['rows'][$k]['admin'] = $admin;

				}
			}
			
			return $data;
		}

		$points_status = config('points_status');
		$users = get_all_users('nickname','user_id');
		$admin = get_all_admin('username,uid','uid');
		$points_public = db('config')->where('name', 'points_public')->value('value');

		$this->assign([
			'admin'=> $admin, 
			'users' => $users, 
			'points_status' => $points_status,
			'points_public' => $points_public,
			]);

		return $this->fetch();
	}

	/**
	 * [log 个人充值列表]
	 * @return [type] [description]
	 */
	public function log(){
		if( request()->isAjax() ){
			$offset = input('offset')?input('offset'):0;
			$limit = input('limit')?input('limit'):10;
			$order = input('order')?input('order'):'desc';
			$sort = input('sort')?input('sort'):'id';

			$timegap = input('timegap');
			if($timegap){
	            $this->assign('timegap', $timegap);
	            $time_r = explode('-', $timegap);
	            $where['add_time'] = ['between', [strtotime(trim($time_r[0])), strtotime('+ 1 day', strtotime(trim($time_r[1]))) ] ];
	        }
	        if(input('uid')){
	        	$where['admin_id'] = input('uid');
	        }

			$data['total'] = db('pool_log')->where($where)->whereor($whereor)->count();
			if($data['total']){
				$data['rows'] = db('pool_log')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
				foreach ($data['rows'] as $k => $v) {
					$data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
					$data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
					$data['rows'][$k]['admin'] = get_admin_name($v['admin_id']);

				}
			}
			
			return $data;
		}
		$admin = get_all_admin('username,uid','uid');

		$this->assign([
			'admin'=> $admin
			]);
		return $this->fetch();
	}

	/**
	 * [forerror 前台错误通过纠正为不处理]
	 * @return [type] [description]
	 */
	public function forerror(){



		if(request()->isAjax()){
			$id = input('id');
			$data = db('points_log')->where('id', $id)->find();
			$data['add_time'] = formatTime($data['add_time'],'Y-m-d H:i:s');
			$data['username'] = find_username($data['user_id']);

			$post = input('post.');
			$order_id = $data['recharge_id']; 
            $pay_status = 2;   //不处理

            //事务数据处理
            db()->startTrans();
            try {
                $map = [ 
                    'order_id' => $order_id
                ];
                if($pay_status == 2){      //不处理
                    $time = time();
                    $recharge_data = [
                        'pay_status'=>2,
                        'update_admin_id' => session('uid'),
                        'update_time'=>$time
                        ];
                    $log_data = [
                        'status'=>2,
                        'des'=> date('Y-m-d H:i:s').'已纠错'.$data['des'],
                        'update_admin_id' => session('uid'),
                        'update_time'=>$time,

                        ];
                	if($data['status']==1){  //已经前台操作扣了钱，补回去
                		//上级处理start
                		db('users')->where('user_id', $data['user_id_pusher'])->setInc('pay_money', $data['points_recharge']);

                		$rebate_money = db('users')->where('user_id', $data['user_id_pusher'])->value('rebate_money');
                		if($rebate_money< $data['points_user_pusher']){
	                        $this->error('用户返利币不足，扣除操作失败！');
	                    }
	                    db('users')->where('user_id', $data['user_id_pusher'])->setDec('rebate_money', $data['points_user_pusher']);
                		//上级处理end


                		//下级处理start
	                    $pay_money = db('users')->where('user_id', $data['user_id'])->value('pay_money');
                		if($pay_money< $data['points_recharge']){
	                        $this->error('用户报单币不足，扣除操作失败！');
	                    }
                    	db('users')->where('user_id', $data['user_id'])->setDec('pay_money', $data['points_recharge']);   //给用户扣值
                		//下级处理end
                	}
                	
            		db('recharge')->where($map)->update($recharge_data);
                    db('points_log')->where('recharge_id', $order_id)->update($log_data);
                	
	                    
                }

                //写入消息
                $time = time();
                $article_data = [
                    'content' => '申请金额'.$data['points_recharge'].'元，申请时间：'.date('Y-m-d H:i:s', $data['add_time']),
                    'title' => '您有一条充值申请被后台管理员修改为拒绝',
                    'admin_id' => 0,
                    'cat_id' => 2,
                    'add_time' => $time,
                    'publish_time' => $time,
                    ];
                $article_id = db('article')->insertGetId($article_data);    
                $message_data = [
                    'message_id' => $article_id,
                    'user_id' => $data['user_id'],
                    'status' => 1,
                    ];
                db('user_message')->insert($message_data); 
                //写入消息end
                
                db()->commit();
                $this->success('提交成功！');
            }
            catch (Exception $e){
                db()->rollback();
                $this->error('失败，请重试！');
            }

            //事务数据处理end 
		}

		return view();
	}

	/**
	 * [edit 后台审核处理]
	 * @return [type] [description]
	 */
	public function edit(){
		$id = input('id');
		$data = db('points_log')->where('id', $id)->find();
		$data['add_time'] = formatTime($data['add_time'],'Y-m-d H:i:s');
		$data['username'] = find_username($data['user_id']);

		$this->assign('data', $data);

		if(request()->isAjax()){
			$post = input('post.');
			$order_id = $post['recharge_id']; 
            $pay_status = $post['status'];

            //事务数据处理
            db()->startTrans();
            try {
                $map = [ 
                    'order_id' => $order_id
                ];
                $data = db('points_log')->where('recharge_id', $order_id )->find();
                if($pay_status == 2){      //不处理
                    $time = time();
                    $recharge_data = [
                        'pay_status'=>2,
                        'update_admin_id' => session('uid'),
                        'update_time'=>$time
                        ];
                    $log_data = [
                        'status'=>2,
                        'des'=> $post['des'],
                        'update_admin_id' => session('uid'),
                        'update_time'=>$time
                        ];

                	if($data['status']==1){  //已经后台操作扣了钱，补回去
                		db('config')->where('name', 'points_public')->setInc('value', $data['points_recharge']);
                		$pay_money = db('users')->where('user_id', $data['user_id'])->value('pay_money');
                		if($pay_money< $data['points_recharge']){
	                        $this->error('用户报单币不足，操作失败！');
	                    }
                    	db('users')->where('user_id', $data['user_id'])->setDec('pay_money', $data['points_recharge']);   //给用户扣值
                	}
                	
            		db('recharge')->where($map)->update($recharge_data);
                    db('points_log')->where('recharge_id', $order_id)->update($log_data);
					
					//写入消息
                    $time = time();
                    $article_data = [
                        'content' => '申请金额'.$data['points_recharge'].'元，申请时间：'.date('Y-m-d H:i:s', $data['add_time']),
                        'title' => '您有一条充值申请被后台管理员拒绝',
                        'admin_id' => 0,
                        'cat_id' => 2,
                        'add_time' => $time,
                        'publish_time' => $time,
                        ];
                    $article_id = db('article')->insertGetId($article_data);    
                    $message_data = [
                        'message_id' => $article_id,
                        'user_id' => $data['user_id'],
                        'status' => 0,
                        ];
                    db('user_message')->insert($message_data); 
                    //写入消息end                	
	                    
                }
                if($pay_status == 1){       //通过 ，同时处理金额

                    //资金处理 从报单池上扣，加到用户身上
                    $recharge = db('recharge')->where('order_id', $order_id)->find();
                    $pay_money = db('config')->where('name', 'points_public')->value('value');
                    if($pay_money< $recharge['account']){
                        $this->error('报单币池不足，请先充值！');
                    }

                    db('config')->where('name', 'points_public')->setDec('value', $recharge['account']);
                    db('users')->where('user_id', $recharge['user_id'])->setInc('pay_money', $recharge['account']);   //给用户充值
                    //资金处理end

                    //更新状态
                    $map = [ 
                        'order_id' => $order_id
                    ];
                    $time = time();
                    $recharge_data = [
                        'pay_status'=>1,
                        'update_time'=>$time,
                        'update_admin_id' => session('uid'),
                        ];

                    db('recharge')->where($map)->update($recharge_data);
                    $log_data = [
                        'status'=>1,
                        'des'=> $post['des'],
                        'update_time'=>$time,
                        'update_admin_id' => session('uid'),
                        ];
                    db('points_log')->where('recharge_id', $order_id)->update($log_data);

                    //写后台操作日志
                    $do_data = [
						'add_time'=> time(),
						'ip'=> request()->ip(),
						'type'=> 1,
						'remark'=> '',
						'money'=> $recharge['account'],
						'admin_id'=> session('uid'),
						'points_log_id' => $id,
						];
					db('pool_log')->insert($do_data);	
                    //写后台操作日志end
                    
                    //写入消息
                    $time = time();
                    $article_data = [
                        'content' => '申请金额'.$recharge['account'].'元，申请时间：'.date('Y-m-d H:i:s', $recharge['pay_time']),
                        'title' => '您有一条充值申请已经后台管理员通过',
                        'admin_id' => 0,
                        'cat_id' => 2,
                        'add_time' => $time,
                        'publish_time' => $time,
                        ];
                    $article_id = db('article')->insertGetId($article_data);    
                    $message_data = [
                        'message_id' => $article_id,
                        'user_id' => $recharge['user_id'],
                        'status' => 0,
                        ];
                    db('user_message')->insert($message_data); 
                    //写入消息end
                   
                }
                
                db()->commit();
                $this->success('提交成功，后台人员会在工作时间内抓紧处理！');
            }
            catch (Exception $e){
                db()->rollback();
                $this->error('添加失败，请重新添加！');
            }

            //事务数据处理end 
		}
		

		$points_status = config('points_status');
		$this->assign('points_status', $points_status);

		return view();
	}

	/**
	 * [detail 详情]
	 * @return [type] [description]
	 */
	public function detail(){
		$id = input('id');
		$data = db('points_log')->where('id', $id)->find();
		$data['add_time'] = formatTime($data['add_time'],'Y-m-d H:i:s');
		$data['update_time'] = formatTime($data['update_time'],'Y-m-d H:i:s');
		$data['username'] = find_username($data['user_id']);
		if($data['user_id_pusher']>0){
			$data['user_check_name'] = find_username($data['user_id_pusher']);
		}
		else{
			$data['user_check_name'] = '';
		}
		if($data['update_admin_id']>0){
			$data['admin_name'] = get_admin_name($data['update_admin_id']);
		}
		else{
			$data['admin_name'] = '';
		}
		$data['status'] = config('points_status')[$data['status']];

		$this->assign('data', $data);



		return view();
	}


	/**
	 * [add 增加报单币池报单币]
	 * @return [type] [description]
	 */
	public function add(){
		if(request()->isAjax()){
			$post = input('post.');

			$data = [
				'add_time'=> time(),
				'ip'=> request()->ip(),
				'type'=> 2,
				'remark'=> $post['remark'],
				'money'=> $post['money'],
				'admin_id'=> session('uid'),
				];

            //事务数据处理
            db()->startTrans();
            try {
                db('config')->where('name', 'points_public')->setInc('value', $data['money']);
                db('pool_log')->insert($data);
                db()->commit();
                $this->success('提交成功！');
            }
            catch (Exception $e){
                db()->rollback();
                $this->error('添加失败，请重新添加！');
            }
            //事务数据处理end 
		}
		
		return view();
	}


}