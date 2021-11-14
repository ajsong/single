<?php
//ini_set('session.save_handler', 'redis');
//ini_set('session.save_path', 'tcp://127.0.0.1:6379');
//ini_set('session.save_path', 'tcp://127.0.0.1:6379?auth=password');
session_start();
setcookie(session_name(), session_id(), time()+60*60*24/2, '/');
date_default_timezone_set($site_config['default_timezone']);
function_exists('header_remove') ? header_remove('X-Powered-By') : ini_set('expose_php', 'off');
define('E_FATAL', E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_PARSE);
define('API_VERSION', '11.2.20210520'); //sdk version
define('IS_POST', (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'));
define('IS_PUT', (strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT'));
define('IS_PATCH', (strtoupper($_SERVER['REQUEST_METHOD']) == 'PATCH'));
define('IS_DELETE', (strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE'));
define('IS_WAP', is_mobile_web());
define('IS_API', preg_match('/\/api(\/|\?|$)/', $_SERVER['REQUEST_URI']));
define('IS_WEB', !preg_match('/\/api(\/|\?|$)/', $_SERVER['REQUEST_URI']));
define('IS_GM', DIRNAME == 'gm');
define('IS_AG', DIRNAME == 'ag');
define('IS_OP', DIRNAME == 'op');
define('IS_APP', isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'laokema') !== false);
define('IS_WX_TOOLS', isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'wechatdevtools') !== false);
define('IS_WX', (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) || IS_WX_TOOLS);
define('IS_MINI', ((isset($_SERVER['HTTP_REFERER']) && stripos($_SERVER['HTTP_REFERER'], 'https://servicewechat.com/wx') !== false) || (isset($_REQUEST['is_miniprogram']) && intval($_REQUEST['is_miniprogram']) == 1)) && IS_WX);
define('PHP_SELF',  htmlentities(isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));
require_once(SDK_PATH . '/class/ez/ez_sql_core.php');
require_once(SDK_PATH . '/class/ez/ez_sql_mysql.php');
require_once(SDK_PATH . '/class/ez/ez_sql_mysqli.php');
require_once(SDK_PATH . '/class/ez/ez_sql_pdo.php');
require_once(SDK_PATH . '/class/ez/ez_sql_sqlite3.php');
require_once(SDK_PATH . '/class/ez/ez_results.smarty.php');
require_once(SDK_PATH . '/class/request/request.php');
require_once(SDK_PATH . '/class/smarty/libs/Smarty.class.php');
require_once(SDK_PATH . '/class/PHPExcel/Classes/PHPExcel.php');
require_once(SDK_PATH . '/class/imagick/imagick.class.php');
require_once(SDK_PATH . '/class/encrypt/rsa.php');
require_once(SDK_PATH . '/class/wxapi/class.wechatCallbackAPI.php');
require_once(SDK_PATH . '/SQL.php');
require_once(SDK_PATH . '/NOSQL.php');
require_once(SDK_PATH . '/shortcut.php');
require_once(APPLICATION_PATH . '/kernel.php');
require_once(APPLICATION_PATH . '/common.php');
require_once(MODEL_PATH . '/base.model.php');
require_once(PLUGIN_PATH . '/plugin.php');
require_once(PLUGIN_PATH . '/base.php');
require_once(APP_PATH . '/core.php');

if (!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
foreach ($site_config as $k => $g) {
	defined(strtoupper($k)) or define(strtoupper($k), is_array($g) ? json_encode($g, JSON_UNESCAPED_UNICODE) : $g);
}
$request = new Request();
$smarty = new Smarty();
$_GET = rewrite_change();
if (!get_magic_quotes_gpc()) {
	$_GET   = addslashes_deep($_GET);
	$_POST  = addslashes_deep($_POST);
	$_COOKIE = addslashes_deep($_COOKIE);
	$_SERVER = addslashes_deep($_SERVER);
}
$json = array(
	'data' => null,
	'msg_type' => 0,
	'msg' => 'SUCCESS',
	'error' => 0
);
$app = $request->get('app', 'home');
$act = $request->get('act', 'index');
$tpl = $request->get('tpl');
$ver = $request->get('ver');

if (!defined('API_DEBUG') || !API_DEBUG) {
	register_shutdown_function('fatalErrorHandler');
	set_error_handler('errorHandler');
}
function fatalErrorHandler() {
	$error = error_get_last();
	if ($error && ($error['type'] === ($error['type'] & E_FATAL))) {
		$errno   = $error['type'];
		$errstr  = $error['message'];
		$errfile = $error['file'];
		$errline = $error['line'];
		errorHandler($errno, $errstr, $errfile, $errline);
	}
}
function errorHandler($errno, $errstr, $errfile, $errline) {
	$e = new Exception;
	$trace = $e->getTraceAsString();
	$str = "message: {$errstr}
file: {$errfile}
line: {$errline}
type: {$errno}".(strlen(SQL::share()->sql)?PHP_EOL."sql: ".SQL::share()->sql:"")."
session: ".session_id()."
url: ".$_SERVER['REQUEST_URI']."
{$trace}";
	write_error($str);
}

$tbp = $site_config['db_master']['prefix'];
$db = SQL::connect('db_master');
$CONFIGS = array();
$WEB_CONFIGS = array();
$WEB_DEFINE = NULL;
$client_id = (defined('IS_SAAS') && IS_SAAS) ? intval($db->get_var("SELECT id FROM {$tbp}client WHERE host LIKE '%".$_SERVER['HTTP_HOST']."%'")) : 0;

//总config抽出为全局变量
$CONFIG = SQL::share('op_config')->cached(60*60*24*3)->find('name, content');
if ($CONFIG) {
	foreach ($CONFIG as $g) {
		$CONFIGS[$g->name] = $g->content;
		$WEB_CONFIGS[$g->name] = $g->content;
	}
}

if (IS_AG || IS_OP) {
	$client_id = 0;
	defined('WX_TAKEOVER') or define('WX_TAKEOVER', 0);
	require_once(SDK_PATH . '/class/wxapi/config.op.php');
} else {
	if (defined('IS_SAAS') && IS_SAAS && (!$client_id || is_null($client_id) || $client_id<=0)) {
		write_error('CLIENT ILLEGAL: ' . $_SERVER['HTTP_HOST']);
		error_tip('CLIENT ILLEGAL');
	}

	//客户config抽出为全局变量
	$CONFIG = SQL::share('config')->where((defined('IS_SAAS') && IS_SAAS) ? "client_id='{$client_id}'" : '')->cached(60*60*24*3)->find('name, content');
	if ($CONFIG) {
		foreach ($CONFIG as $g) {
			$CONFIGS[$g->name] = $g->content;
			$WEB_CONFIGS[$g->name] = $g->content;
		}
	}

	//固定参数定义
	$CONFIG = SQL::share('client_define')->where((defined('IS_SAAS') && IS_SAAS) ? "client_id='{$client_id}'" : '')->cached(60*60*24*3)->row();
	$WEB_DEFINE = $CONFIG;
	unsets($CONFIG, array('id', 'client_id'));
	foreach ($CONFIG as $k=>$g) {
		if ($g == NULL) $g = '';
		defined($k) or define($k, $g);
	}

	//前端模板路径
	$CONFIG = SQL::share('client_facade')->where((defined('IS_SAAS') && IS_SAAS) ? "client_id='{$client_id}'" : '')->cached(60*60*24*3)->find('facade, template');
	if ($CONFIG) {
		foreach ($CONFIG as $g) {
			defined(strtoupper($g->facade).'_TEMPLATE') or define(strtoupper($g->facade).'_TEMPLATE', $g->template);
		}
	}

	//客户数据抽出为全局变量
	$CONFIG = SQL::share('client')->where((defined('IS_SAAS') && IS_SAAS) ? $client_id : '')->cached(60*60*24*3)->row();
	unsets($CONFIG, array('id', 'name', 'host', 'add_time'));
	foreach ($CONFIG as $k=>$g) {
		if ($g == NULL) $g = '';
		if (in_array($k, array('push_fields', 'upload_fields', 'sms_fields'))) {
			$fields = explode('|', $g);
			foreach ($fields as $field) {
				$p = explode('：', $field);
				if (count($p)<=1) continue;
				$CONFIGS[$p[0]] = $p[1];
			}
		} else {
			$CONFIGS[$k] = $g;
		}
	}
}
extract($CONFIGS);
