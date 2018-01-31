<?php
/**
 * 投诉表模型
 */
namespace app\home\model;
use think\Model;

class Complaint extends Model {

	/**
	 * [feedback _list 获取投诉的分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function getList($field,$offset = 0, $limit = 10, $sort = 'id', $order = 'DESC', $where) {
		$data['total'] = db('complaint')->where($where)->count();
		if( $data['total'] ){
			$data['rows'] = db('complaint')->field($field)->where($where)->order($sort . ' ' . $order)->limit($offset, $limit)->select();
			foreach ($data['rows'] as $key => $value) {
				$data['rows'][$key]['add_time'] = date('Y-m-d H:i',strtotime($value['add_time']));
			}
		}else{
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [getAdvList 获取投诉反馈的分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function getAdvList($field, $offset = 0, $limit = 10, $sort = 'R.status', $order = 'ASC', $where) {
		$db = db('complaint_log');
/*		$r = $db->alias('I')
			->field('count(1)')
			->join('yq_complaint R', 'R.id=I.complaint_id','right')
			->where($where)
			->group('R.id')
			//->fetchSql(true)
			->select();

dump($r);*/

		$data['total'] = count($db->alias('I')
			->field('count(1)')
			->join('yq_complaint R', 'R.id=I.complaint_id','right')
			->where($where)
			->group('R.id')
			->select());
		if ($data['total']) {
			$data['rows'] = $db->alias('I')
				->join('yq_complaint R', 'R.id=I.complaint_id','right')
				->field($field)
				->where($where)
				->order('R.status asc,'.$sort . ' ' . $order)
				->limit($offset, $limit)
			    ->group('R.id')
				->select();
			foreach ($data['rows'] as $key => $value) {
				$data['rows'][$key]['add_time'] = date('Y-m-d H:i',strtotime($value['add_time']));
			}
		} else {
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [addComplaint 添加]
	 */
	public function addComplaint($data) {
		$data = db('complaint')->insert($data);
		return $data;
	}

	/**
	 * [addLog 添加回复状态]
	 */
	public function addLog($data) {
		$data = db('complaint_log')->insert($data);
		return $data;
	}

	/**
	 * [getLog 获取回复记录]
	 * @param  [int] $id     [反馈id]
	 * @param  [int] $userid [用户id]
	 */
	public function getLog($id,$userid) {
		$where = array(
			'complaint_id'=> $id,
			'user_id'=> $userid,
		);
		$data = db('complaint_log')->where($where)->find();
		return $data;
	}

	/**
	 * [updateLog 更新回复状态]
	 * @param  [int] $id     [反馈id]
	 * @param  [int] $userid [用户id]
	 * @param  [int] $status   [状态 0-未回复 1-已回复]
	 * @return [array]         [返回结果]
	 */
	public function updateLog($id,$userid,$status = 1) {
		$where = array(
			'complaint_id'=> $id,
			'user_id'=> $userid,
		);
		$data = db('complaint_log')->where($where)->update(array('status'=>$status));
		return $data;
	}

	/**
	 * [updateComplaint 修改]
	 * @param  [type] $data      [修改数据]
	 * @param  [type] $id [投诉id号]
	 */
	public function updateComplaint($data, $id) {
		$result = db('complaint')->where('id=' . $id)->update($data);
		return $result;
	}

	/**
	 * [getOne 查询一条数据]
	 * @param  [type] $map [查询条件]
	 */
	public function getOne($map) {
		$result = db('complaint')->where($map)->find();
		return $result;
	}

	/**
	 * [deleteComplaint 删除数据]
	 * @param  [type] $map [查询条件]
	 */
	public function deleteComplaint($map) {
		$result = db('complaint')->where($map)->update(array('is_delete'=>1));
		return $result;
	}
}
?>