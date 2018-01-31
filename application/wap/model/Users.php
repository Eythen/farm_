<?php
/**
 * 用户
 */
namespace app\wap\model;
use think\Model;
class Users extends Model{

    //注册
    public function reg($data,$expressArea){
        //密码加密
        $data['password'] = md5(md5($data['password']).config('md5_key'));

        //支付密码加密
        $data['pay_code'] = md5(md5($data['pay_code']).config('md5_key'));
        //地区
        /*$expressArea = explode(' > ',$expressArea);
        $data['province'] = db('region')->where('name',$expressArea[0])->value('id');
        $num = count($expressArea);
        if ($num > 1){
            $data['city'] = db('region')->where('name',$expressArea[1])->where('parent_id',$data['province'])->value('id');
            if ($num == 3){
                $data['district'] = db('region')->where('name',$expressArea[2])->where('parent_id',$data['city'])->value('id');
            }
        }*/
        //保存推荐用户
        if(session('tguid')){
             $data['first_leader'] = session('tguid');
             $second_leader = $this->where('user_id',$data['first_leader'])->value('first_leader');
             if($second_leader){
                $data['second_leader'] = $second_leader;
                $third_leader = $this->where('user_id',$data['second_leader'])->value('first_leader');
                if($third_leader){
                    $data['third_leader'] = $third_leader;
                }
             }
        }

        //推荐手机号码
        if ($data['leadermobile']){
            //获取该推荐人的信息
//            $data['first_leader'] = $this->where('mobile',$data['leadermobile'])->value('user_id');
//            unset($data['leadermobile']);
//            $this->setLevel($data['first_leader']);
            $first_leader = $this->field('user_id,first_leader')->where('mobile',$data['leadermobile'])->find();
            $data['first_leader'] = $first_leader['user_id'];
            if ($first_leader['first_leader']){
                $data['second_leader'] = $first_leader['first_leader'];
                $third_leader = $this->where('user_id',$data['second_leader'])->value('first_leader');
                if($third_leader){
                    $data['third_leader'] = $third_leader;
                }
            }
        }
        unset($data['leadermobile']);
        $data['nickname'] = $data['mobile'];
        return $this->save($data);
    }

//    //检验是否可升级
//    public function setLevel($user_id){
//        $leaderInfo = $this->field('level,first_leader')->where('user_id',$user_id)->find();
//        if ($leaderInfo['level'] == 3){
//            return;
//        }elseif ($leaderInfo['level'] == 2){
//            $amount = db('user_level')->where('level_id',$leaderInfo['level']+1)->value('amount');
//            $num = $this->where('first_leader',$user_id)->where('level=2')->count();
//            if ($num >= $amount){
//                $this->where('user_id',$user_id)->setInc('level');
//                if ($leaderInfo['first_leader']){
//                    $this->setLevel($leaderInfo['first_leader']);
//                }
//            }
//            return;
//        }elseif ($leaderInfo['level'] == 1){
//            $amount = db('user_level')->where('level_id',$leaderInfo['level']+1)->value('amount');
//            $num = $this->where('first_leader',$user_id)->count()+1;
//            if ($num >= $amount){
//                $this->where('user_id',$user_id)->setInc('level');
//                if ($leaderInfo['first_leader']){
//                    $this->setLevel($leaderInfo['first_leader']);
//                }
//            }
//            return;
//        }
//    }

    //登陆
    public function login($data){
        $users = Users::getByMobile($data['mobile']);
        if ($users) {
            if ($users['password'] == md5(md5($data['password']).config('md5_key')) ) {
                if($users['is_lock'] == 1){
                    return 4;
                    // 用户冻结
                }
                else{
                    session('user',$users);
                    session('user_id',$users['user_id']);
                    //登陆日志
                    model('index/usersLogic', 'logic')->login_log($users['user_id'], $users['nickname'], 'H5');
                    return 2;
                    // 登陆信息正确
                }
            }
            else{
                return 3;
                // 登陆密码错误
            }
        }else{
            return 1;
            // 用户不存在
        }
    }
}
?>