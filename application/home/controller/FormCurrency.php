<?php
/**
 * 币池数据表
 * 
 */
namespace app\home\controller;

class FormCurrency extends Base {

    public function index(){
        $timegap = input('timegap');
        if($timegap){
            $time_r = explode('-', $timegap);
            $where['l.add_time'] = ['between', [strtotime(trim($time_r[0])), strtotime("+1 day",strtotime(trim($time_r[1])))] ];
        }
        $user_name = input('user_name');
        if ($user_name){
            $where['u.nickname'] = ['like','%'.$user_name.'%'];
        }
        $where['l.type'] = 4;
        $where['l.user_id_pusher'] = 0;
        $where['l.status'] = 1;
        $log = db('points_log')->field("l.add_time,l.update_time,u.nickname,l.points_recharge")->alias('l')->join('users u',"l.user_id=u.user_id")->where($where)->select();
        $this->assign(array(
            'log' => $log,
            'timegap' => $timegap,
            'user_name' => $user_name,
            'empty' => '<td colspan="4">暂无数据</td>',
        ));
        return view();
    }

}