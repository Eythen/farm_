<?php

namespace app\api\model;


use think\Model;

class GoodsEntry extends Model
{
    public function getPicAttr($value){
        return config('img_prefix').$value;
    }
}