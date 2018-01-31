<?php
/**
 * 定时任务控制器
 */
namespace app\home\controller;
use think\Controller;

class Crontab extends Controller {

	/**
	 * [hourTask 每小时定时任务]
	 */
	public function hourTask() {
		//签约关怀列表
		// $this->_signCareList();
		//生日关怀列表
		$this->_birthdayCareList();
		//劳动节关怀列表
		$this->_labordayCareList();
		//国庆节关怀列表
		$this->_nationalDayCareList();
		//中秋节关怀列表
		$this->_moonFestivalCareList();
		//春节关怀列表
		$this->_springFestivalCareList();
	}

	/**
	 * [dayTask 每日定时任务]
	 */
	public function dayTask() {
		//发送短信关怀
		$this->_sendSmsCare();
	}

	/**
	 * [weekTask 每周定时任务]
	 */
	public function weekTask() {

	}

	/**
	 * [monthTask 每月定时任务]
	 */
	public function monthTask() {

	}

	/**
	 * [_springFestivalCareList 春节关怀列表]
	 */
	private function _springFestivalCareList(){
		$date_list = array(
			'2017-01-28',			//春节
			'2018-02-16',			//春节
			'2019-02-05',			//春节
		);
		if (!in_array(date('Y-m-d',strtotime('+1 day')), $date_list)){
			return;
		}
		//获取所有正常客户
		$res = $this->_getRecord();
		if ($res) {
			$res['date'] = date('Y-m-d',strtotime('+1 day'));
			$this->_addCare($res,'节日','愿新年的钟声，敲响您心中快乐的音符。幸运与平安，如春天的脚步紧紧相随，感谢您在过去的一年里大力支持，可爱可亲向您表示最诚挚的谢意和最热情的问候，祝您在新的一年里身体健康，阖家幸福。');
		}
	}

	/**
	 * [_moonFestivalCareList 中秋节关怀列表]
	 */
	private function _moonFestivalCareList(){
		$date_list = array(
			'2016-09-15',			//中秋节
			'2017-10-04',			//中秋节
			'2018-09-24',			//中秋节
		);
		if (!in_array(date('Y-m-d',strtotime('+1 day')), $date_list)){
			return;
		}
		//获取所有正常客户
		$res = $this->_getRecord();
		if ($res) {
			$res['date'] = date('Y-m-d',strtotime('+1 day'));
			$this->_addCare($res,'节日','海上明月共欣生，狮舞祥福随心到！感谢您对可爱可亲的支持与厚爱，值中秋国庆之际，衷心祝福您及家人：月圆福圆人团圆，家庭幸福安康！');
		}
	}

	/**
	 * [_nationalDayCareList 国庆节关怀列表]
	 */
	private function _nationalDayCareList(){
		if (date('m-d') != '09-30'){
			return;
		}
		//获取所有正常客户
		$res = $this->_getRecord();
		if ($res) {
			$res['date'] = date('Y-10-01');
			$this->_addCare($res,'节日','金秋十月，硕果累累，又是一年一度的国庆佳节，享受丰收享受生活。感谢您一路以来的相伴与支持。可爱可亲祝您国庆节日快乐！');
		}
	}

	/**
	 * [_holidayCareList 劳动节关怀列表]
	 */
	private function _labordayCareList(){
		if (date('m-d') != '04-30'){
			return;
		}
		//获取所有正常客户
		$res = $this->_getRecord();
		if ($res) {
			$res['date'] = date('Y-05-01');
			$this->_addCare($res,'节日','劳动创造美好生活，在这五月阳春又是踏青寻芳的季节，愿您有个好心情。感谢您长久以来的信任与陪伴，可爱可亲祝您在节日里开心欢畅，幸福安康。');
		}
	}

	/**
	 * [_birthdayCareList 生日关怀列表]
	 */
	private function _birthdayCareList(){
		$field = 'id,name,tel';
		$map['progress'] = array('in',array('正常','维权'));
		$map['tel'] = array('exp','is not null');
		$map['ID_No'] = array('exp','is not null');
		$map['SUBSTRING(ID_NO,11,4)'] = date('md',strtotime('+1 day'));
		$result = D('record')->getRcord($map,$field);
		if ($result) {
			foreach ($result as $key => $value) {
				$res['id'][] = $value['id'];
				$res['name'][] = $value['name'];
				$res['tel'][] = $value['tel'];
			}
			$res['date'] = date('Y-m-d');
			$this->_addCare($res,'生日','在这个特别的日子里可爱可亲祝您生日快乐，健康平安，感谢您对我们的支持与厚爱。');
		}
	}

