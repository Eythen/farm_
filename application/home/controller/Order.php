<?php
/**
 * Author: 当燃
 * Date: 2015-09-09
 */
namespace app\home\controller;
use app\home\logic\Orders;
//use Think\AjaxPage;
use app\home\controller\Express;

class Order extends Base {
    public  $order_status;
    public  $pay_status;
    public  $shipping_status;
    /*
     * 初始化操作
     */
    public function _initialize() {


        parent::_initialize();

        config('TOKEN_ON',false); // 关闭表单令牌验证
        $this->order_status = config('ORDER_STATUS');
        $this->pay_status = config('PAY_STATUS');
        $this->shipping_status = config('SHIPPING_STATUS');
        // 订单 支付 发货状态
        $this->assign('order_status',$this->order_status);
        $this->assign('pay_status',$this->pay_status);
        $this->assign('shipping_status',$this->shipping_status);
    }

    /*
     *订单首页
     */
    public function index(){
    	$begin = date('Y/m/d',(time()-30*60*60*24));//30天前
    	$end = date('Y/m/d',strtotime('+1 days')); 	
    	$this->assign('timegap',$begin.'-'.$end);
        return $this->fetch();
    }

    /*
     *Ajax首页
     */
    public function ajaxindex(){
        //$orderLogic = model('Orders', 'logic');       
        $orderLogic = new Orders();       
        $timegap = input('timegap');
        $begin = '';
        $end = '';
        /*dump($timegap);
        dump(request());*/
        if($timegap){
        	$gap = explode('-', $timegap);
        	$begin = strtotime($gap[0]);
        	$end = strtotime($gap[1]);
        }
        // 搜索条件
        $condition = array();
        input('consignee') ? $condition['consignee'] = trim(input('consignee')) : false;
        if($begin && $end){
        	//$condition['add_time'] = array('between',"$begin,$end");
        }
        input('order_sn') ? $condition['order_sn'] = trim(input('order_sn')) : false;
        input('order_status') != '' ? $condition['order_status'] = input('order_status') : false;
        input('pay_status') != '' ? $condition['pay_status'] = input('pay_status') : false;
        input('pay_code') != '' ? $condition['pay_code'] = input('pay_code') : false;
        input('shipping_status') != '' ? $condition['shipping_status'] = input('shipping_status') : false;
        input('user_id') ? $condition['user_id'] = trim(input('user_id')) : false;
        $sort_order = input('order_by')?input('order_by').' desc':'order_id desc';
        //$sort_order = input('order_by') input('order_by')?:'DESC'.' '.input('sort');
        /*$count = db('order')->where($condition)->count();
        $Page  = new AjaxPage($count,20);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            $Page->parameter[$key]   =  urlencode($val);
        }
        $show = $Page->show();*/
        //$orderList = $orderLogic->getOrderList($condition,$sort_order,$Page->firstRow,$Page->listRows);
        //获取订单列表
        $page_num = 20;
        $orderList = $orderLogic->getOrderList($condition,$sort_order,$page_num);
        $page = $orderList->render(); //分页
        $this->assign('orderList',$orderList);
        $this->assign('page',$page);// 赋值分页输出
        return $this->fetch();
    }

    
    /*
     * ajax 发货订单列表
    */
    public function ajaxdelivery(){
    	$orderLogic = new Orders();
    	$condition = array();
    	input('consignee') ? $condition['consignee'] = trim(input('consignee')) : false;
    	input('order_sn') != '' ? $condition['order_sn'] = trim(input('order_sn')) : false;
    	$shipping_status = input('shipping_status');
    	//$condition['shipping_status'] = empty($shipping_status) ? array('neq',1) : $shipping_status;
        $condition['order_status'] = array('in','1,2,4');
    	/*$count = db('order')->where($condition)->count();
    	$Page  = new AjaxPage($count,10);
    	//搜索条件下 分页赋值
    	foreach($condition as $key=>$val) {
    		$Page->parameter[$key]   =   urlencode($val);
    	}
    	$show = $Page->show();*/
        //halt($condition);

    	$orderList = db('order')->where($condition)->order('add_time DESC')->paginate(10);
        $page = $orderList->render();
    	$this->assign('orderList',$orderList);
    	$this->assign('page',$page);// 赋值分页输出
    	return $this->fetch();
    }
    
    /**
     * 订单详情
     * @param int $id 订单id
     */
    public function detail($order_id){
        $order_id = (int)$order_id;
        $orderLogic = new Orders();
        $order = $orderLogic->getOrderInfo($order_id);
        $orderGoods = $orderLogic->getOrderGoods($order_id);
        //dump($orderGoods);
        $button = $orderLogic->getOrderButton($order);
        // 获取操作记录
        $action_log = db('order_action')->where(array('order_id'=>$order_id))->order('log_time desc')->select();
        $this->assign('order',$order);
        $this->assign('action_log',$action_log);
        $this->assign('orderGoods',$orderGoods);
        $split = count($orderGoods) >1 ? 1 : 0;
        foreach ($orderGoods as $val){
        	if($val['goods_num']>1){
        		$split = 1;
        	}
        }
        $this->assign('split',$split);
        $this->assign('button',$button);
        return $this->fetch();
    }

