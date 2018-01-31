<?php
/**
 * Mail模块
 */
namespace app\home\controller;
use think\Controller;

class Mail extends Base {
	/**
	 * [邮件首页]
	 */
	public function index() {
		if (request()->isAjax()) {
			$request = input('request.');
			$sort = $request['sort'] ? $request['sort'] : 'id';
			$order = $request['order'] ? $request['order'] : 'desc';
			$offset = $request['offset'] ? $request['offset'] : 0;
			$limit = $request['limit'] ? $request['limit'] : 10;
			$date = $request['date'] ? $request['date'] : '';
			//$where['is_delete'] = array('neq', '1');
			//权限设置 IT开发组有权限
			/*if(session('position_id') != 33){
				$uid = session('uid');
				$where['userid'] = $uid;
				$ur = model('Users')->getAll();
				foreach ($ur as $k => $v) {
					if($v['parent_id'] == 1 && $uid == $v['userid']){
						unset($where['userid']);
					}
				}
			}	*/
			//市
			if ($request['page_date']) {
				$arr = explode(' - ', $request['page_date']);
				sort($arr);
				list($start_time, $end_time) = $arr;
				$where['add_time'][] = array('egt', date('Y-m-d H:i:s',strtotime($start_time)));
				$where['add_time'][] = array('elt', date('Y-m-d H:i:s',strtotime("$end_time +1 day")));
			}

			if ($request['search']) {
				$map = array(
					'username' => array('LIKE', '%' . $request['search'] . '%'),
					'subject' => array('LIKE', '%' . $request['search'] . '%'),
					'addressee' => array('LIKE', '%' . $request['search'] . '%'),
					'sender' => array('LIKE', '%' . $request['search'] . '%'),
				);
				if (strtotime($request['search'])) {
					$map['add_time'] = array('LIKE', '%' . $request['search'] . '%');
				}
				$map['_logic'] = 'OR';
				$where = array($where, $map);
			}
			$field = array('id', 'username', 'subject', 'addressee', 'sender', 'add_time', 'content');
			$data = model('Mail')->listMail($field, $offset, $limit, $sort, $order, $where);
			return $data;
		}
		//查找用户设置邮箱了没

		$map['user_id'] = session('uid');
		$data = db('users_config')->where($map)->find();
		if($data['mail']){
			$this->assign('data',$data);
		}
        return $this->fetch();
	}

	/**
	 * [getInfo 查看邮件信息]
	 * @param  [type] $id [任务id]
	 */
	public function getInfo($id=''){
		if (request()->isPost()) {
			$id = input('id',0,'intval');
			$data = model('Mail')->getOne($id);
			//dump($data);
			if($data['attachment']){
				$arr = explode(';', $data['attachment']);
				array_filter($arr);
				$num = count($arr);
				if($num){
					$data['file_num'] = $num;
					foreach ($arr as $k => $v) {
						$file = explode('|', $v);
						$data['file'][$k]['file_url'] = $file[0];
						$data['file'][$k]['file_name'] = $file[1];
						$data['file'][$k]['file_size'] = round(filesize($_SERVER['DOCUMENT_ROOT'].$file[0])/1024,2);
					}
				}
			}
			return $data;
		}
	}

	/**
	 * [setMail 邮件设置]
	 */
	public function setMail() {
		$map['user_id'] = session('uid');
		$map['user_name'] = session('uname');
		$data = db('users_config')->where($map)->find();
		if($data['mail']){
			 $data['mail_password'] = okcode($data['mail_password'], 'DECODE', 'xxeecce_honey', 0);//解密
			$this->assign('data',$data);
		}
		if(request()->isPost()){
			$post = input('post.');
			$mail_password = okcode($post['mail_password'], 'ENCODE', 'xxeecce_honey', 0);//加密
			//echo $post['mail_password'];
			$data = array(
				'mail' => $post['mail'] ,
				'mail_password' => $mail_password
				);
			if($data){
				$request = db('users_config')->where($map)->update($data);
				if($request){
					$return = array('status'=>1,'info'=>'设置成功！');
				}
				else{
					$data['user_id'] = $map['user_id'];
					$data['user_name'] = $map['user_name'];
					array_filter($data);
					$request = db('users_config')->insert($data);
					if($request){
						$return = array('status'=>1,'info'=>'设置成功！');
					}
					else{
						$return = array('status'=>0,'info'=>'设置失败！');
					}
				}
				return $return;
			}
		}
		return $this->fetch();
	}

	/**
	 * [addMail 发送邮件]
	 */
	public function addMail() {
		header("Content-type: text/html; charset=utf-8");
		$id = session('uid');
		$data = model('Mail')->userConfig($id);
		if(empty($data)){
			return $this->fetch();
			//$this->error('请先设置邮箱账号与密码，再来发送邮件');
		}
		$this->assign('data', $data);
		if(request()->isAjax()){
			//获取数据
			$Mail = db('Mail');
			$request = input('request.');
			$sender     = $data['mail']; //发送者
			$password   = okcode($data['mail_password'], 'DECODE', 'xxeecce_honey', 0); //密码
			$addressee  = $request['addressee']; //收件人
			$subject    = $request['subject']; //主题
			$content    = $request['content']; //内容
			$attachment = $request['file']; //附件

			//中文逗号转换成英文逗号
			$addressee = str_replace('，', ',', $addressee);
			//判断是否发多人
			$ar = explode(',', $addressee);
			$i=0;
			$num = count($ar);
			foreach ($ar as $key => $v) {
				//发送邮件、存入数据库
				$response = $Mail->send($sender, $password, $v, $subject, $content, $attachment);
				//$msg = $response['info'];
				//dump($response);
				if($response['status']){
					$user_info['id'] = session('uid');
					$user_info['username'] =session('uname');
					if(empty($user_info)){
						$this->error('无法获取到用户');
					}
					$postdata['userid']   = $user_info['id']; //用户ID
					$postdata['username'] = $user_info['username']; //用户名
					$postdata['dept_id'] =session('dept_id');
					$postdata['add_time'] = date('Y-m-d H:i:s'); //添加时间
					$postdata['addressee'] = $v;//接收人
					$postdata['content']  = $content; //添加内容 
					$postdata['subject']  = $subject; //添加主题 
					if(!empty($attachment)){
						$postdata['attachment']  = $attachment; //添加附件
					}
					$postdata['sender']  = $sender; //发送邮箱
					//dump($postdata);
					$Mail->addMail($postdata) ?$i++  : ''; //添加数据
					//echo $Mail->_sql();
					unset($postdata);
				}
			}
			$msg = "总共要发送数：".$num."   成功数量：".$i."   失败数量：".($num-$i);
			$this->success($msg);
		}

		//用户设置的邮箱
		return $this->fetch();
	}


}