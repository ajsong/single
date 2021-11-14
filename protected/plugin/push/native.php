<?php
//原生推送
class native extends push_base {
	public function __construct() {
		parent::__construct();
	}
	
	public function send($udid, $text, $extra=array(), $production_mode=true, $os='', $title='', $ticker='') {
		if (!strlen($os)) {
			$this->sendIos($udid, $text, $extra, $production_mode);
			$this->sendAndroid($udid, $text, $extra, $production_mode);
			return true;
		} else {
			if ($os == 'ios') {
				return $this->sendIos($udid, $text, $extra, $production_mode);
			} else {
				return $this->sendAndroid($udid, $text, $extra, $production_mode);
			}
		}
	}
	
	//https://developer.apple.com/account
	private function sendIos($udid, $text, $extra=array(), $production_mode=true) {
		global $passpem, $passphrase;
		/*
		先从https://developer.apple.com/account下载推送证书
		在钥匙串访问生成aps_production.p12
		生成证书, 终端执行(要求输入macos登录密码)
		openssl x509 -in aps_production.cer -inform der -out cert.pem
		openssl pkcs12 -nocerts -out key.pem -in aps_production.p12
		openssl rsa -in key.pem -out key.unencrypted.pem
		cat cert.pem key.unencrypted.pem > push.pem
		push.pem 就是推送服务器要使用的推送证书
		openssl s_client -connect gateway.sandbox.push.apple.com:2195 -cert cert.pem -key key.unencrypted.pem 测试证书是否可用
		*/
		//Put your device token here (without spaces):
		$deviceToken = $udid;
		//Put your PEM pass phrase here:
		//$passphrase = '123456';
		//Put your alert message here:
		$message = $text;
		$debug = !$production_mode;
		$ctx = stream_context_create();
		//Open a connection to the APNS server
		if ($debug) {
			stream_context_set_option($ctx, 'ssl', 'local_cert', $passpem); //push.pem
			$ssl = 'ssl://gateway.sandbox.push.apple.com:2195';
			$message .= '(开发模式)';
		} else {
			stream_context_set_option($ctx, 'ssl', 'local_cert', $passpem);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
			$ssl = 'ssl://gateway.push.apple.com:2195';
		}
		$fp = stream_socket_client($ssl, $err, $errstr, 100, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		if (!$fp) exit("Failed to connect: $err $errstr");
		//Create the payload body
		$body['aps'] = array(
			'alert' => $message,
			'sound' => 'default',
			'badge' => 1
		);
		if (is_array($extra)) {
			foreach ($extra as $key=>$val) {
				if ($key=='aps') continue;
				$body[$key] = $val;
			}
		}
		//Encode the payload as JSON
		$payload = json_encode($body);
		//Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack('n', strlen($payload)) . $payload;
		//Send to the server
		$result = fwrite($fp, $msg, strlen($msg));
		//if (!$result) echo 'Message not delivered';
		//else echo 'Message successfully delivered';
		fclose($fp);
		return $result ? true : false;
	}
	
	//https://console.cloud.google.com/projectselector2/apis/credentials
	//https://www.cnblogs.com/zhwl/p/3370426.html
	private function sendAndroid($udid, $text, $extra=array(), $production_mode=true) {
		global $google_api_key; //AIzaSyCqK95iKMWzVaooV_so20z0gXrOnFQUE5c
		$ssl = 'https://android.googleapis.com/gcm/send';
		$data = array( "message" => $text );
		if (is_array($extra)) {
			foreach ($extra as $key=>$val) {
				if ($key=='message') continue;
				$data[$key] = $val;
			}
		}
		$fields = array(
			'registration_ids'  => array( $udid ),
			'data'              => $data,
		);
		$headers = array(
			'Authorization: key=' . $google_api_key,
			'Content-Type: application/json'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ssl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		//if ($result === FALSE) die('Problem occurred: ' . curl_error($ch));
		curl_close($ch);
		return $result === FALSE ? true : false;
	}
}
