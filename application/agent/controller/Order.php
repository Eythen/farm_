<?php
/**
 * 订单模块
 */
namespace app\agent\controller;
use \think\Controller;

class Order extends Base {
	
	/*
     * 初始化操作
     */
    public function _initialize() {
		if(session('user_id') && session('user')['manager_mobile']){
            //$this->redirect('Users/index');
        }
        else{
            if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
                $this->redirect('Loginweixin/index');
            }
            else{
                $this->redirect('Login/index');
            }
        }
	}
	
    //订单列表
    public function index(){
    	if(request()->isAjax()){
	    	$map['user_id'] = session('user_id');
	    	$map['pay_status'] = 1;
	    	$page = input('num')? input('num'):1;
	    	$limit =  input('size')? input('size'):10;
	    	$offset = ($page-1)*$limit;
	    	$order = model('Agent')->orderlist($map, $offset, $limit);
	    	$data = [];
	    	if($order){
		    	foreach ($order as $k => $v) {
					$data[$k]["pdorderform"] = $v['order_id'];
					$data[$k]["pdtotal"] = $v['order_amount'];
					$data[$k]["pdamount"] = $v['child_num'];
					$data[$k]["pdprice"] = $v['order_amount'] - $v['used_amount'];
					$data[$k]["pdurl"] = url('pick', ['order_id' => $v['order_id'] ]);
					$data[$k]["pdlink"] = url('suborders', ['order_id' => $v['order_id'] ]);	    		
		    	}
	    	}

    		return $data;
    	}

        return view();
	}

	//提货
	public function pick(){
		$order_id = input('order_id/d');
		if(!$order_id){
			$this->error('订单号不存在！');
		}
		$map['user_id'] = session('user_id');
		$map['order_id'] = $order_id;
		$order = db('agent_order')->where($map)->find();
		if(!$order){
			$this->error('订单不存在！');
		}
		if(!$order['pay_status']){
			$this->error('订单未支付不能提货！');
		}
		$spare_amount = $order['order_amount'] - $order['used_amount'];
		//折扣
		$discount = db('users')
					->alias('u')
					->join('user_level l', 'u.level = l.level_id')
					->where('u.user_id', session('user_id'))
					->value('l.discount');

		if(request()->isAjax()){
			//ids:ids, username:username, end:end, street:street, mobile:Mobile, order_id:order_id
			//获取商品
			$ids = input('ids');
			if(!$ids){
				$this->error('商品未选择！');
			}
			$ids = explode(',', $ids);
			$total_price = 0;
			$goods_r = [];
			$total_num = 0;
			foreach ($ids as $k => $v) {
				$goods_id = explode('-', $v)[0];
				$goods_num = explode('-', $v)[1];
				$goods = db('goods')->where('goods_id', $goods_id)->find();
				$total_price += $goods['shop_price'] * $goods_num;
				$total_num += $goods_num;
				$goods_r[$k] = [
					'order_id' => $order_id,
					'goods_id' => $goods_id,
					'goods_num' => $goods_num,
					'goods_price' => $goods['shop_price'],
					'goods_name' => $goods['goods_name'],
				];
			}
			$order_amount = round(($total_price * $discount /10), 2) ;  

			if($order_amount>$spare_amount){
				$this->error('提货金额超出了订单余额！');
			}

			$order = [
				'add_time' => time(),
				'order_id' => $order_id,
				'user_id' => session('user_id'),
				'name' => input('username/s'),
				'mobile' => input('mobile'),
				'address' => input('end/s') . ' ' . input('street/s'),
				'total_amount' => $total_price,
				'order_amount' => $order_amount,
			];
			db()->startTrans();
			try{
				//添加订单
				$order_apply_id = db('agent_order_apply')->insertGetId($order);
				//添加订单商品
				foreach ($goods_r as $k => $v) {
					$goods_r[$k]['apply_id'] = $order_apply_id;
				}
				db('agent_order_apply_goods')->insertAll($goods_r);
				//金额更新
				db('agent_order')->where('order_id', $order_id)->setInc('used_amount',  $order_amount);
				//提货次数
				db('agent_order')->where('order_id', $order_id)->setInc('child_num',  1);
				db()->commit();
				$url = url('Order/detail', ['apply_id' => $order_apply_id] );
				$this->success($url);
			}
			catch(Exception $e){
				db()->rollback();
			}
		}

		$goods = db('goods')->field('goods_remark, original_img, goods_id, goods_name, shop_price')->order('goods_id desc')->select();
		
		$this->assign([
			'order_id' => $order_id,
			'goods' => $goods,
			'discount' => (int)$discount,
			'spare_amount' => $spare_amount,
			]);
		return view();
	}

	//子订单
	public function suborders(){
		$suborder = db('agent_order_apply')->where($map)->select();

		if(request()->isAjax()){
			$map['order_id'] = input('order_id/d');
			$map['user_id'] = session('user_id');
	    	
	    	$page = input('num')? input('num'):1;
	    	$limit =  input('size')? input('size'):10;
	    	$offset = ($page-1)*$limit;
	    	$order = model('Agent')->suborderlist($map, $offset, $limit);
	    	$data = [];
	    	if($order){
		    	foreach ($order as $k => $v) {
					$data[$k]["pdname"] = "子订单号".$v['apply_id'];
					$data[$k]["id"] = $v['apply_id'];
					$data[$k]["pdstatus"] = config('agent_order_apply_status')[$v['order_status']];
					$data[$k]["status"] = $v['order_status'];
					$goods = db('agent_order_apply_goods')->where('apply_id', $v['apply_id'])->select();
					$goods_str = '';
					foreach ($goods as $kk => $vv) {
						$goods_str .= $vv['goods_name'] . "-". $vv['goods_num'] ."件; ";
					}
					$data[$k]["pddetails"] = $goods_str;
					$data[$k]["pdprice"] = $v['order_amount'];
					$data[$k]["pdurl"] = url('detail', ['apply_id' => $v['apply_id'] ]);   		
		    	}
	    	}

    		return $data;
    	}

		$this->assign([
			'suborder' => $suborder,
			'order_id' => input('order_id/d'),
			]);
		return view();
	}

	//订单
	public function detail(){
		$map['apply_id'] = input('apply_id/d');
		$map['user_id'] = session('user_id');
		$suborder = db('agent_order_apply')->where($map)->find();
		$goods = db('agent_order_apply_goods')->where('apply_id', input('apply_id/d'))->select();

		$status = config('agent_order_apply_status')[$suborder['order_status']];
		$this->assign([
			'suborder' => $suborder,
			'goods' => $goods,
			'status' => $status,
			]);
		return view();
	}

	//订单取消
	public function cancel(){
		$map['apply_id'] = input('id/d');
		$map['user_id'] = session('user_id');
		$order = db('agent_order_apply')->where($map)->find();
		if($order_status){
			$this->error('订单不能取消，'.config('agent_order_apply_status')[$order['order_status']]);
		}

		db()->startTrans();
		try{

			$data['order_status'] = 4; //'0等待处理', '1完成', '2确认', '3无效', '4取消', '5发货'
			db('agent_order_apply')->where($map)->update($data);
			$map2['user_id'] = session('user_id');
			$map2['order_id'] = $order['order_id'];
			db('agent_order')->where($map2)->setDec('child_num', 1);
			db('agent_order')->where($map2)->setDec('used_amount', $order['order_amount']);
			db()->commit();
			$this->success('取消成功');
		}
		catch(Exception $e){
			db()->rollback();
			$this->error('取消失败');
		}
		return view();
	}

	//订单提交
    public function submit(){
    	$ids = input('ids');
    	if(!$ids){
    		$this->error('请选择相应套餐支付');
    	}
    	$order_id = model('Agent')->submit($ids);
    	if($order_id){
    		$url = url('payment',['order_id' => $order_id]);
    		$this->success($url);
    	}
    	else{
    		$this->error('提交失败');
    	}
    }

    //订单支付
    public function payment(){
    	$map['order_id'] = input('order_id/d');
    	$map['user_id'] = session('user_id');
    	$order = db('agent_order')->where($map)->find();
    	if(!$order){
    		$this->error('订单不存在');
    	}
    	if($order['pay_status']){
    		$this->error('订单已经支付');
    	}

    	$this->assign('order', $order);
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
        $status = db('agent_order')->where($map)->value('pay_status');
        $msg = url('detail', ['order_id'=> $order_id]);

        //支付成功
        if($status){
            $this->success($status);
        }
        
    }

    /**
     * [logistics 物流状态]
     * @return [type] [description]
     */
    public function logistics(){
    	$map['apply_id'] = input('apply_id/d');
    	$logistics = model('agent')->logistics($map);

    	$this->assign('logistics', $logistics);
    	return view();
    }

    /**
     * [paystatus 支付结果状态]
     * @return [type] [description]
     */
    public function paystatus(){
    	$map['order_id'] = input('order_id/d');
    	$pay_status = db('agent_order')->where($map)->value('pay_status');

    	$this->assign('pay_status', $pay_status);
    	return view();
    }
}
?>