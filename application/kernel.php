<?php
class kernel {
	public $db;
	public $ezr;
	public $tbp;
	public $smarty;
	public $baidu_ak;
	public $act, $app;
	public $request;
	public $referer;
	public $is_wx, $is_mini, $is_web, $is_wap, $is_ios, $is_android, $is_app, $is_mario;
	public $ua, $wx_ua, $now, $ip;
	public $is_gm, $is_ag, $is_op;
	public $domain, $img_domain;
	public $configs;
	public $headers;
	public static $client = NULL;
	public $client_id;
	
	public function __construct() {
		global $db, $ezr, $tbp, $smarty, $baidu_ak, $app, $act, $request, $img_domain, $client_id;
		$this->db = $db;
		$this->ezr = $ezr;
		$this->tbp = $tbp;
		$this->smarty = isset($smarty) ? $smarty : NULL;
		$this->baidu_ak = (isset($baidu_ak) && strlen($baidu_ak)) ? $baidu_ak : 'iaDZrNldobQVbG7L357j8fIPKxIj8A1i';
		$this->app = $app;
		$this->act = $act;
		$this->request = $request;
		$this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$this->is_wx = defined('IS_WX') ? IS_WX : false;
		$this->is_mini = defined('IS_MINI') ? IS_MINI : false;
		$this->is_web = defined('IS_WEB') ? IS_WEB : false;
		$this->is_wap = defined('IS_WAP') ? IS_WAP : false;
		$this->ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$this->wx_ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/8.0.5(0x18000527) NetType/WIFI Language/zh_CN';
		$this->is_ios = strlen($this->ua) ? (stripos($this->ua, 'iPhone') !== false || stripos($this->ua, 'iPad') !== false) : false;
		$this->is_android = strlen($this->ua) ? (stripos($this->ua, 'Android') !== false || stripos($this->ua, 'Linux') !== false) : false;
		$this->is_app = (defined('IS_APP') && IS_APP) || $this->request->request('source') == 'ios' || $this->request->request('source') == 'android';
		$this->is_mario = strlen($this->ua) ? stripos($this->ua, 'mario') !== false : (isset($_REQUEST['mario']) && intval($_REQUEST['mario']) == 1);
		$this->now = time();
		$this->ip = ip();
		$this->is_gm = defined('IS_GM') ? IS_GM : false;
		$this->is_ag = defined('IS_AG') ? IS_AG : false;
		$this->is_op = defined('IS_OP') ? IS_OP : false;
		if (!IS_API && $this->smarty) {
			$this->smarty->assign('is_gm', $this->is_gm);
			$this->smarty->assign('is_ag', $this->is_ag);
			$this->smarty->assign('is_op', $this->is_op);
		}
		$this->domain = https().$_SERVER['HTTP_HOST'];
		if (strlen($img_domain)) {
			$this->img_domain = substr($img_domain, strlen($img_domain)-1)!='/' ? $img_domain : substr($img_domain, 0, strlen($img_domain)-1);
		} else {
			$this->img_domain = $this->domain;
		}
		$this->headers = $this->get_headers();
		$this->client_id = $client_id;
	}
	
	//?????????????????????
	public function get_headers($key='') {
		return get_header($key);
	}
	
