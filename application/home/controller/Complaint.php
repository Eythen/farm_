<?php
namespace app\home\controller;
use think\Controller;
class Complaint extends Base {
	/**
	 * [index 投诉处理表]
	 */
	public function index() {
		if (request()->isAjax()) {
			// 模糊搜索
			$request = input('request.');
			$sort = $request['sort'] ? $request['sort'] : 'add_time';
			$order = $request['order'] ? $request['order'] : 'desc';
			$offset = $request['offset'] ? $request['offset'] : 0;
			$limit = $request['limit'] ? $request['limit'] : 10;
			$type = $request['type'] ? $request['type'] : '';
			if (!empty($request['search'])) {
				$where['user_name'] = array('LIKE', '%' . $request['search'] . '%');
				$where['customer'] = array('LIKE', '%' . $request['search'] . '%');
				$where['shop_rank'] = array('LIKE', '%' . $request['search'] . '%');
				$where['complaint_name'] = array('LIKE', '%' . $request['search'] . '%');
				$where['tel'] = array('LIKE', '%' . $request['search'] . '%');
				// if (strtotime($request['search'])) {
				// 	$where['add_time'] = array('LIKE', '%' . $request['search'] . '%');
				// }
				$where['_logic']="or";
			}
			$map['is_delete'] = array('eq',0);
			if ($request['status'] != '') {
				switch ($request['status']) {
					case '0':
						$map['R.status'] = array('eq',0);
						break;
					case '1':
						$map['R.status'] = array('eq',100);
						break;
					case '2':
						$map['R.status'][] = array('neq',0);
						$map['R.status'][] = array('neq',100);
						break;
				}
			}
	        //超级管理员组权限
	        /*if ($_SESSION['gid'] != config('CORP.ADMIN')) {
		        //投资权限
		        if ($_SESSION['dept_id'] == config('CORP.DEPT_ID')['TOUZI']) {
		            if ($_SESSION['position'] != '经理') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //运营权限
		        } elseif($_SESSION['dept_id'] == config('CORP.DEPT_ID')['YUNYING']){
		            if ($_SESSION['position'] != '总监助理') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //客服权限
		        } elseif($_SESSION['dept_id'] == config('CORP.DEPT_ID')['KEFU']){
		            if ($_SESSION['position'] != '客服经理') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //IT权限
		        } elseif($_SESSION['dept_id'] == config('CORP.DEPT_ID')['IT']){
					$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		        //总裁办权限
		        } elseif($_SESSION['dept_id'] == config('CORP.DEPT_ID')['ZONGCAI']){
		            if ($_SESSION['uname'] == '周彦波') {
		                $depid = config('CORP.DEPT_ID')['IT'];
		            } elseif ($_SESSION['uname'] == '徐宁') {
		                $depid = config('CORP.DEPT_ID')['TOUZI'];
		            } elseif ($_SESSION['uname'] == '钟智敏') {
		                $depid = config('CORP.DEPT_ID')['YUNYING'];
		            } else {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            }
		        //物流权限
		        } elseif ($_SESSION['dept_id'] == config('CORP.DEPT_ID')['WULIU']) {
		            if ($_SESSION['position'] != '仓库主管') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //行政权限
		        } elseif ($_SESSION['dept_id'] == config('CORP.DEPT_ID')['XINGZHENG']) {
		            if ($_SESSION['position'] != '行政部经理') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //生产研发中心权限
		        } elseif ($_SESSION['dept_id'] == config('CORP.DEPT_ID')['YANFA']) {
		            if ($_SESSION['position'] != '生产研发中心经理') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //品牌管理中心权限
		        } elseif ($_SESSION['dept_id'] == config('CORP.DEPT_ID')['PINPAI']) {
		            if ($_SESSION['position'] != '品牌管理中心经理') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //人力资源中心权限
		        } elseif ($_SESSION['dept_id'] == config('CORP.DEPT_ID')['HR']) {
		            if ($_SESSION['position'] != '人力总监') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //财务管理中心权限
		        } elseif ($_SESSION['dept_id'] == config('CORP.DEPT_ID')['CAIWU']) {
		            if ($_SESSION['position'] != '财务总监') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        //O2O营销中心权限
		        } elseif ($_SESSION['dept_id'] == config('CORP.DEPT_ID')['O2O']) {
		            if ($_SESSION['position'] != 'o2o经理') {
						$map['I.user_id|R.user_id'] = array('eq',$_SESSION['uid']);
		            } else{
		                $depid = $_SESSION['position_id'];
		            }
		        }
		        if (isset($depid)) {
		            $list = model('Users')->getDep($depid);
					$user = array();
					foreach ($list as $k => $v) {
						$user[] = $v['userid'];
					}
					$user[] = $_SESSION['uid'];
					$map['I.user_id|R.user_id'] = array('in',$user);
		        }
		    }*/
			if ($type) {
				$map['I.status'] = array('eq',0);
			}
			if($where){
				$map1 = array($where,$map);
			}else{
				$map1 = $map;
			}
			if ($type) {
				// $field = array('id','add_time', 'status', 'customer', 'user_name','complaint_name');
				// $data = model('Complaint')->getList($field,$offset, $limit, $sort, $order, $map1);
			}
			//组合显示
			$field = array('*','R.user_id as add_user_id','I.user_id as log_user_id','R.add_time as add_time', 'R.status as status', 'customer', 'user_name','complaint_name','I.id as log_id');
			$sort = 'R.'.$sort;
			$data = model('Complaint')->getAdvList($field,$offset, $limit, $sort, $order, $map1);
			return $data;
		}
		$this->assign('title', $title);
		return $this->fetch();
	}

