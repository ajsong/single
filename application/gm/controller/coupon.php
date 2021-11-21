<?php
class coupon extends core {
	private $goods_mod;
	private $coupon_mod;
	
	public function __construct() {
		parent::__construct();
		$this->goods_mod = m('goods');
		$this->coupon_mod = m('coupon');
	}

	//index
	public function index() {
		$where = '';
		$id = $this->request->get('id', 0);
		$keyword = $this->request->get('keyword');
		$begin_time = $this->request->get('begin_time');
		$end_time = $this->request->get('end_time');
		if ($id) {
			$where .= " AND id='{$id}'";
		}
		if (strlen($begin_time)) {
			$where .= " AND begin_time>='".strtotime($begin_time)."'";
		}
		if (strlen($end_time)) {
			$where .= " AND end_time<='".strtotime($end_time)."'";
		}
		if ($keyword) {
			$where .= " AND (name LIKE '%{$keyword}%' OR coupon_money LIKE '%{$keyword}%' )";
		}
		$rs = SQL::share('coupon')->where($where)->isezr()->setpages(compact('id', 'keyword', 'begin_time', 'end_time'))->sort('id DESC')->find();
		$sharepage = SQL::share()->page;
		if ($rs) {
			foreach ($rs as $key => $g) {
				$rs[$key]->sn = SQL::share('coupon_sn')->where("coupon_id='{$g->id}'")->count();
			}
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}

	public function add() {
		$this->edit();
	}

	public function edit() {
		$id = $this->request->get('id', 0);
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$name = $this->request->post('name');
			$shop_id = $this->request->post('shop_id', 0);
			$coupon_money = $this->request->post('coupon_money', 0, 'float');
			$coupon_discount = $this->request->post('coupon_discount', 0, 'float');
			$all_price = $this->request->post('all_price', 0);
			$position = $this->request->post('position', 1);
			$min_price = $this->request->post('min_price');
			$begin_time = $this->request->post('begin_time');
			$end_time = $this->request->post('end_time');
			$handy_time = $this->request->post('handy_time', 0);
			$quantity = $this->request->post('quantity');
			$auto_add = $this->request->post('auto_add', 0);
			$num_per_person = $this->request->post('num_per_person');
			$offline_use = $this->request->post('offline_use');
			$permit_goods = $this->request->post('permit_goods');
			$goods = $this->request->post('goods');
			$times = $this->request->post('times', 1);
			$day_times_handle = $this->request->post('day_times_handle', 1);
			$day_times = $this->request->post('day_times', 0);
			$status = $this->request->post('status', 0);
			$type = $this->request->post('type', 0);
			$unlimited = $this->request->post('unlimited', 0);
			$begin_time = strtotime($begin_time);
			$end_time = strtotime($end_time);
			if (!$begin_time) $begin_time = 0;
			if (!$end_time) $end_time = 0;
			if ($day_times_handle==0) $day_times = 1;
			if ($permit_goods==0) {
				$goods = '';
			} else if (!$goods) {
				$permit_goods = 0;
			}
			if ($begin_time<0) $begin_time = 0;
			if ($end_time<0) $end_time = 0;
			if ($end_time!=0 && $end_time<=time()) $status = -2;
			if ($all_price==1) $coupon_money = $coupon_discount = -1;
			if ($coupon_money>0) $coupon_discount = 0;
			if ($coupon_discount>0) $coupon_money = 0;
			if ($times==0) $times = 1;
			if ($times<-1) $times = -1;
			if ($unlimited==1) $times = -1;
			$data = compact('name', 'shop_id', 'coupon_money', 'coupon_discount', 'position', 'min_price', 'begin_time', 'end_time', 'handy_time', 'quantity', 'auto_add',
				'num_per_person', 'status', 'offline_use', 'permit_goods', 'type', 'times', 'day_times');
			if ($id>0) {
				SQL::share('coupon')->where($id)->update($data);
			} else {
				$data['add_time'] = time();
				$id = SQL::share('coupon')->insert($data);
			}
			//增加发行
			if ($auto_add==0) {
				if (strlen($quantity)) {
					$quantity = intval($quantity);
					if ($status==0) $status = 1;
					$data = array();
					for ($i=0; $i<$quantity; $i++) {
						$data[] = date('m') . date('d') . substr(time(), -3) . substr(microtime(), 2, 6) . rand(100, 999);
					}
					SQL::share('coupon_sn')->insert(array('coupon_id'=>$id, 'coupon_money'=>$coupon_money, 'sn'=>$data, 'add_time'=>time(), 'get_time'=>0,
						'times'=>$times, 'status'=>$status), 'sn');
				}
			}
			//相关商品
			SQL::share('coupon_goods')->delete("coupon_id='{$id}'");
			if ($goods) {
				$data = array();
				foreach ($goods as $g) {
					$data[] = $g;
				}
				SQL::share('coupon_goods')->insert(array('coupon_id'=>$id, 'goods_id'=>$data), 'goods_id');
			}
			location("?app=coupon&act=index");
		} else if ($id>0) {
			$row = SQL::share('coupon')->where($id)->row();
			$sn = SQL::share('coupon_sn')->where("coupon_id='{$id}'")->count();
			if (intval($this->configs['G_COUPON_PERMIT_CATEGORY'])==1) {
				$goods = SQL::share('coupon_goods c')->left('goods_category g', 'c.goods_id=g.id')->where("c.coupon_id='{$id}'")->find('g.id, g.name');
				if ($goods) {
					foreach ($goods as $k=>$g) {
						$ids = $this->goods_mod->get_category_parents_tree($g->id);
						$categories = array();
						$rs = SQL::share('goods_category')->where("id IN ({$ids})")->find('id, name');
						foreach ($rs as $r) {
							$categories[] = "<a href='?app=category&act=edit&id={$r->id}' target='_blank'>{$r->name}</a>";
						}
						$goods[$k]->name = $categories;
					}
				}
			} else {
				$goods = SQL::share('coupon_goods c')->left('goods g', 'c.goods_id=g.id')->where("c.coupon_id='{$id}'")->find('g.id, g.name, g.price, g.pic, c.id as cid');
			}
		} else {
			$row = t('coupon');
			$sn = 0;
			$goods = NULL;
		}
		$this->smarty->assign('row', $row);
		$this->smarty->assign('sn', $sn);
		$this->smarty->assign('goods', $goods);
		$shop = SQL::share('shop')->where("status=1")->sort('id DESC')->find('id, name');
		$this->smarty->assign('shop', $shop);
		$this->display('coupon.edit.html');
	}

	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('coupon')->delete($id);
		SQL::share('coupon_goods')->delete("coupon_id='{$id}'");
		SQL::share('coupon_sn')->delete("coupon_id='{$id}'");
		header("Location:?app=coupon&act=index");
	}
	
	//sn
	public function sn() {
		$where = '';
		$coupon_id = $this->request->get('coupon_id', 0);
		$id = $this->request->get('id', 0);
		$status = $this->request->get('status');
		$keyword = $this->request->get('keyword');
		$get_time1 = $this->request->get('get_time1');
		$get_time2 = $this->request->get('get_time2');
		$use_time1 = $this->request->get('use_time1');
		$use_time2 = $this->request->get('use_time2');
		if ($id) {
			$where .= " AND (OR s.id='{$id}' OR m.id='{$id}')";
		}
		if (strlen($get_time1)) {
			$where .= " AND get_time>='".strtotime($get_time1)."'";
		}
		if (strlen($get_time2)) {
			$where .= " AND get_time<='".strtotime($get_time2)."'";
		}
		if (strlen($use_time1)) {
			$where .= " AND s.use_time>='".strtotime($use_time1)."'";
		}
		if (strlen($use_time2)) {
			$where .= " AND s.use_time<='".strtotime($use_time2)."'";
		}
		if ($status) {
			$where .= " AND s.status='{$status}'";
		}
		if ($keyword) {
			$where .= " AND (s.sn LIKE '%{$keyword}%' OR s.id LIKE '%{$keyword}%' OR s.member_id LIKE '%{$keyword}%'OR m.name LIKE '%{$keyword}%')";
		}
		$rs = SQL::share('coupon_sn s')->left('member m', 's.member_id=m.id')->where("coupon_id='{$coupon_id}' {$where}")->isezr()
			->setpages(compact('coupon_id', 'id', 'status', 'keyword', 'get_time1', 'get_time2', 'use_time1', 'use_time2'))->sort('s.id DESC')
			->find('s.*, m.name, m.nick_name');
		$sharepage = SQL::share()->page;
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}

	//delete
	public function delete_sn() {
		$id = $this->request->get('id', 0);
		SQL::share('coupon_sn')->delete($id);
		header("Location:?app=coupon&act=sn");
	}

	//coupon_grant
	public function grant() {
		if (IS_POST) {
			$all = $this->request->post('all', 1);
			$member_id = $this->request->post('member_id', 0);
			$coupon_id = $this->request->post('coupon_id', 0);
			$result = false;
			if ($all == 2) {
				$member = SQL::share('member')->where("status=1")->find('id');
				if ($member) {
					foreach ($member as $k=>$g) {
						$result = $this->coupon_mod->send($coupon_id, $g->id);
					}
				}
			} else {
				$result = $this->coupon_mod->send($coupon_id, $member_id);
			}
			$html = '<meta charset="UTF-8"><script>alert("';
			switch ($result) {
				case 1:$html .= '发放成功';break;
				case 0:$html .= '发放失败';break;
				case -1:$html .= '已领取';break;
				case -2:$html .= '优惠券不存在';break;
			}
			$html .= '");location.href="?app=coupon&act=index";</script>';
			echo $html;
			exit;
		} else {
			$now = time();
			$coupon = SQL::share('coupon c')->where("status=1 AND begin_time<='{$now}' AND (end_time>='{$now}' OR end_time='0' OR handy_time>'0')")
				->find('c.id, c.name, c.coupon_money');
			$this->smarty->assign('coupon', $coupon);
			$member = SQL::share('member')->find('id, name, nick_name');
			$this->smarty->assign('member', $member);
			$this->display();
		}
	}
}
