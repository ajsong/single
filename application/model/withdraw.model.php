<?php
class withdraw_model extends base_model {

	public function __construct() {
		parent::__construct();
	}

	public function status_name($status) {
		$str = $status;
		switch($status){
			case -1:$str = '审核不通过';break;
			case -2:$str = '提现失败';break;
			case 0:$str = '等待审单';break;
			case 1:$str = '审核通过';break;
			case 2:$str = '提现成功';break;
		}
		return $str;
	}
}