	/**
	 * [findCustomer 查找客户档案 ]
	 */
	public function findCustomer() {
		if (request()->isAjax()) {
			$request = input('request.');
			//检测客户名字正确性
			if(empty($request['customer_name'])){
				return;
			}
			$where['name']= array('eq' , $request['customer_name'] );
			$field = 'shop_rank,tel,province,city,store_address,contract_sn';
			$z = model('record')->findRcord($where,$field);
			if (!$z) {
				$this->error(config('MSG.CUSTOMER_TIPS'));
			}
			$data = array();
			$data['store_address'] = $z['store_address'];
			$data['contract_sn'] = $z['contract_sn'];
			$data['province'] = $z['province'];
			$data['shop_rank'] = $z['shop_rank'];
			$data['city'] = $z['city'];
			$data['tel'] = $z['tel'];
			$this->success($data);
		}
	}

	/**
	 * [add 添加投诉表]
	 */
	public function add() {
		if (request()->isAjax()) {
			$request = input('request.');
			if (!$request['sendees']) {
				$this->error(config('MSG.SENDEE_ERROR'));
			}
			if (!$request['complaint_userid']) {
				$this->error(config('MSG.COMPLAINT_ERROR'));
			}
			if(!empty($request['file'])){
				$request['file'] = str_replace(";;", ";", $request['file']);
				$request['file'] = trim($request['file'],";");
			}
			$cUserId = $request['complaint_userid'];
			$userInfo = model('Users')->getInfo($cUserId);
			$data = array(
				'user_id' => session('uid'),
				'user_name' => session('uname'),
				'dep_id' => session('did'),
				'customer' => $request['customer'],
				'address' => $request['address'],
				'tel' => $request['tel'],
				'province' => $request['province'],
				'level' => $request['level'],
				'sendee' => $request['sendees'],
				'complaint_time' => $request['complaint_time'],
				'complaint_userid' => $request['complaint_userid'],
				'complaint_name' => $userInfo['username'],
				'shop_rank' => $request['shop_rank'],
				'contract_sn' => $request['contract_sn'],
				'content' => $request['content'],
				'file' => $request['file'],
				'add_time' => get_date(),
				'status' => 0,
			);
			$sendee = explode(',',$request['sendees']);
			$add = model('Complaint')->addComplaint($data);
			if (!$add) {
				$this->error(config('MSG.ADD_ERROR'));
			} else {
				if ($sendee[0]) {
					//第一接收人发送提示
					$update = array();
					$update['complaint_id'] = $add;
					$update['user_id'] = $sendee[0];
					$update['add_time'] = date('Y-m-d H:i:s');
					$update['status'] = 0;
					model('Complaint')->addLog($update);
				}
				$this->success(config('MSG.ADD_SUCCESS'));
			}
		}

		// // 接收人列表(可选择 法务 招商 运营的人)
		// $yunyings = model('Home/User')->getGroup(14);
		// $zhaoshangs = model('Home/User')->getGroup(15);
		// $fawus = model('Home/User')->getGroup(9);
		// $sendees = array_merge($yunyings, $zhaoshangs, $fawus);
		// $sendees = model('Department')->getUserList(1,1);
		// // $sendees = model('Department')->getInfo(1);
		// print_r($sendees);
		// $sendees = S('all_user');
		$sendees = model('Users')->getAll();	//获取全部用户
		$data['mode'] = 1;
		$data['add']  = 1;
		$data['user_name'] = session('uname');
		$data['add_time'] = get_date();
		$this->assign('sendees', $sendees);
		$this->assign('data',$data);
		return $this->fetch();
	}