    /**
     * 订单编辑
     * @param int $id 订单id
     */
    public function edit_order(){
    	$order_id = input('order_id');
        $orderLogic = new Orders();
        $order = $orderLogic->getOrderInfo($order_id);
        if($order['shipping_status'] != 0){
            $this->error('已发货订单不允许编辑');
            exit;
        } 
    
        $orderGoods = $orderLogic->getOrderGoods($order_id);
                
       	if(request()->isPost())
        {
            $post = input('post.');

            $order['consignee'] = $post['consignee'];// 收货人
            /*$order['province'] = $post['province']; // 省份
            $order['city'] = $post['city']; // 城市
            $order['district'] = $post['district']; // 县*/
            $order['address'] = $post['address']; // 收货地址
            $order['mobile'] = $post['mobile']; // 手机           
            $order['invoice_title'] = $post['invoice_title'];// 发票
            $order['admin_note'] = $post['admin_note']; // 管理员备注
            $order['admin_note'] = $post['admin_note']; //                  
            $order['shipping_code'] = $post['shipping'];// 物流方式
            $order['shipping_name'] = db('plugin')->where(array('status'=>1,'type'=>'shipping','code'=>$post['shipping']))->column('name');            
            $order['pay_code'] = $post['payment'];// 支付方式            
            $order['pay_name'] = db('plugin')->where(array('status'=>1,'type'=>'payment','code'=>$post['payment']))->column('name');                            
            $goods_id_arr = $post['goods_id'];
            $new_goods = $old_goods_arr = array();
            //################################订单添加商品
            if($goods_id_arr){
            	$new_goods = $orderLogic->get_spec_goods($goods_id_arr);
            	foreach($new_goods as $key => $val)
            	{
            		$val['order_id'] = $order_id;
            		$rec_id = db('order_goods')->insert($val);//订单添加商品
            		if(!$rec_id)
            			$this->error('添加失败');
            	}
            }
            
            //################################订单修改删除商品
            $old_goods = $post['old_goods'];
            foreach ($orderGoods as $val){
            	if(empty($old_goods[$val['rec_id']])){
            		db('order_goods')->where("rec_id=".$val['rec_id'])->delete();//删除商品
            	}else{
            		//修改商品数量
            		if($old_goods[$val['rec_id']] != $val['goods_num']){
            			$val['goods_num'] = $old_goods[$val['rec_id']];
            			db('order_goods')->where("rec_id=".$val['rec_id'])->update(array('goods_num'=>$val['goods_num']));
            		}
            		$old_goods_arr[] = $val;
            	}
            }
            
            $goodsArr = array_merge($old_goods_arr,$new_goods);
            $result = calculate_price($order['user_id'],$goodsArr,$order['shipping_code'],0,$order['province'],$order['city'],$order['district'],0,0,0,0);
            if($result['status'] < 0)
            {
            	$this->error($result['msg']);
            }
       
            //################################修改订单费用
            $order['goods_price']    = $result['result']['goods_price']; // 商品总价
            $order['shipping_price'] = $result['result']['shipping_price'];//物流费
            $order['order_amount']   = $result['result']['order_amount']; // 应付金额
            $order['total_amount']   = $result['result']['total_amount']; // 订单总价

            unset($order['address2']);           
            $o = db('order')->where('order_id='.$order_id)->update($order);
            
            $l = $orderLogic->orderActionLog($order_id,'edit','修改订单');//操作日志
            if($o && $l){
            	$this->success('修改成功',url('home/Order/editprice',array('order_id'=>$order_id)));
            }else{
            	$this->success('修改失败',url('home/Order/detail',array('order_id'=>$order_id)));
            }
            exit;
        }
        // 获取省份
        $province = db('region')->where(array('parent_id'=>0,'level'=>1))->select();
        //获取订单城市
        $city =  db('region')->where(array('parent_id'=>$order['province'],'level'=>2))->select();
        //获取订单地区
        $area =  db('region')->where(array('parent_id'=>$order['city'],'level'=>3))->select();
        //获取支付方式
        $payment_list = db('plugin')->where(array('status'=>1,'type'=>'payment'))->select();
        //获取配送方式
        $shipping_list = db('plugin')->where(array('status'=>1,'type'=>'shipping'))->select();
        
        $this->assign('order',$order);
        $this->assign('province',$province);
        $this->assign('city',$city);
        $this->assign('area',$area);
        $this->assign('orderGoods',$orderGoods);
        $this->assign('shipping_list',$shipping_list);
        $this->assign('payment_list',$payment_list);
        return $this->fetch();
    }
    
