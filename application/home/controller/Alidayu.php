<?php
/**
 * Thinkphp整合 啊里云短信平台 https://dysms.console.aliyun.com/dysms.htm?spm=5176.2020520001.1001.16.ba4qfN#/template
 */
namespace app\home\controller;
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



class Alidayu extends Base {
 
	const appkey = 'LTAIa7oek3VUNmxy';
    const secretKey = '4Kpq2KGEtVBDvJvP5dkBBbg8z0qPUb';
	const sms_status = ['0' => '未查询', '1' => '等待回执', '2'=>'发送失败', '3'=>'发送成功'];
	protected $tel; //记录不能发送的手机号码

    
	/**
	 * [index 短信平台]
	 * @return [type] [description]
	 */
	public function index(){
		
		return $this->fetch();    
	}



	/**
     * [send 发送短信]
     * @return [type] [description]
     */
    public function send(){
        $id = input('id',0,'intval');

        $cat = config('alidayu_cat'); //啊里大于短信分类
        $this->assign('cat',$cat);

        if( $id ){
            $data = db('alidayu_template')->find($id);
            $this->assign('data',$data);
        }
        if( request()->isPost() ){
            $request = input('request.');

            
           /* if($data['cat'] == 2){    
	            $data2 = [
	                'sms_code' => $data['templatecode'],
	                'content' => $data['content'],
	                'mobile' => $request['mobile'],
	                'admin_id' => session('uid'),
	                'add_time' => time()
	                ];
            }*/
            $request['mobile'] = str_replace('，', ',', $request['mobile']);
            $request['mobile'] = trim($request['mobile'], ',');


            switch ($data['cat'] ) {
            	case 2:			//短信推广
            		
            		$mobile = explode(',', $request['mobile']);
            		if(count( $mobile ) > 1){
            			foreach ($mobile as $k => $v) {
            				$data2 = [
			                'sms_code' => $data['templatecode'],
			                'content' => $data['content'],
			                'mobile' => $v,
			                'admin_id' => session('uid'),
			                'add_time' => time()
			                ];
		            		$id = db('alidayu')->insertGetId($data2);
		            		$this->alidayuSend($id, $v, $data['templatecode'], $data['sign_name']);

            			}
            		}
        			else{
        				$data2 = [
		                'sms_code' => $data['templatecode'],
		                'content' => $data['content'],
		                'mobile' => $request['mobile'],
		                'admin_id' => session('uid'),
		                'add_time' => time()
		                ];
	            		$id = db('alidayu')->insertGetId($data2);
	            		$this->alidayuSend($id, $request['mobile'], $data['templatecode'], $data['sign_name']);
        			}

            		
            		break;
            	case 1:			//短信通知
            		$mobile = explode(',', $request['mobile']);
            		if(count( $mobile ) > 1){
            			foreach ($mobile as $k => $v) {
            				$data2 = [
			                'sms_code' => $data['templatecode'],
			                'content' => $data['content'],
			                'mobile' => $v,
			                'admin_id' => session('uid'),
			                'add_time' => time()
			                ];
		            		$id = db('alidayu')->insertGetId($data2);
		            		$this->alidayuSend($id, $v, $data['templatecode'], $data['sign_name']);

            			}
            		}
        			else{
        				$data2 = [
		                'sms_code' => $data['templatecode'],
		                'content' => $data['content'],
		                'mobile' => $request['mobile'],
		                'admin_id' => session('uid'),
		                'add_time' => time()
		                ];
	            		$id = db('alidayu')->insertGetId($data2);
	            		$this->alidayuSend($id, $request['mobile'], $data['templatecode'], $data['sign_name']);
        			}
            		break;
            	default:
            		# code...
            		break;
            }


            if( $this->tel ){
                $this->error($this->tel.'这些手机号码发送错误！');
            }else{
                $this->success(config('MSG.ADD_SUCCESS'));
            }
        
        }
        
        return $this->fetch();
    }

