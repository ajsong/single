<?php
class kuaidi extends plugin {
	public function __construct($type='') {
		parent::__construct();
		if (!strlen($type)) $type = 'kd100';
		$this->setAPI(__CLASS__, $type);
	}
	
	public function get($spellName, $mailNo){
		$result = $this->api->get($spellName, $mailNo);
		return $result;
	}
}
