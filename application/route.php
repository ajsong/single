<?php
//路由配置
return [
	/*
	'^gm.cn' => [ //独立域名, ^开头
		APPLICATION_PATH.'/gm/', //程序路径
		'api(\/(\w+)?)?(\/(\w+)?)?\/?(\?(.*))?' => 'gm/api.php?app=$2&act=$4&$6',
		'api\/(\w+)\/(\w+)(\/.+)' => 'gm/api.php?app=$1&act=$2&_param=$3',
		'(\w+)?(\/(\w+)?)?\/?(\?(.*))?' => 'gm/index.php?app=$1&act=$3&$5',
		'(\w+)\/(\w+)(\/.+)' => 'gm/api.php?app=$1&act=$2&_param=$3'
	],
	*/
	'(wap|api|index)(\/v([\d.]+))?(\/(\w+)?)?(\/(\w+)?)?\/?(\?(.*))?' => 'api/$1.php?ver=$3&app=$5&act=$7&$9',
	'(wap|api|index)(\/v([\d.]+))?\/(\w+)\/(\w+)(\/.+)' => 'api/$1.php?ver=$3&app=$4&act=$5&_param=$6'
];
