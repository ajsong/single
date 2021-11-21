<?php
//快递100
//http://www.kuaidi100.com
class kd100 extends kuaidi_base {
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
			'huitongkuaidi' => '汇通快递',
			'tiantian' => '天天快递',
			'zhongtong' => '中通快递',
			'zhaijisong' => '宅急送快递',
			'youzhengguonei' => '中国邮政',
			'quanfengkuaidi' => '全峰快递',
			'guotongkuaidi' => '国通快递',
			'longbanwuliu' => '龙邦快递',
			'debangwuliu' => '德邦物流',
			'rufengda' => '如风达快递',
			'quanritongkuaidi' => '全日通快递',
			'jiajiwuliu' => '佳吉快运',
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
		$result = file_get_contents("http://www.kuaidi100.com/query?type={$index}&postid={$mailNo}");
		$result = preg_replace("/<\/?a[^>]*?>/", '', $result);
		$json = json_decode($result, true);
		if (is_array($json)) {
			if (intval($json['status'])==200 && is_array($json['data'])) {
				$data = $json['data'];
				$data = json_encode($data);
				$data = json_decode($data);
				$data = array_reverse($data);
				return $data;
			} else {
				//error($json['message']);
				write_log($json['message'], '/temp/kuaidi.txt');
				$kuaidi = p('kuaidi', 'kdniao');
				return $kuaidi->get($spellName, $mailNo);
			}
		} else {
			//error('快递100接口发生异常');
			write_log('快递100接口发生异常', '/temp/kuaidi.txt');
			$kuaidi = p('kuaidi', 'kdniao');
			return $kuaidi->get($spellName, $mailNo);
		}
	}
}
