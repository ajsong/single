<?php
define('DIRNAME', 'api');
define('APP_PATH', APPLICATION_PATH . '/' . DIRNAME . '/controller');
require_once(APPLICATION_PATH . '/helper.php');

$signature = $request->get('signature');
$timestamp = $request->get('timestamp');
$nonce = $request->get('nonce');
$echostr = $request->get('echostr');
$component_appid = $request->get('component_appid');

$wxapi = new wechatCallbackAPI();
//$wxapi->log("GET:\n".json_encode($_GET));
//$wxapi->log("POST:\n".json_encode($_POST));
//$wxapi->log(file_get_contents('php://input'));
if (strlen($signature) && strlen($timestamp) && strlen($nonce) && strlen($echostr)) $wxapi->valid(); //验证

$callback = NULL;
$act = $request->request('act');
if (in_array($act, array('weixin_auth', 'mp_auth', 'component_auth', 'getcode', 'get_session_key', 'miniprogram'))) {
	$callback = $act;
	if ($act=='weixin_auth') $callback = NULL;
	//第三方接管的授权登录,appid为公众号AppID
	//www.website.com/wx_interface?act=mp_auth&appid=wx092909739421988d
	//电脑端打开进行公众号授权给第三方开发平台
	//www.website.com/wx_interface?act=component_auth
}
if (strlen($component_appid)) {
	$component = SQL::share('component')->where("appid='{$component_appid}'")->row();
	$wxapi->WX_THIRD = array(
		'appid'=>$component->appid,
		'secret'=>$component->appsecret,
		'token'=>$component->token,
		'aeskey'=>$component->aeskey
	);
}
$wxapi->responseMsg($callback); //获取事件推送

function error($msg='数据错误', $msg_type=-1, $error=1, $isJson=false) {
	global $json;
	$json['error'] = $error;
	$json['msg_type'] = $msg_type;
	$json['msg'] = $msg;
	if ($isJson) {
		die(json_encode($json));
	} else {
		$html = "<meta charset=\"UTF-8\">";
		$html .= "<script>alert('{$msg}');".(is_string($msg_type)?"location.href='{$msg_type}'":"history.back();")."</script>";
		exit($html);
	}
}

//授权登录后执行
function mp_auth($json) {
	global $wxapi, $db, $tbp;
}

//APP端传递code过来换取access_token与获取用户资料
function getcode($json) {
	global $wxapi, $request;
	$member_id = $request->get('member_id', 0);
	if (!$member_id) {
		$error = array('errcode'=>10001, 'errmsg'=>'lost member id');
		exit(json_encode($error));
	}
	$md5 = $wxapi->md5_userinfo($json);
	//$wxapi->log(json_encode($json));
	//$wxapi->log($md5);
	SQL::share('member')->where($member_id)->update(array('wx_name'=>$json['nickname'], 'md5'=>$md5));
	$data = array('md5'=>$md5);
	exit(json_encode($data));
}

//小程序传递code过来换取access_token与openid
function get_session_key($json) {
	exit(json_encode($json));
}

