<?php
namespace app\home\controller;
use think\Controller;

class ToPrint extends Base {
	// private $user;
	// private $group;
	// private $rule;

	// public function _initialize() {
	// 	parent::_initialize(); //权限判断
	// 	$uid = session('user_id');
	// 	// 用户详情
	// 	$this->user = D('Home/User')->getInfo($uid);
	// 	// 小组成员列表
	// 	$this->group = D('Home/User')->getGroup($this->user['supervise_dept_id']);
	// 	// 规则名称
	// 	//$this->rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
	// }

	/**
	 * [print_table 打印]
	 */
	public function print_table() {
		$id = input('id');
		$temp = input('temp');
		if (empty($id)) {
			echo '非法操！';
			exit;
		}
		if($temp=="history"){
			$table="fawu_history";
			$temp="print_history";
		}
		elseif($temp=="feedback"){
			$table="feedback";
			$temp="print_feedback";
		}
		elseif($temp=="complaint"){
			$table="complaint";
			$temp="print_complaint";
		}
		elseif($temp=="reply"){
			$table="replylog";
			$temp="print_reply";
		}
		else{
			$table="record";
		}
		$where['id'] = array('IN', $id);
		$data = db($table)->where($where)->select();
		if($temp=="print_feedback" || $temp=="print_complaint"){
			foreach ($data as $k => $v) {
				$data[$k]['num'] = count(explode(";", $v['file']));

				// $sendee_arr = array();
				// $sendeeIds = explode(',', $v['sendee']);
				// foreach($sendeeIds as $sendeeId) {
				// 	$user = D('Home/User')->getInfo($sendeeId);										//后补
				// 	$sendee_arr[] = $user['username'];
				// }
				// $data[$k]['sendee'] = implode(',', $sendee_arr);

				// $leader_sendee_arr = array();
				// $leader_sendeeIds = explode(',', $v['leader_sendee']);
				// foreach($leader_sendeeIds as $leader_sendeeId) {
				// 	$user = D('Home/User')->getInfo($leader_sendeeId);								//后补
				// 	$leader_sendee_arr[] = $user['username'];
				// }
				// $data[$k]['leader_sendee'] = implode(',', $leader_sendee_arr);
				$data[$k]['reply'] = unserialize($v['reply']);
			}
		}
		if($temp=="print_reply"){
			foreach ($data as $k => $v) {
				$tid = model('Record')->getInfo($v['id']);
				$data[$k]['customer_name'] = $tid['name'];
				// 回访记录
				$where = array('customer_id' => $v['id']);
				//查招商的回访
				if($tid['member_id']>0){
					$whereor['member_id']=array('eq',$tid['member_id']) ;
					//$where['_logic'] = 'OR';
				}
				$data[$k]['log']=db('replylog')->where($where)->whereor($whereor)->order('time asc')->select();			//后补
			}
		}
		$this->assign('data', $data);
		return $this->fetch($temp);
	}
}