    /*
     * 拆分订单
     */
    public function split_order(){
    	$order_id = input('order_id');
    	$orderLogic = new Orders();
    	$order = $orderLogic->getOrderInfo($order_id);
    	if($order['shipping_status'] != 0){
    		$this->error('已发货订单不允许编辑');
    		exit;
    	}
    	$orderGoods = $orderLogic->getOrderGoods($order_id);
    	if(IS_POST){
    		$data = input('post.');
    		//################################先处理原单剩余商品和原订单信息
    		$old_goods = input('old_goods');
    		foreach ($orderGoods as $val){
    			if(empty($old_goods[$val['rec_id']])){
    				db('order_goods')->where("rec_id=".$val['rec_id'])->delete();//删除商品
    			}else{
    				//修改商品数量
    				if($old_goods[$val['rec_id']] != $val['goods_num']){
    					$val['goods_num'] = $old_goods[$val['rec_id']];
    					db('order_goods')->where("rec_id=".$val['rec_id'])->update(array('goods_num'=>$val['goods_num']));
    				}
    				$oldArr[] = $val;//剩余商品
    			}
    			$all_goods[$val['rec_id']] = $val;//所有商品信息
    		}
    		$result = calculate_price($order['user_id'],$oldArr,$order['shipping_code'],0,$order['province'],$order['city'],$order['district'],0,0,0,0);
    		if($result['status'] < 0)
    		{
    			$this->error($result['msg']);
    		}
    		//修改订单费用
    		$res['goods_price']    = $result['result']['goods_price']; // 商品总价
    		$res['order_amount']   = $result['result']['order_amount']; // 应付金额
    		$res['total_amount']   = $result['result']['total_amount']; // 订单总价
    		db('order')->where("order_id=".$order_id)->update($res);
			//################################原单处理结束
			
    		//################################新单处理
    		for($i=1;$i<20;$i++){
    			if(!empty($_POST[$i.'_old_goods'])){
    				$split_goods[] = $_POST[$i.'_old_goods'];
    			}
    		}

    		foreach ($split_goods as $key=>$vrr){
    			foreach ($vrr as $k=>$v){
    				$all_goods[$k]['goods_num'] = $v;
    				$brr[$key][] = $all_goods[$k];
    			}
    		}

    		foreach($brr as $goods){
    			$result = calculate_price($order['user_id'],$goods,$order['shipping_code'],0,$order['province'],$order['city'],$order['district'],0,0,0,0);
    			if($result['status'] < 0)
    			{
    				$this->error($result['msg']);
    			}
    			$new_order = $order;
    			$new_order['order_sn'] = date('YmdHis').mt_rand(1000,9999);
    			$new_order['parent_sn'] = $order['order_sn'];
    			//修改订单费用
    			$new_order['goods_price']    = $result['result']['goods_price']; // 商品总价
    			$new_order['order_amount']   = $result['result']['order_amount']; // 应付金额
    			$new_order['total_amount']   = $result['result']['total_amount']; // 订单总价
    			$new_order['add_time'] = time();
    			unset($new_order['order_id']);
    			$new_order_id = db('order')->insert($new_order);//插入订单表
    			foreach ($goods as $vv){
    				$vv['order_id'] = $new_order_id;
    				unset($vv['rec_id']);
    				$nid = db('order_goods')->insert($vv);//插入订单商品表
    			}
    		}
    		//################################新单处理结束
    		$this->success('操作成功',url('home/Order/detail',array('order_id'=>$order_id)));
            exit;
    	}
    	
    	foreach ($orderGoods as $val){
    		$brr[$val['rec_id']] = array('goods_num'=>$val['goods_num'],'goods_name'=>getSubstr($val['goods_name'], 0, 35).$val['spec_key_name']);
    	}
    	$this->assign('order',$order);
    	$this->assign('goods_num_arr',json_encode($brr));
    	$this->assign('orderGoods',$orderGoods);
    	return $this->fetch();
    }
    
    /*
     * 价钱修改
     */
    public function editprice($order_id){
        $orderLogic = new Orders();
        $order = $orderLogic->getOrderInfo($order_id);
        $this->editable($order);
        if(request()->isPost()){
        	$admin_id = session('admin_id');
            if(empty($admin_id)){
                $this->error('非法操作');
                exit;
            }
            $update['discount'] = input('post.discount');
            $update['shipping_price'] = input('post.shipping_price');
			$update['order_amount'] = $order['goods_price'] + $update['shipping_price'] - $update['discount'] - $order['user_money'] - $order['integral_money'] - $order['coupon_price'];
            $row = db('order')->where(array('order_id'=>$order_id))->update($update);
            if(!$row){
                $this->success('没有更新数据',url('home/Order/editprice',array('order_id'=>$order_id)));
            }else{
                $this->success('操作成功',url('home/Order/detail',array('order_id'=>$order_id)));
            }
            exit;
        }
        $this->assign('order',$order);
        return $this->fetch();
    }

