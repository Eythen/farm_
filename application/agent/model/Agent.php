<?php
/**
 * 代理模型
 */
namespace app\agent\model;
use think\Model;
class Agent extends Model{
    /**
     * [orderApply 发货订单列表]
     * @param  [type] $map [查询条件]
     * @return [type]      [$type=1为后台使用 0为手机端使用]
     */
    public function orderApply($map, $offset, $limit,$type=0){
        if($type){
            $orderlist['total'] = db('agent_order_apply')->where($map)->count();
            if($orderlist['total']){
                $orderlist['rows'] = db('agent_order_apply')->where($map)->order('order_id desc')->limit($offset, $limit)->select(); 
            }
            else{
                $orderlist['rows'] = [];
            }
        }
        else{
            $orderlist = db('agent_order_apply')->where($map)->order('order_id desc')->limit($offset, $limit)->select();
        }
        return $orderlist;
    }

    /**
     * [orderApplyDetail 发货订单详情]
     * @param  [type] $map [查询条件]
     * @return [type]      [description]
     */
    public function orderApplyDetail($map){
        $order = db('agent_order_apply')->where($map)->find();
        if($order){
            $order_goods = db('agent_order_apply_goods')->where('apply_id', $order['apply_id'])->select();
            $order['order_goods'] = $order_goods;
        }
        else{
            $order['order_goods'] = '';
        }

        return $order;
    }
   


    /**
     * [orderlist 订单列表]
     * @param  [type] $map [查询条件]
     * @return [type]      [$type=1为后台使用 0为手机端使用]
     */
    public function orderlist($map, $offset, $limit,$type=0){
        if($type){
            $orderlist['total'] = db('agent_order')->where($map)->count();
            if($orderlist['total']){
                $orderlist['rows'] = db('agent_order')->where($map)->order('order_id desc')->limit($offset, $limit)->select(); 
            }
            else{
                $orderlist['rows'] = [];
            }
        }
        else{
            $orderlist = db('agent_order')->where($map)->order('order_id desc')->limit($offset, $limit)->select();
        }
        return $orderlist;
    }

    /**
     * [suborderlist 订单列表]
     * @param  [type] $map [查询条件]
     * @return [type]      [$type=1为后台使用 0为手机端使用]
     */
    public function suborderlist($map, $offset, $limit,$type=0){
        if($type){
            $orderlist['total'] = db('agent_order_apply')->where($map)->count();
            if($orderlist['total']){
                $orderlist['rows'] = db('agent_order_apply')->where($map)->order('apply_id desc')->limit($offset, $limit)->select(); 
            }
            else{
                $orderlist['rows'] = [];
            }
        }
        else{
            $orderlist = db('agent_order_apply')->where($map)->order('apply_id desc')->limit($offset, $limit)->select();
        }
        return $orderlist;
    }

    /**
     * [orderDetail 订单详情]
     * @param  [type] $map [查询条件]
     * @return [type]      [description]
     */
    public function orderDetail($map){
        $order = db('agent_order')->where($map)->find();
        if($order){
            $order_pigs = db('agent_order_goods')->where('order_id', $order['order_id'])->select();
            $order['order_goods'] = $order_pigs;
        }
        else{
            $order['order_goods'] = '';
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

    /**
     * [submit 订购提交]
     * @param  [type] $ids [商品ID]
     * @return [type]      [description]
     */
    public function submit($ids){
        $map['goods_id'] = ['in', $ids];
        $goods = db('agent_goods')->where($map)->select();

        foreach ($goods as $k => $v) {
            $goods_price += $v['shop_price'];
        }
        $sn = 'agent'.date('YmdHis').rand(10000,99999).rand(10,99);
        $data = [
            'user_id' => session('user_id'),
            'name' => session('user')['nickname'],
            'goods_num' => count($goods),
            'total_amount' => $goods_price,
            'order_amount' => $goods_price,
            'add_time' => time(),
            'order_sn' => $sn,
            ];

        db()->startTrans();
        try{
            $order_id = db('agent_order')->insertGetId($data);
            $agent_goods = [];
            foreach ($goods as $k => $v) {
                $agent_goods[$k] = [
                    'order_id' => $order_id,
                    'goods_num' => 1,
                    'goods_price' => $v['shop_price'],
                    'goods_name' => $v['goods_name'],
                    'goods_id' => $v['goods_id'],
                ];
            }
            db('agent_order_goods')->insertAll($agent_goods);           
            db()->commit();
            return $order_id;
        } catch (Exception $e){
            db()->rollback();
            return 0;
        }
    }

    /**
     * [logistics 根据订单查物流]
     * @param  [int] $map [发货订单查询条件 ]
     * @return [type]           [description]
     */
    public function logistics($map){
        $logistics = db('agent_order_apply_logistics')->where($map)->field('logistics_id,name, code, sn, admin, add_time')->select();
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