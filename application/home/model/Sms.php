<?php
/**
 * Description 			:	短信接口数据模型
 * CreateDate 			:	2016/06/11
 * Creater 				:	yangqing
 * LastChangeDate 		:	2016/06/11
 * LastChanger 			:	yangqing
*/
namespace app\home\model;
use think\Model;

class Sms extends Model {
	/**
	 * [_initialize description]  初始化
	 * @return [type] [description]
	 */
	public function _initialize() {
		//根据入口生成的session 部门ID判断权限
		/*$dpm_id = $_SESSION['user']['dpm_id'];
		if (empty($dpm_id)) {
			echo '没有权限操作本页面!';
			die;
		}
		$this->user = session('user');*/
	}

	/**
	 * [sendApi 发送手机短信]
	 * @param  [array] $data [数组]
	 * $data[] = array(
					'id' => $ar['id'],//如果在表info_sms_index中，可以加这个值更新第三方的短信平台任务ID
					'tel' => $ar['tel'],//多个手机号用英文逗号隔开
					'content' => $ar['content'],
					'send_time' => $ar['send_time'],
					'user_id' => $ar['user_id'],
					'user_name' => $ar['user_name'],
					'dept_id' => $ar['dept_id'],
					'department' => $ar['department'],
					'type' => 1,//1为发送，2为回复
					);
	 */
	public function sendApi($data) {
		foreach ($data as $k => $v) {
			$sendtime = $v['send_time'];
			$tel = $v['tel'];
			$message = $v['content'];
			if(time()>strtotime($sendtime)){
				$sendtime = date("Y-m-d H:i:s", strtotime('+3 seconds'));
			}
			$tel = $this->_repeat($tel);
			$post_data = array();
			$post_data['userid'] = config('sms.userid'); //改为自己的id
			$post_data['account'] = config('sms.account');
			$post_data['password'] = config('sms.password');
			$post_data['content'] = $message;
			// $post_data['content'] = iconv("gbk", "UTF-8", $message);
			$post_data['mobile'] = $tel;
			//不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
			if (!empty($sendtime)) {
				$post_data['sendtime'] = date('Y-m-d H:i:s', strtotime($sendtime));
			} else {
				$post_data['sendtime'] = '';
			}
			$url = 'http://115.29.242.32:8888/sms.aspx?action=send';
			// 拼接参数
			$o = '';
			foreach ($post_data as $key => $value) {
				$o .= "$key=" . urlencode($value) . '&';
			}
			$post_data = substr($o, 0, -1);
		
			// 发送短信
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
			$result = curl_exec($ch);
			$result = (array) simplexml_load_string($result);
			if ($result['returnstatus'] == 'Success') {
				if($v['id']){
					//更新主表外部短信平台taskID
					unset($dataIndex);
					$dataIndex['task_id'] =(int)$result['taskID'];
					$this->updateSmsIndex($v['id'],$dataIndex);
				}	
				//添加发送日志
				$add_time = date('Y-m-d H:i:s');
				$task_id = $result['taskID'];
				unset($dataList);
				if(strlen($tel)>20){
					//处理中文逗号成为英文逗号 
					$tel = str_replace('，', ',', $tel);	
					$tr=explode(',', $tel);
					$num=count($tr);
					for($i=0;$i<$num;$i++){
						$dataList[$i]=array(
							'user_id' => $v['user_id']?$v['user_id']:'' ,
							'user_name' => $v['user_name']?$v['user_name']:'' ,
							'dept_id' => $v['dept_id']?$v['dept_id']:'' ,
							'department' => $v['department']?$v['department']:'' ,
							'content' => $v['content']?$v['content']:'' ,
							'tel' => $tr[$i]?$tr[$i] :'' ,
							'add_time' => $add_time?$add_time:'' ,
							'task_id' => $task_id?$task_id:'' ,
							'type' => 1 ,
							);
					}
					$result = db('sms')->insertAll($dataList);
				}
				else{
					$dataList =array(
							'user_id' => $v['user_id']?$v['user_id']:'' ,
							'user_name' => $v['user_name']?$v['user_name']:'' ,
							'dept_id' => $v['dept_id']?$v['dept_id']:'' ,
							'department' => $v['department']?$v['department']:'' ,
							'content' => $v['content']?$v['content']:'' ,
							'tel' => $tel?$tel :'' ,
							'add_time' => $add_time?$add_time:'' ,
							'task_id' => $task_id?$task_id:'' ,
							'type' => 1 ,
							);
					$result = db('sms')->insert($dataList);
				}		
			} 
		}
	}