    /**
     * 订单删除
     * @param int $id 订单id
     */
    public function delete_order($order_id){
    	$orderLogic = new Orders();
    	$del = $orderLogic->delOrder($order_id);
        if($del){
            $this->success('删除订单成功');
        }else{
        	$this->error('订单删除失败');
        }
    }
    
    /**
     * 订单取消付款
     */
    public function pay_cancel($order_id){
    	if(input('remark')){
    		$data = input('post.');
    		$note = array('退款到用户余额','已通过其他方式退款','不处理，误操作项');
    		if($data['refundType'] == 0 && $data['amount']>0){
    			accountLog($data['user_id'], $data['amount'], 0,  '退款到用户余额');
    		}
    		$orderLogic = new Orders();
                $orderLogic->orderProcessHandle($data['order_id'],'pay_cancel');
    		$d = $orderLogic->orderActionLog($data['order_id'],'pay_cancel',$data['remark'].':'.$note[$data['refundType']]);
    		if($d){
    			exit("<script>window.parent.pay_callback(1);</script>");
    		}else{
    			exit("<script>window.parent.pay_callback(0);</script>");
    		}
    	}else{
    		$order = db('order')->where("order_id=$order_id")->find();
    		$this->assign('order',$order);
    		return $this->fetch();
    	}
    }

    /**
     * 订单打印
     * @param int $id 订单id
     */
    public function order_print(){
        
    	$order_id = input('order_id');
        $orderLogic = new Orders();
        $order = $orderLogic->getOrderInfo($order_id);

        $order['full_address'] = $order['address'];
        $orderGoods = $orderLogic->getOrderGoods($order_id);
        $shop = tpCache('shop_info');
        $this->assign('order',$order);
        $this->assign('shop',$shop);
        $this->assign('orderGoods',$orderGoods);
        $template = input('template','print');
        return $this->fetch($template);
    }

    /**
     * 快递单打印
     */
    public function shipping_print(){
        $code = input('param.code');
        $id = input('param.order_id');
        //查询是否存在订单及物流
        $shipping = db('plugin')->where(array('code'=>$code,'type'=>'shipping'))->find();
        if(!$shipping)
            $this->error('物流插件不存在',url('home/Index/index'));
        	$orderLogic = new Orders();
        	$order = $orderLogic->getOrderInfo($id);
        if(!$order)
            $this->error('订单不存在');
        //检查模板是否存在
        if(!file_exists(APP_PATH."Admin/View/Plugin/shipping/{$code}_print.html"))
            $this->error('请先在插件中心设置打印模板',url('home/Index/index'));
        //获取商店信息
        $shop = tpCache('shop_info');
        $order['province'] = getRegionName($order['province']);
        $order['city'] = getRegionName($order['city']);
        $order['district'] = getRegionName($order['district']);
        $order['full_address'] = $order['province'].' '.$order['city'].' '.$order['district'].' '. $order['address'];
        $this->assign('shop',$shop);
        $this->assign('order',$order);
        return $this->fetch("Plugin/shipping/{$code}_print");
    }

    /**
     * 生成发货单
     */
    public function deliveryHandle(){
        $orderLogic = new Orders();
		$data = input('post.');
        $data['shipping_name'] = db('kuaidi_cat')->where('code',$data['shipping_code'])->value('name');
		$res = $orderLogic->deliveryHandle($data);
		if($res){
			$this->success('操作成功',url('home/Order/delivery_info',array('order_id'=>$data['order_id'])));
		}else{
			$this->success('操作失败',url('home/Order/delivery_info',array('order_id'=>$data['order_id'])));
		}
    }

    
    public function delivery_info(){
    	$order_id = input('order_id');
    	$orderLogic = new Orders();
    	$order = $orderLogic->getOrderInfo($order_id);
    	$order['invoice_no'] = db('delivery_doc')->where('order_id',$order_id)->value('invoice_no');
    	$orderGoods = $orderLogic->getOrderGoods($order_id);
		$delivery_record = db('delivery_doc')->where('order_id='.$order_id)->select();
		$kuaidi = db('kuaidi_cat')->where('status=1')->select();
//		if($delivery_record){
//			//$order['invoice_no'] = $delivery_record[count($delivery_record)-1]['invoice_no'];
//		}
		$this->assign('order',$order);
		$this->assign('orderGoods',$orderGoods);
		$this->assign('delivery_record',$delivery_record);//发货记录
        $this->assign('kuaidi',$kuaidi);
    	return $this->fetch();
    }

    public function getStatusInfo(){
        $id = input('id');
        $info = db('delivery_doc')->field('shipping_code,invoice_no')->find($id);
        $express = new Express();
        $data = $express->getOrderTraces($info['shipping_code'],$info['invoice_no']);
        $this->assign('info',$info);
        $this->assign('data', $data['data']);
        return view();
    }
    
