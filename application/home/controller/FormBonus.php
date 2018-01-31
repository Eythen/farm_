<?php
/**
 * 加权奖金数据
 * 
 */
namespace app\home\controller;

class FormBonus extends Base {

    public function index(){

    	$timegap = input('timegap')? input('timegap'):date('Y-m');
    	//先获取黑钻用户 ，再获取为下线充值
    	$map = [
    		'level' => 1,
    		];
    	$user1 = db('users')->where($map)->column('user_id, level, first_leader', 'user_id');

    	foreach ($user1 as $k => $v) {
    		if($v['first_leader']>=1){    //设置用户的下级黑钻用户
    			$user1[$v['first_leader']]['son'][] = $v['user_id'];
    		}
    	}
    //halt($user1);

    	$users = get_all_users('nickname, mobile', 'user_id');

    	//整盘业绩
    	$whereall['type'] = 4; //2升级 4充值 6报单币转购物币
    	$whereall['status'] = 1;
    	if($timegap){
    		$start = strtotime($timegap);
    		$end = strtotime('+ 1 month', strtotime($timegap) );
    		$whereall['add_time'] = ['between', [$start, $end] ];
    	}
		$all_points_pay = db('points_log')->where($whereall)->sum('points_recharge');   //type 4

		//计算转换的报单币
		$whereall['type'] = 6; // 4充值 6报单币转购物币 （2升级与6有重复）只算一个6
 
		$all_points_pay += db('points_log')->where($whereall)->sum('points_pay'); 		//type 6
		//计算转换的报单币end
		


		if($all_points_pay){

			$month_pay = $all_points_pay * 0.05; //本月月绩分红
			$people_num = count($user1);       //人数
			$avg_pay = floor($month_pay * 0.5 / $people_num*100)/100; //人头平均

			$total_month_pay = 0;
			$total_son_pay = 0;
			$total_avg_pay = 0;
			$total_user_pay = 0;
	    	//再循环取
	    	foreach ($user1 as $k => $v) {
	    		$where['user_id_pusher'] = $v['user_id'];
	    		$where['type'] = 4; //2升级 4充值 6报单币转购物币
	    		$where['status'] = 1;
	    		if($timegap){
		    		$start = strtotime($timegap);
		    		$end = strtotime('+ 1 month', strtotime($timegap) );
		    		$where['add_time'] = ['between', [$start, $end] ];
		    	}
	    		$points[$v['user_id']]['points_recharge'] = db('points_log')->where($where)->sum('points_recharge');
	    		$where['type'] = 6; //2升级 4充值 6报单币转购物币（2升级与6有重复）只算一个6
	    		unset($where['user_id_pusher']);
	    		$where['user_id'] = $v['user_id'];
	    		$points[$v['user_id']]['points_recharge'] += db('points_log')->where($where)->sum('points_pay');
	    	
	    		$points[$v['user_id']]['points_pay_son'] = 0;
	    		if($v['son']){
		    		$where2['user_id_pusher'] = ['in', $v['son']];
		    		$where2['type'] = 4; //2升级 4充值 6报单币转购物币
		    		$where2['status'] = 1;
		    		if($timegap){
			    		$start = strtotime($timegap);
			    		$end = strtotime('+ 1 month', strtotime($timegap) );
			    		$where2['add_time'] = ['between', [$start, $end] ];
			    	}
		    		$points[$v['user_id']]['points_pay_son'] = db('points_log')->where($where2)->sum('points_recharge');
		    		$where2['type'] = 6; //2升级 4充值 6报单币转购物币（2升级与6有重复）只算一个6
		    		unset($where2['user_id_pusher']);
		    		$where['user_id'] = ['in', $v['son']];
		    		$points[$v['user_id']]['points_pay_son'] += db('points_log')->where($where2)->sum('points_pay');
		    	}
	    		$points[$v['user_id']]['user_id'] = $v['user_id'];
	    		$points[$v['user_id']]['nickname'] = $users[$v['user_id']]['nickname'];
	    		$points[$v['user_id']]['mobile'] = $users[$v['user_id']]['mobile'];
	    		$points[$v['user_id']]['month_pay'] = floor($month_pay*100)/100;
	    		$points[$v['user_id']]['avg_pay'] = $avg_pay;
	    		$points[$v['user_id']]['user_pay'] = floor($month_pay * 0.5 * $points[$v['user_id']]['points_recharge'] / $all_points_pay*100)/100;
	    		$points[$v['user_id']]['all_pay'] = $avg_pay + $points[$v['user_id']]['user_pay'];

	    		$total_month_pay += $points[$v['user_id']]['all_pay'];
	    		$total_son_pay += $points[$v['user_id']]['points_pay_son'];
	    		$total_avg_pay += $points[$v['user_id']]['avg_pay'];
	    		$total_user_pay += $points[$v['user_id']]['user_pay'];



	    	}

	    	array_multisort(array_column($points,'points_recharge'),SORT_DESC,$points);

		}
    	

    	$this->assign(['points' => $points, 
    		'total_son_pay' => $total_son_pay,
    		'total_month_pay' => $total_month_pay,
    		'total_avg_pay' => $total_avg_pay,
    		'total_user_pay' => $total_user_pay,
    		'timegap' => $timegap,
    		'all_points_pay' => $all_points_pay,
    		]);
        return view();
    }

}