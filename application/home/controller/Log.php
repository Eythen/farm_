<?php
namespace app\home\controller;
use think\Controller;

class Log extends Base {

	/**
	 * [index 管理员登录日志]
	 * @return [type] [description]
	 */
	public function index(){
		if( request()->isAjax() ){
			$offset = input('offset')?input('offset'):0;
			$limit = input('limit')?input('limit'):10;
			$order = input('order')?input('order'):'desc';
			$sort = input('sort')?input('sort'):'id';
			$search = input('search');
			if(!empty($search)){
				if( strtotime($search) ){
					$where['time'] = array( 'LIKE', '%'.$search.'%' );
				}else{
					$whereor = array(
						'username' => array( 'LIKE', '%'.$search.'%' ),
						'ip' => array( 'LIKE', '%'.$search.'%' ),
						'platform' => array( 'LIKE', '%'.$search.'%' ),
						'browser' => array( 'LIKE', '%'.$search.'%' ),
						'browserversion' => array( 'LIKE', '%'.$search.'%' ),
					);
				}
			}
			$data['total'] = db('login_log')->where($where)->whereor($whereor)->count();
			if($data['total']){
				$data['rows'] = db('login_log')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
			}
			
			return $data;
		}
		return $this->fetch();
	}

	/**
	 * [userlog 用户登录日志]
	 * @return [type] [description]
	 */
	public function userlog(){
		if( request()->isAjax() ){
			$offset = input('offset')?input('offset'):0;
			$limit = input('limit')?input('limit'):10;
			$order = input('order')?input('order'):'desc';
			$sort = input('sort')?input('sort'):'id';
			$search = input('search');
			if(!empty($search)){
				if( strtotime($search) ){
					$start_time =strtotime($search);
					$end_time = strtotime('+ 1 day', strtotime($search));
					$where['time'] = array( 'between', [$start_time, $end_time] );
				}else{
					$whereor = array(
						'user_name' => array( 'LIKE', '%'.$search.'%' ),
						'ip' => array( 'LIKE', '%'.$search.'%' ),
						'platform' => array( 'LIKE', '%'.$search.'%' ),
						'browser' => array( 'LIKE', '%'.$search.'%' ),
					);
				}
			}
			$data['total'] = db('user_login_log')->where($where)->whereor($whereor)->count();
			if($data['total']){
				$data['rows'] = db('user_login_log')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
				foreach ($data['rows'] as $k => $v) {
					$data['rows'][$k]['time'] = date('Y-m-d H:i:s', $v['time']);
				}
			}
			
			return $data;
		}
		return $this->fetch();
	}
}