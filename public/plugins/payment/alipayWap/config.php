<?php
return array(
    'code'=> 'alipayWap',
    'name' => '手机网站支付宝2.0',
    'version' => '2.0',
    'author' => '杨清',
    'desc' => '手机端网站支付宝2.0 ',
    'icon' => 'logo.jpg',
    'scene' =>1,  // 使用场景 0 PC+手机 1 手机 2 PC
    'config' => array(
        array('name' => 'app_id','label'=>'应用ID,您的APPID',           'type' => 'text',   'value' => ''),
        array('name' => 'merchant_private_key','label'=>'商户私钥，您的原始格式RSA私钥',               'type' => 'textarea',   'value' => ''),
        array('name' => 'alipay_public_key','label'=>'对应APPID下的支付宝公钥。',           'type' => 'textarea',   'value' => ''),
        
        array('name' => 'alipay_pay_method','label'=>' 选择接口类型',        'type' => 'select', 'option' => array(
          '1' => '即时到帐交易接口',
        )),
        
    ),    
);