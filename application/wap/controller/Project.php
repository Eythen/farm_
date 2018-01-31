<?php
/**
 * 项目
 */
namespace app\wap\controller;
use app\wap\model\Project as ProjectModel;

class Project extends Base{

    //项目详情
    public function details(){
        $host = request()->domain();
        $user_id = session('user_id');
        //项目
        $help_id = input('help_id');
        $help = db('help')->alias('h')->field('h.*,g.org_id,g.name')->join('org g','h.org_id=g.org_id')->where('help_id',$help_id)->find();
        $help['points_num'] = db('points_log')->where('help_id',$help['help_id'])->count();
        $help['people_num'] = db('help_people')->where('help_id',$help['help_id'])->count();
        $help['car_num'] = db('help_car')->where('help_id',$help['help_id'])->count();
        $help['points'] = db('points_log')->where('help_id',$help['help_id'])->sum('points_pay');

        //进展报告
        $progress = db('help')->field('help_id,title,add_time,zan')->where('help_id','<>',$help_id)->limit(2)->select();
        foreach ($progress as $k => $v){
            $progress[$k]['images'] = db('help_images')->where('help_id',$v['help_id'])->limit(3)->column('image_url');
        }

        //最新动态
        $map['type'] = 2;
        $map['help_id'] = $help_id;
        $news = db('points_log')->alias('p')->field('p.add_time,p.points_pay,u.nickname')->join('users u','p.user_id=u.user_id')->where($map)->order('add_time desc')->limit(5)->select();
        $now = time();
        foreach ($news as $key => $v){
            if ($now - $v['add_time'] < 30){
                $news[$key]['time'] = "30秒前";
            }elseif ($now - $v['add_time'] < 300){
                $news[$key]['time'] = "5分钟前";
            }elseif ($now - $v['add_time'] < 3600){
                $news[$key]['time'] = "1小时前";
            }
            $news[$key]['points_pay'] = ceil($v['points_pay']);
        }
        $this->assign(array(
            'host' => $host,
            'user_id' => $user_id,
            'help' => $help,
            'progress' => $progress,
            'news' => $news,
        ));
        return view();
	}

	//点赞
    public function zan(){
        if (request()->isAjax()){
            $help_id = input('help_id');
            db('help')->where('help_id',$help_id)->setInc('zan');
        }
    }

    //ajax获取更多动态
	public function ajaxMore(){
        if (request()->isAjax()){
            $page = input('page');
            $help_id = input('help_id');
            $where['type'] = 2;
            $where['help_id'] = $help_id;
            $lists = db('points_log')->alias('p')->field('p.add_time,p.points_pay,u.nickname')->join('users u','p.user_id=u.user_id')->where($where)->order('add_time desc')->page($page, '10')->select();
            $now = time();
            foreach ($lists as $key => $v){
                if ($now - $v['add_time'] < 30){
                    $lists[$key]['add_time'] = "30秒前";
                }elseif ($now - $v['add_time'] < 300){
                    $lists[$key]['add_time'] = "5分钟前";
                }elseif ($now - $v['add_time'] < 3600){
                    $lists[$key]['add_time'] = "1小时前";
                }else{
                    $lists[$key]['add_time'] = date("Y-m-d",$v['add_time']);
                }
                $lists[$key]['points_pay'] = ceil($v['points_pay']);
            }
            return $lists;
        }

    }

	//机构详情
	public function organize(){
        $org_id = input('org_id');
        $org = db('org')->field('name,pic,content')->find($org_id);
        $this->assign('org',$org);
        return view();
    }

    //进展报告
    public function progress(){
        $help_id = input('help_id');
        $help = db('help')->field('title,pic,status')->find($help_id);
        $res = db('help_report')->where('help_id',$help_id)->order('id desc')->select();
        $help['points_num'] = db('points_log')->where('help_id',$help_id)->count();
        $this->assign('help',$help);
        $this->assign('res',$res);
        return view();
    }

    //捐助
    public function donate(){
        if (request()->isAjax()){
            $data = array();
            $user_id = session('user_id');
            if ($user_id){
                $integral = input('integral');
                $help_id = input('help_id');

                //事务开始
                db()->startTrans();
                //扣除用户捐助的积分
                $isdec = db('users')->where('user_id',$user_id)->setDec('user_money',$integral);
                $data = array(
                    'add_time' => time(),
                    'type' => 2,
                    'user_id' => $user_id,
                    'points_pay' => $integral,
                    'help_id' => $help_id,
                    'status' => 2,
                );
                $log = db('points_log')->insert($data);
                if ($log && $isdec){
                    db()->commit();
                    $data['status'] = 1;
                    $data['msg'] = '捐助成功！';
                }else{
                    db()->rollback();
                    $data['status'] = 2;
                    $data['msg'] = '捐助失败！';
                }
            }else{
                $data['status'] = 2;
                $data['msg'] = "您还未登陆，请登陆后再操作！";
            }
            return $data;
        }
    }

    //参加志愿者
    public function people(){

        $user_id = session('user_id');
        if ($user_id){
            $help_id = input('help_id');
            if (request()->isPost()){
                $post = input('post.');
                if ($_FILES['pic']['tmp_name']) {
                    $file = request()->file('pic');
                    $info = $file->rule('uniqid')->move(ROOT_PATH . 'public/public' . '/' . 'project' . '/' . $post['user_id']);
                    if ($info) {
                        $pic =  '/' .'public' . '/' . 'project' . '/' . $post['user_id'] . '/' . $info->getSaveName();
                        $post['pic'] = $pic;
                    }
                }
                $post['add_time'] = time();
                $res = db('help_people')->insert($post);
                if ($res){
                    $this->success('报名成功！',url('details',array('help_id'=>$post['help_id'])));
                }else{
                    $this->error('报名失败，请稍后再重试！');
                }
            }
            $is_join = db('help_people')->where('user_id',$user_id)->where('help_id',$help_id)->value('id');
            if ($is_join){
                $this->error('您已经报名过该项目！');
            }
            $this->assign(array(
                'user_id' => $user_id,
                'help_id' => $help_id,
            ));
            return view();
        }else{
            $this->error('您还未登陆！',url('Login/index'));
        }

    }

    //用车
    public function car(){

        $user_id = session('user_id');
        if ($user_id){
            $help_id = input('help_id');
            if (request()->isPost()){
                $post = input('post.');
                if ($_FILES['car_pic']['tmp_name']) {
                    $file = request()->file('car_pic');
                    $info = $file->rule('uniqid')->move(ROOT_PATH . 'public/public' . '/' . 'project' . '/' . $post['user_id']);
                    if ($info) {
                        $pic =  '/' .'public' . '/' . 'project' . '/' . $post['user_id'] . '/' . $info->getSaveName();
                        $post['car_pic'] = $pic;
                    }
                }
                $post['add_time'] = time();
                $res = db('help_car')->insert($post);
                if ($res){
                    $this->success('报名成功！',url('details',array('help_id'=>$post['help_id'])));
                }else{
                    $this->error('报名失败，请稍后再重试！');
                }
            }
            $is_join = db('help_car')->where('user_id',$user_id)->where('help_id',$help_id)->value('id');
            if ($is_join){
                $this->error('您已经报名过该项目！');
            }
            $this->assign(array(
                'user_id' => $user_id,
                'help_id' => $help_id,
            ));
            return view();
        }else{
            $this->error('您还未登陆！',url('Login/index'));
        }

    }
}
?>