<?php
class ad extends core {
	private $ad_mod;
	
	public function __construct() {
		parent::__construct();
		$this->ad_mod = m('ad');
	}

	//列表
	public function index() {
		$where = '';
		$status = $this->request->get('status');
		$keyword = $this->request->get('keyword');
		$ad_id = $this->request->get('ad_id');
		$begin_time = $this->request->get('begin_time');
		$end_time = $this->request->get('end_time');
		$ad_type = $this->request->get('ad_type');
		$position = $this->request->get('position');

		if ($keyword) {
			$where .= "AND (name LIKE '%{$keyword}%' OR ad_content LIKE '%{$keyword}%')";
		}
		if (strlen($ad_id)) {
			$where .= "AND id='{$ad_id}'";
		}
		if (strlen($begin_time)) {
			$where .= " AND add_time>='".strtotime($begin_time)."'";
		}
		if (strlen($end_time)) {
			$where .= " AND add_time<='".strtotime($end_time)."'";
		}
		if ($ad_type) {
			$where .= "AND ad_type='{$ad_type}'";
		}
		if ($position) {
			$where .= "AND position='{$position}'";
		}
		$rs = SQL::share('ad')->where($where)->isezr()->setpages(compact('status', 'keyword', 'ad_id', 'begin_time', 'end_time', 'ad_type', 'position'))
			->sort('id DESC')->find();
		$sharepage = SQL::share()->page;
		if ($rs) {
			foreach ($rs as $key => $row) {
				$rs[$key]->ad_type = $this->ad_mod->type_name($row->ad_type);
				$rs[$key]->position = $this->ad_mod->position_name($row->position);
			}
		}
		$rs = add_domain_deep($rs, array('pic'));
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->smarty->assign('types', $this->ad_mod->types());
		$this->smarty->assign('positions', $this->ad_mod->positions(true));

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
			$ad_content = $this->request->post('ad_content');
			$begin_time = $this->request->post('begin_time');
			$end_time = $this->request->post('end_time');
			$ad_type = $this->request->post('ad_type');
			$sort = $this->request->post('sort', 999);
			$position = $this->request->post('position', 'index');
			$status = $this->request->post('status', 0);
			$channel = $this->request->post('channel', 0);
			$shop_id = $this->request->post('shop_id', 0);
			$offer = $this->request->post('offer', 0); //发送站内消息
			$pic = $this->request->file('ad', 'pic', UPLOAD_LOCAL);
			if ($begin_time) $begin_time = strtotime($begin_time);
			if ($end_time) $end_time = strtotime($end_time);
			if (!$begin_time) $begin_time = 0;
			if (!$end_time) $end_time = 0;
			$data = compact('name', 'ad_content', 'ad_type', 'pic', 'sort', 'position', 'begin_time', 'end_time', 'status', 'channel', 'shop_id');
			if ($id) {
				SQL::share('ad')->where($id)->update($data);
			} else {
				$id = SQL::share('ad')->insert($data);
			}
			if ($offer) $this->send_message($name, 0, 'offer');
			location("?app=ad&act=index");
		} else if ($id>0) {
			$row = SQL::share('ad')->where($id)->row();
			$row->begin_time = date("Y-m-d H:i", $row->begin_time);
			$row->end_time = date("Y-m-d H:i", $row->end_time);
			$row = add_domain_deep($row, array('pic'));
		} else {
			$row = t('ad');
		}
		$this->smarty->assign('row', $row);
		$shop = SQL::share('shop')->sort('id ASC')->find('id, name');
		$this->smarty->assign('shop', $shop);
		$this->smarty->assign('types', $this->ad_mod->types());
		$this->smarty->assign('positions', $this->ad_mod->positions(true));
		$this->display('ad.edit.html');
	}

	//删除
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('ad')->delete($id);
		header("Location:?app=ad&act=index");
	}
}
