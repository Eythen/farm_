<?php
return array(
	//'配置项'=>'配置值'
	//伪静态
	//'URL_MODEL' => 2,
	/**
	数据库配置
	 */

	'DB_TYPE' => 'mysql',
	'DB_HOST' => '127.0.0.1',
	'DB_PORT' => 3306,
	'DB_USER' => 'yang',
	'DB_PWD' => '641868752',
	'DB_NAME' => 'my',
	'DB_PREFIX' => 'info_',

	// 加载扩展配置文件
	'LOAD_EXT_CONFIG' => array('MSG' => 'msg','CORP' => 'corp'),

	// md5加密密钥
	'MD5_KEY' => '2EE72A9235ED92C40A3551AA07DED85D',

	'AUTH_KEY' => '09BA6D7FC20774402EF41FFFEB2F50B4',

	// AUTH权限认证
	'AUTH_ON' => false, // 认证开关
	'AUTH_TYPE' => 1, // 认证方式，1为实时认证；2为登录认证。
	'AUTH_GROUP' => 'info_auth_group', // 用户组数据表名
	'AUTH_GROUP_ACCESS' => 'info_auth_group_access', // 用户-用户组关系表
	'AUTH_RULE' => 'info_auth_rule', // 权限规则表
	'AUTH_USER' => 'info_users', // 用户信息表

	// api接口配置
	'auth' => array(
	    'e491813b203a5cc841a65804cae88345' => array('name' => 'CRM', 'secret'=>'2f99f908a3c5d75eacccb58ac5093fae'),
	),

	// 欣享家接口配置
	'HONEY_AUTH' => array(
	    '10352B27671148568BE42B310322E411E4D1337A' => array('name' => 'honey_CRM', 'secret'=>'denawekdEaZdA_eweqdkkqwqw'),
	),

	// 微信企业版接口配置
	'WEIXIN' => array(
		'CORPID' => 'wxe62f498da5f065fd',
		'SECRET' => 'UkGILEsI_fji_UxSsWr34PeH0NBNf4XQ007ErlQdFe3ed2PLJsw-eDDM9uc3t4lD'
	),
	// 'SHOW_PAGE_TRACE' => true,
);