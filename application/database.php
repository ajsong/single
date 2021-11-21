<?php
return [
	//主数据库
	'db_master' => [
		//服务器
		'host' => 'localhost',
		//账号
		'user' => 'root',
		//密码
		'password' => 'zhangdong',
		//库名
		'name' => 'single',
		//编码
		'encoding' => 'utf8mb4',
		//表名前缀
		'prefix' => 'ws_',
		//类型, 有效值 MYSQL, PDO, SQLITE
		'type' => 'MYSQL'
	],
	//辅助数据库(从数据库)(只读)https://www.cnblogs.com/lelehellow/p/9633315.html
	'db_slaver' => []
];
