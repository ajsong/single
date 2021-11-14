<?php
require_once(dirname(__FILE__) . '/../AndroidNotification.php');

class AndroidCmdcast extends AndroidNotification {
	function __construct($type='') {
		parent::__construct();
		if (!strlen($type)) $type = 'broadcast';
		$this->data["type"] = $type;
		$this->data["device_tokens"] = NULL;
		$this->setPredefinedKeyValue('display_type', 'notification'); //notification-通知，message-消息
	}

}