<?php
/**
 * 会员数据表
 * 
 */
namespace app\home\controller;

class FormUsers extends Base {
    
    public function _initialize()
    {
        parent::_initialize();
        $user_level = db('user_level')->column('level_id,level_name');
        $this->assign('user_level',$user_level);
    }

    //会员总表
    public function index(){
        $level_id = input('level_id');
        if ($level_id){
            $where['ul.level_id'] = $level_id;
        }
        $nickname = input('nickname');
        if ($nickname){
            $where['u.nickname'] = $nickname;
        }
        $mobile = input('mobile');
        if ($mobile){
            $where['u.mobile'] = $mobile;
        }
        $first_leader_name = input('first_leader_name');
        if ($first_leader_name){
            $first_leader = db('users')->where('nickname',$first_leader_name)->value('user_id');
            $where['u.first_leader'] = $first_leader;
        }
        $users = db('users')->field('u.user_id,u.mobile,u.nickname,ul.level_name,ul.level_id,u.first_leader,ul.points_pusher,u.pay_money_used,u.user_money_used')->alias('u')->join('user_level ul','u.level=ul.level_id')->where($where)->select();
        foreach ($users as $k => $v){
            if ($v['first_leader'] == 0 || $v['first_leader'] == null){
                $users[$k]['first_leader'] = '无';
            }
            if ($first_leader_name){
                $users[$k]['first_leader'] = $first_leader_name;
            }else{
                foreach ($users as $k2 => $v2){
                    if ($v['first_leader'] == $v2['user_id']){
                        $users[$k]['first_leader'] = $v2['nickname'];
                        continue;
                    }
                }
            }
        }
        $this->assign('users',$users);
        return $this->fetch();
    }

    //会员类型变化记录表
    public function log(){
        $level_id = input('level_id');
        if ($level_id){
            $where['ul.level_id'] = $level_id;
        }
        $nickname = input('nickname');
        if ($nickname){
            $where['u.nickname'] = $nickname;
        }
        $mobile = input('mobile');
        if ($mobile){
            $where['u.mobile'] = $mobile;
        }
        $first_leader_name = input('first_leader_name');
        if ($first_leader_name){
            $first_leader = db('users')->where('nickname',$first_leader_name)->value('user_id');
            $where['u.first_leader'] = $first_leader;
        }
        $where['l.type'] = 2;
        $log = db('points_log')->field('u.user_id,u.mobile,u.nickname,ul.level_name,u.first_leader,max(l.add_time) as add_time')->alias('l')->join('users u','l.user_id=u.user_id')->join('user_level ul','u.level=ul.level_id')->where($where)->group("l.user_id")->select();
        foreach ($log as $k => $v){
            $log[$k]['first_leader'] = db('users')->where('user_id',$v['first_leader'])->value('nickname');
        }
        $this->assign('log',$log);
        return view();
    }

    public function info(){
        $user_id = input('user_id');
        $info = db('users')->field('u.user_id,u.nickname,u.mobile,ul.level_name,ul.points_pusher,u.first_leader,u.pay_money_used,u.user_money_used,u.rebate_money,u.pay_money,u.user_money')->alias('u')->join('user_level ul','u.level=ul.level_id')->where('u.user_id',$user_id)->find();
        $info['first_leader_name'] = db('users')->where('user_id',$info['first_leader'])->value('nickname');
        $info['counts'] = db('points_log')->where(['user_id'=>$info['user_id'],'type'=>4,'status'=>1])->sum('points_recharge');
        $list = db('users')->field('u.user_id,u.mobile,u.nickname,ul.level_name,u.pay_money_used,u.user_money_used')->alias('u')->join('user_level ul','u.level=ul.level_id')->where('first_leader',$user_id)->order('u.user_id asc')->select();
        foreach ($list as $k => $v){
            $list[$k]['no'] = $v['user_id'];
        }
        $list = json_encode($list);
        $this->assign(array(
            'info' => $info,
            'user_id' => $user_id,
            'list' => $list,
        ));
        return view();
    }

    public function gerChildren(){
        if (request()->isAjax()){
            $user_id = input('user_id');
            $list = db('users')->field('u.user_id,u.mobile,u.nickname,ul.level_name,u.pay_money_used,u.user_money_used')->alias('u')->join('user_level ul','u.level=ul.level_id')->where('first_leader',$user_id)->select();
            foreach ($list as $k => $v){
                $list[$k]['no'] = $user_id.'-'.$v['user_id'];
            }
            return $list;
        }
    }

}