<?php
//成功展示
function success($data, $msg='SUCCESS', $msg_type=0, $element=array(), $gourl='', $goalert='') {
	global $tbp, $app, $act, $smarty, $json, $request, $tpl, $isRSA, $edition, $jssdk;
	
	if ($request->request('source') != 'ios' && $request->request('source') != 'android') {
		$gourl = $request->request('gourl', $gourl);
		$goalert = $request->request('goalert', $goalert);
		if (!strlen($gourl)) $gourl = $request->session('gourl', $gourl);
		$_SESSION['gourl'] = '';
		if (strlen($gourl)) script($goalert, $gourl);
	}

	//后台
	if ((defined('IS_AG') && IS_AG) || (defined('IS_GM') && IS_GM) || (defined('IS_OP') && IS_OP)) {
		//api
		if (IS_API) {
			if (stripos($msg, '.html') !== false) $msg = 'SUCCESS';
			$json['data'] = $data;
			$json['msg_type'] = $msg_type;
			$json['msg'] = $msg;
			if (is_array($element)) foreach ($element as $key=>$val) $json[$key] = $val;
			$str = str_replace(':[]', ':null', json_encode($json));
			exit($str);
		}

		//html
		if (stripos($msg, '.html') !== false) {
			$template_file = $msg;
		} else {
			$template_file = "{$app}.{$act}.html";
		}
		if (defined('EXTEND_APP')) {
			$EXTEND_APP = json_decode(EXTEND_APP);
			foreach ($EXTEND_APP as $extend) {
				$IS_LAST = $EXTEND_APP[count($EXTEND_APP)-1] == $extend;
				if (!file_exists(APPLICATION_PATH . '/' . $extend . '/view/' . $template_file)) {
					if ($IS_LAST) {
						if (!file_exists(TEMPLATE_PATH . '/' . $template_file)) {
							error_tip('TEMPLATE DOES NOT EXISTS');
						}
					}
				}
			}
		} else {
			if (!defined('EXTEND_TEMPLATE_PATH') || !file_exists(EXTEND_TEMPLATE_PATH . '/' . $template_file)) {
				if (!file_exists(TEMPLATE_PATH . '/' . $template_file)) {
					error_tip('TEMPLATE DOES NOT EXISTS');
				}
			}
		}

		$smarty->assign($data);
		$smarty->assign($_GET);
		$smarty->assign($_POST);
		if (is_array($element)) $smarty->assign($element);
		$smarty->assign('app', $app);
		$smarty->assign('act', $act);
		$smarty->assign('domain', https().$_SERVER['HTTP_HOST']);
		$smarty->assign('WEB_NAME', defined('WEB_NAME') ? WEB_NAME : '');
		if (isset($_SESSION['admin'])) $smarty->assign('admin', $_SESSION['admin']);

		$output = $request->request('output');
		if ($output == 'json'){
			$vars = $smarty->getTemplateVars();
			unset($vars['output']);
			exit(json_encode($vars));
		}

		$smarty->display($template_file, md5($_SERVER['REQUEST_URI']));
		exit;
	}

	//前端
	//api
	if (IS_API) {
		$json['data'] = $data;
		$json['msg_type'] = $msg_type;
		$json['msg'] = $msg;
		$vars = $smarty->getTemplateVars();
		$json['edition'] = intval($vars['edition']); //系统功能版本
		$json['function'] = $vars['function']; //系统功能
		$json['badge'] = array(); //底部badge
		$configs = [];
		$arr = ['GLOBAL_MOBILE_CODE_NUM'];
		$_configs = $vars['configs'];
		foreach ($_configs as $k => $g) {
			if (in_array($k, $arr)) $configs[$k] = $g;
		}
		$json['configs'] = $configs; //系统功能
		//$json['red_dot'] = 1; //tabBar角标使用红点代替badge
		
		if (SQL::share()->tableExist('cart')) {
			$cart = o('cart');
			$carts = $cart->total(false);
			$json['cart_notify'] = $carts['quantity'];
			$json['badge'] = array('cart' => $carts['quantity']);
		}

		if (IS_MINI) {
			$core = o('core');
			$core->check_facade('miniprogram');
		}

		if (isset($_SESSION['member']) && isset($_SESSION['member']->id) && intval($_SESSION['member']->id)>0 && SQL::share()->tableExist('cart')) {
			$member = o('member');
			$num = $member->_get_cart_count();
			$json['member_cart'] = $num;
			$num = $member->_get_message_count();
			$num += $member->_get_status_order_count(1);
			$num += $member->_get_status_order_count(2);
			$num += $member->_get_status_order_count(3);
			$json['member_notify'] = $num;
		}

		if (is_array($element)) foreach ($element as $key => $val) $json[$key] = $val;
		$str = str_replace(':[]', ':null', json_encode($json));

		if (defined('RSA_POST') && RSA_POST && isset($isRSA) && $isRSA) {
			$rsa = new Rsa(SDK_PATH . '/class/encrypt/keys', "{$tbp}private", "{$tbp}public");
			$str = $rsa->privEncode($str);
		}
		
		exit($str);
	}

	//html
	if (stripos($msg, '.html') !== false) {
		$template_file = $msg;
	} else {
		if (preg_match('/^[a-z0-9._]+$/', $tpl)) {
			$template_file = "{$tpl}.html";
		} else {
			$template_file = "{$app}.{$act}.html";
		}
	}
	if (!defined('EXTEND_TEMPLATE_PATH') || !file_exists(EXTEND_TEMPLATE_PATH.'/'.$template_file)) {
		if (!file_exists(TEMPLATE_PATH.'/'.$template_file)) {
			error_tip('TEMPLATE DOES NOT EXISTS');
		}
	}

	$core = o('core');
	$core->check_facade("wap' OR facade='pc", $act == 'wxtool');

	if (isset($_SESSION['member']) && isset($_SESSION['member']->id) && intval($_SESSION['member']->id)) {
		$smarty->assign('logined', 1);
		$_SESSION['member'] = add_domain_deep($_SESSION['member'], array('avatar'));
		$_SESSION['member']->reg_time_word = date('Y-m-d', $_SESSION['member']->reg_time);
		$member = $_SESSION['member'];
		$smarty->assign('member', $member);
	} else {
		$smarty->assign('logined', 0);
		$member = t('member');
		$member->id = 0;
		$member->avatar = add_domain('/images/avatar.png');
		$smarty->assign('member', $member);
	}
	$smarty->assign('data', $data);

	$smarty->assign('edition', $edition);

	if (SQL::share()->tableExist('cart')) {
		$cart = o('cart');
		$carts = $cart->total(false);
		$smarty->assign('cart_notify', $carts['quantity']);
	}

	$smarty->assign($_GET);
	$smarty->assign($_POST);
	if (is_array($element)) $smarty->assign($element);

	$vars = $smarty->getTemplateVars();
	$url = $request->request('url');
	$smarty->assign('url', $url);
	$smarty->assign('is_app', IS_APP ? 1 : 0); //是否公司项目APP内打开网页
	$smarty->assign('is_wx', IS_WX ? 1 : 0);
	$smarty->assign('is_mini', IS_MINI ? 1 : 0);
	$smarty->assign('app', $app);
	$smarty->assign('act', $act);
	$smarty->assign('domain', https() . $_SERVER['HTTP_HOST']);
	if (!isset($vars['WEB_TITLE'])) $smarty->assign('WEB_TITLE', defined('WEB_TITLE') ? WEB_TITLE : '');
	$smarty->assign('WEB_NAME', defined('WEB_NAME') ? WEB_NAME : '');
	$smarty->assign('STATIC_DOMAIN', defined('STATIC_DOMAIN') ? STATIC_DOMAIN : '');
	$smarty->assign('AJAX_DOMAIN', defined('AJAX_DOMAIN') ? AJAX_DOMAIN : '');
	//$smarty->assign('WEB_DESCRIPTION', WEB_DESCRIPTION);
	//$smarty->assign('WEB_KEYWORDS', WEB_KEYWORDS);
	//$smarty->assign('cache_control', '86400');
	//$smarty->assign('cache_expires', gmdate('D, d M Y H:i:s', strtotime('+1 day')).' GMT');

	//分享
	if (isset($jssdk) && is_bool($jssdk) && $jssdk) {
		$qs = '';
		if(strlen($_SERVER['QUERY_STRING']))$qs .= '?' . $_SERVER['QUERY_STRING'];
		$link = https() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $qs;
		$link = preg_replace("/\?reseller=\d+&?/", '?', preg_replace("/&reseller=\d+/", '', $link));
		$qrcode = $request->get('qrcode');
		if (!strlen($qrcode) && $member->id>0) $link .= (strpos($link, '?') !== false ? '&' : '?') . "reseller={$member->id}";
		$share_title = defined('SHARE_TITLE') ? SHARE_TITLE : '';
		$share_desc = defined('SHARE_DESC') ? SHARE_DESC : '';
		$share_link = $link;
		$share_img = add_domain_deep(defined('SHARE_IMG') ? SHARE_IMG : '');
		if ($app == 'goods' && $act == 'detail') {
			$share_title = $data->name;
			$share_desc = $data->description;
			$share_img = add_domain_deep($data->pic);
		}
		$jssdk = new wechatCallbackAPI();
		$jssdk = $jssdk->getSignPackage();
		$jssdk['share'] = array(
			'title' => $share_title,
			'desc' => $share_desc,
			'link' => $share_link,
			'img' => $share_img
		);
		$smarty->assign('jssdk', 'Mario'.base64_encode(json_encode($jssdk)));
	}

	$output = $request->request('output');
	if ($output == 'json'){
		unset($vars['output']);
		exit(json_encode($vars));
	}
	$smarty->display($template_file, md5($_SERVER['REQUEST_URI']));
	exit;
}

