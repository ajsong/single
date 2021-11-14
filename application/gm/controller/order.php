<?php
class order extends core {
	private $order_mod;
	private $commission_mod;

	public function __construct() {
		parent::__construct();
		$this->order_mod = m('order');
		$this->commission_mod = m('commission');
	}

	//订单列表
	public function index() {
		$where = 'o.shown=1';
		$export = $this->request->get('export', 0);
		$type = $this->request->get('type');
		$status = $this->request->get('status');
		$keyword = $this->request->get('keyword');
		$order_id = $this->request->get('order_id');
		$shop_id = $this->request->get('shop_id', 0);
		$pay_method = $this->request->get('pay_method');
		$integral_order = $this->request->get('integral_order', 0);

		if ($keyword) {
			$where .= " AND (order_sn LIKE '%{$keyword}%' OR o.name LIKE '%{$keyword}%' OR o.mobile LIKE '%{$keyword}%'
				OR o.address LIKE '%{$keyword}%' )";
		}
		if (strlen($type)) {
			$where .= " AND o.type='{$type}'";
		}
		if (strlen($status)) {
			$where .= " AND o.status IN ({$status})";
		}
		if (strlen($order_id)) {
			$where .= " AND o.id='{$order_id}'";
		}
		if ($shop_id) {
			$where .= " AND o.shop_id='{$shop_id}'";
		}
		if (strlen($pay_method)) {
			$where .= " AND o.pay_method='{$pay_method}'";
		}
		$where .= " AND o.integral_order='{$integral_order}'";
		$rs = SQL::share('order o')
			->left('shop s', 'o.shop_id=s.id')
			->left('member m', 'o.member_id=m.id')
			->where($where)->isezr()->setpages(compact('type', 'status', 'keyword', 'order_id', 'shop_id', 'pay_method', 'integral_order'))
			->sort('o.id DESC')->find("o.*, s.name as shop_name, m.name as member_name, '' as status_name");
		$sharepage = SQL::share()->page;
		$wherebase64 = SQL::share()->wherebase64;
		if ($rs) {
			foreach ($rs as $key => $g) {
				$rs[$key]->goods = SQL::share('order_goods')->where("order_id='{$g->id}'")->find('goods_id, goods_name, quantity, price, goods_pic');
				$rs[$key]->status_name = $this->order_mod->status_name($g->status);
				if ($g->add_time) $rs[$key]->add_time = date("Y-m-d H:i:s", $g->add_time);
				if ($g->pay_time) $rs[$key]->pay_time = date("Y-m-d H:i:s", $g->pay_time);
			}
		}
		if ($export>0) {
			set_time_limit(0);
			export_excel($rs, array(
				'id'=>'ID',
				'order_sn'=>'订单号',
				'member_name'=>'下单人',
				'shop_name'=>'门店',
				'name'=>'收货人',
				'mobile'=>'电话',
				'province'=>'省',
				'city'=>'市',
				'district'=>'区',
				'address'=>'地址',
				'add_time'=>'下单时间',
				'pay_time'=>'支付时间',
				'total_price'=>'订单总价',
				'pay_method'=>'支付方式',
				'status_name'=>'订单状态'
			));
			exit;
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$shops = SQL::share('shop')->sort('id ASC')->find('id, name');
		$this->smarty->assign('shops', $shops);
		$this->smarty->assign('where', $wherebase64);
		$this->display();
	}

