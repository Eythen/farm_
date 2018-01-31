<?php
/**
 * 提现管理
 */
namespace app\home\controller;
use think\Controller;
use app\wap\model\PointsLog;
use think\Loader;

class Withdraw extends Base {

    //protected $Withdrawstatus = ['0' => '待审核', '1' => '已支付', '2' => '审核不通过', '3' => '支付不成功'];


	/**
	 * [index 提现平台]
	 * @return [type] [description]
	 */
	public function index(){
        if(request()->isAjax()){
            $request = input('request.');
            $sort = $request['sort']? $request['sort']:'id';
            $order = $request['order']? $request['order']:'desc';
            $limit = $request['limit']? $request['limit']:'10';
            $offset = $request['offset']? $request['offset']:'0';

            $map = '';//提现
            $data['total'] = db('user_withdraw')->where($map)->count();

            if($data['total']){
                $data['rows'] = db('user_withdraw')->where($map)->order($sort." ".$order)->limit($offset, $limit)->select();
                foreach ($data['rows'] as $k => $v) {
                    $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                    $data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
                    $data['rows'][$k]['user_id'] = find_username($v['user_id']);
                    $data['rows'][$k]['update_admin_id'] = get_admin_name($v['update_admin_id']);
                    $data['rows'][$k]['status'] = config('withdraw_status')[$v['status']];

                }
            }

            return $data;

            //halt($request);

        }
		
		return $this->fetch();    
	}


	/**
     * [edit 编辑]
     * @return [type] [description]
     */
    public function edit(){
        $id = input('id',0,'intval');

        $status = config('withdraw_status'); //处理状态
        $this->assign('status',$status);

        if( $id ){
            $data = db('user_withdraw')->find($id);
            $this->assign('data',$data);
        }
        if( request()->isAjax() ){
            
            $post = input('post.');
            $updateData = [
                            'status' => $post['status'],
                            'remark' => $post['remark'],
                            'update_time' => time(),
                            'update_admin_id' => session('admin_id'),
                            ];
            $updateMap['id'] = $id;               


            db()->startTrans();
            $result = db('user_withdraw')->where($updateMap)->update($updateData);
            //金币处理start
            $logMap['Withdraw_id'] = $data['id']; //金币处理ID
            if($post['status']){
                $p_result =1;
                $money_result = 1;
                if($post['status'] ==1){//打款
                    $points_data = [ 
                            'update_time' => time(),
                            'update_admin_id' => session('admin_id'),
                             'status' => 1,//成功
                            ];
                    $p_result = db('points_log')->where($logMap)->update($points_data);
                    //真实扣钱start
                    $moneyMap['user_id'] = $data['user_id'];
                    $money = db('users')->where($moneyMap)->value('rebate_money');
                    if($money> $data['user_money']){
                        $money_result = db('users')->where($moneyMap)->setDec('rebate_money', $data['user_money']);
                    }
                    else{
                        $this->error('返利币余额不足！');
                    }
                    //真实扣钱end
                }
                if($post['status'] ==3){//打款不成功 
                    $points_data = [ 
                            'update_time' => time(),
                            'update_admin_id' => session('admin_id'),
                             'status' => 2,//处理不成功
                            ];
                    db('points_log')->where($logMap)->update($points_data);
                    
                    //真实补钱start
                    $moneyMap['user_id'] = $data['user_id'];
                    $money_result = db('users')->where($moneyMap)->setInc('rebate_money', $data['user_money']);
                    //真实补钱end

                }

            }
            if($p_result && $result && $money_result){
                db()->commit();
                $this->success('提交成功');
            }
            else{
                db()->rollback();
                $this->error('提交失败，请重试！');
            }
            //金币处理end
        }
        
        return $this->fetch();
    }

    /**
     * [delete 删除]
     * @return [type] [description]
     */
    public function delete(){
        if( request()->isPost() ){

            $request = input('request.');
            $ids = $request['id'];
            $id =explode(',', $ids);
            $id = array_filter($id);


            $where['id'] = ['in', $id];
            $result = db('points_log')->where($where)->delete();



            if( $result ){
                $this->success(config('MSG.DELETE_SUCCESS'));
            }else{
                $this->success(config('MSG.DELETE_ERROR'));
            }
        }
    }


   
}
?>