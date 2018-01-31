<?php

namespace app\api\controller;


use app\api\validate\PagingParameter;
use app\api\model\BookGoods as BookGoodsModel;

class BookGoods extends Base
{
    public function getAll($page=1,$size=10){
        (new PagingParameter())->goCheck();
        $res = BookGoodsModel::getAll($page,$size);
        return $res;
    }
}