<?php

namespace app\api\model;


use think\Model;

class BookComboGoods extends Model
{
    protected $hidden = ['on_time','last_update','last_update_uid','uid','add_time'];

    public function getOriginalImgAttr($value){
        return config('img_prefix').$value;
    }
}