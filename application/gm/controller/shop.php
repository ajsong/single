<?php
class shop extends core {
	public function __construct() {
		parent::__construct();
	}

	//index
	public function index() {
		$where = '';
		$id = $this->request->get('id', 0);
		$keyword = $this->request->get('keyword');
		if ($id) {
			$where .= " AND s.id='{$id}'";
		}
		if ($keyword) {
			$where .= " AND (s.name LIKE '%{$keyword}%' OR s.mobile LIKE '%{$keyword}%' OR m.name LIKE '%{$keyword}%')";
		}
		$rs = SQL::share('shop s')->left('member m', 's.member_id=m.id')->where($where)->isezr()->setpages(compact('id', 'keyword'))
			->sort('s.id DESC')->find('s.*, m.name as member_name');
		$sharepage = SQL::share()->page;
		$wherebase64 = SQL::share()->wherebase64;
		if ($rs) {
			foreach ($rs as $key => $g) {
				$rs[$key]->url = urlencode(https().$_SERVER['HTTP_HOST']."/wap/?app=shop&act=detail&id={$g->id}&reseller={$g->id}&qrcode=1");
			}
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->smarty->assign('where', $wherebase64);
		$this->display();
	}

	public function add() {
		$this->edit();
	}

	public function edit() {
		$id = $this->request->get('id', 0);
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$name = $this->request->post('name');
			$member_id = $this->request->post('member_id', 0);
			$mobile = $this->request->post('mobile');
			$tel = $this->request->post('tel');
			$shop_type = $this->request->post('shop_type', 0);
			$status = $this->request->post('status', 0);
			$province = $this->request->post('province', 440000);
			$city = $this->request->post('city', 440100);
			$district = $this->request->post('district', 0);
			$address = $this->request->post('address');
			$description = $this->request->post('description');
			$longitude = $this->request->post('longitude');
			$latitude = $this->request->post('latitude');
			$sort = $this->request->post('sort', 999);
			$wifi = $this->request->post('wifi', 0);
			$pickup = $this->request->post('pickup', 0);
			$app_discount = $this->request->post('app_discount', 0, 'float');
			$business_time = $this->request->post('business_time');
			$poster_pic = $this->request->file('shop', 'poster_pic', UPLOAD_LOCAL);
			$data = compact('name', 'member_id', 'mobile', 'shop_type', 'status', 'province', 'city', 'district', 'description', 'address', 'tel',
				'longitude', 'latitude', 'sort', 'wifi', 'pickup', 'poster_pic', 'app_discount', 'business_time');
			if ($id) {
				SQL::share('shop')->where($id)->update($data);
			} else {
				$data['add_time'] = time();
				$id = SQL::share('shop')->insert($data);
			}
			//清除该店铺的所属会员
			SQL::share('member')->where("shop_id='{$id}'")->update(array('shop_id'=>0));
			if ($member_id>0) {
				SQL::share('member')->where($member_id)->update(array('shop_id'=>$id));
			}
			location("?app=shop&act=index");
		} else if ($id>0) {
			$row = SQL::share('shop')->where($id)->row();
		} else {
			$row = t('shop');
		}
		$this->smarty->assign('row', $row);
		$member = SQL::share('member')->where("status=1")->sort('id DESC')->find('id, name, nick_name, real_name');
		if ($member) {
			foreach ($member as $k=>$g) {
				if (strlen($g->nick_name)) $member[$k]->name = $g->nick_name;
				else if (strlen($g->real_name)) $member[$k]->name = $g->real_name;
			}
		}
		$this->smarty->assign('member', $member);
		$this->display('shop.edit.html');
	}

	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		if (SQL::share('shop')->delete($id)) { //删除该商店被收藏
			SQL::share('favorite')->delete("item_id='{$id}' AND type_id=2");
		}
		header("Location:?app=shop&act=index");
	}
	
	public function qrcode_out() {
		require_once(SDK_PATH . '/class/phpqrcode/phpqrcode.php');
		$where = stripslashes($this->request->get('where'));
		$row = SQL::share('shop s')->where($where)->sort('id DESC')->find('s.id, s.name');
		//创建目录
		$path = ROOT_PATH.UPLOAD_PATH.'/qrcode/'.time().rand(000,999);
		mkdir($path, 0777);
		foreach ($row as $g) {
			$url = https().$_SERVER['HTTP_HOST']."/wap/?app=shop&act=detail&id={$g->id}&reseller=";
			$name = $g->name;
			$name = $this->replace_specialChar($name);
			$name = iconv('UTF-8', 'GB2312//IGNORE', $name);
			QRcode::pngg($url, $name, $path, true, true);
		};
		require_once(SDK_PATH . '/class/phpqrcode/PHPZip.php');
		// $path = ROOT_PATH.UPLOAD_PATH.'/qrcode';
		$zip = new PHPZip();
		//压缩并下载并
		$zip->ZipAndDownload($path);
		//删除文件夹
		rmdir($path);
	}
	
	//删除字符串中的特殊字符
	public function replace_specialChar($strParam){
    	$regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|\、|\、|\s|/";
    	return preg_replace($regex,"",$strParam);
	}
	
	//申请列表
	public function shop_apply() {
		$where = '';
		$rs = SQL::share('shop_apply s')->left('member m', 's.member_id=m.id')
			->where($where)->isezr()->sort('s.id DESC')->find('s.*, m.name as member_name');
		$sharepage = SQL::share()->page;
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
	
	public function shop_apply_edit() {
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$name = $this->request->post('name');
			$status = $this->request->post('status', 0);
			$remark = $this->request->post('remark');
			SQL::share('shop_apply')->where($id)->update(array('name'=>$name, 'status'=>$status, 'remark'=>$remark));
			
			if ($status==1) {
				$row = SQL::share('shop_apply')->where($id)->row();
				if ($row) {
					//店铺名称是否存在
					if (SQL::share('shop')->where("name='{$row->name}'")->count()) error("该名称已经存在");
					$id = SQL::share('shop')->insert(array('name'=>$row->name, 'member_id'=>$row->member_id, 'add_time'=>time(), 'status'=>1));
					SQL::share('member')->where($row->member_id)->update(array('shop_id'=>$id));
					success('ok', '', '', '', "?app=shop&act=edit&id={$id}", '提交成功，请编辑店铺信息');
				}
			}
			success('ok', '', '', '', '?app=shop&act=shop_apply', '提交成功');
		} else {
			$id = $this->request->get('id', 0);
			$rs = SQL::share('shop_apply s')->left('member m', 's.member_id=m.id')->where("s.id='{$id}'")->row('s.*, m.name as member_name');
			$this->smarty->assign('row',$rs);
			
			$this->display();
		}
	}
	
	public function shop_apply_delete() {
		$id = $this->request->get('id', 0);
		SQL::share('shop_apply')->delete($id);
		header("Location:?app=shop&act=shop_apply");
	}
}
