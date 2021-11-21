<?php
class recharge extends core {
	private $recharge_mod;

	public function __construct() {
		parent::__construct();
		$this->recharge_mod = m('recharge');
	}

	//列表
	public function index() {
		$where = '';
		$rs = SQL::share('recharge r')->where($where)->isezr()->sort('r.id DESC')->pagesize(100)->find('r.*');
		$sharepage = SQL::share()->page;
		if ($rs) {
			foreach ($rs as $key => $row) {
				//$rs[$key]->status_name = $this->recharge_mod->status_name($row->status);
			}
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
	
	public function add() {
		$this->edit();
	}
	
	public function edit() {
		$id = $this->request->get('id', 0);
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$name = $this->request->post('name');
			$price = $this->request->post('price', 0, 'float');
			$recharged_money = $this->request->post('recharged_money', 0, 'float');
			$promotion_begin_time = $this->request->post('promotion_begin_time');
			$promotion_end_time = $this->request->post('promotion_end_time');
			$promotion_recharged_money = $this->request->post('promotion_recharged_money', 0, 'float');
			$status = $this->request->post('status', 0);
			$pic = $this->request->file('recharge', 'pic', UPLOAD_LOCAL);
			if ($promotion_begin_time) $promotion_begin_time = strtotime($promotion_begin_time);
			if ($promotion_end_time) $promotion_end_time = strtotime($promotion_end_time);
			if ($recharged_money<$price) $recharged_money = $price;
			if ($promotion_recharged_money<$price) $promotion_recharged_money = $price;
			$data = compact('name', 'pic', 'price', 'recharged_money', 'promotion_begin_time', 'promotion_end_time', 'promotion_recharged_money', 'status');
			if ($id) {
				SQL::share('recharge')->where($id)->update($data);
			} else {
				$id = SQL::share('recharge')->insert($data);
			}
			location("?app=recharge&act=index");
		} else if ($id>0) {
			$row = SQL::share('recharge')->where($id)->row();
		} else {
			$row = t('recharge');
		}
		$this->smarty->assign('row', $row);
		$this->display('recharge.edit.html');
	}
	
	//删除
	public function delete() {
		$recharge_id = $this->request->get('recharge_id', 0);
		SQL::share('recharge')->delete($recharge_id);
		header("Location:?app=recharge&act=index");
	}

	//明细
	public function history() {
		$where = '';
		$status = $this->request->get('status');
		$keyword = $this->request->get('keyword');
		$recharge_id = $this->request->get('recharge_id', 0);
		$begin_date = $this->request->get('begin_date');
		$end_date = $this->request->get('end_date');
		if ($keyword) {
			$where .= " AND (mr.order_sn LIKE '%{$keyword}%' OR mr.pay_method LIKE '%{$keyword}%' OR m.name LIKE '%{$keyword}%')";
		}
		if (strlen($status)) {
			$where .= " AND mr.status='{$status}'";
		}
		if ($recharge_id) {
			$where .= " AND mr.recharge_id='{$recharge_id}'";
		}
		if (strlen($begin_date)) {
			$where .= " AND mr.add_time>='".strtotime($begin_date)."'";
		}
		if (strlen($end_date)) {
			$where .= " AND mr.add_time<='".strtotime($end_date)."'";
		}
		$rs = SQL::share('member_recharge mr')->left('member m', 'mr.member_id=m.id')->where($where)->isezr()
			->setpages(compact('status', 'keyword', 'recharge_id', 'begin_date', 'end_date'))->sort('mr.id DESC')
			->find('mr.*, m.name as member_name, m.mobile');
		$sharepage = SQL::share()->page;
		$wherebase64 = SQL::share()->wherebase64;
		// if ($rs) {
		// 	foreach ($rs as $key => $g) {
		// 		$rs[$key]->status_name = $this->project_mod->status_name($g->status);
		// 	}
		// }var_dump($rs);exit;
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->smarty->assign('where', $wherebase64);
		$recharges = SQL::share('recharge')->sort('price ASC')->find('id, name');
		$this->smarty->assign('recharges', $recharges);
		$this->display();
	}

	//为会员充值
	public function manual_recharge() {
		if (IS_POST) {
			$members = $this->request->post('member_id', '', 'xg');
			$price = $this->request->post('price', 0, 'float');
			$bonus = $this->request->post('bonus', 0, 'float');
			if (!$members) error("请选择要充值的会员");
			$recharged_money = $price + $bonus;
			foreach ($members as $k=>$member_id) {
				if (!strlen($member_id)) continue;
				$order_sn = "CZ".generate_sn();
				SQL::share('member_recharge')->insert(array('member_id'=>$member_id, 'recharge_id'=>0, 'price'=>$price, 'recharged_money'=>$recharged_money,
					'bonus'=>$bonus, 'add_time'=>time(), 'expired_time'=>0, 'status'=>1, 'order_sn'=>$order_sn, 'pay_method'=>'manual', 'pay_time'=>time()));
				SQL::share('member')->where($member_id)->update(array('money'=>array("+{$recharged_money}")));
				//error("找不到该手机对应的会员");
			}
			header("Location:?app=recharge&act=history");
		} else {
			$member_id = $this->request->get('member_id', 0);
			$member = SQL::share('member m')->find('m.id, m.name, m.mobile, m.nick_name');
			$this->smarty->assign('member', $member);
			$this->smarty->assign("member_id", $member_id);
			$this->display();
		}
	}

	//导出
	public function export() {
		$fields = array(
			'id'=>'ID',
			'order_sn'=>'订单号',
			'member_name'=>'顾客姓名',
			'mobile'=>'顾客手机',
			'price'=>'充值金额',
			'recharged_money'=>'充值后金额',
			'bonus'=>'赠送',
			'pay_method'=>'支付方式',
			'add_time'=>'下单时间',
			'pay_time'=>'支付时间',
			'status'=>'状态'
		);
		$where = base64_decode($this->request->get('where'));
		$rs = SQL::share('member_recharge mr')->left('member m', 'mr.member_id=m.id')->where($where)->sort('mr.id DESC')->find('mr.*, m.name as member_name, m.mobile');
		if ($rs) {
			foreach ($rs as $key => $row) {
				if ($row->status==0) {
					$rs[$key]->status = '未支付';
				} else {
					$rs[$key]->status = '已支付';
				}
				if ($row->add_time) $rs[$key]->add_time = date('Y-m-d H:i:s', $row->add_time);
				if ($row->pay_time) $rs[$key]->pay_time = date('Y-m-d H:i:s', $row->pay_time);
			}
			export_excel($rs, $fields);
		}
	}

	private function _members() {
		$members = SQL::share('member')->where("status=1")->sort('name ASC')->find('id, name, mobile');
		return $members;
	}
}
