<?php
class ad_model extends base_model {

	public function __construct() {
		parent::__construct();
	}

	//获取详情
	public function detail($id) {
		$row = SQL::share('ad')->where($id)->row();
		return $row;
	}
	
	//类型
	public function types() {
		$types = array();
		$type = SQL::share('ad_type')->where("status=1")->find();
		if ($type) {
			$functions = SQL::share('client_function')->returnArray()->find('value');
			foreach ($type as $k=>$g) {
				if (in_array($g->name, $functions)) {
					if (in_array($g->name, $this->function)) {
						$types[] = $g;
					}
				} else {
					$types[] = $g;
				}
			}
		}
		return $types;
	}

	//类型名称
	public function type_name($type) {
		$types = $this->types();
		foreach ($types as $k=>$g) {
			if ($g->name == $type) {
				return $g->value;
				break;
			}
		}
		return '';
	}
	
	//位置
	public function positions($has_categories=false) {
		$position = SQL::share('ad_position')->where("status=1")->find();
		if ($has_categories && in_array('category', $this->function)) {
			$categories = SQL::share('goods_category')->where("status=1 AND parent_id=0")->sort('sort DESC, id ASC')->find('id, name');
			if ($categories) {
				foreach ($categories as $k=>$g) {
					$data = new stdClass();
					$data->name = "category{$g->id}";
					$data->value = "分类 - {$g->name}(category{$g->id})";
					$data->memo = '';
					$position[] = $data;
				}
			}
		}
		return $position;
	}
	
	//位置名称
	public function position_name($position) {
		if (stripos($position, 'category')!==false) {
			$p = str_replace('category', '', $position);
			return $position.'(分类 - '.$this->_category_name($p).')';
		}
		$positions = $this->positions();
		foreach ($positions as $k=>$g) {
			if ($g->name == $position) {
				return $g->value;
				break;
			}
		}
		return '';
	}
	//分类名称
	private function _category_name($id) {
		return SQL::share('goods_category')->where($id)->value('name');
	}
}
