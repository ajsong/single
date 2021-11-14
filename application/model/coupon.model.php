<?php
class coupon_model extends base_model {
	private $goods_mod;

	public function __construct() {
		parent::__construct();
		$this->goods_mod = m('goods');
	}

	//发放优惠券，1发放成功，0发放失败，-1已领取，-2优惠券不存在
	public function send($coupon_id, $member_id) {
		$now = time();
		$coupon = SQL::share('coupon')->where("id='{$coupon_id}' AND status=1 AND begin_time<='{$now}' AND (end_time>='{$now}' OR end_time=0 OR handy_time>0)")
			->row();
		if ($coupon) {
			if ($coupon->num_per_person==0 || $coupon->num_per_person>$this->got($coupon_id, $member_id)) { //每人限领
				if ($coupon->auto_add==1) { //自动增加
					$status = $coupon->status;
					if ($status==0) $status = 1;
					$sn = date('m') . date('d') . substr(time(), -3) . substr(microtime(), 2, 6) . rand(100, 999);
					$success = SQL::share('coupon_sn')->insert(array('member_id'=>$member_id, 'coupon_id'=>$coupon->id, 'coupon_money'=>$coupon->coupon_money, 'sn'=>$sn,
						'add_time'=>$now, 'get_time'=>$now, 'times'=>$coupon->times, 'status'=>$status));
				} else { //由已发行的没领取的优惠券内更新
					$success = SQL::share('coupon_sn')->where("status=1 AND get_time=0 AND member_id=0 AND coupon_id={$coupon_id}")->sort('id ASC')->pagesize(1)
						->update(array('member_id'=>$member_id, 'get_time'=>$now));
				}
				if ($success) return 1;
				else return 0;
			}
			return -1;
		}
		return -2;
	}

	//是否已经获得了此优惠券
	public function got($coupon_id, $member_id) {
		$got = SQL::share('coupon_sn')->where("coupon_id='{$coupon_id}' AND member_id='{$member_id}' AND member_id>0")->count();
		return $got;
	}
	
	//获取优惠券内容
	public function get_coupon_info($coupon) {
		if ($coupon) {
			$coupon->coupon_money = floatval($coupon->coupon_money);
			if (intval($coupon->min_price)) {
				$min_price = floatval($coupon->min_price);
				$coupon->min_price_memo = "满{$min_price}元可使用";
			} else {
				$coupon->min_price_memo = "无门槛";
			}
			if ($coupon->permit_goods) {
				$coupon->memo = "(部分商品可用)";
			} else {
				$coupon->memo = "(全场通用)";
			}
			$begin_time = $coupon->begin_time;
			$end_time = $coupon->end_time;
			if ($end_time>0) {
				$coupon->end_time = date('Y-m-d', $coupon->end_time);
			} else {
				$coupon->end_time = '长期有效';
			}
			if ($begin_time>0) {
				$coupon->begin_time = date('Y-m-d', $coupon->begin_time);
				$coupon->time_memo = $coupon->begin_time.' ~ '.$coupon->end_time;
			} else {
				$coupon->time_memo = '有效期至 '.$coupon->end_time;
			}
			$coupon = add_domain_deep($coupon, array('pic'));
		}
		return $coupon;
	}

	//获取优惠券对应的优惠金额
	public function get_money($sn, $coupons, $shops) {
		if ($coupons) {
			foreach ($coupons as $key => $coupon) {
				if ($sn==$coupon->sn) {
					$money = 0;
					if ($coupon->coupon_money<0 && $coupon->coupon_discount<0) { //抵全额
						if (is_array($coupon->goods) && is_array($shops)) {
							foreach ($coupon->goods as $goods_id) { //查找出对应上的商品的goods_price
								foreach ($shops as $s) {
									if (is_array($s->goods)) {
										foreach ($s->goods as $g) {
											$goods_price = floatval($g->goods_price);
											if (intval($g->id)==$goods_id) return $goods_price;
										}
									}
								}
							}
						}
					} else if ($coupon->coupon_money>0) { //定额
						$money = $coupon->coupon_money;
					} else if ($coupon->coupon_discount>0) { //折扣
						if (is_array($coupon->goods) && is_array($shops)) {
							foreach ($coupon->goods as $goods_id) {
								foreach ($shops as $s) {
									if (is_array($s->goods)) {
										foreach ($s->goods as $g) {
											$goods_price = floatval($g->goods_price);
											if (intval($g->id)==$goods_id) $money += $goods_price - ($coupon->coupon_discount/10*$goods_price);
										}
									}
								}
							}
						}
					}
					$money = floatval($money);
					$money = round($money, 2);
					return $money;
				}
			}
		}
		return 0;
	}

