<?php
/**
 * 任务模型
 */
namespace app\home\model;
use think\Model;

class Task extends Model {
	/**
	 * [listTask 获取分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function Listtask($field, $offset = 0, $limit = 10, $sort = 'id', $order = 'id DESC', $where) {
		$data['total'] = db('task')->where($where)->count();
		//echo db('task')->_sql();
		if( $data['total'] ){
			$data['rows'] = db('task')->field($field)->where($where)->order($order)->limit($offset, $limit)->select();
			//echo db('task')->_sql();
			$users = model('Users')->getAll();	//获取全部用户
			foreach ($users as $i => $u) {
				$ur[$u['userid']] = $u['username'];
			}
			//dump($ur);
			foreach ($data['rows'] as $key => $value) {
				if (empty($value['customer_name'])) {
					$data['rows'][$key]['customer_name'] = '无';
				}
				if ($value['start_time']) {
					$data['rows'][$key]['start_time'] = date('Y-m-d', strtotime($value['start_time']));
				}
				if ($value['end_time']) {
					$data['rows'][$key]['end_time'] = date('Y-m-d', strtotime($value['end_time']));
				}
				//审核人
				if ($value['supervise_id']) {
					$data['rows'][$key]['supervise_id'] = $ur[$value['supervise_id']];
				}
				//负责人
				if ($value['charge_id']) {
					$data['rows'][$key]['charge_id'] = $ur[$value['charge_id']];
				}
				//取节点信息
				$map = array();
				$map['task_id'] = $value['id'];
				$map['status'] = 0;
				$nodes = db('task_node')->where($map)->order('sort asc')->limit(1)->find();
				if($nodes){
				
					$data['rows'][$key]['nodetime'] = date("Y-m-d", $value['end_time']);
					
					//设置正在做的节点
					$status = "";
						
					//task表中status   0为已完成，1为接收任务 ，2为待接收，3为待审核 ，4为审核不通过
					 
					switch ($value['status']) {
						case '1':
							$status = "进行中";
							break;
						
						case '2':
							$status = "待接受";
							break;
						
						case '3':
							$status = "待审核";
							break;
						
						case '4':
							$status = "审核不通过";
							break;
						
						default:
							# code...
							break;
					}
					if(empty($status)){
						$status =  "已完成";
					}

					$sort = (int)$nodes['sort'];
					$sort = $sort+1;
					$data['rows'][$key]['node'] = $sort.'('.$status.')';
					$data['rows'][$key]['nodetitle'] = $nodes['title'];
					$data['rows'][$key]['nodetime'] = date("Y-m-d", $value['end_time']);
				}
				else{
					//设置任务完成显示
					if($value['status'] ==0){
						$data['rows'][$key]['nodetitle'] = "监督人已确认完成";
					}
					else{
						$data['rows'][$key]['nodetitle'] = "请监督人确认任务完成";
					}
					$data['rows'][$key]['node'] = '全部完成';
					$data['rows'][$key]['nodetime'] = date("Y-m-d", $value['end_time']);
				}
			}
			$status = db('task')->where($where)->field('status,count(*) as count')->order('count DESC')->group('status')->select();	//统计各级别店铺数量
			if ($status) {
				//task表中status   0为已完成，1为接收任务 ，2为待接收，3为待审核 ，4为审核不通过
				foreach ($status as $k => $v) {
					if($status[$k]['status'] ==0){
						$status[$k]['status'] = "已完成";
					}
					elseif($status[$k]['status'] ==2){
						$status[$k]['status'] = "待接受";
					}
					elseif($status[$k]['status'] ==3){
						$status[$k]['status'] = "待审核";
					}
					elseif($status[$k]['status'] ==4){
						$status[$k]['status'] = "审核不通过";
					}
					else{
						$status[$k]['status'] = "进行中";
					}
				}
				$data['status_total'] = $status;
			}
		}else{
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [getTask 查询任务]
	 */
	public function getTask($where) {
		return db('task')->where($where)->select();
	}
	
	/**
	 * [getTask 查询任务数]
	 */
	public function getCount($where) {
		return db('task')->where($where)->count();
	}

