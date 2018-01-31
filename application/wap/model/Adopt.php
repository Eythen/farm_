<?php
/**
 * 认养
 */
namespace app\wap\model;
use think\Model;
class Adopt extends Model{

    /**
     * [piglist 花猪列表]
     * @param  [type] $map [查询条件]
     * @param  integer $offset [description]
     * @param  integer $limit  [description]
     * @param  integer $type   [0前台查询，1后台查询]
     * @return [type]      [description]
     */
    public function piglist($map, $offset = 0, $limit = 10, $type = 0){
        if($type){
            $piglist['total'] = db('pig')->where($map)->count();
            if($piglist['total']){
                $piglist['rows'] = db('pig')->where($map)->order('pig_id desc')->limit($offset, $limit)->select();
            }
            else{
                $piglist['rows'] = [];
            }

        }
        else{
            $piglist = db('pig')->where($map)->order('pig_id desc')->limit($offset, $limit)->select();
        }
        return $piglist;
    }

    /**
     * [pigDetail 花猪详情]
     * @param  [type] $map [查询条件]
     * @return [type]      [description]
     */
    public function pigDetail($map){
        $pigDetail = db('pig')->where($map)->find();
        return $pigDetail;
    }

    /**
     * [loglist 花猪成长信息列表]
     * @param  [type] $map [查询条件]
     * @param  integer $offset [description]
     * @param  integer $limit  [description]
     * @param  integer $type   [0前台查询，1后台查询]
     * @return [type]      [description]
     */
    public function loglist($map, $offset = 0, $limit = 10, $type = 0){
        if($type){
            $loglist['total'] = db('pig_log')->where($map)->count();
            if($loglist['total']){
                $loglist['rows'] = db('pig_log')->where($map)->order('log_id desc')->limit($offset, $limit)->select();
            }
            else{
                $loglist['rows'] = [];
            }

        }
        else{
            $loglist = db('pig_log')->where($map)->order('log_id desc')->limit($offset, $limit)->select();
        }
        return $loglist;
    }

    /**
     * [logDetail 花猪信息详情]
     * @param  [type] $map [查询条件]
     * @return [type]      [description]
     */
    public function logDetail($map){
        $logDetail = db('pig_log')->where($map)->order('log_id desc')->select();
        return $logDetail;
    }

    /**
     * [orderlist 订单列表]
     * @param  [type] $map [查询条件]
     * @return [type]      [description]
     */
    public function orderlist($map, $offset, $limit,$type=0){
        if($type){
            $orderlist['total'] = db('pig_order')->where($map)->count();
            if($orderlist['total']){
                $orderlist['rows'] = db('pig_order')->where($map)->order('order_id desc')->limit($offset, $limit)->select(); 
            }
            else{
                $orderlist['rows'] = [];
            }
        }
        else{
            $orderlist = db('pig_order')->where($map)->order('order_id desc')->limit($offset, $limit)->select();
        }
        return $orderlist;
    }

    /**
     * [orderDetail 订单详情]
     * @param  [type] $map [查询条件]
     * @return [type]      [description]
     */
    public function orderDetail($map){
        $order = db('pig_order')->where($map)->find();
        if($order){
            $order_pigs = db('pig_order_pigs')->where('order_id', $order['order_id'])->select();
            $order['order_pigs'] = $order_pigs;
        }
        else{
            $order['order_pigs'] = '';
        }

        return $order;
    }

    public function totalPrice($coupon_red, $coupon_discount){
        //算商品总价
        $ids = session('ids');
        if(!$ids){
            $this->error('您还没有选购！');
        }
        $map['pig_id'] = ['in', $ids];
        $total_money = db('pig')->where($map)->sum('pig_price');

        //计算减去红包
        if($coupon_red){
            $coupon = db('coupon')->where('id', $coupon_red)->value('money');
            $total_money = round(($total_money - $coupon), 2);
        }
        if($total_money<0){
            $this->error('系统有误，不能处理！');
        }
        //计算折扣后价
        if($coupon_discount){
            $coupon = db('coupon')->where('id', $coupon_discount)->value('money');
            $total_money = round(($total_money * $coupon)/10, 2) ;
        }
        
        return $total_money;
    }

