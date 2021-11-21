<?php
//支付宝
//https://docs.open.alipay.com/api_1/alipay.trade.app.pay/
class alipay extends pay_base {
	public function __construct() {
		parent::__construct();
	}
	
	public function pay($order_sn, $total_price, $order_body='订单', $order_param='', $options=array()) {
		$ALIPAY_APPID = ALIPAY_APPID;
		$ALIPAY_PRIVATE_KEY = ALIPAY_PRIVATE_KEY;
		$ALIPAY_PUBLIC_KEY = ALIPAY_PUBLIC_KEY;
		if (strlen($ALIPAY_APPID) && strlen($ALIPAY_PRIVATE_KEY) && strlen($ALIPAY_PUBLIC_KEY)) {
			include_once(PLUGIN_PATH . '/pay/alipay/AopSdk/AopSdk.php');
			$aop = new AopClient();
			$aop->appId = $ALIPAY_APPID;
			$aop->rsaPrivateKey = $ALIPAY_PRIVATE_KEY;
			//$aop->alipayrsaPublicKey = $ALIPAY_PUBLIC_KEY;
			$aop->signType = 'RSA2';
			//SDK已经封装掉了公共参数，这里只需要传入业务参数
			$bizcontent = array();
			$bizcontent['subject'] = WEB_NAME.'支付';
			$bizcontent['body'] = $order_body;
			$bizcontent['out_trade_no'] = $order_sn;
			$bizcontent['timeout_express'] = '30m';
			$bizcontent['total_amount'] = $total_price;
			if ($this->is_app) {
				$bizcontent['passback_params'] = '2';
			} else {
				$bizcontent['passback_params'] = '0';
			}
			//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay、alipay.trade.wap.pay、alipay.trade.page.pay
			if ($this->is_app) {
				$request = new AlipayTradeAppPayRequest();
				$bizcontent['product_code'] = 'QUICK_MSECURITY_PAY';
			} else if (IS_WAP) {
				$request = new AlipayTradeWapPayRequest();
				$bizcontent['product_code'] = 'QUICK_WAP_WAY';
			} else {
				$request = new AlipayTradePagePayRequest();
				$bizcontent['product_code'] = 'FAST_INSTANT_TRADE_PAY';
			}
			$request->setNotifyUrl(https().$_SERVER['HTTP_HOST'].'/notify_url.php');
			if (!$this->is_wx && !$this->is_app && isset($options['return_url'])) $request->setReturnUrl($options['return_url']);
			$request->setBizContent(json_encode($bizcontent));
			if ($this->is_app) {
				$response = $aop->sdkExecute($request);
				return htmlspecialchars($response); //就是orderString 可以直接给客户端请求，无需再做处理
			} else {
				$form = $aop->pageExecute($request);
				exit($form);
			}
		}
		return '';
	}
	
