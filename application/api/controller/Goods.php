<?php

namespace app\api\controller;


use app\api\validate\Count;
use app\api\validate\KeywordParameter;
use app\api\validate\PagingParameter;
use app\api\validate\IDMustBePostiveInt;
use app\api\model\Goods as GoodsModel;
use app\lib\exception\GoodsMissException;
use app\wap\controller\Index;

class Goods extends Base
{
    public function getByCat($id,$page = 1,$size = 15){
        (new IDMustBePostiveInt())->goCheck();
        (new PagingParameter())->goCheck();
        $goods = GoodsModel::getGoodsByCatId($id,$page,$size);
        return $goods;
    }

    public function getByKeyword($keyword='',$page = 1,$size = 15){
        (new KeywordParameter())->goCheck();
        (new PagingParameter())->goCheck();
        $goods = GoodsModel::getGoodsByKeyword($keyword,$page,$size);
        return $goods;
    }

    public function getOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $good = GoodsModel::getProductDetail($id);
        if (!$good){
            throw new GoodsMissException();
        }
        return $good;
    }

    public function getHot($count=5){
        (new Count())->goCheck();
        $hot = Index::getHot($count);
        return $hot;
    }
}