	//详情
	public function edit() {
		if (IS_POST) {
			$shipping_time = '';
			$shouhuo_time = '';
			$id = $this->request->post('id', 0);
			$total_price = $this->request->post('total_price', 0, 'float');
			$address = $this->request->post('address');
			$shipping_company = $this->request->post('shipping_company');
			$shipping_number = $this->request->post('shipping_number');
			$shipping_price = $this->request->post('shipping_price');
			$print = $this->request->post('print', 0);
			$status = $this->request->post('status', -1);
			$oldstatus = $this->request->post('oldstatus', -1);
			$readed = $this->request->post('readed', 0);
			$price = $this->request->post('price', '', 'origin');
			$goods_id = $this->request->post('goods');
			//获取发货时间，用来判断之前是否已经发货
			$shipping = SQL::share('order')->where($id)->row('member_id, shipping_time, order_sn');
			//更改是否阅读
			if ($status>=0 && $status<4 && $status!==$oldstatus) {
				$readed = 1;
			}
			$data = compact('address', 'total_price', 'status', 'shipping_company', 'shipping_number', 'shipping_price', 'readed');
			//发货时间
			if ($status==2) {
				$data['shipping_time'] = time();
			}
			if ($status==3) {
				$data['shouhuo_time'] = time();
			}
			//更新订单信息
			SQL::share('order')->where($id)->update($data);
			if (is_array($price)) {
				foreach ($price as $i=>$v) {
					foreach ($goods_id as $k=>$g) {
						if ($i==$k) {
							SQL::share('order_goods')->where("order_id='{$id}' AND id='{$g}'")->update(array('price'=>$v));
						}
					}
				}
			}
			//订单佣金转账到会员的佣金
			if ($status>=3 && $id>0) {
				// print_r($status);exit;
				$this->commission_mod->turn_commission($id);
			}
			//退回佣金
			if ($status<0) {
				$this->commission_mod->cancel($id);
			}
			
			//发货状态发送物流消息通知
			if ($shipping->shipping_time==0 && $shipping_company && $shipping_number) {
				$this->send_message("您的订单{$shipping->order_sn}已经发货，物流信息请从订单详情查看。", $shipping->member_id, 'logistics');
				$order = SQL::share('order')->where($id)->row('member_id');
				$weixin_mod = m('weixin');
				$weixin_mod->send_template($order->member_id, $id, 2);
			}
			
			if ($print>0) {
				header("Location:?app=order&act=print_order&order_id={$id}");
			} else {
				header("Location:?app=order&act=index");
			}
		} else {
			$id = $this->request->get('id', 0);
			$row = SQL::share('order o')->left('member m', 'o.member_id=m.id')->where("o.id='{$id}'")->row('o.*, m.name as member_name, m.id as member_id');
			if (!$row) error('订单不存在');
			if ($row->coupon_sn!='') $row->coupon_id = intval(SQL::share('coupon_sn')->where("sn='{$row->coupon_sn}'")->value('coupon_id'));
			$this->smarty->assign('row',$row);
			$goods = SQL::share('order_goods og')->left('shop s', 'og.shop_id=s.id')->where("og.order_id='{$id}'")->find('og.*, s.name as shop_name');
			if ($goods) {
				foreach ($goods as $k=>$g) {
					if (is_numeric($g->spec)) {
						$goods[$k]->spec = SQL::share('goods_spec')->where($g->spec)->value('spec');
					}
				}
			}
			$this->smarty->assign('goods',$goods);
			$this->display();
		}
	}

