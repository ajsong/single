<?php
class goods_model extends base_model {

	public function __construct() {
		parent::__construct();
	}

	//获取产品详情
	public function detail($goods_id, $show_origin_pic=false, $status='1') {
		$where = '';
		if (strlen($status)) $where = " AND g.status='{$status}'";
		$row = SQL::share('goods g')->where("g.id='{$goods_id}' {$where}")
			->row("g.*, NULL as pics, NULL as specs, '' as spec, NULL as country, '' as sale_method_name, 0 as favorited,
			0 as groupbuy_show, 0 as groupbuy_now,
			0 as purchase_show, 0 as purchase_now,
			0 as chop_show, 0 as chop_now");
		if ($row) {
			$row = $this->set_min_price($row);
			$row->pics = $this->get_pics($goods_id, $show_origin_pic);
			$row->specs = $this->get_specs($goods_id, true);
			$row->params = $this->get_params($row->params);
			$row->coupons = $this->get_coupons($row->id, $row->shop_id);
			if ($row->specs && count($row->specs)) {
				$specs = array();
				foreach ($row->specs as $k=>$g) $specs[] = $g->name;
				$row->spec = implode('、', $specs);
			}
			//$row->country = $this->get_country($row->country_id);
			//$row->sale_method_name = $this->sale_method_name($row->sale_method);
			$row->favorited = $this->favorited($goods_id, $this->member_id);
			$row = add_domain_deep($row, array('pic'));
		}
		return $row;
	}

	//获取产品详情，不判断是否下架
	public function get_detail($goods_id, $show_origin_pic=false) {
		return $this->detail($goods_id, $show_origin_pic, '');
	}
	
	//设置最低价格
	public function set_min_price($row) {
		if ($row) {
			$row = $this->set_activity($row);
			if ($row->market_price==0) {
				if ($row->price>$row->promote_price || $row->groupbuy_price>0 || $row->purchase_price>0 || $row->chop_price>0) $row->market_price = $row->price;
			}
			$row->origin_price = $this->get_min_price(array($row->price, $row->promote_price));
			$row->price = $this->get_min_price(array($row->price, $row->promote_price, $row->groupbuy_price, $row->purchase_price, $row->chop_price));
		}
		return $row;
	}
	//设置最低价格(列表)
	public function set_min_prices($rs) {
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$rs[$k] = $this->set_min_price($g);
			}
		}
		return $rs;
	}
	private function get_num_of_show($shows=array()) {
		$num = 0;
		foreach ($shows as $show) {
			if ($show==1) $num++;
		}
		return $num;
	}
	
	//设置活动
	public function set_activity($row) {
		if ($row) {
			//拼团
			$row->groupbuy_show = 0;
			$row->groupbuy_now = 0;
			$row->groupbuy_price = $this->get_groupbuy_price($row->id);
			if (floatval($row->groupbuy_price)>0 && isset($row->groupbuy_end_time)) {
				$row->groupbuy_now = time();
				if ($row->groupbuy_begin_time==0 ||
					($row->groupbuy_begin_time>0 && $row->groupbuy_end_time==0) ||
					($row->groupbuy_begin_time>0 && $row->groupbuy_now<$row->groupbuy_end_time)) {
					$row->groupbuy_show = 1;
				} else {
					$row->groupbuy_price = 0;
				}
			}
			//秒杀
			$row->purchase_show = 0;
			$row->purchase_now = 0;
			$row->purchase_price = $this->get_purchase_price($row->id);
			if (floatval($row->purchase_price)>0 && isset($row->purchase_end_time)) {
				$row->purchase_now = time();
				if ($row->purchase_begin_time==0 ||
					($row->purchase_begin_time>0 && $row->purchase_end_time==0) ||
					($row->purchase_begin_time>0 && $row->purchase_now<$row->purchase_end_time)) {
					$row->purchase_show = 1;
				} else {
					$row->purchase_price = 0;
				}
			}
			//砍价
			$row->chop_show = 0;
			$row->chop_now = 0;
			$row->chop_price = $this->get_chop_price($row->id);
			if (floatval($row->chop_price)>0 && isset($row->chop_end_time)) {
				$row->chop_now = time();
				if ($row->chop_begin_time==0 ||
					($row->chop_begin_time>0 && $row->chop_end_time==0) ||
					($row->chop_begin_time>0 && $row->chop_now<$row->chop_end_time)) {
					$row->chop_show = 1;
				} else {
					$row->chop_price = 0;
				}
			}
			if (!in_array('groupbuy', $this->function)) {
				$row->groupbuy_show = 0;
				$row->groupbuy_price = 0;
			}
			if (!in_array('purchase', $this->function)) {
				$row->purchase_show = 0;
				$row->purchase_price = 0;
			}
			if (!in_array('chop', $this->function)) {
				$row->chop_show = 0;
				$row->chop_price = 0;
			}
			//价格最低即默认选用该活动
			$num = $this->get_num_of_show(array($row->groupbuy_show, $row->purchase_show, $row->chop_show));
			if ($num>1) {
				$key = $this->get_min_price_key(array('groupbuy'=>$row->groupbuy_price, 'purchase'=>$row->purchase_price, 'chop'=>$row->chop_price));
				if ($key=='groupbuy') {
					$row->purchase_show = 0;
				} else if ($key=='purchase') {
					$row->groupbuy_show = 0;
				} else if ($key=='chop') {
					$row->chop_show = 0;
				}
			}
			//如果特价比活动价都低就不进行活动
			if (isset($row->promote_price) && $row->promote_price>0 &&
				$row->promote_price<$row->groupbuy_price &&
				$row->promote_price<$row->purchase_price &&
				$row->promote_price<$row->chop_price) {
				$row->groupbuy_show = 0;
				$row->purchase_show = 0;
				$row->chop_show = 0;
			}
		}
		return $row;
	}
	
	//获取最低价格
	public function get_min_price($prices=array()) {
		if (!is_array($prices) || !count($prices)) return 0;
		asort($prices);
		foreach ($prices as $price) {
			if ($price>0) {
				return floatval($price);
				break;
			}
		}
		return floatval($prices[0]);
	}
	
	//获取最低价格的key
	public function get_min_price_key($prices=array()) {
		if (!is_array($prices) || !count($prices)) return '';
		asort($prices);
		foreach ($prices as $key=>$price) {
			if ($price>0) {
				return $key;
				break;
			}
		}
		$keys = array_keys($prices);
		return $keys[0];
	}
	
	//获取拼团价,spec为规格tree
	public function get_groupbuy_price($goods_id, $spec='', $member_id=0) {
		$now = time();
		$row = SQL::share('goods')->where($goods_id)->row('groupbuy_price, groupbuy_begin_time, groupbuy_end_time, groupbuy_amount, groupbuy_count, groupbuy_limit');
		if (!$row) return 0;
		if (strlen($spec)) {
			$spec = SQL::share('goods_spec')->where("goods_id='{$goods_id}' AND spec='{$spec}'")->row('groupbuy_price');
			if ($spec) {
				if ($spec->groupbuy_price>0) $row->groupbuy_price = $spec->groupbuy_price;
			}
		}
		if ($row->groupbuy_price<=0 || $row->groupbuy_amount<=0) return 0;
		if ($row->groupbuy_begin_time>0 && $row->groupbuy_begin_time>$now) return 0;
		if ($row->groupbuy_end_time>0 && $row->groupbuy_end_time<$now) return 0;
		if ($row->groupbuy_count>=$row->groupbuy_amount) return 0;
		if ($member_id<=0) $member_id = $this->member_id;
		if ($row->groupbuy_limit>0 && $member_id>0) {
			$count = SQL::share('order_goods og')->left('order o', 'og.order_id=o.id')
				->where("goods_id='{$goods_id}' AND og.member_id='{$member_id}' AND o.type=1")->count();
			if ($count>=$row->groupbuy_limit) return 0;
		}
		return $row->groupbuy_price;
	}
	
	//获取秒杀价
	public function get_purchase_price($goods_id, $spec='', $member_id=0) {
		$now = time();
		$row = SQL::share('goods')->where($goods_id)->row('purchase_price, purchase_begin_time, purchase_end_time, purchase_amount, purchase_count, purchase_limit');
		if (!$row) return 0;
		if (strlen($spec)) {
			$spec = SQL::share('goods_spec')->where("goods_id='{$goods_id}' AND spec='{$spec}'")->row('purchase_price');
			if ($spec) {
				if ($spec->purchase_price>0) $row->purchase_price = $spec->purchase_price;
			}
		}
		if ($row->purchase_price<=0 || $row->purchase_amount<=0) return 0;
		if ($row->purchase_begin_time>0 && $row->purchase_begin_time>$now) return 0;
		if ($row->purchase_end_time>0 && $row->purchase_end_time<$now) return 0;
		if ($row->purchase_count>=$row->purchase_amount) return 0;
		if ($member_id<=0) $member_id = $this->member_id;
		if ($row->purchase_limit>0 && $member_id>0) {
			$count = SQL::share('order_goods og')->left('order o', 'og.order_id=o.id')
				->where("goods_id='{$goods_id}' AND og.member_id='{$member_id}' AND o.type=2")->count();
			if ($count>=$row->purchase_limit) return 0;
		}
		return $row->purchase_price;
	}
	
	//获取砍价最低价
	public function get_chop_price($goods_id, $spec='', $member_id=0) {
		$now = time();
		$row = SQL::share('goods')->where($goods_id)->row('chop_price, chop_begin_time, chop_end_time, chop_amount, chop_count');
		if (!$row) return 0;
		if (strlen($spec)) {
			$spec = SQL::share('goods_spec')->where("goods_id='{$goods_id}' AND spec='{$spec}'")->row('chop_price');
			if ($spec) {
				if ($spec->chop_price>0) $row->chop_price = $spec->chop_price;
			}
		}
		if ($row->chop_price<=0 || $row->chop_amount<=0) return 0;
		if ($row->chop_begin_time>0 && $row->chop_begin_time>$now) return 0;
		if ($row->chop_end_time>0 && $row->chop_end_time<$now) return 0;
		if ($row->chop_count>=$row->chop_amount) return 0;
		return $row->chop_price;
	}

	//获取商品的产地
	public function get_country($country_id) {
		if ($country_id) {
			$row = SQL::share('country')->where($country_id)->row('id, name, flag_pic');
			return $row;
		}
		return NULL;
	}

	//获取商品的图片
	public function get_pics($goods_id, $show_origin_pic=false) {
		$pics = SQL::share('goods_pic')->where("goods_id='{$goods_id}'")->sort('id ASC')->find();
		$pics = add_domain_deep($pics, array('pic'));
		//为商品增加缩略图
		if (!$show_origin_pic && $pics) {
			$num = count($pics);
			if ($num==1) {
				$size = 'big';
			} else if ($num==2) {
				$size = 'medium';
			} else {
				$size = 'small';
			}
			foreach ($pics as $k=>$p) {
				if (strpos($p->pic, '!') !== FALSE) continue;
				$pics[$k]->pic = get_upyun_thumb_url($p->pic, $size);
			}
		}
		return $pics;
	}

	//规格
	//20160120 by ajsong 因为APP界面原因,需要检测是否只有一条规格记录且名称为默认规格,是的话就返回空, $allways_show不检测是否只有一条记录
	public function get_specs($goods_id, $allways_show=false) {
		if (!$allways_show) {
			$spec = SQL::share('goods_spec')->where("goods_id='{$goods_id}'")->value('spec');
			if (!$spec || $spec=='默认规格') return array();
		}
		$rs = SQL::share('goods_spec_linkage l')->left('goods_spec_category c', 'l.spec_id=c.id')
			->where("l.goods_id='{$goods_id}' AND l.parent_id='0'")->sort('l.id ASC')->find('l.spec_id as id, c.name, NULL as sub');
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$rs[$k]->sub = SQL::share('goods_spec_linkage l')->left('goods_spec_category c', 'l.spec_id=c.id')
					->where("l.goods_id='{$goods_id}' AND l.parent_id='{$g->id}}'")->sort('l.id ASC')->find('l.spec_id as id, c.name');
			}
		}
		return $rs;
	}
	
	//参数
	public function get_params($params) {
		if (!strlen($params)) return '';
		$arr = array();
		$ps = explode('^', $params);
		foreach ($ps as $p) {
			$s = explode('`', $p);
			$arr[] = array('name'=>$s[0], 'value'=>$s[1]);
		}
		return $arr;
	}
	
	//优惠券
	public function get_coupons($goods_id, $shop_id=0) {
		$now = time();
		$rs = SQL::share('coupon')
			->where("shop_id='{$shop_id}' AND type=0 AND permit_goods=0 AND status=1 AND begin_time<='{$now}' AND (end_time>='{$now}' OR end_time=0 OR handy_time>0)")
			->sort('id ASC')->find("*, '' as time_memo, '' as min_price_memo");
		$private = SQL::share('coupon c')->inner('coupon_goods cg', 'c.id=cg.coupon_id')
			->where("shop_id='{$shop_id}' AND type=0 AND permit_goods=1 AND goods_id='{$goods_id}'
			AND c.status=1 AND begin_time<='{$now}' AND (end_time>='{$now}' OR end_time=0 OR handy_time>0)")
			->sort('c.id ASC')->find("c.*, '' as time_memo, '' as min_price_memo");
		if (is_array($private)) $rs = array_merge($rs, $private);
		if ($rs) {
			$coupon_mod = m('coupon');
			foreach ($rs as $k=>$g) {
				$rs[$k] = $coupon_mod->get_coupon_info($g);
			}
		}
		return $rs;
	}

	//获取某个规格下的价格
	public function get_spec_price($goods_id, $spec) {
		$row = SQL::share('goods_spec')->where("goods_id='{$goods_id}' AND spec='{$spec}'")->row('price, promote_price');
		$price = $this->get_min_price(array($row->price, $row->promote_price));
		return $price;
	}
	
	//该商品是否已经收藏
	public function favorited($goods_id, $member_id) {
		$favorited = 0;
		if ($member_id) {
			$favorited = SQL::share('favorite')->where("item_id='{$goods_id}' AND member_id='{$member_id}'")->count();
		}
		return $favorited;
	}

	//状态
	public function status_name($status) {
		$name = $status;
		switch ($status) {
			case 0:
				$name = '下架';
				break;
			case 1:
				$name = '正常';
				break;
		}
		return $name;
	}

	//销售方式：1，保税直发，2：海外直采
	public function sale_method_name($sale_method) {
		$name = '';
		switch ($sale_method) {
			case 1:
				$name = '保税直发';
				break;
			case 2:
				$name = '海外直采';
				break;
		}
		return $name;
	}

	//获取分类
	public function get_categories($parent_id=0) {
		$rs = SQL::share('goods_category')->where("status=1 AND parent_id='{$parent_id}'")->sort('sort ASC, id ASC')->find('*, NULL as categories');
		if ($rs) {
			foreach ($rs as $key=>$g) {
				$rs[$key]->categories = $this->get_categories($g->id);
			}
		}
		return $rs;
	}
	
	//生成分类的option, separated,parents_and_me不用设置,函数递归用, attributes键值:key自定义属性名称,value在categories里的字段名
	public function set_categories_option($categories, $selected_id=0, $attributes=array(), $separated='', $parents_and_me='') {
		if (!is_array($categories)) return '';
		$html = '';
		foreach ($categories as $k=>$g) {
			$html .= '<option value="'.$g->id.'" tree="'.$parents_and_me.$g->id.'"';
			if ($g->id==$selected_id) $html .= ' selected';
			foreach ($attributes as $name=>$value) {
				$html .= " {$name}=\"".preg_replace_callback('/\((\w+)\)/', function($matches)use($g){
						return str_replace('"', '\"', isset($g->$matches[1])?$g->$matches[1]:'');
					}, $value)."\"";
			}
			$html .= '>'.$separated.($k==count($categories)-1?'└':'├').$g->name.'</option>';
			if (is_array($g->categories) && count($g->categories)) {
				$html .= $this->set_categories_option($g->categories, $selected_id, $attributes, '　'.$separated, $parents_and_me.$g->id.',');
			}
		}
		return $html;
	}

	//获取分类与所有上级的id
	public function get_category_parents_tree($category_id) {
		$ids = $category_id;
		$row = SQL::share('goods_category')->where("status=1 AND id='{$category_id}'")->row('parent_id');
		if ($row && $row->parent_id>0) $ids = $this->get_category_parents_tree($row->parent_id) . ',' . $ids;
		return $ids;
	}

	//获取分类与所有下级的id
	public function get_category_children_tree($category_id) {
		$ids = $category_id;
		$rs = SQL::share('goods_category')->where("status=1 AND parent_id='{$category_id}'")->find('id');
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$ids .= ','.$g->id;
				if (SQL::share('goods_category')->where("status=1 AND parent_id='{$g->id}'")->count()) $ids .= ','.$this->get_category_children_tree($g->id);
			}
		}
		$ids = trim($ids, ',');
		return $ids;
	}
}
