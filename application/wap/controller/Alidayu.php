<?php
/**
 * Thinkphp整合 啊里云短信 https://dysms.console.aliyun.com/dysms.htm?spm=5176.2020520001.1001.16.ba4qfN#/template
 */
namespace app\wap\controller;
ini_set("display_errors", "on");

require_once ROOT_PATH . '/extend/aliyun/api_sdk/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();
use think\Controller;
use think\Loader;

class Alidayu extends Controller {
	const appkey = 'LTAIa7oek3VUNmxy';
	const secretKey = '4Kpq2KGEtVBDvJvP5dkBBbg8z0qPUb';
	const sms_status = ['0' => '未查询', '1' => '等待回执', '2'=>'发送失败', '3'=>'发送成功'];
	protected $tel; //记录不能发送的手机号码
    public $code;

	

	/**
	 * [index 短信平台]
	 * @return [type] [description]
	 */
	public function index(){
		
		exit('短信平台');
		return $this->fetch();    
	}

	

    /**
     * [sendcode 发送验证码短信]
     * @return [type] [description]
     */
    public function sendcode($mobile=''){


        if( request()->isAjax() || request()->isGet()){
        //if( $id ){
            $id = 1; //验证码数据库模板ID

            $request['mobile'] = input('mobile');
            if(empty($request['mobile'])){
                $request['mobile'] = $mobile;
            }


            $data = db('alidayu_template')->find($id);
            $code = rand(1000, 9999);
            $data['content'] = str_replace('xxxx', $code, $data['content']);
            cookie('sendcode', $code, 300);
            $this->code = $code;

            if(session('sendtime')){//取上一次发送时间
                $fortime = time()-session('sendtime');
                if($fortime < 30){
                    $this->error('请不要频繁获取！请5分钟后再尝试！');
                }
            }
            $data2 = [
                    'sms_code' => $data['templatecode'],
                    'content' => $data['content'],
                    'mobile' => $request['mobile'],
                    'user_id' => session('user_id'),
                    'add_time' => time()
                    ];
            $id = db('alidayu')->insertGetId($data2);

            $param = '{"code":"'.$code.'"}';


            $this->alidayuSend($id, $request['mobile'], $data['templatecode'], $data['sign_name'], $param );  


            session('sendtime', time());

            if (request()->isAjax()){
                if( $this->tel ){
                    $this->error($this->tel.'这号码发送错误！');
                }else{
                    $this->success(config('MSG.ADD_SUCCESS'));
                }
            }

        }
    }

    /**
     * [alidayuSend 啊里大于短信发送]
     * @param  [type] $id       		[短信内容id]
     * @param  [type] $phoneNumbers   	[手机号码]
     * @param  [type] $templateCode 	[短信模板编号]
     * @param  [type] $signName 		[短信签名]
     * @param  string $templateParam    [短信模板的参数]
     * @return [type]           		[description]
     */
    private function alidayuSend($id, $phoneNumbers, $templateCode, $signName, $templateParam = null, $outId = null){

        // 短信API产品名
        $product = "Dysmsapi";

        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        // 初始化用户Profile实例
        //$profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        $profile = DefaultProfile::getProfile($region, self::appkey, self::secretKey);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 初始化AcsClient用于发起请求
        $this->acsClient = new DefaultAcsClient($profile);

        /*$templateCode = 'SMS_100815061';
        $signName = '商华爱心商城';*/
    
		// 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($phoneNumbers);

        // 必填，设置签名名称
        $request->setSignName($signName);

        // 必填，设置模板CODE
        $request->setTemplateCode($templateCode);

        // 可选，设置模板参数
        if($templateParam) {
            $request->setTemplateParam($templateParam);
        }


        // 可选，设置流水号
        if($outId) {
            $request->setOutId($outId);
        }

        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);
        $acsResponse = json_decode(json_encode($acsResponse),TRUE); //转换成数组


        // 打印请求结果
        // var_dump($acsResponse);
       	//dump($acsResponse);

        if($acsResponse['Code'] == 'OK'){
            $map['id'] = $id;
            $data['biz_id'] = $acsResponse['BizId'];
            db('alidayu')->where($map)->update($data);
        }
        else{
            $this->tel .= ','.$mobile;
        }

    }

	
    


}
?>