	/**
	 * [updateComplaint 修改投诉处理表]
	 */
	public function updateComplaint() {
		$id = input('id', 0, 'intval');
		$user_id = session('uid');
		if (request()->isAjax()) {
			$request = input('request.');
			$map = array();
			$map['id'] = array('eq', $id);
			$getData = model('Complaint')->getOne($map);
			//制表人可以修改内容
			// if ($user_id == $data['user_id']) {
			// 	if(!empty($request['file'])){
			// 		$request['file'] = str_replace(";;", ";", $request['files']);
			// 		$request['file'] = trim($request['files'],";");
			// 	}
			// 	$data['customer'] = $request['customer'];
			// 	$data['address'] = $request['address'];
			// 	$data['tel'] = $request['tel'];
			// 	$data['feedback_name'] = $request['feedback_name'];
			// 	$data['zhaoshang_manager'] = $request['zhaoshang_manager' ];
			// 	$data['quyu_manager'] = $request['quyu_manager'];
			// 	$data['yunying_manager'] = $request['yunying_manager'];
			// 	$data['sendee'] = $request['sendees'];
			// 	$data['content'] = $request['content'];
			// 	$data['file'] = $request['file'];
			// }
			$sendee = explode(',',$getData['sendee']);
			if ($user_id == $sendee[$getData['status']] ){	//接收人可以修改附件
				$data['file'] = $request['file'];
			}
			//处理回复内容
			if($request['reply']){
				$oldReply = unserialize($getData['reply']);
				$reply['userid'] = $user_id;
				$reply['time'] = date('Y-m-d H:i:s');
				$reply['content'] = $request['reply'];
				$userInfo = model('Users')->getInfo($user_id);
				$reply['username'] = $userInfo['username'];
				$reply['framework_name'] = $userInfo['framework_name'];
				$reply['position'] = $userInfo['position'];

				$oldReply[] = $reply;
				$data['reply'] = serialize($oldReply);
				$data['status'] = 0;
				$sendeeKey = 0;
				foreach ($sendee as $key => $val) {
					foreach ($oldReply as $k => $v) {
						if ($val == $v['userid']) {
							$sendeeKey = $key;
							$data['status'] = $key + 1;					//更新投诉状态
						}
					}
				}
				if (in_array($user_id,$sendee) && $sendeeKey == $getData['status']) {
					if($data['status'] == count($sendee)){
						//更改当前接收人状态
						model('Complaint')->updateLog($id,$sendee[$data['status']-1]);
						$data['status'] = 100;								//投诉完毕
					}else{
						if ($data['status']>0) {
							$callNext = $sendee[$data['status']];
							//更改当前接收人状态
							model('Complaint')->updateLog($id,$sendee[$data['status']-1]);
							//通知下一接收人
							$update = array();
							$update['complaint_id'] = $id;
							$update['user_id'] = $callNext;
							$update['add_time'] = get_date();
							$update['status'] = 0;
							model('Complaint')->addLog($update);
						}
					}
				}else{
					$this->error('回复失败,暂未到你回复');
				}
			}
			$add = model('Complaint')->updateComplaint($data, $id);
			if (!$add) {
				$this->error(config('MSG.SUBMIT_ERROR'));
			} else {
				$this->success(config('MSG.SUBMIT_SUCCESS'));
			}
		}
		$map['id'] = array('eq',$id);
		$data=model('Complaint')->getOne($map);
		$files=explode(';', $data['file']);
		foreach ($files as $key => $v) {
			if ($v) {
				$file = explode('|',$v);
				if (file_exists(iconv('UTF-8', 'GB2312', '.'.$file[0]))) {
					$data['fileshow'][$key]['path'] = $file[0];
					$data['fileshow'][$key]['name'] = $file[1];
					$data['fileshow'][$key]['pic'] = check_ext($file[0]);
				}
			}
		}
		$data['mode'] = 0;
		$data['add']  = 0;
		$sendee = explode(',',$data['sendee']);
		if ($user_id == $sendee[$data['status']] ){
			$data['add'] = 1;					//接收人有回复权限
		}
		if ($data['reply']) {
			$data['reply'] = unserialize($data['reply']);
			foreach ($data['reply'] as $k => $v) {
				$userInfo = model('Users')->getInfo($v['userid']);
				$data['reply'][$k]['username'] = $userInfo['username'];
				$data['reply'][$k]['framework_name'] = $userInfo['framework_name'];
				$data['reply'][$k]['position'] = $userInfo['position'];
			}

		}
		if ($user_id == $data['user_id']) {
			$data['mode'] = 1;					//制单人有修改权限
		}
		$sendees = model('Users')->getAll();		//获取全部用户
		$this->assign('data', $data);
		$this->assign('sendees', $sendees);
		$this->assign('sendee', $sendee);
		return $this->fetch('add');
	}

	/**
	 * [deleteComplaint 删除投诉表]
	 */
	public function deleteComplaint() {
		if (request()->isAjax()) {
			$id = input('id');
			$where['id'] = array('IN', $id);
			$delete = model('Complaint')->deleteComplaint($where);
			if ($delete) {
				$this->success(config('MSG.DELETE_SUCCESS'));
			} else {
				$this->error(config('MSG.DELETE_ERROR'));
			}
		}
	}
}