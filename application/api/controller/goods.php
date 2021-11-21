<?php
class goods extends core {
	private $goods_mod;

	public function __construct() {
		parent::__construct();
		$this->goods_mod = m('goods');
	}

	//商品列表
	public function index($activity='') {
		$where = "";
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$category_id = $this->request->get('category_id', 0); //分类
		$ext_property = $this->request->get('ext_property'); //扩展属性
		$country_id = $this->request->get('country_id', 0); //产地
		$brand_id = $this->request->get('brand_id', 0); //品牌
		$shop_id = $this->request->get('shop_id', 0); //店铺
		$min_price = $this->request->get('min_price', 0); //价格区间
		$max_price = $this->request->get('max_price', 0);
		$keyword = $this->request->get('keyword', '', 'urldecode'); //关键词搜索
		$order_field = $this->request->get('order_field'); //排序字段
		$order_sort = $this->request->get('order_sort'); //排序方向
		$integral = $this->request->get('integral', 0); //积分商城
		$longitude = $this->request->get('longitude', 0); //经度
		$latitude = $this->request->get('latitude', 0); //纬度
		if ($category_id) {
			$categories = $this->goods_mod->get_category_children_tree($category_id);
			$where .= " AND g.category_id IN ({$categories})";
		}
		if ($ext_property) {
			$exts = explode(',',$ext_property);
			$w = "";
			foreach ($exts as $e) {
				$w .= " OR CONCAT(',',g.ext_property,',') LIKE '%,{$e},%'";
			}
			$where .= " AND (".trim($w,' OR ').")";
		}
		if ($shop_id) {
			$where .= " AND g.shop_id='{$shop_id}'";
		}
		if ($country_id) {
			$where .= " AND g.country_id='{$country_id}'";
		}
		if ($brand_id) {
			$where .= " AND g.brand_id='{$brand_id}'";
		}
		if ($keyword) {
			//插入搜索记录表
			if ($this->member_id) {
				SQL::share('seach_history')->delete("member_id='{$this->member_id}' AND content='{$keyword}'");
				SQL::share('seach_history')->insert(array('member_id'=>$this->member_id, 'content'=>$keyword, 'add_time'=>time()));
			}
			$where .= " AND (g.name like '%{$keyword}%' OR g.description like '%{$keyword}%' OR g.keywords like '%{$keyword}%')";
		}
		if ($min_price) {
			$where .= " AND g.price>='{$min_price}'";
		}
		if ($max_price) {
			$where .= " AND g.price<='{$max_price}'";
		}
		$is_groupbuy = 0; //拼团
		$is_purchase = 0; //秒杀
		$is_chop = 0; //砍价
		if (strlen($activity)) {
			switch ($activity) {
				case '1':case 'groupbuy':$is_groupbuy = 1;break;
				case '2':case 'purchase':$is_purchase = 1;break;
				case '3':case 'chop':$is_chop = 1;break;
			}
		}
		if ($is_groupbuy) {
			$this->check_edition('groupbuy');
			$where .= " AND g.groupbuy_price>0 AND g.groupbuy_amount>g.groupbuy_count";
			//是否已过结束时间
			$ope = "FROM_UNIXTIME(g.groupbuy_end_time,'%Y-%m-%d %H:%i:%s')";
			$where .= " AND TIMESTAMPDIFF(HOUR,{$ope},NOW())<=0";
			$where .= " AND (groupbuy_begin_time=0 OR (groupbuy_begin_time>0 AND groupbuy_end_time=0) OR (groupbuy_begin_time>0 AND TIMESTAMPDIFF(HOUR,{$ope},NOW())<=0))";
		}
		if ($is_purchase) {
			$this->check_edition('purchase');
			$where .= " AND g.purchase_price>0 AND g.purchase_amount>g.purchase_count";
			$ope = "FROM_UNIXTIME(g.purchase_end_time,'%Y-%m-%d %H:%i:%s')";
			$where .= " AND (purchase_begin_time=0 OR (purchase_begin_time>0 AND purchase_end_time=0) OR (purchase_begin_time>0 AND TIMESTAMPDIFF(HOUR,{$ope},NOW())<=0))";
		}
		if ($is_chop) {
			$this->check_edition('chop');
			$where .= " AND g.chop_price>0 AND g.chop_amount>g.chop_count";
			$ope = "FROM_UNIXTIME(g.chop_end_time,'%Y-%m-%d %H:%i:%s')";
			$where .= " AND (chop_begin_time=0 OR (chop_begin_time>0 AND chop_end_time=0) OR (chop_begin_time>0 AND TIMESTAMPDIFF(HOUR,{$ope},NOW())<=0))";
		}
		if ($integral) {
			$this->check_edition(3);
			$where .= " AND g.integral>0";
		}
		if ($longitude && $latitude) {
			$polygon = locationGeo($longitude, $latitude);
			$left_top_lat = $polygon[0]['lat'];
			$left_top_lng = $polygon[0]['lng'];
			$right_bottom_lng = $polygon[3]['lng'];
			$right_bottom_lat = $polygon[3]['lat'];
			$where .= " AND g.longitude>{$left_top_lng} AND g.longitude<{$right_bottom_lng} AND g.latitude>{$right_bottom_lat} AND g.latitude<{$left_top_lat}";
		}
		if (!$order_sort) $order_sort = 'DESC';
		if ($order_sort =='DESC') $order_sort='ASC';
		switch ($order_field) {
			case 'id':
				$orderby = "g.id {$order_sort}";
				break;
			case 'sales':
				$orderby = "g.sales {$order_sort}";
				break;
			case 'price':
				$orderby = "g.price {$order_sort}";
				break;
			default:
				$orderby = "g.sort ASC, g.id DESC";
				break;
		}
		$goods = SQL::share('goods g')->where("g.status=1 {$where}")->sort($orderby)->limit($offset, $pagesize)->find('g.*, NULL as groupbuy_list');
		if ($goods) {
			foreach ($goods as $k=>$g) {
				unset($goods[$k]->content);
				if (in_array('grade', $this->function)) {
					if ($this->member_id) {
						$goods[$k]->grade_price = floatval(SQL::share('goods_grade_price')->where("goods_id='{$g->id}' AND grade_id='{$this->member_grade_id}'")->value('price'));
					} else {
						$grade_id = intval(SQL::share('goods_grade_price')->where("goods_id='{$g->id}'")->value('MIN(grade_id)'));
						$goods[$k]->grade_price = floatval(SQL::share('goods_grade_price')->where("goods_id='{$g->id}' AND grade_id='{$grade_id}'")->value('price'));
					}
				}
				if ($is_groupbuy) {
					$ope = "FROM_UNIXTIME(mg.add_time,'%Y-%m-%d %H:%i:%s')";
					$goods[$k]->groupbuy_list = SQL::share('member_groupbuy mg')->left('member m', 'mg.member_id=m.id')
						->where("goods_id='{$g->id}' AND mg.parent_id=0 AND mg.status=0 AND TIMESTAMPDIFF(HOUR,{$ope},NOW())<='{$g->groupbuy_time}'")
						->sort('mg.id DESC')->pagesize(5)->find('m.id, m.avatar');
				}
			}
		}
		$goods = $this->goods_mod->set_min_prices($goods);
		$goods = add_domain_deep($goods, array('pic'));
		$category = $categories = $brand = $brands = $country = $country_categories = NULL;
		//幻灯广告
		$flashes = $this->_flashes();
		//国家馆进入
		if ($country_id) {
			$country = SQL::share('country')->where($country_id)->row();
			$ids = SQL::share('goods')->where("country_id='{$country_id}'")->returnArray()->find('DISTINCT(category_id)');
			if (!count($ids)) $ids = array(0);
			$country_categories = SQL::share('goods_category')->where("id IN (".implode(',', $ids).")")->find();
		}
		//获取分类
		if ($category_id) {
			$category = SQL::share('goods_category')->where($category_id)->row();
			$categories = $this->goods_mod->get_categories($category_id);
		} else {
			$categories = SQL::share('goods_category')->where("status=1 AND parent_id=0")->sort('sort ASC, id ASC')->find();
		}
		//获取品牌
		if ($brand_id) {
			$brand = SQL::share('brand')->where("$brand_id")->row();
			$brands = SQL::share('brand')->where("status='1'")->sort('sort ASC, id DESC')->find('id, name');
		}
		success(compact('flashes', 'category', 'categories', 'brand', 'brands', 'country', 'country_categories', 'goods'));
	}
	
