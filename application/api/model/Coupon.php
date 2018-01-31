<?php

namespace app\api\model;


use app\lib\exception\CouponException;
use think\Db;
use think\Exception;
use think\Model;

class Coupon extends Model
{

    protected $hidden = ['createnum','send_num','use_num','add_time','send_start_time','send_end_time'];

    public function lst(){
        return $this->hasMany('CouponList','cid','id');
    }

    public static function receiveCoupon($id,$user_id){
        $coupon = self::get($id);
        if (!$coupon){
            throw new CouponException();
        }
        Db::startTrans();
        try{
            $coupon->setDec('createnum');
            $coupon->setInc('send_num');
            $data = [
                'type' => $coupon->data['type'],
                'uid' => $user_id,
                'send_time' => time(),
            ];
            $coupon->lst()->save($data);
            Db::commit();
            return true;
        }catch (Exception $ex){
            Db::rollback();
            return false;
        }
    }
}