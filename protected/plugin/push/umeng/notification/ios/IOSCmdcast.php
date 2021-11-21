<?php
require_once(dirname(__FILE__) . '/../IOSNotification.php');

class IOSCmdcast extends IOSNotification {
	function __construct($type='') {
		parent::__construct();
		if (!strlen($type)) $type = 'broadcast';
		$this->data["type"] = $type;
		$this->data["device_tokens"] = NULL;
		$this->setPredefinedKeyValue('content-available', '1');
	}

}