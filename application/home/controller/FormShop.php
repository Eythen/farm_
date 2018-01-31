<?php
/**
 * 订单表单
 * 
 */
namespace app\home\controller;

class FormShop extends Base {

    //订单记录
    public function index(){
        $timegap = input('timegap');
        $timegaparry = explode('-',$timegap);
        $begin_time = strtotime(trim($timegaparry['0']));
        $end_time = strtotime("+1 day",strtotime(trim($timegaparry[1])));
        if ($begin_time && $end_time){
            $where['o.add_time'] = ['between',[$begin_time,$end_time]];
        }
        $where['pay_status'] = 1;
        $orders = db('order')->field('o.order_id,o.add_time,o.order_sn,o.pay_time,o.order_amount,u.mobile,ul.level_name,u.first_leader')->alias('o')->join('users u','o.user_id=u.user_id')->join('user_level ul','ul.level_id=u.level')->where($where)->select();
        foreach ($orders as $k => $v){
            $orders[$k]['goods_num'] = db('order_goods')->where('order_id',$v['order_id'])->sum('goods_num');
            $first_leader_info = db('users')->field('u.mobile,ul.level_name')->alias('u')->join('user_level ul','ul.level_id=u.level')->where('user_id',$v['first_leader'])->find();
            $orders[$k]['mobile_pusher'] = $first_leader_info['mobile'];
            $orders[$k]['user_level_pusher'] = $first_leader_info['level_name'];
        }
        $this->assign('orders',$orders);
        $this->assign('timegap',$timegap);
        return view();
    }

}