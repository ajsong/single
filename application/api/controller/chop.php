<?php
class chop extends core {
	private $chop_mod;
	private $goods_mod;
	private $member_mod;

	public function __construct() {
		parent::__construct();
		$this->chop_mod = m('chop');
		$this->goods_mod = m('goods');
		$this->member_mod = m('member');
	}
	
	//我的订单列表，0进行中，1砍价成功，-1砍价失败
	public function index() {
		$status = $this->request->get('status');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 10);
		$where = " AND mc.member_id='{$this->member_id}'";
		if (strlen($status)) {
			$where .= " AND mc.status IN ({$status})";
		}
		$rs = SQL::share('member_chop mc')->left('order o', 'mc.order_id=o.id')
			->where($where)->sort('mc.id DESC')->limit($offset, $pagesize)->find("mc.*, order_sn, '' as status_name");
		if ($rs) {
			foreach ($rs as $k=>$chop) {
				$goods = SQL::share('order_goods og')->where("og.order_id='{$chop->order_id}'")->find();
				if ($goods) {
					foreach ($goods as $gk=>$g) {
					
					}
				}
				$rs[$k]->goods = $goods;
				$rs[$k]->status_name = $this->chop_mod->status_name($chop->status);
			}
		}
		$rs = add_domain_deep($rs, array('goods_pic'));
		success($rs);
	}
	
	//砍价详情
	public function detail() {
		$order_sn = $this->request->get('order_sn');
		if (!strlen($order_sn)) error('缺少数据');
		$jsApiParameters = NULL;
		$order = SQL::share('order o')
			->where("parent_order_sn='{$order_sn}' OR order_sn='{$order_sn}' OR pay_order_sn='{$order_sn}'")
			->row('o.*, NULL as order_goods, NULL as member, NULL as member_list, 0 as chop_status, 0 as owner, NULL as goods');
		if ($order) {
			$member = SQL::share('member')->where("id='{$order->member_id}' AND status=1")->row('name, avatar');
			if (!$member) error('该订单不存在');
			$member->name = $this->member_mod->get_name($order->member_id);
			$order->member = $member;
			$goods = SQL::share('order_goods og')->left('goods g', 'goods_id=g.id')->left('member_chop mc', 'mc.order_id=og.order_id')
				->where("og.order_id='{$order->id}' AND g.status=1")
				->row('g.id, market_price, goods_name as name, goods_pic as pic, g.price, g.promote_price,
				g.chop_begin_time, mc.chop_end_time, g.chop_time, mc.id as chop_id, remain_price, mc.readed, mc.status, mc.add_time');
			if (!$goods) error('该商品不存在或已下架', -99);
			$goods->price = $this->goods_mod->get_min_price(array($goods->price, $goods->promote_price));
			$goods = $this->goods_mod->set_activity($goods);
			$goods->chop_end_time = $goods->add_time + 60*60*$goods->chop_time;
			$order->order_goods = $goods;
			if ($goods->remain_price==0 || $goods->status==1) {
				$order->chop_status = 1;
			} else if ($goods->chop_end_time<=$goods->chop_now || $goods->status==-1) {
				$order->chop_status = -1;
			}
			$member_list = SQL::share('member_chop_list mcl')->left('member m', 'mcl.member_id=m.id')->where("mcl.parent_id='{$goods->chop_id}'")
				->sort('mcl.id DESC')->find('m.id, m.name, m.avatar, mcl.price, mcl.memo');
			if ($member_list && $this->member_id>0) {
				foreach ($member_list as $k=>$g) {
					if ($this->member_id==$g->id) {
						$member_list[$k]->name = '我自己';
					} else {
						$member_list[$k]->name = $this->member_mod->get_name($g->id);
					}
				}
			}
			$order->member_list = $member_list;
			if ($this->member_id>0 && $this->member_id==$order->member_id) {
				$order->owner = 1;
			}
			$detail = $this->goods_mod->detail($goods->id, true);
			unset($detail->content);
			$order->goods = $detail;
			
			SQL::share('member_chop')->where($goods->chop_id)->update(array('readed'=>1));
			
			if ($order->owner==1 && $order->status==0 && $order->chop_status==1) {
				$pay_order_sn = generate_sn();
				$order->pay_order_sn = $pay_order_sn;
				SQL::share('order')->where($order->id)->update(array('pay_order_sn'=>$pay_order_sn));
				
				$order_body = WEB_NAME.'-订单';
				$total_price = $order->total_price;
				$api = p('pay', 'wxpay');
				$jsApiParameters = $api->pay($pay_order_sn, $total_price, $order_body);
			}
		}
		$client = SQL::share('client')->row();
		success(compact('order', 'client', 'jsApiParameters'));
	}
	
	public function cut() {
		if ($this->member_id<=0) error('请登录', -100);
		$order_sn = $this->request->post('order_sn');
		if (!strlen($order_sn)) error('缺少数据');
		$order = SQL::share('order o')->where("parent_order_sn='{$order_sn}' OR order_sn='{$order_sn}' OR pay_order_sn='{$order_sn}'")->row();
		if (!$order) error('记录不存在');
		$chop = SQL::share('member_chop')->where("order_id='{$order->id}'")->row();
		if (!$chop) error('记录不存在');
		if (SQL::share('member_chop_list')->where("parent_id='{$chop->id}' AND member_id='{$this->member_id}'")->count()) error('你已帮TA砍过了');
		$chop->chop_end_time = $chop->add_time + 60*60*$chop->chop_time;
		if ($chop->remain_price==0 || $chop->status==1) {
			error('该砍价大会已完结');
		} else if ($chop->chop_end_time<=time() || $chop->status==-1) {
			error('该砍价大会已完结');
		}
		$memo = $this->chop_mod->random_memo();
		$price = 0;
		$remain_price = $chop->remain_price;
		$num = $chop->num;
		if ($num+1<$chop->chop_num) {
			$min = 0.01; //最小金额
			$max = $remain_price / ($chop->chop_num-$num) * 2; //最大金额
			$price = random_float() * $max; //砍掉的金额
			$price = $price<=$min ? $min : $price; //如果比要求的最小金额还小，则取最小金额
			$price = floor($price * 100) / 100;
		} else {
			$price = $remain_price;
		}
		$remain_price -= $price;
		$data = array('remain_price'=>$remain_price, 'num'=>array('+1'));
		if ($remain_price<=0) $data['status'] = 1;
		SQL::share('member_chop')->where($chop->id)->update($data);
		SQL::share('member_chop_list')->insert(array('member_id'=>$this->member_id, 'parent_id'=>$chop->id, 'price'=>$price, 'memo'=>$memo, 'add_time'=>time()));
		success(array('avatar'=>$this->member_avatar, 'memo'=>$memo, 'price'=>$price));
	}
}
