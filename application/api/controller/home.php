<?php
class home extends core {
	public $goods_mod;

	public function __construct() {
		parent::__construct();
		$this->goods_mod = m('goods');
	}

	//首页接口
	public function index() {
		/*//
		SQL::share(':demo')->create(array(
			'demo' => array(
				'id' => array('type'=>'key'),
				'name' => array('type'=>'varchar(255)', 'comment'=>'名称'),
				'price' => array('type'=>'decimal(10,2)', 'comment'=>'价格'),
				'content' => array('type'=>'text', 'comment'=>'详细内容'),
				'add_time' => array('type'=>'int', 'comment'=>'添加时间')
			)
		));
		$res = t(':demo');
		echo json_encode($res);exit;
		//*/
		$flashes = $this->_flashes();
		$categories = $this->_categories();
		$coupons = $this->_coupons();
		$recommend = $this->_goods(1); //推荐
		$hotsale = $this->_goods(2); //热销
		$boutique = $this->_goods(3); //精品
		$newgoods = $this->_goods(4); //新品
		$discount = $this->_goods(5); //折扣
		success(compact('flashes', 'categories', 'coupons', 'recommend', 'hotsale', 'boutique', 'newgoods', 'newgoods', 'discount'));
	}

	//幻灯广告
	private function _flashes() {
		$rs = SQL::share('ad')->where("(begin_time=0 OR begin_time<='{$this->now}') AND (end_time=0 OR end_time>='{$this->now}')
			AND status='1' AND position='flash'")->sort('sort ASC, id DESC')->pagesize(5)->cached(60*2)->find();
		$rs = add_domain_deep($rs, array('pic'));
		return $rs;
	}
	
	//商品分类
	private function _categories() {
		$rs = SQL::share('goods_category')->where("status='1' AND parent_id=0")->sort('sort ASC, id ASC')->cached(60*2)->find('id, name, pic');
		$rs = add_domain_deep($rs, array('pic'));
		return $rs;
	}
	
	//优惠券
	private function _coupons() {
		$rs = SQL::share('coupon')->where("status='1'")->sort('id DESC')->pagesize(10)->find();
		if ($rs) {
			$coupon_mod = m('coupon');
			foreach ($rs as $k=>$g) {
				$rs[$k] = $coupon_mod->get_coupon_info($g);
			}
		}
		return $rs;
	}
	
	//商品
	public function _goods($ext_property, $not_in='') {
		$offset = 0;
		$pagesize = 6;
		if ($ext_property==1) {
			$offset = $this->request->get('offset', 0);
			$pagesize = $this->request->get('pagesize', 12);
		}
		$where = '';
		if (strlen($not_in)) $where = " AND g.id NOT IN ({$not_in})";
		$rs = SQL::share('goods g')->where("g.status=1 AND LOCATE(',{$ext_property},', CONCAT(',',ext_property,','))>0{$where}")
			->sort('g.sort ASC, g.id DESC')->limit($offset, $pagesize)->cached(60*2)->find('g.*, 0 as grade_price');
		if ($rs) {
			foreach ($rs as $k=>$g) {
				unset($rs[$k]->content);
				if (in_array('grade', $this->function)) {
					if ($this->member_id) {
						$rs[$k]->grade_price = floatval(SQL::share('goods_grade_price')->where("goods_id='{$g->id}' AND grade_id='{$this->member_grade_id}'")->value('price'));
					} else {
						$grade_id = intval(SQL::share('goods_grade_price')->where("goods_id='{$g->id}'")->value('MIN(grade_id)'));
						$rs[$k]->grade_price = floatval(SQL::share('goods_grade_price')->where("goods_id='{$g->id}' AND grade_id='{$grade_id}'")->value('price'));
					}
				}
			}
		}
		$rs = $this->goods_mod->set_min_prices($rs);
		$rs = add_domain_deep($rs, array('pic'));
		return $rs;
	}

	//搜索历史
	public function search_history() {
		$history = $hot = array();
		if ($this->member_id) {
			$rs = SQL::share('seach_history')->where("member_id='{$this->member_id}'")->sort('id DESC')->pagesize(20)->find();
			if ($rs) {
				foreach ($rs as $k=>$g) {
					$history[] = $g->content;
				}
			}
		}
		if (isset($this->configs['G_HOT_SEARCH']) && strlen(trim($this->configs['G_HOT_SEARCH']))) {
			$arr = explode(',', $this->configs['G_HOT_SEARCH']);
			foreach ($arr as $g) {
				$hot[] = trim($g);
			}
		}
		success(array('history'=>$history, 'hot'=>$hot));
	}
	
	//清空搜索历史
	public function clear_search_history() {
		SQL::share('seach_history')->delete("member_id='{$this->member_id}'");
		success('ok');
	}

	//删除搜索历史
	public function delete_search_history() {
		$content = $this->request->get('content');
		SQL::share('seach_history')->delete("member_id='{$this->member_id}' AND content='{$content}'");
		success('ok');
	}
}
