<?php
//爱查快递
//http://www.ickd.cn
class ickd extends kuaidi_base {
	public function __construct() {
		parent::__construct();
	}
	
	public function company() {
		return array(
			'ems' => 'EMS快递',
			'shunfeng' => '顺丰快递',
			'shentong' => '申通快递',
			'yuantong' => '圆通快递',
			'yunda' => '韵达快递',
			'huitong' => '汇通快递',
			'tiantian' => '天天快递',
			'zhongtong' => '中通快递',
			'zhaijisong' => '宅急送快递',
			'pingyou' => '中国邮政',
			'quanfeng' => '全峰快递',
			'guotong' => '国通快递',
			'kuaijie' => '快捷快递',
			'jingdong' => '京东快递',
			'ririshun' => '日日顺物流',
			'longbang' => '龙邦快递',
			'debang' => '德邦物流',
			'rufeng' => '如风达快递',
			'quanritong' => '全日通快递',
			'jiaji' => '佳吉快运',
			'dhl' => 'DHL快递'
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
		} else if (isset($kd[strtolower($spellName)])) {
			$index = strtolower($spellName);
		}
		if ($index === false) error("没有该物流公司代号: {$spellName}");
		$data = file_get_contents("http://biz.trace.ickd.cn/{$index}/{$mailNo}?callback=callback");
		if (strpos($data, 'callback(')!==false) {
			$result = str_replace('callback(', '', $data);
			$result = preg_replace("/<span[^>]+?>/", '', $result);
			$result = str_replace('<\\/span>', '', $result);
			$result = preg_replace("/<\/?a[^>]*?>/", '', $result);
			$result = substr($result, 0, strlen($result)-2);
			$json = json_decode($result, true);
			if (!is_null($json)) {
				if (intval($json['errCode'])==0) {
					if (is_array($json['data'])) {
						$data = $json['data'];
						$data = json_encode($data);
						$data = json_decode($data);
						return $data;
					} else {
						$kuaidi = p('kuaidi', 'kd100');
						return $kuaidi->get($spellName, $mailNo);
					}
				} else {
					error($json['message']);
				}
			} else {
				exit($data);
			}
		} else {
			error('爱查快递接口发生异常');
		}
	}
}
