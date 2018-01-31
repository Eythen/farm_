<?php

namespace app\api\model;


use think\Model;
use app\api\model\BookComboGoods as BookComboGoodsModel;

class BookCombo extends Model
{
    protected $hidden = ['goods_content','last_update','last_update_uid','uid','add_time'];

    public function getOriginalImgAttr($value){
        return config('img_prefix').$value;
    }

    public static function getAll(){
        $book = self::select();
        foreach ($book as $k => $v){
            $goods_ids = ltrim($v['goods_content'],',');
            $where['goods_id'] = ['in',$goods_ids];
            $book[$k]['combo_goods'] = BookComboGoodsModel::all($where);
        }
        return $book;
    }
}