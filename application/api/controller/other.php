<?php
class other extends core {
	public function __construct() {
		parent::__construct();
	}

	//配置开关
	//on20210519: 2021年5月19日提交给苹果的审核参数
	public function c() {
		$encode = $this->request->get('encode');
		if (strlen($encode)) {
			require_once(SDK_PATH . '/class/encrypt/encipher.php');
			$app = str_replace('\\', '/', dirname(ROOT_PATH));
			$folder = explode('/', $app);
			$folder = array_pop($folder);
			$encipher = new Encipher(ROOT_PATH, "{$app}/{$folder}_encode");
			$encipher->advancedEncryption = true;
			$encipher->exclude_path = array('.git', '.idea', 'PHPExcel', 'smarty/', 'alipay/', 'wxapi/Component', 'WxPayPubHelper', 'alidayu', 'Qiniu', 'upyun');
			$encipher->encode();
			exit('ENCODE FINISH');
		}
		$client = SQL::share('client')->row();
		$client_define = SQL::share('client_define')->row();
		//检测是否微信自动登录(一般小程序用)
		$wx_auto_login = (defined('WX_LOGIN') && WX_LOGIN && $this->is_wx) ? 1 : 0;
		$configs = array(
			'on20210519' => 0,
			'wx_auto_login' => $wx_auto_login,
			/*
			'app' => array( //app信息
				'app_name' => $client_define->WEB_NAME, //APP名称
				'navigation_bgcolor' => $client->navigation_bgcolor, //导航背景颜色
				'navigation_color' => $client->navigation_color, //导航文字颜色
				'tabbar' => array( //底栏,为NULL即不使用底栏
					'bgcolor' => '#ffffff', //底栏背景颜色
					'badge_dot' => 1, //badge使用红点代替数字
					'items' => array( //底栏选项卡,最多5个
						array(
							'ico'=>'http://yunfei.softstao.com/images/mobile/tabBar/tabBar1@2x.png', //图标
							'selected_ico'=>'http://yunfei.softstao.com/images/mobile/tabBar/tabBar1-x@2x.png', //选中图标
							'type'=>'url', //选项卡连接类型,url:跳转网址,im:即时通讯(设后下面url参数无效),不设即默认url
							'url'=>'/wap/', //跳转网址
							'text'=>'', //文字
							'color'=>'', //文字颜色
							'selected_color'=>'', //选中文字颜色
							'badge'=>0, //badge数
							'logined'=>0 //是否必须登录才可跳转网址
						),
						array(
							'ico'=>'http://yunfei.softstao.com/images/mobile/tabBar/tabBar2@2x.png',
							'selected_ico'=>'http://yunfei.softstao.com/images/mobile/tabBar/tabBar2-x@2x.png',
							'url'=>'/wap/?app=category&act=index'
						),
						array(
							'ico'=>'http://yunfei.softstao.com/images/mobile/tabBar/tabBar4@2x.png',
							'selected_ico'=>'http://yunfei.softstao.com/images/mobile/tabBar/tabBar4-x@2x.png',
							'url'=>'/wap/?app=cart&act=index'
						),
						array(
							'ico'=>'http://yunfei.softstao.com/images/mobile/tabBar/tabBar5@2x.png',
							'selected_ico'=>'http://yunfei.softstao.com/images/mobile/tabBar/tabBar5-x@2x.png',
							'url'=>'/wap/?app=member&act=index'
						)
					)
				)
			)
			*/
		);
		success($configs);
	}
	
	//获取支付参数
	public function pay() {
		$pay_method = $this->request->post('pay_method', 'wxpay');
		$order_sn = $this->request->post('order_sn');
		$total_price = $this->request->post('total_price', 0.00);
		$order_body = $this->request->post('order_body', WEB_NAME.'-订单');
		$order_param = $this->request->post('order_param');
		$order_option = $this->request->post('order_option', array());
		if (!strlen($order_sn) || $total_price<=0) error('支付参数缺失');
		if (strpos($pay_method, 'wxpay')!==false) {
			$api = p('pay');
		} else {
			$api = p('pay', 'alipay');
		}
		$jsApiParameters = $api->pay($order_sn, $total_price, $order_body, $order_param, $order_option);
		success($jsApiParameters);
	}

	//获取开机广告图
	public function launch_image() {
		$result = SQL::share('launch_image')->sort('id ASC')->find('pic');
		success($result);
	}

	//RSA证书生成
	public function rsa() {
		global $tbp;
		new Rsa(SDK_PATH . '/class/encrypt/keys', "{$tbp}private", "{$tbp}public");
		exit('Certificate created success');
	}
	