	//获取优惠券对应的商品id
	public function get_goods_id($sn, $coupons, $shops) {
		if ($coupons) {
			foreach ($coupons as $key => $coupon) {
				if ($sn==$coupon->sn) {
					if (is_array($coupon->goods) && is_array($shops)) {
						foreach ($coupon->goods as $goods_id) {
							foreach ($shops as $s) {
								if (is_array($s->goods)) {
									foreach ($s->goods as $g) {
										if (intval($g->id)==$goods_id) return intval($goods_id);
									}
								}
							}
						}
					}
					return 0;
				}
			}
		}
		return 0;
	}

	//使用优惠券
	public function using($sn, $member_id) {
		if (!strlen($sn)) return;
		$row = SQL::share('coupon_sn')->where("sn='{$sn}' AND member_id='{$member_id}' AND member_id>0")->row();
		$times = intval($row->times);
		$status = intval($row->status);
		if ($times>0) {
			$times--;
			if ($times==0) $status = -1;
		}
		$now = time();
		SQL::share('coupon_sn')->where("sn='{$sn}' AND member_id='{$member_id}' AND member_id>0")->update(array('times'=>$times, 'use_time'=>$now, 'status'=>$status));
		SQL::share('coupon_history')->insert(array('coupon_sn_id'=>$row->id, 'add_time'=>$now));
	}

	//释放使用优惠券
	public function free($sn, $member_id) {
		$id = intval(SQL::share('coupon_sn')->where("sn='{$sn}' AND member_id='{$member_id}' AND member_id>0")->value('id'));
		if ($id) {
			SQL::share('coupon_sn')->where("sn='{$sn}' AND member_id='{$member_id}' AND member_id>0")->update(array('status'=>0));
			SQL::share('coupon_history')->delete("coupon_sn_id='{$id}'");
		}
	}

