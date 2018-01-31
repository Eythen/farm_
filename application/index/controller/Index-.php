<?php
namespace app\index\controller;
use think\Controller;
use think\Request;

class Index extends Controller
{
    public function index()
    {
        if(request()->isPost()){
        	$request = input('request.');

        	$where['sn'] = $request['sn'];
        	$sn = db('sn')->where($where)->find();

        	$ip = Request::instance()->ip();
        	//$ip = Request()->ip();

        	if($sn){
	        	$data = [
	        		'ip' => $ip,
	        		'num' =>$sn['num']+1,
	        		'update_time' => time()
	        	];
	        	$map['id'] = $sn['id'];
	        	db('sn')->where($map)->update($data);

	        	$log = [
	        		'ip' => $ip,
	        		'sn' => $request['sn'],
	        		'type' => 1,
	        		'update_time' => time()
	        	];

	        	if($sn['num'] < 1){
	        		$msg = '你的防伪码是正品';
	        	}
	        	else{
	        		$msg = '你的防伪码被查询过多，可能是盗品，详情请咨询客服！';
	        	}
	        	$code = 1;
	        	 	
        		
        	}
        	else{
        		$log = [
	        		'ip' => $ip,
	        		'sn' => $request['sn'],
	        		'type' => 2,
	        		'update_time' => time()
	        	];

	        	$msg = '你的防伪码查询不到，可能是盗版！详情请咨询客服！';
	        	$code = 0;

        	}
        	//添加操作日志
	        db('sn_log')->insert($log);

	        $return = ['code' => $code, 'msg' => $msg];
	        return $return;

        }
        return $this->fetch();
        //return $this->fetch();
        //return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }

    public function weiui(){
    	echo strtotime('2017-07-01');
    	
    	return $this->fetch();
    }
}
