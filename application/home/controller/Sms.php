<?php
/**
 * Description 			:	短信控制模块
 * CreateDate 			:	2016/06/11
 * Creater 				:	yangqing
 * LastChangeDate 		:	2016/06/11
 * LastChanger 			:	yangqing
*/
namespace app\home\controller;
use think\Controller;

class Sms extends Base {

	/**
	 * [addSms 创建短信]
	 */
	public function addSms() {
		$ids = input('id');
		//获取客户手机号码
		if($ids){
			$ids = rtrim($ids, ',');
			$where['id'] = array('in', $ids);
			$cr = model('Customer')->getWhere($where);
			$tel ='';
			foreach ($cr as $k => $v) {
				$tel .= $v['tel'].",";
			}
			$data['tel'] = rtrim($tel, ',');
		}

		$top_dept = model('Department')->getTop(session('dept_id'));
		$top_dept_id = $top_dept['id'];
		//判断不是客服中心 要填审核人
		if(session('dept_id')!=44){
			if (session('dept_id') != 2) {
				$data['model'] = 1;
				$status = 2;//待审核
			}
		}
		else{
			$status = 1;//发送
		}
		if(request()->isPost()){
			$request = input('request.');
			//处理中文逗号成为英文逗号 
			$tel = str_replace('，', ',', $request['connect']);
			if($request['send_time']){
				$send_time = date("Y-m-d H:i:s", strtotime($request['send_time']));
			}
			else{
				$send_time = date("Y-m-d H:i:s", strtotime('+3 seconds'));
			}
			$content = $request['content'].config('sms.sign');

			$data = array(
				'add_time' => date("Y-m-d H:i:s",time()),
				'user_name' => session('uname'),
				'user_id' => session('uid'),
				'dept_id' => session('dept_id'),
				'department' => session('dept_name'),
				'tel' => $tel,
				'supervise_id' => $request['supervise_id'],
				'content' => $request['content'].config('sms.sign'),
				'status' => $status,
				'send_time' => $send_time,
				'type' => 1,
				);

			$data = array_filter($data);
			$result = model('Sms')->addSmsIndex($data);
			if($result){
				//判断是否直接发送
				if($status==1){
					$send_data[] = array(
						'id' => $result,
						'user_name' => session('uname'),
						'user_id' => session('uid'),
						'dept_id' => session('dept_id'),
						'department' => session('dept_name'),
						'tel' => $tel,
						'content' => $request['content'].config('sms.sign'),
						'send_time' => $send_time,
						'type' => 1,
						);
					$send_data = array_filter($send_data);
					dump($send_data);
					$send = model('Sms')->sendApi($send_data);
				}
				$this->success('创建短信成功！');
			}
			else{
				$this->error('创建短信失败！');
			}
			

		}

		
		$sms_config = model('Sms')->smsConfigList(session('uid'));//获取个人短信模板
		$this->assign('sms_config', $sms_config);
		
		$data['sign'] =config('sms.sign');//短信签名
		$ur = model('Users')->getAll();	//获取全部用户

		$position_id = session('position_id');

		$possion_rule = config('sms_check');	//获取审核人职位
		$position = session('position');

		$users = $ur;
		/*$users = array_column($ur, 'username', 'uid');
		dump($users);
		die;*/

		/*$users = array();

		foreach ($ur as $k => $v) {
			//if(in_array($v['position'],$possion_rule) ){
			if(in_array(substr($v['position'], -6),$possion_rule) && ($position_id ==$v['framework_id']) ){
				$u['userid'] = $v['userid'];
				$u['username'] = $v['username']."-".$v['position'];
				array_push($users, $u);
			}
		}*/
		$data['user_name'] = session('uname');
		$this->assign('users', $users);
		$this->assign('data',$data);
		return $this->fetch();
	}

	/**
	 * [index 短信创建记录]
	 */
	public function index() {
		//更新短信发送状态与添加短信回复
		//$this->smsOutStatus();

		if (request()->isPost()) {
			// 模糊搜索
			
			$request = input('request.');
			$sort = $request['sort'] ? $request['sort'] : 'id';
			$order = $request['order'] ? $request['order'] : 'desc';
			$offset = $request['offset'] ? $request['offset'] : 0;
			$limit = $request['limit'] ? $request['limit'] : 10;
			//权限设置 IT开发组有权限
			/*if(session('position_id') != 33){
				$uid = session('uid');
				$where1['user_id'] = $uid;
				$ur = model('Users')->getAll();
				foreach ($ur as $k => $v) {
					if($v['parent_id'] == 1 && $uid == $v['userid']){
						unset($where1['user_id']);
					}
				}
			}*/
			if (!empty($request['page_date'])) {
				$day = explode(' - ', $request['page_date']);
				sort($day);
				list($start_time,$end_time) = $day;
				$where1['add_time'] = array('between',array($start_time,date("Y-m-d", strtotime("+24 hours",strtotime($end_time)))) );
			}
			if (!empty($request['status'])) {
				$where1['status'] = array('eq', $request['status']);
			}
			if (!empty($request['search'])) {
				$where1['tel'] = array('LIKE', '%' . $request['search'] . '%');
				$where1['user_name'] = array('LIKE', '%' . $request['search'] . '%');
				if (strtotime($request['search'])) {
					$where1['add_time'] = array('LIKE', '%' . $request['search'] . '%');
				}
				$where1['_logic']="or";
			}			
			
			//设置各部门看到的权限
			//$where1['dpm_id']= array(array('eq',9),array('exp',"IS NULL"),'or');
			//设置IT中心看到的权限
			if($user['dpm_id']==5){
				//unset($where1['dpm_id']);
			}

			$data = model('Sms')->smsList($where1, $sort, $order, $offset, $limit);
			return $data;
			//return $data;
		}

		$this->assign('title', $title);
		return $this->fetch();
	}

