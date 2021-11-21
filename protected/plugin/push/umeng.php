<?php
//友盟
//http://www.umeng.com
class umeng extends push_base {
	private $api;
	public function __construct() {
		parent::__construct();
		global $ios_app_key, $ios_master_secret, $android_app_key, $android_master_secret;
		require_once(PLUGIN_PATH . '/push/umeng/notify.php');
		$this->api = new notify($ios_app_key, $ios_master_secret, $android_app_key, $android_master_secret);
	}
	
	public function send($udid, $text, $extra=array(), $production_mode=true, $os='', $title='', $ticker='') {
		return $this->api->send($udid, $text, $extra, $production_mode, $os, $title, $ticker);
	}
}
