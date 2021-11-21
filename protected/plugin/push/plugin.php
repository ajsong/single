<?php
class push extends plugin {
	public function __construct($type='') {
		parent::__construct();
		if (!strlen($type)) $type = 'umeng';
		$this->setAPI(__CLASS__, $type);
	}
	
	public function send($udid, $text, $extra=array(), $production_mode=true, $os='', $title='', $ticker=''){
		$result = $this->api->send($udid, $text, $extra, $production_mode, $os, $title, $ticker);
		return $result;
	}
}
