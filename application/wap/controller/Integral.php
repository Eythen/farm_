<?php
/**
 * 我的积分
 */
namespace app\wap\controller;

class Integral extends Base{

    public function index(){
        //余额
        $map['user_id'] = session('user_id');
        $user['user_money'] = db('users')->where($map)->value('user_money');
        //工资start
        $where['type'] = 4;
        $where['status'] = 1;
        $where['user_id'] = session('user_id');

        $user['points_wages'] = db('points_log')->where($where)->sum('points_wages');
        //充值累计
        $rechargeMap['user_id'] = session('user_id');
        $rechargeMap['pay_status'] = 1;

        $user['account'] = db('recharge')->where($rechargeMap)->sum('account');

        //返利start
        $where2['type'] = 1;
        $where2['status'] = 1;
        $where2['user_id_pusher|user_id'] = session('user_id');

        $result = db('points_log')->where($where2)->select();
        $user['points_return'] = 0;
        foreach ($result as $k => $v) {
            if(session('user_id') == $v['user_id']){
                $user[$k]['points'] = $v['points_user']; 
            }
            else{
                $user[$k]['points'] = $v['points_user_pusher']; 
            }
            $user['points_return'] += $user[$k]['points'];
        }

        //返利end
        //累计使用start
        $usedMap['status'] = 1;
        $usedMap['points_pay'] = ["<>", '0.00'];
        $usedMap['user_id'] = session('user_id');
        $user['used'] = db('points_log')->where($usedMap)->order('id desc')->sum('points_pay');
        //累计使用end

        $this->assign('user', $user);
        return view();
	}

	//充值记录
	public function recording(){
        return view();
    }

    //返利币转换报单币
    public function rebate(){
        if(request()->isAjax()){
            $post = input('post.');
            $data = [
                'points_rebate' => $post['money'],
                'user_id' => session('user_id'),
                'status' => 1,
                'type' => 7,
                'add_time' => time(),
                ];

            $user = db('users')->where('user_id', session('user_id'))->find();
            if($user['rebate_money'] < $post['money']){
                $this->error('报单币不足！');
            }
            //事务数据处理
            db()->startTrans();
            try { 
                db('users')->where('user_id', session('user_id'))->setDec('rebate_money', $post['money']);
                db('users')->where('user_id', session('user_id'))->setInc('pay_money', $post['money']);
                db('points_log')->insert($data);

                db()->commit();
                $this->success('兑换成功');
            }
            catch (Exception $e){
                db()->rollback();
                $this->error('兑换不成功，请刷新后重新尝试提交！');
            }   
                
        }
        $money = db('users')->where('user_id', session('user_id'))->value('rebate_money');
        $this->assign('money', $money);

        return view();
    }
    //返利币转换购物币
    public function rebate2(){
        if(request()->isAjax()){
            $post = input('post.');
            $data = [
                'points_rebate' => $post['money'],
                'user_id' => session('user_id'),
                'status' => 1,
                'type' => 8,    //'0未分类，1商城消费，2升级，3提现 ,4充值,5购物邮费，6报单币换购物7返利币换报单币8返利币换购物币'
                'add_time' => time(),
                ];

            $user = db('users')->where('user_id', session('user_id'))->find();
            if($user['rebate_money'] < $post['money']){
                $this->error('报单币不足！');
            }
            //事务数据处理
            db()->startTrans();
            try { 
                db('users')->where('user_id', session('user_id'))->setDec('rebate_money', $post['money']);
                db('users')->where('user_id', session('user_id'))->setInc('user_money', $post['money']);
                db('points_log')->insert($data);

                db()->commit();
                $this->success('兑换成功');
            }
            catch (Exception $e){
                db()->rollback();
                $this->error('兑换不成功，请刷新后重新尝试提交！');
            }   
                
        }
        $money = db('users')->where('user_id', session('user_id'))->value('rebate_money');
        $this->assign('money', $money);
        return view();
    }

    //奖励积分
    public function reward(){
        return view();
    }

    //使用记录
    public function usage(){
        return view();
    }

