<?php
class cron extends core {
	private $order_mod;
	
	public function __construct() {
		parent::__construct();
		set_time_limit(0);
		ini_set('memory_limit', '10240M');
		$this->order_mod = m('order');
	}
	
	//生成平台授权文件
	public function create_auth() {
		$rs = SQL::share('platform_auth')->find();
		if ($rs) {
			$path = ROOT_PATH.'/temp/auth';
			delete_folder($path);
			makedir($path, false);
			foreach ($rs as $g) {
				file_put_contents($path.'/'.str_replace('/', '|', $g->auth), '');
			}
		}
		write_log('CREATE_AUTH', '/temp/cron.txt', false, true);
	}
	
	//作业总汇
	public function jobs() {
		//$this->order_cancel(); //取消24小时内没有支付的订单
		//$this->order_shouhuo(); //发货超过指定天数，系统确认收货，默认7天
		//$this->order_comment(); //已经完成的订单，默认一天后好评，5星
		//$this->order_refund(); //24小时后自动确认退货退款申请
		//$this->unvalid_coupon(); //使优惠券失效，过期的现金券状态为-2
		//$this->groupbuy_cancel(); //取消指定小时内没有拼团成功的订单，包括退款
	}
	
	//取消24小时内没有支付的订单
	public function order_cancel() {
		$now = time();
		$hours_24 = $now - 24 * 3600;
		SQL::share('order')->where("status='0' AND pay_time='0' AND add_time<='{$hours_24}'")->update(array('status'=>-1, 'cancel_time'=>$now));
		write_log('ORDER_CANCEL', '/temp/cron.txt', false, true);
	}

	//发货超过指定天数，系统确认收货，默认7天
	public function order_shouhuo() {
		$G_AUTO_SHOUHUO_DAYS = $this->request->act('G_AUTO_SHOUHUO_DAYS', 7, 'int', $this->configs);
		$day7 = time() - (($G_AUTO_SHOUHUO_DAYS-1)*24*3600);
		$rs = SQL::share('order')->where("shipping_time<='{$day7}' AND shouhuo_time=0")->find();
		if ($rs) {
			foreach ($rs as $k => $g) {
				$this->order_mod->shouhuo($g->id, $g->member_id);
			}
		}
		write_log('ORDER_SHOUHUO', '/temp/cron.txt', false, true);
	}

	//已经完成的订单，默认一天后好评，5星
	public function order_comment() {
		$after_one_day = date("Y-m-d H:i", time() + 24*3600);
		$orders = SQL::share('order')->where("status='3' AND shouhuo_time<'{$after_one_day}'")->find();
		if ($orders) {
			foreach ($orders as $key => $order) {
				$rs = SQL::share('order_goods')->where("order_id='{$order->id}'")->find();
				if ($rs) {
					//如果还没评论
					if ($rs->comment_time == 0) {
						SQL::share('order_goods')->where($rs->id)->update(array('comment_stars'=>5, 'comment_time'=>time(),
							'comment_content'=>'评价方未及时做出评价,系统默认好评!'));
					}
				}
				//改变订单状态
				SQL::share('order')->where($order->id)->update(array('status'=>4));
			}
		}
		write_log('ORDER_COMMENT', '/temp/cron.txt', false, true);
	}

	//24小时后自动确认退货退款申请
	public function order_refund() {
		$now = time();
		$day7 = $now - 7*24*3600;
		$rs = SQL::share('order_refund')->where("add_time<='{$day7}' AND status='0' AND add_time>'0'")->find();
		if ($rs) {
			$order = o('order');
			foreach ($rs as $k => $g) {
				$order->order_check_return_goods($g->order_id, '自动确认退货或退款', 1, false);
			}
		}
		write_log('ORDER_REFUND', '/temp/cron.txt', false, true);
	}

	//使优惠券失效，过期的现金券状态为-2
	public function unvalid_coupon() {
		$now = time();
		$rs = SQL::share('coupon')->where("status=1 AND end_time<'{$now}'")->find();
		if ($rs) {
			foreach ($rs as $k=>$g) {
				SQL::share('coupon_sn')->where("status=1 AND coupon_id='{$g->id}'")->update(array('status'=>-2));
			}
		}
		write_log('UNVALID_COUPON', '/temp/cron.txt', false, true);
	}
	
	//取消指定小时内没有拼团成功的订单，包括退款
	public function groupbuy_cancel() {
		$now = time();
		$hours_24 = $now - 24 * 3600;
		SQL::share('order')->where("status='0' AND pay_time='0' AND add_time<='{$hours_24}'")->update(array('status'=>-1, 'cancel_time'=>$now));
		write_log('GROUPBUY_CANCEL', '/temp/cron.txt', false, true);
	}
}
