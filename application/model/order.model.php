<?php
class order_model extends base_model {
	private $commission_mod;

	public function __construct() {
		parent::__construct();
		$this->commission_mod = m('commission');
	}

	//获取详情
	public function detail($id) {
		$row = SQL::share('order')->where($id)->row();
		return $row;
	}

	//0未支付，1已支付，2已发货，3完成（已收货），-1取消，-2退款，-3退货，-4未确认
	public function status_name($status) {
		$str = $status;
		switch ($status) {
			case 0:$str = '未支付';break;
			case 1:$str = '已支付';break;
			case 2:$str = '已发货';break;
			case 3:$str = '交易成功';break;
			case 4:$str = '已评价';break;
			case -1:$str = '取消';break;
			case -2:$str = '退款';break;
			case -3:$str = '退货';break;
			case -4:$str = '未确认';break;
		}
		return $str;
	}

	//确认收货
	public function shouhuo($order_id=0, $member_id=0) {
		if (!$order_id) $order_id = $this->request->post('id', 0);
		if ($order_id<=0) error('信息错误');
		if ($member_id==0) $member_id = $this->member_id;
		$order = SQL::share('order')->where("id='{$order_id}' AND member_id='{$member_id}'")->row();
		if (!$order) error('订单不存在');
		//已经确认过收货的，不能再确认。
		if ($order->status>=3) return;
		$rs = SQL::share('order')->where("id='{$order_id}' AND member_id='{$member_id}'")->update(array('status'=>3, 'shouhuo_time'=>time()));
		//订单佣金转账到会员的佣金
		if ($rs) $this->commission_mod->turn_commission($order_id);
	}

	//是否阅读订单
	public function readed($order) {
		$readed = 0;
		if ($this->member_id){
			if ($_SESSION['member']->member_type==2) {
				if ($order->reseller_read_time>0) $readed = 1;
			} else if ($_SESSION['member']->member_type==3) {
				if ($order->factory_read_time>0) $readed = 1;
			}
		}
		return $readed;
	}
	
	//支付类型
	public function pay_method($method, $unified=false) {
		switch ($method) {
			case 'alipay':$method = '支付宝支付';break;
			case 'wxpay':$method = $unified ? '微信支付' : 'APP内微信支付';break;
			case 'wxpay_h5':$method = $unified ? '微信支付' : '服务号内支付';break;
			case 'wxpay_mini':$method = $unified ? '微信支付' : '小程序内支付';break;
			case 'yue':$method = '余额支付';break;
			case 'offline':$method = '线下支付';break;
			case 'integral':$method = '积分支付';break;
		}
		return $method;
	}

	//支付成功后，设置订单状态，更新信息
	public function handle_paid_order($out_trade_no, $check_order_status=true) {
		if ($out_trade_no) {
			$flag = substr($out_trade_no, 0, 2);
			if ($flag == "CZ") {
				$row = SQL::share('member_recharge')->where("order_sn='{$out_trade_no}' AND status='0'")->row('recharged_money, member_id');
				if ($row) {
					SQL::share('member_recharge')->where("order_sn='{$out_trade_no}'")->update(array('status'=>1, 'pay_time'=>time()));
					SQL::share('member')->where($row->member_id)->update(array('money'=>array('`money`', "+{$row->recharged_money}")));
				}
				$this->log_result(SQL::share()->sql);
				//logResult($sql);
			} else {
				//订购服务的订单
				//check_order_status，是否检查订单的状态，如果支付宝回调，必须检查；在下单时，则不需要
				if ($check_order_status) {
					$status_sql = " AND status='0'";
				} else {
					$status_sql = "";
				}
				$order = SQL::share('order')->where("order_sn='{$out_trade_no}' {$status_sql}")->row('id, member_id, status');
				if ($order) {
					SQL::share('order')->where($order->id)->update(array('status'=>1, 'pay_time'=>time()));
					$this->log_result("{$out_trade_no}->{$order->id}: {SQL::share()->sql}");
				} else {
					$this->log_result("{$out_trade_no}不存在");
				}
				//logResult($sql);
			}
		}
	}

	//取消订单时退还金额
	public function return_yue_and_commission($order_id) {
		$order = SQL::share('order o')->inner('member m', 'o.member_id=m.id')
			->where("o.id='{$order_id}' AND member_id='{$this->member_id}'")->row('o.*, m.money as member_money, m.commission as member_commission');
		if ($order && $order->pay_method=='yue' && $order->status>0 && intval($order->cancel_time)==0) {
			//退还佣金和余额
			$commission = floatval($order->member_commission + $order->used_commission);
			$money = floatval($order->member_money + $order->used_money);
			SQL::share('member')->where($this->member_id)->update(array('commission'=>$commission, 'money'=>$money));
			SQL::share('order')->where($order_id)->update(array('cancel_time'=>time()));
			$_SESSION['member']->commission = $commission;
			$_SESSION['member']->money = $money;
		}
	}

	//写入日志
	private function log_result($log) {
		$file = fopen('temp/pay.txt', 'a+');
		fwrite($file, date('Y-m-d H:i:s')." {$log} \n\r<hr> ");
		fclose($file);
	}

}