    //积分充值
    public function recharge(){
        if(request()->isAjax()){
            $post = input('post.');

            $order_sn = date("YmdHis").rand(100,999).rand(1,100).'recharge';

            $time = time(); 
            $data = [
                    'user_id' => session('user_id'),
                    'nickname' => session('user')['nickname'],
                    'order_sn' => $order_sn,
                    'account' => $post['money'],
                    'ctime' => $time,
                    'pay_code' => 0,
                    'pay_name' => '人工处理',
                    'pay_time' => $time,
                    'update_user_id' => session('user')['first_leader'],
                    ];

            db()->startTrans();
            try {

                $order_id = db('recharge')->insertGetId($data);  
                //提交到总表
                $log = [
                    'recharge_id' => $order_id,
                    'points_recharge' => $post['money'],
                    'user_id' => session('user_id'),
                    'user_id_pusher' => session('user')['first_leader'],
                    'add_time' => $time,
                    'status' => 0,
                    'type' => 4,//0未分类，1商城消费，2升级，3提现 ,4充值,5购物邮费
                ];
                db('points_log')->insert($log);


                db()->commit();
                return $this->success('提交成功，您的上级会抓紧时间处理！');
            }
            catch (Exception $e){
                db()->rollback();
                return $this->error('添加失败，请重新添加！');
            }     
        }
        return view();
    }

    //积分充值审核
    public function examine(){
        $map = [
                    'pay_status' => 0,
                    'update_user_id' => session('user_id')
                ];

        $map2 = [
                    'pay_status' => 0,
                    'user_id' => session('user_id')
                ];
        $son_check = db('recharge')->where($map)->count();
        $my_check = db('recharge')->where($map2)->count();
        $this->assign([
            'son_check' => $son_check,
            'my_check' => $my_check,
            ]);
        return view();
    }

    //积分充值审核列表
    public function examine2(){
        $type = input('type');
        $this->assign('type', $type);
        if(request()->isPost()){


            $post = input('post.');
            $limit = $post['pagesize']? $post['pagesize']:10;
            $page = $post['page']? $post['page'] :1;      
            $offset = $limit*($page-1);
            if($type==1){
                $map = [
                    'pay_status' => 0,
                    'update_user_id' => session('user_id')
                ];
            }
            if($type==2){
                $map = [
                    'pay_status' => 0,
                    'user_id' => session('user_id')
                ];
            }
            
            if(input('pay_status')){
                $map['pay_status'] = ['neq', 0];
            }
            $data = db('recharge')->field('order_id, user_id, account, ctime, pay_status')->where($map)->order('order_id desc')->limit($offset, $limit)->select();
            //halt(db()->getLastSql());
            foreach ($data as $k => $v) {
                $user_level_mobile = user_level_mobile($v['user_id']);
                $data[$k]['phone'] = $user_level_mobile['mobile'];
                $data[$k]['level'] = $user_level_mobile['level'];
                $data[$k]['time'] = formatTime($v['ctime'], 'Y-m-d');
                $data[$k]['money'] = $v['account'];
            }
            $list['lists'] = $data;
            return $list;

            
                
        }
        return view();
    }

