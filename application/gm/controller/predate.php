<?php
class predate extends core {
	
	public function __construct() {
		parent::__construct();
	}

	//index
	public function index() {
		$where = '';
		$id = $this->request->get('id', 0);
		$keyword = $this->request->get('keyword');
		if ($id) {
			$where .= " AND id='{$id}'";
		}
		if ($keyword) {
			$where .= " AND (name LIKE '%{$keyword}%' OR mobile LIKE '%{$keyword}%')";
		}
		$rs = SQL::share('predate')->where($where)->isezr()->setpages(compact('id', 'keyword'))->sort('id DESC')->find();
		$sharepage = SQL::share()->page;
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}

	public function add() {
		$this->edit();
	}
	public function edit() {
		$id = $this->request->get('id', 0);
		if (IS_POST) { //添加
			$id = $this->request->post('id', 0);
			$remark = $this->request->post('remark');
			if ($id>0) {
				SQL::share('predate')->where($id)->update(array('remark'=>$remark));
			} else {
			
			}
			location("Location:?app=predate&act=index");
		} else if ($id>0) { //显示
			$row = SQL::share('predate')->where($id)->row();
		} else {
			$row = t('predate');
		}
		$this->smarty->assign('row', $row);
		$this->display('predate.edit.html');
	}

	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('predate')->delete($id);
		header("Location:?app=predate&act=index");
	}
}