	/**
	 * [sendAll 发送手机短信]
	 * @param  [int] $id  [sms_index表ID]
	 */
	public function sendAll($id) {
		$data = $this->getSmsOne($id);
		$sendtime = $data['send_time'];
		$tel = $data['tel'];
		$message = $data['content'];
		if(time()>strtotime($sendtime)){
			$sendtime = date("Y-m-d H:i:s", strtotime('+3 seconds'));
		}
		$tel = $this->_repeat($tel);
		$post_data = array();
		$post_data['userid'] = config('sms.userid'); //改为自己的id
		$post_data['account'] = config('sms.account');
		$post_data['password'] = config('sms.password');
		$post_data['content'] = $message;
		// $post_data['content'] = iconv("gbk", "UTF-8", $message);
		$post_data['mobile'] = $tel;
		//不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
		if (!empty($sendtime)) {
			$post_data['sendtime'] = date('Y-m-d H:i:s', strtotime($sendtime));
		} else {
			$post_data['sendtime'] = '';
		}
		$url = 'http://115.29.242.32:8888/sms.aspx?action=send';
		// 拼接参数
		$o = '';
		foreach ($post_data as $k => $v) {
			$o .= "$k=" . urlencode($v) . '&';
		}
		$post_data = substr($o, 0, -1);
				
		// 发送短信
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
		$result = curl_exec($ch);
		$result = (array) simplexml_load_string($result);
		if (IS_AJAX) {
			if ($result['returnstatus'] == 'Success') {
				//更新主表外部短信平台taskID
				$data['task_id'] =array('eq',$result['taskID']);
				$this->updateSmsIndex($id,$data);
				//添加发送日志
				$this->log($tel, $message, $sendtime, $result['taskID']);
				$return = array('status' => 1, 'info' => '成功发送' . $result['successCounts'] . '条短信');
			} else {
				$return = array('status' => 0, 'info' => '发送失败了！失败原因：' . $result['message']);
			}
			return $return;
		}
	}

	/**
	 * [getSend description] 发送状态
	 * @return [type] [description]
	 */
	public function getSend() {
		$post_data = array();
		$post_data['userid'] = config('sms.userid'); //改为自己的id
		$post_data['account'] = config('sms.account');
		$post_data['password'] = config('sms.password');
		$url = 'http://115.29.242.32:8888/statusApi.aspx?action=query';
		// 拼接参数
		$o = '';
		foreach ($post_data as $k => $v) {
			$o .= "$k=" . urlencode($v) . '&';
		}
		$post_data = substr($o, 0, -1);

		// 发送短信
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
		$result = curl_exec($ch);

		$result = (array) simplexml_load_string($result);
		$num=0;
		if($result['statusbox']){
			$num=1;
		}
		if ($result['statusbox'] && is_array($result['statusbox'])) {
			$num=count($result['statusbox']);
		}

		
		if($num>=1){
			unset($data);
			for($i=0;$i<$num;$i++){
				foreach ($result['statusbox'][$i] as $key => $v) {
					$$key=$v;									
				}
				if($status==10){
					$data['status']=1;
				}
				else{
					$data['status']=2;
				}
				$map['tel']=$mobile;
				$map['task_id']=$taskid;
				$map['type']=1;
				//$map['status']=array('exp','IS NULL');
				//更新短信状态
				db('sms')->where($map)->save($data);
				//echo db()->_sql();			
			}

		}
	}

