<?php

return array(
    'code'=> 'weixinM',
    'name' => '微信支付',
    'version' => '1.0',
    'author' => 'yangqing',
    'desc' => '手机端+微信H5支付',
    'icon' => 'logo.jpg',
    'scene' => 1,  // 使用场景 0 PC+手机 1 手机 2 PC
    'config' => array(
        array('name' => 'appid','label'=>'绑定支付的APPID','type' => 'text',   'value' => ''), // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        array('name' => 'mchid',   'label'=>'商户号', 'type' => 'text',   'value' => ''), // * MCHID：商户号（必须配置，开户邮件中可查看）
        array('name' => 'key',  'label'=>'商户支付密钥', 'type' => 'text',   'value' => ''), // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        //array('name' => 'appsecret',  'label'=>'公众帐号secert（仅JSAPI支付的时候需要配置)', 'type' => 'text',   'value' => ''), // 公众帐号secert（仅JSAPI支付的时候需要配置)，
    ),
);