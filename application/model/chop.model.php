<?php
class chop_model extends base_model {

	public function __construct() {
		parent::__construct();
	}

	//0进行中，1砍价成功，-1砍价失败
	public function status_name($status) {
		$str = $status;
		switch ($status) {
			case 0:$str = '砍价中';break;
			case 1:$str = '砍价成功';break;
			case -1:$str = '砍价失败';break;
		}
		return $str;
	}
	
	//随机获取砍价说明
	public function random_memo() {
		$array = array(
			'用神奇大宝剑',
			'助你一臂之力',
			'用尚方宝剑',
			'用王者的气质',
			'浑身解数来砍价',
			'大刀阔斧砍一刀',
			'助力砍砍砍',
			'帅气的一挥舞',
			'看我的锋利宝刀',
			'全靠俺的一声吼',
			'看我的青龙偃月刀',
			'路见砍价，随手一砍'
		);
		$result = array_rand($array, 1);
		return $array[$result];
	}

}
