<?php
/**
 * 项目
 */
namespace app\home\controller;
use app\home\model\Help as HelpModel;

class Help extends Base {

    public function helpList(){
        if(request()->isAjax()){
            $request = input('request.');
            $status = array('','进行中','已完结');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = '';
            if($request['search']){
                $where['title'] = ['like', '%'.$request['search'].'%'];
            }

            $data['rows'] = db('help')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('help')->where($where)->count();

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['title'] = getSubstr($v['title'], '0', '33');
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['org'] = getOrgName($v['org_id']);
                $data['rows'][$k]['status'] = $status[$v['status']];
            }
            return $data;

        }
        return view();
    }

    public function help(){
 		$act = input('act')?input('act'):'add';
        $info = array();
        if(input('help_id')){
           $help_id = input('help_id');
           $info = db('help')->where('help_id='.$help_id)->find();
           $helpImages = db('help_images')->where('help_id',$help_id)->select();
           $this->assign('helpImages',$helpImages);
        }
        $org = db('org')->column('org_id,name');
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->assign('org',$org);
        $this->initEditor();
        return $this->fetch();
    }

    /**
     * 初始化编辑器链接
     * @param $post_id post_id
     */
    private function initEditor()
    {
        $this->assign("URL_upload", url('home/Ueditor/imageUp',array('savepath'=>'help')));
        $this->assign("URL_fileUp", url('home/Ueditor/fileUp',array('savepath'=>'help')));
        $this->assign("URL_scrawlUp", url('home/Ueditor/scrawlUp',array('savepath'=>'help')));
        $this->assign("URL_getRemoteImage", url('home/Ueditor/getRemoteImage',array('savepath'=>'help')));
        $this->assign("URL_imageManager", url('home/Ueditor/imageManager',array('savepath'=>'help')));
        $this->assign("URL_imageUp", url('home/Ueditor/imageUp',array('savepath'=>'help')));
        $this->assign("URL_getMovie", url('home/Ueditor/getMovie',array('savepath'=>'help')));
        $this->assign("URL_home", "");
    }
    
    public function helpHandle(){
        $help = new HelpModel();
        $data = input('post.');
        if($data['act'] == 'add'){
            unset($data['act']);
        	$data['add_time'] = time();
            $r = $help->allowField(true)->save($data);
            $help->setImgs($help->help_id);
        }
        
        if($data['act'] == 'edit'){
            unset($data['act']);
            $r = $help->allowField(true)->update($data);
            $help->setImgs($data['help_id']);
        }
        
        if($data['act'] == 'del'){
            $data['help_id'] = trim($data['help_id'], ',');
            $map['help_id'] = ['in', $data['help_id']];
        	$r = $help->where($map)->delete();
        }

        if ($data['act'] == 'end'){
            $update['status'] = 2;
            $update['end_time'] = time();
            

            $r = $help->where('help_id',$data['help_id'])->update($update);

        }

        if($r !== false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }

    //支付帮扶工资
    public function pay(){
        if(request()->isAjax()){
            $data = input('post.');
            $d_r = explode("-", $data['days']);
            $done_start_time = strtotime(trim($d_r[0]));
            $done_end_time = strtotime(trim($d_r[1]));
            $days = ($done_end_time - $done_start_time)/24/3600+1; //天数

            $status = db('help')->where('help_id', $data['help_id'])->value('status');
            if($status <> '2'){
                $this->error('线上项目未完完结！请完结后再来支付工资！');
            }
            $pay = db('points_log')->where('help_id', $data['help_id'])->order('add_time desc')->find();
            if($pay){
                $this->error('已经支付工资，不可再支付！');
            }
            //结算工资
            $mapDone['status'] = 3;
            $mapDone['help_id'] = ['eq', $data['help_id'] ];

            $user_car = db('help_car')->where($mapDone)->column('user_id');
            $user_done = db('help_people')->where($mapDone)->column('user_id');

            //取工资系数
            $points_wages = db('config')->where('name = "points_wages"')->value('value');
            //积分池
            $points_public = db('config')->where('name = "points_public"')->value('value');

            $add_time = time();
            $user_get = 0;      //志愿者总工资
            $cat_get = 0;       //车辆总工资
            db()->startTrans();

            try {
                //更新项目执行日期与支付工资状态
                $up['done_start_time'] = $done_start_time;
                $up['done_end_time'] = $done_end_time;
                $up['is_pay'] = 1;
                db('help')->where('help_id', $data['help_id'])->update($up);
                if($user_done){

                    //志愿者工资
                    foreach ($user_done as $key => $value) {
                        $points_get = $points_wages*$days;
                        $user_get += $points_get;
                        $data3[] = [
                                'add_time' => $add_time,
                                'user_id' => $value,
                                'points_wages' => $points_get,
                                'status' => 1,
                                'type' => 4,
                                'help_id' => $data['help_id'],
                                'des' => '志愿者',
                        ];
                        unset($map);
                        $map['user_id'] = $value;
                        db('users')->where($map)->setInc('user_money', $points_get);
                    }

                    db('points_log')->insertAll($data2);
                }

                if($user_car){

                    //车辆服务工资
                    foreach ($user_car as $key => $value) {
                        $points_get = 2*$points_wages*$days;
                        $car_get += $points_get;
                        $data2[] = [
                                'add_time' => $add_time,
                                'user_id' => $value,
                                'points_wages' => $points_get,
                                'status' => 1,
                                'type' => 4,
                                'help_id' => $data['help_id'],
                                'des' => '车辆服务',
                        ];
                        unset($map);
                        $map['user_id'] = $value;
                        db('users')->where($map)->setInc('user_money', $points_get);
                    }
                    db('points_log')->insertAll($data3);

                }

                $total_get = $user_get + $car_get;
                $has_money = $points_public - $total_get;
                if($has_money< 0 ){
                    $this->error('积分池积分不足');
                }
                db('config')->where('name="points_public"')->setDec('value', $total_get);

                                

                db()->commit();
            }
            catch (Exception $e){
                db()->rollback();
            }
        }
    }

    //删除相册图
    public function del_help_images()
    {
        $path = input('filename','');
        db('help_images')->where("image_url = '$path'")->delete();
    }

    //自愿者列表
    public function peopleList(){
        $status = array('全部状态','已报名','待执行','已执行','已拒绝');
        if(request()->isAjax()){
            $request = input('request.');
            $sex = array('','男','女');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = '';
//            $help_id = input('help_id');
//            if ($help_id){
//                $help_id = trim($help_id, ',');
//                $where['help_id'] = ['in', $help_id];
//            }
            if($request['status']){
                $where['status'] = ['eq', $request['status'] ];
            }
            if($request['help_id']){
                $where['help_id'] = ['eq', $request['help_id'] ];
            }
            if($request['search']){
                $where['name'] = ['like', '%'.$request['search'].'%'];
            }
            $data['rows'] = db('help_people')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('help_people')->where($where)->count();

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['status'] = $status[$v['status']];
                $data['rows'][$k]['sex'] = $sex[$v['sex']];
                $data['rows'][$k]['help_name'] = db('help')->where('help_id',$v['help_id'])->value('title');
            }
            return $data;

        }
        $this->assign('status',$status);
        return view();
    }

    //志愿者(车辆)操作
    public function Handle(){
        $data = input('post.');

        if($data['act'] == 'confirm'){
            $status = 2;
        }

        if($data['act'] == 'execute'){
            $status = 3;
        }

        if($data['act'] == 'refuse'){
            $status = 4;
        }

        if ($data['type'] == 'people') {
            $r = db('help_people')->where('id',$data['id'])->setField('status',$status);
        }elseif ($data['type'] == 'car'){
            $r = db('help_car')->where('id',$data['id'])->setField('status',$status);
        }

        if($r !== false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }

    //车辆列表
    public function carList(){
        $status = array('全部状态','已报名','待执行','已执行','已拒绝');
        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = '';
            if($request['status']){
                $where['status'] = ['eq', $request['status'] ];
            }
            if($request['help_id']){
                $where['help_id'] = ['eq', $request['help_id'] ];
            }
            if($request['search']){
                $where['name'] = ['like', '%'.$request['search'].'%'];
            }

            $data['rows'] = db('help_car')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('help_car')->where($where)->count();

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['status'] = $status[$v['status']];
                $data['rows'][$k]['help_name'] = db('help')->where('help_id',$v['help_id'])->value('title');
            }
            return $data;
        }
        $this->assign('status',$status);
        return view();
    }

    //志愿者(车辆)详情
    public function detail(){
        $id = input('id');
        $type = input('type');
        if ($type == 'people'){
            $people = db('help_people')->find($id);
            $this->assign('res',$people);
            return view('peopledetail');
        }elseif ($type == 'car'){
            $car = db('help_car')->find($id);
            $this->assign('res',$car);
            return view('cardetail');
        }
    }

    //项目报告列表
    public function reportList(){
        $help_id = input('help_id');
        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $where = '';
            $where['help_id'] = $request['sort'];
            if($request['search']){
                $where['title'] = ['like', '%'.$request['search'].'%'];
            }
            $data['rows'] = db('help_report')->where($where)->limit($offset.','.$limit)->select();
            $data['total'] = db('help_report')->where($where)->count();
            $help_name = db('help')->where('help_id',$where['help_id'])->value('title');

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['help_name'] = $help_name;
            }
            return $data;
        }
        $this->assign('help_id',$help_id);
        return view();
    }

    //进展报告
    public function report(){
        $help_id = input('help_id');
        $act = input('act')?input('act'):'add';
        $info = array();
        if(input('id')){
            $id = input('id');
            $info = db('help_report')->where('id='.$id)->find();
        }
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->assign('help_id',$help_id);
        $this->initEditor();
        return $this->fetch();
    }

    //进展报告操作
    public function reportHandle(){
        $data = input('post.');
        if($data['act'] == 'add'){
            unset($data['act']);
            $data['add_time'] = time();
            $r = db('help_report')->insert($data);
        }

        if($data['act'] == 'edit'){
            unset($data['act']);
            $r = db('help_report')->update($data);
        }

        if($data['act'] == 'del'){
            $data['id'] = trim($data['id'], ',');
            $map['id'] = ['in', $data['id']];
            $r = db('help_report')->where($map)->delete();
        }

        if($r !== false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }

}