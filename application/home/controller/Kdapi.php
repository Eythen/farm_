<?php
/**
 * 快递鸟Api
 * 
 */
namespace app\home\controller;
use think\Controller;
use app\home\controller\Express;
class Kdapi extends Base {
    //电商ID
    const EBusinessID ='1292385' ; //('EBusinessID', '请到快递鸟官网申请http://kdniao.com/reg');
    //电商加密私钥，快递鸟提供，注意保管，不要泄漏
    const AppKey = '8632a0b8-bc0b-4442-a145-a1523938fcef';      //('AppKey', '请到快递鸟官网申请http://kdniao.com/reg');
    //请求url
    const ReqURL = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';
    //const ReqURL = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';

    //快递状态
    private $status = ['0'=>'无轨迹','1'=>'已揽收', '2'=>'在途中', '201'=>'到达派件城市', '3'=>'签收', '4'=>'问题件'];
    //调用查询物流轨迹
    //---------------------------------------------

    /**
     * [index 账号列表]
     */
    public function index(){
        

        /*$logisticResult = $this->getOrderTracesByJson();
        dump($logisticResult);

        $json = $logisticResult;
        $json = json_decode($json, true);

        dump($json);*/

        //---------------------------------------------
        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where =[];
            $where['is_delete'] = 0;
            if($request['search']){
                $where['sn'] = ['like', $request['search']];
            }
            
            $data['rows'] = db('kuaidi')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('kuaidi')->where($where)->whereor($whereor)->count();

            /*$ur = model('Users')->getAll(); //获取全部用户
            $ur = array_column($ur, 'username', 'uid');*/

            $status = $this->status;
            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['update_userid'] = get_admin_name($v['update_userid']);

                //由签收与问题件状态去判断是否去读取物流api
                if($v['status'] !=3 && $v['status'] !=4){
                    $logisticResult = $this->getOrderTracesByJson($v['name_code'], $v['sn']);

                    $ar = json_decode($logisticResult, true);
                   // dump($ar);
                    $map['id'] = $v['id'];
                    $updata['status'] = $ar['State'];
                    $updata['result'] = serialize($ar);
                    db('kuaidi')->where($map)->update($updata);

                    $data['rows'][$k]['status'] = $status[$ar['State']];
                }
                else{
                    $data['rows'][$k]['status'] = $status[$v['status']];
                }
                $data['rows'][$k]['userid'] = get_admin_name($v['userid']);
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['name_code'] = model('Kuaidi')->getName($v['name_code']);
            }
            
            
            return $data;

           // dump($request);

        }
        return $this->fetch();
    }

