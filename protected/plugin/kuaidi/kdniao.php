<?php
//快递鸟 - 物流公司对应字母
//http://www.kdniao.com/YundanChaxunAPI.aspx
class kdniao extends kuaidi_base {
	public function __construct() {
		parent::__construct();
	}
	
	public function company() {
		return array(
			'EMS' => 'EMS快递',
			'SF' => '顺丰快递',
			'STO' => '申通快递',
			'YTO' => '圆通快递',
			'YD' => '韵达快递',
			'HTKY' => '汇通快递',
			'HHTT' => '天天快递',
			'ZTO' => '中通快递',
			'ZJS' => '宅急送快递',
			'YZPY' => '中国邮政',
			'QFKD' => '全峰快递',
			'GTO' => '国通快递',
			'FAST' => '快捷快递',
			'JD' => '京东快递',
			'LB' => '龙邦快递',
			'DBL' => '德邦物流',
			'RFD' => '如风达快递',
			'QRT' => '全日通快递',
			'JJKY' => '佳吉快运',
			'DHL' => 'DHL快递'
		);
	}
	
	public function get($spellName, $mailNo) {
		if (!$spellName || !$mailNo) error('缺少快递单号或订单编号');
		$index = false;
		$kd = $this->company();
		preg_match("/[\x{4e00}-\x{9fa5}]+/u", $spellName, $matches);
		if (count($matches)) {
			$companyName = str_replace('快递','',str_replace('物流','',str_replace('快运','',str_replace('速递','',str_replace('速运','',$spellName)))));
			foreach ($kd as $k => $v) {
				if (stripos($v, $companyName)!==false) {
					$index = $k;
					break;
				}
			}
		} else if (isset($kd[strtoupper($spellName)])) {
			$index = strtoupper($spellName);
		}
		if ($index === false) error("没有该物流公司代号: {$spellName}");
		$EBusinessID = "1256920"; //电商ID
		$AppKey = "e7bbede8-6d12-439f-9ebf-d835613b638f"; //电商加密私钥
		$data = array('OrderCode'=>'', 'ShipperCode'=>"{$index}", 'LogisticCode'=>"{$mailNo}");
		$requestData = json_encode($data);
		$dataSign = "{$requestData}{$AppKey}";
		$dataSign = urlencode(base64_encode(md5($dataSign)));
		$postData = array(
			'EBusinessID' => $EBusinessID,
			'RequestType' => '1002',
			'RequestData' => urlencode($requestData),
			'DataType' => '2',
			'DataSign' => $dataSign
		);
		$json = requestData('post', 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx', $postData, true);
		if (is_array($json)) {
			if (intval($json['Success'])==1) {
				//$json['State'] 2:在途中, 3:签收, 4:问题件
				$result = json_encode($json);
				$result = str_replace('"Traces":', '"data":', $result);
				$result = str_replace('"AcceptStation":', '"context":', $result);
				$result = str_replace('"AcceptTime":', '"time":', $result);
				$result = preg_replace("/<\/?a[^>]*?>/", '', $result);
				$json = json_decode($result, true);
				if (!is_null($json) && is_array($json['data'])) {
					$data = $json['data'];
					$data = json_encode($data);
					$data = json_decode($data);
					//$data = array_reverse($data);
					return $data;
				} else {
					//$kuaidi = p('kuaidi', 'ickd');
					//return $kuaidi->get($spellName, $mailNo);
					return array();
				}
			} else {
				//error($json['Reason']);
				write_log($json['Reason'], '/temp/kuaidi.txt');
				//$kuaidi = p('kuaidi', 'ickd');
				//return $kuaidi->get($spellName, $mailNo);
				return array();
			}
		} else {
			//error('快递鸟接口发生异常');
			write_log('快递鸟接口发生异常', '/temp/kuaidi.txt');
			//$kuaidi = p('kuaidi', 'ickd');
			//return $kuaidi->get($spellName, $mailNo);
			return array();
		}
	}
}
