<?php

namespace app\api\validate;


use app\lib\exception\ParameterException;

class OrderPlace extends Base
{
    protected $rule = [
        'address_id' => 'require|isPostiveInteger',
        'goods' => 'checkGoods',
        'red_id' => 'isPostiveInteger',
        'rebate_id' => 'isPostiveInteger',
    ];

    protected $singleRule = [
        'goods_id' => 'require|isPostiveInteger',
        'goods_num' => 'require|isPostiveInteger',
    ];

    protected $message = [
        'address_id.require' => '收货地址必填',
        'address_id.isPostiveInteger' => '收货地址id必须为正整数',
        'red_id.isPostiveInteger' => '红包id必须为正整数',
        'rebate_id.isPostiveInteger' => '折扣券id必须为正整数',
    ];

    protected function checkGoods($values){
        if (!is_array($values)){
            throw new ParameterException([
                'msg' => '商品参数不正确'
            ]);
        }

        if (empty($values)){
            throw new ParameterException([
                'msg' => '商品列表不能为空',
            ]);
        }

        foreach ($values as $v){
            $this->checkGood($v);
        }
        return true;
    }

    protected function checkGood($value){
        $validate = new Base($this->singleRule);
        $res = $validate->check($value);
        if (!$res){
            throw new ParameterException([
                'msg' => '商品列表参数错误',
            ]);
        }
    }

}