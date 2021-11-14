<?php
abstract class pay_base extends plugin_base {
	abstract function pay($order_sn, $total_price, $order_body='订单', $order_param='', $options=array());
	abstract function notify();
	abstract function refund($order_sn, $total_price, $out_refund_no, $pay_method='wxpay_h5', $trade_no='');
	abstract function withdraw($order_sn, $amount, $desc='用户提现');
}