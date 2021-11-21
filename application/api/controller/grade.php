<?php
class grade extends core {
	private $member_mod;
	private $withdraw_mod;
	private $commission_mod;

	public function __construct() {
		parent::__construct();
		$this->member_mod = m('member');
		$this->withdraw_mod = m('withdraw');
		$this->commission_mod = m('commission');
	}
	
	//等级列表
	public function index() {
		$price = $this->request->get('price', 0);
		$orderby = $this->request->get('orderby', 'ASC');
		$orderby = strtoupper($orderby);
		$where = '';
		if ($price>0) $where = " AND price>0";
		if ($orderby!='ASC' && $orderby!='DESC') $orderby = 'ASC';
		$rs = SQL::share('grade')->where("status=1 {$where}")->sort("sort ASC, id {$orderby}")->find();
		success($rs);
	}
	
	//等级详情
	public function detail() {
		$id = $this->request->get('id', 0);
		if ($id<=0) error('缺少参数');
		$row = SQL::share('grade')->where("status=1 AND id='{$id}'")->row();
		if (!$row) error('该等级不存在');
		//获取当前等级的下个等级
		$score = 0;
		$next = SQL::share('grade')->where("status=1 AND id>'{$id}'")->sort('sort ASC, id ASC')->row('score');
		if ($next) $score = intval($next->score);
		if ($score==0) {
			$score = intval(SQL::share('grade')->where($id)->value('score'));
		}
		$row->next_score = "{$score}";
		success($row);
	}
	
	//购买等级,通过购买手段来达成
	public function order($is_show=true) {
		if ($this->member_id<=0) error('请登录', -100);
		$grade_id = $this->request->post('grade_id', 0);
		$pay_method = $this->request->post('pay_method', 'wxpay');
		if ($grade_id<=0) error('缺少参数');
		
		$row = SQL::share('grade')->where("status=1 AND id='{$grade_id}'")->row('score, price');
		if (!$row) error('该等级不存在');
		if ($this->member_score >= $row->score) error('您当前的会员等级已高于或等于该等级');
		
		$total_price = floatval($row->price);
		
		if ($pay_method=='yue') {
			if ($this->member_mod->is_yue_and_commission_enough($total_price)) {
				$status = 1;
				$pay_time = time();
			} else {
				error('您的佣金和余额不足以支付');
			}
		} else {
			$status = 0;
			$pay_time = 0;
		}
		
		//客户端类型
		$client_type = 0;
		if ($this->is_mini) $client_type = 1;
		else if ($this->is_ios) $client_type = 2;
		else if ($this->is_android) $client_type = 3;
		$order_sn = 'DJ'.generate_sn();
		$order_id = SQL::share('grade_order')->insert(array('member_id'=>$this->member_id, 'grade_id'=>$grade_id, 'order_sn'=>$order_sn, 'total_price'=>$total_price,
			'pay_method'=>$pay_method, 'pay_time'=>$pay_time, 'status'=>$status, 'add_time'=>time(), 'client_type'=>$client_type));
		
		//用余额支付，扣减余额和财富
		if ($pay_method == 'yue') {
			$score = intval(SQL::share('grade')->where($grade_id)->value('score'));
			SQL::share('member')->where($this->member_id)->update(array('grade_id'=>$grade_id, 'grade_score'=>$score));
			//$this->member_mod->update_grade_from_score($this->member_id);
			$this->member_mod->pay_with_yue_and_commission($total_price, $order_id, 4);
		}
		
		$pay_method_name = '';
		switch ($pay_method) {
			case 'alipay':$pay_method_name = '支付宝支付';break;
			case 'wxpay':case 'wxpay_h5':case 'wxpay_mini':$pay_method_name = '微信支付';break;
			case 'yue':$pay_method_name = '余额支付';break;
		}
		$order_body = WEB_NAME.'-激活等级';
		$_SESSION['order_sn'] = $order_sn;
		$_SESSION['total_price'] = $total_price;
		$_SESSION['order_body'] = $order_body;
		$_SESSION['pay_method'] = $pay_method;
		$_SESSION['pay_method_name'] = $pay_method_name;
		
		if ($is_show) {
			$jsApiParameters = '';
			if ($this->is_app) {
				$type = '';
				switch ($pay_method) {
					case 'wxpay':case 'wxpay_h5':case 'wxpay_mini':$type = 'wxpay';break;
					case 'alipay':$type = 'alipay';break;
				}
				$api = p('pay', $type);
				$jsApiParameters = $api->pay($order_sn, $total_price, $order_body);
			}
			success(array('order_id'=>$order_id, 'order_sn'=>$order_sn, 'total_price'=>$total_price, 'jsApiParameters'=>$jsApiParameters, 'pay_method'=>$pay_method));
		}
	}

	//购买等级,微信端用,因为涉及到需要即时跳转去支付
	public function order_pay() {
		//判断是否提交过来，如果是，表示是新订单，清空订单SESSION
		if (IS_POST) {
			$_SESSION['order_sn'] = '';
			$_SESSION['total_price'] = '';
			$_SESSION['order_body'] = '';
			$_SESSION['pay_method'] = '';
			$_SESSION['pay_method_name'] = '';
		}
		$order_sn = $this->request->session('order_sn');
		$total_price = $this->request->session('total_price', 0, 'float');
		$order_body = $this->request->session('order_body');
		$pay_method = $this->request->session('pay_method', 'wxpay_h5');
		$pay_method_name = $this->request->session('pay_method_name');
		if ($order_sn=='' || $total_price<=0) {
			$this->order(false);
			$order_sn = $this->request->session('order_sn');
			$total_price = $this->request->session('total_price', 0, 'float');
			$order_body = $this->request->session('order_body');
			$pay_method = $this->request->session('pay_method', 'wxpay_h5');
			$pay_method_name = $this->request->session('pay_method_name');
		}
		if ($order_sn=='' || $total_price<=0) {
			error('信息错误！');
		}
		if (!strlen($order_body)) $order_body = WEB_NAME.'-激活等级';
		
		if ($this->is_wx || strpos($pay_method, 'wxpay')!==false) {
			$api = p('pay');
		} else {
			$api = p('pay', 'alipay');
		}
		$jsApiParameters = $api->pay($order_sn, $total_price, $order_body);
		
		success($jsApiParameters, '成功', 0, array("order_sn"=>$order_sn, "total_price"=>$total_price, "pay_method_name"=>$pay_method_name));
	}
	
	//等级支付成功
	public function complete() {
		$order_sn = $this->request->get('order_sn');
		if ($order_sn) {
			$_SESSION['order_sn'] = '';
			$_SESSION['total_price'] = '';
			$_SESSION['pay_method_name'] = '';
		}
		success('ok');
	}
}