	/**
	 * [indexStatus 短信状态]
	 */
	public function indexStatus() {
		// auth权限验证
		/*if (!$this->_auth()) {
			echo '您无权进行此项操作！';
			exit;
		}*/
		//更新短信发送状态与添加短信回复
		$this->smsOutStatus();

		if (request()->isPost()) {
			// 模糊搜索
			
			$request = input('request.');
			$sort = $request['sort'] ? $request['sort'] : 'id';
			$order = $request['order'] ? $request['order'] : 'desc';
			$offset = $request['offset'] ? $request['offset'] : 0;
			$limit = $request['limit'] ? $request['limit'] : 10;
			//权限设置 IT开发组有权限
			/*if(session('position_id') != 33){
				$uid = session('uid');
				$where1['userid'] = $uid;
				//设置总监权限
				if(session('dept_id') == 1){
					$ur = model('Users')->getAll();
					foreach ($ur as $k => $v) {
						if($v['parent_id'] == 1 && $uid == $v['userid']){
							unset($where1['_string']);
						}
					}
				}
			}*/
			if (!empty($request['status'])) {
				if($request['status'] ==3){
					$where1['status'] = array('exp', 'is null');
				}
				else{
					$where1['status'] = array('eq', $request['status']);
				}
			}
			if (!empty($request['page_date'])) {
				$day = explode(' - ', $request['page_date']);
				sort($day);
				list($start_time,$end_time) = $day;
				$where1['add_time'] = array('between',array($start_time,date("Y-m-d", strtotime("+24 hours",strtotime($end_time)))) );
			}
			if (!empty($request['search'])) {
				$where1['tel'] = array('LIKE', '%' . $request['search'] . '%');
				$where1['user_name'] = array('LIKE', '%' . $request['search'] . '%');
				if (strtotime($request['search'])) {
					$where1['add_time'] = array('LIKE', '%' . $request['search'] . '%');
				}
				$where1['_logic']="or";
			}			
			

			$data = model('Sms')->smsStatusList($where1, $sort, $order, $offset, $limit);
			return $data;
		}

		$this->assign('title', $title);
		return $this->fetch();
	}

	/**
	 * [smsInfoAll 查看短信详情]
	 */
	public function smsInfoAll() {
		// auth权限验证
		/*if (!$this->_auth()) {
			echo '您无权进行此项操作！';
			exit;
		}*/

		$id = input('id','','intval');
		if($id){
			$map['id'] = array('eq',$id);
			$dr = model('Sms')->getSmsOne($id);
			$data['error'] = '';
			if($dr['task_id']){
				$data = model('sms')->getSmsTask($dr['task_id']);
				foreach ($data as $k => $v) {
					if($v['status'] ==1){
						if($v['type'] ==2){
							$status ="客户回复";
						}
						else{
							$status ="发送成功";
						}
					}
					else{
						$status ="等待发送状态更新，或者发送失败";
					}
					$data[$k]['status_process'] = $status;
				}
			}
			else{
				$data['error'] = "短信未审核，或者出错了，可以联系管理员查询具体原因！";
			}
		}
		$this->assign('data', $data);
		return $this->fetch();
	}

	/**
	 * [smsInfo 查看短信记录]
	 */
	public function smsInfo() {
		$id = input('id','','intval');
		if($id){
			$data = model('Sms')->getSmsOne($id);
			$status = $data['status'];
			$uid = $data['user_id'];
			if($status == 3 && 42 == session('uid')){
				$this->updataSms($data['id']);
				die;
			}
			$this->assign('data', $data);
		}
		return $this->fetch();
	}