	//??????Authorization
	public function get_authorization() {
		$username = '';
		$password = '';
		if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) { //Apache?????????
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
		} else if(isset($_SERVER['HTTP_AUTHORIZATION']) && stripos($_SERVER['HTTP_AUTHORIZATION'], 'basic')!==false) { //?????????????????? Nginx  Authorization
			$auth = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
			$username = isset($auth[0]) ? $auth[0] : '';
			$password = isset($auth[1]) ? $auth[1] : '';
		}
		return array($username, $password);
	}
	
	//??????????????????
	public function setConfigs() {
		global $WEB_CONFIGS;
		$this->configs = $WEB_CONFIGS;
	}
	
	//??????Referer
	public function setReferer() {
		$_SESSION['referer'] = $this->referer;
	}
	//??????Referer
	public function getReferer() {
		return $this->request->session('referer', '?');
	}
	
	//??????COOKIE??????????????????,token??????????????????????????????,???NULL???????????????, ???????????????token???, ???_token,name:16,token:32
	//CREATE TABLE `ws_member_token` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(16) DEFAULT NULL, `token` varchar(32) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8
	public function cookieAccount($table, $name, $token='', $field='m.*') {
		$master = explode('_', $table);
		$master = $master[0];
		if (strlen($token)) {
			return SQL::share("{$master} m")->left("{$table} t", 'm.name=t.name')->where("t.name='{$name}' AND t.token='{$token}'")->cached(60*60*24*7)->row($field);
		} else if (is_string($token)) {
			$token = md5(uniqid(rand(), true));
			SQL::share($table)->delete("name='{$name}'");
			SQL::share($table)->insert(compact('name', 'token'));
			setcookie("{$master}_name", $name, time()+60*60*24*365, '/');
			setcookie("{$master}_token", $token, time()+60*60*24*365, '/');
		} else {
			SQL::share($table)->delete("name='{$name}'");
			setcookie("{$master}_name", NULL, time()-60*60*24*365, '/');
			setcookie("{$master}_token", NULL, time()-60*60*24*365, '/');
		}
		return NULL;
	}
	
	//????????????
	public function send_notify($options=array()) {
		$this->notification($options);
	}
	//????????????
	public function send_sms($options=array()) {
		$options['sms_only'] = true;
		$this->notification($options);
	}
	//??????????????????????????????????????????????????????????????????
	public function notification($options=array()) {
		global $push_type, $sms_type;
		$members = $this->request->object('members', $options, -1); //??????????????????,-1????????????
		$content = $this->request->object('content', $options); //????????????,??????????????????
		$message_type = $this->request->object('message_type', $options); //??????????????????
		$target = $this->request->object('target', $options); //?????????????????????????????????
		$mobile = $this->request->object('mobile', $options); //????????????,????????????????????????????????????
		$udid = $this->request->object('udid', $options); //?????????UDID,????????????????????????
		$extends = $this->request->object('extends', $options, array()); //?????????????????????
		$sms = $this->request->object('sms', $options); //????????????
		$sign = $this->request->object('sign', $options); //????????????,????????????????????????
		$template_id = $this->request->object('template_id', $options, 0); //????????????id,????????????????????????
		$sms_only = $this->request->object('sms_only', $options, false); //???????????????
		if (is_numeric($members)) $members = array($members);
		foreach ($members as $member_id) {
			if ($member_id>0) {
				$member = SQL::share('member')->where($member_id)->row('udid, mobile');
				if ($member) $mobile = $member->mobile;
			} else if ($member_id<0 && isset($_SESSION['member'])) {
				$member_id = $_SESSION['member']->id;
				if (!strlen($mobile)) $mobile = $_SESSION['member']->mobile;
			}
			//???????????????
			if (!strlen($udid) && !$sms_only) {
				if (strlen($message_type)) {
					$type = $message_type;
				} else {
					if (strpos($content, '??????') !== false) {
						$type = 'goods';
					} else if (strpos($content, '??????') !== false) {
						$type = 'shop';
					} else if (strpos($content, '??????') !== false) {
						$type = 'order';
					} else {
						$type = 'html5';
					}
				}
				$this->send_message($content, $member_id, $type, $target);
			}
			//????????????
			if (!$sms_only && strlen($content) && $push_type!='nopush') {
				$this->config_notify($member_id, $content, array_merge($extends, array('type'=>'message')), $udid);
			}
			//????????????
			if (!strlen($udid) && (is_array($sms) || strlen($sms)) && strlen($mobile) && $sms_type!='nosms') {
				$sms_id = $this->save_sms($mobile, $sms);
				$api = p('sms', $sms_type);
				if ($api->send($mobile, $sms, $template_id, $sign)) $this->save_sms($mobile, $sms, $sms_id);
			}
		}
	}
	//????????????
	public function config_notify($member_id, $message, $extends=array(), $udid='') {
		global $push_type;
		if ($push_type!='nopush') {
			if (!strlen($udid)) {
				$where = "status=1 AND udid!=''";
				if ($member_id>0) {
					$where .= " AND id='{$member_id}'";
				} else if ($member_id<0) {
					if (!isset($_SESSION['member']) || !isset($_SESSION['member']->id) || $_SESSION['member']->id<=0) return;
					$where .= " AND id='".$_SESSION['member']->id."'";
				}
				$member = SQL::share('member')->where($where)->find('id, udid, badge');
				if ($member) {
					foreach ($member as $g) {
						$badge = $g->badge + 1; //??????APP??????
						SQL::share('member')->where($g->id)->update(array('badge'=>$badge));
						$extends = array_merge(array('badge'=>$badge), $extends);
						$this->put_notify($g->udid, $message, $extends);
					}
				}
			} else {
				$this->put_notify($udid, $message, $extends);
			}
		}
	}
	public function put_notify($udid, $message, $extends=array()) {
		global $push_type;
		if ($push_type!='nopush') {
			$api = p('push', $push_type);
			$api->send($udid, $message, $extends, API_DEBUG?false:true);
		}
	}
	//???????????????
	public function save_sms($mobile, $sms, $sms_id=0) {
		if ($sms_id>0) {
			return SQL::share('sms')->where($sms_id)->update(array('status'=>1));
		} else {
			return SQL::share('sms')->insert(array('mobile'=>$mobile, 'content'=>$sms, 'ip'=>$this->ip, 'add_time'=>time(), 'status'=>0));
		}
	}
	//?????????????????????
	public function send_message($content, $member_id, $type='', $target='') {
		$add_time = time();
		if ($member_id>0) {
			SQL::share('message')->insert(compact('member_id', 'content', 'type', 'target', 'add_time'));
		} else if ($member_id==0) {
			$members = SQL::share('member')->where("status=1 AND last_time>'".(time()-60*60*24*30)."'")->find('id'); //?????????????????????????????????
			if ($members) {
				$member_id = array();
				foreach ($members as $k=>$g) {
					$member_id[] = $g->id;
				}
				SQL::share('message')->insert(compact('member_id', 'content', 'type', 'target', 'add_time'), 'member_id');
			}
		}
	}
	
	//?????????????????????
	public function is_weixin() {
		if (isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')!==false || stripos($_SERVER['HTTP_USER_AGENT'], 'wechatdevtools')!==false)) {
			return true;
		} else {
			return false;
		}
	}
	
	//??????
	public function _handle_log() {
		if (!($this->app=='setting' && $this->act=='log')) {
			$log = m('log');
			$log->create();
		}
	}
	
	//????????????/????????????
	//????????????????????????????????????????????????????????????(private)????????????????????????
	public function __set($name, $value) {
		$array = explode('_', $name);
		if (count($array)<2) return;
		$subject = $array[0];
		$property = substr($name, strlen($subject)+1);
		if (!isset($_SESSION[$subject])) $_SESSION[$subject] = new stdClass();
		$_SESSION[$subject]->{$property} = $value;
	}
	//????????????????????????????????????????????????????????????(private)????????????????????????
	public function __get($name) {
		$array = explode('_', $name);
		if (count($array)<2) return NULL;
		$subject = $array[0];
		$property = substr($name, strlen($subject)+1);
		if (!isset($_SESSION[$subject])) return NULL;
		return isset($_SESSION[$subject]->{$property}) ? $_SESSION[$subject]->{$property} : NULL;
	}
}