    /**
     * 发货单列表
     */
    public function delivery_list(){
        return $this->fetch();
    }
	
    /*
     * ajax 退货订单列表
     */
    public function ajax_return_list(){
        // 搜索条件        
        $order_sn =  trim(input('order_sn'));
        $order_by = input('order_by') ? input('order_by') : 'id';
        $sort_order = input('sort_order') ? input('sort_order') : 'desc';
        $status =  input('status');
        
        $where = " 1 = 1 ";
        $order_sn && $where.= " and order_sn like '%$order_sn%' ";
        empty($order_sn) && $where.= " and status = '$status' ";
        /*$count = db('return_goods')->where($where)->count();
        $Page  = new AjaxPage($count,13);
        $show = $Page->show();*/

        $list = db('return')->where($where)->order("$order_by $sort_order")->paginate(13);
        $page = $list->render();       
        $goods_id_arr = get_arr_column($list, 'goods_id');
        if(!empty($goods_id_arr))
            $goods_list = db('goods')->where("goods_id in (".implode(',', $goods_id_arr).")")->column('goods_id,goods_name');
        $this->assign('goods_list',$goods_list);
        $this->assign('list',$list);
        $this->assign('page',$page);// 赋值分页输出
        return $this->fetch();
    }
    
    /**
     * 删除某个退换货申请
     */
    public function return_del(){
        $id = input('param.id');
        db('return')->where("id = $id")->delete();
        $this->success('成功删除!');
    }

