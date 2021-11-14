<?php
class order extends core {
	private $order_mod;
	private $commission_mod;

	public function __construct() {
		parent::__construct();
		$this->order_mod = m('order');
		$this->commission_mod = m('commission');
	}
	
	//我的订单列表，0未支付，1已支付，2已发货，3完成（已收货），4完成（已评价），-1取消，-2退款，-3退货
	public function index() {
		$keyword = $this->request->get('keyword');
		$status = $this->request->get('status');
		//积分商城订单
		$integral_order = $this->request->get('integral_order', 0);
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 10);
		$where = " AND o.member_id='{$this->member_id}'";
		if (strlen($status)) {
			if ($status=='-2,-3') {
				$where .= " AND (o.status IN ({$status}) OR o.ask_refund_time>0)";
			} else {
				$where .= " AND o.status IN ({$status})";
			}
		}
		if ($keyword) {
			$where .= " AND (o.order_sn LIKE '%{$keyword}%' OR o.name LIKE '%{$keyword}%' OR o.mobile LIKE '%{$keyword}%')";
		}
		$where .= " AND o.integral_order='{$integral_order}'";
		if ($integral_order) {
			$this->check_edition(3);
		}
		
		$orders = SQL::share('order o')->where($where)->sort('o.id DESC')->limit($offset, $pagesize)->find("o.*, '' as shop_name, '' as status_name");
		if ($orders) {
			foreach ($orders as $k=>$order) {
				$goods = SQL::share('order_goods og')
					->left('goods g', 'og.goods_id=g.id')
					->left('goods_spec gs', 'og.spec_linkage=gs.spec')
					->where("order_id='{$order->id}' AND gs.goods_id=og.goods_id")->find('og.*, g.integral, gs.stocks');
				if ($goods) {
					foreach ($goods as $gk=>$g) {
					
					}
				}
				$orders[$k]->goods = $goods;
				$orders[$k]->status_name = $this->order_mod->status_name($order->status);
			}
		}
		$orders = add_domain_deep($orders, array('goods_pic'));
		success($orders);
	}
	
	//订单详情
	public function detail($is_shop=false, $is_show=true) {
		$for_pay = $this->request->get('for_pay', 0);
		if ($is_show && $for_pay>0) $this->_detail_for_pay();
		
		$order_sn = $this->request->get('order_sn');
		$order_id = $this->request->get('id', 0);
		$where = " (o.order_sn='{$order_sn}' OR o.parent_order_sn='{$order_sn}')";
		if ($order_id>0) $where = " o.id='{$order_id}'";
		$order = SQL::share('order o')->left('shop s', 'o.shop_id=s.id')->where("{$where} AND o.member_id='{$this->member_id}'")
			->row("o.*, s.name as shop_name, s.member_id as shop_member_id, s.tel as shop_mobile, s.avatar as shop_avatar,
				'' as status_name, '' as auto_shouhuo, NULL as express");
		if ($order) {
			//更新订单已阅读
			SQL::share('order o')->where("{$where} AND o.member_id='{$this->member_id}'")->update(array('readed'=>1));
			$goods = SQL::share('order_goods og')
				->left('goods g', 'og.goods_id=g.id')
				->left('goods_spec gs', 'og.spec_linkage=gs.spec')
				->where("order_id='{$order->id}' AND gs.goods_id=og.goods_id")->find('og.*, g.integral, gs.stocks');
			if ($goods) {
				foreach ($goods as $gk=>$g) {
				
				}
			}
			$order->goods = $goods;
			$order->status_name = $this->order_mod->status_name($order->status);
			if ($order->status==2 && $order->shipping_time>0) {
				$shipping_time = $order->shipping_time;
				$G_AUTO_SHOUHUO_DAYS = intval($this->configs['G_AUTO_SHOUHUO_DAYS']) * 60*60*24;
				$date = $shipping_time + $G_AUTO_SHOUHUO_DAYS - $this->now;
				if ($date>0) $order->auto_shouhuo = date('还剩j天G时自动确认收货', $date);
			}
			if (strlen($order->shipping_number)) {
				$kuaidi = p('kuaidi');
				$express = $kuaidi->get($order->shipping_company, $order->shipping_number);
				//if (is_array($express)) $express = array_reverse($express);
				$order->express = $express;
			}
			//退货退款申请
			$g = SQL::share('order_refund')->where("order_id='{$order->id}'")->sort('id DESC')->row();
			if ($g) {
				$g->add_time = date('Y-m-d H:i:s', $g->add_time);
				$g->audit_time = date('Y-m-d H:i:s', $g->audit_time);
			}
			$order->refund = $g;
			$order->add_time_word = date('Y-m-d H:i:s', $order->add_time);
			$order->pay_time_word = date('Y-m-d H:i:s', $order->pay_time);
			$order->shipping_time_word = date('Y-m-d H:i:s', $order->shipping_time);
			$order->shouhuo_time_word = date('Y-m-d H:i:s', $order->shouhuo_time);
			$order->G_DELAY_SHOUHUO_DAYS = isset($this->configs['G_DELAY_SHOUHUO_DAYS']) ? intval($this->configs['G_DELAY_SHOUHUO_DAYS']) : 0;
			$order = add_domain_deep($order, array('goods_pic', 'shop_avatar'));
		}
		if ($is_show) success($order, '成功', 0, array('jsApiParameters'=>NULL));
		return $order;
	}
	
	//直接可支付的detail,微信端用
	private function _detail_for_pay() {
		$order_id = $this->request->get('id', 0);
		$order_sn = $this->request->get('order_sn');
		$total_price = 0;
		$order = NULL;
		if ($order_id>0 || strlen($order_sn)) {
			$order = $this->detail(false, false);
			if (intval($order->status)!=0) {
				success($order, '成功', 0, array('jsApiParameters'=>NULL));
			}
			$order_id = $order->id;
			$_SESSION['origin_order_sn'] = '';
			$_SESSION['order_sn'] = '';
			$_SESSION['total_price'] = '';
			$_SESSION['order_id'] = $order_id;
			
			//确保库存足够
			$goods = SQL::share('order_goods og')->left('order o', 'o.id=og.order_id')->where("o.id='{$order_id}'")->find('og.*');
			if ($goods) {
				foreach ($goods as $k => $g) {
					$row = SQL::share('goods g')->inner('goods_spec gs', 'g.id=gs.goods_id')
						->where("g.id='{$g->goods_id}' AND gs.spec='{$g->spec}'")->row("g.*, '' as spec, 0 as quantity");
					if ($row) {
						$name = mb_substr($row->name, 0, 6);
						if ($g->quantity > $row->stocks) {
							error("商品（{$name}）库存不足");
						}
					}
				}
			}
			
			//重新付款需要重写订单号,微信必须这么做
			$pay_order_sn = generate_sn();
			$order->pay_order_sn = $pay_order_sn;
			$total_price = $order->total_price;
			SQL::share('order')->where($order_id)->update(array('pay_order_sn'=>$pay_order_sn));
		} else {
			$order_id = $this->request->session('order_id', 0);
			if ($order_id<=0) error('订单信息错误！');
			$_GET = array('id'=>$order_id);
			$order = $this->detail(false, false);
			$total_price = $order->total_price;
		}
		
		$order_sn = $order->pay_order_sn;
		$order_body = WEB_NAME.'-订单';
		
		$pay = p('pay');
		$jsApiParameters = $pay->pay($order_sn, $total_price, $order_body, "&for_pay=1&id={$order_id}");
		
		success($order, '成功', 0, compact('jsApiParameters'));
	}
	
	//供APP调用支付
	public function pay() {
		if (!$this->is_app) error('该接口仅提供APP端使用');
		$order_sn = $this->request->get('order_sn');
		if (!strlen($order_sn)) error('缺少订单号');
		$order = SQL::share('order')->where("order_sn='{$order_sn}'")->row('total_price, pay_method, status');
		if (!$order) error('该订单不存在');
		if ($order->status>0) error('该订单已支付');
		$order_body = WEB_NAME.'-订单';
		$type = '';
		switch ($order->pay_method) {
			case 'wxpay':case 'wxpay_h5':case 'wxpay_mini':$type = 'wxpay';break;
			case 'alipay':$type = 'alipay';break;
		}
		$pay = p('pay', $type);
		$jsApiParameters = $pay->pay($order_sn, $order->total_price, $order_body);
		success($jsApiParameters);
	}
	
	//物流情况
	public function express() {
		$order_id = $this->request->get('id', 0);
		$type = $this->request->get('type', 'kd100');
		if ($order_id<=0) error('数据错误');
		$order = SQL::share('order')->where("id='{$order_id}' AND member_id='{$this->member_id}'")->row("*, '' as express");
		if ($order) {
			$goods = SQL::share('order_goods og')->left('goods g', 'og.goods_id=g.id')->where("order_id='{$order->id}'")->find('og.*, g.stocks, g.price as goods_price');
			$goods = add_domain_deep($goods, array("goods_pic"));
			$order->goods = $goods;
			$kuaidi = p('kuaidi', $type);
			$express = $kuaidi->get($order->shipping_company, $order->shipping_number);
			if (is_array($express)) $express = array_reverse($express);
			if (is_array($express)) {
				foreach ($express as $k=>$e) {
					$time = strtotime($e->time);
					$express[$k]->day = date('m-d', $time);
					$express[$k]->hour = date('H:i', $time);
				}
			} else {
				$e = new stdClass();
				$e->context = '已发货';
				$e->time = $order->add_time + 10*60*60;
				$e->day = date('m-d', $e->time);
				$e->hour = date('H:i', $e->time);
				$express = array($e);
			}
			$order->express = $express;
		}
		success($order);
	}
	
	//修改订单价格
	public function change_price() {
		$order_id = $this->request->post('id', 0);
		$price = $this->request->post('price', 0, 'float');
		if ($order_id) {
			$status = intval(SQL::share('order')->where("id='{$order_id}' AND shop_id='{$this->shop_id}'")->value('status'));
			if ($status!=0) error('只有未支付的订单才能修改价格');
			SQL::share('order')->where("id='{$order_id}' AND shop_id='{$this->shop_id}'")->update(array('total_price'=>$price));
		}
		success('ok');
	}
	
	//订单评论
	public function comment() {
		if (IS_POST) {
			$order_id = $this->request->post('id', 0);
			$order_goods_id = $this->request->post('order_goods_id', '', 'origin');
			$stars = $this->request->post('stars', '', 'origin');
			$content = $this->request->post('content', '', 'origin');
			$pic = $this->request->post('pic', '', 'origin');
			if (!is_array($order_goods_id)) error('数据错误');
			foreach ($order_goods_id as $k=>$og) {
				$goods_id = intval(SQL::share('order_goods')->where($og)->value('goods_id'));
				if ($goods_id) {
					SQL::share('order_goods')->where($og)->update(array('comment_stars'=>$stars[$k], 'comment_content'=>$content[$k], 'comment_pic'=>$pic[$k],
						'comment_time'=>time()));
					//增加商品评论数
					$comments = SQL::share('order_goods')->where("goods_id='{$goods_id}' AND comment_time>0")->count();
					SQL::share('goods')->where($goods_id)->update(array('comments'=>$comments));
					//一个订单只要评价过一个商品，即可认为该订单被评价过
					SQL::share('order')->where($order_id)->update(array('status'=>4));
				}
			}
			success('ok');
		}
		$order_id = $this->request->get('id', 0);
		$goods = SQL::share('order_goods og')->left('order o', 'o.id=og.order_id')
			->where("order_id='{$order_id}' AND o.member_id='{$this->member_id}'")->find('og.*');
		success(compact('order_id', 'goods'));
	}
	
	//取消订单
	public function cancel() {
		$order_id = $this->request->post('id', 0);
		$order = SQL::share('order')->where("id='{$order_id}' AND member_id='{$this->member_id}'")->row('shop_id, total_price, status');
		if (!$order) error('数据错误');
		//设置状态
		SQL::share('order')->where("id='{$order_id}' AND member_id='{$this->member_id}'")->update(array('status'=>-1, 'cancel_time'=>time()));
		if ($order->status>0) {
			/*
			//减少收入
			$total_price = floatval($order->total_price);
			if ($total_price) {
				$sql = "SELECT member_id FROM {$this->tbp}shop WHERE id='{$order->shop_id}'";
				$member_id = intval(SQL::share('')->where("")->value(''));
				$sql = "SELECT total_income FROM {$this->tbp}shop WHERE id='{$member_id}'";
				$total_income = floatval(SQL::share('')->where("")->value(''));
				if ($total_price >= $total_income) {
					$income = 0;
				} else {
					$income = $total_income - $total_price;
				}
				$sql = "UPDATE {$this->tbp}shop SET total_income={$income} WHERE id='{$order->shop_id}'";
				$this->db->query($sql);
				//echo $income;
				//$_SESSION['member']->total_income = $income;
			}
			*/
		}
		success('ok');
	}
	
	//20171213 by @jsong 自动收货、自动退款已交由 cron 统一处理
	
	//确认收货
	public function shouhuo() {
		$order_id = $this->request->post('id', 0);
		$this->order_mod->shouhuo($order_id);
		success('ok');
	}
	
	//延迟发货
	public function delay_shouhuo() {
		$order_id = $this->request->post('id', 0);
		SQL::share('order')->where("id='{$order_id}' AND member_id='{$this->member_id}'")->update(array('delay_shouhuo_time'=>time()));
		success('ok');
	}
	
	//提醒发货
	public function ask_shipping() {
		$order_id = $this->request->post('id', 0);
		SQL::share('order')->where("id='{$order_id}' AND member_id='{$this->member_id}'")->update(array('ask_shipping_time'=>time()));
		success('ok');
	}
	
	//获取快递公司列表
	public function shipping_company($is_show=true) {
		$other = o('other');
		return $other->shipping_company($is_show);
	}
	
	//退款退货申请
	public function refund() {
		$order_id = $this->request->get('id', 0);
		if (IS_POST) {
			$order_id = $this->request->post('id', 0);
			$type = $this->request->post('type', 0); //1:退款, 2:退货
			//$price = $this->request->post('price', 0, 'float');
			$reason = $this->request->post('reason');
			$memo = $this->request->post('memo');
			$pics = $this->request->post('pics', '', 'xg');
			if (!$order_id) error('无效的订单号');
			
			$order = SQL::share('order')->where("id='{$order_id}' AND member_id='{$this->member_id}' AND status>0")->row('order_sn');
			if (!$order) error('订单不存在!');
			
			$order = SQL::share('order')->where($order_id)->row('member_id, mobile, total_price, pay_method, parent_order_sn, trade_no, status');
			$out_refund_no = generate_sn();
			
			$pic1 = $pic2 = $pic3 = '';
			if (strlen($pics)) {
				$pics = json_decode($pics);
				for ($i=0; $i<count($pics); $i++) {
					if ($i==0) $pic1 = $pics[$i];
					else if ($i==1) $pic2 = $pics[$i];
					else if ($i==2) $pic3 = $pics[$i];
				}
			}
			
			SQL::share('order_refund')->insert(array('order_id'=>$order_id, 'member_id'=>$order->member_id, 'pay_method'=>$order->pay_method,
				'out_refund_no'=>$out_refund_no, 'type'=>$type, 'price'=>$order->total_price, 'reason'=>$reason, 'memo'=>$memo, 'pic1'=>$pic1, 'pic2'=>$pic2, 'pic3'=>$pic3,
				'status'=>0, 'add_time'=>time()));
			
			SQL::share('order')->where("id='{$order_id}' AND member_id='{$this->member_id}'")->update(array('ask_refund_time'=>time()));
			
			$str = '退款';
			if ($type==2) $str = '退货';
			success('ok', "{$str}申请已提交，请耐心等待商家处理");
		}
		
		$row = SQL::share('order')->where($order_id)->row('id, total_price');
		success($row);
	}
	
	//第三方退款
	public function _refund($order, $out_refund_no, $is_show=true) {
		$pay_method = $order->pay_method;
		switch ($pay_method) {
			case 'wxpay':
			case 'wxpay_h5':
			case 'wxpay_mini':
				if ($order->is_mini) $pay_method = 'wxpay_mini';
				$refund = p('pay');
				$refund->refund($order->order_sn, $order->total_price, $out_refund_no, $pay_method, $order->trade_no);
				break;
			case 'alipay':
				$refund = p('pay', 'alipay');
				$refund->refund($order->order_sn, $order->total_price, $out_refund_no, $pay_method, $order->trade_no);
				break;
			case 'yue':
				SQL::share('member')->where($order->member_id)->update(array('money'=>array("+{$order->total_price}")));
				break;
			default:
				error('不支持的付款方式');
				break;
		}
	}
	
	//退货退款审核，1:退款, 2:退货
	public function order_check_return_goods($order_id=0, $reason='', $status=1, $is_show=true) {
		if ($order_id<=0) {
			$order_id = $this->request->post('id', 0);
			$reason = $this->request->post('reason');
			$status = $this->request->post('status', 0);
		}
		if ($order_id) {
			//判断是退款还是退货
			$type = SQL::share('order_refund')->where("order_id='{$order_id}'")->sort('id DESC')->value('type');
			$order = SQL::share('order o')->left('shop s', 'factory_shop_id=s.id')->where("o.id='{$order_id}'")->row('o.*, s.member_id as shop_member_id');
			if ($status==1) {
				$out_refund_no = SQL::share('order_refund')->where("order_id='{$order_id}'")->sort('id DESC')->row('out_refund_no');
				$this->_refund($order, $out_refund_no, $is_show);
				if ($order->status>2) { //已完成的订单需要扣除店铺佣金
					$this->commission_mod->cancel($order->id, 1);
				}
				if ($type==1) {
					SQL::share('order')->where($order_id)->update(array('status'=>-2));
					$reason = "已退款";
				} else {
					SQL::share('order')->where($order_id)->update(array('status'=>-3));
					$shop_id = SQL::share('order')->where($order_id)->value('factory_shop_id');
					$g = SQL::share('shop')->where($shop_id)->row('return_province, return_city, return_district, return_address, return_name, return_mobile');
					$reason = "退货地址：{$g->return_province}{$g->return_city}{$g->return_district}{$g->return_address}，联系人：{$g->return_name}，电话：{$g->return_mobile}";
				}
				//扣减退货退款后店铺的佣金
				$this->_reduce_order_commission($order_id);
			}
			SQL::share('order_refund')->where("order_id='{$order_id}'")->update(array('status'=>$status, 'audit_memo'=>$reason, 'audit_time'=>time()));
			SQL::share('order')->where($order_id)->update(array('ask_refund_time'=>0));
			
			//发送短信、推送
			$member_id = -1;
			$notify = '';
			$sms = array();
			$template_id = 0;
			if ($status==1) {
				if ($type==1) {
					$member_id = $order->member_id;
					$notify = "您的订单{$order->order_sn}退款成功，退款金额为{$order->total_price}元";
					$sms = array($order->order_sn, "{$order->total_price}元");
					$template_id = 221218;
				} else {
					$member_id = $order->member_id;
					$notify = "您的订单{$order->order_sn}退货成功";
					$sms = array($order->order_sn);
					$template_id = 221222;
				}
			} else {
				if ($type==1) {
					$member_id = $order->member_id;
					$notify = "您申请的退款请求失败，请联系卖家";
					$template_id = 221225;
				} else {
					$member_id = $order->member_id;
					$notify = "您申请的退货请求失败，请联系卖家";
					$template_id = 221226;
				}
			}
			$this->notification(array(
				'message'=>$notify,
				'members'=>$member_id,
				'sms'=>$sms,
				'template_id'=>$template_id
			));
			
			if ($status==1 && $type==1) {
				switch ($order->pay_method) {
					case 'wxpay':
					case 'wxpay_h5':
					case 'wxpay_mini':
					case 'alipay':
						if ($is_show) success('订单已退款，请及时检查您的第三方支付平台的账户，若未收到款项请尽快联系我们');
						break;
					case 'yue':
						if ($order->member_id == $this->member_id) {
							if ($is_show) success('订单已退款，请在会员中心查看您的余额是否已到账');
						}
						break;
				}
			}
		}
		if ($is_show) success('ok');
	}
	
	//退货退款成功后，扣减店铺的佣金
	public function _reduce_order_commission($order_id) {
		$rs = SQL::share('member_commission')->where("parent_id='{$order_id}' AND type=1 AND status=1")->find();
		if ($rs) {
			foreach ($rs as $k => $g) {
				$money = $g->commission;
				if ($money) {
					//处理店铺佣金
					$order = SQL::share('order')->where($g->parent_id)->row('shop_id');
					if ($order) {
						$shop = SQL::share('shop')->where($order->shop_id)->row('total_income, can_withdraw_money, member_id');
						if ($shop) {
							if ($shop->total_income>=$money) {
								$shop->total_income = $shop->total_income - $money;
							} else {
								$shop->total_income = 0;
							}
							if ($shop->can_withdraw_money>=$money) {
								$shop->can_withdraw_money = $shop->can_withdraw_money - $money;
							} else {
								$shop->can_withdraw_money = 0;
							}
							SQL::share('shop')->where("$order->shop_id")
								->update(array('total_income'=>$shop->total_income, 'can_withdraw_money'=>$shop->can_withdraw_money));
							//处理会员佣金
							$member = SQL::share('member')->where($shop->member_id)->row('commission');
							if ($member) {
								if ($member->commission>=$money) {
									$member->commission = $member->commission - $money;
								} else {
									$member->commission = 0;
								}
								SQL::share('member')->where($shop->member_id)->update(array('commission'=>$member->commission));
							}
						}
					}
				}
			}
		}
		SQL::share('member_commission')->where("parent_id='{$order_id}' AND type=1")->update(array('status'=>-1));
	}
}