	//删除
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('order')->delete($id);
		header("Location:?app=order&act=index");
	}
	//发货单
	public function print_order(){
		$id = $this->request->get('order_id', 0);
		$row = SQL::share('order o')->left('member m', 'o.member_id=m.id')->where("o.id='{$id}'")->sort('o.id DESC')->row('o.*, m.name as member_name');
		$goods = SQL::share('order_goods og')->left('shop s', 'og.shop_id=s.id')->where("og.order_id='{$id}'")->find('og.*, s.name as shop_name');
		$total = 0;
		$i = 0;
		foreach ($goods as  $value) {
			$i =$value->sort= $i + 1;
			$total = $value->quantity +$total;
		}
		$this->smarty->assign('total',$total);
		$this->smarty->assign('goods',$goods);
		$this->smarty->assign('row',$row);
		$this->display();
	}
	
	//退货退款订单
	public function return_goods(){
		$where = '';
		$id = $this->request->get('id', 0);
		$keyword = $this->request->get('keyword');
		if ($keyword) {
			$where .= "AND (m.name LIKE '%{$keyword}%' OR m.mobile LIKE '%{$keyword}%')";
		}
		if ($id) {
			$where .= "AND r.id='{$id}'";
		}
		$rs = SQL::share('order_refund r')
			->left('order o', 'r.order_id=o.id')
			->left('member m', 'r.member_id=m.id')
			->where($where)->isezr()->setpages(compact('id', 'keyword'))
			->sort('r.id DESC, r.status ASC')->find('r.*, o.order_sn, m.name, m.nick_name, m.mobile');
		$sharepage = SQL::share()->page;
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$name = $g->nick_name;
				if (!strlen($name)) $name = $g->name;
				if (!strlen($name)) $name = $g->mobile;
				$rs[$k]->name = $name;
			}
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
	
	public function return_edit(){
		$id = $this->request->get('id', 0);
		$row = SQL::share('order_refund r')
			->left('order o', 'r.order_id=o.id')
			->left('member m', 'r.member_id=m.id')
			->where("r.id={$id}")->row('r.*, o.order_sn, m.name, m.nick_name, m.mobile');
		if ($row) {
			$name = $row->nick_name;
			if (!strlen($name)) $name = $row->name;
			if (!strlen($name)) $name = $row->mobile;
			$row->name = $name;
		}
		$this->smarty->assign('row',$row);
		$this->display();
	}
	
	//删除
	public function return_goods_delete() {
		$id = $this->request->get('id', 0);
		$order_id = $this->request->get('order_id', 0);
		SQL::share('order_refund')->delete("id='{$id}' OR order_id='{$order_id}'");
		header("Location:?app=order&act=return_goods");
	}

	//第三方退款
	private function _refund($order, $refund) {
		if ($order->pay_method=='wxpay' || $order->pay_method=='wxpay_h5' || $order->pay_method=='wxpay_mini') {
			$pay_method = $order->pay_method;
			if ($order->is_mini) $pay_method = 'wxpay_mini';
			$pay = p('pay');
			$pay->refund($order->order_sn, $order->total_price, $refund->out_refund_no, $pay_method, $order->trade_no);
		} else if ($order->pay_method=='alipay') {
			$pay = p('pay', 'alipay');
			$pay->refund($order->order_sn, $order->total_price, $refund->out_refund_no, $order->pay_method, $order->trade_no);
		} else if ($order->pay_method=='yue') {
			SQL::share('member')->where($order->member_id)->update(array('money'=>array("+{$order->total_price}")));
			SQL::share('order_refund')->where($refund->id)->update(array('status'=>1));
			SQL::share('order')->where($order->id)->update(array('status'=>-2));
		}
	}
	//退货退款审核，1:退款, 2:退货
	public function order_check_return_goods($order_id=0, $reason='', $status=1) {
		if (!$order_id) {
			$order_id = $this->request->post('order_id', 0);
			$reason = $this->request->post('audit_memo');
			$status = $this->request->post('status', 0);
		}
		if ($order_id) {
			//判断是退款还是退货
			$type = SQL::share('order_refund')->where("order_id='{$order_id}'")->sort('id DESC')->value('type');
			$order = SQL::share('order')->where($order_id)->row();

			if ($status==1) {
				$refund = SQL::share('order_refund')->where("order_id='{$order_id}'")->sort('id DESC')->row('id, out_refund_no');
				$this->_refund($order, $refund);
				if ($type==1) {
					if (!strlen($reason)) $reason = "已退款";
				} else {
					SQL::share('order')->where($order_id)->update(array('status'=>-3));
					$shop_id = SQL::share('order')->where($order_id)->value('shop_id');
					if ($shop_id) {
						$row = SQL::share('shop')->where($shop_id)->row('return_province, return_city, return_district, return_address, return_name, return_mobile');
						if (!strlen($reason)) $reason = "退货地址：{$row->return_province}{$row->return_city}{$row->return_district}{$row->return_address}，联系人：{$row->return_name}，电话：{$row->return_mobile}";
					} else {
						if (!strlen($reason)) $reason = '退货地址：'.$this->configs['G_ORDER_REFUND_INFO'];
					}
				}
				//扣减退货退款后会员的佣金
				$this->_reduce_order_commission($order_id);
			}
			SQL::share('order_refund')->where("order_id='{$order_id}'")->update(array('status'=>$status, 'audit_memo'=>$reason, 'audit_time'=>time()));

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
						//header('Location:?app=order&act=return_goods');
						break;
					case 'yue':
						//header('Location:?app=order&act=return_goods');
						//success('订单已退款，请在会员中心查看您的余额是否已到账');
						break;
				}
			}
		}
		header('Location:?app=order&act=return_goods');
	}

	//退货退款成功后，扣减会员的佣金
	private function _reduce_order_commission($order_id) {
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
							SQL::share('shop')->where($order->shop_id)->update(array('total_income'=>$shop->total_income, 'can_withdraw_money'=>$shop->can_withdraw_money));
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