	/**
	 * [updataSms 审核不通过编辑后再提交短信]
	 * @param  [int] $id [sms_index表id]
	 */
	public function updataSms($id) {
		
		$id = (int)$id;
		$data = model('Sms')->getSmsOne($id);
		if($data['content']){
			$data['content'] = rtrim($data['content'], config('sms.sign'));
		}
		$top_dept = model('Department')->getTop(session('dept_id'));
		$top_dept_id = $top_dept['id'];
		//判断不是客服中心 要填审核人
		if(session('dept_id')!=44){
			$data['model'] = 1; //根据这个参数设置审核人是否必填
			$status = 2;//待审核
		}
		else{
			$status = 1;//发送
		}
		if(request()->isPost()){
			$request = input('request.');
			$id2 = (int)$request['id'];
			//处理中文逗号成为英文逗号 
			$tel = str_replace('，', ',', $request['connect']);
			if($request['send_time']){
				$send_time = date("Y-m-d H:i:s", strtotime($request['send_time']));
			}
			else{
				$send_time = date("Y-m-d H:i:s", strtotime('+3 seconds'));
			}
			$content = $request['content'].config('sms.sign');

			$data2 = array(
				'tel' => $tel,
				'supervise_id' => $request['supervise_id'],
				'content' => $request['content'].config('sms.sign'),
				'status' => $request['status'],
				'send_time' => $send_time,
				);

			$data2 = array_filter($data2);
			dump($data2);
			dump($id2);
			$result = model('Sms')->saveSmsIndex($id2,$data2);
			
			if($request !== false){
				$this->success('提交成功！');
			}
			else{
				$this->error('更新失败！');
			}

		}

		//获取个人短信模板
		$sms_config = model('Sms')->smsConfigList(session('uid'));
		$this->assign('sms_config', $sms_config);
		//短信签名
		$data['sign'] =config('sms.sign');
		$ur = model('Users')->getAll();	//获取全部用户

		$position_id = session('position_id');

		$possion_rule = config('sms_check');	//获取审核人职位
		$position = session('position');


		$users = array();

		foreach ($ur as $k => $v) {
			if(in_array(substr($v['position'], -6),$possion_rule)){
				$u['userid'] = $v['userid'];
				$u['username'] = $v['username']."-".$v['position'];
				array_push($users, $u);
			}
		}
	
		$this->assign('users', $users);
		$this->assign('data',$data);
		return $this->fetch('addSms');
	}

	/**
	 * [smsStatusMore 查看单个号码短信上下详情]
	 */
	public function smsStatusMore() {
		// auth权限验证
		/*if (!$this->_auth()) {
			echo '您无权进行此项操作！';
			exit;
		}*/

		$id = input('id','','intval');
		if($id){
			$map['id'] = array('eq',$id);
			$dr = model('Sms')->getStatusOne($id);
			$data['error'] = '';
			if($dr['task_id']){
				$data = model('sms')->getStatusTask($dr['task_id'],$dr['tel']);
				foreach ($data as $k => $v) {
					if($v['status'] ==1){
						if($v['type'] ==2){
							$status ="客户回复";
						}
						else{
							$status ="发送成功";
						}
					}
					else{
						$status ="状态未更新，或者发送失败";
					}
					$data[$k]['status_process'] = $status;
				}
			}
			else{
				$data['error'] = "短信未审核，或者出错了，可以联系管理员查询具体原因！";
			}
		}
		$this->assign('data', $data);
		return $this->fetch('smsInfoAll');
	}

	/**
	 * [smsStatusInfo 查看真实发送短信记录]
	 */
	public function smsStatusInfo() {
		$id = input('id','','intval');
		if($id){
			$data = model('Sms')->getStatusOne($id);
			$this->assign('data', $data);
		}
		return $this->fetch('smsInfo');
	}

	/**
	 * [setSms 设置短信模板]
	 */
	public function setSms() {
		
		if(request()->isPost()){
			$request = input('request.');

			$data = array(
				'uid' => session('uid'),
				'uname' => session('uname'),
				'dept_id' => session('dept_id'),
				'sms' => $request['sms'],
				'add_time' => date("Y-m-d H:i:s",time()),
				);

			$result = model('Sms')->setSms($data);
			if($result){
				$this->success('设置成功！');
			}
			else{
				$this->success('设置失败！');
			}
		}
		return $this->fetch();
	}

	/**
	 * [smsCheck 审核短信]
	 */
	public function smsCheck() {
		
		if(request()->isPost()){
			$request = input('request.');
			$data = array(
				'status' =>  $request['status'],
				'check_time' => date("Y-m-d H:i:s",time()),
				);

			$result = model('Sms')->updateSmsIndex($request['id'],$data);
			if($result !== flase){
				if($request['status'] ==1){
					$ar = model('Sms')->getSmsOne($request['id']);
					$dr[] = array(
						'id' => $ar['id'],
						'tel' => $ar['tel'],
						'content' => $ar['content'],
						'send_time' => $ar['send_time'],
						'user_id' => $ar['user_id'],
						'user_name' => $ar['user_name'],
						'dept_id' => $ar['dept_id'],
						'department' => $ar['department'],
						'type' => $ar['type'],
						);

					model('Sms')->sendApi($dr);
				}
				$this->success('审核提交成功！');
			}
			else{
				$this->success('审核失败，请重新审核！');
			}
		}
		return $this->fetch();
	}

	/**
	 * [smsOutStatus 获取短信发送状态与回复]
	 */
	public function smsOutStatus(){
		$get_back = model('sms')->getBack();
		$get_send = model('sms')->getSend();
	}

	
}