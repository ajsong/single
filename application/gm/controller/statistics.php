<?php
class statistics extends core {
	public function __construct() {
		parent::__construct();
	}
	
	public function member() {
		$begin_time = $this->request->get('begin_time');
		$end_time = $this->request->get('end_time');
		$begintime = strlen($begin_time) ? $begin_time : date('Y-m');
		$endtime = strlen($end_time) ? $end_time : date('Y-m');
		$xaxis = array();
		$arr = array();
		$months = get_month_range($begintime, $endtime);
		$num = count($months);
		if (count($months)==1) {
			$num = date('t', strtotime($begintime));
			for ($i=1; $i<=$num; $i++) {
				$xaxis[] = "'{$i}日'";
			}
		} else if (count($months)>0) {
			for ($i=0; $i<$num; $i++) {
				$xaxis[] = "'".$months[$i]."'";
			}
		}
		$xaxis = implode(',', $xaxis);
		for ($i=0; $i<$num; $i++) {
			if (count($months)==1) {
				$starttime = strtotime($begintime.'-'.($i+1));
				$endtime = $starttime + (60*60*24-1);
			} else {
				$starttime = strtotime($months[$i].'-1');
				$endtime = strtotime($months[$i].'-'.date('t', strtotime($months[$i])).' 23:59:59');
			}
			$where = "reg_time>={$starttime} AND reg_time<={$endtime}";
			$arr[] = SQL::share('member')->where($where)->count();
		}
		$rs = implode(',', $arr);
		$this->smarty->assign('xaxis', $xaxis);
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('begin_time', $begin_time);
		$this->smarty->assign('end_time', $end_time);
		$this->display();
	}
	
	public function order() {
		$begin_time = $this->request->get('begin_time');
		$end_time = $this->request->get('end_time');
		$begintime = strlen($begin_time) ? $begin_time : date('Y-m');
		$endtime = strlen($end_time) ? $end_time : date('Y-m');
		$xaxis = array();
		$total_price = array();
		$arr = array();
		$months = get_month_range($begintime, $endtime);
		$num = count($months);
		if (count($months)==1) {
			$num = date('t', strtotime($begintime));
			for ($i=1; $i<=$num; $i++) {
				$xaxis[] = "'{$i}日'";
			}
		} else if (count($months)>0) {
			for ($i=0; $i<$num; $i++) {
				$xaxis[] = "'".$months[$i]."'";
			}
		}
		$xaxis = implode(',', $xaxis);
		for ($i=0; $i<$num; $i++) {
			if (count($months)==1) {
				$starttime = strtotime($begintime.'-'.($i+1));
				$endtime = $starttime + (60*60*24-1);
			} else {
				$starttime = strtotime($months[$i].'-1');
				$endtime = strtotime($months[$i].'-'.date('t', strtotime($months[$i])).' 23:59:59');
			}
			$where = "status>0 AND add_time>={$starttime} AND add_time<={$endtime}";
			$price = floatval(SQL::share('order')->where($where)->value('SUM(total_price)'));
			$total_price[] = $price;
			$arr[] = SQL::share('order')->where($where)->count();
		}
		$total_price = implode(',', $total_price);
		$rs = implode(',', $arr);
		$this->smarty->assign('xaxis', $xaxis);
		$this->smarty->assign('total_price', $total_price);
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('begin_time', $begin_time);
		$this->smarty->assign('end_time', $end_time);
		$this->display();
	}
	
	public function sales() {
		$sort = $this->request->get('sort', 0);
		$begin_time = $this->request->get('begin_time');
		$end_time = $this->request->get('end_time');
		$begintime = strlen($begin_time) ? $begin_time : date('Y-m');
		$endtime = strlen($end_time) ? $end_time : date('Y-m');
		$orderby = $sort==2 ? 'quantity' : 'price';
		$starttime = 0;
		$overtime = 0;
		$months = get_month_range($begintime, $endtime);
		if (count($months)==1) {
			$starttime = strtotime($begintime.'-1');
			$overtime = $starttime + (60*60*24-1);
		} else if (count($months)>0) {
			$starttime = strtotime($months[0].'-1');
			$overtime = strtotime($months[count($months)-1].'-'.date('t', strtotime($months[count($months)-1])).' 23:59:59');
		}
		$ids = SQL::share('order')->where("status>0 AND add_time>={$starttime} AND add_time<={$overtime}")->returnArray()->find('id');
		if (!count($ids)) $ids = array(0);
		$rs = SQL::share('order_goods')->where("order_id IN (".implode(',', $ids).")")
			->isezr()->setpages(compact('begin_time', 'end_time', 'sort'))->group('goods_id')->sort("{$orderby} DESC")
			->find('goods_name, goods_id, SUM(price*quantity) as price, SUM(quantity) as quantity');
		$sharepage = SQL::share()->page;
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
}
