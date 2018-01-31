<?php
/**
 * 培训调查模型
 */
namespace app\home\model;
use think\Model;

class Training extends Model {
	/**
	 * [getList 获取报表列表]
	 * @param  [type] $offset [查询开始记录]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序字段]
	 * @param  [type] $order  [排序方式]
	 * @param  [type] $where  [查询条件]
	 */
	public function getList($offset, $limit, $sort, $order, $where) {
		$data['total'] = db('training')->where($where)->count();
		$data['rows'] = db('training')->where($where)->order($sort . ' ' . $order)->limit($offset, $limit)->select();
		if ($data['rows']) {
			foreach($data['rows'] as $key => $row) {
				$data['rows'][$key]['course_id'] = sprintf("%06d", $row['id']);
				if ($row['file']) {
					$files=explode(';', $row['file']);
					foreach ($files as $k => $v) {
						if ($v) {
							$file = explode('|',$v);
							if (file_exists(iconv('UTF-8', 'GB2312', '.'.$file[0]))) {
								$data['rows'][$key]['fileshow'][$k]['path'] = $file[0];
								$data['rows'][$key]['fileshow'][$k]['name'] = $file[1];
							}
						}
					}
				}
			}
		}else{
			$data['rows'] = array();
		}
		return $data;
	}
	/**
	 * [getNewId 获取最新ID]
	 */
	public function getNewId(){
		$lastId = db('training')->order('id DESC')->value('id');
		$newId = $lastId + 1;
		return $newId;
	}
	/**
	 * [getOne 获取报表详情]
	 */
	public function getOne($id) {
		return db('training')->find($id);
	}

	/**
	 * [addData 添加课程]
	 */
	public function addData($data) {
		return db('training')->insert($data);
	}

	/**
	 * [updateData 修改报表]
	 */
	public function updateData($data) {
		return db('training')->update($data);
	}

	/**
	 * [addLog 添加记录]
	 */
	public function addLog($data) {
		$data['add_time'] = date('Y-m-d H:i:s');
		return db('training_log')->insert($data);
	}

	/**
	 * [getLog 获取记录]
	 */
	public function getLog($id) {
		$map['training_id'] = $id;
		$result = db('training_log')->field(
		   'avg(score_training) as score_training,
			avg(score_teacher) as score_teacher,
			avg(score_lecture) as score_lecture,
			avg(score_plan) as score_plan'
			)->where($map)->find();
		$map['remark'] = array('neq', '');
		$result['remark'] = db('training_log')->field('remark')->where($map)->select();
		return $result;
	}
}
?>