//错误展示
function error($msg='DATA ERROR', $msg_type=0, $error=1, $isJson=false) {
	global $json, $request;
	$json['error'] = $error;
	$json['msg_type'] = $msg_type;
	$json['msg'] = $msg;
	
	$gourl = $request->request('gourl');
	if (strlen($gourl)) historyBack($msg);
	
	$errorto = $request->request('errorto');
	if (strlen($errorto)) {
		$sign = (isset($_SESSION['member']) && isset($_SESSION['member']->sign)) ? $_SESSION['member']->sign : '';
		$errorto = str_replace('<#sign#>', $sign, $errorto);
		if (stripos($errorto, 'history.back()') !== false || stripos($errorto, 'history.go(-1)') !== false) {
			$mixed = "javascript:{$errorto}";
		} else {
			$mixed = $errorto;
		}
		script($msg, $mixed);
	}
	
	//后台
	if ((defined('IS_AG') && IS_AG) || (defined('IS_GM') && IS_GM) || (defined('IS_OP') && IS_OP)) {
		//api
		if (IS_API) {
			exit(json_encode($json));
		}
		
		//html
		if ($isJson) {
			exit(json_encode($json));
		} else {
			script($msg, is_string($msg_type) ? $msg_type : 'javascript:history.back()');
		}
	}
	
	//前端
	//api
	if (IS_API) {
		if (defined('WX_LOGIN') && WX_LOGIN && ($msg_type==-9 || $msg_type==-100)) {
			$json['msg'] = '';
			$json['msg_type'] = -10;
		}
		if (is_string($msg_type)) {
			script($msg, !IS_APP ? $msg_type : 'javascript:history.back()');
		}
		exit(json_encode($json));
	}
	
	//html
	//重新登录
	if ($msg_type == -100) {
		$gourl = '/wap/?tpl=login';
		if (defined('EXTEND_TEMPLATE_PATH')) {
			if (file_exists(EXTEND_TEMPLATE_PATH . '/home.login.html')) $gourl = '/wap/home/login';
		} else if (!file_exists(TEMPLATE_PATH . '/login.html')) {
			if (file_exists(TEMPLATE_PATH . '/home.login.html')) $gourl = '/wap/home/login';
			else $gourl = '/wap/passport/wx_login';
		}
		location($gourl);
	}
	//AJAX请求,bodyView的时候用
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		exit(json_encode($json));
	}
	//商品下架
	if ($msg_type == -99) {
		script('该商品已经被抢光，库存不足已下架。', '/wap/');
	}
	switch ($msg_type) {
		case -9:
			script($msg, '/api/?app=passport&act=logout&gourl=' . urlencode('/wap/member/index'));
			break;
		case -1:
			script($msg, '/wap/');
			break;
		default:
			script($msg, (!IS_APP && is_string($msg_type)) ? $msg_type : 'javascript:history.back()');
			break;
	}
	exit;
}
