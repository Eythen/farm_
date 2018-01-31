<?php
/**
 * 购物车
 */
namespace app\wap\model;
use think\Model;
class Coupon extends Model{
    /**
     * [couponlist 有效期内的优惠券]
     * @return [type] [description]
     */
    public function couponlist(){
        $time = time();
        $map['send_start_time'] = ['<=', $time];
        $map['send_end_time'] = ['>=', $time];
        $map['type'] = ['in',[5,7]];

        $list = db('coupon')->where($map)->field('id, type,createnum, send_num, money, condition, use_start_time, use_end_time')->order('id desc')->select();

        if($list){
            $data = [];
            foreach ($list as $k => $v) {
                $num = 1;//设置有值
                if($v['createnum']){
                    $num = $v['createnum'] - $v['send_num'];
                }
                if($num>0){
                    $data[$k]['id'] = $v['id'];
                    $data[$k]['money'] = $v['money'];
                    $data[$k]['condition'] = $v['condition'];
                    $data[$k]['type'] = $v['type'];
                    $data[$k]['use_start_time'] = date('Y-m-d', $v['use_start_time']);
                    $data[$k]['use_end_time'] = date('Y-m-d', $v['use_end_time']);
                    $data[$k]['type_name'] = config('coupon_type')[$v['type']];
                }
            }
        }
        return $data;
    }

    //当前有效的个人红包
    public function coupon_red($total_money){
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
        return $coupon_red;
    }

    //当前有效的个人折扣券
    public function coupon_discount($total_money){
        $coupon_map['c.type'] = 7;
        $coupon_map['l.uid'] = session('user_id');
        $time = time();
        $coupon_map['c.use_start_time'] = ['<=', $time];
        $coupon_map['c.use_end_time'] = ['>=', $time];
        $coupon_map['c.condition'] = ['<=', $total_money];
        $coupon_map['l.order_id'] = ['=', 0];
        $coupon_discount = db('coupon_list')
                        ->alias('l')
                        ->join('coupon c', 'l.cid = c.id')
                        ->where($coupon_map)
                        ->select();
        return $coupon_discount;
    }
    



}
?>