<?php
$site_database = require_once(APPLICATION_PATH . '/database.php');
return array_merge([
	//授权码
	'platform_auth' => '',
	//调试模式, false隐藏所有提示且记录到log, true显示所有错误
	'api_debug' => true,
	//表前缀占位符, 会被替换为表前缀字符串, 或者表字符串使用__XXX__, 将替换为 表前缀_xxx
	'db_tbp_placeholder' => '%tbp',
	//不加client_id条件的表
	'db_non_client_tables' => [],
	//不记录操作日志的表
	'db_not_access_log_tables' => ['access_log'],
	//水平分表的表名
	'db_split_tables' => [],
	//前台检测登录状态APP、ACT (H5、WEB、小程序、全局默认)
	'not_check_login' => [
		'wap' => [],
		'web' => [],
		'mini' => [],
		'global' => [
			'core' => ['weixin_auth', 'wx_login', 'get_wxcode'],
			'home' => ['index'],
			'article' => ['index', 'detail'],
			'category' => ['*'],
			'goods' => ['index', 'detail'],
			'passport' => ['*'],
			'other' => ['*'],
			'cron' => ['*']
		]
	],
	//前台不检测小程序token
	'not_check_mini' => [
		'member' => ['code']
	],
	//可外站AJAX跨域的APP、ACT, 星号为全站可跨域
	'access_allow' => ['*'],
	//允许跨域请求的地址, 如 http://localhost:8080, 星号为全站可跨域
	'access_allow_host' => ['*'],
	//上传的图片使用服务器存储, 0否(第三方存储), 1是
	'upload_local' => 1,
	//本地上传文件路径
	'upload_path' => '/public/uploads',
	//扩展库路径
	'extend_path' => '/public/extend',
	//REDIS参数
	'redis_setting' => [
		'host' => '127.0.0.1',
		'port' => 6379
	],
	//MEMCACHED参数
	'memcached_setting' => [
		'host' => '127.0.0.1',
		'port' => 11211
	],
	//SMARTY缓存
	'smarty_caching' => false,
	//SMARTY缓存时长, 单位秒
	'smarty_cache_lifetime' => 60 * 60,
	//SMARTY缓存路径
	'smarty_cache_path' => '/temp/cache_c',
	//SMARTY模板缓存路径
	'smarty_template_cache_path' => '/temp/templates_c',
	//SQL查询缓存路径
	'cache_sql_path' => '/temp/sql_c',
	//致命错误记录文件名
	'error_file' => '/temp/error.txt',
	//CRON定时器可执行IP白名单
	'cron_allow_ip' => ['127.0.0.1', '::1'],
	//加解密KEY
	'crypt_key' => 'MARIO_@AES_@20200703',
	//时区
	'default_timezone' => 'PRC',
	//是否SAAS
	'is_saas' => false,
	//小程序业务域名
	'miniprogram_domain' => 'www.laokema.com',
	//静态文件域名, http开头
	'static_domain' => '',//https://image.laokema.com
	//AJAX域名, http开头
	'ajax_domain' => ''//https://www.laokema.com
], $site_database);