    //积分充值审核操作
    public function examineHandle(){
        if(request()->isAjax()){
            $post = input('post.');
            $order_id = $post['order_id']; 
            $pay_status = $post['paystatus'];

            

            //事务数据处理
            db()->startTrans();
            try {
                $log = db('points_log')->where('recharge_id', $order_id)->find();


                $map = [ 
                    'update_user_id' => session('user_id'),
                    'order_id' => $order_id
                ];
                if($pay_status == 2){      //拒绝
                    $time = time();
                    $recharge_data = [
                        'pay_status'=>2,
                        'update_time'=>$time
                        ];
                    db('recharge')->where($map)->update($recharge_data);
                    $log_data = [
                        'status'=>2,
                        'update_time'=>$time
                        ];
                    db('points_log')->where('recharge_id', $order_id)->update($log_data);

                    //写入消息
                    $time = time();
                    $article_data = [
                        'content' => '申请金额'.$log['points_recharge'].'元，申请时间：'.date('Y-m-d H:i:s', $log['add_time']),
                        'title' => '您有一条充值申请被拒绝',
                        'admin_id' => 0,
                        'cat_id' => 2,
                        'add_time' => $time,
                        'publish_time' => $time,
                        ];
                    $article_id = db('article')->insertGetId($article_data);    
                    $message_data = [
                        'message_id' => $article_id,
                        'user_id' => $log['user_id'],
                        'status' => 1,
                        ];
                    db('user_message')->insert($message_data); 
                    //写入消息end
                    
                }
                if($pay_status == 1){       //通过 ，同时处理金额

                    //资金处理 从自己扣，加到用户身上
                    $recharge = db('recharge')->where('order_id', $order_id)->find();
                    $pay_money = db('users')->where('user_id', session('user_id'))->value('pay_money');
                    if($pay_money< $recharge['account']){
                        $this->error('您的报单币不足，请先充值或积分兑换！');
                    }

                    db('users')->where('user_id', session('user_id'))->setDec('pay_money', $recharge['account']);
                    db('users')->where('user_id', session('user_id'))->setInc('pay_money_used', $recharge['account']);//加到自己的充值消耗总量上
                    db('users')->where('user_id', $recharge['user_id'])->setInc('pay_money', $recharge['account']);   //给用户充值

                    //获得返利start
                    if($log['user_id_pusher'] > 0){
                        $level_id = db('users')->where('user_id', session('user_id'))->value('level');
                        $points_pusher = db('user_level')->where('level_id', $level_id)->value('points_pusher');  //用户等级返比

                        $points_user_pusher = $recharge['account']*$points_pusher/100;

                        db('points_log')->where('recharge_id', $order_id)->setField('points_user_pusher', $points_user_pusher);//设置推荐人返利
                        db('users')->where('user_id', session('user_id'))->setInc('rebate_money', $points_user_pusher);         //推荐人返利
                        
                    }
                    //获得返利end


                    //资金处理end

                    //更新状态
                    $map = [ 
                        'update_user_id' => session('user_id'),
                        'order_id' => $order_id
                    ];
                    $time = time();
                    $recharge_data = [
                        'pay_status'=>1,
                        'update_time'=>$time
                        ];

                    db('recharge')->where($map)->update($recharge_data);
                    $log_data = [
                        'status'=>1,
                        'update_time'=>$time
                        ];
                    db('points_log')->where('recharge_id', $order_id)->update($log_data);

                    //写入消息
                    $time = time();
                    $article_data = [
                        'content' => '申请金额'.$recharge['account'].'元，申请时间：'.date('Y-m-d H:i:s', $recharge['pay_time']),
                        'title' => '您有一条充值申请已经通过',
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
    }

    //币种兑换列表
    public function exchange(){
        if(request()->isAjax()){
            $post = input('post.');

                
        }
        return view();
    }

    //报单币兑换购物币
    public function exchange2(){
        if(request()->isAjax()){
            $post = input('post.');
            $data = [
                'points_pay' => $post['money'],
                'user_id' => session('user_id'),
                'status' => 1,
                'type' => 6,
                'add_time' => time(),
                ];

            $user = db('users')->where('user_id', session('user_id'))->find();
            if($user['pay_money'] < $post['money']){
                $this->error('报单币不足！');
            }
            //事务数据处理
            db()->startTrans();
            try { 
                db('users')->where('user_id', session('user_id'))->setDec('pay_money', $post['money']);
                db('users')->where('user_id', session('user_id'))->setInc('user_money', $post['money']);
                db('points_log')->insert($data);

                db()->commit();
                $this->success('兑换成功');
            }
            catch (Exception $e){
                db()->rollback();
                $this->error('兑换不成功，请刷新后重新尝试提交！');
            }   
                
        }
        $money = db('users')->where('user_id', session('user_id'))->value('pay_money');
        $this->assign('money', $money);
        return view();
    }

    //积分提现
    public function withdraw(){

        $map['user_id'] = session('user_id');
        $user = db('users')->where($map)->find();
        $bank = $user['bank'];
        if(empty($bank)){
            $this->error('请先绑定银行卡！', url('users/bank'));
        }

        if(request()->isAjax()){
            $post = input('post.');
            if($post['user_money']%100){
                $this->error('提现金额必须要100的整数倍 ！');
            }
            if($user['rebate_money'] < $post['user_money']){
                $this->error('返利币不足！');
            }

            //事务数据处理
            db()->startTrans();
            try {
                $time = time();
                $data = [
                        'bank' => $user['bank'],
                        'openbank' => $user['openbank'],
                        'number' => $user['bankcard'],
                        'mobile' => $user['mobile'],
                        'name' => $user['username'],
                        'user_id' => session('user_id'),
                        'user_money' => $post['user_money'],
                        'add_time' => $time
                        ];

                $id = db('user_withdraw')->insertGetId($data);

                //写入金币变动
                $log = [
                    'withdraw_id' => $id,
                    'add_time' => $time,
                    'user_id' => session('user_id'),
                    'status' => 0,
                    'points_rebate'=> $post['user_money'],
                    'type'=> 3,  //提现类型
                    ];

                db('points_log')->insert($log);

                db()->commit();
                $this->success('提交成功');
            }
            catch (Exception $e){
                db()->rollback();
                $this->error('提交不成功，请刷新后重新尝试提交！');
            }
        }
        return view();
    }

}
?>