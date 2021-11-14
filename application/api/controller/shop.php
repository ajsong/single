<?php
class shop extends core {

	public function __construct() {
		parent::__construct();
	}
	
	//我是商家首页
	public function index() {
		if ($this->member_id<=0) error('请登录', -100);
		if ($this->shop_id<=0) error('您不是商家');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		//店铺
		$shop = SQL::share('shop')->where($this->shop_id)->row();
		if (!$shop) error('数据错误');
		$goods = SQL::share('goods')->where("status='1' AND shop_id='{$this->shop_id}'")->sort('sort ASC, id DESC')->limit($offset, $pagesize)
			->find('id, name, pic, price');
		$shop->goods = $goods;
		$shop = add_domain_deep($shop, array('avatar', 'poster_pic', 'pic'));
		//订单总数
		$order_total = SQL::share('order')->where("shop_id='{$this->shop_id}' AND status>0")->count();
		//本月订单总数
		$order_month_total = SQL::share('order')
			->where("shop_id='{$this->shop_id}' AND status>0 AND add_time>='".strtotime(date('Y-m-1'))."' AND add_time<='".strtotime(date('Y-m-d 23:59:59'))."'")
			->count();
		//我的收入
		$commission = floatval(SQL::share('member')->where($this->member_id)->value('commission'));
		
		success(array('order_total'=>$order_total, 'order_month_total'=>$order_month_total, 'commission'=>$commission, 'shop'=>$shop));
	}
	
	//店铺订单列表，0未支付，1已支付，2已发货，3完成（已收货），-1取消，-2退款，-3退货
	public function order() {
		if ($this->member_id<=0) error('请登录', -100);
		if ($this->shop_id<=0) error('您不是商家');
		$keyword = $this->request->get('keyword');
		$status = $this->request->get('status');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$where = "o.shop_id='{$this->shop_id}' AND o.shown=1";
		if (strlen($status)) {
			if ($status=='-2,-3') {
				$where .= " AND (o.status IN ({$status}) OR o.ask_refund_time>0)";
			} else {
				$where .= " AND o.status IN ({$status})";
			}
		}
		if ($keyword) {
			$where .= " AND (o.order_sn LIKE '%{$keyword}%' OR o.name LIKE '%{$keyword}%' OR o.mobile LIKE '%{$keyword}%')";
		}
		$orders = SQL::share('order o')->where($where)->sort('o.id DESC')->limit($offset, $pagesize)->find('o.*');
		if ($orders) {
			foreach ($orders as $k=>$o) {
				$goods = SQL::share('order_goods')->where("order_id='{$o->id}'")->find();
				$goods = add_domain_deep($goods, array("goods_pic"));
				$orders[$k]->goods = $goods;
			}
		}
		success($orders);
	}

	//订单详情
	public function order_detail() {
		$member = o('member');
		$member->order_detail(true);
	}
	
	//店铺列表
	public function shop_list() {
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$shops = SQL::share('shop')->where("status='1'")->sort('sort ASC, id DESC')->limit($offset, $pagesize)->find();
		if ($shops) {
			foreach ($shops as $key => $shop) {
				//$shops[$key]->comment_time = date("Y-m-d H:i:s", $shop->comment_time);
			}
			$shops = add_domain_deep($shops, array("avatar", "poster_pic"));
		}
		success($shops);
	}

	//店铺详情
	public function detail() {
		$id = $this->request->get('id', 0);
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		SQL::share('shop')->where($id)->update(array('clicks'=>array('+1')));
		$shop = SQL::share('shop')->where("id='{$id}' AND status='1'")->row();
		if ($shop) {
			$ads = SQL::share('ad')->where("(begin_time=0 OR begin_time<='{$this->now}') AND (end_time=0 OR end_time>='{$this->now}')
				AND status='1' AND shop_id='{$id}'")->sort('sort ASC, id DESC')->pagesize(5)->find();
			$shop->ads = $ads;
			$goods = SQL::share('goods')->where("status='1' AND shop_id='{$id}'")->sort('sort ASC, id DESC')->limit($offset, $pagesize)->find();
			if ($goods) {
				foreach ($goods as $k => $g) {
					unset($goods[$k]->content);
					$goods[$k]->price = $this->goods_mod->get_min_price(array($g->price, $g->promote_price));
				}
			}
			$shop->goods = $goods;
			$shop = add_domain_deep($shop, array("avatar", "poster_pic", "pic"));
		}
		success($shop);
	}
	
	//申请入驻
	public function apply() {
		if ($this->member_id<=0) error('请先登录', -100);
		if (SQL::share('shop_apply')->where("member_id='{$this->member_id}'")->count()) error('您已经申请了入驻，请勿重复提交');
		if (IS_POST) {
			$type_id =  $this->request->post('type_id', 0);
			$type_sub =  $this->request->post('type_sub', 0);
			$name = $this->request->post('name');
			$idcard_pic1 = $this->request->post('idcard_pic1');
			$idcard_pic2 = $this->request->post('idcard_pic2');
			$business_license_pic = $this->request->post('business_license_pic');
			$license_pic = $this->request->post('license_pic');
			$other_pic = $this->request->post('other_pic');
			$contacter = $this->request->post('contacter');
			$mobile = $this->request->post('mobile');
			$province = $this->request->post('province');
			$city = $this->request->post('city');
			$district = $this->request->post('district');
			$address = $this->request->post('address');
			$reason = $this->request->post('reason');
	
			//店铺名称长度2~24个字符 willson2016/2/3
			$len = mb_strlen($name);
			if ($len<2 || $len>24) error('名称不能超过24个字符');
			
			//店铺名称是否存在
			if (SQL::share('shop')->where("name='{$name}'")->count()) error('该名称已经存在');
			
			if ($type_sub>0) $type_id = 5; //团餐
			SQL::share('shop_apply')->insert(array('member_id'=>$this->member_id, 'name'=>$name, 'province'=>$province, 'city'=>$city, 'district'=>$district,
				'address'=>$address, 'contacter'=>$contacter, 'mobile'=>$mobile, 'reason'=>$reason, 'type_id'=>$type_id, 'type_sub'=>$type_sub,
				'idcard_pic1'=>$idcard_pic1, 'idcard_pic2'=>$idcard_pic2, 'business_license_pic'=>$business_license_pic, 'license_pic'=>$license_pic,
				'other_pic'=>$other_pic, 'status'=>0, 'add_time'=>time()));
		}
		success('ok');
	}

	//编辑店铺
	public function edit_info() {
		$name = $this->request->post('name');
		if ($name) {
			if (SQL::share('shop')->where("name='{$name}' AND id!='{$this->shop_id}'")->count()) error('该名称已经存在');
		}
		$fields = array("name", "description", "qq", "weixin", "email", "avatar", "poster_pic", 
			"return_province", "return_city", "return_district", "return_address", "return_name", "return_mobile", 
			"province", "city", "district", "address", "contacter", "mobile", "tel"
		);
		$data = array();
		foreach ($fields as $field) {
			if (isset($_POST[$field])) {
				$value = $this->request->post($field);
				$data[$field] = $value;
			}
		}
		if (count($data)) {
			SQL::share('shop')->where($this->shop_id)->update($data);
		}
		$shop = SQL::share('shop')->where($this->shop_id)->row();
		success($shop);
	}

	//门店地图
	public function map() {
		$this->detail();
	}
}
