<?php
class brand extends core {
	public function __construct() {
		parent::__construct();

	}

	//品牌首页
	public function index(){
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$rs = SQL::share('brand')->where("status='1'")->sort('sort ASC, id DESC')->limit($offset, $pagesize)->find();
		if ($rs) {
			foreach ($rs as $k => $c) {

			}
			$rs = add_domain_deep($rs, array('pic', 'banner'));
		}
		$flashes = $this->_flashes();
		success(array("flashes"=>$flashes, "brands"=>$rs));
	}

	//幻灯广告
	private function _flashes() {
		$ads = SQL::share('ad')->where("(begin_time=0 OR begin_time<='{$this->now}') AND (end_time=0 OR end_time>='{$this->now}')
			AND status='1' AND position='brand'")->sort('sort ASC, id DESC')->pagesize(5)->find();
		if ($ads) {
			$ads = add_domain_deep($ads, array('pic'));
		}
		return $ads;
	}
}
