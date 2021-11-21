<?php
class address_model extends base_model {

	public function __construct() {
		parent::__construct();
	}

	//获取默认地址，已经登录的情况下才返回
	public function default_address() {
		if ($this->member_id>0) {
			$address = SQL::share('address')->where("member_id='{$this->member_id}'")->sort('is_default DESC, id DESC')->row();
			if (!$address) $address = $this->_init_address();
		} else {
			$address = $this->_init_address();
		}
		return $address;
	}

	//初始化一个地址对象。
	private function _init_address() {
		$address = t('address');
		return $address;
	}
}
