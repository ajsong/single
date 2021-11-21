<?php
class message extends core {

	public function __construct() {
		parent::__construct();
	}

	//消息
	public function index() {
		$type = $this->request->get('type');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);

		$where = '';
		if (strlen($type)) {
			$where .= "member_id='{$this->member_id}' AND type LIKE '%{$type}%'";
		} else {
			$where .= "member_id='{$this->member_id}'";
		}
		$rs = SQL::share('message')->where($where)->sort('id DESC')->limit($offset, $pagesize)->find();
		if ($rs) {
			foreach ($rs as $k=>$g) {
				//$rs[$k]->add_time = get_time_word($g->add_time);
				$rs[$k]->add_time = date("Y-m-d H:i:s", $g->add_time);
			}
		}
		success($rs);
	}

	//阅读消息
	public function read() {
		$id = $this->request->post('id', 0);
		SQL::share('message')->where("id='{$id}' AND member_id='{$this->member_id}'")->update(array('readed'=>1));
		success('ok');
	}

	//删除消息
	public function delete() {
		$id = $this->request->post('id', 0);
		SQL::share('message')->delete("id='{$id}' AND member_id='{$this->member_id}'");
		success('ok');
	}

}