    /**
     * [sendcode 发送验证码短信]
     * @return [type] [description]
     */
    public function sendcode(){


        if( request()->isAjax() ){
        //if( $id ){
            $id = 1; //验证码数据库模板ID

            $request['mobile'] = '15820213972';

            $data = db('alidayu_template')->find($id);
            $code = rand(1000, 9999);
            $data['content'] = str_replace('xxxx', "${".$code."}", $data['content']);
            session('sendcode', $rand, 300);
            $fortime = time()-session('sendtime');
            if($fortime < 300){
                $this->error('请不要频繁获取！请5分钟后再尝试！');
            }

            $data2 = [
                    'sms_code' => $data['templatecode'],
                    'content' => $data['content'],
                    'mobile' => $request['mobile'],
                    'admin_id' => session('uid'),
                    'add_time' => time()
                    ];
            $id = db('alidayu')->insertGetId($data2);

            $param = '{"code":"'.$code.'"}';
            $this->alidayuSend($id, $request['mobile'], $data['templatecode'], $data['sign_name'], $param);  


            if( $this->tel ){
                $this->error($this->tel.'这号码发送错误！');
            }else{
                session('sendtime', time());
                session('sendcode', null);
                $this->success(config('MSG.ADD_SUCCESS'));
            }
        
        }
    }

    /**
     * [alidayuSend 啊里云短信发送]
     * @param  [type] $id            [短信内容id]
     * @param  [type] $phoneNumbers  [发送的手机号]
     * @param  [type] $templateCode  [短信云平台模板代码编号]
     * @param  [type] $signName      [短信签名]
     * @param  [type] $templateParam [模板代码中的参数]
     * @param  [type] $outId         [流水编号]
     * @return [type]                [description]
     */
    public function alidayuSend($id, $phoneNumbers, $templateCode, $signName, $templateParam = null, $outId = null){

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
            $request->setTemplateParam(json_encode($templateParam));
        }


