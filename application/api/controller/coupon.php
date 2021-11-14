<?php
class coupon extends core {
	private $coupon_mod;
	
	public function __construct() {
		parent::__construct();
		$this->coupon_mod = m('coupon');
	}

	//我的优惠券列表
	//-2过期，-1已使用，0无效，1正常
	public function index() {
		$this->_set_expired();
		$where = " cs.member_id='{$this->member_id}' AND cs.member_id>0";
		$status = $this->request->get('status');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		if (strlen($status)) {
			//失效：包括过期和已使用的
			$status = intval($status);
			if ($status==0) {
				$where .= " AND cs.status<=0";
			} else {
				$where .= " AND cs.status='{$status}'";
			}
		}
		$rs = SQL::share('coupon_sn cs')->left('coupon c', 'cs.coupon_id=c.id')->where($where)->sort('cs.id DESC')->limit($offset, $pagesize)
			->find("c.*, cs.sn, cs.id as coupon_sn_id, cs.status as coupon_sn_status");
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$rs[$k] = $this->coupon_mod->get_coupon_info($g);
				$rs[$k]->id = $g->coupon_sn_id;
				$rs[$k]->status = $g->coupon_sn_status;
			}
		}
		success($rs);
	}

	//优惠券列表(不是我的优惠券)
	public function coupon_list() {
		$type = $this->request->get('type', 0);
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$rs = SQL::share('coupon')->where("type='{$type}'")->sort('id DESC')->limit($offset, $pagesize)->find("*, '' as time_memo, '' as min_price_memo");
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$rs[$k] = $this->coupon_mod->get_coupon_info($g);
				$rs[$k]->quantity = SQL::share('coupon_sn')->where("coupon_id='{$g->id}'")->count();
				$rs[$k]->persons = SQL::share('coupon_sn')->where("coupon_id='{$g->id}' AND member_id>0")->count();
			}
		}
	    success($rs);
	}

	public function course(){
	    success('ok');
    }

	//优惠券详情，可用在html5页面
	public function detail() {
		$id = $this->request->get('id', 0);
		$coupon = SQL::share('coupon')->where($id)->row("*, '' as goods");
		if ($coupon) {
			if ($coupon->permit_goods==1) {
				$goods_ids = $this->coupon_mod->get_coupon_goods($id);
				if ($goods_ids) {
					$goods_ids = implode(",", $goods_ids);
					$goods = SQL::share('goods')->where("id IN ($goods_ids)")->find('id, name');
					$coupon->goods = $goods;
				}
			}
		}
		success($coupon);
	}


	//领取优惠券
	public function ling() {
		if (!$this->member_id) error('请登录', -100);
		$coupon_id = $this->request->get('id', 0);
		if ($coupon_id<=0) error('数据错误');
		$result = $this->coupon_mod->send($coupon_id, $this->member_id);
		switch ($result) {
			case 1:success('ok', '恭喜，抢到了');break;
			case 0:error('领取优惠券失败');break;
			case -1:error('你已经领取了该优惠券');break;
			case -2:error('该优惠券不存在');break;
		}
		error('领取优惠券失败');
	}

	//领取优惠券，先判断优惠券是否有效
	private function _ling($coupon_id) {
		$id = SQL::share('coupon_sn cs')->inner('coupon c', 'cs.coupon_id=c.id')
			->where("coupon_id='{$coupon_id}' AND cs.status='1' AND c.status='1' AND cs.member_id='0'")->value('MIN(cs.id)');
		$coupon_sn_id = intval($id);
		if ($coupon_sn_id) {
			return SQL::share('coupon_sn')->where($coupon_sn_id)->update(array('member_id'=>$this->member_id, 'get_time'=>time()));
		}
		return false;
	}


	//手工添加优惠券
	public function add() {
		$sn = $this->request->post('sn');
		if ($sn) {
			$id = intval(SQL::share('coupon_sn')->where("sn='{$sn}' AND member_id='0'")->value('id'));
			if ($id) {
				SQL::share('coupon_sn')->where($id)->update(array('member_id'=>$this->member_id, 'get_time'=>time()));
				success('ok');
			} else {
				error("优惠券码不正确");
			}
		}
	}

	//设置过期的优惠券
	private function _set_expired() {
		$now = time();
		$coupons = SQL::share('coupon_sn cs')->left('coupon c', 'cs.coupon_id=c.id')
			->where("cs.member_id='{$this->member_id}' AND c.end_time<'{$now}' AND c.end_time>'0' AND cs.status=1")
			->find('cs.id, c.name, c.begin_time, c.end_time');
		if ($coupons) {
			$ids = '';
			foreach ($coupons as $key => $coupon) {
				if ($ids) {
					$ids .= ", {$coupon->id}";
				} else {
					$ids = "{$coupon->id}";
				}
			}
			if ($ids) {
				SQL::share('coupon_sn')->where("member_id='{$this->member_id}' AND id IN ($ids)")->update(array('status'=>-2));
			}
		}
	}
}