//关注
function subscribe($toUserName, $fromUserName) {
	global $wxapi;
	//$url = https().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	//$urls = explode('?', $url);
	//$url = $urls[0]."?weixin_auth=aes";
	//$wxapi->sendTextMsg($fromUserName, $toUserName, '请<a href="'.$url.'">点击这里</a>进行授权登录');
	if (strlen(WX_THIRDPARTY_APPID)) {
		$json = $wxapi->authorizer_access_token('', $toUserName);
		//$wxapi->log(json_encode($json));
		$json = $wxapi->get_userinfo($json['authorizer_access_token'], $fromUserName);
		$wxapi->log(json_encode($json));
		$md5 = $wxapi->md5_userinfo($json);
		$member = SQL::share('member')->where("md5='{$md5}'")->row();
		$wxapi->log(SQL::share()->sql);
		$wxapi->log(json_encode($member));
		if ($member) {
			SQL::share('member')->where($member->id)->update(array('wx_name'=>$json['nickname']));
			//关注后操作
			$brand_id = SQL::share('brand')->where("origin_id='{$toUserName}'")->value('id');
			$wxapi->log(SQL::share()->sql);
			$order = SQL::share('order o')->left('member_focus mf', 'mf.order_id=o.id')
				->where("mf.member_id='{$member->id}' AND mf.brand_id='{$brand_id}' AND o.status='3' AND is_focus='0' AND now_people<hope_people")
				->sort('o.id DESC')->row('o.*, mf.id as focus_id');
			$wxapi->log(SQL::share()->sql);
			$wxapi->log(json_encode($order));
			if ($order) {
				//关注表修改状态
				SQL::share('member_focus')->where($order->focus_id)->update(array('is_focus'=>1, 'focus_time'=>time()));
				//为会员增加关注报酬
				//配置参数
				$money = SQL::share('config')->where("name='min_focus_money' OR name='max_focus_money'")->sort('id DESC')->find('content');
				//根据配置参数随机拿到关注佣金
				$focus_money = substr($money[1]->content + mt_rand()/mt_getrandmax() * ($money[0]->content-$money[1]->content), 0, 3);
				SQL::share('member')->where($member->id)->update(array('money'=>array('`money`', "+{$focus_money}")));
				//$wxapi->log(SQL::share()->sql);
				//增加收入明细
				//SQL::share('financial_detail')
				//  ->insert(array('type'=>3, 'member_id'=>$member->id, 'money'=>$focus_money, 'brand_id'=>$order->brand_id, 'add_time'=>time()));
				//$wxapi->log(SQL::share()->sql);
				//订单当前关注人数增加人，且判断是否已够期望关注人数，如果够即修改状态
				$status = 3;
				$now_people = $order->now_people + 1;
				if ($now_people>=$order->hope_people) $status = -1;
				SQL::share('order')->where($order->id)->update(array('now_people'=>$now_people, 'status'=>$status));
				//如果满足订单完成条件而且没有其他推广订单，设为入驻订单，不让关注单可显示
				if ($status == -1) {
					$val = SQL::share('order')->where("brand_id='{$order->brand_id}' AND status=3")->find('id');
					if (!$val) {
						//入驻订单
						SQL::share('order')->where($order->id)->update(array('status'=>1));
					}
				}
				//发送推送
				//if ($member->udid!='') $notify->send($member->udid, "关注成功，恭喜你获得{$focus_money}个多元币");
			}
		}
	}
	return '';
}

//取消关注
function unsubscribe($toUserName, $fromUserName) {
	global $wxapi;
	return '';
}

//关键字回复
function msgText($toUserName, $fromUserName, $content='', $isMiniprogram=false) {
	global $wxapi;
	if (!strlen($content)) return '';
	if (strpos($content, 'QUERY_AUTH_CODE:')!==false) { //全网发布 - 返回Api文本检测
		$auth_code = trim(str_replace('QUERY_AUTH_CODE:', '', $content));
		$component_access_token = $wxapi->component_access_token();
		$url = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token={$component_access_token}";
		$data = array();
		$data['component_appid'] = WX_THIRDPARTY_APPID;
		$data['authorization_code'] = $auth_code;
		$json = $wxapi->requestData('post', $url, json_encode($data), true, true);
		$authorizer_access_token = $json['authorization_info']['authorizer_access_token'];
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$authorizer_access_token}";
		$data = array();
		$data['touser'] = $fromUserName;
		$data['msgtype'] = 'text';
		$data['text'] = array('content'=>"{$auth_code}_from_api");
		$wxapi->requestData('post', $url, json_encode($data), true, true);
		return '';
	} else if (strpos($content, 'TESTCOMPONENT_MSG_TYPE_TEXT')!==false) { //全网发布 - 返回普通文本检测
		return 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
	}
	$return = '';
	if ($isMiniprogram) {
		if (preg_match('/^66$/', $content)) {
			$content = '<a href="http://a.279618.com.cn/v/U1010SV3CR8">点我看看</a>';
			$filename = ROOT_PATH.'/temp/miniprogram/'.$wxapi->WX_THIRD['appid'].'/access_token.json';
			$json = $wxapi->access_token($wxapi->WX_THIRD['appid'], $wxapi->WX_THIRD['secret'], $filename);
			$access_token = $json['access_token'];
			$data = array();
			$data['access_token'] = $access_token;
			$data['touser'] = $fromUserName;
			$data['msgtype'] = 'text';
			$data['text'] = array('content'=>$content);
			$data = json_encode($data, JSON_UNESCAPED_UNICODE);
			$json = $wxapi->requestData('post', "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}", $data, true, true);
			if (isset($json['errcode']) && intval($json['errcode'])!=0) {
				$wxapi->log(json_encode($json), true);
			}
		}
	} else {
		preg_match('/(BL\d{5,6})/i', $content, $matcher);
		if ($matcher) {
			$return = $matcher[1];
		}
	}
	return $return;
}