	/**
	 * [getBack 获取回复短信]

	 */
	public function getBack() {
		$post_data = array();
		$post_data['userid'] = config('sms.userid'); //改为自己的id
		$post_data['account'] = config('sms.account');
		$post_data['password'] = config('sms.password');
		$url = 'http://115.29.242.32:8888/callApi.aspx?action=query';

		// 拼接参数
		$o = '';
		foreach ($post_data as $k => $v) {
			$o .= "$k=" . urlencode($v) . '&';
		}
		$post_data = substr($o, 0, -1);

		// 发送短信
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
		$result = curl_exec($ch);
		$result = (array) simplexml_load_string($result);
		$num=0;
		if($result['callbox']){
			$num=1;
		}
		if ($result['callbox'] && is_array($result['callbox'])) {
			$num=count($result['callbox']);
		}

		
		if($num>=1){
			for($i=0;$i<$num;$i++){
				foreach ($result['callbox'][$i] as $key => $v) {
					$$key=$v;
					$tel=(string)$mobile;
					$content=(string)$content;
					$add_time=(string)$receivetime;
					$task_id=(string)$taskid;
					$data[$i]=array(
						'tel' => $tel?$tel:'',
						'type' =>2 ,
						'status' =>1 ,
						'content' => $content? $content:'' ,
						'add_time' => $add_time?$add_time:'' ,
						'task_id' => $task_id?$task_id:'' ,
					);									
				}							
			}
			//添加短信回复
			db('sms')->insertAll($data);
		}
	}

	/**
	 * [overage 余额及已发送量查询接口]
	 */
	public function overage() {
		$post_data = array();
		$post_data['userid'] = config('sms.userid'); //改为自己的id
		$post_data['account'] = config('sms.account');
		$post_data['password'] = config('sms.password');
		$url = 'http://115.29.242.32:8888/sms.aspx?action=overage';
		$o = '';
		foreach ($post_data as $k => $v) {
			$o .= "$k=" . $v . '&';
		}
		$post_data = substr($o, 0, -1);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
		$result = curl_exec($ch);
		$result = (array) simplexml_load_string($result);
		
	}

	/**
	 * [log 短信发送日志]
	 * @param  [string] $tel      [电话号码]
	 * @param  [string] $content  [发送内容]
	 * @param  [datetime] $sendtime [指定发送时间]
	 * @param  [int] $task_id  [外部短信平台任务id]
	 */
/*	private function log($tel, $content, $sendtime, $task_id='') {
		$tel=$tel;
		$content=$content;
		if(strlen($tel)>20){
			$tr=explode(',', $tel);
			$num=count($tr);
			$add_time = date('Y-m-d H:i:s');

			for($i=0;$i<$num;$i++){
				$data[$i]=array(
					'user_id' => session('uid')?session('uid'):'' ,
					'user_name' => session('uname')?session('uname'):'' ,
					'dept_id' => session('dept_id')?session('dept_id'):'' ,
					'department' => session('dept_name')?session('dept_name'):'' ,
					'content' => $content?$content:'' ,
					'tel' => $tr[$i]?$tr[$i] :'' ,
					'add_time' => $add_time?$add_time:'' ,
					'task_id' => $task_id?$task_id:'' ,
					'type' => 1 ,
					);
			}
			$result = db('sms')->addAll($data);
		}
		else{
			$data =array(
					'user_id' => session('uid')?session('uid'):'' ,
					'user_name' => session('uname')?session('uname'):'' ,
					'dept_id' => session('dept_id')?session('dept_id'):'' ,
					'department' => session('dept_name')?session('dept_name'):'' ,
					'content' => $content?$content:'' ,
					'tel' => $tel?$tel :'' ,
					'add_time' => $add_time?$add_time:'' ,
					'task_id' => $task_id?$task_id:'' ,
					'type' => 1 ,
					);
			$result = db('sms')->add($data);
		}		
	}*/

	/**
	 * [_repeat 过滤重复的电话号码]
	 * @param  [type] $tel [电话号码]
	 */
	private function _repeat($tel) {
		$tel = explode(',', $tel);
		if (is_array($tel)) {
			$tel = array_flip(array_flip($tel));
			return implode(',', $tel);
		} else {
			return $tel;
		}
	}

