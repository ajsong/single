<?php
class feedback extends core {
	public function __construct() {
		parent::__construct();
	}

	//index
	public function index() {
		$where = '';
		$member_id = $this->request->get('member_id');
		$keyword = $this->request->get('keyword');
		if ($keyword) {
			$where .= " AND (name LIKE '%{$keyword}%' OR content LIKE '%{$keyword}%')";
		}
		if (strlen($member_id)) {
			$where .= " AND member_id='{$member_id}'";
		}
		$rs = SQL::share('feedback f')->left('member m', 'f.member_id=m.id')->where($where)->isezr()->setpages(compact('member_id', 'keyword'))
			->sort('f.add_time DESC')->find('f.*, m.name as member_name');
		$sharepage = SQL::share()->page;
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
	
	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('feedback')->delete($id);
		header("Location:?app=feedback&act=index");
	}
}
