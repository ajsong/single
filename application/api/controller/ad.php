<?php
class ad extends core {

	public function __construct() {
		parent::__construct();
	}

	//读取广告
	public function index() {
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$ads = SQL::share('ad')->where("(begin_time=0 OR begin_time<='{$this->now}') AND (end_time=0 OR end_time>='{$this->now}')
			AND status='1'")->sort('sort ASC, id DESC')->limit($offset, $pagesize)->find();
		if ($ads) {
			foreach ($ads as $k => $ad) {
				//
			}
			$ads = add_domain_deep($ads, array('pic'));
		}
		success($ads);
	}

}
