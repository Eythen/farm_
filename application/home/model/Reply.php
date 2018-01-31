<?php
/**
 * Description 			:	回访模型
 * CreateDate 			:	2016/05/04
 * Creater 				:	yangqing
 * LastChangeDate 		:	2016/05/04
 * LastChanger 			:	yangqing
 */
namespace app\home\model;
use think\Model;
use think\Db;

class Reply extends Model {

	//protected $tablePrefix = 'info_';
	//protected $tableName = 'replylog';

	/**
	 * [getReplyList 客户个人单表的回访数据]
	 * @param  [int] $offset [从哪条查起]
	 * @param  [int] $limit  [显示条数]
	 * @param  [string] $sort   [排序字段]
	 * @param  [string] $order  [排序方式]
	 * @param  [array] $where  [查询条件]
	 * @return [array]   $data      [返回数据]
	 */
	public function getReplyList($offset = 0, $limit = 10, $sort = 'id', $order = 'DESC', $where){
		/*dump($where);
		die;*/
		$data['total'] = db('replylog')->where($where)->count();
		if( $data['total'] ){
			$data['rows'] = db('replylog')->where($where)->order($sort . ' ' . $order)->limit($offset, $limit)->select();
		}else{
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [getList 获取全部客户联表查询回访数据]
	 * @param [int] $offset [分页查询开始的条数]
	 * @param [int] $limit  [分页长度]
	 * @param [string] $sort   [排序方式]
	 * @param [string] $order  [排序字段]
	 * @param [array] $where  [查询条件]
	 * @param [array] $groupWhere  [回访表查询条件]
	 * @param [string] $groupOrder  [回访表查询条件排序]
	 * @param [int] $info  [是否查询详情 1为是，0为否]
	 * @return [array] $data  [返回数据]
	 */
	public function getList($offset = 0, $limit=10, $sort = 'R.time', $order = 'DESC', $where,  $groupWhere='', $groupOrder='time',$info=1) {
		$where = $this->_map($where);
		if(empty($groupOrder)){
			$groupOrder = 'time';

		}
		$groupWhere['customer_id'] = array('gt','0');
		//$sort = 'R.time';
		$subQuery = db('Replylog')->where($groupWhere)->order($groupOrder.' desc')->buildSql();
		$replyLog = Db::table($subQuery.' b')->group('b.customer_id')->buildSql();

		//$join = $replyLog." R', 'C.id=R.customer_id";

		$field = 'C.id as id, C.name as name, C.city as city, C.province as province,C.store_address as store_address, C.shop_rank as shop_rank, C.sign_time,R.time, R.end_time, R.zy_name as username, R.department';
		if(empty($groupWhere)){
			$data['total']=  db('record')
								->alias('C')
								->join($replyLog.' R', 'C.id=R.customer_id', 'left')
								->field('id')
								->where($where)
								->count();

			if($data['total'] && $info){
				$data['rows']= db('record')
								->alias('C')
								->join($replyLog.' R', 'C.id=R.customer_id', 'left')
								->field($field)
								->where($where)
								//->group($group)
								->order($sort.' '.$order)
								->limit($offset, $limit)
								->select();
			}
		}
		else{
			$data['total']=  db('record')
								->alias('C')
								->join($replyLog.' R', 'C.id=R.customer_id', 'right')
								//->join('RIGHT JOIN '.$join)
								->field('id')
								->where($where)
								->count();
			if($data['total'] && $info){
				$data['rows']= db('record')
								->alias('C')
								->join($replyLog.' R', 'C.id=R.customer_id', 'right')
								->field($field)
								->where($where)
								//->group($group)
								->order($sort.' '.$order)
								->limit($offset, $limit)
								->select();
			}
		}
		return $data;
	}

	/**
	 * [addReply 添加]
	 */
	public function addReply($data) {
		$data = db('replylog')->insert($data);
		return $data;
	}

	/**
	 * [addReply 添加运营回访详细]
	 */
	public function addReply_yunying($data) {
		$data = db('reply_yunying')->insert($data);
		return $data;
	}

	/**
	 * [updateReply 修改]
	 * @param  [type] $data      [修改数据]
	 * @param  [type] $id          [对应id号]
	 */
	public function updateReply($data, $id) {
		$result = db('replylog')->where('id=' . $id)->update($data);
		return $result;		
	}

	/**
	 * [updateReply_yunying 修改]
	 * @param  [type] $data      [修改数据]
	 * @param  [type] $id          [对应id号]
	 */
	public function updateReply_yunying($data, $id) {
		$result = db('reply_yunying')->where('reply_id=' . $id)->update($data);
		return $result;		
	}

	/**
	 * [getOne 查询一条数据]
	 * @param  [type] $map [查询条件]
	 */
	public function getOne($map) {
		$result = db('replylog')->where($map)->find();
		return $result;		
	}

	/**
	 * [getOneId 查询一条数据]
	 * @param  [type] $map [查询条件]
	 */
	public function getOneId($map) {
		$result = db('replylog')->where($map)->find();
		return $result;		
	}

	/**
	 * [getOne_yunying 查询一条运营详细数据]
	 * @param  [type] $map [查询条件]
	 */
	public function getOne_yunying($map) {
		$result = db('reply_yunying')->where($map)->find();
		return $result;		
	}

	/**
	 * [reply_dolist 获取每个客户回访详细分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function reply_dolist($field,$offset = 0, $limit=10, $sort = 'add_time', $order = 'DESC', $where) {
		$data['total'] = db('reply_yunying')->where($where)->count();
		if( $data['total'] ){
			$data['rows'] = db('reply_yunying')->field($field)->where($where)->order($sort . ' ' . $order)->limit($offset,$limit)->select();
		}else{
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [deleteReply 删除数据]
	 * @param  [type] $map [查询条件]
	 */
	public function deleteReply($map) {
		$result = db('replylog')->where($map)->delete();
		return $result;
	}

	/**
	 *[_map 查询条件处理]
	 * @param  [array] $where [查询条件]
	 */
	private function _map($where) {
		if (is_array($where)) {
			$R = array('rank', 'zy_name', 'status','time','end_time','department', 'level');
			$C = array('name', 'tel', 'address', 'shop_rank', 'sign_time', 'zhaoshang_manager', 'zhaoshang_manager2');
			foreach ($where as $key => $value) {
				if (in_array($key, $R)) {
					$map['R.' . $key] = $value;
				} else if (in_array($key, $C)) {
					$map['C.' . $key] = $value;
				}
				else{
					$map[$key] = $value;
				}
			}
			return $map;
		} else {
			return $where;
		}
	}
}
?>