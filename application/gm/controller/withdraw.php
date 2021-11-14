<?php
class withdraw extends core {
	private $withdraw_mod;

	public function __construct() {
		parent::__construct();
		$this->withdraw_mod = m('withdraw');
	}

	//列表
	public function index() {
		$where = '';
		$id = $this->request->get('id');
		$keyword = $this->request->get('keyword');
		$status = $this->request->get('status');
		$begin_date = $this->request->get('begin_date');
		$end_date = $this->request->get('end_date');
		if (strlen($id)) {
			$where .= "AND w.id='{$id}'";
		}
		if ($keyword) {
			$where .= "AND (m.name LIKE '%{$keyword}%' OR w.bank_name LIKE '%{$keyword}%' OR w.bank_branch LIKE '%{$keyword}%'
				OR w.bank_card LIKE '%{$keyword}%' OR w.alipay LIKE '%{$keyword}%')";
		}
		if (strlen($begin_date)) {
			$where .= " AND w.add_time>='".strtotime($begin_date)."'";
		}
		if (strlen($end_date)) {
			$where .= " AND w.add_time<='".strtotime($end_date)."'";
		}
		if (strlen($status)) {
			$where .= "AND w.status='{$status}'";
		}
		$rs = SQL::share('withdraw w')->left('member m', 'w.member_id=m.id')->where($where)->isezr()
			->setpages(compact('id', 'keyword', 'status', 'begin_date', 'end_date'))->sort('w.id DESC')->find('w.*, m.name as member_name, m.mobile');
		$sharepage = SQL::share()->page;
		$wherebase64 = SQL::share()->wherebase64;
		if ($rs) {
			foreach ($rs as $key => $row) {
				$rs[$key]->status_name = $this->withdraw_mod->status_name($row->status);
			}
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->smarty->assign('where', $wherebase64);
		$this->display();
	}

	//编辑
	public function edit() {
		global $push_type;
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$status = $this->request->post('status', 0);
			$audit_memo = $this->request->post('audit_memo');
			$withdraw_money = $this->request->post('withdraw_money');
			$audit_adminid = $_SESSION['admin']->id;
			$audit_admin = $_SESSION['admin']->name;
			
			SQL::share('withdraw')->where($id)->update(array('status'=>$status, 'audit_memo'=>$audit_memo, 'audit_time'=>time(), 'audit_adminid'=>$audit_adminid,
				'audit_admin'=>$audit_admin));
			
			$type = SQL::share('withdraw')->where($id)->value('type');
			if (!strlen($type)) error('数据错误');
			//判断表是否存在字段
			$row = SQL::share()->query("DESCRIBE {$this->tbp}member {$type}");
			if (!$row || (stripos($row->Type,'decimal(')===false && stripos($row->Type,'int(')===false)) error('数据错误');
			
			//更新可提现的金额，以及发送通知消息
			$row = SQL::share('withdraw w')->left('member m', 'w.member_id=m.id')->where("w.id='{$id}'")->row("{$type}, withdraw_money, udid, member_id");
			if ($row) {
				switch ($status) {
					case 1:
						if ($row->$type >= $row->withdraw_money) {
							$remain_income = $row->$type - $row->withdraw_money;
							SQL::share('member')->where($row->member_id)->update(array("{$type}"=>$remain_income));
							if ($row->udid!='' && $push_type!='nopush') {
								$push = p('push', $push_type);
								$push->send($row->udid, '恭喜您，您的提现申请已经审核并发放。');
							}
						}
						break;
					case -1:
						$remain_income = $row->$type + $row->withdraw_money;
						SQL::share('member')->where($row->member_id)->update(array("{$type}"=>$remain_income));
						if ($row->udid!='' && $push_type!='nopush') {
							$push = p('push', $push_type);
							$push->send($row->udid, "您的提现申请被拒绝：{$audit_memo}。");
						}
						break;
				}
			}
			header("Location:?app=withdraw&index");
		} else {
			$id = $this->request->get('id', 0);
			$row = SQL::share('withdraw w')->left('member m', 'w.member_id=m.id')->where("w.id='{$id}'")->row('w.*, m.name as member_name');
			if ($row) {
				$row->status_name = $this->withdraw_mod->status_name($row->status);
			}
			$this->smarty->assign('row', $row);
			$this->display();
		}
	}

	//删除
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('withdraw')->delete($id);
		header("Location:?app=withdraw&act=index");
	}

	//导出
	public function export() {
		set_time_limit(0);
		$fields = array(
			'id'=>'ID',
			'member_name'=>'会员',
			'bank_name'=>'银行名称',
			'bank_branch'=>'支行名称',
			'bank_card'=>'银行卡号',
			'alipay'=>'支付宝',
			'withdraw_money'=>'提现金额',
			'add_time'=>'申请日期',
			'status'=>'状态',
			'audit_time'=>'审核日期',
			'audit_memo'=>'审核备注'
		);
		$where = base64_decode($this->request->get('where'));
		$rs = SQL::share('withdraw w')->left('member m', 'w.member_id=m.id')->where($where)->sort('w.id DESC')->find("w.*, m.name as member_name");
		if ($rs) {
			foreach ($rs as $key => $row) {
				$rs[$key]->status = $this->withdraw_mod->status_name($row->status);
				if ($row->add_time) $rs[$key]->add_time = date('Y-m-d H:i:s', $row->add_time);
				if ($row->audit_time) $rs[$key]->audit_time = date('Y-m-d H:i:s', $row->audit_time);
			}
			export_excel($rs, $fields);
		}
	}
}
