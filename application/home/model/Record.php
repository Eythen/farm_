<?php
/**
 * 客户档案模型
 */
namespace app\home\model;
use think\Model;

class Record extends Model {

	//protected $table = 'yq_record';

	/**
	 * [record _list 获取分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function record_list($field, $offset = 0, $limit = 10, $sort = 'id', $order = 'DESC', $where) {
		$data['total'] = db('record')->where($where)->count();
		if( $data['total'] ){
			$data['rows'] =  db('record')->field($field)->where($where)->order($sort . ' ' . $order)->limit($offset, $limit)->select();
			foreach ($data['rows'] as $key => $value) {
				if ($value['sign_time']) {
					$data['rows'][$key]['sign_time'] = date('Y-m-d', strtotime($value['sign_time']));
				}
				if ($value['tel']) {
					$data['rows'][$key]['change_tel'] = change_tel($value['tel']);
				}
				if ($value['id_no']) {
					$data['rows'][$key]['change_idcard'] = change_idcard($value['id_no']);
				}
				if(in_array($value['province'], config('CORP.NANCANG'))){
					$data['rows'][$key]['warehouse'] = '南仓';
				}else{
					$data['rows'][$key]['warehouse'] = '北仓';
				}
			}
			$rank_res =  db('record')->where($where)->field('shop_rank,count(*) as count')->order('count DESC')->group('shop_rank')->select();	//统计各级别店铺数量
			if ($rank_res) {
				$rank = config('CORP.SHOP_RANK')[1];
				foreach ($rank_res as $k => $v) {
					if(!in_array( $v['shop_rank'], $rank)) {	//过滤店铺级别
						unset($rank_res[$k]);
					}
				}
				$data['rank_total'] = $rank_res;
			}
		}else{
			$data['rows'] = array();
		}
		return $data;
	}
	/**
	 * [getOne 获取客户档案]
	 * @param  [type] $memberid [查询条件]
	 */
	public function getOne($memberid) {
		$where['member_id'] = $memberid;
		return  db('record')->where($where)->find();
	}

	/**
	 * [getRcord 查询客户档案]
	 */
	public function getRcord($where,$field='*') {
		return  db('record')->field($field)->where($where)->select();
	}
	/**
	 * [getRcord 查询客户档案数]
	 */
	public function getCount($where) {
		return  db('record')->where($where)->count();
	}

	/**
	 * [findRcord 获取单个客户档案]
	 */
	public function findRcord($where, $field='*') {
		return  db('record')->field($field)->where($where)->find();
	}

	/**
	 * [getInfo 根据ID获取单个客户档案]
	 */
	public function getInfo($id) {
		$res =  db('record')->find($id);
		if ($res['tel']) {
			$res['change_tel'] = change_tel($res['tel']);
		}
		if ($res['id_no']) {
			$res['change_idcard'] = change_idcard($res['id_no']);
		}
		return $res;
	}

	/**
	 * [addRecord 添加客户档案]
	 * @param [type] $data [添加数据]
	 */
	public function addRecord($data) {
		$res =  db('record')->add($data);
		if ($res) {
			//新客户审核
			$record_info =  db('record')->getInfo($res);
			$data = array(
				'auditing_record_id' => $record_info['id'],
				'auditing_member_id' => $record_info['member_id'],
				'auditing_type' => 1,
				'auditing_user' => $_SESSION['uid'],
				'auditing_addtime' => get_date(),
			);
			D('Auditing')->addData($data);
		}
		return $res;
	}

	/**
	 * [saveRecord 保存客户档案]
	 * @param [type] $data [保存数据]
	 * @param [type] $where [查询条件]
	 */
	public function saveRecord($data,$where = array()) {
		return  db('record')->where($where)->save($data);
	}

	/**
	 * [updateRecord 修改档案]
	 * @param  [type] $data     [修改数据]
	 * @param  [type] $memberid [客户id]
	 */
	public function updateRecord($data, $memberid) {
		$where['member_id'] = $memberid;
		return  db('record')->where($where)->save($data);
	}

	/**
	 * [updatestatus 修改客户档案状态值]
	 * @param  [type] $status   [状态值]	2：待审核    1：审核通过   3：审核未通过    4:已删除
	 * @param  [type] $memberid [客户id]
	 */
	public function updatestatus($status, $memberid) {
		$where['member_id'] = $memberid;
		$data['status'] = $status;
		return  db('record')->where($where)->save($data);
	}

	/**
	 * [deleteRecord 删除客户档案]
	 */
	public function deleteRecord($id) {
		 db('record')->startTrans();
		foreach ($id as $key => $value) {
			if (!empty($value)) {
				$data['id'] = $value;
				$data['status'] = 4;
				$result =  db('record')->save($data);
				if (!$result) {
					 db('record')->rollback();
					return false;
				}
			}
		}
		 db('record')->commit();
		return true;
	}

	/**
	 * [checkPending 待审核档案数量]
	 */
	public function checkPending() {
		$where['status'] = 2;
		return  db('record')->where($where)->count();
	}
}
?>