<?php

namespace app\api\model;


use think\Model;

class GoodsImages extends Model
{
    public function getImageUrlAttr($value){
        return config('img_prefix').$value;
    }
}