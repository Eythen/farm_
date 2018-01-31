<?php
/**
 * 反馈模型
 */
namespace app\home\model;
use think\Model;

class Feedback extends Model {

	/**
	 * [getList 获取反馈的分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function getList($field,$offset = 0, $limit = 10, $sort = 'id', $order = 'DESC', $where) {

		$data['total'] = db('feedback')->where($where)->count();
		if( $data['total'] ){
			$data['rows'] = db('feedback')->field($field)->where($where)->order($sort . ' ' . $order)->limit($offset, $limit)->select();
			foreach ($data['rows'] as $key => $value) {
				$data['rows'][$key]['add_time'] = date('Y-m-d H:i',strtotime($value['add_time']));
			}
		}else{
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [getAdvList 获取详细反馈的分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function getAdvList($field, $offset = 0, $limit = 10, $sort = 'R.status', $order = 'ASC', $where) {
		$db = db('feedback_log');
		$data['total'] = count($db->alias('I')
			->field('count(1)')
			->join('yq_feedback R', 'R.id=I.feedback_id')
			->where($where)
			->group('R.id')
			->select());

		if ($data['total']) {
			$data['rows'] = $db->alias('I')
				->join('yq_feedback R', 'R.id=I.feedback_id')
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
	 * [addFeedback 添加]
	 */
	public function addFeedback($data) {
		$data = db('feedback')->insert($data);
		return $data;
	}

	/**
	 * [addLog 添加回复状态]
	 */
	public function addLog($data) {
		$data = db('feedback_log')->insert($data);
		return $data;
	}

	/**
	 * [getLog 获取回复记录]
	 * @param  [int] $id     [反馈id]
	 * @param  [int] $userid [用户id]
	 */
	public function getLog($id,$userid) {
		$where = array(
			'feedback_id'=> $id,
			'user_id'=> $userid,
		);
		$data = db('feedback_log')->where($where)->find();
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
			'feedback_id'=> $id,
			'user_id'=> $userid,
		);
		$data = db('feedback_log')->where($where)->save(array('status'=>$status));
		return $data;
	}

	/**
	 * [updateFeedback 修改]
	 * @param  [type] $data      [修改数据]
	 * @param  [type] $id [投诉id号]
	 */
	public function updateFeedback($data, $id) {
		$result = db('feedback')->where('id=' . $id)->save($data);
		return $result;
	}

	/**
	 * [getOne 查询一条数据]
	 * @param  [type] $map [查询条件]
	 */
	public function getOne($map) {
		$result = db('feedback')->where($map)->find();
		return $result;
	}

	/**
	 * [deleteFeedback 删除数据]
	 * @param  [type] $map [查询条件]
	 */
	public function deleteFeedback($map) {
		$result = db('feedback')->where($map)->save(array('is_delete'=>1));
		return $result;
	}
}
?>