//默认自动回复
function autoReply($toUserName, $fromUserName, $isMiniprogram=false) {
	global $wxapi;
	return '';
}

//扫描
function scan($fromUserName, $sceneValue, $toUserName='') {
	global $wxapi;
	//$wxapi->log($sceneValue);
	if ($toUserName) subscribe($toUserName, $fromUserName);
	if (!SQL::share('member_thirdparty')->where("mark='{$fromUserName}'")->exist() &&
		!SQL::share('openid')->where("openid='{$fromUserName}'")->exist()) {
		SQL::share('openid')->insert(array('openid'=>$fromUserName, 'reseller_id'=>$sceneValue));
	}
	return '';
}

//公众号授权给第三方开发平台后执行
//伪静增加 ^wx\w{16}/(.+)$ /$1
function component_auth($json) {
	global $wxapi, $component_appid;
	$html = '';
	$appid = $json['authorizer_appid'];
	if (isset($json['authorizer_info']['MiniProgramInfo'])) {
		$row = SQL::share('miniprogram')->where("appid='{$appid}'")->row('id');
		if (!$row) {
			$component = SQL::share('component')->where("appid='{$component_appid}'")->row();
			$component_id = $component->id;
			$name = $json['authorizer_info']['nick_name'];
			$username = $json['authorizer_info']['user_name'];
			$first = $json['authorizer_info']['MiniProgramInfo']['categories'][0]['first'];
			$second = $json['authorizer_info']['MiniProgramInfo']['categories'][0]['second'];
			$alias = $json['authorizer_info']['alias'];
			$pic = $json['authorizer_info']['head_img'];
			$qrcode = $json['authorizer_info']['qrcode_url'];
			$qrcode = download_file('wxqrcode', $qrcode, false, '.jpg');
			$file = PUBLIC_PATH.$qrcode;
			$fp = @fopen($file, 'r');
			$qrcode = @fread($fp, @filesize($file));
			@fclose($fp);
			$qrcode = upload_obj_file($qrcode, 'wxqrcode');
			$qrcode = add_domain($qrcode);
			@unlink($file);
			$serverdomain = defined('MINIPROGRAM_DOMAIN') ? MINIPROGRAM_DOMAIN : '';
			$businessdomain = defined('MINIPROGRAM_DOMAIN') ? MINIPROGRAM_DOMAIN : '';
			$miniprogram_id = SQL::share('miniprogram')->insert(compact('component_id', 'appid', 'name', 'username', 'first', 'second', 'alias', 'pic', 'qrcode', 'serverdomain', 'businessdomain'));
			
			$s = $second=='视频' ? 'VIDEO' : 'ARTICLE';
			$rs = SQL::share('config')->where("name LIKE 'G_{$s}%'")->find('id, content');
			if ($rs) {
				$data = array();
				foreach ($rs as $row) {
					$data[] = array('miniprogram_id'=>$miniprogram_id, 'config_id'=>$row->id, 'content'=>$row->content);
				}
				SQL::share('miniprogram_config')->insert($data);
			}
			
			$article = SQL::share('article')->sort('id ASC')->find('id');
			foreach ($article as $g) {
				SQL::share('article_attr')->insert(array('miniprogram_id'=>$miniprogram_id, 'article_id'=>$g->id));
			}
			
			if ($component_id==6) { //第三方平台创建小程序
				requestData('GET', STATIC_DOMAIN."/miniprogram.config.php?act=create&appid={$appid}", NULL);
			}
			
			$wxapi->miniprogramServerDomain($appid, defined('MINIPROGRAM_DOMAIN')?MINIPROGRAM_DOMAIN:'', 'set');
			if (intval($json['authorizer_info']['verify_type_info']['id'])>-1) { //未认证无法设置业务域名
				$wxapi->miniprogramBusinessDomain($appid, defined('MINIPROGRAM_DOMAIN')?MINIPROGRAM_DOMAIN:'', 'set');
			}
		} else {
			$html = '小程序已存在，更新信息成功';
			$name = $json['authorizer_info']['nick_name'];
			$pic = $json['authorizer_info']['head_img'];
			$qrcode = $json['authorizer_info']['qrcode_url'];
			$qrcode = download_file('wxqrcode', $qrcode, false, '.jpg');
			$file = PUBLIC_PATH.$qrcode;
			$fp = @fopen($file, 'r');
			$qrcode = @fread($fp, @filesize($file));
			@fclose($fp);
			$qrcode = upload_obj_file($qrcode, 'wxqrcode');
			$qrcode = add_domain($qrcode);
			@unlink($file);
			SQL::share('miniprogram')->where("appid='{$appid}'")->update(compact('name', 'pic', 'qrcode'));
		}
	} else {
		$row = SQL::share('wechat')->where("appid='{$appid}'")->row('id');
		if (!$row) {
			$component = SQL::share('component')->where("appid='{$component_appid}'")->row();
			$component_id = $component->id;
			$name = $json['authorizer_info']['nick_name'];
			$username = $json['authorizer_info']['user_name'];
			$type = $json['authorizer_info']['service_type_info']['id'];
			$alias = $json['authorizer_info']['alias'];
			$pic = $json['authorizer_info']['head_img'];
			$qrcode = $json['authorizer_info']['qrcode_url'];
			$qrcode = download_file('wxqrcode', $qrcode, false, '.jpg');
			$file = PUBLIC_PATH.$qrcode;
			$fp = @fopen($file, 'r');
			$qrcode = @fread($fp, @filesize($file));
			@fclose($fp);
			$qrcode = upload_obj_file($qrcode, 'wxqrcode');
			$qrcode = add_domain($qrcode);
			@unlink($file);
			SQL::share('wechat')->insert(compact('component_id', 'appid', 'name', 'username', 'type', 'alias', 'pic', 'qrcode'));
		} else {
			$html = '公众号已存在，更新信息成功';
			$name = $json['authorizer_info']['nick_name'];
			$pic = $json['authorizer_info']['head_img'];
			$qrcode = $json['authorizer_info']['qrcode_url'];
			$qrcode = download_file('wxqrcode', $qrcode, false, '.jpg');
			$file = PUBLIC_PATH.$qrcode;
			$fp = @fopen($file, 'r');
			$qrcode = @fread($fp, @filesize($file));
			@fclose($fp);
			$qrcode = upload_obj_file($qrcode, 'wxqrcode');
			$qrcode = add_domain($qrcode);
			@unlink($file);
			SQL::share('wechat')->where("appid='{$appid}'")->update(compact('name', 'pic', 'qrcode'));
		}
	}
	if (!strlen($html)) $html = '绑定授权成功';
	$html .= PHP_EOL.'<script>
if(window.top.document!==window.document){
	let count = 3, timer = setInterval(function(){
		if(count<=0){
			clearInterval(timer);timer = null;
			if(window.top.closeLay)window.top.closeLay();
			window.top.location.reload();
			return;
		}
		count--;
	}, 1000);
}
</script>';
	return $html;
}

