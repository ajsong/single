<?php
class commission_model extends base_model {

	public function __construct() {
		parent::__construct();
	}

	//写入佣金表
	public function add($member_id, $parent_id=0, $type=1, $memo='', $commission=0, $status=0) {
		switch ($type) {
			case 1: //邀请人分利
				$rs = SQL::share('order_goods og')->left('goods g', 'og.goods_id=g.id')->where("og.order_id='{$parent_id}'")->find('og.price, commission_type, commissions');
				$this->calculate($member_id, $parent_id, $type, $memo, $commission, $status, $rs);
				break;
			case 2: //订单金额转佣金
				$order_price = floatval(SQL::share('order')->where($parent_id)->value('total_price'));
				$this->calculate($member_id, $parent_id, $type, $memo, $commission, $status, $order_price);
				break;
			case 3: //邀请注册佣金
				$this->calculate($member_id, $parent_id, $type, $memo, $commission, $status);
				break;
			default:
				$this->calculate($member_id, $parent_id, $type, $memo, $commission, $status);
				break;
		}
	}
	
	//计算佣金
	public function calculate($member_id, $parent_id, $type=1, $memo='', $commission=0, $status=0, $data=NULL) {
		switch ($type) {
			case 1:
				if ($data) {
					foreach ($data as $k=>$g) {
						if ($g->commission_type>0 && strlen($g->commissions)) {
							$commissions = explode(',', $g->commissions);
							foreach ($commissions as $c) {
								if ($member_id<=0 || !is_numeric($c) || floatval($c)<=0) continue;
								$commission = 0;
								if ($g->commission_type==1) { //百分比
									$commission = bcmul(floatval($g->price), floatval($c), 2);
								} else if ($g->commission_type==2)  { //固定金额
									$commission = floatval($c);
								}
								$this->_add($member_id, $parent_id, $type, $commission, strlen($memo)?$memo:'邀请人订单佣金收入', $status);
								$member_id = intval(SQL::share('member')->where($member_id)->value('parent_id'));
							}
						}
					}
				}
				break;
			case 2:
				$percent = floatval($this->configs['G_SHOP_ORDER_COMMISSION']);
				if ($percent) $commission = bcmul($data, $percent, 2);
				$this->_add($member_id, $parent_id, $type, $commission, strlen($memo)?$memo:'订单佣金收入', $status);
				break;
			case 3:
				$commission = floatval($this->configs['G_INVITE_REGISTER_COMMISSION']);
				$this->_add($member_id, $parent_id, $type, $commission, strlen($memo)?$memo:'邀请用户注册收入', $status);
				break;
			default:
				$this->_add($member_id, $parent_id, $type, $commission, $memo, $status);
				break;
		}
	}
	private function _add($member_id, $parent_id, $type, $commission, $memo, $status) {
		$add_time = time();
		SQL::share('member_commission')->insert(compact('member_id', 'parent_id', 'type', 'commission', 'memo', 'status', 'add_time'));
	}

	//结算佣金
	public function commit($parent_id, $type=1){
		$commission = 0;
		$row = SQL::share('member_commission')->where("type='{$type}' AND parent_id='{$parent_id}'")->row();
		if ($row) {
			if ($row->status==0) {
				$commission = $row->commission;
				//更新状态
				SQL::share('member_commission')->where($row->id)->update(array('status'=>1));
				//更新用户的佣金
				SQL::share('member')->where($row->member_id)->update(array(
					'commission'=>array("+{$row->commission}"),
					'commission_total'=>array("+{$row->commission}")
				));
			}
		}
		return $commission;
	}

	//取消佣金
	public function cancel($parent_id=0, $type=1){
		$commission = 0;
		$row = SQL::share('member_commission')->where("type='{$type}' AND parent_id='{$parent_id}'")->row();
		if ($row) {
			$commission = $row->commission;
			//原本是已经结算的状态，需要减去
			if ($row->status==1) {
				$member = SQL::share('member')->where($row->member_id)->row('commission, commission_total');
				$new_commission = $member->commission - $commission;
				$new_commission_total = $member->commission_total - $commission;
				//允许负数的佣金出现
				//if ($new_commission < 0) $new_commission = 0;
				//if (new_commission_total < 0) new_commission_total = 0;
				//更新用户的佣金
				SQL::share('member')->where($row->member_id)->update(array('commission'=>$new_commission, 'commission_total'=>$new_commission_total));
				//写入余额变动明细
				SQL::share('member_money_history')->insert(array('member_id'=>$row->member_id, '`money`'=>"-{$commission}", 'memo'=>"取消佣金",
					'type'=>$type, 'parent_id'=>$parent_id, 'add_time'=>time(), 'status'=>1));
			}
			//更新状态
			SQL::share('member_commission')->where($row->id)->update(array('status'=>-1));
		}
		return $commission;
	}

	//状态
	public function status_name($status) {
		$str = $status;
		switch ($status) {
			case -1:$str = '退款';break;
			case 0:$str = '冻结';break;
			case 1:$str = '正常(已到会员钱包)';break;
		}
		return $str;
	}
	
	//转到可用佣金
	public function turn_commission($order_id) {
		$order = SQL::share('order')->where($order_id)->row('status');
		if ($order->status>2) {
			//查出佣金
			$rs = SQL::share('member_commission')->where("status=0 AND (type=1 OR type=2) AND parent_id='{$order_id}'")->find('id, commission, member_id');
			if ($rs) {
				foreach ($rs as $k=>$g) {
					//佣金转到会员表
					SQL::share('member')->where($g->member_id)->update(array(
						'commission'=>array('commission', "+{$g->commission}"),
						'commission_total'=>array('commission_total', "+{$g->commission}")
					));
				}
			}
			//更新佣金状态
			SQL::share('member_commission')->where("status=0 AND (type=1 OR type=2) AND parent_id='{$order_id}'")->update(array('status'=>1));
		}
	}
}
