<?php
class recharge extends core {
	private $recharge_mod;
	public function __construct() {
		$this->recharge_mod = m('recharge');
		parent::__construct();
	}

	//充值列表
	public function index() {
		$recharges = SQL::share('recharge')->where("status=1")->sort('price ASC')->find();
		if ($recharges) {
			foreach ($recharges as $k => $recharge) {
				$recharges[$k]->recharged_money = $this->recharge_mod->recharged_money($recharge);
				$recharges[$k]->bonus = $recharge->recharged_money - $recharge->price;
			}
		}
		$money = floatval(SQL::share('member')->where($this->member_id)->value('money'));
		success(array( 'recharges'=>$recharges, 'money'=>$money ));
	}

	//20171221 by @jsong 弃用，使用member/money_history代替
	//充值明细
	public function history() {
		$type = $this->request->get('type', 'recharge');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 15);
		if ($type=="order") {
			$orders = SQL::share('order')->where("member_id='{$this->member_id}' AND pay_method='yue' AND status<>0")
				->sort('id DESC')->limit($offset, $pagesize)->find('id, add_time');
			if ($orders) {
				foreach ($orders as $k => $order) {
					$orders[$k]->content = "使用余额";
					$orders[$k]->add_time = date("Y-m-d H:i:s", $order->add_time);
				}
			}
			success($orders);
		} else {
			$histories = SQL::share('member_recharge')->where("member_id='{$this->member_id}' AND status='1'")
				->sort('id DESC')->limit($offset, $pagesize)->find("id, recharge_id, price, bonus, add_time, '' as content");
			if ($histories) {
				foreach ($histories as $k => $h) {
					$histories[$k]->add_time = date("Y-m-d H:i:s", $h->add_time);
					$content = "充值{$h->price}";
					if ($h->bonus) {
						$content .= "送{$h->bonus}";
					}
					$histories[$k]->content = $content;
				}
			}
			success($histories);
		}
	}

	//结算页面
	public function commit() {
		$id = $this->request->get('id', 0);
		if ($id<=0) error("找不到此记录");
		$recharge = SQL::share('recharge')->where("id='{$id}' AND status='1'")->row();
		if (!$recharge) error("找不到此记录");
		$recharge->recharged_money = $this->recharge_mod->recharged_money($recharge);
		$recharge->bonus = $recharge->recharged_money - $recharge->price;
		success($recharge);
	}

	//购买
	public function order($is_show=true) {
		$status = 0;
		$id = $this->request->post('id', 0);
		$pay_method = $this->request->post('pay_method', 'wxpay');
		$pay_method_name = $this->request->post('pay_method_name');
		$recharge = SQL::share('recharge')->where("id='{$id}' AND status='1'")->row();
		if (!$recharge) error("找不到此记录");
		if ($recharge) {
			$recharge->recharged_money = $this->recharge_mod->recharged_money($recharge);
			$recharge->bonus = $recharge->recharged_money - $recharge->price;
		}
		//客户端类型
		$client_type = 0;
		if ($this->is_mini) $client_type = 1;
		else if ($this->is_ios) $client_type = 2;
		else if ($this->is_android) $client_type = 3;
		//余额支付
		/*
		if ($pay_method=="yue") {
			if ($_SESSION['member']->money >= $recharge->price) {
				$remain = $_SESSION['member']->money - $recharge->price;
				$sql = "UPDATE {$this->tbp}member SET money='{$remain}' WHERE id='{$this->member_id}'";
				$this->db->query($sql);
				$_SESSION['member']->money = $remain;
				$status = 1;
			} else {
				error("您的余额不足以支付");
			}
		}
		*/
		//
		$order_sn = "CZ".generate_sn();
		$order_id = SQL::share('member_recharge')->insert(array('recharge_id'=>$id, 'member_id'=>$this->member_id, 'price'=>$recharge->price, 'client_type'=>$client_type,
			'recharged_money'=>$recharge->recharged_money, 'order_sn'=>$order_sn, 'bonus'=>$recharge->bonus, 'pay_method'=>$pay_method, 'status'=>$status,
			'add_time'=>time(), 'pay_time'=>0, 'is_mini'=>($this->is_mini?1:0)));
		
		$order_body = WEB_NAME.'-会员充值';
		$_SESSION['order_sn'] = $order_sn;
		$_SESSION['order_price'] = $recharge->price;
		$_SESSION['total_price'] = $recharge->price;
		$_SESSION['order_body'] = $order_body;
		$_SESSION['pay_method'] = $pay_method;
		$_SESSION['pay_method_name'] = $pay_method_name;
		if ($is_show) {
			$jsApiParameters = '';
			if ($this->is_app) {
				$api = p('pay', $pay_method);
				$jsApiParameters = $api->pay($order_sn, $recharge->price, $order_body);
			}
			success(array("status"=>$status, "order_id"=>$order_id, "order_sn"=>$order_sn, "total_price"=>$recharge->price, 'jsApiParameters'=>$jsApiParameters));
		}
	}

	//下单接口,微信端用,因为涉及到需要即时跳转去支付
	public function order_pay() {
		//判断是否从结算那里提交过来，如果是，表示是新订单，清空订单SESSION
		if (IS_POST) {
			$_SESSION['order_sn'] = '';
			$_SESSION['order_price'] = '';
			$_SESSION['total_price'] = '';
			$_SESSION['order_body'] = '';
			$_SESSION['pay_method'] = '';
			$_SESSION['pay_method_name'] = '';
		}
		$order_sn = $this->request->session('order_sn');
		$order_price = $this->request->session('order_price', 0, 'float');
		$order_body = $this->request->session('order_body');
		$pay_method = $this->request->session('pay_method', 'wxpay_h5');
		$pay_method_name = $this->request->session('pay_method_name');
		if ($order_sn=='' || $order_price<=0) {
			$this->order(false);
			$order_sn = $this->request->session('order_sn');
			$order_price = $this->request->session('order_price', 0, 'float');
			$order_body = $this->request->session('order_body');
			$pay_method = $this->request->session('pay_method', 'wxpay_h5');
			$pay_method_name = $this->request->session('pay_method_name');
		}
		if ($order_sn=='' || $order_price<=0) {
			error('订单信息错误！');
		}
		if (!strlen($order_body)) $order_body = WEB_NAME.'-会员充值';
		
		$type = '';
		switch ($pay_method) {
			case 'wxpay':case 'wxpay_h5':case 'wxpay_mini':$type = 'wxpay';break;
			case 'alipay':$type = 'alipay';break;
		}
		$api = p('pay', $type);
		$jsApiParameters = $api->pay($order_sn, $order_price, $order_body);
		
		success($jsApiParameters, '成功', 0, array("order_sn"=>$order_sn, "total_price"=>$order_price, "pay_method_name"=>$pay_method_name));
	}
}
