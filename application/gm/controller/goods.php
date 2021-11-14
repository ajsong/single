<?php
class goods extends core {
	public $goods_mod;

	public function __construct() {
		parent::__construct();
		$this->goods_mod = m('goods');
	}

	//列表
	public function index() {
		$where = '';
		$id = $this->request->get('id');
        $keyword = $this->request->get('keyword');
        $startprice = $this->request->get('startprice');
		$endprice = $this->request->get('endprice');
		$shop_id = $this->request->get('shop_id');
		$category_id = $this->request->get('category_id');
		$brand_id = $this->request->get('brand_id');
		$status = $this->request->get('status');
        $ext_property = $this->request->get('ext_property', '', 'origin');
		$purchase = $this->request->get('purchase');
        if (strlen($id)) {
            $where .= " AND g.id='{$id}'";
        }
        if (strlen($keyword)) {
            $where .= " AND (g.name LIKE '%{$keyword}%' OR g.description LIKE '%{$keyword}%' OR g.content LIKE '%{$keyword}%')";
        }
		if (strlen($startprice)) {
			$where .= " AND g.price>='{$startprice}'";
		}
		if (strlen($endprice)) {
			$where .= " AND g.price<='{$endprice}'";
		}
		if (strlen($shop_id)) {
			$where .= " AND g.shop_id='{$shop_id}'";
		}
		if (strlen($category_id)) {
			$categories = $this->goods_mod->get_category_children_tree($category_id);
			$where .= " AND g.category_id IN ($categories)";
		}
		if (strlen($brand_id)) {
			$where .= " AND g.brand_id='{$brand_id}'";
		}
		if (strlen($status)) {
			$where .= " AND g.status='{$status}'";
		}
		if ($ext_property) {
			if (!is_array($ext_property)) $ext_property = explode(',', $ext_property);
			$where .= " AND (";
			foreach ($ext_property as $e) {
				$where .= "CONCAT(',',g.ext_property,',') LIKE '%,{$e},%' OR ";
			}
			$where = trim($where, ' OR ').")";
		}
		if (strlen($purchase)) {
			$where .= " AND g.purchase_price>0 AND g.purchase_amount>g.purchase_count";
		}
		$rs = SQL::share('goods g')
			->left('goods_category c', 'g.category_id=c.id')
			->left('brand b', 'g.brand_id=b.id')
			->left('shop s', 'g.shop_id=s.id')
			->setpages(compact('id', 'keyword', 'startprice', 'endprice', 'shop_id', 'category_id', 'brand_id', 'status', 'ext_property', 'purchase'))
			->where($where)->isezr()->sort('g.id DESC')->find('g.*, c.name as category_name, b.name as brand_name, s.name as shop_name');
		$sharepage = SQL::share()->page;
		if ($rs) {
			foreach ($rs as $k => $g) {
				$rs[$k]->url = urlencode(https().$_SERVER['HTTP_HOST']."/wap/?app=goods&act=detail&id={$g->id}&qrcode=1");
			}
		}
		$rs = add_domain_deep($rs, array('pic'));
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);

