<?php

namespace app\wap\controller;
use app\wap\model\Book as BookModel;

class Book extends Base
{
    //初始化
    public function _initialize() {
        
        if(!session('user_id')){
            $url = url("login/index");
            $this->redirect($url);
        }
    }

    public function index(){
        $user_id = session('user_id');
        if(empty($user_id)){
            $this->redirect('login/index');
        }
        if(request()->isAjax()){
            $request = input('request.');
            $data = [
                'user_id' => session('user_id'),
                'add_time' => time(),
                'last_update' => time(),
                'mobile' => $request['mobile'],
                'name' => $request['name'],
                'remark' => $request['remark'],
                'at_time' => $request['at_time'],
                'people_num' => $request['people_num'],
                ];
            $has = db('book_temp')->where('user_id', $user_id)->find(); 
            if($has){
                $r = db('book_temp')->where('user_id', $user_id)->update($data);
            }
            else{
                $r = db('book_temp')->insert($data);
            }  
            if($r){
                $this->success('提交成功');
            }
            else{
                $this->error('提交失败');
            }
            
        }
        $temp = db('book_temp')->where('user_id', $user_id)->find();
        
        $this->assign([
            'temp' => $temp
            ]);
        return view();
    }

    public function table(){
        /*
        
        1，先查订单有没有被订桌了
        2， 选择时再查有没有被订桌了
        3，订桌
         */
        if(request()->isAjax()){
            $request = input('request.');
            $data = [
                'last_update' => time(),
                'table' => $request['table']
                ];
            $id = db('book_temp')->where('user_id', session('user_id'))->value('id');
            $r = db('book_temp')->where('id', $id)->update($data);
            if($r){
                $this->success('提交成功');
            }
            else{
                $this->error('提交失败');
            }
            
        }
        //判断选择时间了没
        $model = new BookModel();
        $at_time = db('book_temp')->where('user_id', session('user_id'))->value('at_time');
        $at_time_unix = $model->atTimeUnix($at_time);
 
        $time = time();
        if($at_time_unix < $time){
            $this->error('用餐时间已经过期！请重新选择！', url('index'));
        }
        //查询这个时间中的订单桌被订了的
        $model = new BookModel();
        $booked = $model->booked($at_time);
        $this->assign('booked', $booked);

        return view();
    }
    //购物车添加商品
    public function ordering(){
        if(request()->isAjax()){
            $request = input('request.');
            $combo = $request['combo'];
            $goods = $request['goods'];
            $combo = json_decode($combo, true);
            $goods = json_decode($goods, true);
            db()->startTrans();
            try{
                if($goods){
                    $data = [];
                    foreach ($goods as $k => $v) {
                        $goodsinfo = db('book_goods')
                                    ->field('goods_id, goods_name, goods_remark, shop_price')
                                    ->where('goods_id', $v['id'])
                                    ->find();
                        $data[$k]['goods_id'] = $v['id'];
                        $data[$k]['goods_num'] = $v['num'];
                        $data[$k]['goods_price'] = $goodsinfo['shop_price'];
                        $data[$k]['goods_name'] = $goodsinfo['goods_name'];
                        $data[$k]['goods_remark'] = $goodsinfo['goods_remark'];
                        $data[$k]['user_id'] = session('user_id');
                        $data[$k]['session_id'] = SESSION_ID;
                        $data[$k]['add_time'] = time();
                        $data[$k]['selected'] = 1;
                        //查用户购物车
                        $map['user_id'] = session('user_id');
                        $map['goods_id'] = $v['id'];
                        $has_num = db('book_cart')->where($map)->value('goods_num');
                        if($has_num){
                            $data[$k]['goods_num'] = $v['num'] + $has_num;
                            db('book_cart')->where($map)->update($data[$k]);
                        }
                        else{
                            $r = db('book_cart')->insert($data[$k]);
                        }
                    }
                }
                if($combo){
                    $comb_data = [];
                    foreach ($combo as $k => $v) {
                        $comboinfo = db('book_combo')
                                    ->field('id, goods_name, goods_remark, shop_price')
                                    ->where('id', $v['id'])
                                    ->find();
                        $comb_data[$k]['combo_id'] = $v['id'];
                        $comb_data[$k]['goods_num'] = $v['num'];
                        $comb_data[$k]['goods_price'] = $comboinfo['shop_price'];
                        $comb_data[$k]['goods_name'] = $comboinfo['goods_name'];
                        $comb_data[$k]['goods_remark'] = $comboinfo['goods_remark'];
                        $comb_data[$k]['user_id'] = session('user_id');
                        $comb_data[$k]['session_id'] = SESSION_ID;
                        $comb_data[$k]['add_time'] = time();
                        $comb_data[$k]['selected'] = 1;
                        //查用户购物车
                        $combo_map['user_id'] = session('user_id');
                        $combo_map['combo_id'] = $v['id'];
                        $has_combo_num = db('book_cart')->where($combo_map)->value('goods_num');
                        if($has_combo_num){
                            $comb_data[$k]['goods_num'] = $v['num'] + $has_num;
                            db('book_cart')->where($combo_map)->update($comb_data[$k]);
                        }
                        else{
                            $r = db('book_cart')->insert($comb_data[$k]);
                        }
                    }
                }
                db()->commit();  
                $this->success('提交成功');
            } catch (Exception $e){
                // 回滚事务
                db()->rollback();
                $this->error('提交失败');
            }

        }
        //套餐列表
        $combo = db('book_combo')->order('id desc')->select();

        foreach ($combo as $k => $v) {
            $map['goods_id'] = ['in', $v['goods_content'] ];
            $combo_goods = db('book_combo_goods')->where($map)->order('goods_id desc')->select();
            $combo[$k]['combo_goods'] = $combo_goods;
        }
        

        $this->assign([
                'combo' => $combo
            ]);

        return view();
    }