//根据小程序账号切换第三方平台
function actMiniprogram($toUserName) {
	global $wxapi;
	$wechat = SQL::share('miniprogram')->where("username='{$toUserName}'")->row('component_id');
	if (!$wechat) return;
	$component = SQL::share('component')->where($wechat->component_id)->row('appid, appsecret, token, aeskey');
	if (!$component) return;
	$wxapi->WX_THIRD = array(
		'appid'=>$component->appid,
		'secret'=>$component->appsecret,
		'token'=>$component->token,
		'aeskey'=>$component->aeskey
	);
}

//小程序进入客服会话
function userEnterTempsession($toUserName, $fromUserName) {
	global $wxapi;
	$json = $wxapi->access_token(WX_PROGRAM_APPID, WX_PROGRAM_SECRET, ROOT_PATH.'/temp/miniprogram/'.WX_PROGRAM_APPID.'/access_token.json');
	$access_token = $json['access_token'];
	$data = array();
	$data['access_token'] = $access_token;
	$data['touser'] = $fromUserName;
	$data['msgtype'] = 'link';
	$data['link'] = array(
		'title'=>'军狮报读',
		'description'=>'新时事，新发现，最新的时事信息就在这里',
		'url'=>'http://a.279618.com.cn/v/U1010SV3CR8',
		'thumb_url'=>'http://mmbiz.qpic.cn/mmbiz_png/Xu9Z3Tb6AZS2zO0lSRmXfD3gWVezuc4yyRFtGwjT8ibXlicia9MAAW4o8ZWCeibD8d56QURkib0TVVCzMEfMkXnR5lA/0?wx_fmt=png'
	);
	$data = json_encode($data, JSON_UNESCAPED_UNICODE);
	$json = $wxapi->requestData('post', "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}", $data, true, true);
	if (isset($json['errcode']) && intval($json['errcode'])!=0) {
		$wxapi->log(json_encode($json));
	}
	return '';
}

