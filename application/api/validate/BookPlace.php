<?php

namespace app\api\validate;


use app\lib\exception\ParameterException;

class BookPlace extends Base
{
    protected $rule = [
        'table' => 'require',
        'red_id' => 'isPostiveInteger',
        'rebate_id' => 'isPostiveInteger',
        'goods' => 'checkGoods',
        'combo_goods' => 'checkGoods'
    ];

    protected $singleRule = [
        'goods_id' => 'require|isPostiveInteger',
        'goods_num' => 'require|isPostiveInteger',
    ];

    protected $message = [
        'table.require' => '桌数必填',
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