	//拼团
	public function groupbuy() {
		$this->index('groupbuy');
	}
	
	//秒杀
	public function purchase() {
		$this->index('purchase');
	}
	
	//砍价
	public function chop() {
		$this->index('chop');
	}

	//商品详情
	public function detail() {
		$id = $this->request->get('id', 0);
		$qrcode = $this->request->get('qrcode', 0);
		$row = $this->goods_mod->detail($id, true);
		if (!$row) error('该商品不存在或已下架', -99);
		$row->content = preg_replace('/(width|height):\s*\d+px;?/', '', stripslashes($row->content));
		if (in_array('grade', $this->function)) {
			if ($this->member_id) {
				$row->grade_price = floatval(SQL::share('goods_grade_price')->where("goods_id='{$id}' AND grade_id='{$this->member_grade_id}'")->value('price'));
			} else {
				$grade_id = intval(SQL::share('goods_grade_price')->where("goods_id='{$id}'")->value('MIN(grade_id)'));
				$row->grade_price = floatval(SQL::share('goods_grade_price')->where("goods_id='{$id}' AND grade_id='{$grade_id}'")->value('price'));
			}
		}
		$groupbuy_list = NULL;
		if ($row->groupbuy_show==1) {
			$ope = "FROM_UNIXTIME(mg.add_time,'%Y-%m-%d %H:%i:%s')";
			$groupbuy_list = SQL::share('member_groupbuy mg')->left('member m', 'mg.member_id=m.id')
				->where("goods_id='{$id}' AND mg.parent_id=0 AND mg.status=0 AND TIMESTAMPDIFF(HOUR,{$ope},NOW())<='{$row->groupbuy_time}'")
				->sort('mg.id DESC')->pagesize(5)->find("mg.*, '' as name, m.avatar, 0 as remain, 0 as groupbuy_now");
			if ($groupbuy_list) {
				$member_mod = m('member');
				foreach ($groupbuy_list as $k=>$g) {
					$groupbuy_list[$k]->name = $member_mod->get_name($g->member_id);
					$groupbuy_list[$k]->remain = $row->groupbuy_number - (SQL::share('member_groupbuy')->where("parent_id='{$g->id}'")->count()+1);
					$groupbuy_list[$k]->groupbuy_now = time();
					$groupbuy_list[$k]->groupbuy_end_time = $g->add_time + 60*60*$row->groupbuy_time;
				}
			}
		}
		$row->groupbuy_list = $groupbuy_list;
		$shop = NULL;
		if ($row->shop_id) {
			$shop = SQL::share('shop s')
				->left('province p', 'province_id=s.province')
				->left('city c', 'city_id=s.city')
				->left('district d', 'district_id=s.district')
				->where("s.id='{$row->shop_id}'")->row('s.*, p.name as province_name, c.name as city_name, d.name as district_name');
			if ($shop) {
				$shop->province = $shop->province_name;
				$shop->city = $shop->city_name;
				$shop->district = $shop->district_name;
			}
		}
		$row->shop = $shop;
		$home = o('home');
		$row->recommend = $home->_goods(1, $id);
		if ($this->member_id) {
			SQL::share('member_goods_history')->delete("member_id='{$this->member_id}' AND goods_id='{$id}'");
			SQL::share('member_goods_history')->insert(array('member_id'=>$this->member_id, 'goods_id'=>$id));
		}
		SQL::share('goods')->where($id)->update(array('clicks'=>array('clicks','+1')));
		success($row);
	}
	
