<?php
class groupbuy extends core {
	private $groupbuy_mod;
	private $goods_mod;

	public function __construct() {
		parent::__construct();
		$this->groupbuy_mod = m('groupbuy');
		$this->goods_mod = m('goods');
	}
	
	//我的订单列表，0拼团中，1拼团成功，-1拼团失败
	public function index() {
		$status = $this->request->get('status');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 10);
		$where = " AND mg.member_id='{$this->member_id}'";
		if (strlen($status)) {
			$where .= " AND mg.status IN ({$status})";
		}
		$rs = SQL::share('member_groupbuy mg')->left('order o', 'mg.order_id=o.id')
			->where($where)->sort('mg.id DESC')->limit($offset, $pagesize)->find("mg.*, order_sn, '' as status_name");
		if ($rs) {
			foreach ($rs as $k=>$groupbuy) {
				$goods = SQL::share('order_goods og')->where("og.order_id='{$groupbuy->order_id}'")->find();
				if ($goods) {
					foreach ($goods as $gk=>$g) {
					
					}
				}
				$rs[$k]->goods = $goods;
				$rs[$k]->status_name = $this->groupbuy_mod->status_name($groupbuy->status);
			}
		}
		$rs = add_domain_deep($rs, array('goods_pic'));
		success($rs);
	}
	
	//拼团详情
	public function detail() {
		$order_sn = $this->request->get('order_sn');
		if (!strlen($order_sn)) error('缺少数据');
		$order = SQL::share('order o')
			->where("o.status>0 AND (parent_order_sn='{$order_sn}' OR order_sn='{$order_sn}' OR pay_order_sn='{$order_sn}') AND handle_after_paid='1'")
			->row('o.*, NULL as order_goods, NULL as member_list, 0 as remain_number, 0 as groupbuy_status, 0 as owner, NULL as goods');
		if ($order) {
			$goods = SQL::share('order_goods og')->left('goods g', 'goods_id=g.id')->left('member_groupbuy mg', 'mg.order_id=og.order_id')
				->where("og.order_id='{$order->id}' AND g.status=1")
				->row('g.id, market_price, goods_name as name, goods_pic as pic, og.price,
				g.groupbuy_begin_time, mg.groupbuy_end_time, g.groupbuy_number, g.groupbuy_time, mg.add_time, mg.status');
			if (!$goods) error('该商品不存在或已下架', -99);
			$goods = $this->goods_mod->set_activity($goods);
			$goods->remain = $goods->groupbuy_number - (SQL::share('member_groupbuy')->where("parent_id='{$goods->id}'")->count()+1);
			$goods->groupbuy_end_time = $goods->add_time + 60*60*$goods->groupbuy_time;
			$order->order_goods = $goods;
			if ($goods->remain==0 || $goods->status==1) {
				$order->groupbuy_status = 1;
			} else if ($goods->groupbuy_end_time<=$goods->groupbuy_now || $goods->status==-1) {
				$order->groupbuy_status = -1;
			}
			$order->member_list = SQL::share('member_groupbuy mg')->left('member m', 'mg.member_id=m.id')->where("order_id='{$order->id}'")->sort('mg.parent_id ASC, id ASC')
				->find('m.id, m.name, m.avatar, mg.parent_id');
			$order->remain_number = $goods->groupbuy_number - count($order->member_list);
			if ($this->member_id>0 && $this->member_id==$order->member_id) {
				$order->owner = 1;
			}
			$detail = $this->goods_mod->detail($goods->id, true);
			unset($detail->content);
			$order->goods = $detail;
		}
		success($order);
	}
}
