<?php
class pay extends plugin {
	public function __construct($type='') {
		parent::__construct();
		if (!strlen($type)) $type = $this->request->get('paytype', 'wxpay');
		$this->setAPI(__CLASS__, $type);
	}
	
	public function pay($order_sn, $total_price, $order_body='订单', $order_param='', $options=array()) {
		$result = $this->api->pay($order_sn, $total_price, $order_body, $order_param, $options);
		return $result;
	}
	
	public function notify() {
		$data = $this->api->notify();
		if (is_array($data)) {
			$trade_no = isset($data['trade_no']) ? $data['trade_no'] : ''; //第三方平台订单号
			$out_trade_no = isset($data['out_trade_no']) ? $data['out_trade_no'] : ''; //商户订单号
			$total_fee = isset($data['total_fee']) ? $data['total_fee'] : 0; //支付金额
			if ($this->type=='wxpay') $total_fee = $total_fee/100; //单位统一为元
			$flag = substr($out_trade_no, 0, 2);
			if ($flag == 'CZ') { //充值
				$row = SQL::share('member_recharge')->where("order_sn='{$out_trade_no}' AND status=0")->row();
				write_log(SQL::share()->sql, '/temp/notify.txt');
				if ($row) {
					SQL::share('member_recharge')->where("order_sn='{$out_trade_no}'")
						->update(array('status'=>1, 'pay_time'=>time(), 'trade_no'=>$trade_no, 'total_fee'=>$total_fee));
					write_log(SQL::share()->sql, '/temp/notify.txt');
					SQL::share('member')->where($row->member_id)->update(array('money'=>array("`money`", "+{$row->recharged_money}")));
					write_log(SQL::share()->sql, '/temp/notify.txt');
					SQL::share('member_money_history')->insert(array('member_id'=>$row->member_id, 'money'=>$row->recharged_money, 'memo'=>'余额充值',
						'type'=>2, 'parent_id'=>$row->id, 'add_time'=>time(), 'status'=>1));
					write_log(SQL::share()->sql, '/temp/notify.txt');
				}
			} else if ($flag == 'DJ') { //等级
				$row = SQL::share('grade_order')->where("order_sn='{$out_trade_no}' AND status=0")->row('id, member_id, grade_id');
				write_log(SQL::share()->sql, '/temp/notify.txt');
				if ($row) {
					SQL::share('grade_order')->where($row->id)->update(array('status'=>1, 'pay_time'=>time(), 'trade_no'=>$trade_no, 'total_fee'=>$total_fee));
					write_log(SQL::share()->sql, '/temp/notify.txt');
					$score = SQL::share('grade')->where($row->grade_id)->value('score');
					write_log(SQL::share()->sql, '/temp/notify.txt');
					SQL::share('member')->where($row->member_id)->update(array('grade_id'=>$row->grade_id, 'grade_score'=>$score, 'grade_time'=>time()));
					write_log(SQL::share()->sql, '/temp/notify.txt');
					if ($this->type=='wxpay') {
						$weixin_mod = m('weixin');
						$weixin_mod->send_template($row->member_id, $row->id, 7);
					}
					$member_mod = m('member');
					$member_mod->update_grade_from_score($row->member_id);
				}
			} else { //订单
				$rs = SQL::share('order')->where("(pay_order_sn='{$out_trade_no}' OR parent_order_sn='{$out_trade_no}') AND status=0")->find();
				write_log(SQL::share()->sql, '/temp/notify.txt');
				if ($rs) {
					$member_id = 0;
					$integral = 0;
					foreach ($rs as $k=>$o) {
						SQL::share('order')->where($o->id)->update(array('status'=>1, 'pay_time'=>time(), 'trade_no'=>$trade_no, 'total_fee'=>$total_fee));
						write_log(SQL::share()->sql, '/temp/notify.txt');
						//增加积分
						$goods = SQL::share('goods g')->left('order_goods og', 'og.goods_id=g.id')->where("og.order_id='{$o->id}'")->find('g.give_integral, og.quantity');
						write_log(SQL::share()->sql, '/temp/notify.txt');
						if ($goods) {
							foreach ($goods as $kg=>$g) {
								$integral += $g->give_integral * $g->quantity;
							}
						}
						$member_id = $o->member_id;
					}
					SQL::share('member')->where($member_id)->update(array('integral'=>array("`integral`", "+{$integral}")));
					write_log(SQL::share()->sql, '/temp/notify.txt');
					//调用订单支付成功接口,更新库存、销量等
					$_GET = array('order_sn'=>$out_trade_no);
					$cart = o('cart');
					$cart->order_complete(false);
				}
			}
		}
	}
	
	public function refund($order_sn, $total_price, $out_refund_no, $pay_method='wxpay_h5', $trade_no='') {
		$result = $this->api->refund($order_sn, $total_price, $out_refund_no, $pay_method, $trade_no);
		if ($result['error']==0) {
			$flag = substr($order_sn, 0, 2);
			if ($flag == 'CZ') { //充值
			
			} else { //订单
				SQL::share('order')->where("order_sn='{$order_sn}'")->update(array('refund_time'=>time()));
				$order = SQL::share('order')->where("order_sn='{$order_sn}'")->row('id, member_id, pay_method');
				if ($order) {
					if ($this->type=='wxpay') {
						$weixin_mod = m('weixin');
						$weixin_mod->send_template($order->member_id, $order->id, 3);
					}
				}
			}
		}
		return $result;
	}
	
	public function withdraw($order_sn, $amount, $desc='用户提现') {
		$result = $this->api->withdraw($order_sn, $amount, $desc);
		if ($result['error']==0) {
			SQL::share('withdraw')->where("order_sn='{$order_sn}'")
				->update(array('trade_no'=>$result['trade_no'], 'pay_time'=>$result['pay_time'], 'status'=>2));
			$withdraw = SQL::share('withdraw')->where("order_sn='{$order_sn}'")->row('id, member_id, pay_method');
			if ($withdraw) {
				if ($this->type=='wxpay') {
					$weixin_mod = m('weixin');
					$weixin_mod->send_template($withdraw->member_id, $withdraw->id, 4);
				}
			}
		}
		return $result;
	}
	
}
