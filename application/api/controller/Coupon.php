<?php

namespace app\api\controller;

use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\CouponException;
use app\lib\exception\SuccessMessage;
use app\wap\model\Coupon as WapCouponModel;
use app\api\service\Token as TokenService;
use app\api\model\Coupon as CouponModel;
use app\api\model\CouponList as CouponListModel;

class Coupon extends Base
{
    public function getCoupon(){
        $CouponModel = new WapCouponModel();
        $couponlist = $CouponModel->couponlist();
        return $couponlist;
    }

    public function receive($id){
        (new IDMustBePostiveInt())->goCheck();
        $user_id = TokenService::getCurrentUid();
        $code = CouponModel::receiveCoupon($id,$user_id);
        if (!$code){
            throw new CouponException([
                'msg' => '领取优惠卷失败',
                'error_code' => 70001
            ]);
        }
        return json(new SuccessMessage(),201);
    }

    public function getByType($type=5){
        $user_id = TokenService::getCurrentUid();
        $list = CouponListModel::getByType($user_id,$type);
        return $list;
    }
}