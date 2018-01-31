<?php

namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class Base extends Validate
{
    public function goCheck(){
        $request = Request::instance();
        $params = $request->param();

        $res = $this->batch()->check($params);
        if (!$res){
            $e = new ParameterException([
                'msg' => is_array($this->error) ? implode(
                    ';', $this->error) : $this->error,
            ]);
            throw $e;
        }else{
            return true;
        }
    }

    protected function isPostiveInteger($value,$rule='',$data='',$field=''){
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0){
            return true;
        }else{
            return false;
        }
    }

    protected function isNotEmpty($value,$rule='',$data='',$field=''){
        if (empty($value)){
            return false;
        }else{
            return true;
        }
    }

    protected function isMobile($value){
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $res = preg_match($rule,$value);
        if ($res){
            return true;
        }else{
            return false;
        }
    }
}