<?php
/**
 * 订单支付
 */ 
namespace app\wap\controller;
use think\Controller;

class Payment extends Base {
    
    public $payment; //  具体的支付类
    public $pay_code; //  具体的支付code
 
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();                                                  
        //  订单支付提交
        /*$pay_radio = input('pay_code');
            

        if(!empty($pay_radio['pay_code'])) 
        {                         
            //$pay_radio = parse_url_param($pay_radio);
            $this->pay_code = $pay_radio['pay_code']; // 支付 code
        }
        else // 第三方 支付商返回
        {            
            $_GET = input('get.');            
            //file_put_contents('./a.html',$_GET,FILE_APPEND);    
            $this->pay_code = input('pay_code');
            unset($_GET['pay_code']); // 用完之后删除, 以免进入签名判断里面去 导致错误
        } */                       
        $this->pay_code = input('pay_code');
		if(empty($this->pay_code)){
            $url = request()->url();
            $url_data = parse_url( $url );
            if($url_data['path']){
                
                $pay_code_str = strchr($url_data['path'], 'pay_code');
                $pay_code_arr = explode('/', $pay_code_str);
                $this->pay_code = $pay_code_arr[1];
            }
        }
		
		
		//halt($this->pay_code);
        //获取通知的数据
        //$xml = $GLOBALS['HTTP_RAW_POST_DATA'];  //tp5 与 php7不支持
        $xml = file_get_contents("php://input");  
                      
        // 导入具体的支付类文件                
        include_once  "./plugins/payment/{$this->pay_code}/{$this->pay_code}.php"; // D:\wamp\www\svn_tpshop\www\plugins\payment\alipay\alipayPayment.class.php                       
        $code = '\\'.$this->pay_code; // \alipay
        $this->payment = new $code();
    }
   
    /**
     *  提交支付方式
     */
    public function getCode(){     
        
            config('TOKEN_ON',false); // 关闭 TOKEN_ON
            header("Content-type:text/html;charset=utf-8"); 

            


            $order_id = input('order_id'); // 订单id
            // 修改订单的支付方式
            $payment_arr = db('Plugin')->where("`type` = 'payment'")->column("code,name"); 
            $type = input('type');
            if($type == 'book'){
                db('book_order')->where("order_id = $order_id")->update(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));           
                $order = db('book_order')->where("order_id = $order_id")->find(); 
            }
            elseif($type == 'adopt'){
                db('pig_order')->where("order_id = $order_id")->update(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));
                $order = db('pig_order')->where("order_id = $order_id")->find(); 
            }
            else{
                db('order')->where("order_id = $order_id")->update(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));           
                $order = db('order')->where("order_id = $order_id")->find();
            }                       
            
            if($order['pay_status'] == 1){
            	$this->error('此订单，已完成支付!');
            }
            //订单支付提交
            $config_value = input('request.');
            //$config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
            //微信JS支付
           if($this->pay_code == 'weixin' && session('openid') && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
               $code_str = $this->payment->getJSAPI($order);
               exit($code_str);
           }else{
           		$code_str = $this->payment->get_code($order,$config_value);
                if($this->pay_code == 'alipayWap'){
                    
                    exit();
                }
           }
            $this->assign('code_str', $code_str); 
            $this->assign('order_id', $order_id); 
            $this->assign('pay_status', $order['pay_status']); 
            if($type == 'book'){
                return $this->fetch('payment');  // 分跳转 和不 跳转 
            }
    }

    public function getPay(){
    	//手机端在线充值
        config('TOKEN_ON',false); // 关闭 TOKEN_ON 
        header("Content-type:text/html;charset=utf-8");
        $order_id = input('order_id'); //订单id
        $user = session('user');
        $data['account'] = input('account');
        if($order_id>0){
        	db('recharge')->where(array('order_id'=>$order_id,'user_id'=>$user['user_id']))->update($data);
        }else{
        	$data['user_id'] = $user['user_id'];
        	$data['nickname'] = $user['nickname'];
        	$data['order_sn'] = 'recharge'.get_rand_str(6,1);
        	$data['ctime'] = time();
        	$order_id = db('recharge')->add($data);
        }
        if($order_id){
        	$order = db('recharge')->where("order_id = $order_id")->find();
        	if(is_array($order) && $order['pay_status']==0){
        		$order['order_amount'] = $order['account'];
        		$config_value = input('request.');
        		//$config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
        		$payment_arr = db('Plugin')->where("`type` = 'payment'")->column("code,name");
        		db('recharge')->where("order_id = $order_id")->update(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));       		
        		//微信JS支付
        		if($this->pay_code == 'weixin' && session('openid') && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
        			$code_str = $this->payment->getJSAPI($order);
        			exit($code_str);
        		}else{
        			$code_str = $this->payment->get_code($order,$config_value);
        		}
        	}else{
        		$this->error('此充值订单，已完成支付!');
        	}
        }else{
        	$this->error('提交失败,参数有误!');
        }
        $this->assign('code_str', $code_str); 
        $this->assign('order_id', $order_id); 
    	return $this->fetch('recharge'); //分跳转 和不 跳转
    }
        // 服务器点对点 // http://m.septfarm.com/index.php/index/Payment/notifyUrl        
        public function notifyUrl(){            
            $this->payment->response();            
            exit();
        }

        // 页面跳转 // http://m.septfarm.com/index.php/index/Payment/returnUrl        
        public function returnUrl(){
            $result = $this->payment->respond2(); // $result['order_sn'] = '201512241425288593';  
            if(stripos($result['order_sn'],'book') !== false)
            {
            	if (stripos($order_sn,'_2_') !== false) {
                    $order = db('book_order')->where("order_sn2 = '{$result['order_sn']}'")->find();
                    $this->redirect('wap/book/orderDetail', ['order_id' => $order['order_id'] ]) ;
                }
                else{
                    $order = db('book_order')->where("order_sn = '{$result['order_sn']}'")->find();
                    $this->redirect('wap/book/orderDetail', ['order_id' => $order['order_id'] ]) ;
                }
            	/*$this->assign('order', $order);
            	if($result['status'] == 1)
            		return $this->fetch('recharge_success');
            	else
            		return $this->fetch('recharge_error');
            	exit();*/
            }
            elseif(stripos($result['order_sn'],'adopt') !== false){
                $order = db('pig_order')->where("order_sn = '{$result['order_sn']}'")->find();
                $this->redirect('wap/adopt/detail', ['order_id' => $order['order_id'] ]) ;
            }  
            else{
                $order = db('order')->where("order_sn = '{$result['order_sn']}'")->find();
                $this->redirect('wap/cart/details', ['order_id' => $order['order_id'] ]) ;
            }        

            /*$this->assign('order', $order);
            if($result['status'] == 1)
                return $this->fetch('success');   
            else
                return $this->fetch('error'); */  
        }                
}
