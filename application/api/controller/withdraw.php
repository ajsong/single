<?php
class withdraw extends core {
	public $withdraw_mod;
	public function __construct() {
		parent::__construct();
		$this->withdraw_mod = m('withdraw');
	}
	
	public function index() {
		if ($this->member_id<=0) error('请登录', -100);
		$type = $this->request->get('type', 'commission'); //资金来源(member表的字段)，commission佣金
		if (!strlen($type)) error('数据错误');
		//判断表是否存在字段
		$row = SQL::share()->query("DESCRIBE {$this->tbp}member {$type}");
		if (!$row || (stripos($row->Type,'decimal(')===false && stripos($row->Type,'int(')===false)) error('数据错误');
		$member = SQL::share('member')->where($this->member_id)->row("{$type}");
		if (!$member) error('会员不存在');
		success(array('transfers_appid'=>WX_WITHDRAW_APPID, 'money'=>$member->$type));
	}

	//申请提现
	public function apply() {
		if ($this->member_id<=0) error('请登录', -100);
		//if (!$this->is_wx) $this->weixin_warning();
		$withdraw_money = $this->request->post('withdraw_money', 0, 'float');
		$bank_name = $this->request->post('bank_name');
		$bank_branch = $this->request->post('bank_branch');
		$name = $this->request->post('name');
		$bank_card = $this->request->post('bank_card');
		$bank_id = $this->request->post('bank_id', 0); //银行id,大于0覆盖上面4项
		$type = $this->request->post('type', 'commission'); //资金来源(member表的字段)，commission佣金
		$pay_method = $this->request->post('pay_method');
		
		if (isset($_POST['code'])) {
			$mobile = $this->request->post('mobile');
			$code = $this->request->post('code');
			$session_mobile = $this->request->session('sms_mobile');
			$session_code = $this->request->session('sms_code');
			if (!strlen($mobile)) error('手机号码不能为空');
			if (!strlen($code)) error('验证码不能为空');
			if ($mobile != $session_mobile) error('手机号码不正确');
			if ($code != $session_code) error('验证码不正确');
			$_SESSION['sms_code'] = '';
			$_SESSION['sms_mobile'] = '';
		}
		
		if (!strlen($pay_method)) {
			if ($this->is_wx && strlen(WX_WITHDRAW_APPID)) {
				if (IS_APP) {
					$pay_method = 'wxpay';
				} else {
					$pay_method = 'wxpay_h5';
				}
			} else if (strlen(ALIPAY_APPID)) {
				$pay_method = 'alipay';
			} else {
				$pay_method = 'manual';
			}
		}
		
		if (!strlen($type)) error('数据错误');
		//判断表是否存在字段
		$row = SQL::share()->query("DESCRIBE {$this->tbp}member {$type}");
		if (!$row || (stripos($row->Type,'decimal(')===false && stripos($row->Type,'int(')===false)) error('数据错误');
		
		//判断是否足够资金用来提现
		$money = floatval(SQL::share('member')->where($this->member_id)->value($type));
		if ($money<=0 || $money<$withdraw_money) error('当前账户余额不足以提现');
		
		if ($this->is_wx && strlen(WX_WITHDRAW_APPID) && $withdraw_money<0.3) error('提现金额不能低于0.3元');
		
		$bank = new stdClass();
		if ($bank_id>0) {
			$bank = SQL::share('member_bank')->where("id='{$bank_id}' AND member_id='{$this->member_id}'")->row();
			if ($bank) {
				$bank_name = $bank->bank_name;
				$bank_branch = $bank->bank_branch;
				$name = $bank->name;
				$bank_card = $bank->bank_card;
			}
		} else {
			$bank->bank_name = $bank_name;
			$bank->bank_branch = $bank_branch;
			$bank->name = $name;
			$bank->bank_card = $bank_card;
		}
		$order_sn = 'TX'.generate_sn();
		$member_id = $this->member_id;
		$add_time = time();
		$status = 0;
		$ip = $this->ip;
		//客户端类型
		$client_type = 0;
		if ($this->is_mini) $client_type = 1;
		else if ($this->is_ios) $client_type = 2;
		else if ($this->is_android) $client_type = 3;
		$alipay = $pay_method=='manual' ? SQL::share('member')->where($member_id)->value('alipay') : '';
		$withdraw_id = SQL::share('withdraw')->insert(compact('member_id', 'order_sn', 'pay_method', 'alipay', 'client_type', 'withdraw_money',
			'bank_name', 'bank_branch', 'name', 'bank_card', 'add_time', 'status', 'type', 'ip'));
		if ($pay_method != 'manual') {
			if (($this->is_wx && strlen(WX_WITHDRAW_APPID) || strlen(ALIPAY_APPID))) {
				if ($this->is_wx && strlen(WX_WITHDRAW_APPID)) {
					$api = p('pay');
				} else {
					$api = p('pay', 'alipay');
				}
				$result = $api->withdraw($order_sn, $withdraw_money, WEB_NAME.'-会员提现');
				if ($result['error']==1) {
					SQL::share('withdraw')->where($withdraw_id)->update(array('status'=>-2));
					error($result['msg']);
				}
			}
		}
		$money -= $withdraw_money;
		SQL::share('member')->where($this->member_id)->update(array("{$type}"=>$money));
		$_SESSION["member"]->$type = $money;
		if ($this->is_wx) {
			$weixin_mod = m('weixin');
			$weixin_mod->send_template($this->member_id, $withdraw_id, 9);
		}
		success($bank);
	}

	//20171221 by @jsong 弃用，使用member/withdraw_history代替
	//申请提现历史
	public function history() {
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 15);
		$results = SQL::share('withdraw')->where("member_id='{$this->member_id}'")->sort('id DESC')->limit($offset, $pagesize)
			->find('withdraw_money, add_time, withdraw_memo as memo, status');
		if ($results) {
			foreach ($results as $g) {
				$g->status_name = $this->withdraw_mod->status_name($g->status);
				$g->add_time = date("Y-m-d", $g->add_time);
				$g->title = '申请提现';
			}
		}
		success($results);
	}
	
	//检测提现密码
	public function password(){
		$password = $this->request->post('password');
		if ($password=='') error('提现密码不能为空');
		$row = SQL::share('member')->where($this->member_id)->row('withdraw_password, withdraw_salt');
		$crypt_password = crypt_password($password, $row->withdraw_salt);
		if ($row->withdraw_password!=$crypt_password) error('提现密码不正确');
	    success('ok');
    }

	public function complete(){
	    success('ok');
    }
}
