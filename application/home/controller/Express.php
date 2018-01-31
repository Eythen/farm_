<?php
/**
 * 快递Api
 */
namespace app\home\controller;

class Express{
    //公司编号
    const Customer = "D5E383DCC4B45F31F5E5D30EC3173812";
    //apikey
    const ApiKey = "eKqJSuKz6326";

    //获取快递公司的编码
    public function getComCode($num){
        $url = 'http://www.kuaidi100.com/autonumber/auto?num='.$num.'&key='.ApiKey;
        $arr = $this->http_curl($url);
        return $arr[0]['comCode'];
    }

    //获取订单物流信息
    public function getOrderTraces($comcode,$num){
        $arr = array();
        $arr['customer'] = self::Customer;
        $arr['param'] = '{"com":"'.$comcode.'","num":"'.$num.'"}';
        $arr['sign'] =  md5($arr['param'].self::ApiKey.$arr['customer']);
        $arr['sign'] = strtoupper($arr['sign']);
        $url = 'http://poll.kuaidi100.com/poll/query.do';
        $o = "";
        foreach ($arr as $k=>$v){
            $o .= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }
        $arr = substr($o,0,-1);
        $data = $this->http_curl($url,$type='post',$res='json',$arr);
        return $data;
    }

    //curl采集
    public function http_curl($url,$type='get',$res='json',$arr=''){
        //初始化
        $ch = curl_init();
        //设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        if ($type == 'post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        //采集
        $output = curl_exec($ch);
        if ($res == 'json'){
            if (curl_errno($ch)){
                return curl_error($ch);
            }else{
                return json_decode($output,true);
            }
        }
        //关闭
        curl_close($ch);
    }

}