    //单品列表
    public function goodslist(){
        if(request()->isAjax()){
            $request = input('get.');
            $num = $request['num'];
            $size = $request['size'];
            $offset = ($num-1)*$size;
            //单品列表
            $goods = db('book_goods')
                    ->field('goods_id, goods_name, shop_price, goods_remark, original_img')
                    ->order('goods_id desc')
                    ->limit($offset, $size)
                    ->select();
            return $goods;
            
        }
    }

    //购物车操作
    public function cart(){
        if(request()->isAjax()){
            $request = input('request.');
            $map = [
                'user_id' => session('user_id'),
                'id' => $request['id']
                ];    
            if($request['act'] == 'selected'){
                $data = [
                    'selected' => $request['selected']
                    ];
                $result = db('book_cart')->where($map)->update($data);
            }
            if($request['act'] == 'num'){
                $data = [
                    'goods_num' => $request['goods_num']
                    ];
                $result = db('book_cart')->where($map)->update($data);
            }
            if($request['act'] == 'delete'){
                $result = db('book_cart')->where($map)->delete();
            }
            if($request['act'] == 'selectedAll'){
                $data = [
                    'selected' => $request['selected']
                    ];
                $mapall['user_id'] = session('user_id');
                $result = db('book_cart')->where($mapall)->update($data);
            }
            if($result !== 'false'){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }
        }
        $map['user_id'] = session('user_id');
        $list = db('book_cart')->where($map)->order('combo_id desc, goods_id desc')->select();
        foreach ($list as $k => $v) {
            if($v['combo_id']>0){
                $combo = db('book_combo')->where('id', $v['combo_id'])->find();
                $list[$k]['goods_name'] = $combo['goods_name'];
                $list[$k]['goods_remark'] = $combo['goods_remark'];
                $list[$k]['goods_price'] = $combo['shop_price'];
                $list[$k]['original_img'] = $combo['original_img'];
                $list[$k]['type'] = 'combo';
            }
            if($v['goods_id']>0){
                $goods = db('book_goods')->where('goods_id', $v['goods_id'])->find();
                $list[$k]['goods_name'] = $goods['goods_name'];
                $list[$k]['goods_remark'] = $goods['goods_remark'];
                $list[$k]['goods_price'] = $goods['shop_price'];
                $list[$k]['original_img'] = $goods['original_img'];
                $list[$k]['type'] = 'goods';
            }
        }
        $this->assign('list', $list);
        return view();
    }

