<?php
class brand extends core {
	public function __construct() {
		parent::__construct();
	}

	//index
	public function index() {
		$where = '';
		$brand_id = $this->request->get('brand_id', 0);
		$keyword = $this->request->get('keyword');
		if ($keyword) {
			$where .= "AND (name LIKE '%{$keyword}%')";
		}
		if ($brand_id) {
			$where .= "AND b.id='{$brand_id}'";
		}
		$rs = SQL::share('brand')->where($where)->isezr()->setpages(compact('brand_id', 'keyword'))->sort('sort ASC')->find();
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('page',SQL::share()->page);
		$this->display();
	}

	public function add() {
		$this->edit();
	}

	//edit
	public function edit() {
		$id = $this->request->get('id', 0);
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$name = $this->request->post('name');
			$memo = $this->request->post('memo');
			$keywords = $this->request->post('keywords');
			$sort = $this->request->post('sort', 999);
			$status = $this->request->post('status', 0);
			$pic = $this->request->file('brand', 'pic', UPLOAD_LOCAL);
			$banner = $this->request->file('brand', 'banner', UPLOAD_LOCAL);
			$data = compact('name', 'memo', 'keywords', 'sort', 'status', 'pic', 'banner');
			if ($id > 0) {
				SQL::share('brand')->where($id)->update($data);
			} else {
				$id = SQL::share('brand')->insert($data);
			}
			location("?app=brand");
		} else if ($id>0) {
			$row = SQL::share('brand')->where($id)->row();
		} else {
			$row = t('brand');
		}
		$this->smarty->assign('row', $row);
		$this->display('brand.edit.html');
	}

	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('brand')->delete($id);
		header("Location:?app=brand");
	}
}
