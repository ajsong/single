<?php
define('DIRNAME', 'api');
define('APP_PATH', APPLICATION_PATH . '/' . DIRNAME . '/controller');
require_once(APPLICATION_PATH . '/helper.php');
define('TEMPLATE_PATH', APPLICATION_PATH . '/' . DIRNAME . '/view/' . WAP_TEMPLATE);

//初始化smarty
//framework/class/smarty/libs/sysplugins/smarty_internal_resource_file.php
$smarty = new Smarty();
$smarty->setCompileDir(ROOT_PATH . SMARTY_TEMPLATE_CACHE_PATH . '/' . DIRNAME . '/');
$smarty->setCacheDir(ROOT_PATH . SMARTY_CACHE_PATH . '/' . DIRNAME . '/');
$smarty->setTemplateDir(TEMPLATE_PATH);

write_log("\$_GET\n".print_r($_GET, true), '/temp/notify.txt');
write_log("\$_POST\n".print_r($_POST, true), '/temp/notify.txt');
write_log("php://input\n".file_get_contents('php://input'), '/temp/notify.txt');

if (isset($_POST['trade_status'])) { //支付宝
	$api = p('pay', 'alipay');
	$api->notify();
} else if (isset($_POST['applepay_status'])) { //苹果支付
	$api = p('pay', 'applepay');
	$api->notify();
} else {
	$xml = file_get_contents('php://input');
	if (!empty($xml)) {
		libxml_disable_entity_loader(true);
		$obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		if ($obj && isset($obj->result_code)) { //微信
			$api = p('pay', 'wxpay');
			$api->notify();
		}
	}
}
