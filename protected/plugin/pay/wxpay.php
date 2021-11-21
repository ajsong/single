<?php
//微信
//https://pay.weixin.qq.com/wiki/doc/api/js/api/?chapter=9_1
class wxpay extends pay_base {
	public function __construct() {
		parent::__construct();
	}
	
	public function pay($order_sn, $total_price, $order_body='订单', $order_param='', $options=array()) {
		require_once(SDK_PATH . '/class/wxapi/WxPayPubHelper/WxPayPubHelper.php');
		
		//=========使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		//$unifiedOrder->debug = true;
		
		//设置统一支付接口参数
		//设置必填参数
		$unifiedOrder->setParameter('body', "{$order_body}");//商品描述
		$unifiedOrder->setParameter('out_trade_no', "{$order_sn}");//商户订单号
		$unifiedOrder->setParameter('total_fee', floatval($total_price)*100);//总金额,单位:分
		$unifiedOrder->setParameter('notify_url', https().$_SERVER['HTTP_HOST']."/notify_url.php");//回调通知
		$unifiedOrder->setParameter('time_start', date('YmdHis'));//交易起始时间
		$unifiedOrder->setParameter('time_expire', date('YmdHis', time()+10*60));//交易结束时间
		
		if ($this->is_app) {
			$unifiedOrder->setParameter('trade_type', 'APP');//交易类型
			$unifiedOrder->setParameter('attach', '2');//附加数据
			$unifiedOrder->config->APPID = WX_PAYAPP_APPID;
			$unifiedOrder->config->APPSECRET = WX_PAYAPP_SECRET;
			$unifiedOrder->config->MCHID = WX_PAYAPP_MCHID;
			$unifiedOrder->config->KEY = WX_PAYAPP_KEY;
			
			$prepay_id = $unifiedOrder->getPrepayId();
			$return = $unifiedOrder->getParametersForAPP($prepay_id);
			return json_encode($return);
		}
		
		/**
		 * JS_API支付
		 * ====================================================
		 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
		 * 成功调起支付需要三个步骤：
		 * 步骤1：网页授权获取用户openid
		 * 步骤2：使用统一支付接口，获取prepay_id
		 * 步骤3：使用jsapi调起支付
		 */
		if (!$this->is_wx) return 'null';
		$api = new JsApi_pub();
		//$api->debug = true;
		
		$openid = '';
		
		if ($this->is_mini==0) {
			//=========网页授权获取用户openid============
			//通过code获得openid
			$code = $this->request->get('code');
			if (!strlen($code)) {
				//外部平台支付
				//$appid = isset($options['appid']) ? $options['appid'] : WX_APPID;
				//$order_body = urlencode($order_body);
				//$redirect_url = urlencode(https().$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?app={$this->app}&act={$this->act}{$order_param}");//跳转回来的成功页面
				//$url = "http://website.cn/api/?app=core&act=get_wxcode&appid={$appid}&is_pay=1&order_sn={$order_sn}&total_price={$total_price}&order_body={$order_body}&url={$redirect_url}";
				//location($url);
				$thirdparty = SQL::share('member_thirdparty')->where("member_id='{$this->member_id}' AND type='wechat'")->row('mark');
				if (!$thirdparty || !strlen($thirdparty->mark)) {
					if (strlen($order_param) && substr($order_param, 0, 1)!='&') $order_param = '&'.$order_param;
					//触发微信返回code码
					$url = $api->createOauthUrlForCode(urlencode(https().$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?app={$this->app}&act={$this->act}{$order_param}"));
					location($url);
				} else {
					$openid = $thirdparty->mark;
				}
			} else {
				//获取code码，以获取openid
				$api->setCode($code);
				$openid = $api->getOpenid();
			}
			
			$unifiedOrder->setParameter('attach', '0');//附加数据
		} else {
			$api->miniprogram = true;
			
			$api->config->APPID = WX_PROGRAM_APPID;
			$api->config->APPSECRET = WX_PROGRAM_SECRET;
			$api->config->MCHID = WX_PROGRAM_MCHID;
			$api->config->KEY = WX_PROGRAM_KEY;
			if (strlen(WX_PROGRAM_SSLCERT_PATH)) $api->config->SSLCERT_PATH = ROOT_PATH . WX_PROGRAM_SSLCERT_PATH;
			if (strlen(WX_PROGRAM_SSLKEY_PATH)) $api->config->SSLKEY_PATH = ROOT_PATH . WX_PROGRAM_SSLKEY_PATH;
			
			$unifiedOrder->config->APPID = $api->config->APPID;
			$unifiedOrder->config->APPSECRET = $api->config->APPSECRET;
			$unifiedOrder->config->MCHID = $api->config->MCHID;
			$unifiedOrder->config->KEY = $api->config->KEY;
			$unifiedOrder->config->SSLCERT_PATH = $api->config->SSLCERT_PATH;
			$unifiedOrder->config->SSLKEY_PATH = $api->config->SSLKEY_PATH;
			
			$openid = SQL::share('member_thirdparty')->where("member_id='{$this->member_id}' AND type='mini'")->value('mark');
			
			$unifiedOrder->setParameter('attach', '1');
		}
		
		$unifiedOrder->setParameter('openid', "{$openid}");//用户标识
		$unifiedOrder->setParameter('trade_type', 'JSAPI');//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter('sub_mch_id', "XXXX");//子商户号
		//$unifiedOrder->setParameter('device_info', "XXXX");//设备号
		//$unifiedOrder->setParameter('goods_tag', "XXXX");//商品标记
		//$unifiedOrder->setParameter('product_id', "XXXX");//商品ID
		
		$prepay_id = $unifiedOrder->getPrepayId();
		//=========使用jsapi调起支付============
		$api->setPrepayId($prepay_id);
		
		return $api->getParameters();
	}
	
	public function notify() {
		//$xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : ''; //PHP7以上不支持
		$xml = file_get_contents('php://input');
		write_log($xml);
		if (!empty($xml)) {
			header('content-type:application/xml; charset=utf-8');
			libxml_disable_entity_loader(true); //禁止引用外部xml实体,修复XXE漏洞
			$obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
			$obj = json_decode(json_encode($obj), true);
			//write_log(json_encode($obj));
			if (!isset($obj['transaction_id'])) exit;
			//查询订单，判断订单真实性
			$query = new OrderQuery_pub();
			$query->setParameter('transaction_id', $obj['transaction_id']);
			$result = $query->getResult();
			if (isset($result['result_code']) && isset($result['return_code']) && $result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS') {
			//if ($obj['result_code'] == 'SUCCESS' && $obj['return_code'] == 'SUCCESS') {
				//效验签名
				$attach = intval($obj['attach']);
				$WX_KEY = WX_KEY;
				if ($attach==1) {
					$WX_KEY = WX_PROGRAM_KEY;
				} else if ($attach==2) {
					if (strlen(WX_PAYAPP_KEY)) $WX_KEY = WX_PAYAPP_KEY;
				}
				$objSign = $obj['sign'];
				unset($obj['sign']);
				$sign = http_build_query($obj);
				$sign = md5("{$sign}&key={$WX_KEY}");
				$sign = strtoupper($sign);
				if ($sign != $objSign) {
					write_log('wxpay sign fail');
					echo "<xml>
						<return_code><![CDATA[FAIL]]></return_code>
						<return_msg><![CDATA[OK]]></return_msg>
					</xml>";
					exit;
				}
				//支付成功
				$data = array();
				//$data['openid'] = $obj['openid']; //用户的微信标识
				$data['trade_no'] = isset($obj['transaction_id']) ? $obj['transaction_id'] : ''; //微信支付订单号
				$data['out_trade_no'] = $obj['out_trade_no']; //商户订单号
				$data['total_fee'] = $obj['total_fee']; //支付金额,单位分
				$data['time_end'] = $obj['time_end']; //支付时间,格式为yyyyMMddHHmmss
				
				echo "<xml>
					<return_code><![CDATA[SUCCESS]]></return_code>
					<return_msg><![CDATA[OK]]></return_msg>
				</xml>";
				return $data;
			} else {
				//结果为支付失败
				write_log('wxpay fail');
				echo "<xml>
					<return_code><![CDATA[FAIL]]></return_code>
					<return_msg><![CDATA[OK]]></return_msg>
				</xml>";
				exit;
			}
		}
		/*
		<xml>
			<appid><![CDATA[wx2421b1c4370ec43b]]></appid>
			<attach><![CDATA[支付测试]]></attach>
			<bank_type><![CDATA[CFT]]></bank_type>
			<fee_type><![CDATA[CNY]]></fee_type>
			<is_subscribe><![CDATA[Y]]></is_subscribe>
			<mch_id><![CDATA[10000100]]></mch_id>
			<nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
			<openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
			<out_trade_no><![CDATA[1409811653]]></out_trade_no>
			<result_code><![CDATA[SUCCESS]]></result_code>
			<return_code><![CDATA[SUCCESS]]></return_code>
			<sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
			<sub_mch_id><![CDATA[10000100]]></sub_mch_id>
			<time_end><![CDATA[20140903131540]]></time_end>
			<total_fee>1</total_fee>
			<trade_type><![CDATA[JSAPI]]></trade_type>
			<transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
		</xml>
		*/
	}
	
	public function refund($order_sn, $total_price, $out_refund_no, $pay_method='wxpay_h5', $trade_no='', $is_show=true) {
		require_once(SDK_PATH . '/class/wxapi/WxPayPubHelper/WxPayPubHelper.php');
		$api = new Refund_pub();
		if ($pay_method=='app' || $pay_method=='wxpay') {
			$api->config->APPID = WX_REFUNDAPP_APPID;
			$api->config->APPSECRET = WX_REFUNDAPP_SECRET;
			$api->config->MCHID = WX_REFUNDAPP_MCHID;
			$api->config->KEY = WX_REFUNDAPP_KEY;
			$api->config->SSLCERT_PATH = ROOT_PATH . WX_REFUNDAPP_SSLCERT_PATH;
			$api->config->SSLKEY_PATH = ROOT_PATH . WX_REFUNDAPP_SSLKEY_PATH;
		}
		if (strlen($trade_no)) {
			$api->setParameter('transaction_id', $trade_no); //微信订单号
		} else {
			$api->setParameter('out_trade_no', $order_sn); //原订单号
		}
		$api->setParameter('total_fee', $total_price*100); //订单金额
		$api->setParameter('refund_fee', $total_price*100); //退款金额
		$api->setParameter('op_user_id', $api->config->MCHID); //操作员账号，一般为商户号
		$api->setParameter('out_refund_no', $out_refund_no); //退款订单号
		if ($this->is_mini!=0) {
			$api->isMiniProgram(true);
		}
		$data = $api->getResult();
		if ($data['return_code']=='SUCCESS') {
			if ($data['result_code']=='SUCCESS') {
				return array('error'=>0);
			} else {
				return array('error'=>1, 'msg'=>$data['err_code_des']);
			}
		} else {
			return array('error'=>1, 'msg'=>$data['return_msg']);
		}
	}
	
	public function withdraw($order_sn, $amount, $desc='用户提现') {
		//企业付款到零钱
		//pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2
		require_once(SDK_PATH . '/class/wxapi/WxPayPubHelper/WxPayPubHelper.php');
		
		if (!$this->is_wx) return 'null';
		
		$openid = '';
		if ($this->is_mini==0) {
			$openid = SQL::share('member_thirdparty')->where("member_id='{$this->member_id}' AND type='wechat'")->value('mark');
		} else {
			$openid = SQL::share('member_thirdparty')->where("member_id='{$this->member_id}' AND type='mini'")->value('mark');
		}
		
		$api = new TransfersOrder_pub();
		//$api->debug = true;
		
		$api->config->APPID = WX_WITHDRAW_APPID;
		$api->config->APPSECRET = WX_WITHDRAW_SECRET;
		$api->config->MCHID = WX_WITHDRAW_MCHID;
		$api->config->KEY = WX_WITHDRAW_KEY;
		$api->config->SSLCERT_PATH = ROOT_PATH . WX_WITHDRAW_SSLCERT_PATH;
		$api->config->SSLKEY_PATH = ROOT_PATH . WX_WITHDRAW_SSLKEY_PATH;
		
		//设置必填参数
		$api->setParameter('openid', "{$openid}");//用户标识
		$api->setParameter('partner_trade_no', "{$order_sn}");//商户订单号
		$api->setParameter('amount', floatval($amount)*100);//总金额,单位:分
		$api->setParameter('desc', "{$desc}");//企业付款描述信息
		if ($this->is_mini!=0) {
			$api->isMiniProgram(true);
		}
		
		$data = $api->getResult();
		if ($data['return_code']=='SUCCESS') {
			if ($data['result_code']=='SUCCESS') {
				return array('error'=>0, 'partner_trade_no'=>$data['partner_trade_no'], 'payment_no'=>$data['payment_no'], 'payment_time'=>strtotime($data['payment_time']));
			} else {
				return array('error'=>1, 'msg'=>$data['err_code_des']);
			}
		} else {
			return array('error'=>1, 'msg'=>$data['return_msg']);
		}
	}
}
