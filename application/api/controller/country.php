<?php
class country extends core {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 6);
		$rs = SQL::share('country')->where("status='1'")->sort('sort ASC, id DESC')->limit($offset, $pagesize)->find();
		if ($rs) {
			$rs = add_domain_deep($rs, array("detail_pic", "list_pic_big", "list_pic_small", "flag_pic"));
		}
		success($rs);
	}
}
