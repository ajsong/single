<?php
class recharge_model extends base_model {

	public function __construct() {
		parent::__construct();
	}

	//获取充值的价格，考虑上是否在活动期内
	public function recharged_money($recharge) {
		$recharged_money = $recharge->recharged_money;
		if (is_object($recharge) && $recharge) {
			$now = time();
			if ($now >= $recharge->promotion_begin_time 
				&& $now <= $recharge->promotion_end_time 
				&& $recharge->promotion_recharged_money) {
				$recharged_money = $recharge->promotion_recharged_money;
			}
		}
		return $recharged_money;
	}

	public function status_name($status) {
		$str = $status;
		switch($status){
			case 0:$str = '隐藏';break;
			default:$str = '正常';break;
		}
		return $str;
	}	
}
