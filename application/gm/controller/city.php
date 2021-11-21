<?php
class city extends core {

	public function __construct() {
		parent::__construct();
	}

	//列表
	public function index() {
		$where = '';
		$keyword = $this->request->get('keyword');
		$city_id = $this->request->get('city_id');
		if ($keyword) {
			$where .= "AND (name LIKE '%{$keyword}%')";
		}
		if ($city_id) {
			$where .= "AND city_id='{$city_id}'";
		}
		$rs = SQL::share('open_city')->where($where)->isezr()->setpages(compact('keyword', 'city_id'))->sort('id DESC')->pagesize(20)->find();
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
			$city_id = $this->request->post('city_id', 0);
			$province_id = $this->request->post('province_id', 0);
			$name = $this->request->post('name');
			$pinyin = strtoupper($this->request->post('pinyin'));
			$first_letter = strtoupper($this->request->post('first_letter'));
			$is_hot = $this->request->post('is_hot', 0);
			$opening = $this->request->post('opening', 0);
			$sort = $this->request->post('sort', 999);
			$data = compact('name', 'pinyin', 'is_hot', 'opening', 'sort', 'first_letter', 'city_id', 'province_id');
			if ($id>0) {
				SQL::share('open_city')->where($id)->update($data);
			} else {
				if (SQL::share('open_city')->where("name='{$name}'")->count()) error('该城市已经存在');
				$id = SQL::share('open_city')->insert($data);
			}
			header("Location:?app=city&act=index");
		} else if ($id>0) {
			$row = SQL::share('open_city')->where($id)->row();
		} else {
			$row = t('open_city');
		}
		$this->smarty->assign('row', $row);
		$this->display('city.edit.html');
	}

	//删除城市
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('open_city')->delete($id);
		header("Location:?app=city&act=index");
	}

	//根据名称查找城市、区域编号
	public function get_city_code() {
		$where = '';
		$city = $this->request->get('city');
		if ($city) {
			$where .= "AND (name LIKE '%{$city}%')";
		}
		$rs = SQL::share('city')->where($where)->isezr()->setpages(compact('city'))->sort('id DESC')->pagesize(20)->find();
		$sharepage = SQL::share()->page;
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}

	//根据名称查找城市、区域编号
	public function get_district_code() {
		$where = '';
		$district = $this->request->get('district');
		if ($district) {
			$where .= "AND (d.name LIKE '%{$district}%')";
		}
		$rs = SQL::share('district d')->left('city c', 'd.father_id=c.city_id')->where($where)->isezr()->setpages(compact('district'))->sort('d.id DESC')->pagesize(20)
			->find('d.*, c.name as city_name');
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
}
