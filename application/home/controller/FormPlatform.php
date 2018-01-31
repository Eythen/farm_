<?php
/**
 * 平台表单（收支总表）
 * 
 */
namespace app\home\controller;

class FormPlatform extends Base {

    //返利币收支表
    public function rebate(){
        $where['l.type'] = ['in','3,4,7,8,9'];
        $where['l.status'] = 1;
        $timegap = input('timegap');
        if($timegap){
            $time_r = explode('-', $timegap);
            $where['l.add_time'] = ['between', [strtotime(trim($time_r[0])), strtotime("+1 day",strtotime(trim($time_r[1])))] ];
        }
        $source = input('source');
        $purpose = input('purpose');
        if ($source && $purpose){
            $where['l.type'] = ['in',$source.','.$purpose];
        }elseif($purpose){
            $where['l.type'] = $purpose;
        }elseif($source){
            $where['l.type'] = $source;
        }
        $pusher = input('pusher');
        if ($pusher){
            $where['l.user_id_pusher'] = db('users')->where('nickname',$pusher)->value('user_id');
        }
        $nickname = input('nickname');
        if ($nickname){
            $where['u.nickname'] = $nickname;
        }
        $log = db('points_log')->field('l.id,l.add_time,u.nickname,l.type,l.user_id_pusher,l.points_user_pusher,l.points_rebate')->alias('l')->join('users u','l.user_id=u.user_id')->where($where)->select();
        foreach ($log as $k => $v){
            $log[$k]['pusher_name'] = db('users')->where('user_id',$v['user_id_pusher'])->value('nickname');
            if ($v['type'] == 4){
                $log[$k]['points_pusher'] = db('user_level')->alias('ul')->join('users u','ul.level_id=u.level')->where('user_id',$v['user_id_pusher'])->value('ul.points_pusher');
            }

        }
        $this->assign(array(
            'log' => $log,
            'timegap' => $timegap,
            'source' => $source,
            'purpose' => $purpose,
            'pusher' => $pusher,
            'nickname' => $nickname,
        ));
        return $this->fetch();
    }

    //报单币收支表
    public function pay(){
        $where['l.type'] = ['in','4,6,7'];
        $timegap = input('timegap');
        if($timegap){
            $time_r = explode('-', $timegap);
            $where['l.add_time'] = ['between', [strtotime(trim($time_r[0])), strtotime("+1 day",strtotime(trim($time_r[1])))] ];
        }
        $source = input('source');
        $purpose = input('purpose');
        if ($purpose == 9 && $source == 4){
            $purpose = 4;
        }elseif ($purpose == 9){
            $purposes = 9;
            $purpose = 4;
        }elseif ($source == 4){
            $purposes = 4;
        }
        if ($source && $purpose){
            $where['l.type'] = ['in',$source.','.$purpose];
        }elseif($purpose){
            $where['l.type'] = $purpose;
        }elseif($source){
            $where['l.type'] = $source;
        }
        $nickname = input('nickname');
        if ($nickname){
            $where['u.nickname'] = $nickname;
        }
        $pusher = input('pusher');
        if ($pusher){
            $where['l.user_id_pusher'] = db('users')->where('nickname',$pusher)->value('user_id');
        }

        $logs = array();
        $log = db('points_log')->field('l.add_time,u.nickname,l.type,l.points_recharge,l.points_rebate,l.points_pay,l.user_id_pusher')->alias('l')->join('users u','l.user_id=u.user_id')->where($where)->select();
        foreach ($log as $k => $v){
            $v['pusher'] = db('users')->where('user_id',$v['user_id_pusher'])->value('nickname');
            if ($purposes || $purposes == 9){
                if ($v['type'] == 4){
                    $v['type'] = $purposes;
                }
            }else{
                if ($v['type'] == 4){
                    $value = $v;
                    $value['type'] = 9;
                    $logs[] = $value;
                }
            }
            $logs[] = $v;
        }
        $this->assign(array(
            'log' => $logs,
            'timegap' => $timegap,
            'nickname' => $nickname,
            'pusher' => $pusher,
        ));
        return $this->fetch();
    }

    //购物币收支表
    public function money(){
        $where['l.type'] = ['in','1,6,8'];
        $timegap = input('timegap');
        if($timegap){
            $time_r = explode('-', $timegap);
            $where['l.add_time'] = ['between', [strtotime(trim($time_r[0])), strtotime("+1 day",strtotime(trim($time_r[1])))] ];
        }
        $source = input('source');
        $purpose = input('purpose');
        if ($source && $purpose){
            $where['l.type'] = ['in',$source.','.$purpose];
        }elseif($purpose){
            $where['l.type'] = $purpose;
        }elseif($source){
            $where['l.type'] = $source;
        }
        $nickname = input('nickname');
        if ($nickname){
            $where['u.nickname'] = $nickname;
        }
        $log = db('points_log')->field('l.add_time,u.nickname,l.type,l.points_pay,l.points_rebate,l.points_money')->alias('l')->join('users u','l.user_id=u.user_id')->where($where)->select();
        $this->assign(array(
            'log' => $log,
            'timegap' => $timegap,
            'source' => $source,
            'purpose' => $purpose,
            'nickname' => $nickname,
        ));
        return $this->fetch();
    }

}