	/**
	 * [findTask 获取单个任务]
	 */
	public function findTask($where) {
		return db('task')->where($where)->find();
	}

	/**
	 * [getInfo 根据ID获取单个任务]
	 */
	public function getInfo($id) {
		$id = (int)$id;
		$data = db('task')->find($id);
		$data['nodes'] = db('task_node')->where('task_id='.$id)->select();
		return $data;
	}

	/**
	 * [getTaskOne 根据ID获取单个任务基本信息]
	 * @param [int] $id [ID数据]
	 */
	public function getTaskOne($id) {
		$map['id'] = (int)$id;
		$data = db('task')->where($map)->find();
		return $data;
	}

	/**
	 * [getTaskTemp 根据ID获取单个任务与临时节点]
	 */
	public function getTaskTemp($id) {
		$id = (int)$id;
		$data = db('task')->find($id);
		$data['nodes'] = db('task_node_temp')->where('task_id='.$id)->order('sort')->select();
		return $data;
	}

	/**
	 * [getSign 根据ID获取单个任务签到]
	 */
	public function getSign($id) {
		$id = (int)$id;
		$data['total'] = db('task_sign')->where('task_id='.$id)->count();
		if($data['total']){
			$data['rows'] = db('task_sign')->where('task_id='.$id)->select();
			return $data;
		}
	}

	/**
	 * [addTask 添加任务]
	 * @param [type] $data [添加数据]
	 */
	public function addTask($data) {
		return db('task')->insert($data);
	}

	/**
	 * [addNode 添加节点]
	 * @param [type] $data [添加数据]
	 */
	public function addNode($data) {
		$result = db('task_node_temp')->insert($data);
		return $result;
	}

	/**
	 * [updateNodesTemp 排序节点]
	 * @param [type] $data [需排序的数据]
	 */
	public function updateNodesTemp($data,$id) {
		//更新临时表sort排序
		foreach ($data as $k => $v) {
			$save = array(
							'sort' => $k,
							'id' => $v,
							'task_id' => $id,
				);
			db('task_node_temp')->update($save);
		}

		$map['id'] = array('in', $data);
		$nodes = db('task_node_temp')->where($map)->order('sort')->select();
		foreach ($nodes as $k => $v) {
			$add[]=array(
						'title' =>$v['title'],
						'content' =>$v['content'],
						'add_time' =>$v['add_time'],
						'user_name' =>$v['user_name'],
						'user_id' =>$v['user_id'],
						'task_id' =>$v['task_id'],
						'delivery' =>$v['delivery'],
						'sort' =>$v['sort'],
						'temp_id' =>$v['id'],
				);
		}
		//先删除原来的节点
		$result = db('task_node')->where('task_id='.$id)->delete();
		//再加上新排序的节点
		$result = db('task_node')->insert($add);
	}

	/**
	 * [addNodes 添加节点关联任务]
	 * @param [type] $id [任务id]
	 * @param [type] $data [添加数据]
	 */
	public function addNodes($id, $data) {
		$map['id'] = array('in', $data);
		$nodes = db('task_node_temp')->where($map)->order('sort')->select();
		foreach ($nodes as $k => $v) {
			$add[]=array(
						'task_id' =>$id,
						'title' =>$v['title'],
						'content' =>$v['content'],
						'add_time' =>$v['add_time'],
						'user_name' =>$v['user_name'],
						'user_id' =>$v['user_id'],
						'task_id' =>$v['task_id'],
						'delivery' =>$v['delivery'],
						'sort' =>$v['sort'],
						'temp_id' =>$v['id'],
				);
		}
		$result = db('task_node')->insertAll($add);
		if($result){
			//更新临时表
			$data['task_id'] = $id;
			db('task_node_temp')->where($map)->update($data);
		}

		return $result;
	}

	/**
	 * [updatenodes 修改节点关联任务]
	 * @param [type] $data [保存数据]
	 * @param [type] $id [任务ID]
	 */
	public function updatenodes($data,$id) {
		db('task_node')->where('task_id='.$id)->delete();
		return db('task')->where($where)->update($data);

	}

	/**
	 * [updateTask 修改任务]
	 * @param  [type] $data     [修改数据]
	 * @param  [type] $id [任务id]
	 */
	public function updateTask($data, $id) {
		$where['id'] = $id;
		return db('task')->where($where)->update($data);
	}