	//获得我的优惠券(在下单时)
	public function coupons($member_id, $price=0, $goods=array()) {
		$valid_coupons = array();
		$all_coupons = SQL::share('coupon_sn cs')->inner('coupon c', 'cs.coupon_id=c.id')->sort('cs.id DESC')
			->where("cs.member_id='{$member_id}' AND cs.member_id>'0' AND cs.times!=0 AND cs.status=1 AND c.status=1
				AND c.begin_time<='{$this->now}' AND (c.end_time>='{$this->now}' OR c.end_time=0 OR c.handy_time>0) AND c.offline_use=0")
			->find('cs.id, cs.coupon_money, cs.sn, cs.coupon_id, cs.times, cs.get_time,
				c.name, c.begin_time, c.end_time, c.min_price, c.permit_goods, c.handy_time, c.day_times, NULL as goods');
		if ($all_coupons) {
			foreach ($all_coupons as $k => $coupon) {
				//满足一天内使用次数
				if ($this->get_coupon_history($coupon->id, $coupon->day_times)) {
					//满足价格范围
					if ($this->in_coupon_price($price, $coupon->min_price)) {
						//满足动态时间
						if ($coupon->handy_time>0) {
							$handy_time = $coupon->get_time + $coupon->handy_time * 24*60*60;
							if ($handy_time >= $this->now) {
								//优惠券限制了特定商品
								if ($coupon->permit_goods==1) {
									$coupon_goods = $this->get_coupon_goods($coupon->coupon_id);
									if ($this->is_coupon_goods($goods, $coupon_goods)) {
										$coupon->goods = $this->get_goods($coupon_goods);
										$valid_coupons[] = $coupon;
									}
								} else {
									$valid_coupons[] = $coupon;
								}
							}
						} else {
							if ($coupon->permit_goods==1) {
								$coupon_goods = $this->get_coupon_goods($coupon->coupon_id);
								if ($this->is_coupon_goods($goods, $coupon_goods)) {
									$coupon->goods = $this->get_goods($coupon_goods);
									$valid_coupons[] = $coupon;
								}
							} else {
								$valid_coupons[] = $coupon;
							}
						}
					}
				}
			}
		}
		return $valid_coupons;
	}

	//获取优惠券使用历史
	public function get_coupon_history($coupon_sn_id, $day_times) {
		if ($day_times<=0) return true;
		$now1 = strtotime(date('Y-m-d'));
		$now2 = strtotime(date('Y-m-d').' 23:59:59');
		$count = SQL::share('coupon_history')->where("coupon_sn_id='{$coupon_sn_id}' AND add_time>='{$now1}' AND add_time<='{$now2}'")->count();
		if ($day_times > $count) {
			return true;
		} else {
			return false;
		}
	}

	//优惠券的最低使用价格
	public function in_coupon_price($buy_price, $coupon_price) {
		if ($coupon_price==0) {
			return true;
		} else {
			if ($buy_price >= $coupon_price) {
				return true;
			} else {
				return false;
			}
		}
	}

	//获取优惠券的商品/类型
	public function get_coupon_goods($coupon_id) {
		$ids = array();
		$goods = SQL::share('coupon_goods')->where("coupon_id='{$coupon_id}'")->find('goods_id');
		if ($goods) {
			foreach ($goods as $k=>$g) {
				$ids[] = $g->goods_id;
			}
			$ids = array_unique($ids);
		}
		return $ids;
	}

	//购买的商品是否优惠券范围内的商品/类型
	public function is_coupon_goods($buy_goods, $coupon_goods) {
		if (is_array($buy_goods) && count($buy_goods)) {
			//优惠券的限制商品改为限制分类
			if (intval($this->configs['G_COUPON_PERMIT_CATEGORY'])==1) {
				$category_ids = '';
				foreach ($buy_goods as $k => $goods_id) {
					$category_id = SQL::share('goods')->where($goods_id)->value('category_id');
					$category_ids .= ','.$this->goods_mod->get_category_parents_tree($category_id);
				}
				$category_ids = trim($category_ids, ',');
				$buy_goods = explode(',', $category_ids);
			}
			foreach ($buy_goods as $k => $goods_id) {
				if (in_array($goods_id, $coupon_goods)) return true;
			}
		}
		return false;
	}

	//根据商品/类型获取优惠券的商品id
	public function get_goods($coupon_goods) {
		if (intval($this->configs['G_COUPON_PERMIT_CATEGORY'])==1) {
			$categories = '';
			foreach ($coupon_goods as $category_id) {
				$categories = ','.$this->goods_mod->get_category_children_tree($category_id);
			}
			$categories = trim($categories, ',');
			$categories = explode(',', $categories);
			//去重
			//$categories = array_unique($categories); //效率较低
			$categories = array_flip($categories);
			$categories = array_flip($categories);
			$goods = array();
			foreach ($categories as $category_id) {
				$rs = SQL::share('goods')->where("category_id='{$category_id}'")->find('id');
				if ($rs) {
					foreach ($rs as $g) {
						$goods[] = $g->id;
					}
				}
			}
			//去重
			$goods = array_flip($goods);
			$goods = array_flip($goods);
			//去除数字索引
			$goods = array_values($goods);
			return $goods;
		} else {
			$coupon_goods = array_flip($coupon_goods);
			$coupon_goods = array_flip($coupon_goods);
			$coupon_goods = array_values($coupon_goods);
			return $coupon_goods;
		}
	}
}
