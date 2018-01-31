<?php
/**
 * 邮件模型
 */
namespace app\home\model;
use think\Model;

class Mail extends Model {

	protected $tablePrefix = 'info_';
	protected $tableName = 'mail';

	/**
	 * [listMail获取分页数据]
	 * @param  [type] $field [需查询的字段]
	 * @param  [type] $offset [分页查询开始的条数]
	 * @param  [type] $limit  [分页长度]
	 * @param  [type] $sort   [排序方式]
	 * @param  [type] $order  [排序字段]
	 * @return [type] $where  [查询条件]
	 */
	public function listMail($field, $offset = 0, $limit = 10, $sort = 'id', $order = 'DESC', $where) {
		$data['total'] = db('mail')->where($where)->count();
		if( $data['total'] ){
			$data['rows'] = db('mail')->field($field)->where($where)->order($sort . ' ' . $order)->limit($offset, $limit)->select();
			foreach ($data['rows'] as $key => $value) {
				if ($value['content']) {
					$str = htmlspecialchars_decode($value['content']);
					$str = strip_tags($str);
					$data['rows'][$key]['content'] = mb_substr($str, 0,21,'utf-8');
				}
				if ($value['subject']) {
					$data['rows'][$key]['subject'] = mb_substr($value['subject'], 0,21,'utf-8');
				}
			}
		}else{
			$data['rows'] = array();
		}
		return $data;
	}
	/**
	 * [sendMail 发送邮件]
	 * @param  [string] $username [发件人]
	 * @param  [string] $password [密码]
	 * @param  [string] $addressee [收件人]
	 * @param  [string] $subject [主题]
	 * @param  [string] $msg [内容]
	 * @param  [array] $attachment [附件]
	 */
	public function send($username, $password, $addressee, $subject, $msg, $attachment=''){
		import('Common.Common.phpmailer'); //引用邮件类
		import('Common.Common.smtp'); //引用smtp类
		$mail = new \PHPMailer; //实例化邮件类
		$mail->isSMTP(); //使用smtp方法发送邮件
		$mail->Host = "smtp.qq.com";
		$mail->Port = 25;
		$mail->SMTPAuth = true; 
		$mail->Username = $username;
		$mail->Password = $password;
		$mail->setFrom($username);
		$mail->addAddress($addressee);
		$mail->Subject = $subject;
		//处理内容
		$msg = preg_replace('/(src=")/', '\1'.$_SERVER['DOCUMENT_ROOT'], $msg);
		$mail->msgHTML(htmlspecialchars_decode($msg));
		//处理附件
		if(!empty($attachment)){
			//$data = D('Files')->get_files_info("id in ($attachment)");
			$arr = explode(';', $attachment);
			array_filter($arr);
			if(!empty($arr)){
				foreach ($arr as $k => $vv) {
					$file = explode('|', $vv);
					$filepath = $_SERVER['DOCUMENT_ROOT'].$file[0];
					$file_name = $file[1];
					$mail->addAttachment($filepath, $file_name);
				}
			}
		}
		return $mail->send() ? array('status'=> 1, 'info'=> '邮件发送成功') : array('status'=> 0, 'info'=> '邮件发送失败：'.$mail->ErrorInfo);
	}

	/**
	 * [getOne 获取邮件详情]
	 * @param  [type] $id [邮件id]
	 * @return [type]     [邮件内容]
	 */
	public function getOne($id){
		$data = db('mail')->find($id);
		$data['content'] = htmlspecialchars_decode($data['content']);
		return($data);
	}

	
	/**
	 * [addMail 添加邮件记录]
	 * @return [array]  $data   [邮件内容]
	 */
	public function addMail($data){
		$result = $this->insert($data);
		return $result;
	}

	/**
	 * [userConfig 用户邮箱配置]
	 * @param  [type] $id [用户id号]
	 * @return [type] $data   [数组]
	 */
	public function userConfig($id){
		$map['user_id'] =array('eq', $id);
		$data = db('users_config')->where($map)->find();
		return $data;
	}
}