//小程序客服消息卡片
function msgMiniprogramPage($toUserName, $fromUserName, $AppId='', $Title='', $PagePath='', $ThumbUrl='') {
	global $wxapi, $request;
	$wechat = SQL::share('miniprogram')->where("username='{$toUserName}'")->row();
	if (!$wechat) return '';
	if ($wechat->type==1) $s = 'VIDEO';
	else if ($wechat->type==2) $s = 'BLESSING';
	else if ($wechat->type==3) $s = 'BUDDHA';
	else $s = 'ARTICLE';
	$rs = SQL::share('config c')->left('miniprogram_config mc', 'mc.config_id=c.id')->where("c.name LIKE 'G_{$s}_CUSTOM_MESSAGE%' AND mc.miniprogram_id='{$wechat->id}'")->find('c.name, mc.content');
	if ($rs) {
		$configs = array();
		foreach ($rs as $row) {
			$configs[$row->name] = $row->content;
		}
	}
	$component = SQL::share('component')->where($wechat->component_id)->row();
	$wxapi->WX_THIRD = array(
		'appid'=>$component->appid,
		'secret'=>$component->appsecret,
		'token'=>$component->token,
		'aeskey'=>$component->aeskey
	);
	$json = $wxapi->authorizer_access_token('', $wechat->appid, true);
	if (!$json) return false;
	$access_token = $json['authorizer_access_token'];
	if (!strlen($access_token)) return false;
	$data = array();
	$data['touser'] = $fromUserName;
	$message_type = $request->act("G_{$s}_CUSTOM_MESSAGE_SEND_TYPE", 0, '', $configs);
	switch ($message_type) {
		case 0:
			$data['msgtype'] = 'text';
			$data['text'] = array(
				'content'=>$request->act("G_{$s}_CUSTOM_MESSAGE_SEND_TEXT", '', '', $configs)
			);
			break;
		case 1:
			$media_id = $request->act("G_{$s}_CUSTOM_MESSAGE_SEND_MEDIAID", '', '', $configs);
			$media_id = explode('|', $media_id);
			$data['msgtype'] = 'image';
			$data['image'] = array(
				'media_id'=>$media_id[0]
			);
			break;
		case 2:
			$data['msgtype'] = 'link';
			$data['link'] = array(
				'title'=>$request->act("G_{$s}_CUSTOM_MESSAGE_SEND_TITLE", '', '', $configs),
				'description'=>$request->act("G_{$s}_CUSTOM_MESSAGE_SEND_DESCRIPTION", '', '', $configs),
				'url'=>$request->act("G_{$s}_CUSTOM_MESSAGE_SEND_LINK", '', '', $configs),
				'thumb_url'=>add_domain($request->act("G_{$s}_CUSTOM_MESSAGE_SEND_IMG", '', '', $configs))
			);
			break;
		case 3:
			$media_id = $request->act("G_{$s}_CUSTOM_MESSAGE_SEND_MEDIAID", '', '', $configs);
			$media_id = explode('|', $media_id);
			$data['msgtype'] = 'miniprogrampage';
			$data['miniprogrampage'] = array(
				'title'=>$request->act("G_{$s}_CUSTOM_MESSAGE_SEND_TITLE", '', '', $configs),
				'pagepath'=>$request->act("G_{$s}_CUSTOM_MESSAGE_SEND_PATH", '', '', $configs),
				'thumb_media_id'=>$media_id[0]
			);
			break;
	}
	$data = json_encode($data, JSON_UNESCAPED_UNICODE);
	$json = $wxapi->requestData('post', "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}", $data, true, true);
	if (isset($json['errcode']) && intval($json['errcode'])!=0) {
		write_log(json_encode($json), true);
	}
	return '';
}

