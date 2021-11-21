<?php
class commission extends core {
	private $member_mod;

	public function __construct() {
		parent::__construct();
		$this->member_mod = m('member');
	}

	//列表
	public function index() {
		$status = $this->request->get('status');
		$keyword = $this->request->get('keyword');
		$where = "c.commission>0";
		if ($keyword) {
			$where .= " AND (m.name LIKE '%{$keyword}%' OR m.mobile LIKE '%{$keyword}%' OR c.memo LIKE '%{$keyword}%')";
		}
		if (strlen($status)) {
			$where .= " AND c.status='{$status}'";
		}
		$rs = SQL::share('member_commission c')->left('member m', 'c.member_id=m.id')->where($where)->isezr()
			->setpages(compact('status', 'keyword'))->sort('c.id DESC')
			->find('c.*, m.name, m.mobile, m.invite_code');
		$sharepage = SQL::share()->page;
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$rs[$k]->name = $this->member_mod->get_name($g->member_id);
			}
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
}
