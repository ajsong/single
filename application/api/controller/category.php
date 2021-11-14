<?php
class category extends core {
	public function __construct() {
		parent::__construct();
	}

	//分类首页
	public function index(){
		$category = $this->get_categories();
		$category = add_domain_deep($category, array('pic'));
		success(array('category'=>$category));
	}
	
	private function get_categories($parent_id=0) {
		$category = SQL::share('goods_category')->where("status='1' AND parent_id='{$parent_id}'")->sort('sort ASC, id ASC')->find('*, NULL as categories');
		if ($category) {
			foreach ($category as $k => $g) {
				$category[$k]->flashes = $this->_flashes($g->id);
				if (SQL::share('goods_category')->where("parent_id='{$g->id}'")->count()) $category[$k]->categories = $this->get_categories($g->id);
			}
		}
		return $category;
	}

	//幻灯广告
	private function _flashes($id) {
		$ads = SQL::share('ad')->where("(begin_time=0 OR begin_time<='{$this->now}') AND (end_time=0 OR end_time>='{$this->now}')
			AND status='1' AND position='category{$id}'")->sort('sort ASC, id DESC')->pagesize(5)->find();
		if ($ads) {
			$ads = add_domain_deep($ads, array("pic"));
		}
		return $ads;
	}
}
