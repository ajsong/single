<?php
return array(
	'partner'=>ALIPAY_PARTNER, //合作身份者id，以2088开头的16位纯数字
	'seller_email'=>ALIPAY_EMAIL, //收款支付宝账号，一般情况下收款账号就是签约账号
	'key'=>ALIPAY_KEY, //安全检验码，以数字和字母组成的32位字符
	'private_key_path'=>ROOT_PATH . ALIPAY_PRIVATE_PATH, //商户的私钥（后缀是.pem）文件相对路径
	'ali_public_key_path'=>ROOT_PATH . ALIPAY_PUBLIC_PATH, //支付宝公钥（后缀是.pem）文件相对路径
	'sign_type'=>strtoupper('RSA'), //签名方式 不需修改
	'sign_type_refund'=>strtoupper('MD5'), //退款签名方式 不需修改
	'input_charset'=>strtolower('utf-8'), //字符编码格式 目前支持 gbk 或 utf-8
	'cacert'=>PLUGIN_PATH.'/pay/alipay/cacert.pem', //ca证书路径地址，用于curl中ssl校验
	'transport'=>'http' //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
);
