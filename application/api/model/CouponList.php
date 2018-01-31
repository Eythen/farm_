<?php

namespace app\api\model;


use think\Model;

class CouponList extends Model
{
    protected $hidden = ['order_id','use_time','code','is_book'];

    public function coupon(){
        return $this->belongsTo('Coupon','cid','id')->setEagerlyType(0);
    }

    public static function getByType($user_id,$type){
        $time = time();
        $map['coupon_list.type'] = $type;
        $map['coupon_list.uid'] = $user_id;
        $map['coupon.use_start_time'] = ['<=', $time];
        $map['coupon.use_end_time'] = ['>=', $time];
        $lst = self::all($map,'coupon');
        return $lst;
    }


}