	public function notify() {
		write_log(json_encode($_POST));
		$ALIPAY_APPID = ALIPAY_APPID;
		$ALIPAY_PRIVATE_KEY = ALIPAY_PRIVATE_KEY;
		$ALIPAY_PUBLIC_KEY = ALIPAY_PUBLIC_KEY;
		if (strlen($ALIPAY_APPID) && strlen($ALIPAY_PRIVATE_KEY) && strlen($ALIPAY_PUBLIC_KEY)) { //使用 RSA2 版本
			include_once(PLUGIN_PATH . '/pay/alipay/AopSdk/AopSdk.php');
			//验证签名
			$aop = new AopClient();
			$aop->alipayrsaPublicKey = $ALIPAY_PUBLIC_KEY;
			$_POST['fund_bill_list'] = str_replace('\"', '"', $_POST['fund_bill_list']);
			$flag = $aop->rsaCheckV1($_POST, NULL, 'RSA2');
			//验签
			if ($flag) {
				//处理业务，并从$_POST中提取需要的参数内容
				if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
					//处理交易完成或者支付成功的通知
					$data = array();
					$data['out_trade_no'] = $_POST['out_trade_no']; //商户订单号
					$data['trade_no'] = $_POST['trade_no']; //支付宝交易号
					$data['total_fee'] = $_POST['total_amount']; //交易金额
					$data['trade_status'] = $_POST['trade_status']; //交易状态
					echo 'success'; //请不要修改或删除
					return $data;
				} else if ($_POST['trade_status'] == 'TRADE_FINISHED') {
					exit('success'); //请不要修改或删除
				}
			} else {
				//验证失败
				write_log('alipay fail');
				exit('fail');
			}
		}
		//使用 RSA 版本
		$alipay_config = require_once(PLUGIN_PATH . '/pay/alipay/alipay.config.php');
		require_once(PLUGIN_PATH . '/pay/alipay/lib/alipay_notify.class.php');
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if ($verify_result) {//验证成功
			//只有支付成功才处理
			if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				$data = array();
				$data['out_trade_no'] = $_POST['out_trade_no']; //商户订单号
				$data['trade_no'] = $_POST['trade_no']; //支付宝交易号
				$data['total_fee'] = $_POST['total_fee']; //交易金额
				$data['trade_status'] = $_POST['trade_status']; //交易状态
				//write_log($_POST['trade_no']);
				echo 'success'; //请不要修改或删除
				return $data;
			} else if ($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
				
				//注意：
				//该种交易状态只在两种情况下出现
				//1、开通了普通即时到账，买家付款成功后。
				//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
				
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
				exit('success'); //请不要修改或删除
			}
		}
		
		//验证失败
		write_log(json_encode($verify_result));
		write_log('alipay fail');
		exit('fail');
		/*
		{
			"gmt_create": "2019-01-16 14:15:11",
			"charset": "UTF-8",
			"seller_email": "arobozyb1988@sina.com",
			"subject": "牛友圈支付",
			"sign": "ZwOMmkhnmErncqSHjgIm33FCwX4cssQY+A2ve1uf8GnjEuNGip6k7Bj/6w8aMq9eI24vxpKyziPEj9BV6qp/ediEH4WrfX2IH7+aPtPzDhRU38eV9LIIBD8yuUoZlYB6n/MvB/j54Y7LMyRJGYJGoONeN4w+kvc/6TaMMmgxS8UkfNl1Focw5dE2JPak2plSlIk46FYPpZUqHN2Qv++Ene135X4oxAzFv2HzY3tGQ14aCTGus4gzzlO4md7KI+RfoM0fBc3pu8N+mmW5KYJ26jAViixU23MHNMw87hDHyjjEeD0FL/r83Y98xyq5WBX/OxTIX5EZUTp0qUCT2ZggmA==",
			"body": "开通铜牌会员",
			"buyer_id": "2088302138780521",
			"invoice_amount": "59.00",
			"notify_id": "2019011600222141512080521035438867",
			"fund_bill_list": "[{\"amount\":\"59.00\",\"fundChannel\":\"PCREDIT\"}]",
			"notify_type": "trade_status_sync",
			"trade_status": "TRADE_SUCCESS",
			"receipt_amount": "59.00",
			"app_id": "2018121362559307",
			"buyer_pay_amount": "59.00",
			"sign_type": "RSA2",
			"seller_id": "2088102855900022",
			"gmt_payment": "2019-01-16 14:15:12",
			"notify_time": "2019-01-16 14:15:12",
			"version": "1.0",
			"out_trade_no": "DJ1911614142017557",
			"total_amount": "59.00",
			"trade_no": "2019011622001480521011877574",
			"auth_app_id": "2018121362559307",
			"buyer_logon_id": "jgh***@qq.com",
			"point_amount": "0.00"
		}
		*/
	}
	
	public function refund($order_sn, $total_price, $out_refund_no, $pay_method='wxpay_h5', $trade_no='') {
		$ALIPAY_APPID = ALIPAY_APPID;
		$ALIPAY_PRIVATE_KEY = ALIPAY_PRIVATE_KEY;
		$ALIPAY_PUBLIC_KEY = ALIPAY_PUBLIC_KEY;
		if (strlen($ALIPAY_APPID) && strlen($ALIPAY_PRIVATE_KEY) && strlen($ALIPAY_PUBLIC_KEY)) {
			include_once(PLUGIN_PATH . '/pay/alipay/AopSdk/AopSdk.php');
			$aop = new AopClient();
			$aop->appId = $ALIPAY_APPID;
			$aop->rsaPrivateKey = $ALIPAY_PRIVATE_KEY;
			$aop->alipayrsaPublicKey = $ALIPAY_PUBLIC_KEY;
			$aop->signType = 'RSA2';
			//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.refund
			$request = new AlipayTradeRefundRequest();
			//SDK已经封装掉了公共参数，这里只需要传入业务参数
			$bizcontent = array();
			$bizcontent['trade_no'] = $trade_no;
			$bizcontent['out_trade_no'] = $order_sn;
			$bizcontent['refund_amount'] = $total_price;
			$bizcontent['refund_reason'] = '买家主动退款';
			//$request->setNotifyUrl(https().$_SERVER['HTTP_HOST'].'/notify_url.php'); //因为使用execute即时返回结果,所以没必要使用回调
			$request->setBizContent(json_encode($bizcontent));
			$result = $aop->execute($request);
			$responseNode = str_replace('.', '_', $request->getApiMethodName()) . '_response';
			$data = $result->$responseNode;
			$resultCode = $data->code;
			if (!empty($resultCode) && $resultCode == 10000) {
				return array('error'=>0);
			} else {
				return array('error'=>1, 'msg'=>$data->msg);
			}
		}
		$alipay_config = require_once(PLUGIN_PATH . '/pay/alipay/alipay.config.php');
		require_once(PLUGIN_PATH . '/pay/alipay/lib/alipay_refund_nopwd_service.php');
		$seller_email = $alipay_config['seller_email']; //卖家支付宝帐户
		$refund_date = date('Y-m-d H:i:s'); //退款当天日期
		$batch_no = date('YmdHis'); //批次号, 格式：当天日期[8位]+序列号[3至24位]，如：201008010000001
		$batch_num = 1; //退款笔数
		//必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
		$detail_data = "{$trade_no}^{$total_price}^买家主动退款"; //退款详细数据
		//必填，具体格式请参见接口技术文档，单笔退款数据集格式：原付款支付宝交易号^退款总金额^退款理由
		//$notify_url = https().$_SERVER['HTTP_HOST'].'/notify_url.php'; //异步通知页面
		//构造要请求的参数数组，无需改动
		$parameter = array(
			//接口名称，不需要修改
			'service'			=> 'refund_fastpay_by_platform_nopwd',
			//获取配置文件(alipay_config.php)中的值
			'partner'			=> trim($alipay_config['partner']),
			//'notify_url'		=> $notify_url,
			'_input_charset'	=> trim(strtolower($alipay_config['input_charset'])),
			//必填参数
			'refund_date'		=> $refund_date,
			'batch_no'			=> $batch_no,
			'batch_num'			=> $batch_num,
			'detail_data'		=> $detail_data
		);
		//构造请求函数, 配置环境须支持SSL
		$alipay = new alipay_refund_nopwd_service($parameter, $alipay_config['key'], $alipay_config['sign_type_refund']);
		$url = $alipay->create_url();
		$data = file_get_contents($url);
		$obj = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
		$obj = (array)$obj;
		//write_log(json_encode($obj));
		$nodeIs_success = '';
		$nodeError_code = '';
		if (isset($obj['is_success'])) $nodeIs_success = $obj['is_success']; //获取成功标识is_success
		if (isset($obj['error'])) $nodeError_code = $obj['error']; //获取错误代码 error
		if ($nodeIs_success=='T') {
			return array('error'=>0);
		} else {
			write_log(print_r($obj,true));
			return array('error'=>1, 'msg'=>$nodeError_code);
		}
	}
	
	public function withdraw($order_sn, $amount, $desc='用户提现') {
		//单笔转账到支付宝账户接口
		//docs.open.alipay.com/api_28/alipay.fund.trans.toaccount.transfer
		$ALIPAY_APPID = ALIPAY_WITHDRAW_APPID;
		$ALIPAY_PRIVATE_KEY = ALIPAY_WITHDRAW_PRIVATE_KEY;
		$ALIPAY_PUBLIC_KEY = ALIPAY_WITHDRAW_PUBLIC_KEY;
		if (!strlen($ALIPAY_APPID)) $ALIPAY_APPID = ALIPAY_APPID;
		if (!strlen($ALIPAY_PRIVATE_KEY)) $ALIPAY_PRIVATE_KEY = ALIPAY_PRIVATE_KEY;
		if (!strlen($ALIPAY_PUBLIC_KEY)) $ALIPAY_PUBLIC_KEY = ALIPAY_PUBLIC_KEY;
		if (strlen($ALIPAY_APPID) && strlen($ALIPAY_PRIVATE_KEY) && strlen($ALIPAY_PUBLIC_KEY)) {
			include_once(PLUGIN_PATH . '/pay/alipay/AopSdk/AopSdk.php');
			$aop = new AopClient();
			$aop->appId = $ALIPAY_APPID;
			$aop->rsaPrivateKey = $ALIPAY_PRIVATE_KEY;
			$aop->alipayrsaPublicKey = $ALIPAY_PUBLIC_KEY;
			$aop->signType = 'RSA2';
			
			$member = SQL::share('member')->where("id='{$this->member_id}' AND status=1")->row('alipay');
			if (!$member || !strlen($member->alipay)) error('缺少收款人支付宝账号');
			
			//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.fund.trans.toaccount.transfer
			$request = new AlipayFundTransToaccountTransferRequest();
			//SDK已经封装掉了公共参数，这里只需要传入业务参数
			$bizcontent = array();
			$bizcontent['out_biz_no'] = $order_sn;
			$bizcontent['amount'] = $amount;
			$bizcontent['payee_type'] = 'ALIPAY_LOGONID';
			$bizcontent['payee_account'] = $member->alipay;
			$bizcontent['payer_show_name'] = WEB_NAME;
			$bizcontent['remark'] = $desc;
			//$request->setNotifyUrl(https().$_SERVER['HTTP_HOST'].'/notify_url.php'); //因为使用execute即时返回结果,所以没必要使用回调
			$request->setBizContent(json_encode($bizcontent));
			$result = $aop->execute($request);
			$responseNode = str_replace('.', '_', $request->getApiMethodName()) . '_response';
			$data = $result->$responseNode;
			$resultCode = $data->code;
			if (!empty($resultCode) && $resultCode == 10000) {
				return array('error'=>0, 'out_trade_no'=>$data->out_biz_no, 'trade_no'=>$data->order_id, 'pay_time'=>strtotime($data->pay_date));
			} else {
				write_log(print_r($result,true));
				return array('error'=>1, 'msg'=>$data->sub_msg);
			}
		}
		return array('error'=>1, 'msg'=>'please use aopsdk!');
	}
}
