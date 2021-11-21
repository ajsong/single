<?php
class cart extends core {
	public $goods_mod;
	public $member_mod;
	public $order_mod;
	public $commission_mod;
	public $coupon_mod;

	public function __construct() {
		parent::__construct();
		$this->goods_mod = m('goods');
		$this->member_mod = m('member');
		$this->order_mod = m('order');
		$this->commission_mod = m('commission');
		$this->coupon_mod = m('coupon');
	}

	//购物车数量
	public function total($is_show=true) {
		$quantity = 0;
		$total_price = 0;
		$where = "goods_id>0";
		if ($this->member_id>0) {
			$where .= " AND (c.member_id='{$this->member_id}' OR session_id='{$this->session_id}')";
		} else {
			$where .= " AND session_id='{$this->session_id}'";
		}
		$rs = SQL::share('cart c')->left('goods g', 'c.goods_id=g.id')->where("{$where} AND g.status='1'")
			->find("c.goods_id, c.spec, c.quantity, g.stocks, g.price, g.promote_price");
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$price = $this->goods_mod->get_min_price(array($g->price, $g->promote_price));
				$stocks = $g->stocks;
				if (strlen($g->spec)) {
					$spec = SQL::share('goods_spec')->where("goods_id='{$g->goods_id}' AND spec='{$g->spec}'")->row();
					if ($spec) {
						$price = $this->goods_mod->get_min_price(array($spec->price, $spec->promote_price));
						$stocks = $spec->stocks;
					}
				}
				if ($stocks<=0) continue;
				$quantity += $g->quantity;
				$total_price += $price * $g->quantity;
			}
		}
		$data = compact('quantity', 'total_price');
		if ($is_show) success($data);
		return $data;
	}
	
	//合并购物车里同一会员、同一商品、同一规格的商品的数量
	private function _merge() {
		$table = "{$this->tbp}cart";
		$where = "";
		if ($this->member_id>0) {
			$where .= "(member_id='{$this->member_id}' OR session_id='{$this->session_id}')";
		} else {
			$where .= "session_id='{$this->session_id}'";
		}
		$rs = SQL::share()->query("SELECT id, goods_id, spec FROM {$table} WHERE {$where}
			AND CONCAT(goods_id,spec) IN (SELECT CONCAT(goods_id,spec) FROM {$table} WHERE {$where} GROUP BY goods_id,spec HAVING COUNT(*)>1)
			AND id IN (SELECT MIN(id) FROM {$table} WHERE {$where} GROUP BY goods_id,spec HAVING COUNT(*)>1)");
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$quantity = SQL::share('cart')->where("{$where} AND goods_id='{$g->goods_id}' AND spec='{$g->spec}'")->value('SUM(quantity)');
				SQL::share('cart')->where($g->id)->update(compact('quantity'));
			}
		}
		$rs = SQL::share()->query("SELECT id FROM {$table} WHERE {$where}
			AND CONCAT(goods_id,spec) IN (SELECT CONCAT(goods_id,spec) FROM {$table} WHERE {$where} GROUP BY goods_id,spec HAVING COUNT(*)>1)
			AND id NOT IN (SELECT MIN(id) FROM {$table} WHERE {$where} GROUP BY goods_id,spec HAVING COUNT(*)>1)");
		if ($rs) {
			foreach ($rs as $k=>$g) {
				SQL::share('cart')->delete($g->id);
			}
		}
	}

	//加入到购物车
	public function add() {
		$this->_merge();
		$cart_id = $this->request->post('cart_id', 0); //存在cart_id即修改规格(先删除购物车再添加)
		$goods = $this->request->post('goods', '', 'xg');
		//邀请我的渠道商的店铺id
		$reseller_id = $this->request->session('reseller_id', 0);
		//操作是根据quantity值, 传1:当前操作是购物车修改商品总数量, 0:进行累加(数字)/累减(-数字)
		$edit = $this->request->post('edit', 0);
		if ($goods) {
			$goods = json_decode($goods);
			if (is_array($goods) && count($goods)) {
				foreach ($goods as $k=>$g) {
					$goods = SQL::share('goods')->where($g->goods_id)->row('shop_id, stocks, price, promote_price');
					if (!$goods) continue;
					if (in_array('grade', $this->function)) {
						if ($this->member_id) {
							$goods->grade_price = floatval(SQL::share('goods_grade_price')
								->where("goods_id='{$g->goods_id}' AND grade_id='{$this->member_grade_id}'")->value('price'));
						} else {
							$goods->grade_price = 0;
						}
						$goods->price = $this->goods_mod->get_min_price(array($goods->price, $goods->grade_price));
					}
					$shop_id = $goods->shop_id;
					$stocks = $goods->stocks;
					$price = $this->goods_mod->get_min_price(array($goods->price, $goods->promote_price));
					if (isset($g->spec) && strlen($g->spec)) {
						//检测库存
						$spec = SQL::share('goods_spec')->where("goods_id='{$g->goods_id}' AND spec='{$g->spec}'")->row('stocks, price, promote_price');
						if ($spec) {
							$stocks = $spec->stocks;
							$price = $this->goods_mod->get_min_price(array($spec->price, $spec->promote_price));
						}
					}
					$where = "goods_id='{$g->goods_id}'";
					if (isset($g->spec) && strlen($g->spec)) $where .= " AND spec='{$g->spec}'";
					if ($this->member_id>0) {
						$where .= " AND (member_id='{$this->member_id}' OR session_id='{$this->session_id}')";
					} else {
						$where .= " AND session_id='{$this->session_id}'";
					}
					$row = SQL::share('cart')->where($where)->row('id, quantity');
					if ($row) {
						if (!$edit) {
							if (intval($g->quantity)<0 && $row->quantity-intval($g->quantity)<=0) {
								SQL::share('cart')->delete($row->id);
							} else {
								if ($row->quantity+$g->quantity > $stocks) error("该商品规格的库存只剩下{$stocks}件");
								SQL::share('cart')->where($row->id)->update(array('quantity'=>array('quantity', "+{$g->quantity}")));
							}
						} else {
							if (intval($g->quantity)<=0) {
								SQL::share('cart')->delete($row->id);
							} else {
								if ($g->quantity > $stocks) error("该商品规格的库存只剩下{$stocks}件");
								SQL::share('cart')->where($row->id)->update(array('quantity'=>$g->quantity));
							}
						}
					} else {
						if ($g->quantity > $stocks) error("该商品规格的库存只剩下{$stocks}件");
						if ($cart_id>0) {
							SQL::share('cart')->where($cart_id)->update(array('member_id'=>$this->member_id, 'spec'=>$g->spec, 'price'=>$price, 'quantity'=>$g->quantity));
						} else {
							SQL::share('cart')->insert(array('member_id'=>$this->member_id, 'session_id'=>$this->session_id, 'shop_id'=>$shop_id, 'ip'=>$this->ip,
								'reseller_id'=>$reseller_id, 'goods_id'=>$g->goods_id, 'spec'=>$g->spec, 'price'=>$price, 'quantity'=>$g->quantity, 'add_time'=>time()));
						}
					}
				}
			}
		}
		$this->total();
	}

	//从购物车删除
	public function delete() {
		$id = $this->request->post('cart_id', 0);
		if ($id<=0) error('数据错误');
		$where = "id='{$id}'";
		if ($this->member_id>0) {
			$where .= " AND (member_id='{$this->member_id}' OR session_id='{$this->session_id}')";
		} else {
			$where .= " AND session_id='{$this->session_id}'";
		}
		SQL::share('cart')->delete($where);
		$this->total();
	}

	//购物车首页
	//支持分单，如不需要分单按照默认即可(shop_id=0)
	public function index() {
		$this->_merge();
		$where = '';
		if ($this->member_id>0) {
			$where .= " AND (c.member_id='{$this->member_id}' OR session_id='{$this->session_id}')";
		} else {
			$where .= " AND session_id='{$this->session_id}'";
		}
		$count = SQL::share('cart c')->where($where)->value('COUNT(DISTINCT(shop_id))');
		$field = '';
		if ($count>1) {
			$field = 'c.shop_id';
		} else {
			$field = 'DISTINCT(c.shop_id)';
		}
		//$sql .= " ORDER BY id ASC";
		//exit($sql);
		$rs = SQL::share('cart c')->left('goods g', 'c.goods_id=g.id')->where("{$where} AND g.status=1")->find($field);
		if ($rs) {
			foreach ($rs as $k=>$s) {
				$row = SQL::share('shop')->where($s->shop_id)->row('name, avatar');
				if ($row) {
					$rs[$k]->shop_name = $row->name;
					$rs[$k]->shop_avatar = $row->avatar;
				}
				$goods = SQL::share('cart c')->left('goods g', 'c.goods_id=g.id')->where("{$where} AND c.shop_id='{$s->shop_id}' AND g.status='1'")
					->find("c.id, c.goods_id, c.spec, c.price as cart_price, c.quantity, c.reseller_id,
						g.id as goods_id, g.name, g.pic, g.stocks, g.price, g.promote_price, g.stock_alert_number, '' as spec_name");
				if ($goods) {
					foreach ($goods as $gk=>$g) {
						$goods[$gk]->cart_price = floatval($g->cart_price);
						if (in_array('grade', $this->function)) {
							if ($this->member_id) {
								$g->grade_price = floatval(SQL::share('goods_grade_price')
									->where("goods_id='{$g->goods_id}' AND grade_id='{$this->member_grade_id}'")->value('price'));
							} else {
								$g->grade_price = 0;
							}
							$g->price = $this->goods_mod->get_min_price(array($g->price, $g->grade_price));
						}
						$goods[$gk]->price = $this->goods_mod->get_min_price(array($g->price, $g->promote_price));
						$goods[$gk]->stocks = intval($g->stocks);
						if (strlen($g->spec)) {
							$spec = SQL::share('goods_spec')->where("goods_id='{$g->goods_id}' AND spec='{$g->spec}'")->row();
							if ($spec) {
								if ($spec->pic) $goods[$gk]->pic = $spec->pic;
								$goods[$gk]->price = $this->goods_mod->get_min_price(array($spec->price, $spec->promote_price));
								//获取最新库存
								$goods[$gk]->stocks = intval($spec->stocks);
								//获取规格名
								$spec_name = '';
								$p = array();
								$specs = explode(',', $g->spec);
								foreach ($specs as $s) {
									$row = SQL::share('goods_spec_category')->skipClient()->where($s)->row('name');
									if ($row) $p[] = $row->name;
								}
								if (count($p)) $spec_name = implode(';', $p);
								$goods[$gk]->spec_name = $spec_name;
							}
						}
					}
				}
				$rs[$k]->goods = $goods;
			}
		}
		$rs = add_domain_deep($rs, array('shop_avatar', 'pic'));
		success($rs);
	}

	//结算接口
	public function commit() {
		if ($this->member_id<=0) error('请登录', -100);
		//订单类型
		$type = $this->request->post('type', 0);
		//所属订单类型主id
		$parent_id = $this->request->post('parent_id', 0);
		//使用积分抵扣
		$integral_cash = $this->request->post('integral_cash', 0);
		//积分商城订单
		$integral_order = $this->request->post('integral_order', 0);
		//是否显示线下支付
		$offline = $this->configs['G_ORDER_PAYMOTHED_OFFLINE'];
		
		$shops = $this->_split_shops();
		$goods_ids = $this->_get_goods_ids($shops);
		$shops = add_domain_deep($shops, array('pic'));
		$total_price = $this->_get_total_price($shops);

		//获取默认地址
		$address_model = m('address');
		$address = $address_model->default_address();

		//获取用户的佣金、余额、积分
		$money = 0; //佣金和余额之和
		$integral = 0; //积分
		$member = SQL::share('member')->where($this->member_id)->row('money, commission, integral');
		if ($member) {
			$money = $member->money + $member->commission;
			$integral = $member->integral;
		}
		$coupons = $this->coupon_mod->coupons($this->member_id, $total_price, $goods_ids);
		if ($type>0) {
			$coupons = NULL;
		}

		//积分抵扣
		$integral_pay = null;
		if ($integral_cash) {
			if ($this->member_mod->order_integral_check($this->member_id, $total_price)) {
				$integral_pay = $this->member_mod->check_pay_with_integral($total_price);
			}
		}
		
		//计算总运费, 积分商城订单免运费
		$shipping_fee = 0;
		if (is_array($shops) && !$integral_order) {
			foreach ($shops as $s) {
				$shipping_fee += $s->shipping_fee;
			}
		}
		
		//商品总金额
		$goods_total_price = 0;
		if (is_array($shops)) {
			foreach ($shops as $s) {
				foreach ($s->goods as $g) {
					$goods_total_price += $g->goods_price;
				}
			}
		}

		success(compact('type', 'money', 'offline', 'total_price', 'address', 'shops', 'shipping_fee', 'coupons', 'goods_total_price',
			'integral_order', 'integral', 'integral_pay'));
	}

	//下单接口
	public function order($is_show=true) {
		//订单类型
		$type = $this->request->post('type', 0);
		//所属订单类型主id
		$parent_id = $this->request->post('parent_id', 0);
		//支付方式
		$pay_method = $this->request->post('pay_method', 'wxpay');
		$coupon_sn = $this->request->post('coupon_sn');
		//积分抵扣
		$integral_cash = $this->request->post('integral_cash', 0);
		//积分商城订单
		$integral_order = $this->request->post('integral_order', 0);
		//收货地址
		$province = $this->request->post('province');
		$city = $this->request->post('city');
		$district = $this->request->post('district');
		$address = $this->request->post('address');
		$mobile = $this->request->post('mobile');
		$name = $this->request->post('contactman');
		$zipcode = $this->request->post('zipcode');
		$idcard = $this->request->post('idcard');
		$memo = $this->request->post('memo');
		//发票
		$invoice_type = $this->request->post('invoice_type');
		$invoice_name = $this->request->post('invoice_name');
		$invoice_content = $this->request->post('invoice_content');
		//分销人id
		$reseller_id = $this->request->session('reseller_id', 0);

		//分单
		$shops = $this->_split_shops();
		$total_price = $this->_get_total_price($shops);
		
		if ($this->_is_entity_goods($shops)) {
			if (!$province || !$city || !$district || !$address || !$mobile || !$name) error('缺失收货地址信息');
		}
		
		if ($type>0) {
			$coupon_sn = '';
		}
		
		$status = 0;
		$pay_time = 0;

		//积分商城订单
		if ($integral_order) {
			$integral = SQL::share('member')->where($this->member_id)->value('integral');
			if ($integral<$total_price) error('您的积分不足以兑换');
		}

		//优惠券金额
		$coupon_money = 0;
		if (strlen($coupon_sn) && $total_price>0 && $type==0) {
			$goods_ids = $this->_get_goods_ids($shops);
			$coupons = $this->coupon_mod->coupons($this->member_id, $total_price, $goods_ids);
			$coupon_money = $this->coupon_mod->get_money($coupon_sn, $coupons, $shops);
			$total_price = $total_price - $coupon_money;
			if ($total_price<0) $total_price = 0;
		}

		//积分抵扣
		$integral_pay = null;
		$used_integral = 0; //使用积分抵现
		$integral = 0; //使用了多少积分
		if ($integral_cash && $total_price>0 && $type==0) {
			$integral_pay = $this->member_mod->check_pay_with_integral($total_price);
			$used_integral = $integral_pay->money;
			$integral = $integral_pay->integral;
			$total_price = $total_price - $used_integral;
		}
		
		//余额支付
		if ($pay_method=='yue') {
			if ($this->member_mod->is_yue_and_commission_enough($total_price)) {
				$status = 1;
				$pay_time = time();
			} else {
				error('您的佣金和余额不足以支付');
			}
		}

		if ($integral_order || $total_price==0) {
			$status = 1;
			$pay_time = time();
		}
		
		$order_id = 0;
		//订单类型
		$order_type = $type;
		//是否对用户显示
		$shown = 1;
		if ($type==1 || $type==3) $shown = 0;
		//客户端类型
		$client_type = 0;
		if ($this->is_mini) $client_type = 1;
		else if ($this->is_ios) $client_type = 2;
		else if ($this->is_android) $client_type = 3;
		//总订单SN，用来提交到支付宝，回调时提取出相关订单设置状态
		$parent_order_sn = generate_sn();
		if ($shops) {
			//获取优惠券对应的商品id
			$coupon_goods_id = 0;
			if ($coupon_sn && $total_price) {
				$coupon_goods_id = $this->coupon_mod->get_goods_id($coupon_sn, $coupons, $shops);
			}
			foreach ($shops as $k=>$shop) {
				$order_sn = generate_sn();
				$discount_price = 0; //折扣
				$couponsn = '';
				$used_coupon = 0;
				if ($coupon_goods_id) {
					foreach ($shop->goods as $j=>$g) {
						if ($g->id==$coupon_goods_id) {
							$couponsn = $coupon_sn;
							$used_coupon = $coupon_money;
							$discount_price = $shop->shop_price - $coupon_money;
							break;
						}
					}
				}
				//写入订单表
				$order_id = SQL::share('order')->insert(array('member_id'=>$this->member_id, 'province'=>$province, 'city'=>$city, 'district'=>$district,
					'address'=>$address, 'zipcode'=>$zipcode, 'name'=>$name, 'idcard'=>$idcard, 'mobile'=>$mobile, 'memo'=>$memo, 'reseller_id'=>$reseller_id,
					'total_price'=>$shop->shop_price, 'shipping_price'=>$shop->shipping_fee, 'discount_price'=>$discount_price, 'status'=>$status, 'add_time'=>time(),
					'pay_time'=>$pay_time, 'ip'=>$this->ip, 'order_sn'=>$order_sn, 'pay_order_sn'=>$order_sn, 'parent_order_sn'=>$parent_order_sn, 'is_mini'=>$this->is_mini,
					'shop_id'=>$shop->shop_id, 'pay_method'=>$pay_method, 'invoice_type'=>$invoice_type, 'invoice_name'=>$invoice_name, 'invoice_content'=>$invoice_content,
					'coupon_sn'=>$couponsn, 'used_coupon'=>$used_coupon, 'used_integral'=>$used_integral, 'integral'=>$integral, 'integral_order'=>$integral_order,
					'type'=>$type, 'parent_id'=>$parent_id, 'shown'=>$shown, 'client_type'=>$client_type));
				//用余额支付，扣减余额和财富
				if ($pay_method == 'yue') {
					$this->member_mod->pay_with_yue_and_commission($shop->shop_price, $order_id, 1);
				}
				//写入订单商品表
				foreach ($shop->goods as $j=>$g) {
					SQL::share('order_goods')->insert(array('shop_id'=>$shop->shop_id, 'member_id'=>$this->member_id, 'order_id'=>$order_id, 'goods_id'=>$g->id,
						'goods_name'=>addslashes($g->name), 'goods_pic'=>$g->pic, 'spec_linkage'=>$g->spec, 'spec'=>$g->spec_name,
						'price'=>$g->goods_price, 'single_price'=>$g->price, 'quantity'=>$g->quantity));
					//砍价订单需插入砍价详情
					if ($type==3) {
						$remain_price = round($g->price/2, 2);
						$goods = SQL::share('goods')->where($g->id)->row('chop_end_time, chop_time, chop_num');
						$chop_id = SQL::share('member_chop')->insert(array('order_id'=>$order_id, 'goods_id'=>$g->id, 'member_id'=>$this->member_id,
							'chop_end_time'=>$goods->chop_end_time, 'chop_time'=>$goods->chop_time, 'chop_num'=>$goods->chop_num, 'price'=>$g->goods_price,
							'remain_price'=>$remain_price, 'status'=>0, 'add_time'=>time()));
						$memo = '使用洪荒之力';
						SQL::share('member_chop_list')
							->insert(array('member_id'=>$this->member_id, 'parent_id'=>$chop_id, 'price'=>$g->price-$remain_price, 'memo'=>$memo, 'add_time'=>time()));
					}
				}
			}
			//调用订单支付成功接口,更新库存、销量等
			if ($pay_method=='yue') {
				$_GET = array('order_sn'=>$parent_order_sn);
				$order_type = $this->order_complete(false);
			}
			//使用优惠券
			if (strlen($coupon_sn)) $this->coupon_mod->using($coupon_sn, $this->member_id);
		}

		//减去会员抵扣的积分
		if ($integral_cash && $integral_pay) {
			SQL::share('member')->where($this->member_id)->update(array('integral'=>array('integral', "-{$integral_pay->integral}")));
			SQL::share('member_integral_history')->insert(array('member_id'=>$this->member_id, 'memo'=>'订单积分抵扣', 'integral'=>"-{$integral_pay->integral}",
				'add_time'=>time()));
		}

		//积分商城订单，减去会员对应积分
		if ($integral_order) {
			SQL::share('member')->where($this->member_id)->update(array('integral'=>array('integral', "-{$total_price}")));
			SQL::share('member_integral_history')->insert(array('member_id'=>$this->member_id, 'memo'=>'积分商城商品兑换', 'integral'=>"-{$total_price}",
				'add_time'=>time()));
		}
		
		$order_body = WEB_NAME.'-订单';
		$_SESSION['order_sn'] = $parent_order_sn;
		$_SESSION['total_price'] = $total_price;
		$_SESSION['order_body'] = $order_body;
		$_SESSION['pay_method'] = $pay_method;
		$_SESSION['pay_method_name'] = $this->order_mod->pay_method($pay_method, true);

		//20160316 by ajsong 需要同时删除购物车表里面对应的记录
		$goods = $this->request->post('goods', '', 'xg');
		if ($goods && !$integral_order) {
			$goods = json_decode($goods);
			if (is_array($goods) && count($goods)) {
				$where = '';
				if ($this->member_id>0) {
					$where .= " AND (member_id='{$this->member_id}' OR session_id='{$this->session_id}')";
				} else {
					$where .= " AND session_id='{$this->session_id}'";
				}
				foreach ($goods as $k=>$g) {
					SQL::share('cart')->delete("goods_id='{$g->goods_id}' AND spec='{$g->spec}' {$where}");
				}
			}
		}
		
		//如果有多个订单，order_id为最后一个
		if ($is_show) {
			$jsApiParameters = '';
			if ($this->is_app) {
				$type = '';
				switch ($pay_method) {
					case 'wxpay':case 'wxpay_h5':case 'wxpay_mini':$type = 'wxpay';break;
					case 'alipay':$type = 'alipay';break;
				}
				$api = p('pay', $type);
				$jsApiParameters = $api->pay($parent_order_sn, $total_price, $order_body);
			}
			success(array('order_id'=>$order_id, 'order_sn'=>$parent_order_sn, 'jsApiParameters'=>$jsApiParameters, 'pay_method'=>$pay_method, 'order_type'=>$order_type,
				'total_price'=>$integral_order?0:$total_price));
		}
	}

	//下单接口,微信端用,因为涉及到需要即时跳转去支付
	public function order_pay() {
		//判断是否提交过来，如果是，表示是新订单，清空订单SESSION
		if (IS_POST) {
			$_SESSION['order_sn'] = '';
			$_SESSION['total_price'] = '';
			$_SESSION['order_body'] = '';
			$_SESSION['pay_method'] = '';
			$_SESSION['pay_method_name'] = '';
		}
		$order_sn = $this->request->session('order_sn');
		$total_price = $this->request->session('total_price', 0, 'float');
		$order_body = $this->request->session('order_body');
		$pay_method = $this->request->session('pay_method', 'wxpay_h5');
		$pay_method_name = $this->request->session('pay_method_name');
		if ($order_sn=='' || $total_price<=0) {
			$this->order(false);
			$order_sn = $this->request->session('order_sn');
			$total_price = $this->request->session('total_price', 0, 'float');
			$order_body = $this->request->session('order_body');
			$pay_method = $this->request->session('pay_method', 'wxpay_h5');
			$pay_method_name = $this->request->session('pay_method_name');
		}
		if ($order_sn=='' || $total_price<=0) {
			error('信息错误！');
		}
		if (!strlen($order_body)) $order_body = WEB_NAME.'-订单';
		
		if ($this->is_wx || strpos($pay_method, 'wxpay')!==false) {
			$api = p('pay');
		} else {
			$api = p('pay', 'alipay');
		}
		$jsApiParameters = $api->pay($order_sn, $total_price, $order_body);
		if ($this->is_app) {
			if ($this->is_wap && $pay_method=='alipay') {
				//$jsApiParameters = urlencode($jsApiParameters);
				location(APP_SCHEME."://alipay?{$jsApiParameters}");
			} else {
				success($jsApiParameters);
			}
		}
		
		$order_type = intval(SQL::share('order')->where("order_sn='{$order_sn}'")->value('type'));
		success($jsApiParameters, '成功', 0, compact('order_sn', 'total_price', 'pay_method_name', 'order_type'));
	}

	//扫码支付
	public function native() {
		$order_sn = $this->request->get('order_sn');
		$url = $this->request->get('url');
		success(array('order_sn'=>$order_sn, 'url'=>$url));
	}

	//下单成功后，需要调用此接口，用来更新库存和销量，并发送消息推送给卖家
	public function order_complete($is_show=true) {
		$order_sn = $this->request->get('order_sn');
		$orders = NULL;
		if ($order_sn) {
			//减少库存，增加销量，发送通知
			$orders = SQL::share('order o')
				->where("o.status=1 AND (parent_order_sn='{$order_sn}' OR order_sn='{$order_sn}' OR pay_order_sn='{$order_sn}') AND handle_after_paid=0")->find('o.*');
			//更新订单处理后的标识，以后不再发送推送和更新销量。
			SQL::share('order')->where("parent_order_sn='{$order_sn}' OR order_sn='{$order_sn}' OR pay_order_sn='{$order_sn}'")
				->update(array('handle_after_paid'=>1));
			if ($orders) {
				$sn = $order_sn;
				$is_groupbuy = 0;
				$is_purchase = 0;
				$is_chop = 0;
				$order_ids = array();
				foreach ($orders as $k=>$o) {
					$sn = $o->order_sn;
					$order_ids[] = $o->id;
					//有邀请人的，需要写入返利情况
					if ($o->reseller_id>0) {
						$this->commission_mod->add($o->reseller_id, $o->id, 1);
					} else {
						$parent_id = intval(SQL::share('member')->where($o->member_id)->value('parent_id'));
						if ($parent_id>0) $this->commission_mod->add($parent_id, $o->id, 1);
					}
					//发送消息给用户
					if ($o->member_id>0) {
						$member = SQL::share('member')->where($o->member_id)->row('openid');
						if ($member) {
							$this->notification(array(
								'message'=>"您的订单{$o->order_sn}已支付成功，请耐心等待卖家发货。",
								'members'=>$o->member_id,
								'sms'=>"您的订单{$o->order_sn}已支付成功，请耐心等待卖家发货。",
								'template_id'=>221214
							));
							$weixin_mod = m('weixin');
							$weixin_mod->send_template($o->member_id, $o->id, 1);
						}
					}
					//发送消息给商户
					if ($o->shop_id>0) {
						$member_id = intval(SQL::share('shop')->where($o->shop_id)->value('member_id'));
						if ($member_id>0) {
							$this->notification(array(
								'message'=>"您有一个新订单",
								'members'=>$member_id,
								'sms'=>"您有一个新订单",
								'template_id'=>221214
							));
							$this->commission_mod->add($member_id, $o->id, 2); //增加金额到佣金表
						}
					}
					//订单类型，0普通，1拼团，2秒杀，3砍价
					switch ($o->type) {
						case 1: //拼团
							$is_groupbuy = 1;
							$goods = SQL::share('order_goods og')->left('goods g', 'goods_id=g.id')
								->where("order_id='{$o->id}'")->row('g.id, groupbuy_end_time, groupbuy_time, groupbuy_number');
							if ($goods) {
								SQL::share('member_groupbuy')->insert(array('order_id'=>$o->id, 'goods_id'=>$goods->id, 'member_id'=>$o->member_id,
									'parent_id'=>$o->parent_id, 'groupbuy_end_time'=>$goods->groupbuy_end_time, 'groupbuy_time'=>$goods->groupbuy_time, 'add_time'=>time()));
								if ($o->parent_id>0) {
									$count = SQL::share('member_groupbuy')->where("(id='{$o->parent_id}' OR parent_id='{$o->parent_id}') AND status=0")->count();
									if ($goods->groupbuy_number<=$count) { //拼团成功
										SQL::share('member_groupbuy')->where("id='{$o->parent_id}' OR parent_id='{$o->parent_id}'")->update(array('status'=>1));
										//增加已拼团数量
										SQL::share('goods')->where($goods->id)->update(array('groupbuy_count'=>array("+{$count}")));
										//把相关订单显示给用户
										$orders = SQL::share('member_groupbuy')->where("(id='{$o->parent_id}' OR parent_id='{$o->parent_id}') AND status=0")
											->returnArray()->find('order_id');
										SQL::share('order')->where("id IN (".implode(',',$orders).")")->update(array('shown'=>1));
									}
								}
							}
							break;
						case 2: //秒杀
							$is_purchase = 1;
							$goods = SQL::share('order_goods og')->left('goods g', 'goods_id=g.id')->where("order_id='{$o->id}'")->row('g.id');
							if ($goods) {
								//增加已秒杀数量
								SQL::share('goods')->where($goods->id)->update(array('purchase_count'=>array('+1')));
							}
							break;
						case 3: //砍价
							$is_chop = 1;
							$goods = SQL::share('order_goods og')->left('goods g', 'goods_id=g.id')->where("order_id='{$o->id}'")->row('g.id');
							if ($goods) {
								SQL::share('order')->where($o->id)->update(array('shown'=>1));
								//增加已砍价数量
								SQL::share('goods')->where($goods->id)->update(array('chop_count'=>array('+1')));
							}
							break;
					}
				}
				$order_ids = implode(',', $order_ids);
				if ($order_ids) {
					$order_goods = SQL::share('order_goods')->where("order_id IN ({$order_ids})")->find();
					foreach ($order_goods as $ok=>$og) {
						$goods = SQL::share('goods g')
							->left('goods_spec gs', 'gs.goods_id=g.id')
							->where("g.id='{$og->goods_id}' AND g.status=1 AND gs.spec='{$og->spec_linkage}'")
							->find('g.id as goods_id, g.stocks, gs.id as spec_id, gs.stocks as spec_stocks');
						if ($goods) {
							foreach ($goods as $k=>$g) {
								$status = 0;
								$stocks = $g->spec_stocks - $og->quantity;
								if ($stocks<=0) $stocks = 0;
								SQL::share('goods_spec')->where($g->spec_id)->update(array('stocks'=>$stocks));
								$stocks = 0;
								$specs = SQL::share('goods_spec')->where("goods_id='{$g->goods_id}'")->find('stocks');
								if ($specs) {
									foreach ($specs as $i=>$s) {
										$stocks += $s->stocks;
									}
								}
								if ($stocks>0) $status = 1;
								SQL::share('goods')->where($g->goods_id)->update(array('stocks'=>$stocks, 'status'=>$status, 'sales'=>array('sales', "+{$og->quantity}")));
								//同步所有下家的商品库存
								//SQL::share('goods')->where("parent_id='{$g->goods_id}'")->update(array('stocks'=>$stocks, 'status'=>$status));
							}
						}
					}
				}
				if ($is_groupbuy) return 1;
				if ($is_purchase) return 2;
				if ($is_chop) return 3;
			}
		}
		if ($is_show) success($orders);
		else return 0;
	}
	
	//检测是否有实体商品
	private function _is_entity_goods($shops) {
		if (!$shops) return false;
		foreach ($shops as $s) {
			foreach ($s->goods as $g) {
				$type = SQL::share('goods')->where($g->id)->value('type');
				if ($type==0 || $type==4) return true;
			}
		}
		return false;
	}

	//根据提交的产品数据来分单，返回分单后的店铺
	private function _split_shops() {
		$province = $this->request->post('province');
		$city = $this->request->post('city');
		$district = $this->request->post('district');
		$type = $this->request->post('type', 0); //空为自动判断,0为独立购买
		$goods = $this->request->post('goods', '', 'xg');
		$shop_ids = array();
		//积分商城订单
		$integral_order = $this->request->post('integral_order', 0);
		if (!$goods) error('请选择商品');
		$gs = json_decode($goods);
		if (!$gs) error('请选择商品');
		$goods = array();
		foreach ($gs as $k=>$g) {
			$row = SQL::share('goods g')->where("g.id='{$g->goods_id}' AND g.status=1")
				->row("g.id, g.name, g.pic, g.shop_id, g.integral, g.stocks, g.price, g.promote_price, g.market_price,
				g.free_shipping, g.shipping_fee, g.shipping_fee_id, g.weight, g.free_shipping_count,
				g.groupbuy_price, g.groupbuy_begin_time, g.groupbuy_end_time, g.groupbuy_free_shipping,
				g.purchase_price, g.purchase_begin_time, g.purchase_end_time, g.purchase_free_shipping,
				g.chop_price, g.chop_begin_time, g.chop_end_time, g.chop_free_shipping,
				0 as quantity, 0 as goods_price, '' as spec, '' as spec_name");
			if ($row) {
				$pic = $row->pic;
				$stocks = $row->stocks;
				$price = $row->price;
				if (in_array('grade', $this->function)) {
					if ($this->member_id) {
						$row->grade_price = floatval(SQL::share('goods_grade_price')
							->where("goods_id='{$g->goods_id}' AND grade_id='{$this->member_grade_id}'")->value('price'));
					} else {
						$row->grade_price = 0;
					}
					$price = $this->goods_mod->get_min_price(array($row->price, $row->grade_price));
				}
				$promote_price = $row->promote_price;
				$groupbuy_price = 0;
				$purchase_price = 0;
				$chop_price = 0;
				if ($type>0) {
					$groupbuy_price = $this->goods_mod->get_groupbuy_price($row->id);
					$purchase_price = $this->goods_mod->get_purchase_price($row->id);
					$chop_price = $this->goods_mod->get_chop_price($row->id);
				}
				if (isset($g->spec) && strlen($g->spec)) {
					$spec = SQL::share('goods_spec')->where("goods_id='{$g->goods_id}' AND spec='{$g->spec}'")->row();
					if ($spec) {
						if (strlen($spec->pic)) $pic = $spec->pic;
						$stocks = $spec->stocks;
						$price = $spec->price;
						if ($spec->promote_price>0) $promote_price = $spec->promote_price;
						if ($type>0) {
							if ($spec->groupbuy_price>0) $groupbuy_price = $this->goods_mod->get_groupbuy_price($row->id, $g->spec);
							if ($spec->purchase_price>0) $purchase_price = $this->goods_mod->get_purchase_price($row->id, $g->spec);
							if ($spec->chop_price>0) $chop_price = $this->goods_mod->get_chop_price($row->id, $g->spec);
						}
					}
				}
				if ($stocks<=0) continue;
				if (isset($g->spec) && strlen($g->spec)) {
					//获取规格名
					$spec_name = '';
					$p = array();
					$specs = explode(',', $g->spec);
					foreach ($specs as $s) {
						$r = SQL::share('goods_spec_category')->skipClient()->where($s)->find('name');
						if ($r) {
							foreach ($r as $rg) {
								$p[] = $rg->name;
							}
						}
					}
					if (count($p)) $spec_name = implode(';', $p);
					$row->spec_name = $spec_name;
				}
				$row->groupbuy_price = $groupbuy_price;
				$row->purchase_price = $purchase_price;
				$row->chop_price = $chop_price;
				$row->spec = (isset($g->spec) && strlen($g->spec)) ? $g->spec : '';
				$row->pic = $pic;
				$row->quantity = intval($g->quantity);
				$row->price = $this->goods_mod->get_min_price(array($price, $promote_price, $groupbuy_price, $purchase_price, $chop_price));
				$row->goods_price = ($integral_order ? intval($row->integral) : floatval($row->price)) * $row->quantity;
				$goods[] = $row;
				$shop_ids[] = $row->shop_id;
			}
		}
		//print_r($goods);
		if (!count($goods)) error('商品不存在或已下架');
		$shop_ids = implode(',', $shop_ids);
		$single_shop = false; //兼容单店铺的项目,清空店铺表里的所有记录或者goods表的shop_id设为0即自动转为单店铺
		$shops = SQL::share('shop')->where("id IN ({$shop_ids})")
			->find('id as shop_id, name as shop_name, avatar as shop_avatar, 0 as shop_price, 0 as shipping_fee, NULL as goods');
		if (!$shops) {
			$single_shop = true;
			$shop_obj = new stdClass();
			$shop_obj->shop_id = 0;
			$shop_obj->shop_price = 0;
			$shop_obj->shipping_fee = 0;
			$shop_obj->goods = NULL;
			$shops = array($shop_obj);
		}
		//计算每个店铺的商品价格
		foreach ($goods as $k=>$g) {
			foreach ($shops as $j=>$shop) {
				//兼容单店铺的项目
				if ($single_shop) {
					$shops[$j]->goods[] = $g;
					$shops[$j]->shop_price += $g->goods_price;
				} else {
					//同一个店铺内的
					if ($g->shop_id==$shop->shop_id) {
						$shops[$j]->goods[] = $g;
						$shops[$j]->shop_price += $g->goods_price;
						break;
					}
				}
			}
		}
		//计算每个店铺的运费
		if (!$province || !$city || !$district) {
			//获取默认地址
			$address_model = m('address');
			$address = $address_model->default_address();
			$province = $address->province;
			$city = $address->city;
			$district = $address->district;
		}
		$province = SQL::share('province')->where("name='{$province}'")->value('province_id');
		$city = SQL::share('city')->where("name='{$city}' AND parent_id='{$province}'")->value('city_id');
		$district = SQL::share('district')->where("name='{$district}' AND parent_id='{$city}'")->value('district_id');
		if ($district) {
			foreach ($shops as $k=>$shop) {
				$shops[$k]->shipping_fee = $integral_order ? 0 : $this->caculate_shipping_fee($shop->goods, $district); //积分商城订单免运费
				if ($shops[$k]->shipping_fee>0) {
					$shops[$k]->shop_price += $shops[$k]->shipping_fee;
				}
			}
		}
		$shops = add_domain_deep($shops, array('pic', 'shop_avatar'));
		return $shops;
	}
	
	//修改收货地址
	public function change_address() {
		$id = $this->request->post('id', 0);
		$goods = $this->request->post('goods', '', 'xg');
		//是否积分商城订单
		$integral_order = $this->request->post('integral_order', 0);
		if ($integral_order>0) success('0');
		if (!$goods) error('缺失商品');
		$origin_goods = json_decode($goods);
		if (!$origin_goods) error('缺失商品');
		$address = SQL::share('address')->where("member_id='{$this->member_id}' AND id='{$id}'")->row();
		if (!$address) error('缺失收货地址');
		$province = SQL::share('province')->where("name='{$address->province}'")->value('province_id');
		$city = SQL::share('city')->where("name='{$address->city}' AND parent_id='{$province}'")->value('city_id');
		$district = SQL::share('district')->where("name='{$address->district}' AND parent_id='{$city}'")->value('district_id');
		if (!$district) error('缺失区域数据');
		$goods = array();
		foreach ($origin_goods as $k=>$g) {
			$row = SQL::share('goods')->where($g->goods_id)
				->row('free_shipping, shipping_fee, shipping_fee_id, weight, free_shipping_count, 0 as quantity,
				groupbuy_price, groupbuy_free_shipping,
				purchase_price, purchase_free_shipping,
				chop_price, chop_free_shipping');
			$row->quantity = $g->quantity;
			$row->groupbuy_price = $this->goods_mod->get_groupbuy_price($g->goods_id);
			$row->purchase_price = $this->goods_mod->get_purchase_price($g->goods_id);
			$row->chop_price = $this->goods_mod->get_chop_price($g->goods_id);
			$goods[] = $row;
		}
		$shipping_fee = $this->caculate_shipping_fee($goods, $district);
		success($shipping_fee);
	}
	
	//计算运费，只计算商品里运费最高的
	public function caculate_shipping_fee($goods, $district) {
		$shipping_fee = 0;
		if ($goods) {
			foreach ($goods as $k=>$g) {
				if ($g->free_shipping==0) {
					if (($g->groupbuy_price>0 && $g->groupbuy_free_shipping==1) ||
						($g->purchase_price>0 && $g->purchase_free_shipping==1) ||
						($g->chop_price>0 && $g->chop_free_shipping==1) ||
						($g->free_shipping_count>0 && $g->free_shipping_count<=$g->quantity)) continue;
					if ($g->shipping_fee_id>0) {
						$shipping = SQL::share('shipping_fee')->where($g->shipping_fee_id)->row();
						$rs = SQL::share('shipping_fee_area')->where("shipping_fee_id='{$g->shipping_fee_id}' AND districts LIKE '%{$district}%'")->find();
						if ($rs) {
							foreach ($rs as $area) {
								$fee = floatval($area->first_price);
								if ($shipping->type==0) { //按重量
									$weight = ceil($g->weight) - $area->first;
									if ($weight>0 && $area->second>0) {
										$weight = $weight / $area->second;
										$weight = ceil($weight);
										$fee += $weight * $area->second_price;
									}
								} else { //按件数
									$quantity = ceil($g->quantity) - $area->first;
									if ($quantity>0 && $area->second>0) {
										$quantity = $quantity / $area->second;
										$quantity = ceil($quantity);
										$fee += $quantity * $area->second_price;
									}
								}
								if ($fee > $shipping_fee) {
									$shipping_fee = $fee;
								}
							}
						} else {
							//没有设置该地区的运费,使用默认运费
							if ($shipping) {
								$fee = floatval($shipping->first_price);
								if ($shipping->type==0) { //按重量
									$weight = ceil($g->weight) - $shipping->first;
									if ($weight>0 && $shipping->second>0) {
										$weight = $weight / $shipping->second;
										$weight = ceil($weight);
										$fee += $weight * $shipping->second_price;
									}
								} else { //按件数
									$quantity = ceil($g->quantity) - $shipping->first;
									if ($quantity>0 && $shipping->second>0) {
										$quantity = $quantity / $shipping->second;
										$quantity = ceil($quantity);
										$fee += $quantity * $shipping->second_price;
									}
								}
								if ($fee > $shipping_fee) {
									$shipping_fee = $fee;
								}
							}
						}
					} else {
						if ($g->shipping_fee > $shipping_fee) {
							$shipping_fee = $g->shipping_fee;
						}
					}
				}
			}
		}
		return $shipping_fee;
	}

	//获取订单总价
	private function _get_total_price($shops) {
		$shop_price = 0;
		if ($shops) {
			foreach ($shops as $k=>$shop) {
				$shop_price += $shop->shop_price;
			}
		}
		return $shop_price;
	}

	//判断该会员是否该店铺的批发会员
	public function is_reseller($shop_id) {
		return SQL::share('shop_seller')->where("shop_id='{$shop_id}' AND member_id='{$this->member_id}'")->count();
	}

	//检测订单的商品是否还可购买
	//该函数用在我的订单列表里未支付的订单进行支付时，判断商品库存是否足够、商品是否下架等
	public function check_order_goods() {
		$order_id = $this->request->get('order_id', 0);
		if ($this->member_id<=0) error('请登录', -100);
		if ($order_id<=0) error('数据错误');
		$goods = SQL::share('order_goods og')->inner('order o', 'og.order_id=o.id')->where("o.id='{$order_id}' AND o.member_id='{$this->member_id}'")
			->find('og.shop_id, og.goods_id, og.goods_name, g.stocks, g.status, g.id, og.quantity');
		if (!$goods) error('订单商品已经失效');
		foreach ($goods as $k => $g) {
			$goods_name = mb_substr($g->goods_name, 0, 6);
			if (!intval($g->id)) error("商品（{$goods_name}）已经失效");
			if (intval($g->stocks) <= 0) error("商品（{$goods_name}）已经下架");
			if (intval($g->status) == 0) error("商品（{$goods_name}）已经下架");
			if ($g->quantity > $g->stocks) error("商品（{$goods_name}）库存不足");
		}
		$row = SQL::share('order')->where($order_id)->row('id, order_sn, total_price, pay_method');
		success($row);
	}

	//根据shops数组来获取goods_id的数组
	private function _get_goods_ids($shops) {
		$ids = array();
		if ($shops) {
			foreach ($shops as $key => $shop) {
				if ($shop->goods) {
					foreach ($shop->goods as $j => $goods) {
						$ids[] = $goods->id;
					}
				}
			}
			$ids = array_unique($ids);
		}
		return $ids;
	}
}
