<?php

namespace app\api\model;


use think\Model;

class Ad extends Model
{
    protected $visible = ['ad_id','ad_name','ad_link','ad_code'];

    public function getAdCodeAttr($value){
        return config('img_prefix').$value;
    }
}