    //提交前浏览
    public function reserved(){
        $user = db('book_temp')->where('user_id', session('user_id'))->order('id desc')->find();
        if(!$user['table']){
            $this->error('请先订桌！');
        }
        $tables = explode(',', $user['table']);
        $tables = array_filter($tables);
        $user['seat'] = "";
        foreach ($tables as $k => $v) {
            $user['seat'] .= config('seat')[$v].",";
        }
        
        $user['seat'] = trim($user['seat']);
        $map['user_id'] = session('user_id');
        $map['selected'] = 1;
        $list = db('book_cart')->where($map)->order('combo_id desc, goods_id desc')->select();
        $combo_r = [];
        $goods_r = [];
        $total_money = 0;
        foreach ($list as $k => $v) {
            if($v['combo_id']>0){
                $combo = db('book_combo')->where('id', $v['combo_id'])->find();
                $combo_goods_map['goods_id'] = ['in', $combo['goods_content']];
                $combo_goods = db('book_combo_goods')->where($combo_goods_map)->column('goods_name');
                $combo_goods = implode($combo_goods, ', ');

                $combo_r[$k]['goods_name'] = $combo['goods_name'];
                $combo_r[$k]['goods_content'] = $combo_goods;
                $combo_r[$k]['goods_price'] = $combo['shop_price'];
                $combo_r[$k]['goods_num'] = $v['goods_num'];
                $total_money += $v['goods_num'] * $combo['shop_price'];

            }
            if($v['goods_id']>0){
                $goods = db('book_goods')->where('goods_id', $v['goods_id'])->find();

                $goods_r[$k]['goods_name'] = $goods['goods_name'];
                $goods_r[$k]['goods_price'] = $goods['shop_price'];
                $goods_r[$k]['goods_num'] = $v['goods_num'];
                $total_money += $v['goods_num'] * $goods['shop_price'];
                //dump('$total_money'.$total_money);
            }

        }
        //红包
        $coupon_map['c.type'] = 5;
        $coupon_map['l.uid'] = session('user_id');
        $time = time();
        $coupon_map['c.use_start_time'] = ['<=', $time];
        $coupon_map['c.use_end_time'] = ['>=', $time];
        $coupon_map['c.condition'] = ['<=', $total_money];
        $coupon_map['l.order_id'] = ['=', 0];

        $coupon_red = db('coupon_list')
                        ->alias('l')
                        ->join('coupon c', 'l.cid = c.id')
                        ->where($coupon_map)
                        ->select();
        //折扣
        $coupon_map['c.type'] = 7;
        $coupon_discount = db('coupon_list')
                        ->alias('l')
                        ->join('coupon c', 'l.cid = c.id')
                        ->where($coupon_map)
                        ->select();


        $this->assign([
            'user' => $user,
            'total_money' => $total_money,
            'goods_r' => $goods_r,
            'combo_r' => $combo_r,
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
            $is_full = input('is_full/d');
            //算商品总价
            $model = new BookModel();
            $total_money = $model->totalPrice($coupon_red, $coupon_discount, $is_full);
 
            $this->success($total_money);
        }
    }


    //提交订单
    public function order(){
        if(request()->isAjax()){
            $is_full = input('is_full/d');
            $coupon_red = input('coupon_red/d');
            $coupon_discount = input('coupon_discount/d');
            $model = new BookModel();
            $order_id = $model->order($coupon_red, $coupon_discount, $is_full);
            if($order_id){
                if(stripos($order_id, '号') !== false){
                    $url = url('book/table');
                    $url = url('book/table');
                    $return['code'] = 3;
                    $return['url'] = $url;
                    $return['msg'] = $order_id.'被他人抢先订了!';

                    return $return;
                }
                else{

                    $url = url('payment',['order_id' => $order_id, 'is_full'=> $is_full]);
                    $this->success('提交成功', '', $url);
                }
            }
            else{
                $this->error('提交失败');
            }
        }
    }

    //已经下单后，选择20% 或全款
    public function totalPricePay(){
        if(request()->isAjax()){
            $order_id = input('order_id/d');
            $is_full = input('is_full/d');
            if($is_full == 1){
                $total_money = db('book_order')->where('order_id', $order_id)->value('order_amount');
            }
            if($is_full == 2){
                $total_money = db('book_order')->where('order_id', $order_id)->value('amount20');
            }
            $this->success($total_money);
        }
    }

    //提交支付
    public function payment(){
        $order_id = input('order_id/d');
        $is_full = input('is_full/d');
        if($order_id){
            $order = db('book_order')->where('order_id', $order_id)->find();
            if($order['pay_status'] == 1){
                $this->success('已经支付全款');
            }
            $must_pay = $order['order_amount'];
            if($is_full == 2){
                $must_pay = $order['amount20'];
            }
            if($order['pay_status'] == 2){
                 $must_pay = $order['amount80'];
            }
            $map['order_id'] = $order_id;
            $map['user_id'] = session('user_id');
            db('book_order')->where($map)->update(['is_full' => $is_full]);
            $this->assign([
                'is_full' => $is_full,
                'must_pay' => $must_pay,
                'order_id' => $order_id,
                'pay_status' => $order['pay_status'],
                ]);
        }
        else{
            $this->error('请到订单列表，选择订单支付！');
        }
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
        $status = db('book_order')->where($map)->value('pay_status');
        $msg = url('orderDetail', ['order_id'=> $order_id]);

        //支付成功
        if($status){
            $this->success($status);
        }
        
    }

    //订单详情
    public function orderDetail(){
        $order_id = input('order_id/d');
        if($order_id){
            $model = new BookModel();
            $map['user_id'] = session('user_id');
            $map['order_id'] = $order_id;
            $order = $model->orderDetail($map);
            $curNavTitle = '已预订';
            $template = 'orderdetail';
            $curNavIndex = input('curNavIndex');

            $time = time() - $order['add_time'];
            $order['spare_time'] = '0:00';
            if(($time <= 3600) && !$order['pay_status']){   //一小时内支付
                $spare_time = 3600 - $time;
                $order['spare_time'] = floor($spare_time/60).":".$spare_time%60;
                $order['can_pay'] = 1;
            }
            
            if(!$order['pay_status'] && !$order['order_status']){
                $order['order_status'] = config('book_order_status')['3'];  //未支付
                if($order['at_time_unix'] < time()){
                    $template = 'expired';
                }
            }

            if($order['order_status'] == 1){
                if($order['refund_amount']){
                    $order['order_status'] = '退款已完成'; 
                }
                else{
                    $order['order_status'] = config('book_order_status')[$order['order_status']];
                }
                $template = 'completed'; 
            }
            if($order['order_status'] == 2){     //已取消
                $order['order_status'] = config('book_order_status')[$order['order_status']];
                $template = 'unsubscribe';
            }

            
           /* if($curNavIndex == 1){  //过期
                $curNavTitle = '已过期';
                $template = 'expired';
            }
            if($curNavIndex == 2){  //退订
                $curNavTitle = '退订中';
                $template = 'unsubscribe';
            }
            if($curNavIndex == 3){  //完成
                $curNavTitle = '已完成';
                $template = 'completed';
            }*/

        }
        else{
            $this->error('请选择订单！');
        }

        

        $this->assign([
                'order' => $order,
                ]);
        return $this->fetch($template);
    }


    //订单列表
    public function orderlist(){
        if(request()->isAjax()){
            $model = new BookModel();
            $map['user_id'] = session('user_id');
            $curNavIndex = input('curNavIndex')?input('curNavIndex'):0;
            $num = input('num')?input('num'):1;
            $limit = input('size')?input('size'):10;
            $offset = ($num-1) * $limit;
            $curNavTitle = '已预订';
            $template = 'reserved';
            $map['order_status'] = 0;

            $time = time();
            if($curNavIndex == 0){
                $map['at_time_unix'] = ['>=', $time];
            }
            if($curNavIndex == 1){  //过期
                $map['at_time_unix'] = ['<', $time];
                $map['order_status'] = 0;
                $map['pay_status'] = 0;
                $curNavTitle = '已过期';
                $template = 'expired';
            }
            if($curNavIndex == 2){  //退订
                $map['order_status'] = 2;
                $curNavTitle = '退订中';
                $template = 'unsubscribe';
            }
            if($curNavIndex == 3){  //完成
                $map['order_status'] = 1;
                $curNavTitle = '已完成';
                $template = 'completed';
            }




            $orderlist = $model->orderList($map, $offset, $limit);
            foreach ($orderlist as $k => $v) {
                $time = time() - $v['add_time'];
                if(($time > 3600) && !$v['pay_status']){   //一小时内支付
                    $orderlist[$k]['can_pay'] = 0;
                    $orderlist[$k]['spare_time'] = '00:00';
                }
                else{
                    $orderlist[$k]['can_pay'] = 1;
                    $time = 3600 - $time;
                    $minute = floor($time/60);
                    $sec = $time%60;
                    $orderlist[$k]['spare_time'] = $minute .':' . $sec;
                }
                $orderlist[$k]['end_time'] = date("Y/m/d H:i:s", strtotime("+ 3600 seconds", $v['add_time']));
                $orderlist[$k]['order_id'] = $v['order_id'];
                $orderlist[$k]['pdordernumber'] = $v['order_sn'];
                $orderlist[$k]['pdreserved'] = $curNavTitle;
                $orderlist[$k]['pdposition'] = $v['seat'];
                $orderlist[$k]['pdpeople'] = $v['people_num'];
                //$orderlist[$k]['pddiningtime'] = $v['at_time'];
                $orderlist[$k]['pdtotalprice'] = $v['order_amount'];
                $orderlist[$k]['pddepositpaid'] = $v['amount20'];
                $orderlist[$k]['amount20'] = $v['amount20'];
                $orderlist[$k]['amount80'] = $v['amount80'];
                $orderlist[$k]['order_amount'] = $v['order_amount'];
                $orderlist[$k]['pdunpaidbalance'] = $v['order_amount'] - $v['amount20'];
                $orderlist[$k]['pdurl'] = url('payment',['order_id' => $v['order_id'], 'is_full'=> 1, 'type' => 'book']);
                $orderlist[$k]['pdurl2'] = url('payment',['order_id' => $v['order_id'], 'is_full'=> 2, 'type' => 'book']);
                $url = url('orderDetail', ['order_id' => $v['order_id'], 'curNavIndex' => $curNavIndex ]);
                $orderlist[$k]['pdlink'] = $url;
                $orderlist[$k]['pdlinktwo'] = $url;
                $orderlist[$k]['pdlinkthree'] = $url;
                $orderlist[$k]['pdlinkfour'] = $url;  
                //处理日期
                $at_time = explode('日', $v['at_time']);
                $orderlist[$k]['pddiningtime'] = str_replace(['年', '月'], '-', $at_time[0]);
            }

            return $orderlist;
        }

        //$this->assign('orderlist', $orderlist);
        return view();
    }

    //桌状态
    public function tableStatus(){
        if(request()->isAjax()){
            $at_time = db('book_temp')->where('user_id', session('user_id'))->value('at_time');
            $table = input('table');
            $model = new BookModel();
            $r = $model->tableStatus($table, $at_time);
            if($r){
                
                $this->error('已被人抢先订了啦，请选择其它的...');
            }
            else{
                $this->success('可以订');
            }
        }
    }

    //删除购物车里的商品
    public function ajaxDelCart(){
        if(request()->isAjax()){
            $ids = input('ids/s');
            $ids = trim($ids, ',');
            $map['id'] = ['in', $ids];
            $map['user_id'] = session('user_id');
            $r = db('book_cart')->where($map)->delete();
            if(!$r){
                $this->error('删除有误，请刷新页面，再操作');
            }
        }
    }

    //取消订单
    /*
1、全额支付订单的退定规则：
(1)用户在距离预定用餐时间24小时（含）以上取消订单的，无责，全额退款。
(2)用户在距离预定用餐时间12（含）-24小时（不含）内取消订单的，系统将收取全额订单金额的5%，余款原路退回。
(3)用户在距离预定用餐时间6（含）-12（不含）小时内取消订单的，系统将收取全额订单金额的10%，余款原路退回。
(4)用户在距离预定用餐时间6小时内取消订单的，系统将收取全额订单金额的15%，余款原路退回。
(5)用户超过预定用餐时间40分钟未能到场用餐的，该订单将自动失效，系统将收取全额订单金额的15%，余款原路退回。
2、支付定金订单的退定规则：
(1)用户在距离预定用餐时间24小时（含）以上取消订单的，无责，全额退回预付定金。
(2)用户在24小时内取消订单的，预付定金不予退回。
3、退定周期：1-5个工作日

     */
    public function cancel(){

        if(request()->isAjax()){
            $order_id = input('order_id/d');
            $order = db('book_order')->where('order_id', $order_id)->find();
            $refund_amount = 0; //退款金额
            if(!empty($order['pay_status'])){  //支付成功的，处理相应的
                $time = $order['at_time_unix'] - time();
                if($time> 0){
                    $to_time = ceil($time/3600);
                    if($to_time > 24){
                        if($order['pay_status'] == 2) $refund_amount = $order['amount20'];
                        if($order['pay_status'] == 1) $refund_amount = $order['order_amount'];
                    }
                    // 2、距离预定时间12-24小时取消订单
                    if( ($to_time <= 24) && ($to_time > 12) ){
                        if($order['pay_status'] == 2){
                            $refund_amount = 0;
                        } 
                        if($order['pay_status'] == 1){
                            $refund_amount = round($order['order_amount']*0.95, 2);
                        } 

                    }
                    //3、距离预定时间6-12小时内取消订单
                    if( ($to_time <= 12) && ($to_time > 6) ){
                        $money = round($order['order_amount']*0.9, 2);
                        if($order['pay_status'] == 2){
                            $refund_amount = 0 ;
                        } 
                        if($order['pay_status'] == 1){
                            $refund_amount = $money;
                        } 
                    }
                    //距离预定时间0-6小时内取消订单
                    if( ($to_time <= 6) && ($to_time > 0) ){
                        $money = round($order['order_amount']*0.85, 2);
                        if($order['pay_status'] == 2){
                            $refund_amount = 0 ;
                        } 
                        if($order['pay_status'] == 1){
                            $refund_amount = $money;
                        } 
                    } 
                }
                else{ //5
                    $money = round($order['order_amount']*0.85, 2);
                    if($order['pay_status'] == 2){
                        $refund_amount = 0 ;
                    } 
                    if($order['pay_status'] == 1){
                        $refund_amount = $money;
                    } 
                }
            }

            //更新订单状态
            $data['order_status'] = 2;
            $data['refund_amount'] = $refund_amount;
            $data['refund_time'] = time();
            $map['user_id'] = session('user_id');
            $map['order_id'] = $order_id;

            $r = db('book_order')->where($map)->update($data);
            if($r){
                $this->success('取消成功');
            }
            else{
                $this->error('取消失败');
            }

        }
    }


}