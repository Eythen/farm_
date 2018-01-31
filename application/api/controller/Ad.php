<?php

namespace app\api\controller;

use app\api\validate\IDMustBePostiveInt;
use app\api\model\Ad as AdModel;
use app\lib\exception\BannerMissException;

class Ad extends Base
{
    public function getBanner($id){
        (new IDMustBePostiveInt())->goCheck();
        $where['pid'] = $id;
        $where['enabled'] = 1;
        $where['end_time'] = ['>',time()];
        $banner = AdModel::all($where);
        if (!$banner){
            throw new BannerMissException();
        }
        return $banner;
    }

    public function getOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $ad = AdModel::get($id);
        if (!$ad){
            throw new BannerMissException();
        }
        return $ad;
    }
}