        // 可选，设置流水号
        if($outId) {
            $request->setOutId($outId);
        }

        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);
        $acsResponse = json_decode(json_encode($acsResponse),TRUE); //转换成数组


        // 打印请求结果
         //var_dump($acsResponse);

        if($acsResponse['Code'] == 'OK'){
            $map['id'] = $id;
            $data['biz_id'] = $acsResponse['BizId'];
            db('alidayu')->where($map)->update($data);
        }
        else{
            $this->tel .= ','.$mobile;
        }

    }


	/**
     * [edit 编辑]
     * @return [type] [description]
     */
    public function edit(){
        $id = input('id',0,'intval');

        $cat = config('alidayu_cat'); //啊里大于短信分类
        $this->assign('cat',$cat);

        if( $id ){
            $data = db('alidayu_template')->find($id);
            $this->assign('data',$data);
        }
        if( request()->isPost() ){
            $request = input('request.');

            $id = $request['id'];
            unset($request['id']);
            if( $id ){
                $where['id'] = $id;
                $data2 = [
                    'cat' => $request['cat'],
                    'content' => $request['content'],
                    'templatecode' => $request['templatecode'],
                    'sign_name' => $request['sign_name'],
                    'check_userid' => session('uid'),
                    'check_time' => time()
                    ];
                $result = db('alidayu_template')->where($where)->update($data2);
                
                       
                if( $result ){
                    $this->success(config('MSG.UPDATE_SUCCESS'));
                }else{
                    $this->error(config('MSG.UPDATE_ERROR'));
                }
            }else{
                
                $data2 = [
                    'cat' => $request['cat'],
                    'content' => $request['content'],
                    'admin_id' => session('uid'),
                    'add_time' => time()
                    ];

                $result = db('alidayu_template')->insert($data2);

                if( $result ){
                    $this->success(config('MSG.ADD_SUCCESS'));
                }else{
                    $this->error(config('MSG.ADD_ERROR'));
                }
            }
            
        }
        
        return $this->fetch();
    }

    /**
     * [delete 删除]
     * @return [type] [description]
     */
    public function delete(){
        if( request()->isPost() ){

            $request = input('request.');
            $ids = $request['id'];
            $id =explode(',', $ids);
            $id = array_filter($id);


            $where['id'] = ['in', $id];
            $result = db('alidayu')->where($where)->delete();



            if( $result ){
                $this->success(config('MSG.DELETE_SUCCESS'));
            }else{
                $this->success(config('MSG.DELETE_ERROR'));
            }
        }
    }


    /**
     * [log 日志列表]
     */
    public function log(){

        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where ='';
            if($request['admin_id']){
                $where['admin_id'] = ['eq', $request['admin_id']];
            }
            if($request['search']){
                $where['content'] = ['like', '%'.$request['search'].'%'];
            }


            $data['rows'] = db('alidayu')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('alidayu')->where($where)->whereor($whereor)->count();

            

            //格式化
            foreach ($data['rows'] as $k => $v) {
            	if($v['sms_status'] ==0 && !empty($v['biz_id'])){
            		$this->updateStatus($v['biz_id'], $v['mobile'], formatTime($v['add_time'], 'Ymd') );

            		$map['id'] = $v['id'];
            		$new = db('alidayu')->where($map)->find();
            		$data['rows'][$k]['sms_receiver_time'] = formatTime($new['sms_receiver_time'], 'Y-m-d H:i:s');
	                $data['rows'][$k]['sms_send_time'] = formatTime($new['sms_send_time'], 'Y-m-d H:i:s');
	                $data['rows'][$k]['sms_status'] = self::sms_status[$new['sms_status']];
            	}
            	else{
	                $data['rows'][$k]['sms_receiver_time'] = formatTime($v['sms_receiver_time'], 'Y-m-d H:i:s');
	                $data['rows'][$k]['sms_send_time'] = formatTime($v['sms_send_time'], 'Y-m-d H:i:s');
	                $data['rows'][$k]['sms_status'] = self::sms_status[$v['sms_status']];
            	}
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['admin_id'] = get_admin_name($v['admin_id']);
            }
            return $data;

           // dump($request);

        }
        return $this->fetch();
    }

    
    /**
     * [logOne 单个日志列表]
     */
    public function logOne($mobile){
  
        if(request()->isAjax()){
            $request = input('request.');
   
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            
            $where ='';
            $where['mobile'] = ['eq', $mobile];
            if($request['admin_id']){
                $where['admin_id'] = ['eq', $request['admin_id']];
            }
            if($request['search']){
                $where['content'] = ['like', '%'.$request['search'].'%'];
            }


            $data['rows'] = db('alidayu')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('alidayu')->where($where)->whereor($whereor)->count();

            

            //格式化
            foreach ($data['rows'] as $k => $v) {
            	if($v['sms_status'] ==0){
            		$this->updateStatus($v['biz_id'], $v['mobile'], formatTime($v['add_time'], 'Ymd') );

            		$map['id'] = $v['id'];
            		$new = db('alidayu')->where($map)->find();
            		$data['rows'][$k]['sms_receiver_time'] = formatTime($new['sms_receiver_time'], 'Y-m-d H:i:s');
	                $data['rows'][$k]['sms_send_time'] = formatTime($new['sms_send_time'], 'Y-m-d H:i:s');
	                $data['rows'][$k]['sms_status'] = self::sms_status[$new['sms_status']];
            	}
            	else{
	                $data['rows'][$k]['sms_receiver_time'] = formatTime($v['sms_receiver_time'], 'Y-m-d H:i:s');
	                $data['rows'][$k]['sms_send_time'] = formatTime($v['sms_send_time'], 'Y-m-d H:i:s');
	                $data['rows'][$k]['sms_status'] = self::sms_status[$v['sms_status']];
            	}
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['admin_id'] = get_admin_name($v['admin_id']);
            }
            return $data;

           // dump($request);

        }
        $this->assign('mobile', $mobile);
        return $this->fetch();
    }

     /**
     * [templateList 短信申请列表]
     */
    public function templateList(){

        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where ='';
            if($request['admin_id']){
                $where['admin_id'] = ['eq', $request['admin_id']];
            }
            if($request['search']){
                $where['content'] = ['like', '%'.$request['search'].'%'];
            }


            $data['rows'] = db('alidayu_template')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('alidayu_template')->where($where)->whereor($whereor)->count();

            

            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['cat'] = config('alidayu_cat')[$v['cat']];
                $data['rows'][$k]['admin_id'] = get_admin_name($v['admin_id']);
                $data['rows'][$k]['check_userid'] = get_admin_name($v['check_userid']);
                $data['rows'][$k]['check_time'] = formatTime($v['check_time'], 'Y-m-d H:i:s');
            }
            return $data;

           // dump($request);

        }
        return $this->fetch();
    }


    /**
     * [updateStatus 更新短信状态]
     * @param  [type]  $bizId          [啊里云流水号]
     * @param  [type]  $phoneNumbers    [查询手机号]
     * @param  [type]  $sendDate        [查询时间 格式20170615]
     * @param  integer $currentPage     [当前分页默认为1]
     * @param  integer $pageSize        [分页数量默认10为一页]
     * @return [type]                   [description]
     */
	public function updateStatus($bizId, $phoneNumbers, $sendDate, $currentPage='1', $pageSize='10'){
        
        /*$bizId = '403805807529214330^0';
        $phoneNumbers = '15820213972';
        $sendDate = '20171009';
        $currentPage='1';
        $pageSize='10';*/

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

		 // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
        $request = new QuerySendDetailsRequest();

        // 必填，短信接收号码
        $request->setPhoneNumber($phoneNumbers);

        // 选填，短信发送流水号
        $request->setBizId($bizId);

        // 必填，短信发送日期，支持近30天记录查询，格式Ymd
        $request->setSendDate($sendDate);

        // 必填，分页大小
        $request->setPageSize($pageSize);

        // 必填，当前页码
        $request->setCurrentPage($currentPage);

        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);
        $acsResponse = json_decode(json_encode($acsResponse), TRUE); //转换成数组

        // 打印请求结果
        //var_dump($acsResponse);

		
		//dump($statusData);
        if( $acsResponse['TotalCount']>=1){
            $result = $acsResponse['SmsSendDetailDTOs']["SmsSendDetailDTO"][0];
    		$map['biz_id'] = $bizId;

    		$data['sms_code'] = $result['TemplateCode'];
    		$data['sms_status'] = $result['SendStatus'];
    		$data['sms_receiver_time'] = strtotime($result['ReceiveDate']);
    		$data['sms_send_time'] = strtotime($result['SendDate']);
    		$result = db('alidayu')->where($map)->update($data);
        }

		
		//return $statusData;
	}
    

    /**
     * [moreAdd 批量导入添加]
     */
    public function moreAdd(){
        return $this->fetch();
    } 

    /**
     * [upExcel 上传Excel文件批量导入添加]
     */
    public function upExcel(){
        
        $filename = htmlspecialchars($_POST['Filename'], ENT_QUOTES); 
        $path = 'test';
        $file = request()->file('file');

        $file_dir_path = REAL_PATH.'public' . DS . $path . DS;

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate([ 'size' => 20000000, 'ext' => 'xls,xlsx,txt' ])->move($file_dir_path);
           
        if ($info) { 

            $result = action('PHPExcel/read',array($file_dir_path, $info->getSaveName(), '0', '2') );

            if ($result['status']) {
                $data = $result['info'];
                $ar = array();
                foreach ($data as $key => $value) {
                    $data = array(
                        'sn' => $value['A'],
                        );
                    array_push($ar, $data);
                }
                
            }
        }
        if(!empty($ar)){
             $result = db('generalize')->insertAll($ar);
        }
        if($result){
            $return = array('status'=>1, 'info'=>'添加成功');
        }
        else{
            $return = array('status'=>0, 'info'=>'添加失败，请重新上传！');
        }
        
        return json_encode($return);
    } 




	/**
	 * [read 读取表格数据]
	 * @param  [type] $savepath [文件目录]
	 * @param  [type] $savename [文件名称]
	 * @param  [type] $Sheet    [工作表]	获取表中工作表，如果要获取第一个，为0，依次类推
	 * @param  [type] $Row      [行]	从哪行开始读取数据，索引值从0开始
	 * @param  [type] $Column   [列]	//从哪列开始，A表示第一列	依次类推
	 */
	public function read($savepath, $savename, $Sheet = 0, $Row = 0, $Column = 'A') {
		$filename = $savepath . $savename;
		if (!file_exists($filename)) {
			$return = array('status' => 0, 'info' => '文件不存在!');
		} else {
			//import("PHPExcel.PHPExcel"); //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
			//$PHPExcel = new \PHPExcel(); //创建PHPExcel对象，注意，不能少了\
			Loader::import('PHPExcel.PHPExcel');
	        $PHPExcel=new \PHPExcel();


			// 判断后缀名称
			$ext = $this->_getEtc($savename);
			if ($ext == 'xls') {
				//如果excel文件后缀名为.xls，导入这个类
				Loader::import('PHPExcel.PHPExcel.Reader.Excel5');
				$PHPReader = new \PHPExcel_Reader_Excel5();
			} else if ($ext == 'xlsx') {
				//如果excel文件后缀名为.xlsx，导入这下类
				Loader::import('PHPExcel.PHPExcel.Reader.Excel2007');
				$PHPReader = new \PHPExcel_Reader_Excel2007();
			}
			$PHPExcel = $PHPReader->load($filename); //载入文件
			$currentSheet = $PHPExcel->getSheet($Sheet); //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
			$allColumn = $currentSheet->getHighestColumn(); //获取总列数
			$allRow = $currentSheet->getHighestRow(); //获取总行数

			//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
			for ($currentRow = $Row; $currentRow <= $allRow; $currentRow++) {
				//从哪列开始，A表示第一列
				for ($currentColumn = $Column; $currentColumn <= $allColumn; $currentColumn++) {
					$address = $currentColumn . $currentRow; //数据坐标
					//读取到的数据，保存到数组$arr中
					$arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
				}
			}
			$return = array('status' => 1, 'info' => $arr);
		}
		return $return;
	}

	/**
	 * [_getEtc 判断后缀名称]
	 */
	private function _getEtc($savename) {
		return substr(strrchr($savename, '.'), 1);
	}

	public function write($expTitle, $expCellName, $expTableData) {
		//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
		Loader::import('PHPExcel.PHPExcel');
		Loader::import('PHPExcel.PHPExcel.Writer.Excel5');
		Loader::import('PHPExcel.PHPExcel.IOFactory.php');
		//对数据进行检验
		if (empty($expTableData) || !is_array($expTableData)) {
			die("data must be a array");
		}
		//检查文件名
		if (empty($expTitle)) {
			exit;
		}

		$filename = $expTitle . '_' . date('YmdHis');
		//创建PHPExcel对象，注意，不能少了\
		$objPHPExcel = new \PHPExcel();
		$objProps = $objPHPExcel->getProperties();

		//设置表头
		$key = ord("A");
		foreach ($expCellName as $v) {
			$colum = chr($key);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
			$key += 1;
		}

		$column = 2;
		$objActSheet = $objPHPExcel->getActiveSheet();
		foreach ($expTableData as $key => $rows) {
			//行写入
			$span = ord("A");
			foreach ($rows as $keyName => $value) {
				// 列写入
				$j = chr($span);
				$objActSheet->setCellValue($j . $column, $value);
				$span++;
			}
			$column++;
		}

		$fileName = iconv("utf-8", "gb2312", $filename);
		//设置活动单指数到第一个表,所以Excel打开这是第一个表
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=$fileName.xls");
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); //文件通过浏览器下载
		exit;
	}
}
?>