	/**
	 * [_signCareList 签约关怀列表]
	 */
	private function _signCareList(){
		$field = 'id,name,tel';
		$map['progress'] = array('in',array('正常','维权'));
		$map['tel'] = array('exp','is not null');
		$map['ID_No'] = array('exp','is not null');
		$map['sign_time'] = date('Y-m-d 00:00:00',strtotime('-1 year +1 day'));
		$result = D('record')->getRcord($map,$field);
		if ($result) {
			foreach ($result as $key => $value) {
				$res['id'][] = $value['id'];
				$res['name'][] = $value['name'];
				$res['tel'][] = $value['tel'];
			}
			$res['date'] = date('Y-m-d',strtotime('+1 day'));
			$this->_addCare($res,'其它','签约关怀');
		}
	}

	/**
	 * [_getRecord 获取所有正常客户]
	 */
	private function _getRecord(){
		$field = 'id,name,tel';
		$map['status'] = array('eq',1);
		$map['progress'] = array('in',array('正常','维权'));
		$map['tel'] = array('exp','is not null');
		$map['ID_No'] = array('exp','is not null');
		$result = model('record')->getRcord($map,$field);
		if ($result) {
			foreach ($result as $key => $value) {
				$res['id'][] = $value['id'];
				$res['name'][] = $value['name'];
				$res['tel'][] = $value['tel'];
			}
		}
		return $res;
	}

	/**
	 * [_addCare 添加关怀]
	 */
	private function _addCare($res,$title,$content){
		$data = array();
		$data['customer_id'] = implode(',',$res['id']);
		$data['customer_name'] = implode(',',$res['name']);
		$data['connect'] = implode(',',$res['tel']);
		$data['user_id'] = 0;
		$data['user_name'] = '系统自动添加';
		$data['dpm_id'] = 0;
		$data['type'] = 1;
		$data['title'] = $title;
		$data['send_time'] = $res['date'];
		$data['content'] = $content;
		$data['warn'] = 1;
		$data['add_time'] = date("Y-m-d H:i:s");
		$data['status'] = 0;
		$add = model('Care')->addCare($data);
		echo 'Success';
	}

	/**
	 * [_sendSMSCare 发送短信关怀]
	 */
	private function _sendSmsCare(){
		$Care = model('Care');
		$map1['status'] = 0;
		$map1['warn'] = 1;
		$map1['send_time'] = date("Y-m-d",strtotime('+1 day'));
		$map2['status'] = 0;
		$map2['warn'] = 2;
		$map2['send_time'] = date("Y-m-d",strtotime('+2 day'));
		$map3['status'] = 0;
		$map3['warn'] = 3;
		$map3['send_time'] = date("Y-m-d",strtotime('+3 day'));
		$map4['status'] = 0;
		$map4['warn'] = 4;
		$map4['send_time'] = date("Y-m-d",strtotime('+4 day'));
		$map5['status'] = 0;
		$map5['warn'] = 5;
		$map5['send_time'] = date("Y-m-d",strtotime('+5 day'));
		$where = array($map1, $map2, $map3, $map4, $map5);
		$where['_logic'] = 'OR';
		$data = $Care->selectCare($where);
		$sms  = array();
		if ($data) {
			$send = model('Sms');
			foreach ($data as $k => $v) {
				$massage = array();
				$massage['tel'] = $v['connect'];
				$massage['send_time'] = $v['send_time']." 20:00:00";
				$massage['content'] = $v['content'].C('CORP.sms')['sign'];
				$massage['user_id'] = $v['user_id'];
				$massage['user_name'] = $v['user_name'];
				$massage['dept_id'] = 30;
				$massage['department'] = 'IT事业中心';
				$massage['type'] = 3;
				$sms[] = $massage;
				$update['status'] =1;
				$Care->updateCare($update,$v['id']);
			}
			$send->sendApi($sms);		//发送信息
		}
		echo 'Success';
	}

	/**
	 * [_sendWXCare 发送微信关怀]
	 */
	private function _sendWXCare(){

	}
}