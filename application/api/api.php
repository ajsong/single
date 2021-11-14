<?php
define('DIRNAME', basename(dirname(__FILE__)));
define('APP_PATH', APPLICATION_PATH . '/' . DIRNAME . '/controller');
require_once(APPLICATION_PATH . '/helper.php');
define('TEMPLATE_PATH', APPLICATION_PATH . '/' . DIRNAME . '/view/' . WAP_TEMPLATE);

//初始化smarty
//framework/class/smarty/libs/sysplugins/smarty_internal_resource_file.php
$smarty->setCompileDir(ROOT_PATH . SMARTY_TEMPLATE_CACHE_PATH . '/' . DIRNAME . '/');
$smarty->setCacheDir(ROOT_PATH . SMARTY_CACHE_PATH . '/' . DIRNAME . '/');
$smarty->setTemplateDir(TEMPLATE_PATH);
$smarty->caching = SMARTY_CACHING;
$smarty->cache_lifetime = SMARTY_CACHE_LIFETIME;
//$smarty->clearCache('home.index.html');
//$smarty->clearAllCache();

//header('Access-Control-Allow-Origin:*'); //任意跨域
$isRSA = false; //供success函数输出值类型判断用
if (IS_POST) $_POST = getData(); //RSA加密获取数据(兼容非RSA传输)

if (preg_match("/^[a-zA-Z0-9_.-]+$/", $app) && preg_match("/^[a-zA-Z0-9_.-]+$/", $act)) {
	//如使用不同版本的参数ver，则调用此版本的类文件，如 home_v1.0.php
	if (strlen($ver)) {
		if (file_exists(APP_PATH . "/{$app}_v{$ver}.php")) $app = "{$app}_v{$ver}";
	}
	$file = APP_PATH . "/{$app}.php";
	if (file_exists($file)) {
		require_once($file);
		if (class_exists($app)) {
			$class = new $app();
			$function = $class->function;
			if (method_exists($class, $act)) {
				$class->$act();
			} else {
				error_tip('MISSING METHOD');
			}
		} else {
			error_tip('MISSING CONTROLLER');
		}
	} else {
		error_tip('MISSING FILE');
	}
} else {
	error_tip('WRONG FILE');
}