//小程序审核通过
function weappAuditSuccess($toUserName, $fromUserName) {
	global $wxapi;
	if (!strlen($fromUserName)) return '';
	$row = SQL::share('miniprogram')->where("username='{$toUserName}'")->row();
	if (!$row) return '';
	$component = SQL::share('component')->where($row->component_id)->row();
	$wxapi->WX_THIRD = array(
		'appid'=>$component->appid,
		'secret'=>$component->appsecret,
		'token'=>$component->token,
		'aeskey'=>$component->aeskey
	);
	$wxapi->miniprogramRelease($row->appid);
	$version = intval(str_replace('.', '', $row->version));
	$version++;
	$version = strval($version);
	$version = str_split($version);
	$version = implode('.', $version);
	$data = array();
	$data['review'] = 0;
	$data['promote_status'] = 1;
	$data['version'] = $version;
	$data['audit_status'] = 2;
	$data['audit_time'] = time();
	SQL::share('miniprogram')->where($row->id)->update($data);
	return '';
}

//小程序审核失败
function weappAuditFail($toUserName, $fromUserName, $reason, $screenshot=array()) {
	global $wxapi;
	if (!strlen($fromUserName)) return '';
	$row = SQL::share('miniprogram')->where("username='{$toUserName}'")->row();
	if (!$row) return '';
	$data = array();
	$data['review'] = 0;
	$data['promote_status'] = 1;
	$data['audit_status'] = -1;
	$data['audit_reason'] = $reason;
	$data['audit_time'] = time();
	if (count($screenshot)) $data['audit_screenshot'] = implode('|', $screenshot);
	SQL::share('miniprogram')->where($row->id)->update($data);
	return '';
}

//小程序审核延迟
function weappAuditDelay($toUserName, $fromUserName, $reason) {
	global $wxapi;
	if (!strlen($fromUserName)) return '';
	$row = SQL::share('miniprogram')->where("username='{$toUserName}'")->row();
	if (!$row) return '';
	$data = array();
	$data['review'] = 0;
	$data['promote_status'] = 1;
	$data['audit_status'] = -1;
	$data['audit_reason'] = $reason;
	$data['audit_time'] = time();
	SQL::share('miniprogram')->where($row->id)->update($data);
	return '';
}

//第三方平台快速注册小程序
function thirdFasteregister($component_appid, $appid, $info, $auth_code, $status=0, $reason='') {
	global $wxapi;
	$component = SQL::share('component')->where("appid='{$component_appid}'")->row();
	$component_id = $component->id;
	$name = $info['name'];
	$code = $info['code'];
	$legal_persona_wechat = $info['legal_persona_wechat'];
	$legal_persona_name = $info['legal_persona_name'];
	$row = SQL::share('miniprogram_box')
		->where("component_id='{$component_id}' AND status=0 AND name='{$name}' AND code='{$code}' AND legal_persona_wechat='{$legal_persona_wechat}' AND legal_persona_name='{$legal_persona_name}'")
		->row();
	if ($row) {
		if (strlen($reason)) {
			SQL::share('miniprogram_box')->where($row->id)->update(array('status'=>$status, 'reason'=>$reason));
		} else {
			$name = $row->app_name;
			$first = $row->category_first;
			$second = $row->category_second;
			$fast = 1;
			$status = 0;
			$serverdomain = defined('MINIPROGRAM_DOMAIN') ? MINIPROGRAM_DOMAIN : '';
			$businessdomain = defined('MINIPROGRAM_DOMAIN') ? MINIPROGRAM_DOMAIN : '';
			$wxapi->WX_THIRD = array(
				'appid'=>$component->appid,
				'secret'=>$component->appsecret,
				'token'=>$component->token,
				'aeskey'=>$component->aeskey
			);
			$wxapi->authorizer_access_token($auth_code);
			SQL::share('miniprogram_box')->where($row->id)->update(array('status'=>1));
			SQL::share('miniprogram')->insert(compact('component_id', 'appid', 'name', 'first', 'second', 'fast', 'status', 'serverdomain', 'businessdomain'));
			$wxapi->miniprogramServerDomain($appid, defined('MINIPROGRAM_DOMAIN')?MINIPROGRAM_DOMAIN:'', 'set');
			$wxapi->miniprogramBusinessDomain($appid, defined('MINIPROGRAM_DOMAIN')?MINIPROGRAM_DOMAIN:'', 'set');
		}
	}
	return '';
}