	//幻灯广告
	private function _flashes() {
		$now = time();
		$ads = SQL::share('ad')->where("(begin_time=0 OR begin_time<='{$this->now}') AND (end_time=0 OR end_time>='{$this->now}')
			AND status='1' AND position='integral'")->sort('sort ASC, id DESC')->pagesize(5)->find();
		if ($ads) {
			$ads = add_domain_deep($ads, array("pic"));
		}
		return $ads;
	}
	
	//商品详情
	//http://domain/wap/?app=goods&act=scan&qrcode=xxxxx
	public function scan() {
		$reseller_id = 0;
		$qrcode = $this->request->get('qrcode');
		if ($qrcode) {
			$row = SQL::share('order_goods')->where("qrcode='{$qrcode}'")->row();
			if ($row) {
				$reseller_id = SQL::share('order')->where($row->order_id)->value('shop_id');
				if ($reseller_id) {
					header("Location:/wap/?app=goods&act=detail&id={$row->goods_id}&reseller={$reseller_id}");
					exit;
				} else {
					error('找不到对应的渠道商');
				}
			} else {
				error('找不到对应的条码');
			}
		}
		error('找不到对应的商品');
	}
	
	//获取商品对应的规格记录
	public function get_specs() {
		$goods_id = $this->request->get('goods_id', 0);
		if ($goods_id<=0) error('缺少商品id');
		$specs = $this->goods_mod->get_specs($goods_id, true);
		success($specs);
	}
	
	//根据规格树获取规格图片价格等, spec为空即获取商品的第一个规格
	public function get_spec() {
		$goods_id = $this->request->get('goods_id', 0);
		$spec = $this->request->get('spec');
		if ($goods_id<=0) error('缺少商品id');
		$where = '';
		if (strlen($spec)) {
			$where = " AND spec='{$spec}'";
		}
		$row = SQL::share('goods_spec')->where("goods_id='{$goods_id}' {$where}")->row();
		if ($row) {
			$groupbuy_price = $row->groupbuy_price>0 ? $this->goods_mod->get_groupbuy_price($goods_id) : 0;
			$purchase_price = $row->purchase_price>0 ? $this->goods_mod->get_purchase_price($goods_id) : 0;
			$chop_price = $row->chop_price>0 ? $this->goods_mod->get_chop_price($goods_id) : 0;
			$row->price = $this->goods_mod->get_min_price(array($row->price, $row->promote_price, $groupbuy_price, $purchase_price, $chop_price));
		}
		success($row);
	}
	
	//获取附近店铺
	public function get_nearby_shop() {
		$latitude = $this->request->get('latitude', 0, 'float');
		$longitude = $this->request->get('longitude', 0, 'float');
		$rs = SQL::share('shop')->where("status=1 AND latitude>0 AND longitude>0")->sort('distance ASC')->row('*, 0 as distance');
		if ($rs) {
			//$_SESSION['reseller_id'] = $rs->member_id;
			$other = o('other');
			$geo = array('lat1'=>$rs->latitude, 'lng1'=>$rs->longitude, 'lat2'=>$latitude, 'lng2'=>$longitude);
			$distance = $other->get_distance($geo, false);
			if (is_array($distance)) $rs->distance = round($distance->value/1000, 1);
		}
		success($rs);
	}

	//检查防伪码是否有效
	public function check_qrcode() {
		$return = array('valid'=>'0', 'shop'=>'0');
		$qrcode = $this->request->get('qrcode');
		if ($qrcode) {
			$exists = SQL::share('order_goods')->where("qrcode='{$qrcode}'")->count();
			if ($exists) error('防伪码已经被使用');
			$qrcode = base64_encode($qrcode);
			$url = https().$_SERVER['HTTP_HOST']."/api/?app=goods&act=_check_qrcode&qrcode={$qrcode}";
			$json = file_get_contents($url);
			$json = json_decode($json);
			$return = $json;
		}
		success($return);
	}

	public function _check_qrcode() {
		$return = array('valid'=>'0', 'shop'=>'0');
		$qrcode = $this->request->get('qrcode');
		$qrcode = base64_decode($qrcode);
		$row = SQL::share('qrcode')->where("qrcode='{$qrcode}'")->row();
		if ($row) {
			$return['valid'] = '1';
			$return['shop'] = $row->shop_id;
		}
		exit(json_encode($return));
	}

	public function create() {
		$this->create_edit();
	}
	public function edit() {
		$this->create_edit();
	}

	//发布、编辑商品
	public function create_edit() {
		$pics = array();
		$pic_memos = array();
		$goods_id = $this->request->post('id', 0);
		$name = $this->request->post('name');
		$description = $this->request->post('description');
		//$specs = $this->request->post('specs');
		//浏览权限
		$group_ids = $this->request->post('group_ids');
		//规格
		$specs_string = $this->request->post('specs', '', 'xg');
		//运费
		$shipping_fee = $this->request->post('shipping_fee', 0, 'float');
		//分类
		$type_id = $this->request->post('type_id', 0);
		//分利模板
		$commission_template_id = $this->request->post('commission_template_id', 0);

		if ($name=='') error("缺少商品资料");
		//规格
		//规格名称1:价格1:促销价1:库存1
		$stocks = 0;
		if (!$specs_string) {
			//print_r($_SERVER);
			//print_r($_SESSION);
			error('请设置规格、价格、库存');
		}
		$specs = json_decode($specs_string);
		if (!$specs) error('请设置规格、价格、库存');
		$specs = $specs->specs;
		foreach ($specs as $key => $spec) {
			$stocks += $spec->stocks;
		}
		/*
		$specs_arr = explode("||", $specs_string);
		$specs = array();
		$stocks = 0;
		foreach ($specs_arr as $k => $str) {
			$arr = explode(":", $str);
			if (count($arr)==4) {
				$specs[] = $arr;
				$stocks += $arr[3];
			}
		}
		if (!$specs) {
			error('请设定商品规格');
		}
		*/
		//分利
		$commission1 = $commission2 = $commission3 = 0;
		if ($commission_template_id) {
			$commission_template = SQL::share('commission_template')->where("id='{$commission_template_id}' AND member_id='{$this->member_id}'")->row();
			if ($commission_template) {
				$commission1 = $commission_template->commission1;
				$commission2 = $commission_template->commission2;
				$commission3 = $commission_template->commission3;
			} else {
				$commission1 = $commission2 = $commission3 = 0;
			}
		}
		//图片，最多9张
		for ($i=1; $i<=9; $i++) {
			$pic = $this->request->file('goods', "pic{$i}", UPLOAD_LOCAL);
			if ($pic) $pics[] = $pic;
			$pic_memos[] = $this->request->post("memo{$i}");
		}
		if (count($pics)==0) {
			error("至少需要一张商品图片");
		}
		if ($goods_id) {
			//只能编辑我的商品
			if (!SQL::share('goods')->where("id='{$goods_id}' AND shop_id='{$this->shop_id}'")->count()) error('您没有编辑该商品的权限');
			SQL::share('goods')->where("id='{$goods_id}' AND shop_id='{$this->shop_id}'")
				->update(array('name'=>$name, 'pic'=>$pics[0], 'price'=>$specs[0]->price, 'special_price'=>$specs[0]->special_price, 'stocks'=>$stocks,
					'type_id'=>$type_id, 'edit_time'=>time(), 'description'=>$description, 'shipping_fee'=>$shipping_fee,
					'commission_template_id'=>$commission_template_id, 'commission1'=>$commission1, 'commission2'=>$commission2, 'commission3'=>$commission3));
		} else {
			//写入数据
			SQL::share('goods')->insert(array('name'=>$name, 'pic'=>$pics[0], 'price'=>$specs[0]->price, 'special_price'=>$specs[0]->special_price, 'stocks'=>$stocks,
				'sales'=>0, 'clicks'=>0, 'comments'=>0, 'type_id'=>$type_id, 'add_time'=>time(), 'status'=>1, 'sort'=>999, 'description'=>$description,
				'shop_id'=>$this->shop_id, 'shipping_fee'=>$shipping_fee,
				'commission_template_id'=>$commission_template_id, 'commission1'=>$commission1, 'commission2'=>$commission2, 'commission3'=>$commission3));
		}
		if ($goods_id) {
			//写入权限
			//$this->goods_mod->add_own_privilege($goods_id);
			//写入规格
			SQL::share('goods_spec')->delete("goods_id='{$goods_id}'");
			if ($specs) {
				foreach ($specs as $spec) {
					SQL::share('goods_spec')->insert(array('spec'=>$spec->spec, 'price'=>$spec->price, 'special_price'=>$spec->special_price, 'stocks'=>$spec->stocks,
						'goods_id'=>$goods_id));
				}
			}
			//写入图片
			SQL::share('goods_pic')->delete("goods_id='{$goods_id}'");
			if ($pics) {
				for ($i=0; $i<count($pics); $i++) {
					$pic = $pics[$i];
					$memo = $pic_memos[$i];
					if ($pic) {
						SQL::share('goods_pic')->insert(array('goods_id'=>$goods_id, 'shop_id'=>$this->shop_id, 'pic'=>$pic, 'memo'=>$memo, 'add_time'=>time()));
					}
				}
			}
			//更新商品数
			SQL::share('shop')->where($this->shop_id)->update(array('goods'=>SQL::share('goods')->where("shop_id='{$this->shop_id}'")->count()));
			success($goods_id);
		}
		error('发布商品失败');
	}

	//上架商品
	public function on_sale() {
		$goods_id = $this->request->post('goods_id', 0);
		$stocks = $this->request->post('stocks', 0);
		if ($goods_id) {
			//重新上架，需要将商品显示在前面
			//添加时间，也设置成重新上架时间
			$data = array('status'=>1, 'edit_time'=>time());
			if ($stocks) $data['stocks'] = $stocks;
			SQL::share('goods')->where("id='{$goods_id}' AND shop_id='{$this->shop_id}'")->update($data);
			//店铺的商品数量增加1
			$this->_recaculate_goods();
		}
		success('ok');
	}

	//下架商品
	public function off_sale() {
		$goods_id = $this->request->post('goods_id', 0);
		if ($goods_id) {
			SQL::share('goods')->where("id='{$goods_id}' AND shop_id='{$this->shop_id}'")->update(array('status'=>0));
			//店铺的商品数量增加1
			//$sql = "UPDATE yd_shop SET goods=goods+1 WHERE id='{$this->shop_id}' AND member_id='{$this->member_id}'";
			//重新计算店铺商品数量
			$this->_recaculate_goods();
		}
		success('ok');
	}

	//删除商品
	public function delete() {
		$goods_id = $this->request->post('goods_id', 0);
		if ($goods_id) {
			SQL::share('goods')->delete("id='{$goods_id}' AND shop_id='{$this->shop_id}'");
			//删除规格
			//删除图片
			//店铺的商品数量增加1
			//SQL::share('shop')->where("id='{$this->shop_id}' AND member_id='{$this->member_id}'")->update(array('goods'=>array('goods','+1')));
			//重新计算店铺商品数量
			$this->_recaculate_goods();
		}
		success('ok');
	}

	private function _recaculate_goods() {
		SQL::share('shop')->where($this->shop_id)->update(array('goods'=>SQL::share('goods')->where("shop_id='{$this->shop_id}'")->count()));
	}
	
	public function video() {
		$path = $this->request->get('path');
		$html = '<!doctype html>
		<html>
		<head>
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=0" />
		<meta name="format-detection" content="telephone=no" />
		<meta name="format-detection" content="email=no" />
		<meta name="format-detection" content="address=no" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta charset="UTF-8">
		<title>视频播放</title>
		<style>
		html, body{background:#000; height:100%; margin:0; padding:0; overflow:hidden;}
		</style>
		</head>
		<body>
		<video width="100%" height="100%" poster="" preload="auto" autoplay controls>
		<source src="'.$path.'" type="video/mp4" />
		您的浏览器不支持 video 标签。
		</video>
		</body>
		</html>';
		echo $html;
		exit;
	}
}
