<?php
/**
 * 全返记录
 * 
 */
namespace app\home\controller;

class FormRebate extends Base {

    public function index(){
        $where = '';
        $timegap = input('timegap');
        if($timegap){
            $time_r = explode('-', $timegap);
            $where['r.pay_time'] = ['between', [strtotime(trim($time_r[0])), strtotime("+1 day",strtotime(trim($time_r[1])))] ];
        }
        $nickname = input('nickname');
        if ($nickname){
            $where['u.nickname'] = $nickname;
        }
        $mobile = input('mobile');
        if ($mobile){
            $where['u.mobile'] = $mobile;
        }
        $rebate_name = input('rebate_name');
        if ($rebate_name){
            $where['u.rebate_name'] = $rebate_name;
        }
        $level_id = input('level_id');
        if ($level_id){
            $where['r.level'] = $level_id;
        }

        $levels = db('user_level')->column('level_id,level_name');
        $res = db('rebate')->field('r.order_id,r.order_sn,r.order_amount,r.level,r.user_id,r.pay_time,r.mobile,r.level_name,r.rebate,r.money,u.nickname,u.rebate_name,u.username')->alias('r')->join('users u','r.user_id=u.user_id')->where($where)->select();
        $this->assign(array(
            'res' => $res,
            'levels' => $levels,
            'timegap' => $timegap,
            'nickname' => $nickname,
            'mobile' => $mobile,
            'rebate_name' => $rebate_name,
            'level_id' => $level_id,
        ));
        return view();
    }

}