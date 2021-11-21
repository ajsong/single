<?php
#//收货地址
class address extends core {
	public $addr_mod;
	public function __construct() {
		parent::__construct();
		$this->addr_mod = m('address');
	}
	
	#//获取默认地址|已经登录的情况下才返回
	public function default_address() {
		$address = $this->addr_mod->default_address();
		success($address);
	}
	
	#//设置默认地址
	public function set_default() {
		$id = $this->request->post('id', 0); #//设置id
		if ($id<=0) error('数据错误');
		SQL::share('address')->where("member_id='{$this->member_id}'")->update(array('is_default'=>0));
		SQL::share('address')->where("member_id='{$this->member_id}' AND id='{$id}'")->update(array('is_default'=>1));
		success('ok');
	}
	
	#//地址列表
	public function index() {
		$offset = $this->request->get('offset', 0); #//
		$pagesize = $this->request->get('pagesize', 8); #//
		$addresses = SQL::share('address')->where("member_id='{$this->member_id}'")->sort('id ASC')->limit($offset, $pagesize)->find();
		success($addresses);
	}
	
	public function add() {
		$this->edit();
	}
	#//修改收货地址
	public function edit() {
		$id = $this->request->get('id', 0);
		if (IS_POST) {
			$id = $this->request->post('id', 0); #//修改id
			$contactman = $this->request->post('contactman'); #//联系人
			$province = $this->request->post('province'); #//省份
			$city = $this->request->post('city'); #//城市
			$district = $this->request->post('district'); #//区县
			$address = $this->request->post('address'); #//详细地址||textarea
			$mobile = $this->request->post('mobile'); #//联系电话
			$zipcode = $this->request->post('zipcode'); #//邮编
			$idcard = $this->request->post('idcard'); #//身份证
			$is_default = $this->request->post('is_default', 0); #//是否默认|1为默认
			if ($is_default>0) {
				SQL::share('address')->where("member_id='{$this->member_id}'")->update(array('is_default'=>0));
			}
			$member_id = $this->member_id;
			$data = compact('member_id', 'contactman', 'mobile', 'zipcode', 'idcard', 'province', 'city', 'district', 'address', 'is_default');
			if ($id>0) {
				SQL::share('address')->where("id='{$id}' AND member_id='{$member_id}'")->update($data);
			} else {
				$id = SQL::share('address')->insert($data);
			}
			success($id);
		} else if ($id>0) {
			$address = SQL::share('address')->where("member_id='{$this->member_id}' AND id='{$id}'")->row();
		} else {
			$address = t('address');
		}
		success($address, 'address.edit.html');
	}
	
	#//删除地址
	public function delete() {
		$id = $this->request->post('id', 0); #//删除id
		SQL::share('address')->delete("member_id='{$this->member_id}' AND id='{$id}'");
		success('ok');
	}
}
