<?php
class groupbuy_model extends base_model {

	public function __construct() {
		parent::__construct();
	}

	//0拼团中，1拼团成功，-1拼团失败
	public function status_name($status) {
		$str = $status;
		switch ($status) {
			case 0:$str = '拼团中';break;
			case 1:$str = '拼团成功';break;
			case -1:$str = '拼团失败';break;
		}
		return $str;
	}

}
