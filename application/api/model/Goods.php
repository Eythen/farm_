<?php

namespace app\api\model;


use think\Model;

class Goods extends Model
{
//    protected $visible = ['goods_id','cat_id','goods_sn','goods_name','store_count',
//        'weight','shop_price','goods_remark','original_img','goods_content'];

    protected $hidden = ['extend_cat_id','click_count','comment_count','cost_price','keywords','is_real',
        'on_time','sort','is_recommend','is_new','is_hot','last_update','goods_type','spec_type',
        'give_integral','exchange_integral','suppliers_id','sales_sum','prom_type','prom_id','commission','spu','sku'];

    public function getOriginalImgAttr($value){
        return config('img_prefix').$value;
    }

    public function getGoodsContentAttr($value){
        $arr = explode('</p>',$value);
        $vas = array();
        foreach ($arr as $v){
            preg_match('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i',$v,$match);
            if ($match[2]){
                $vas[] = config('img_prefix').$match[2];
            }
        }
        return $vas;
    }

    public function img(){
        return $this->hasMany('GoodsImages','goods_id','goods_id');
    }

    public function entry(){
        return $this->hasMany('GoodsEntry','pid','goods_id');
    }

    public function size(){
        return $this->hasMany('GoodsSize','pid','goods_id');
    }

    public static function getGoodsByCatId($cat_id,$page,$size){
        $where['cat_id'] = $cat_id;
        $where['is_on_sale'] = 1;
        $where['store_count'] = ['>','0'];
        $goods = self::where($where)
            ->page($page,$size)
            ->order('on_time desc')
            ->select();
        return $goods;
    }

    public static function getGoodsByKeyword($keyword,$page,$size){
        $where['goods_name'] = ['like','%'.$keyword.'%'];
        $where['is_on_sale'] = 1;
        $where['store_count'] = ['>','0'];
        $goods = self::where($where)
            ->page($page,$size)
            ->order('on_time desc')
            ->select();
        return $goods;
    }

    public static function getProductDetail($goods_id){
        $good = self::with('img,entry,size')
            ->find($goods_id);
        return $good;
    }
}