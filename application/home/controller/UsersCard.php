<?php
/**
 * 用户认证
 */
namespace app\home\controller;

class UsersCard extends Base {

    public function cardList(){
        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $status = array('待审核','已审核');
            $where ='';
            if($request['search']){
                $where['title'] = ['like', '%'.$request['search'].'%'];
            }

            $data['rows'] = db('user_card')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('user_card')->where($where)->count();

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['user_name'] = db('users_config')->where('user_id',$v['user_id'])->value('user_name');
                $data['rows'][$k]['status'] = $status[$v['status']];
                $data['rows'][$k]['check_time'] = formatTime($v['check_time'], 'Y-m-d H:i:s');
            }
            return $data;
        }
        return view();
    }

    public function detail(){
        $card_id = (int)input('card_id',20);
        $card = db('user_card')->where("card_id=$card_id")->find();
        $this->assign('card',$card);
        return $this->fetch();
    }

    public function cardHandle(){
        $card_id = input('card_id');
        $data = array(
            'card_id'=>$card_id,
            'status'=>'1',
            'check_user'=>session('uname'),
            'check_time'=>time(),
        );
        $res = db('user_card')->update($data);
        if($res){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }

}