		$shops = SQL::share('shop')->sort('id ASC')->find('id, name');
		$this->smarty->assign('shops', $shops);
		//品牌
		$brand = SQL::share('brand')->where("status=1")->sort('sort ASC, id ASC')->find('id, name');
		$this->smarty->assign('brand', $brand);
		//分类
		$categories = $this->goods_mod->get_categories();
		$this->smarty->assign('categories', $this->goods_mod->set_categories_option($categories, $category_id));
		$this->display();
	}
	
	public function change_ext_property() {
		$id = $this->request->post('id', 0);
		$checked = $this->request->post('checked', 0);
		$ext_property = $this->request->post('ext_property');
		$ext = SQL::share('goods')->where($id)->value('ext_property');
		if (strpos(",{$ext},", ",{$ext_property},")===false) {
			if ($checked) {
				SQL::share('goods')->where($id)->update(array('ext_property'=>strlen($ext)?"{$ext},{$ext_property}":$ext_property));
			}
		} else {
			if (!$checked) {
				$ext = preg_replace("/,?{$ext_property}/", '', $ext);
				SQL::share('goods')->where($id)->update(array('ext_property'=>$ext));
			}
		}
		success('ok');
	}
	
	//添加
	public function add() {
		$this->edit();
	}
	//编辑
	public function edit() {
		$id = $this->request->get('id', 0);
		$category_id = 0;
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$type = $this->request->post('type', 0);
			$name = $this->request->post('name');
			$price = $this->request->post('price', 0, 'float');
			$promote_price = $this->request->post('promote_price', 0, 'float');
			$promote_begin_time = $this->request->post('promote_begin_time');
			$promote_end_time = $this->request->post('promote_end_time');
			$market_price = $this->request->post('market_price', 0, 'float');
			$cost_price = $this->request->post('cost_price', 0, 'float');
			$description = $this->request->post('description');
			$keywords = $this->request->post('keywords');
			$content = $this->request->post('content', '', '\\');
			$category_id = $this->request->post('category_id', 0);
			$shop_id = $this->request->post('shop_id', 0);
			$brand_id = $this->request->post('brand_id', 0);
			$country_id = $this->request->post('country_id', 0);
			$stocks = $this->request->post('stocks', 0);
			$stock_alert_number = $this->request->post('stock_alert_number', 0);
			$ext_property = $this->request->post('ext_property', '', 'origin');
			$free_shipping = $this->request->post('free_shipping', 0);
			$shipping_fee = $this->request->post('shipping_fee', 0, 'float');
			$shipping_fee_id = $this->request->post('shipping_fee_id', 0);
			$weight = $this->request->post('weight', 0, 'float');
			$free_shipping_count = $this->request->post('free_shipping_count', 0);
			$integral = $this->request->post('integral', 0);
			$give_integral = $this->request->post('give_integral', 0);
			$sort = $this->request->post('sort', 0);
			$status = $this->request->post('status', 0);
			$release_time = $this->request->post('release_time');
			$seller_note = $this->request->post('note');
			$in_shop = $this->request->post('in_shop', 0);
			$sale_method = $this->request->post('sale_method', 0);
			
			$groupbuy_price = $this->request->post('groupbuy_price', 0, 'float');
			$groupbuy_begin_time = $this->request->post('groupbuy_begin_time');
			$groupbuy_end_time = $this->request->post('groupbuy_end_time');
			$groupbuy_amount = $this->request->post('groupbuy_amount', 0);
			$groupbuy_number = $this->request->post('groupbuy_number', 0);
			$groupbuy_time = $this->request->post('groupbuy_time', 0);
			$groupbuy_limit = $this->request->post('groupbuy_limit', 0);
			$groupbuy_free_shipping = $this->request->post('groupbuy_free_shipping', 0);
			
			$purchase_price = $this->request->post('purchase_price', 0, 'float');
			$purchase_begin_time = $this->request->post('purchase_begin_time');
			$purchase_end_time = $this->request->post('purchase_end_time');
			$purchase_amount = $this->request->post('purchase_amount', 0);
			$purchase_limit = $this->request->post('purchase_limit', 0);
			$purchase_free_shipping = $this->request->post('purchase_free_shipping', 0);
			
			$chop_price = $this->request->post('chop_price', 0, 'float');
			$chop_begin_time = $this->request->post('chop_begin_time');
			$chop_end_time = $this->request->post('chop_end_time');
			$chop_num = $this->request->post('chop_num', 5);
			$chop_amount = $this->request->post('chop_amount', 0);
			$chop_time = $this->request->post('chop_time', 0);
			$chop_free_shipping = $this->request->post('chop_free_shipping', 0);
			
			$pics = $this->request->post('pics', '', 'origin');
			$videos = $this->request->post('videos', '', 'origin');
			$memos = $this->request->post('memos', '', 'origin');
			
			$grade_id = $this->request->post('grade_id', '', 'origin');
			$grade_price = $this->request->post('grade_price', '', 'origin');
			
			$params_name = $this->request->post('params_name', '', 'origin');
			$params_value = $this->request->post('params_value', '', 'origin');
			
			$commission_type = $this->request->post('commission_type', 0);
			$commissions = $this->request->post('commissions', '', 'origin');
			
			$spec_id = $this->request->post('spec_id', '', 'origin');
			$spec_subid = $this->request->post('spec_subid', '', 'origin');
			$spec_tree = $this->request->post('spec_tree', '', 'origin');
			$spec_price = $this->request->post('spec_price', '', 'origin');
			$spec_stock = $this->request->post('spec_stock', '', 'origin');
			$spec_promote = $this->request->post('spec_promote', '', 'origin');
			$spec_groupbuy = $this->request->post('spec_groupbuy', '', 'origin');
			$spec_purchase = $this->request->post('spec_purchase', '', 'origin');
			$spec_chop = $this->request->post('spec_chop', '', 'origin');
			$spec_pic = $this->request->post('spec_pic', '', 'origin');
			
			$pic = $this->request->file('goods', 'pic', UPLOAD_LOCAL);
			$ad_pic = $this->request->file('goods', 'ad_pic', UPLOAD_LOCAL);
			if ($promote_begin_time) $promote_begin_time = strtotime($promote_begin_time);
			if ($promote_end_time) $promote_end_time = strtotime($promote_end_time);
			if ($release_time) $release_time = strtotime($release_time);
			if ($free_shipping==1) {
				$shipping_fee = 0;
				$shipping_fee_id = 0;
			} else {
				if ($shipping_fee_id>0) $shipping_fee = 0;
				else $shipping_fee_id = 0;
			}
			if ($stock_alert_number<0) $stock_alert_number = 0;
			if (is_array($commissions)) $commissions = implode(',', $commissions);
			if (is_array($ext_property)) $ext_property = implode(',', $ext_property);
			if ($type>0) {
				$free_shipping = 1;
				$shipping_fee = 0;
				$shipping_fee_id = 0;
			}
			if ($free_shipping_count<0) $free_shipping_count = 0;
			
			//拼团
			if ($groupbuy_begin_time) $groupbuy_begin_time = strtotime($groupbuy_begin_time);
			if ($groupbuy_end_time) $groupbuy_end_time = strtotime($groupbuy_end_time);
			if ($groupbuy_number<=0) $groupbuy_number = 2;
			if ($groupbuy_time<=0) $groupbuy_time = 24;
			if ($groupbuy_price>0) {
				$amount = $stocks;
				if (is_array($spec_tree)) {
					foreach ($spec_tree as $k=>$g) {
						if (!strlen($g) || !strlen($spec_price[$k]) || !strlen($spec_stock[$k])) continue;
						$dstock = (isset($spec_stock[$k]) && intval($spec_stock[$k])) ? intval($spec_stock[$k]) : 1;
						$dgroupbuy = (isset($spec_groupbuy[$k]) && intval($spec_groupbuy[$k])) ? intval($spec_groupbuy[$k]) : 0;
						if ($dgroupbuy>0) {
							if ($dstock<$amount) $amount = $dstock;
						}
					}
				}
				if ($groupbuy_amount>$amount) $groupbuy_amount = $amount;
			}
			if ($groupbuy_amount<0) $groupbuy_amount = 0;
			if ($groupbuy_price>0 && ($groupbuy_amount>$stocks || $groupbuy_amount==0)) $groupbuy_amount = $stocks;
			if ($groupbuy_amount>0 && $groupbuy_amount%$groupbuy_number>0) { //拼团数量必须是成团人数整倍数
				$div = floor($groupbuy_amount/$groupbuy_number);
				$groupbuy_amount = $div * $groupbuy_number;
			}
			if ($groupbuy_limit<0) $groupbuy_limit = 0;
			
			//秒杀
			if ($purchase_begin_time) $purchase_begin_time = strtotime($purchase_begin_time);
			if ($purchase_end_time) $purchase_end_time = strtotime($purchase_end_time);
			if ($purchase_price>0) {
				$amount = $stocks;
				if (is_array($spec_tree)) {
					foreach ($spec_tree as $k=>$g) {
						if (!strlen($g) || !strlen($spec_price[$k]) || !strlen($spec_stock[$k])) continue;
						$dstock = (isset($spec_stock[$k]) && intval($spec_stock[$k])) ? intval($spec_stock[$k]) : 1;
						$dpurchase = (isset($spec_purchase[$k]) && intval($spec_purchase[$k])) ? intval($spec_purchase[$k]) : 0;
						if ($dpurchase>0) {
							if ($dstock<$amount) $amount = $dstock;
						}
					}
				}
				if ($purchase_amount>$amount) $purchase_amount = $amount;
			}
			if ($purchase_amount<0) $purchase_amount = 0;
			if ($purchase_price>0 && ($purchase_amount>$stocks || $purchase_amount==0)) $purchase_amount = $stocks;
			if ($purchase_limit<0) $purchase_limit = 0;
			
			//砍价
			if ($chop_num<=1) $chop_num = 2;
			if ($chop_begin_time) $chop_begin_time = strtotime($chop_begin_time);
			if ($chop_end_time) $chop_end_time = strtotime($chop_end_time);
			if ($chop_time<=0) $chop_time = 24;
			if ($chop_price>0) {
				$amount = $stocks;
				if (is_array($spec_tree)) {
					foreach ($spec_tree as $k=>$g) {
						if (!strlen($g) || !strlen($spec_price[$k]) || !strlen($spec_stock[$k])) continue;
						$dstock = (isset($spec_stock[$k]) && intval($spec_stock[$k])) ? intval($spec_stock[$k]) : 1;
						$dchop = (isset($spec_chop[$k]) && intval($spec_chop[$k])) ? intval($spec_chop[$k]) : 0;
						if ($dchop>0) {
							if ($dstock<$amount) $amount = $dstock;
						}
					}
				}
				if ($chop_amount>$amount) $chop_amount = $amount;
			}
			if ($chop_amount<0) $chop_amount = 0;
			if ($chop_price>0 && ($chop_amount>$stocks || $chop_amount==0)) $chop_amount = $stocks;
			
			$params = array();
			if (is_array($params_name)) {
				foreach ($params_name as $k=>$p) {
					if (!strlen($p) || !strlen($params_value[$k])) continue;
					$params[] = $p.'`'.$params_value[$k];
				}
			}
			$params = implode('^', $params);
			
			$data = compact('type', 'name', 'description', 'keywords', 'content', 'price', 'market_price', 'cost_price', 'promote_price', 'promote_begin_time',
				'promote_end_time', 'pic', 'ad_pic', 'integral', 'give_integral', 'category_id', 'shop_id', 'brand_id', 'country_id', 'stocks', 'stock_alert_number',
				'ext_property', 'shipping_fee', 'shipping_fee_id', 'free_shipping', 'weight', 'free_shipping_count', 'commission_type', 'commissions', 'params', 'sort',
				'status', 'release_time', 'seller_note', 'in_shop', 'sale_method',
				'groupbuy_price', 'groupbuy_begin_time', 'groupbuy_end_time', 'groupbuy_amount', 'groupbuy_number', 'groupbuy_time', 'groupbuy_limit', 'groupbuy_free_shipping',
				'purchase_price', 'purchase_begin_time', 'purchase_end_time', 'purchase_amount', 'purchase_limit', 'purchase_free_shipping',
				'chop_price', 'chop_begin_time', 'chop_end_time', 'chop_num', 'chop_amount', 'chop_time', 'chop_free_shipping');
			if ($id>0) {
				$data['edit_time'] = time();
				SQL::share('goods')->where($id)->update($data);
			} else {
				$data['add_time'] = time();
				$id = SQL::share('goods')->insert($data);
			}
			//关键词
			SQL::share('goods_keyword')->delete("goods_id='{$id}'");
			if ($keywords) {
				SQL::share('goods_keyword')->insert(array('keyword'=>$keywords, 'goods_id'=>$id));
			}
			//等级会员价
			if (in_array('grade', $this->function)) {
				SQL::share('goods_grade_price')->delete("goods_id='{$id}'");
				if (is_array($grade_id) && is_array($grade_price)) {
					$did = $dprice = array();
					foreach ($grade_id as $k=>$g) {
						if (!strlen($g) || in_array($g, $did) || !isset($grade_price[$k]) || floatval($grade_price[$k])<=0) continue;
						$price = (isset($grade_price[$k]) && floatval($grade_price[$k])) ? (floatval($grade_price[$k])<0?0:floatval($grade_price[$k])) : 0;
						$did[] = $g;
						$dprice[] = $price;
					}
					SQL::share('goods_grade_price')->insert(array('goods_id'=>$id, 'grade_id'=>$did, 'price'=>$dprice), 'grade_id');
				}
			}
			//商品规格
			SQL::share('goods_spec_linkage')->delete("goods_id='{$id}'");
			SQL::share('goods_spec')->delete("goods_id='{$id}'");
			if (is_array($spec_id) && is_array($spec_subid) && is_array($spec_tree)) {
				//保存规格分类联动
				$did = $dparent = array();
				foreach ($spec_id as $k=>$g) {
					if (!strlen($g)) continue;
					$did[] = $g;
					$dparent[] = 0;
				}
				foreach ($spec_subid as $k=>$g) {
					if (!strlen($g)) continue;
					$did[] = $g;
					$parent_id = intval(SQL::share('goods_spec_category')->skipClient()
						->where("id='{$g}'")->value('parent_id'));
					$dparent[] = $parent_id;
				}
				SQL::share('goods_spec_linkage')->insert(array('goods_id'=>$id, 'spec_id'=>$did, 'parent_id'=>$dparent), 'spec_id');
				//保存规格价格
				$dtree = $dprice = $dstock = $dpromote = $dgroupbuy = $dpurchase = $dchop = $dpic = array();
				foreach ($spec_tree as $k=>$g) {
					if (!strlen($g) || !isset($spec_price[$k]) || floatval($spec_price[$k])<=0 || !isset($spec_stock[$k]) || intval($spec_stock[$k])<=0) continue;
					$dtree[] = $g;
					$dprice[] = (isset($spec_price[$k]) && floatval($spec_price[$k])) ? (floatval($spec_price[$k])<0?0:floatval($spec_price[$k])) : 0;
					$dstock[] = (isset($spec_stock[$k]) && intval($spec_stock[$k])) ? (intval($spec_stock[$k])<1?1:intval($spec_stock[$k])) : 1;
					$dpromote[] = (isset($spec_promote[$k]) && floatval($spec_promote[$k])) ? (floatval($spec_promote[$k])<0?0:floatval($spec_promote[$k])) : 0;
					$dgroupbuy[] = (isset($spec_groupbuy[$k]) && floatval($spec_groupbuy[$k])) ? (floatval($spec_groupbuy[$k])<0?0:floatval($spec_groupbuy[$k])) : 0;
					$dpurchase[] = (isset($spec_purchase[$k]) && floatval($spec_purchase[$k])) ? (floatval($spec_purchase[$k])<0?0:floatval($spec_purchase[$k])) : 0;
					$dchop[] = (isset($spec_chop[$k]) && floatval($spec_chop[$k])) ? (floatval($spec_chop[$k])<0?0:floatval($spec_chop[$k])) : 0;
					$dpic[] = (isset($spec_pic[$k]) && trim($spec_pic[$k])) ? trim($spec_pic[$k]) : '';
				}
				SQL::share('goods_spec')->insert(array('goods_id'=>$id, 'spec'=>$dtree, 'price'=>$dprice, 'promote_price'=>$dpromote, 'groupbuy_price'=>$dgroupbuy,
					'purchase_price'=>$dpurchase, 'chop_price'=>$dchop, 'stocks'=>$dstock, 'pic'=>$dpic), 'spec');
			}
			//图片表
			SQL::share('goods_pic')->delete("goods_id='{$id}'");
			if (is_array($pics)) {
				$dpic = $dvideo = $dmemo = array();
				foreach ($pics as $k=>$g) {
					if (!strlen($g)) continue;
					$dpic[] = $g;
					$dvideo[] = (isset($videos[$k]) && trim($videos[$k])) ? trim($videos[$k]) : '';
					$dmemo[] = (isset($memos[$k]) && trim($memos[$k])) ? trim($memos[$k]) : '';
				}
				SQL::share('goods_pic')->insert(array('goods_id'=>$id, 'pic'=>$dpic, 'video'=>$dvideo, 'memo'=>$dmemo, 'add_time'=>time()), 'pic');
			}
			location("?app=goods&act=edit&id={$id}&msg=1");
		} else if ($id>0) {
			//商品
			$row = SQL::share('goods')->where($id)->row();
			if (strlen($row->commissions)) $row->commissions = explode(',', $row->commissions);
			$category_id = $row->category_id;
			//参数
			if (strlen($row->params)) {
				$arr = array();
				$ps = explode('^', $row->params);
				foreach ($ps as $p) {
					$s = explode('`', $p);
					$arr[] = array('name'=>$s[0], 'value'=>$s[1]);
				}
				$row->params = $arr;
			}
			//等级会员价
			if (in_array('grade', $this->function)) {
				$gradeprices = SQL::share('goods_grade_price')->where("goods_id='{$id}'")->find();
			}
			//商品图
			$pics = SQL::share('goods_pic')->where("goods_id='{$id}'")->find();
			$pics = add_domain_deep($pics, array('pic'));
			//规格
			$spec = SQL::share('goods_spec_linkage gsl')->left('goods_spec_category gsc', 'gsl.spec_id=gsc.id')->skipClient()
				->where("gsl.goods_id='{$id}' AND gsl.parent_id=0")->find('gsl.*, gsc.name, NULL as sub');
			if ($spec) {
				foreach ($spec as $k=>$g) {
					$spec[$k]->sub = SQL::share('goods_spec_linkage gsl')->left('goods_spec_category gsc', 'gsl.spec_id=gsc.id')->skipClient()
						->where("gsl.goods_id='{$id}' AND gsl.parent_id='{$g->spec_id}'")->find('gsl.*, gsc.name');
				}
			}
			$data = SQL::share('goods_spec')->where("goods_id='{$id}'")->find();
			$specData = array();
			if ($data) {
				foreach ($data as $k=>$g) {
					$specData['spec_price_'.str_replace(',', '_', $g->spec)] = strlen($g->price) ? $g->price : '';
					$specData['spec_promote_'.str_replace(',', '_', $g->spec)] = strlen($g->promote_price) ? $g->promote_price : '';
					$specData['spec_groupbuy_'.str_replace(',', '_', $g->spec)] = strlen($g->groupbuy_price) ? $g->groupbuy_price : '';
					$specData['spec_purchase_'.str_replace(',', '_', $g->spec)] = strlen($g->purchase_price) ? $g->purchase_price : '';
					$specData['spec_chop_'.str_replace(',', '_', $g->spec)] = strlen($g->chop_price) ? $g->chop_price : '';
					$specData['spec_stock_'.str_replace(',', '_', $g->spec)] = strlen($g->stocks) ? $g->stocks : '';
					$specData['spec_pic_'.str_replace(',', '_', $g->spec)] = strlen($g->pic) ? $g->pic : '';
				}
			}
		} else {
			$row = t('goods');
			$gradeprices = NULL;
			$pics = NULL;
			$spec = NULL;
			$specData = array();
		}
		$this->smarty->assign('row', $row);
		if (in_array('grade', $this->function)) {
			$this->smarty->assign('gradeprices', $gradeprices);
		}
		$this->smarty->assign('pics', $pics);
		$this->smarty->assign('spec', $spec);
		$this->smarty->assign('specData', json_encode($specData));
		//店铺
		$shop = SQL::share('shop')->find('id, name');
		$this->smarty->assign('shop', $shop);
		//品牌
		$brand = SQL::share('brand')->where("status='1'")->find('id, name');
		$this->smarty->assign('brand', $brand);
		//分类
		$categories = $this->goods_mod->get_categories();
		$this->smarty->assign('categories', $this->goods_mod->set_categories_option($categories, $category_id));
		//等级
		if (in_array('grade', $this->function)) {
			$grade = SQL::share('grade')->where("status='1'")->sort('sort ASC, id ASC')->find('id, name');
			$this->smarty->assign('grade', $grade);
		}
		//运费模板
		$shipping = SQL::share('shipping_fee')->find();
		$this->smarty->assign('shipping', $shipping);
		$this->display('goods.edit.html');
	}
	
	//删除
	public function delete() {
		$id = $this->request->get('id', 0);
		if (SQL::share('goods')->delete($id)) {
			//删除该商品产品规格表
			SQL::share('goods_spec')->delete("goods_id='{$id}'");
			//删除该商品被添加的购物车
			SQL::share('cart')->delete("goods_id='{$id}'");
			//删除该商品的收藏
			SQL::share('favorite')->delete("item_id='{$id}' AND type_id=1");
			location("?app=goods&act=index");
		} else {
			script('删除失败', "?app=goods&act=index");
		}
	}
	
	public function upload_pic() {
		$result = $this->request->file('goods', 'pic', UPLOAD_LOCAL);
		success($result);
	}
	public function get_shipping() {
		$rs = SQL::share('shipping_fee')->find();
		success($rs);
	}
	public function get_grade_price() {
		$rs = SQL::share('grade')->where("status=1")->sort('sort ASC, id ASC')->find();
		success($rs);
	}
	
	//选择规格分类
	public function get_spec_category() {
		$parent_id = $this->request->get('parent_id', 0);
		$rs = SQL::share('goods_spec_category')->skipClient()
			->where("parent_id='{$parent_id}'")->sort('id ASC')->find();
		success($rs);
	}
	
	//设置规格分类
	public function set_spec_category() {
		$parent_id = $this->request->post('parent_id', 0);
		$name = $this->request->post('name');
		if(!strlen($name))error('缺少名称');
		$id = SQL::share('goods_spec_category')->skipClient()
			->where("parent_id='{$parent_id}' AND name='{$name}'")->value('id');
		if (!$id) {
			$id = SQL::share('goods_spec_category')->insert(array('parent_id'=>$parent_id, 'name'=>$name));
		}
		success($id);
	}
	
    //改变分类
    public function check_cate(){
    	$checkbox = $this->request->post('checkbox', '', 'origin');
		$cate_type = $this->request->post('cate_type');
    	if (is_array($checkbox)) {
			$ids = implode(',', $checkbox);
			switch ($cate_type) {
				case 'price':
					$price = $this->request->post('price', 0, 'float');
					$price_type = $this->request->post('price_type', 0);
					if ($price>0) {
						if ($price_type==0) {
							SQL::share('goods')->where("id IN ({$ids})")->update(array('price'=>$price));
							SQL::share('goods_spec')->where("goods_id IN ({$ids})")->update(array('price'=>$price));
						} else {
							$price /= 100;
							foreach ($checkbox as $k=>$id) {
								SQL::share('goods')->where($id)->update(array('price'=>array("*{$price}")));
								SQL::share('goods_spec')->where("goods_id='{$id}'")->update(array('price'=>array("*{$price}")));
							}
						}
					}
					break;
				case 'integral':
					$integral = $this->request->post('integral', 0);
					$integral_type = $this->request->post('integral_type', 0);
					if ($integral>0) {
						if ($integral_type==0) {
							SQL::share('goods')->where("id IN ({$ids})")->update(array('integral'=>$integral));
						} else {
							$integral /= 100;
							foreach ($checkbox as $k=>$id) {
								SQL::share('goods')->where($id)->update(array('integral'=>array("FLOOR(integral*{$integral})", '')));
							}
						}
					}
					break;
				case 'ext_property':
					$ext_property = $this->request->post('ext_property');
					if (is_array($ext_property)) {
						$ext_property = implode(',', $ext_property);
						SQL::share('goods')->where("id IN ({$ids})")->update(array('ext_property'=>$ext_property));
					}
					break;
				case 'cate':
					$catechange = $this->request->post('catechange', 0);
					if ($catechange>0) {
						SQL::share('goods')->where("id IN ({$ids})")->update(array('category_id'=>$catechange));
					}
					break;
				case 'brand':
					$brandchange = $this->request->post('brandchange', 0);
					if ($brandchange>0) {
						SQL::share('goods')->where("id IN ({$ids})")->update(array('brand_id'=>$brandchange));
					}
					break;
			}
    	}
    	location("?app=goods&act=index");
    }
	
	public function goods_qrcode(){
		$where = '';
		//分页
		$id = $this->request->get('id', 0);
		$keyword = $this->request->get('keyword');
		$shop_id = $this->request->get('shop_id', 0);
		if ($keyword) {
			$where .= "AND (g.name LIKE '%{$keyword}%' OR g.description LIKE '%{$keyword}%' OR g.content LIKE '%{$keyword}%')";
		}
		if ($id) {
			$where .= "AND g.id='{$id}'";
		}
		$rs = SQL::share('goods g')->where($where)->isezr()->setpages(compact('id', 'keyword', 'shop_id'))->sort('g.id DESC')->find('g.*');
		$sharepage = SQL::share()->page;
		$wherebase64 = SQL::share()->wherebase64;
		if ($rs) {
			foreach ($rs as $key => $g) {
				$rs[$key]->url = urlencode(https().$_SERVER['HTTP_HOST']."/wap/?app=goods&act=detail&id={$shop_id}&goods_id={$g->id}&reseller=");
			}
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->smarty->assign('where', $wherebase64);
		$shops = SQL::share('shop')->sort('id ASC')->find('id, name');
		$this->smarty->assign('shops',$shops);
		$this->display();
	}
	
    public function qrcode_out() {
    	set_time_limit(600);
		$where = '';
		$id = $this->request->get('id', 0);
        $keyword = $this->request->get('keyword');
		$shop_id = $this->request->get('shop_id');
		if ($keyword) {
            $where .= "AND (g.name LIKE '%{$keyword}%' OR g.huohao LIKE '%{$keyword}%'
            	OR g.description LIKE '%{$keyword}%' OR g.content LIKE '%{$keyword}%')";
        }
        if ($id) {
            $where .= "AND g.id='{$id}'";
        }
		if ($where || $shop_id) {
			require_once (SDK_PATH . '/class/phpqrcode/phpqrcode.php');
			$shop_name = SQL::share('shop')->where($shop_id)->row('name');
			$rs = SQL::share('goods g')->where($where)->sort('g.id DESC')->find('g.id, g.name');
			//创建目录
			$path = ROOT_PATH.UPLOAD_PATH.'/qrcode/'.time().rand(000,999);
			mkdir($path, 0777);
			foreach ($rs as $g) {
				$url = https().$_SERVER['HTTP_HOST']."/wap/?app=goods&act=detail&id={$shop_id}&goods_id={$g->id}&reseller=";
				$url = urldecode($url);
				$name = $shop_name->name.$g->name;
				$name = $this->replace_specialChar($name);
				$name = iconv('UTF-8', 'GB2312//IGNORE', $name);
				QRcode::pngg($url, $name, $path, true, true);
			}
			require_once (SDK_PATH . '/class/phpqrcode/PHPZip.php');
			$zip = new PHPZip();
			//压缩并下载并
			$zip->ZipAndDownload($path);
			//删除文件夹
			rmdir($path);
		} else {
			echo "<meta charset='UTF-8'><script>alert('请填入搜索条件！');</script>";
		}
	}
	
	//删除字符串中的特殊字符
	public function replace_specialChar($strParam){
    	$regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|\、|\、|\s|/";
    	return preg_replace($regex,"",$strParam);
	}
}