    /**
     * 退换货操作
     */
    public function return_info()
    {
        $id = input('id');
        $return_goods = db('return')->where('id','=',$id)->find();
        $user = db('users')->where('user_id','=',$return_goods['user_id'])->find();
        $goods = db('goods')->where('goods_id','=',$return_goods['goods_id'])->find();
        if(request()->isPost()){
            $data['id'] = input('id');
            $data['status'] = input('status');
            $data['remark'] = input('remark');
            $data['admin_id'] = session('uid');
            $data['open_time'] = time();
            //事务开始
            db()->startTrans();
            if ($data['status'] <= 1){// 未处理、处理中
                $res = db('return')->update($data);
            }elseif ($data['status'] == 2){//已完成
                $map['order_id'] = $return_goods['order_id'];
                $map['goods_id'] = $return_goods['goods_id'];
                $map['is_send'] = 4;
                //订单物品表物品状态改为已退货
                $is_send = db('order_goods')->where($map)->setField('is_send',3);
                //商品表增加库存
                $goods = db('goods')->where('goods_id','=',$return_goods['goods_id'])->setInc('store_count',$return_goods['goods_num']);
                if ($is_send && $goods){
                    $res = db('return')->update($data);
                }
            }
            if ($res){
                db()->commit();
                $this->success('操作成功！',url('return_list'));
            }else{
                db()->rollback();
                $this->error('操作失败！检查是否订单重复退货或者退货跳过处理！');
            }
        }
        $this->assign('id',$id);
        $this->assign('user',$user); // 用户
        $this->assign('goods',$goods);// 商品
        $this->assign('return_goods',$return_goods);// 退换货
        return $this->fetch();
    }

//    public function return_info()
//    {
//        $id = input('id');
//        $return_goods = db('return')->where("id= $id")->find();
//        $user = db('users')->where("user_id = {$return_goods['user_id']}")->find();
//        $goods = db('goods')->where("goods_id = {$return_goods['goods_id']}")->find();
//        if(request()->isPost()){
//            $data['id'] = input('id');
//            $data['status'] = input('status');
//            $data['remark'] = input('remark');
//            $data['admin_id'] = session('uid');
//            $data['open_time'] = time();
//            //事务开始
//            db()->startTrans();
//            if ($data['status'] == 1){//处理中
//                $map['type'] = 1;
//                $map['user_id'] = $return_goods['user_id'];
//                $map['order_id'] = $return_goods['order_id'];
//                $map['goods_id'] = $return_goods['goods_id'];
//                $map['status'] = 0;
//                $status = db('points_log')->where($map)->setField('status','2');
//                $res = db('return')->update($data);
//
//                //发通知短信
//                $sms_id = 5; //验证码数据库模板ID
//                $mobile = db('users')->where('user_id', $return_goods['user_id'])->value('mobile');
//                $sms_data = db('alsms_idayu_template')->find($sms_id);
//
//                $sms_data2 = [
//                        'sms_code' => $sms_data['templatecode'],
//                        'content' => $sms_data['content'],
//                        'mobile' => $mobile,
//                        'admin_id' => session('admin_id'),
//                        'add_time' => time()
//                        ];
//                $id = db('alidayu')->insertGetId($sms_data2);
//                $send = new Alidayu;
//                $send->alidayuSend($id, $mobile, $sms_data['templatecode'], $sms_data['sign_name']);
//
//            }elseif ($data['status'] == 2){//已完成
//                $map['type'] = 1;
//                $map['user_id'] = $return_goods['user_id'];
//                $map['order_id'] = $return_goods['order_id'];
//                $map['goods_id'] = $return_goods['goods_id'];
//                $map['status'] = 2;
//                $info = db('points_log')->where($map)->find();
//                if ($info['points_pay'] == $return_goods['money']){//商品全退
//                    if ($info['points_user'] == 0){//用户首单
//                        $where['order_id'] = $return_goods['order_id'];
//                        $where['status'] = 2;
//                        $return_num = db('return')->where($where)->sum('goods_num');
//                        $return_num = $return_num + $return_goods['goods_num'];
//                        $order_num = db('order_goods')->where('order_id',$return_goods['order_id'])->sum('goods_num');
//                        if ($return_num == $order_num){//一整张订单全退
//                            db('users')->where('user_id',$return_goods['user_id'])->setField('is_buy','0');
//                        }
//                    }
//                    $status = db('points_log')->where($map)->delete();
//                    //返还用户积分
//                    db('users')->where('user_id',$return_goods['user_id'])->setInc('user_money',$return_goods['money']);
//                }elseif ($info['points_pay'] > $return_goods['money']){//商品退一部分
//                    $log = array();
//                    $log['points_pay'] = $info['points_pay'] - $return_goods['money'];//商品总价
//
//                    $user = db('users')->where('user_id='.$return_goods['user_id'])->find();
//                    $level = db('user_level')->select();
//                    $goods_points = db('config')->where('name="goods_points"')->value('value');//商品返比%
//                    $points_r = array_column($level, 'points', 'level_id');//分类消费积分比例%
//                    $points_pusher_r = array_column($level, 'points_pusher', 'level_id');//分类推荐用户消费积分比例%
//
//                    $points_total = ($log['points_pay']*$goods_points)/100;//商品返比
//                    if($user['first_leader']){
//                        $pusher_level = db('users')->where('user_id='.$user['first_leader'])->value('level');
//                        $pusher_points = $points_pusher_r[$pusher_level];
//                    }
//                    $log['points_user_pusher'] = floor($log['points_pay']*$pusher_points)/100;
//                    $log['points_user'] = 0;
//                    if ($info['points_user'] != 0){//不为用户首单
//                        $my_points = $points_r[$user['level']];
//                        $log['points_user'] = floor($log['points_pay']*$my_points)/100;
//                    }
//                    $log['points_public'] = $points_total-$log['points_user']-$log['points_user_pusher'];
//                    $status = db('points_log')->where($map)->update($log);
//                    //返还用户积分
//                    db('users')->where('user_id',$return_goods['user_id'])->setInc('user_money',$return_goods['money']);
//
//
//                }
//                $res = db('return')->update($data);
//
//                //发通知短信
//                $sms_id = 6; //验证码数据库模板ID
//                $mobile = db('users')->where('user_id', $return_goods['user_id'])->value('mobile');
//                $sms_data = db('alsms_idayu_template')->find($sms_id);
//
//                $sms_data2 = [
//                        'sms_code' => $sms_data['templatecode'],
//                        'content' => $sms_data['content'],
//                        'mobile' => $mobile,
//                        'admin_id' => session('admin_id'),
//                        'add_time' => time()
//                        ];
//                $id = db('alidayu')->insertGetId($sms_data2);
//                $send = new Alidayu;
//                $send->alidayuSend($id, $mobile, $sms_data['templatecode'], $sms_data['sign_name']);
//            }
//            if ($res && $status){
//                db()->commit();
//                $this->success('操作成功！',url('return_list'));
//            }else{
//                db()->rollback();
//                $this->error('操作失败！检查是否订单重复退货或者退货跳过处理！');
//            }
//        }
//        $this->assign('id',$id);
//        $this->assign('user',$user); // 用户
//        $this->assign('goods',$goods);// 商品
//        $this->assign('return_goods',$return_goods);// 退换货
//        return $this->fetch();
//    }
    
    /**
     * 管理员生成申请退货单
     */
    public function add_return_goods()
   {                
            $order_id = input('order_id'); 
            $goods_id = input('goods_id');
                
            $return_goods = db('return_goods')->where("order_id = $order_id and goods_id = $goods_id")->find();            
            if(!empty($return_goods))
            {
                $this->error('已经提交过退货申请!',url('home/Order/return_list'));
                exit;
            }
            $order = db('order')->where("order_id = $order_id")->find();
            
            $data['order_id'] = $order_id; 
            $data['order_sn'] = $order['order_sn']; 
            $data['goods_id'] = $goods_id; 
            $data['addtime'] = time(); 
            $data['user_id'] = $order[user_id];            
            $data['remark'] = '管理员申请退换货'; // 问题描述            
            db('return_goods')->insert($data);            
            $this->success('申请成功,现在去处理退货',url('home/Order/return_list'));
            exit;
    }

