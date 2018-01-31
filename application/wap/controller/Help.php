<?php
/**
 * 我的帮扶
 */
namespace app\wap\controller;

class Help extends Base{

    public function index(){
        return view();
	}

	//捐助记录
	public function donation(){
        if(request()->isAjax()){
            $post = input('post.');
            if(strtotime($post['day'])){
                $where['add_time'] = ['between', [strtotime($post['day']), strtotime($post['day']." 23:59:59")]];
            }
            
            $where['user_id'] = session('user_id');
            $where['type'] = 2;

            $user = db('points_log')->where($where)->order('add_time desc')->limit(1000)->select();
            foreach ($user as $k => $v) {
                $user[$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d');
                $user[$k]['time'] = formatTime($v['add_time'], 'H:i:s');
                $user[$k]['status'] = '已付'.$v['points_pay'];
                $user[$k]['help'] = '<a href="'.url('project/details', ['help_id' => $v['help_id'] ]).'">详情'.$v['help_id']."</a>";
            }
            return $user;
        }
        

        $this->assign('title', '捐助记录');

        return $this->fetch('list');
    }

    //参加志愿者记录
    public function join(){
        if(request()->isAjax()){
            $post = input('post.');
            if(strtotime($post['day'])){
                $where['add_time'] = ['between', [strtotime($post['day']), strtotime($post['day']." 23:59:59")]];
            }
            
            $where['user_id'] = session('user_id');

            $user = db('help_people')->where($where)->order('add_time desc')->limit(1000)->select();
            foreach ($user as $k => $v) {
                $user[$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d');
                $user[$k]['time'] = formatTime($v['add_time'], 'H:i:s');
                $user[$k]['status'] = config('help_status')[$v['status']];
                $user[$k]['help'] = '<a href="'.url('project/details', ['help_id' => $v['help_id'] ]).'">详情'.$v['help_id']."</a>";
            }
            return $user;
        }      

        $this->assign('title', '参与志愿者记录');

        return $this->fetch('list');
    }

    //车辆援助记录
    public function car(){
        if(request()->isAjax()){
            $post = input('post.');
            if(strtotime($post['day'])){
                $where['add_time'] = ['between', [strtotime($post['day']), strtotime($post['day']." 23:59:59")]];
            }
            
            $where['user_id'] = session('user_id');

            $user = db('help_car')->where($where)->order('add_time desc')->limit(1000)->select();
            foreach ($user as $k => $v) {
                $user[$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d');
                $user[$k]['time'] = formatTime($v['add_time'], 'H:i:s');
                $user[$k]['status'] = config('help_status')[$v['status']];
                $user[$k]['help'] = '<a href="'.url('project/details', ['help_id' => $v['help_id'] ]).'">详情'.$v['help_id']."</a>";
            }
            return $user;
        }
        //工资end
        

        $this->assign('title', '车辆缓助记录');

        return $this->fetch('list');
    }

}
?>