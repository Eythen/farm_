<?php
/**
 * 关怀模型
 */
namespace app\home\model;
use think\Model;

class Care extends Model {

	/**
	 * [getList 获取投诉的分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function getList($offset = 0, $limit = 10, $sort = 'id', $order = 'DESC', $where) {
		$data['total'] = $this->where($where)->count();
		if( $data['total'] ){
			$data['rows'] = $this->where($where)->order($sort . ' ' . $order)->limit($offset, $limit)->select();
		}else{
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [addCare 添加]
	 */
	public function addCare($data) {
		return $this->add($data);
	}

	/**
	 * [selectCare 查询结果]
	 */
	public function selectCare($map, $field='*') {
		return $this->where($map)->field($field)->select();
	}

	/**
	 * [updateCare 修改]
	 * @param  [type] $data      [修改数据]
	 * @param  [type] $id [投诉id号]
	 */
	public function updateCare($data, $id) {
		$result = $this->where('id=' . $id)->save($data);
		return $result;
	}

	/**
	 * [getOne 查询一条数据]
	 * @param  [type] $map [查询条件]
	 */
	public function getOne($map) {
		$result = $this->where($map)->find();
		return $result;
	}
}
?>