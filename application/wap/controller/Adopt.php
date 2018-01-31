<?php
/**
 * 认养模块
 */
namespace app\wap\controller;
use app\wap\model\Adopt as AdoptModel;
use app\wap\model\Coupon as CouponModel;

class Adopt extends Base{
	//初始化
	public function _initialize() {
		
		if(!session('user_id')){
			$url = url("login/index");
			$this->redirect($url);
		}
	}

	//
    public function index(){
    	if(request()->isAjax()){
    		$request = input('request.');

            
    		$page = input('num')?input('num'):1;
            $limit = input('size')?input('size'):10;
            $offset = ($page-1)*$limit;
            $where['user_id'] = 0; //未有人认养
            $where['is_on_sale'] = 1; //在上架销售
            if(input('curNavIndex') == 0){
            	$where['pig_weight'] = ['between', [30,50]];
            	$pdcatty = "30-50斤";
            }
            elseif(input('curNavIndex') == 1){
            	$where['pig_weight'] = ['between', [51,80]];
            	$pdcatty = "51-80斤";
            }
            else{
            	$where['pig_weight'] = ['between', [81,120]];
            	$pdcatty = "81-120斤";
            }


	    	$model = new AdoptModel();
	        $data = $model->piglist($where, $offset, $limit);
	        $list = [];

	        if($data){
		        foreach ($data as $k => $v) {
					$list[$k]['pdcatty'] = $pdcatty;
					$list[$k]['pdimg'] = $v['original_img'];
					$list[$k]['id'] = $v['pig_id'];
					$list[$k]['pdnumber'] = $v['pig_name'];
					$list[$k]['pdprice'] = $v['pig_price'];
					$list[$k]['pdweight'] = $v['pig_weight'];
					$list[$k]['pdheight'] = $v['pig_long'];
					$list[$k]['pdbelly'] = $v['pig_round'];
					$list[$k]['pdvaccine'] = $v['pig_vaccine'];
					$list[$k]['pdtime'] = date('Y-m-d', $v['out_time']); 
		        }
	        	
	        }
    		return $list;
    	}

    	return view();
	}

	public function adoptselect(){
		if(request()->isAjax()){
			$ids = trim(input('ids'), ',');
			session('ids', $ids);
			$this->success('选择成功');
		}
	}

	public function adoptsubmit(){
		$ids = session('ids');
		if(!$ids){
			$url = url('index');
			$this->redirect($url);
		}
		$map['pig_id'] = ['in', $ids];
		$pigs = db('pig')->where($map)->select();
		foreach ($pigs as $k => $v) {
			$pigs[$k]['out_time'] = date('Y-m-d', $v['out_time']);
			$total += $v['pig_price'];
		}

		$coupon = new CouponModel();
		//红包
        $coupon_red = $coupon->coupon_red($total);
        //折扣
        $coupon_discount = $coupon->coupon_discount($total);
		$this->assign('total', $total);
		$this->assign([
			'pigs' => $pigs,
			'total' => $total,
			'coupon_red' => $coupon_red,
			'coupon_discount' => $coupon_discount,
			]);
		return view();
	}

	//计算总价
    public function totalPrice(){
        if(request()->isAjax()){
            $coupon_red = input('coupon_red/d');
            $coupon_discount = input('coupon_discount/d');
            //算商品总价
            $model = new AdoptModel();
            $total_money = $model->totalPrice($coupon_red, $coupon_discount);
 
            $this->success($total_money);
        }
    }


    //提交订单
    public function order(){
        if(request()->isAjax()){
            $coupon_red = input('coupon_red/d');
            $coupon_discount = input('coupon_discount/d');
            if(!session('ids') || !input('phone') || !input('username')){
            	$this->error('收货人与手机号码一定要填写');
            }
            $data = [
            	'coupon_red' => $coupon_red,
            	'coupon_discount' => $coupon_discount,
            	'mobile' => input('phone'),
            	'address' => input('end').input('endtwo'),
            	'partition' => input('picker'),
            	'consign' => input('receive'),
            	'name' => input('username'),
            	'ids' => trim(session('ids'), ','),
            	];
            	
            $model = new AdoptModel();
            $order_id = $model->order($data);
            if($order_id){
            	if(is_numeric($order_id)){
	                $url = url('payment',['order_id' => $order_id ]);
	                session('ids', null);  //提交成功后，不能再提交
	                $this->success('提交成功', '', $url);
            	}
            	else{
            		$this->error($order_id);
            	}
            }
            else{
                $this->error('提交失败');
            }
        }
    }