//    public function test(){
//        $num = input('num');
//        $express = new Express();
//        $com = $express->getComCode($num);
//        $res = $express->getOrderTraces($com,$num);
//        dump($res);
//    }

    /**
     * Json方式 单号识别
     */
    public function getKuaidiNameByJson(){
        if(request()->isAjax()){
            $request = input('request.');

            dump($request);

            $requestData= "{'LogisticCode':'885432877610751286'}";
            $datas = array(
                'EBusinessID' => self::EBusinessID,
                'RequestType' => '2002',
                'RequestData' => urlencode($requestData) ,
                'DataType' => '2',
            );
            $datas['DataSign'] = $this->encrypt($requestData, self::AppKey);
            $result = $this->sendPost(self::ReqURL, $datas);   
            
            //根据公司业务处理返回的信息......
            
            return $result;
            
        }
    }
     
    /**
     * [getOrderTracesByJson Json方式 查询订单物流轨迹]
     * @param  [type] $ShipperCode  [物流代号编码]
     * @param  [type] $LogisticCode [物流单号]
     * @return [type]               [description]
     */
    public function getOrderTracesByJson($ShipperCode, $LogisticCode){
        $requestData= "{'OrderCode':'','ShipperCode':'".$ShipperCode."','LogisticCode':'".$LogisticCode."'}";
        
        $datas = array(
            'EBusinessID' => self::EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, self::AppKey);
        $result = $this->sendPost(self::ReqURL, $datas);   
        
        //根据公司业务处理返回的信息......
        
        return $result;
    }
     
    /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    public function sendPost($url, $datas) {
        $temps = array();   
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);      
        }   
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;   
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);  
        
        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容   
     * @param appkey Appkey
     * @return DataSign签名
     */
    public function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

    /**
     * [edit 编辑]
     * @return [type] [description]
     */
    public function edit(){
        $id = input('id',0,'intval');

        $cat = model('Kuaidi')->getCat();
        $this->assign('cat',$cat);

        //获取用户上次添加的快递代号
        $where_max = "userid =".session('uid');
        $max_id = db('kuaidi')->where($where_max)->max('id');
        if($max_id){
            $where_last['id'] = $max_id;
            $last_name_code = db('kuaidi')->where($where_last)->value('name_code');
            $this->assign('last_name_code',$last_name_code);
        }
        //获取用户上次添加的快递代号end



        if( $id ){
            $data = db('kuaidi')->find($id);
            $this->assign('data',$data);
        }
        if( request()->isPost() ){
            $request = input('request.');

            $id = $request['id'];
            unset($request['id']);
			
			
            if( $id ){
                $where['id'] = $id;
                $data2 = [
                    'sn' => $request['sn'],
                    'name_code' => $request['name_code'],
                    'update_userid' => session('uid'),
                    'update_time' => time()
                    ];
                $map['sn'] =  $request['sn'];
                $map['id'] =  ['neq', $id];
                $fr = db('kuaidi')->where($map)->find();
                if($fr){
                    $this->error('已经存在这个单号，请使用搜索！');
                }    
                $result = db('kuaidi')->where($where)->update($data2);
                //比较数据更新的内容
                $content = '';    
                $data3 = [
                    'sn' => $data['sn'],
                    'name_code' => $data['name_code'],
                    ];   
                foreach ($data3 as $k => $v) {
                    if(!in_array($v, $data2)){
                        $content .= $k."：".$data3[$k]. "->". $data2[$k]."；";
                    }
                }
                //写入更改记录 
                $log = [
                    'kd_id' => $id,
                    'content' => $content,
                    'update_userid' => session('uid'),
                    'update_time' => time(),

                    ];
                db('kuaidi_log')->insert($log);
                       
                if( $result ){
                    $this->success(config('MSG.UPDATE_SUCCESS'));
                }else{
                    $this->error(config('MSG.UPDATE_ERROR'));
                }
            }else{
				$request['sn'] = $request['sn']."\n";
				preg_match_all('/(.*?)\\n/',$request['sn'], $matchs);
				
				foreach($matchs[0] as $v){
					$r[] = trim($v);
				}
				$r = array_unique($r); //过滤重复的
				$r = array_filter($r); //过滤空的	

				
				foreach($r as $vv){
					$map['sn'] =  $vv;
					$fr = db('kuaidi')->where($map)->find();
					if($fr){
						$exif[]=$vv;
						continue; //跳过数据库已经存在的
					}

					$data2[] = [
						'sn' => $vv,
						'name_code' => $request['name_code'],
						'userid' => session('uid'),
						'add_time' => time()
						];
				}
				
				if(isset($data2)){
					$result = db('kuaidi')->insertAll($data2);
				}
				else{
					if(!empty($exif)){
						$this->error(implode(',', $exif)."这以前存在！请留意处理！");
					}
				}
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
     * [getStatusInfo 获取单号的物流详情]
     * @return [type] [description]
     */
    public function getStatusInfo(){

            $request = input('request.');

            $where['id'] = $request['id'];
            $data = db('kuaidi')->where($where)->find();
            $data['result'] = unserialize($data['result']);

            $data['info'] = $data['result']['Traces']; //获取物流信息
            $data['name'] = model('Kuaidi')->getName($data['name_code']);; //获取物流名称

            krsort($data['info']);                     //键名降序处理
            $this->assign('data', $data);

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
            $data['is_delete'] = 1;
            $data['update_userid'] = session('uid');
            $data['update_time'] = time();
            $result = db('kuaidi')->where($where)->update($data);



            if( $result ){
                //写入更改记录
                foreach ($id as $k => $v) {
                    $log = [
                        'kd_id' => $v,
                        'content' => '删除id='.$v,
                        'update_userid' => session('uid'),
                        'update_time' => time(),

                        ];
                    db('kuaidi_log')->insert($log);
                 } 
                $this->success(config('MSG.DELETE_SUCCESS'));
            }else{
                $this->success(config('MSG.DELETE_ERROR'));
            }
        }
    }


    /**
     * [log 记录列表]
     */
    public function log(){

        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where =[];
            if($request['userid']){
                $where['userid'] = ['eq', $request['userid']];
            }
            if($request['search']){
                $where['content'] = ['like', '%'.$request['search'].'%'];
            }


            $data['rows'] = db('kuaidi_log')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('kuaidi_log')->where($where)->whereor($whereor)->count();


            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['update_userid'] = get_admin_name($v['update_userid']);
            }
            return $data;

           // dump($request);

        }
        return $this->fetch();
    }

    /**
     * [logOne 单个记录列表]
     */
    public function logOne($kd_id){
  
        if(request()->isAjax()){
            $request = input('request.');
   
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where =[];
            $where['kd_id'] = ['eq', $kd_id];
            /*if($request['search']){
                $where['sn'] = ['like', '%'.$request['search'].'%'];
              }*/

            $data['rows'] = db('kuaidi_log')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('kuaidi_log')->where($where)->whereor($whereor)->count();


            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['update_userid'] = get_admin_name($v['update_userid']);
            }
            return $data;

           // dump($request);

        }
        $this->assign('kd_id', $kd_id);
        return $this->fetch();
    }
    
    /**
     * [deleteLog 删除记录]
     * @return [type] [description]
     */
    public function deleteLog(){
        if( request()->isPost() ){

            $request = input('request.');
            $ids = $request['id'];
            $id =explode(',', $ids);
            $id = array_filter($id);


            $where['id'] = ['in', $id];
            $data['is_delete'] = 1;
            $result = db('kuaidi_log')->where($where)->update($data);


            if( $result ){
                $this->success(config('MSG.DELETE_SUCCESS'));
            }else{
                $this->success(config('MSG.DELETE_ERROR'));
            }
        }
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
        $file = request()->file('Filedata');

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
             $result = db('kuaidi')->insertAll($ar);
        }
        if($result){
            $return = array('status'=>1, 'info'=>'添加成功');
        }
        else{
            $return = array('status'=>0, 'info'=>'添加失败，请重新上传！');
        }
        
        return json_encode($return);
    } 

    
    
    
}