<?php
/**
 * 微信企业版模型
 */
namespace app\home\model;
use think\Model;

class WeixinModel{
	//微信企业版网址
	const QYURL = 'https://qyapi.weixin.qq.com/cgi-bin/';

	/**
	 * [CorpID 获取企业版corpid]
	 */
	private function CorpID(){
		return config('WEIXIN.CORPID');
	}

	/**
	 * [CorpID 获取企业版Secret]
	 */
	private function Secret(){
		return config('WEIXIN.SECRET');
	}

	/**
	 * [_getAccessToken 获取接口凭证]
	 */
	private function _getAccessToken() {
		if (!cache('AccessToken')) {
			$url = self::QYURL.'gettoken?corpid=' . $this->CorpID() . '&corpsecret=' . $this->Secret();
			$output = cget($url);
			$output = json_decode($output, true);
			if ($output) {
				cache('AccessToken', $output['access_token'], $output['expires_in']-15);
			}
		}
		return cache('AccessToken');
	}

	/**
	 * [_postUrl 获取POST方式url]
	 * @param  [type] $action [请求接口]
	 */
	private function _postUrl($action) {
		$str = '?access_token=' . $this->_getAccessToken();
		$url = self::QYURL . $action . $str;
		return $url;
	}

	/**
	 * [sendMsg 发送消息]
	 */
	public function sendMsg($tel,$content) {
		// $tel = '13711666499|15802015098';						//测试
		$data = array(
			'touser' => $tel,
			'msgtype' => 'text',
			'agentid' => '2',
			'text' => array('content' => $content),
			'safe' => '0',
		);
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		$action = 'message/send';
		$url = $this->_postUrl($action);
		$output = cpost($url, $data);
		$result = json_decode($output, true);
		return $result['errcode'] == 0 ? true : false;
	}
}