	/**
	 * [updateNode 修改节点]
	 * @param  [type] $id   [节点中的ID]
	 * @param  [type] $data   [节点中的数据]
	 */
	public function updateNode($id,$data) {
		$where['id'] = $id;
		db('task_node_temp')->where($where)->update($data);
		$map['temp_id'] = $id;
		$result = db('task_node')->where($map)->update($data);
		if($result !== false){
			return 1;
		}
	}

	/**
	 * [getNodeTemp 获取临时表节点信息]
	 * @param  [type] $id   [节点中的ID]
	 */
	public function getNodeTemp($id) {
		$where['id'] =  (int)$id;
		return db('task_node_temp')->where($where)->find();
	}

	/**
	 * [getNode 获取节点信息]
	 * @param  [type] $id   [节点中的ID]
	 */
	public function getNode($id) {
		$where['id'] =  (int)$id;
		return db('task_node')->where($where)->find();
	}

	/**
	 * [doneNode 完成节点信息]
	 * @param  [type] $id   [节点中的ID]
	 * @param  [type] $data   [节点中的data]
	 */
	public function doneNode($id,$data) {
		$where['id'] =  (int)$id;
		$r = db('task_node')->where($where)->update($data);
		if($r !== false){
			return 1;
		}
	}


	/**
	 * [delNode 册除节点]
	 * @param  [type] $id   [节点中的ID]
	 */
	public function delNode($id) {
		$where['id'] = $id;
		db('task_node_temp')->where($where)->delete();
		$map['temp_id'] = $id;
		db('task_node')->where($map)->delete();
	}

	/**
	 * [delNodeTemp 根据任务ID册除临时表节点]
	 * @param  [type] $id   [任务ID]
	 */
	public function delNodeTemp($id) {
		$where['task_id'] = $id;
		db('task_node_temp')->where($where)->delete();
	}

	/**
	 * [deleteTask 删除任务]
	 */
	public function deleteTask($id) {
		db('task')->startTrans();
		foreach ($id as $key => $value) {
			if (!empty($value)) {
				$data['id'] = $value;
				$data['is_delete'] = 1;
				$result = db('task')->update($data);
				if (!$result) {
					db('task')->rollback();
					return false;
				}
			}
		}
		db('task')->commit();
		return true;
	}

	/**
	 * [sendWx 发送微信提醒]
	 * @param  [int] $id [任务id]
	 * @param  [int] $type [类型]
	 * @param  [array] $data [审核内容]
	 */
	public function sendWx($id, $type, $data){
		$newMsg    = '你有新的任务需要审批';
		$newRecMsg = '你有新的任务需要接收';
		$runMsg    = '负责人任务节点完成提醒';
		$endMsg    = '负责人任务完结提醒';
		$remindMsg = '提醒您请及时跟进任务';
		$nodeMsg   = '任务节点完成提醒';
		$taskMsg   = '任务完结提醒';

		$user = model('Users');
		switch ($type) {
			//新建任务、派发任务
			case '1':
				//接收任务
				if ($data['status'] == 2) {
					$phone = $user->getPhone($data['charge_id']);		//通知负责人接收
					$msg   = $newRecMsg;
				}
				//审核任务
				if ($data['status'] == 3) {
					$phone = $user->getPhone($data['supervise_id']);	//通知监督人审批
					$msg   = $newMsg;
				}
				break;
			//任务节点完成
			case '2':
				$msg   = $user->getName($data['charge_id']).$nodeMsg;
				$phone = $user->getPhone($data['supervise_id']);		//通知监督人
				break;
			//任务完结
			case '3':
				$msg   = $user->getName($data['charge_id']).$taskMsg;
				$phone = $user->getPhone($data['supervise_id']);		//通知监督人
				break;
			//一键提醒
			case '4':
				$msg   = $user->getName($data['supervise_id']).$remindMsg;
				$phone = $user->getPhone($data['charge_id']);			//通知负责人
				break;
		}

		if ($msg && $phone) {
			model('Weixin')->sendMsg($phone,$msg);
		}
	}
}
?>