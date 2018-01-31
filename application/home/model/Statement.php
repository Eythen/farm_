<?php
namespace app\home\model;
use think\Model;

class Statement extends Model {

	/**
	 * [Statement _list 获取工作报表 分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function getList($field,$offset = 0, $limit = 10, $sort = 'id', $order = 'DESC', $where) {
	   
		$data['total'] = db('statement')->where($where)->count();
		//dump(db('statement')->getlastsql());
		if( $data['total'] ){
			$data['rows'] = db('statement')->field($field)->where($where)->order($sort . ' ' . $order)->limit($offset, $limit)->select();
			foreach ($data['rows'] as $key => $value) {
				$data['rows'][$key]['add_time'] = date('Y-m-d H:i',strtotime($value['add_time']));
			}
		}else{
			$data['rows'] = array();
		}
		return $data;
	}






}
?>