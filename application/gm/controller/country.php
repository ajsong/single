<?php
class country extends core {
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
			$where .= " AND (name LIKE '%{$keyword}%' OR name_english LIKE '%{$keyword}%' OR memo LIKE '%{$keyword}%')";
		}
		$rs = SQL::share('country')->where($where)->isezr()->setpages(compact('id', 'keyword'))->sort('id DESC')->find();
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
			$name_english = $this->request->post('name_english');
			$memo = $this->request->post('memo');
			$status = $this->request->post('status', 0);
			$sort = $this->request->post('sort', 0);
			$detail_pic = $this->request->file('country', 'pic', UPLOAD_LOCAL);
			$list_pic_big = $this->request->file('country', 'pic_big', UPLOAD_LOCAL);
			$list_pic_small = $this->request->file('country', 'pic_small', UPLOAD_LOCAL);
			$flag_pic = $this->request->file('country', 'flag_pic', UPLOAD_LOCAL);
			$data = compact('name', 'name_english', 'sort', 'status', 'memo', 'detail_pic', 'list_pic_big', 'list_pic_small', 'flag_pic');
			if ($id > 0) {  //edit
				SQL::share('country')->where($id)->update($data);
			} else { //add
				SQL::share('country')->insert($data);
			}
			header("Location:?app=country&act=index");
		} else if ($id>0) {
			$row = SQL::share('country')->where($id)->row();
		} else {
			$row = t('country');
		}
		$this->smarty->assign('row', $row);
		$this->display('country.edit.html');
	}

	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		if (SQL::share('country')->delete($id)) { //删除该商品的收藏
			SQL::share('favorite')->delete("item_id='{$id}' AND type_id=3");
		}
		header("Location:?app=country&act=index");
	}
}