    /**
     * 订单操作
     * @param $id
     */
    public function order_action(){    	
        $orderLogic = new Orders();
        $action = input('param.type');
        $order_id = input('param.order_id');
        if($action && $order_id){
        	 $a = $orderLogic->orderProcessHandle($order_id,$action);       	
        	 $res = $orderLogic->orderActionLog($order_id,$action,input('note'));
        	 if($res && $a){
        	 	exit(json_encode(array('status' => 1,'msg' => '操作成功')));
        	 }else{
        	 	exit(json_encode(array('status' => 0,'msg' => '操作失败')));
        	 }
        }else{
        	$this->error('参数错误',url('home/Order/detail',array('order_id'=>$order_id)));
        }
    }
    
    public function order_log(){

    	$timegap = input('timegap')?input('timegap'):'';
        $this->assign('timegap',$timegap );
        $begin ="";
        $end = "";
        if($timegap){
            $gap = explode('-', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }
        $condition = array();
        $log =  db('order_action');

    	if($begin && $end){
    		$condition['log_time'] = array('between',"$begin,$end");
    	}
    	$admin_id = input('admin_id');
		if($admin_id >0 ){
			$condition['action_user'] = $admin_id;
		}
    	/*$count = $log->where($condition)->count();
    	$Page = new \Think\Page($count,20);
    	foreach($condition as $key=>$val) {
    		$Page->parameter[$key] = urlencode($val);
    	}
    	$show = $Page->show();*/
    	$list = $log->where($condition)->order('action_id desc')->paginate(20);
        $page = $list->render();
    	$this->assign('list',$list);
    	$this->assign('page',$page);   	
    	$admin = db('admin')->column('uid as admin_id,username as user_name');
    	$this->assign('admin',$admin);
    	return $this->fetch();
    }

    /**
     * 检测订单是否可以编辑
     * @param $order
     */
    private function editable($order){
        if($order['shipping_status'] != 0){
            $this->error('已发货订单不允许编辑');
            exit;
        }
        return;
    }

    public function export_order()
    {
    	//搜索条件
		$where = 'where 1=1 ';
		$consignee = input('consignee');
		if($consignee){
			$where .= " AND consignee like '%$consignee%' ";
		}
		$order_sn =  input('order_sn');
		if($order_sn){
			$where .= " AND order_sn = '$order_sn' ";
		}
		if(input('order_status')){
			$where .= " AND order_status = ".input('order_status');
		}
		
		$timegap = input('timegap');
		if($timegap){
			$gap = explode('-', $timegap);
			$begin = strtotime($gap[0]);
			$end = strtotime($gap[1]);
			$where .= " AND add_time>$begin and add_time<$end ";
		}
		    
		$sql = "select *,FROM_UNIXTIME(add_time,'%Y-%m-%d') as create_time from ".config('database.prefix')."order $where order by order_id";
    	$orderList = db()->query($sql);
    	$strTable ='<table width="500" border="1">';
    	$strTable .= '<tr>';
    	$strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="100">日期</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货人</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货地址</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">电话</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">实际支付</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付方式</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付状态</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">发货状态</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品信息</td>';
    	$strTable .= '</tr>';
	    if(is_array($orderList)){
	    	$region	= db('region')->column('id,name');
	    	foreach($orderList as $k=>$val){
	    		$strTable .= '<tr>';
	    		$strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_sn'].'</td>';
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['create_time'].' </td>';	    		
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['consignee'].'</td>';
                        $strTable .= '<td style="text-align:left;font-size:12px;">'."{$region[$val['province']]},{$region[$val['city']]},{$region[$val['district']]},{$region[$val['twon']]}{$val['address']}".' </td>';                        
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['goods_price'].'</td>';
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_amount'].'</td>';
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_name'].'</td>';
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$this->pay_status[$val['pay_status']].'</td>';
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$this->shipping_status[$val['shipping_status']].'</td>';
	    		$orderGoods = db('order_goods')->where('order_id='.$val['order_id'])->select();
	    		$strGoods="";
	    		foreach($orderGoods as $goods){
	    			$strGoods .= "商品编号：".$goods['goods_sn']." 商品名称：".$goods['goods_name'];
	    			if ($goods['spec_key_name'] != '') $strGoods .= " 规格：".$goods['spec_key_name'];
	    			$strGoods .= "<br />";
	    		}
	    		unset($orderGoods);
	    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$strGoods.' </td>';
	    		$strTable .= '</tr>';
	    	}
	    }
    	$strTable .='</table>';
    	unset($orderList);
    	downloadExcel($strTable,'order');
    	exit();
    }
    
    /**
     * 退货单列表
     */
    public function return_list(){
        return $this->fetch();
    }
    
    /**
     * 添加一笔订单
     */
    public function add_order()
    {
        
        $order['province'] = input('province')? input('province'):'';
        $order['city'] = input('city')? input('city'):'';
        $order['consignee'] = input('consignee')? input('consignee'):'';
        //  获取省份
        $province = db('region')->where(array('parent_id'=>0,'level'=>1))->select();
        //  获取订单城市
        $city =  db('region')->where(array('parent_id'=>$order['province'],'level'=>2))->select();
        //  获取订单地区
        $area =  db('region')->where(array('parent_id'=>$order['city'],'level'=>3))->select();
        //  获取配送方式
        $shipping_list = db('plugin')->where(array('status'=>1,'type'=>'shipping'))->select();
        //  获取支付方式
        $payment_list = db('plugin')->where(array('status'=>1,'type'=>'payment'))->select();
        if(request()->isPost())
        {
            $order['user_id'] = input('user_id');// 用户id 可以为空
            $order['consignee'] = input('consignee');// 收货人
            $order['province'] = input('province'); // 省份
            $order['city'] = input('city'); // 城市
            $order['district'] = input('district'); // 县
            $order['address'] = input('address'); // 收货地址
            $order['mobile'] = input('mobile'); // 手机           
            $order['invoice_title'] = input('invoice_title');// 发票
            $order['admin_note'] = input('admin_note'); // 管理员备注            
            $order['order_sn'] = date('YmdHis').mt_rand(1000,9999); // 订单编号;
            $order['admin_note'] = input('admin_note'); // 
            $order['add_time'] = time(); //                    
            $order['shipping_code'] = input('shipping');// 物流方式
            $order['shipping_name'] = db('plugin')->where(array('status'=>1,'type'=>'shipping','code'=>input('shipping')))->column('name');            
            $order['pay_code'] = input('payment');// 支付方式            
            $order['pay_name'] = db('plugin')->where(array('status'=>1,'type'=>'payment','code'=>input('payment')))->column('name');            
                            
            $goods_id_arr = input("goods_id");
            $orderLogic = new Orders();
            $order_goods = $orderLogic->get_spec_goods($goods_id_arr);          
            $result = calculate_price($order['user_id'],$order_goods,$order['shipping_code'],0,$order['province'],$order['city'],$order['district'],0,0,0,0);      
            if($result['status'] < 0)	
            {
                 $this->error($result['msg']);      
            } 
           
           $order['goods_price']    = $result['result']['goods_price']; // 商品总价
           $order['shipping_price'] = $result['result']['shipping_price']; //物流费
           $order['order_amount']   = $result['result']['order_amount']; // 应付金额
           $order['total_amount']   = $result['result']['total_amount']; // 订单总价
           
            // 添加订单
            $order_id = db('order')->insert($order);
            if($order_id)
            {
                foreach($order_goods as $key => $val)
                {
                    $val['order_id'] = $order_id;
                    $rec_id = db('order_goods')->insert($val);
                    if(!$rec_id)                 
                        $this->error('添加失败');                                  
                }
                $this->success('添加商品成功',U("admin/Order/detail",array('order_id'=>$order_id)));
                exit();
            }
            else{
                $this->error('添加失败');
            }                
        }     
        $this->assign('order',null);
        $this->assign('shipping_list',$shipping_list);
        $this->assign('payment_list',$payment_list);
        $this->assign('province',$province);
        $this->assign('city',$city);
        $this->assign('area',$area);        
        return $this->fetch();
    }
    
    /**
     * 选择搜索商品
     */
    public function search_goods()
    {
    	$brandList =  db("brand")->select();
    	$categoryList =  db("goods_category")->select();
    	$this->assign('categoryList',$categoryList);
    	$this->assign('brandList',$brandList);   	
    	$where = ' is_on_sale = 1 ';//搜索条件
    	input('intro')  && $where = "$where and ".input('intro')." = 1";
    	if(input('cat_id')){
    		$this->assign('cat_id',input('cat_id'));    		
            $grandson_ids = getCatGrandson(input('cat_id')); 
            $where = " $where  and cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
                
    	}
        if(input('brand_id')){
            $this->assign('brand_id',input('brand_id'));
            $where = "$where and brand_id = ".input('brand_id');
        }
    	if(!empty($_REQUEST['keywords']))
    	{
    		$this->assign('keywords',input('keywords'));
    		$where = "$where and (goods_name like '%".input('keywords')."%' or keywords like '%".input('keywords')."%')" ;
    	}  	
    	$goodsList = db('goods')->where($where)->order('goods_id DESC')->limit(10)->select();
                
        foreach($goodsList as $key => $val)
        {
            $spec_goods = db('spec_goods_price')->where("goods_id = {$val['goods_id']}")->select();
            $goodsList[$key]['spec_goods'] = $spec_goods;            
        }
    	$this->assign('goodsList',$goodsList);
    	return $this->fetch();        
    }
    
    public function ajaxOrderNotice(){
        $order_amount = db('order')->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();
        echo $order_amount;
    }
}
