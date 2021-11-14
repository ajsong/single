<?php
class favorite extends core {

	public function __construct() {
		parent::__construct();
	}
	
	//收藏总数
	public function total($item_id=0, $type_id=0) {
		$isReturn = false;
		if ($item_id) $isReturn = true;
		$item_id = $this->request->post('item_id', $item_id);
		$type_id = $this->request->post('type_id', $type_id);
		$count = 0;
		if ($this->member_id && $item_id) {
			$count = SQL::share('favorite')->where("item_id='{$item_id}' AND type_id='{$type_id}' AND member_id='{$this->member_id}'")->count();
		}
		if ($isReturn) {
			return $count;
		} else {
			success($count);
		}
	}

	//添加、删除收藏
	public function add() {
		if (!$this->member_id) error('请登录', -100);
		$item_id = $this->request->post('item_id', 0);
		$type_id = $this->request->post('type_id', 0);
		if ($item_id) {
			$exists = SQL::share('favorite')->where("item_id='{$item_id}' AND type_id='{$type_id}' AND member_id='{$this->member_id}'")->count();
			if ($exists) {
				SQL::share('favorite')->delete("item_id='{$item_id}' AND type_id='{$type_id}' AND member_id='{$this->member_id}'");
			} else {
				SQL::share('favorite')->insert(array('item_id'=>$item_id, 'type_id'=>$type_id, 'member_id'=>$this->member_id, 'add_time'=>time()));
			}
		}
		$count = $this->total($item_id, $type_id);
		success($count);
	}

	//我的收藏
	public function index() {
		$type_id = $this->request->get('type_id', 0);
		$rs = null;
		switch ($type_id) {
			case 1:
				$rs = $this->_goods();
				break;
			case 2:
				$rs = $this->_shops();
				break;
			case 3:
				$rs = $this->_countries();
				break;
			case 4:
				$rs = $this->_articles();
				break;
			default:
				$rs = $this->_goods();
				break;
		}
		success($rs);
	}

	private function _goods() {
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$rs = SQL::share('favorite f')->left('goods g', 'f.item_id=g.id')->where("g.status=1 AND f.member_id='{$this->member_id}' AND f.type_id=1")
			->sort('f.id DESC')->limit($offset, $pagesize)->find('g.*');
		if ($rs) {
			foreach ($rs as $k=>$g) {
				unset($rs[$k]->content);
			}
		}
		$goods_mod = m('goods');
		$rs = $goods_mod->set_min_prices($rs);
		$rs = add_domain_deep($rs, array('pic'));
		return $rs;
	}

	private function _articles() {
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$rs = SQL::share('favorite f')->left('article a', 'f.item_id=a.id')->where("f.member_id='{$this->member_id}' AND f.type_id='4'")
			->sort('f.id DESC')->limit($offset, $pagesize)->find('a.*');
		return $rs;
	}

	private function _countries() {
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$rs = SQL::share('favorite f')->left('country c', 'f.item_id=c.id')->where("f.member_id='{$this->member_id}' AND f.type_id='2'")
			->sort('f.id DESC')->limit($offset, $pagesize)->find('c.*');
		return $rs;
	}

	private function _shops() {
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$rs = SQL::share('favorite f')->left('shop s', 'f.item_id=s.id')->where("f.member_id='{$this->member_id}' AND f.type_id='2'")
			->sort('f.id DESC')->limit($offset, $pagesize)->find('s.*');
		return $rs;
	}
}
