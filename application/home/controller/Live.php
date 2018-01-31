<?php
/**
 * 直播
 */
namespace app\home\controller;

class Live extends Base {

    public function liveList(){
        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where ='';
            if($request['search']){
                $where['title'] = ['like', '%'.$request['search'].'%'];
            }

            $data['rows'] = db('live')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('live')->where($where)->count();

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['title'] = getSubstr($v['title'], '0', '33');
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
            }
            return $data;

        }
        return view();
    }

    public function live(){
 		$act = input('act')?input('act'):'add';
        $info = array();
        $info['publish_time'] = time()+3600*24;
        if(input('live_id')){
           $live_id = input('live_id');
           $info = db('live')->where('live_id='.$live_id)->find();
        }
        $this->assign('act',$act);
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    public function liveHandle(){
        $data = input('post.');
        $data['publish_time'] = strtotime($data['publish_time']);
        if($data['act'] == 'add'){
            unset($data['act']);
        	$data['add_time'] = time(); 
            $r = db('live')->insert($data);
        }
        
        if($data['act'] == 'edit'){
            unset($data['act']);
            $r = db('live')->where('live_id='.$data['live_id'])->update($data);
        }
        
        if($data['act'] == 'del'){
            $data['live_id'] = trim($data['live_id'], ',');
            $map['live_id'] = ['in', $data['live_id']];
        	$r = db('live')->where($map)->delete();

        }
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('home/Live/liveList');
        if($r){
            $this->success("操作成功",$referurl);
        }else{
            $this->error("操作失败",$referurl);
        }
    }

}