    //订购
    public function order($data){
        $total_money = 0; 
        $pig_num = 0;  
        $pig_r = [];  
        $map['pig_id'] =['in', session('ids')];
        $list = db('pig')->where($map)->select();
        foreach ($list as $k => $v) {
            $pig_r[$v['pig_id']]['pig_id'] = $v['pig_id'];
            $pig_r[$v['pig_id']]['pig_name'] = $v['pig_name'];
            $pig_r[$v['pig_id']]['pig_price'] = $v['pig_price'];
            $pig_r[$v['pig_id']]['pig_num'] = 1;
            $total_money += 1 * $v['pig_price']; 
            $pig_num +=1;          
        }

        $total_amount = $total_money; //订单总价
        //计算减去红包
        $coupon_red = $data['coupon_red'];
        $coupon_discount = $data['coupon_discount'];
        if($coupon_red){
            $coupon_red_money = db('coupon')->where('id', $coupon_red)->value('money');
            $total_money = round(($total_money - $coupon_red_money), 2);
        }
        if($total_money<0){
            $this->error('系统有误，不能处理！');
        }
        //计算折扣后价
        if($coupon_discount){
            $coupon = db('coupon')->where('id', $coupon_discount)->value('money');
            $discount_money = round(($total_money * $coupon)/10, 2);
            $coupon_discount_money = $total_money - $discount_money; //折扣后省的钱
            $total_money = $discount_money;
        }
        $order_amount = $total_money; //应付款总金额

        
        $order_sn ='adopt'.date('YmdHis').'_'.rand(1000,9999).rand(10,99);
        $order_data = [
            'coupon_discount' => $coupon_discount_money?$coupon_discount_money:0,
            'coupon_red' => $coupon_red_money?$coupon_red_money:0,
            'total_amount' => $total_amount,
            'order_amount' => $order_amount,
            'add_time' => time(),
            'order_sn' => $order_sn,
            'user_id' => session('user_id'),
            'consign' => $data['consign'],
            'partition' => $data['partition'],
            'address' => $data['address'],
            'mobile' => $data['mobile'],
            'name' => $data['name'],
            'pig_num' => $pig_num,
            ];

        //下单前检查是否被抢先下单

        if($pig_r){
            $pig_name = '';
            foreach ($pig_r as $k => $v) {
                $check_map['p.pig_id'] = $v['pig_id'];
                //订单状态0认养中，1已完成，2退订中，3无效，4发货，5退订完成
                $check_map['o.order_status'] = ['not in', [3,5]];
                $has = db('pig_order_pigs')->alias('p')->join('pig_order o', 'o.order_id = p.order_id')->where($check_map)->find();
                logWrite(db()->getLastSql());
                if($has){
                    $pig_name .= $has['pig_name'].',';
                }
            }
            $pig_name = trim($pig_name, ',');
            if($pig_name){
                return $pig_name.'已被抢先认养了！现在返回，重新选择。';
            }
        }

        //下单前检查是否被抢先下单end
        db()->startTrans();
        try{
            
            $order_id = db('pig_order')->insertGetId($order_data);
            //处理添加认养的花猪
            if($pig_r){
                foreach ($pig_r as $k => $v) {
                   $pig_r[$k]['order_id'] = $order_id;
                }
                db('pig_order_pigs')->insertAll($pig_r);  //添加单品
            }
            //处理红包
            if($coupon_red){
                $coupon_red_data['order_id'] = $order_id;
                $coupon_red_data['is_book'] = 3;
                $coupon_red_data['use_time'] = time();

                $coupon_red_map['uid'] = session('user_id');
                $coupon_red_map['cid'] = $coupon_red;
                db('coupon_list')->where($coupon_red_map)->update($coupon_red_data);
            }
            //处理折扣券
            if($coupon_discount){
                $coupon_discount_data['order_id'] = $order_id;
                $coupon_discount_data['is_book'] = 3;
                $coupon_discount_data['use_time'] = time();

                $coupon_discount_map['uid'] = session('user_id');
                $coupon_discount_map['cid'] = $coupon_discount;
                db('coupon_list')->where($coupon_discount_map)->update($coupon_discount_data);
            }
            //处理花猪认养人
            $pigMap['pig_id'] = ['in', session('ids')];
            $user_data = [
                'user_id' => session('user_id'),
                'user_name' => session('user')['nickname'],
                'adopt_time' => time(),
                'order_id' => $order_id,
            ];
            db('pig')->where($pigMap)->update($user_data);
            
            db()->commit();
            return $order_id;
        } catch (Exception $e){
            db()->rollback();
            return 0;
        }
    }

    /**
     * [logistics 根据订单查物流]
     * @param  [int] $order_id [订单id必填 ]
     * @return [type]           [description]
     */
    public function logistics($order_id){
        $logistics = db('pig_order_logistics')->where('order_id', $order_id)->field('logistics_id,name, code, sn, user_name, add_time')->select();
        if($logistics){
            foreach ($logistics as $k => $v) {
                $express = controller('home/Express');
                $express_info = $express->getOrderTraces($v['code'], $v['sn']);
                if($express_info['message'] == 'ok'){
                    $logistics[$k]['data'] = $express_info['data'];
                }
                else{
                    $logistics[$k]['data'] = [];
                }
            }
        }

        return $logistics;
    }

}
?>