<?php

namespace app\api\model;


use think\Model;

class BookGoods extends Model
{
    protected $hidden = ['last_update','last_update_uid','uid','add_time'];

    public function getOriginalImgAttr($value){
        return config('img_prefix').$value;
    }

    public static function getAll($page,$size){
        $res = self::page($page,$size)
            ->order('on_time desc')
            ->select();
        return $res;
    }
}