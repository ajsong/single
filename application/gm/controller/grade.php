<?php
class grade extends core {
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
			$where .= " AND (name LIKE '%{$keyword}%' OR memo LIKE '%{$keyword}%')";
		}
		$rs = SQL::share('grade')->where($where)->isezr()->setpages(compact('id', 'keyword'))->sort('sort ASC, id ASC')->find();
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
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$name = $this->request->post('name');
			$memo = $this->request->post('memo');
			$score = $this->request->post('score', 0);
			$price = $this->request->post('price', 0, 'float');
			$status = $this->request->post('status', 0);
			$sort = $this->request->post('sort', 0);
			$pic = $this->request->file('grade', 'pic', UPLOAD_LOCAL);
			$data = compact('name', 'memo', 'score', 'price', 'pic', 'status', 'sort');
			if ($id > 0) {
				SQL::share('grade')->where($id)->update($data);
			} else {
				$id = SQL::share('grade')->insert($data);
			}
			location("?app=grade&act=index");
		} else if ($id>0) {
			$row = SQL::share('grade')->where($id)->row();
		} else {
			$row = t('grade');
		}
		$this->smarty->assign('row', $row);
		$this->display('grade.edit.html');
	}

	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('grade')->delete($id);
		header("Location:?app=grade&act=index");
	}
}
