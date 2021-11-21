<?php
$site_config = require_once(APPLICATION_PATH . '/config.php');
define('CRYPT_KEY', $site_config['crypt_key']);
/*$authFile = ROOT_PATH . '/temp/website.auth';
if (!file_exists($authFile) || file_get_contents($authFile)<=time()) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, 'http://gm.newpager.cc/auth/'.str_replace('/', '|', $site_config['platform_auth']));
	$res = json_decode(curl_exec($ch), true);
	curl_close($ch);
	if (!$res || !intval($res['code'])) exit(base64_decode('QVVUSE9'.'SSVpBVElP'.'TiBDT0RF'.'IEVSUk9S'));
	file_put_contents($authFile, time()+60*60*6);
}*/
$IS_ALONE_DOMAIN = false;
$DIRECTORY_PATH = '';
$s = substr($_SERVER['REQUEST_URI'], 1);
$page = APPLICATION_PATH.'/api/';
$route = require_once(APPLICATION_PATH . '/route.php');
$aloneHost = isset($route['^'.$_SERVER['HTTP_HOST']]) ? $route['^'.$_SERVER['HTTP_HOST']] : NULL;
if (is_array($aloneHost) && count($aloneHost)>1) {
	$IS_ALONE_DOMAIN = true;
	$aloneHost = array_reverse($aloneHost);
	foreach ($aloneHost as $k => $r) {
		if (is_numeric($k)) {
			$page = $r;
			$DIRECTORY_PATH = preg_replace('/^.+?\/(\w+)\/$/', '$1', $r);
		} else {
			$route = [$k => $r] + $route;
		}
	}
} else {
	$route = ['gm\/xfile(.*)' => 'gm/xfile.php$1'] + $route;
	$route = ['(gm|ag|op)\/(\w+)\/(\w+)(\/.+)' => '$1/index.php?app=$2&act=$3&_param=$4'] + $route;
	$route = ['(gm|ag|op)(\/(\w+)?)?(\/(\w+)?)?\/?(\?(.*))?' => '$1/index.php?app=$3&act=$5&$7'] + $route;
	$route = ['(gm|ag|op)\/api\/(\w+)\/(\w+)(\/.+)' => '$1/api.php?app=$2&act=$3&_param=$4'] + $route;
	$route = ['(gm|ag|op)\/api(\/(\w+)?)?(\/(\w+)?)?\/?(\?(.*))?' => '$1/api.php?app=$3&act=$5&$7'] + $route;
}
$route = ['wx\w{16}\/wx_interface(\/(wx\w{16}))?(\?(.*))?' => '/wx_interface.php?component_appid=$2&$4'] + $route;
$route = ['wx_interface(\/(wx\w{16}))?(\?(.*))?' => '/wx_interface.php?component_appid=$2&$4'] + $route;
$route = ['notify_url(.*)' => '/notify_url.php$1'] + $route;
$route = ['auth\/(.+)' => '/auth.php?auth=$1'] + $route;
$polymer = array(
	'tips' => 'THIS PAGE MAY BE ON MARS.',
	'icon' => '/images/404.svg',
	'iconWidth' => '2.5rem',
	'iconHeight' => '2.5rem',
	'bgColor' => '#fff',
	'textStyle' => 'margin-top:0.2rem;color:#999;'
);
foreach ($route as $k => $r) {
	if (preg_match('/^\^/', $k)) {
		foreach ($r as $_k => $_r) {
			if (is_numeric($_k) && preg_match('/^\/'.preg_replace('/^.+?\/(\w+)\/$/', '$1', $_r).'\b/', $_SERVER['REQUEST_URI'])) error_tip($polymer);
		}
		unset($route[$k]);
	}
}
if (strlen($s)) {
	if (strlen($s)>=64 && strpos($s, '.php')===false && strpos($s, '/')===false && strpos($s, '?')===false) {
		$secretCount = 0;
		if (preg_match('/_\d+$/', $s)) {
			$secretCount = intval(preg_replace('/^.+_(\d+)$/', '$1', $s));
			$s = preg_replace('/_\d+$/', '', $s);
		}
		$arr = decrypt_param($s, CRYPT_KEY, $secretCount);
		$s = $arr['result'];
		if (substr($s, 0, 1)=='/') $s = substr($s, 1);
	}
	$uri = '';
	foreach ($route as $k => $r) {
		if (!preg_match('/^'.$k.'$/', $s)) continue;
		if (substr($r, 0, 1)=='/') {
			$uri = preg_replace('/^'.$k.'$/', substr($r, 1), $s);
		} else {
			if (strlen($DIRECTORY_PATH)) $r = preg_replace('/^api\//', "{$DIRECTORY_PATH}/", $r);
			$uri = preg_replace('/^'.$k.'$/', APPLICATION_PATH.'/'.$r, $s);
		}
		$arr = explode('?', $uri);
		$page = $arr[0];
		if (isset($arr[1])) {
			$_GET = rewrite_change($arr[1]);
			$_REQUEST = array_merge($_GET, $_POST);
		}
		break;
	}
	if (!strlen($uri) && preg_match('/^\?/', $s)) $uri = 'index'.$s;
	if (!strlen($uri)) error_tip($polymer);
}
if (!strlen($page) || !file_exists($page)) error_tip($polymer);
if (strpos($page, '.php')===false) $page .= (substr($page, -1)=='/'?'':'/') . 'index.php';
require_once($page);