	//发送反馈
	public function feedback() {
		if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'mpcrawler')!==false) success('ok');
		$type_id = $this->request->post('type_id', 0);
		$name = $this->request->post('name');
		$mobile = $this->request->post('mobile');
		$content = $this->request->post('content');
		SQL::share('feedback')->insert(array('member_id'=>$this->member_id, 'name'=>$name, 'mobile'=>$mobile, 'content'=>$content, 'add_time'=>time(),
			'ip'=>$this->ip, 'type_id'=>$type_id));
		success('ok');
	}
	public function get_feedback() {
		echo '<meta charset="utf-8">
		<meta name="viewport" content="width=320,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no">
		<meta name="format-detection" content="telephone=no">
		<meta name="format-detection" content="email=no">
		<meta name="format-detection" content="address=no">';
		$row = SQL::share('feedback')->where(1)->row('content');
		if (!$row) exit('NO RECORD');
		exit($row->content);
	}
	
	//DEBUG收集
	public function debug() {
		if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'mpcrawler')!==false) success('ok');
		if (!IS_POST) success('ok');
		$header = json_encode($this->headers, JSON_UNESCAPED_UNICODE);
		$data = json_encode($_POST, JSON_UNESCAPED_UNICODE);
		$add_time = time();
		SQL::share('debug')->insert(compact('header', 'data', 'add_time'));
		success('ok');
	}
	
	//公众号、小程序订阅消息
	public function wechat_subscribe_message() {
		$parent_type = $this->request->get('parent_type', 0);
		$openid = $this->request->get('openid');
		$template_id = $this->request->get('template_id');
		if (!strlen($openid)) error('缺少openid');
		if (SQL::share('wechat_template_subscribe')->where("openid='{$openid}' AND send_time=0")->exist()) success('ok');
		$appid = $this->request->get('appId');
		if (!strlen($appid) && isset($this->headers['Appid'])) $appid = $this->headers['Appid'];
		if ($parent_type==0) {
			$wechat = SQL::share('wechat')->where("appid='{$appid}'")->row();
		} else {
			$wechat = SQL::share('miniprogram')->where("appid='{$appid}'")->row();
		}
		$data = compact('parent_type', 'template_id', 'openid');
		$data['parent_id'] = $wechat->id;
		$data['add_time'] = time();
		SQL::share('wechat_template_subscribe')->insert($data);
		success('ok');
	}
	
	//使用google api生成二维码图片, content二维码内容, size尺寸, lev容错级别, margin二维码离边框距离
	public function qrcode_google($content, $size=200, $level='Q', $margin=0) {
		$content = urlencode($content);
		return https()."chart.apis.google.com/chart?chs={$size}x{$size}&cht=qr&chld={$level}|{$margin}&chl={$content}";
	}
	
	//使用第三方接口生成二维码
	public function qrcode_encode($data='', $dir='', $show=true, $options=array()) {
		$url = 'http://qr.liantu.com/api/?text='.urlencode($data);
		if (is_array($options)) {
			if (isset($options['bg'])) $url .= '&bg='.$options['bg']; //背景颜色,如ffffff
			if (isset($options['fg'])) $url .= '&fg='.$options['fg']; //前景颜色
			if (isset($options['gc'])) $url .= '&gc='.$options['gc']; //渐变颜色
			if (isset($options['el'])) $url .= '&el='.$options['el']; //纠错等级,h|q|m|l
			if (isset($options['w'])) $url .= '&w='.$options['w']; //尺寸大小
			if (isset($options['m'])) $url .= '&m='.$options['m']; //外边距
			if (isset($options['pt'])) $url .= '&pt='.$options['pt']; //定位点颜色(外框)
			if (isset($options['inpt'])) $url .= '&inpt='.$options['inpt']; //定位点颜色(内点)
			if (isset($options['logo'])) $url .= '&logo='.$options['logo']; //logo图片,网址
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		if ($show) {
			header('Content-type: image/png');
			echo $data;
			exit;
		} else {
			$dir = UPLOAD_PATH.(strlen($dir)?'/'.$dir:'').'/'.date('Y').'/'.date('m').'/'.date('d');
			$qrcode = $dir.'/'.date('Ymd').rand(10000,99999).'.png';
			makedir(ROOT_PATH.$dir);
			file_put_contents(ROOT_PATH.$qrcode, $data);
			return $qrcode;
		}
	}
	
	//生成二维码
	public function qrcode($data='', $logo='', $dir='', $show=true, $width_percent=0.33) {
		if (!strlen($data)) $data = $this->request->get('data', '', 'urldecode'); //生成二维码的字符串
		if (!strlen($data)) error('MISSING QRCODE DATA');
		if (!strlen($logo)) $logo = $this->request->get('logo', '', 'urldecode'); //LOGO图片
		if (strlen($logo) && strpos($logo, 'http')===false && strpos($logo, ROOT_PATH)===false) $logo = ROOT_PATH . $logo;
		if (!strlen($dir)) $dir = $this->request->get('dir', '', 'urldecode'); //二维码存放的文件夹名称
		$percent = $this->request->get('percent', 0);
		if ($percent<=0) $percent = $width_percent; //0.33=1/3
		require_once(SDK_PATH . '/class/phpqrcode/phpqrcode.php');
		$errorCorrectionLevel = 'Q'; //容错级别
		$matrixPointSize = 6; //生成图片大小
		$dir = UPLOAD_PATH.(strlen($dir)?'/'.$dir:'').'/'.date('Y').'/'.date('m').'/'.date('d');
		$qrcode = $dir.'/'.date('Ymd').rand(10000,99999).'.png';
		makedir(ROOT_PATH.$dir);
		$filename = ROOT_PATH.$qrcode; //已生成的二维码图片文件名
		if (!strlen($logo)) {
			QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 0);
			if (class_exists('Imagick')) {
				$image = new Imagick($filename);
			} else {
				$image = imagecreatefromstring(file_get_contents($filename));
			}
		} else {
			QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 0);
			if (class_exists('Imagick')) {
				$radius = 3; //圆角
				$borderWidth = 3; //边框宽度
				$image = new Imagick($filename);
				if (strpos($logo, 'http')!==false) {
					$url = $logo;
					$logo = new Imagick();
					$logo->readImageBlob(file_get_contents($url));
				} else {
					if (!file_exists($logo)) $logo = ROOT_PATH.'/images/space.gif';
					$logo = new Imagick($logo);
				}
				$QR_width = $image->getImageWidth();
				$logo_small_width = $QR_width * $percent;
				$from_x = ($QR_width - ($logo_small_width+$borderWidth*2)) / 2;
				$logo->thumbnailImage($logo_small_width, $logo_small_width);
				if (method_exists($logo, 'roundCorners')) $logo->roundCorners($radius, $radius); //圆角
				$border = new Imagick(); //边框
				$border->newImage($logo->getImageWidth()+$borderWidth*2, $logo->getImageHeight()+$borderWidth*2, 'white', strtolower($logo->getImageFormat()));
				if (method_exists($logo, 'roundCorners')) $border->roundCorners($radius, $radius);
				$border->compositeImage($logo, Imagick::COMPOSITE_OVER, $borderWidth, $borderWidth);
				$image->setImageCompressionQuality(100);
				$image->compositeImage($border, Imagick::COMPOSITE_OVER, $from_x, $from_x);
				$logo->destroy();
				$border->destroy();
			} else {
				$QR = imagecreatefromstring(file_get_contents($filename));
				$logo = imagecreatefromstring(file_get_contents($logo));
				$QR_width = imagesx($QR); //二维码宽度
				$QR_height = imagesy($QR); //二维码高度
				$logo_width = imagesx($logo); //logo宽度
				$logo_height = imagesy($logo); //logo高度
				$logo_small_width = $QR_width * $percent;
				$from_x = ($QR_width - $logo_small_width) / 2;
				$image = imagecreatetruecolor($QR_width, $QR_height);
				$color = imagecolorallocate($image, 1000, 1000, 1000);//此处3个1000可以使背景设为白色，3个255可以使背景变成透明色
				imagefill($image, 0, 0, $color);
				imagecopyresampled($image, $QR, 0, 0, 0, 0, $QR_width, $QR_height, $QR_width, $QR_height);
				//调整logo大小
				$canvas = imagecreatetruecolor($logo_small_width, $logo_small_width);
				imagecopyresampled($canvas, $logo, 0, 0, 0, 0, $logo_small_width, $logo_small_width, $logo_width, $logo_height);
				//组合图片
				imagecopymerge($image, $canvas, $from_x, $from_x, 0, 0, $logo_small_width, $logo_small_width, 100);
				imagedestroy($logo);
				imagedestroy($canvas);
			}
		}
		if ($show) {
			header('Content-type: image/png');
			if (class_exists('Imagick')) {
				echo $image;
				$image->destroy();
			} else {
				imagepng($image);
				imagedestroy($image);
			}
			unlink($filename);
		} else {
			if (class_exists('Imagick')) {
				$image->writeImage($filename);
				$image->destroy();
			} else {
				imagepng($image, $filename);
				imagedestroy($image);
			}
		}
		return $qrcode;
	}
	
	//上传文件
	public function uploadfile() {
		$name = $this->request->request('name', 'filename');
		$dir = $this->request->request('dir', 'pic');
		$local = $this->request->request('local', UPLOAD_LOCAL);
		$detail = $this->request->request('detail', 0);
		$type = $this->request->request('type', 'jpg,jpeg,png,gif,bmp');
		if (isset($_REQUEST['type']) && !strlen($_REQUEST['type'])) $type = '';
		if (strlen($type)) $type = explode(',', $type);
		if ($this->is_wx && !$this->is_mini) {
			$media_id = $this->request->post($name, '', 'array');
			if (!$media_id) error('请选择文件');
			$file = array();
			$wxapi = new wechatCallbackAPI();
			$json = $wxapi->access_token();
			$access_token = $json['access_token'];
			foreach ($media_id as $g) {
				$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$g}";
				$data = $wxapi->downloadFile($url);
				$path = upload_obj_file($data, $dir, 'body', $local, $detail, $type);
				$file[] = $path;
			}
		} else {
			$file = $this->request->file($dir, $name, $local, $detail, $type);
		}
		if (!$file || (is_array($file) && !count($file))) error('请选择文件');
		success($file);
	}
	
	//获取省份
	public function get_province($is_show=true) {
		$result = SQL::share('province')->sort('province_id ASC')->find('province_id as id, name');
		if ($is_show) success($result);
		return $result;
	}
	
	//获取城市
	public function get_city($is_show=true) {
		$province_id = $this->request->get('province_id', 0);
		$result = SQL::share('city')->where("parent_id='{$province_id}'")->sort('city_id ASC')->find('city_id as id, name');
		if ($is_show) success($result);
		return $result;
	}
	
	//获取区县
	public function get_district($is_show=true) {
		$city_id = $this->request->get('city_id', 0);
		$result = SQL::share('district')->where("parent_id='{$city_id}'")->sort('district_id ASC')->find('district_id as id, name');
		if ($is_show) success($result);
		return $result;
	}
	
	//设置经纬度
	public function set_geo() {
		$longitude = $this->request->get('longitude', 0, 'float');
		$latitude = $this->request->get('latitude', 0, 'float');
		$_SESSION['longitude'] = $longitude;
		$_SESSION['latitude'] = $latitude;
		success('ok');
	}
	
	//获取地址的经纬度
	public function get_geo($address='', $is_show=true) {
		if (!$address) $address = $this->request->get('address');
		$url = "http://api.map.baidu.com/geocoder/v2/?ak={$this->baidu_ak}&address={$address}&output=json";
		$html = file_get_contents($url);
		$json = json_decode($html);
		if (!isset($json->status)) error('BAIDU API ERROR');
		if (intval($json->status)!=0) error($json->message);
		$data = array('longitude'=>floatval($json->result->location->lng), 'latitude'=>floatval($json->result->location->lat));
		if ($is_show) success($data);
		return $data;
	}
	
	//手机经纬度转百度坐标
	public function get_baidu_geo() {
		$longitude = $this->request->get('longitude', 0, 'float');
		$latitude = $this->request->get('latitude', 0, 'float');
		$url = "http://api.map.baidu.com/geoconv/v1/?ak={$this->baidu_ak}&coords={$longitude},{$latitude}";
		$html = file_get_contents($url);
		$json = json_decode($html);
		if (!isset($json->status)) error('BAIDU API ERROR');
		if (intval($json->status)!=0) error($json->message);
		$data = array(
			'longitude'=>$json->result[0]->x,
			'latitude'=>$json->result[0]->y
		);
		success($data);
	}
	
	//手机经纬度获取地址
	public function get_geo_address() {
		$longitude = $this->request->get('longitude', 0, 'float');
		$latitude = $this->request->get('latitude', 0, 'float');
		$url = "http://api.map.baidu.com/geocoder/v2/?ak={$this->baidu_ak}&location={$latitude},{$longitude}&coordtype=wgs84ll&output=json";
		$html = file_get_contents($url);
		$json = json_decode($html);
		if (!isset($json->status)) error('BAIDU API ERROR');
		if (intval($json->status)!=0) error($json->message);
		$data = array(
			'country'=>$json->result->addressComponent->country,
			'country_code_iso'=>$json->result->addressComponent->country_code_iso,
			'province'=>$json->result->addressComponent->province,
			'city'=>$json->result->addressComponent->city,
			'district'=>$json->result->addressComponent->district,
			'adcode'=>$json->result->addressComponent->adcode,
			'street'=>$json->result->addressComponent->street,
			'street_number'=>$json->result->addressComponent->street_number,
			'formatted_address'=>$json->result->formatted_address,
			'sematic_description'=>$json->result->sematic_description
		);
		success($data);
	}
	
	//获取两个经纬度之间的距离,单位公里
	//http://lbsyun.baidu.com/index.php?title=webapi/route-matrix-api-v2
	//驾车:driving, 骑行:riding, 步行:walking
	//round($result[0]->distance->value/1000, 1)
	public function get_distance($geo=array(), $is_show=true, $type='driving') {
		if (count($geo)) $_GET = $geo;
		$lng1 = (isset($_GET['lng1']) && trim($_GET['lng1'])) ? floatval($_GET['lng1']) : 0;
		$lat1 = (isset($_GET['lat1']) && trim($_GET['lat1'])) ? floatval($_GET['lat1']) : 0;
		$lng2 = (isset($_GET['lng2']) && trim($_GET['lng2'])) ? floatval($_GET['lng2']) : 0;
		$lat2 = (isset($_GET['lat2']) && trim($_GET['lat2'])) ? floatval($_GET['lat2']) : 0;
		if (!strlen($type) || !in_array($type, array('driving', 'riding', 'walking'))) $type = 'driving';
		$url = "http://api.map.baidu.com/routematrix/v2/{$type}?ak={$this->baidu_ak}&output=json&origins={$lat1},{$lng1}&destinations={$lat2},{$lng2}";
		$cnt = 0;
		$opts = array(
			'http' => array('method'=>'GET', 'timeout'=>3), //单位秒
			'ssl' => array('verify_peer'=>false, 'verify_peer_name'=>false) //不认证证书来源的检查
		);
		while ($cnt<2 && ($html=file_get_contents($url, false, stream_context_create($opts)))===FALSE) $cnt++;
		$json = json_decode($html);
		if (!isset($json->status)) {
			write_log('BAIDU API ERROR');
			$result = $this->fun_distance($lat1, $lng1, $lat2, $lng2);
			if ($is_show) success($result);
			return $result;
		}
		if (intval($json->status)!=0) {
			if (intval($json->status)==1) {
				//服务器内部错误
				$json = new stdClass();
				$json->result = $this->get_distance($geo, false, $type);
			} else {
				write_log("BAIDU Route Matrix API ERROR: {$json->message}");
				$result = $this->fun_distance($lat1, $lng1, $lat2, $lng2);
				if ($is_show) success($result);
				return $result;
			}
		}
		$result = (is_array($json->result) && count($json->result)) ? $json->result[0] : NULL;
		if ($is_show) success($result);
		return $result;
	}
	//使用数据库函数获取两个经纬度之间的距离,单位公里
	public function fun_distance($latitude1, $longitude1, $latitude2, $longitude2) {
		$sql = "SELECT fun_distance({$latitude1}, {$longitude1}, {$latitude2}, {$longitude2}) as distance";
		$distance = $this->db->get_var($sql);
		$distance = array('text'=>(floatval($distance)>=1?round($distance,1).'公里':round($distance*1000,1).'米'), 'value'=>intval($distance*1000));
		$distance = json_encode($distance);
		$distance = json_decode($distance);
		$duration = array('text'=>'未知', 'value'=>0);
		$duration = json_encode($duration);
		$duration = json_decode($duration);
		$result = new stdClass();
		$result->distance = $distance;
		$result->duration = $duration;
		return $result;
	}
	
	//获取两个地址之间的距离,单位公里
	public function get_distance_from_address($address1='', $address2='', $is_show=true) {
		if (!$address1) $address1 = $this->request->get('address1');
		if (!$address2) $address2 = $this->request->get('address2');
		$data1 = $this->get_geo($address1, false);
		$data2 = $this->get_geo($address2, false);
		$result = $this->get_distance(array(
			'lng1'=>$data1['longitude'], 'lat1'=>$data1['latitude'], 'lng2'=>$data2['longitude'], 'lat2'=>$data2['latitude']
		), false);
		if ($is_show) success($result);
		return $result;
	}
	
	//微信小程序工具
	public function wxtool() {
		success('ok');
	}
	public function image_base64() {
		$url = $this->image_online(true);
		echo image_base64($url);
		if (!preg_match('/^https?:\/\//', $url)) unlink(ROOT_PATH.$url);
		exit;
	}
	public function image_online($return=false) {
		if (!defined('WX_MINIPROGRAM') || !WX_MINIPROGRAM) error404();
		$type = $this->request->get('type', 'jpg,jpeg,png,gif,bmp');
		$type = preg_split('/\s*,\s*/', $type);
		$dir = (in_array('jpg', $type) || in_array('jpeg', $type) || in_array('png', $type) || in_array('gif', $type) || in_array('bmp', $type)) ? 'pic' : $type[0];
		$url = $this->request->file($dir, 'file', UPLOAD_LOCAL, false, $type);
		if (empty($url)) error('请选择图片');
		if ($return) return $url;
		exit(json_encode($url));
	}
	
	//获取快递公司列表
	public function shipping_company($is_show=true) {
		$result = SQL::share('shipping_company')->sort('sort ASC, name ASC')->find('pinyin, name');
		if ($is_show) success($result);
		return $result;
	}

	//物流查询
	public function kuaidi($spell_name='', $mail_no='') {
		if (!strlen($spell_name)) $spell_name = $this->request->get('spell_name');
		if (!strlen($mail_no)) $mail_no = $this->request->get('mail_no');
		if (!$spell_name || !$mail_no) error('缺少快递单号或订单编号');
		$kuaidi = p('kuaidi');
		$data = $kuaidi->get($spell_name, $mail_no);
		success($data);
	}
	
	//获取微信凭证
	public function get_wxinfo() {
		$type = $this->request->get('type');
		$suffix = $this->request->get('suffix');
		$callback = $this->request->get('callback');
		if (strlen($suffix)) $suffix = ".{$suffix}";
		$jssdk = new wechatCallbackAPI();
		$res = '';
		if ($type=='token') {
			$res = json_encode(array('access_token'=>$jssdk->getAccessToken("access_token{$suffix}.json")));
		} else if ($type=='ticket') {
			$res = json_encode(array('jsapi_ticket'=>$jssdk->getJsApiTicket("jsapi_ticket{$suffix}.json")));
		}
		if (strlen($callback)) $res = "{$callback}({$res})";
		echo $res;
		exit;
	}
	
	//通用成功显示
	public function complete() {
		$tips = $this->request->get('tips', '操作成功');
		$this->weixin_html($tips, '', false);
	}
	
	//在后台参数设置增加 G_IOS_UPDATE_VERSION、G_IOS_UPDATE_CONTENT、G_IOS_UPDATE_URL、G_ANDROID_UPDATE_VERSION、G_ANDROID_UPDATE_CONTENT、G_ANDROID_UPDATE_URL
	//APP检测更新
	public function update() {
		$source = $this->request->request('source'); #//客户端类型|ios苹果，android安卓
		$data = NULL;
		if ($this->is_app) {
			if (($this->is_ios || $source=='ios') && isset($this->configs['G_IOS_UPDATE_VERSION'])) {
				$data = new stdClass();
				$data->version = $this->configs['G_IOS_UPDATE_VERSION'];
				$data->content = $this->configs['G_IOS_UPDATE_CONTENT'];
				$data->url = $this->configs['G_IOS_UPDATE_URL'];
			} else if (($this->is_android || $source=='android') && isset($this->configs['G_ANDROID_UPDATE_VERSION'])) {
				$data = new stdClass();
				$data->version = $this->configs['G_ANDROID_UPDATE_VERSION'];
				$data->content = $this->configs['G_ANDROID_UPDATE_CONTENT'];
				$data->url = $this->configs['G_ANDROID_UPDATE_URL'];
			}
		}
		success($data);
	}
	
	//更新省市区街道数据
	public function update_city() {
		/*nginx配置http加
		fastcgi_connect_timeout 1200s; #原设置为300s
		fastcgi_send_timeout 1200s; #原设置为300s
		fastcgi_read_timeout 1200s; #原设置为300s
		fastcgi_buffer_size 64k;
		fastcgi_buffers 4 64k;
		fastcgi_busy_buffers_size 128k;
		fastcgi_temp_file_write_size 256k;*/
		set_time_limit(0);
		error_reporting(0);
		ini_set('memory_limit', '10240M');
		ini_set('max_execution_time', '80000');
		ini_set('request_terminate_timeout', '0');
		$year = $this->request->get('year', date('Y')-1);
		
		$fillZero = function($num) {
			$number = $num;
			if (strlen(strval($num))<6) {
				for ($i=strlen(strval($num)); $i<6; $i++) {
					$number .= '0';
				}
			} else {
				$number = substr($number, 0, 6);
			}
			return $number;
		};
		$curl = function($url) {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
			$content = curl_exec($curl);
			curl_close($curl);
			$content = iconv("gb2312", "utf-8//IGNORE", $content);
			return $content;
		};
		
		$sqls = "DROP TABLE IF EXISTS `{$this->tbp}province_temp`;
DROP TABLE IF EXISTS `{$this->tbp}city_temp`;
DROP TABLE IF EXISTS `{$this->tbp}district_temp`;
DROP TABLE IF EXISTS `{$this->tbp}town_temp`;
CREATE TABLE `{$this->tbp}province_temp` LIKE `{$this->tbp}province`;
INSERT INTO `{$this->tbp}province_temp` SELECT * FROM `{$this->tbp}province`;
CREATE TABLE `{$this->tbp}city_temp` LIKE `{$this->tbp}city`;
INSERT INTO `{$this->tbp}city_temp` SELECT * FROM `{$this->tbp}city`;
CREATE TABLE `{$this->tbp}district_temp` LIKE `{$this->tbp}district`;
INSERT INTO `{$this->tbp}district_temp` SELECT * FROM `{$this->tbp}district`;
CREATE TABLE `{$this->tbp}town_temp` LIKE `{$this->tbp}town`;
DROP TABLE IF EXISTS `{$this->tbp}province`;
CREATE TABLE `{$this->tbp}province` LIKE `{$this->tbp}province_temp`;
DROP TABLE IF EXISTS `{$this->tbp}city`;
CREATE TABLE `{$this->tbp}city` LIKE `{$this->tbp}city_temp`;
DROP TABLE IF EXISTS `{$this->tbp}district`;
CREATE TABLE `{$this->tbp}district` LIKE `{$this->tbp}district_temp`;
DROP TABLE IF EXISTS `{$this->tbp}town`;
CREATE TABLE `{$this->tbp}town` LIKE `{$this->tbp}town_temp`;";
		$sqls = explode(';', trim($sqls, ';'));
		foreach ($sqls as $sql) {
			SQL::share()->query($sql);
		}
		
		$js_province = $js_city = $js_district = $js_town = '';
		
		$sql_province = "INSERT INTO {$this->tbp}province (`province_id`, `name`) VALUES ";
		$sql_city = "INSERT INTO {$this->tbp}city (`city_id`, `name`, `parent_id`) VALUES ";
		$sql_district = "INSERT INTO {$this->tbp}district (`district_id`, `name`, `parent_id`) VALUES ";
		$sql_town = "INSERT INTO {$this->tbp}town (`town_id`, `name`, `parent_id`) VALUES ";
		
		$url = "http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/{$year}/";
		$index = $curl("{$url}index.html");
		preg_match_all("/<a href='(\d+).html'>([^<]+)<br\/><\/a>/", $index, $matcheProvince);
		if (!$matcheProvince) exit('PROVINCE DATA ERROR');
		for ($i=0; $i<count($matcheProvince[1]); $i++) {
			$province_id = $fillZero($matcheProvince[1][$i]);
			$province_name = $matcheProvince[2][$i];
			$sql_province .= "({$province_id}, '{$province_name}'),";
			$js_province .= "{$province_id}|{$province_name},";
			
			$index = $curl($url.$matcheProvince[1][$i].'.html');
			preg_match_all("/<a href='\d+\/(\d+).html'>([^<]+)<\/a><\/td><\/tr>/", $index, $matcheCity);
			if (!$matcheCity) exit('CITY DATA ERROR');
			
			for ($j=0; $j<count($matcheCity[1]); $j++) {
				$city_id = $fillZero($matcheCity[1][$j]);
				$city_name = $matcheCity[2][$j];
				if ($city_name=='市辖区' &&
					(intval(substr($city_id, 0, 2))==11 || intval(substr($city_id, 0, 2))==12 || intval(substr($city_id, 0, 2))==31)) $city_name = $province_name;
				$sql_city .= "({$city_id}, '{$city_name}', {$province_id}),";
				$js_city .= "{$city_id}|{$city_name},";
				$index = $curl($url.$matcheProvince[1][$i].'/'.$matcheCity[1][$j].'.html');
				preg_match_all("/<a href='\d+\/(\d+).html'>([^<]+)<\/a><\/td><\/tr>/", $index, $matcheDistrict);
				if (!$matcheDistrict) exit('PROVINCE DATA ERROR');
				
				for ($k=0; $k<count($matcheDistrict[1]); $k++) {
					$district_id = $fillZero($matcheDistrict[1][$k]);
					$district_name = $matcheDistrict[2][$k];
					$sql_district .= "({$district_id}, '{$district_name}', {$city_id}),";
					$js_district .= "{$district_id}|{$district_name},";
					
					$aru = substr($matcheCity[1][$j], 2, 2);
					$index = $curl($url.$matcheProvince[1][$i].'/'.$aru.'/'.$matcheDistrict[1][$k].'.html');
					preg_match_all("/<a href='\d+\/(\d+).html'>([^<]+)<\/a><\/td><\/tr>/", $index, $matcheTown);
					//部分省市的html不一样,需要重写规则
					if (!$matcheTown || !$matcheTown[0]) preg_match_all("/<td>(.{1,30})<\/td><td>\d{1,10}<\/td><td>([^<]+)<\/td><\/tr>/", $index, $matcheTown);
					if (!$matcheTown) exit('TOWN DATA ERROR');
					
					for ($l=0; $l<count($matcheTown[1]); $l++) {
						$town_id = substr($matcheTown[1][$l], 0, 9);
						$town_name = $matcheTown[2][$l];
						$sql_town .= "({$town_id}, '{$town_name}', {$district_id}),";
						$js_town .= "{$town_id}|{$town_name},";
					}
				}
			}
		}
		
		$sql_province = trim($sql_province, ',').';';
		$sql_city = trim($sql_city, ',').';';
		$sql_district = trim($sql_district, ',').';';
		$sql_town = trim($sql_town, ',').';';
		$sqls = $sql_province . $sql_city . $sql_district . $sql_town;
		$sqls = explode(';', trim($sqls, ';'));
		foreach ($sqls as $sql) {
			SQL::share()->query($sql);
		}
		
		$sqls = "INSERT INTO `{$this->tbp}province` (`province_id`, `name`) SELECT `province_id`, `name` FROM `{$this->tbp}province_temp` WHERE `province_id`>700000 ORDER BY `province_id` ASC;
INSERT INTO `{$this->tbp}city`(`city_id`, `name`, `parent_id`) SELECT `city_id`, `name`, `parent_id` FROM `{$this->tbp}city_temp` WHERE `city_id`>700000 ORDER BY `city_id` ASC;
INSERT INTO `{$this->tbp}district`(`district_id`, `name`, `parent_id`) SELECT `district_id`, `name`, `parent_id` FROM `{$this->tbp}district_temp` WHERE `district_id`>700000 ORDER BY `district_id` ASC;
DROP TABLE `{$this->tbp}province_temp`;
DROP TABLE `{$this->tbp}city_temp`;
DROP TABLE `{$this->tbp}district_temp`;
DROP TABLE `{$this->tbp}town_temp`;";
		$sqls = explode(';', trim($sqls, ';'));
		foreach ($sqls as $sql) {
			SQL::share()->query($sql);
		}
		
		$js = "let provinceDatas = '{$js_province}';\n
let cityDatas = '{$js_city}';\n
let districtDatas = '{$js_district}';\n
let townDatas = '{$js_town}';";
		file_put_contents(TEMP_PATH.'/city_data.js', $js);
		
		exit('UPDATE COMPLETE');
	}
	
	//生成视频html代码
	public function video() {
		$path = $this->request->get('path');
		$html = '<!doctype html>
<html style="font-size:100px;">
<head>
<meta name="viewport" content="width=320,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=0" />
<meta name="format-detection" content="telephone=no" />
<meta name="format-detection" content="email=no" />
<meta name="format-detection" content="address=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta charset="UTF-8">
<title>视频播放</title>
<style>
html, body{background:#000; height:100%; margin:0; padding:0; overflow:hidden;}
</style>
</head>
<body>
<video width="100%" height="100%" poster="" preload="auto" autoplay controls>
<source src="'.$path.'" type="video/mp4" />
您的浏览器不支持 video 标签。
</video>
</body>
</html>';
		echo $html;
		exit;
	}
	
	//生成微信支付html代码
	public function jsPayHtml($order_sn, $total_price, $order_body='订单', $order_param='', $options=array()) {
		$successUrl = isset($options['successUrl']) ? $options['successUrl'] : '';
		$errorAlert = isset($options['errorAlert']) ? $options['errorAlert'] : '';
		$callpay = isset($options['callpay']) ? intval($options['callpay']) : 1;
		$api = p('pay');
		$jsApiParameters = $api->pay($order_sn, $total_price, $order_body, $order_param, $options);
		$html = "<script>
function jsApiCall(){
	WeixinJSBridge.invoke(
		'getBrandWCPayRequest',
		{$jsApiParameters},
		function(res){
			//WeixinJSBridge.log(res.err_msg);
			if(res.err_msg && res.err_msg==='get_brand_wcpay_request:ok'){
				location.href = '".(strlen($successUrl)?$successUrl:https().$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?app={$this->app}&act={$this->act}")."';
			}else if(res.err_msg && res.err_msg==='get_brand_wcpay_request:cancel'){
				//
			}else if(res.errMsg){
				alert(".(strlen($errorAlert)?"'{$errorAlert}'":"'支付失败，原因：\\n'+res.errMsg").");
			}else{
				alert('支付失败，原因：未知');
			}
		}
	);
}
function callpay(){
	if (typeof WeixinJSBridge == 'undefined'){
		if(document.addEventListener){
			document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		}else if(document.attachEvent){
			document.attachEvent('WeixinJSBridgeReady', jsApiCall);
			document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		}
	}else{
		jsApiCall();
	}
}
".($callpay?'$(function(){ callpay() });':'')."
</script>";
		return $html;
	}
}
