<?php
class bank extends core {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$banks = SQL::share('member_bank')->where("member_id='{$this->member_id}'")->sort('id ASC')->find();
		success($banks);
	}

	public function edit() {
		$id = $this->request->get('id', 0);
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$bank_name = $this->request->post('bank_name');
			$address = $this->request->post('address');
			$bank_branch = $this->request->post('bank_branch');
			$name = $this->request->post('name');
			$bank_card = $this->request->post('bank_card');
			$data = compact('bank_name', 'bank_branch', 'bank_card', 'name', 'address');
			if ($id) {
				SQL::share('member_bank')->where("member_id='{$this->member_id}' AND id='{$id}'")->update($data);
			} else {
				$data['member_id'] = $this->member_id;
				$data['add_time'] = time();
				$id = SQL::share('member_bank')->insert($data);
				//检测是否已设置提现密码
				if (!SQL::share('member')->where("id='{$this->member_id}' AND withdraw_password!=''")->count()) {
					$_POST['gourl'] = '/wap/?app=bank&act=password_set';
					$_POST['goalert'] = '请设置提现密码';
				}
			}
			$banks = SQL::share('member_bank')->where("member_id='{$this->member_id}' AND id='{$id}'")->row();
			success($banks);
		} else if ($id>0) {
			$row = SQL::share('member_bank')->where("member_id='{$this->member_id}' AND id='{$id}'")->row();
		} else {
			$row = t('member_bank');
		}
		success($row);
	}

	public function delete() {
		$id = $this->request->post('id', 0);
		SQL::share('member_bank')->delete("id='{$id}' AND member_id='{$this->member_id}'");
		success('ok');
	}

    public function set(){
        success('ok');
    }
	
	//设置密码
	public function password_set(){
		if (IS_POST) {
			$forget = $this->request->post('forget'); //由?tpl=forget跳转过来
			$password = $this->request->post('password');
			if (!strlen($password)) error('提现密码不能为空');
			if (strlen($forget)) {
				$mobile = $this->request->post('mobile');
				$code = $this->request->post('code');
				$session_code = $this->request->session('forget_sms_code');
				if (!strlen($mobile) || !strlen($code)) error('请输入手机号、验证码');
				if (!strlen($session_code) || $code!=$session_code) error('验证码不正确');
				if (!SQL::share('member')->where("id='{$this->member_id}' AND mobile='{$mobile}'")->count()) error('该会员不存在');
				$_SESSION['forget_sms_code'] = '';
				$_SESSION['forget_sms_mobile'] = '';
			}
			$salt = generate_salt();
			$crypt_password = crypt_password($password, $salt);
			SQL::share('member')->where($this->member_id)->update(array('withdraw_password'=>$crypt_password, 'withdraw_salt'=>$salt));
			$_SESSION['member']->withdraw_password = $crypt_password;
			success($_SESSION['member']);
		}
		success('ok');
    }
	
	public function repassword(){
		success('ok');
	}
	
	public function password_edit() {
		if (IS_POST) {
			$password = $this->request->post('password');
			if ($password=='') error('提现密码不能为空');
			$row = SQL::share('member')->where($this->member_id)->row('withdraw_password, withdraw_salt');
			$crypt_password = crypt_password($password, $row->withdraw_salt);
			if ($row->withdraw_password!=$crypt_password) error('提现密码不正确');
		}
		success('ok');
	}
}