	/**
	 * [smsList 短信列表]
	 * @param  [type] $where [短信查询条件]
	 */
	public function smsList($where, $sort, $order, $offset, $limit){
		//组合显示	
		$data['total'] = db('sms_index')->where($where)->count();
		if( $data['total'] ){
			//获取审核人
			//$ur = model('Users')->getAll();
			//$arr = array_column($ur,'username','userid');
			/*
			$sms_check = config('sms_check');
			foreach ($sms_check as $key => $value) {
				$arr[$value['userid']] = $value['username'];
			}*/		
			$field = array('id','add_time', 'status', 'tel', 'type', 'user_name', 'supervise_id');
			$data['rows'] = db('sms_index')
					->field($field)
					->where($where)
					->order($sort . ' ' . $order)
					->limit($offset, $limit)
					->select();
			foreach ($data['rows'] as $k => $v) {
						$data['rows'][$k]['add_time'] = date("Y-m-d",strtotime($v['add_time']));
						if($v['supervise_id']){
							$supervise = $arr[$v['supervise_id']];
						}
						else{
							$supervise ="无";
						}
						$data['rows'][$k]['supervise_id'] = $supervise;
					}		
		}else{
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [smsStatusList 短信状态详情列表]
	 * @param  [type] $where [短信查询条件]
	 */
	public function smsStatusList($where, $sort, $order, $offset, $limit){
		//组合显示	
		$data['total'] = db('sms')->where($where)->count();
		if( $data['total'] ){
			//获取审核人
			/*$sms_check = config('sms_check');
			foreach ($sms_check as $key => $value) {
				$arr[$value['userid']] = $value['username'];
			}*/		
			$field = array('id','add_time', 'status', 'tel', 'type', 'user_name');
			$data['rows'] = db('sms')
					->field($field)
					->where($where)
					->order($sort . ' ' . $order)
					->limit($offset, $limit)
					->select();
			foreach ($data['rows'] as $k => $v) {
						$data['rows'][$k]['add_time'] = date("Y-m-d",strtotime($v['add_time']));
					}		
		}else{
			$data['rows'] = array();
		}
		return $data;
	}

	/**
	 * [getSmsOne 获取主表发送的短信]
	 * @param  [int] $id [短信表id]
	 */
	public function getSmsOne($id){
		$id = (int)$id;
		$data = db('sms_index')->find($id);
		return $data;
	}

	/**
	 * [saveSmsIndex 更新主表发送的短信]
	 * @param  [int] $id [短信表sms_index   id]
	 * @param  [array] $data [更新的数据]
	 */
	public function saveSmsIndex($id,$data){
		$map['id'] = (int)$id;
		$data = db('sms_index')->where($map)->update($data);
		return $data;
	}

	/**
	 * [getSmsTask 获取批量发送]
	 * @param  [int] $id [sms_index表task_id]
	 * @return [array]     [数组]
	 */
	public function getSmsTask($id){
		$map['task_id'] = $id;
		$result = db('sms')->where($map)->select();
		return $result;
	}

	/**
	 * [getStatusOne 获取发送的短信内容]
	 * @param  [int] $id [短信info_sms表id]
	 */
	public function getStatusOne($id){
		$id = (int)$id;
		$data = db('sms')->find($id);
		return $data;
	}

	/**
	 * [getStatusTask 获取本次任务本手机号的详细内容]
	 * @param  [int] $id [sms表task_id]
	 * @param  [int] $id [sms表tel]
	 * @return [array]     [数组]
	 */
	public function getStatusTask($id,$tel){
		$map['task_id'] = $id;
		$map['tel'] = $tel;
		$result = db('sms')->where($map)->select();
		return $result;
	}

	/**
	 * [addSmsIndex 添加主表短信]
	 * @param [array] $data [短信数组]
	 */
	public function addSmsIndex($data){
		$result = db('sms_index')->insert($data);
		return $result;
	}

	/**
	 * [updateSmsIndex 更新主表短信]
	 * @param [int] $id [短信id]
	 * @param [array] $data [短信数组]
	 */
	public function updateSmsIndex($id, $data){
		$map['id'] = $id;
		$result = db('sms_index')->where($map)->update($data);
		if($result !== false){
			return 1;
		}
		else{
			return;
		}
	}

	/**
	 * [setSms 设置短信模板]
	 * @param [array] $data [添加的数据]
	 */
	public function setSms($data){
		$result = db('sms_config')->insert($data);
		return $result;
	}

	/**
	 * [smsConfigList 获取个人短信模板]
	 * @param  [int] $uid [个人id]
	 */
	public function smsConfigList($uid){
		$map['uid'] = $uid;
		$data = db('sms_config')->where($map)->order('id desc')->select();
		return $data;
	}

}