    public function payment(){
    	$order_id = input('order_id');
    	if(!$order_id){
    		$this->error('请选择订单支付');
    	}
    	$order = db('pig_order')->where('order_id', $order_id)->find();
        
        $this->assign([
            'must_pay' => $order['order_amount'],
            'order_id' => $order_id
            ]);
		return view();
	}

	/**
     * [findPayStatus 查看支付状态]
     * @return [type] [如果支付成功，返回订单链接]
     */
    public function findPayStatus(){
        $order_id = input('order_id');
        $map['order_id'] = ['eq', $order_id];
        $map['user_id'] = session('user_id');
        $status = db('pig_order')->where($map)->value('pay_status');
        $msg = url('detail', ['order_id'=> $order_id]);

        //支付成功
        if($status){
            $this->success($status);
        }
        
    }

    //认养协议
	public function protocol(){
		return view();
	}

	//花猪成长数据
	public function pigdata(){
		$map['pig_id'] = input('pig_id/d');
		if($map){
			$model = new AdoptModel();
			$data = $model->logDetail($map);
			$this->assign('data', $data);
		}
		return view();
	}

	//订单列表
	public function recording(){
		return view();
	}

	//ajax 猎取订单列表
	public function orderlist(){
		$map['user_id'] = session('user_id');
		if(input('curNavIndex') == 0){
			$map['order_status'] = 0;
			$pdstatus = '认养中';
		}
		elseif(input('curNavIndex') == 1){
			$map['order_status'] = 4;
			$pdstatus = '交付过程';
		}
		else{
			$map['order_status'] = 1;
			$pdstatus = '已完成';
		}
		$limit = input('size/d')?input('size/d'):10;
		$page = input('num/d')?input('num/d'):1;
		$offset = ($page-1) * $limit;
		$model = new AdoptModel();
		$list = $model->orderlist($map, $offset, $limit);
		if($list){
			foreach ($list as $k => $v) {
				$pig_id = db('pig_order_pigs')->where('order_id', $v['order_id'])->max('pig_id');
				$pig = db('pig')->where('pig_id', $pig_id)->find();
				$list[$k]["pdordernumber"] = $v['order_sn'];
				$list[$k]["pdstatus"] = $pdstatus;
				$list[$k]["pdimg"] = $pig['original_img'];
				$list[$k]["pdname"] =  "编号-".$pig['pig_name'];
				$list[$k]["pdprice"] =  $v['order_amount'];
				$list[$k]["pdoriginalprice"] =  $v['total_amount'];
				$list[$k]["pdweight"] = $pig['pig_weight'] . "kg";
				$list[$k]["pdbuild"] = $pig['pig_long'] . "cm";
				$list[$k]["pdbelly"] = $pig['pig_round'] . "cm";
				$list[$k]["pdvaccine"] =  $pig['pig_vaccine'];
				$list[$k]["pddate"] = date('Y-m-d', $pig['out_time']);
				$list[$k]["pdPieces"] = $v['pig_num'];
				$list[$k]["pdurl"] = url('detail', ['order_id'=>$v['order_id']]);
			}
		}
		return $list;
	} 

	//订单详情
	public function detail(){
		$map['user_id'] = session('user_id');
		$map['order_id'] = input('order_id/d');
		$model = new AdoptModel();
		$order = $model->orderDetail($map);
		if($order){
			$this->assign([
				'order' => $order,
				]);
		}
		else{
			$this->error('请选择正确的订单');
		}

		return view();
	}


	public function logistics(){
		$order_id = input('order_id/d');
		if(!$order_id){
			$this->error('订单号有误？');
		}
		//取快递信息
        $logistics = model('Adopt')->logistics($order_id);
        $this->assign('logistics', $logistics);
		return view();
	}

	

}
?>