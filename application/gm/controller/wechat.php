<?php
class wechat extends core {
	public function __construct() {
		parent::__construct();
	}
	
	public function index() {
		if (SQL::share()->tableExist('wechat')) {
			$where = '';
			$sort = 'id DESC';
			$keyword = $this->request->get('keyword');
			$sortby = $this->request->get('sortby');
			if (strlen($keyword)) {
				$where .= " AND name LIKE '%{$keyword}%'";
			}
			if ($sortby) {
				$sort = 'w.'.str_replace(',', ' ', $sortby).', '.$sort;
			}
			$rs = SQL::share('wechat w')->where($where)->sort($sort)->isezr()->setpages(compact('keyword', 'sortby'))
				->find("w.*, 0 as alive_fans");
			$sharepage = SQL::share()->page;
			if ($rs) {
				$wxapi = new wechatCallbackAPI();
				foreach ($rs as $g) {
					if (!strlen($g->pic)) {
						$json = $wxapi->authorizer_userinfo($g->appid);
						if ($json) {
							$name = $json['authorizer_info']['nick_name'];
							$type = $json['authorizer_info']['service_type_info']['id'];
							$alias = $json['authorizer_info']['alias'];
							$pic = $json['authorizer_info']['head_img'];
							$qrcode = $json['authorizer_info']['qrcode_url'];
							SQL::share('wechat')->where($g->id)->update(compact('name', 'type', 'alias', 'pic', 'qrcode'));
							$g->name = $name;
							$g->type = $type;
							$g->alias = $alias;
							$g->pic = $pic;
							$g->qrcode = $qrcode;
						}
					}
					if ($g->fans_time==0 || time()-$g->fans_time>60*60*24) {
						$fans = $g->fans;
						$json = $wxapi->authorizer_userlist($g->appid, '', true);
						if ($json && isset($json['total'])) $fans = $json['total'];
						$fans_time = time();
						$g->fans = $fans;
						SQL::share('wechat')->where($g->id)->update(compact('fans', 'fans_time'));
					}
					$g->alive_fans = SQL::share('wechat_user')->where("wechat_id='{$g->id}'")->comparetime('h', 'add_time', '<48')->count();
				}
			}
			$this->smarty->assign('rs', $rs);
			$this->smarty->assign('sharepage', $sharepage);
			$this->display();
		} else {
			$this->wxmenu();
		}
	}

	//菜单
	public function wxmenu() {
		$wx = new wechatCallbackAPI();
		if (IS_POST) {
			$menu = $this->request->post('menu');
			//$menu = '{"button":[{"type":"view","name":"上头条","url":"https://m.joyicloud.com/","sub_button":[]},{"type":"view","name":"达人榜","url":"https://m.joyicloud.com/wap/?app=talent","sub_button":[]},{"name":"我的","sub_button":[{"type":"view","name":"个人中心","url":"https://m.joyicloud.com/wap/?app=member","sub_button":[]},{"type":"view","name":"我的团队","url":"https://m.joyicloud.com/wap/?app=member&act=team","sub_button":[]},{"type":"view","name":"推广赚钱","url":"https://m.joyicloud.com/wap/?app=member&act=poster","sub_button":[]},{"type":"view","name":"我的钱包","url":"https://m.joyicloud.com/wap/?app=member&act=commission","sub_button":[]},{"type":"view","name":"我发布的任务","url":"https://m.joyicloud.com/wap/?app=member&act=task","sub_button":[]}]}]}';
			$wx->setMenu($menu);
			location("?app=wechat&act=wxmenu");
		}
		$menu = $wx->getMenu();
		if (is_array($menu)) $menu = json_encode($menu['menu']);
		$this->smarty->assign('menu', $menu);
		$this->display();
	}

	//模板消息
	public function template() {
		$rs = SQL::share('wechat_template wt')->left('wechat_template_type wtt', 'wt.type_id=wtt.id')->sort('wt.id ASC')->find('wt.*, name');
		$this->smarty->assign('rs', $rs);
		$this->display();
	}

	//导入模板消息列表
	public function template_edit() {
		$wx = new wechatCallbackAPI();
		$message = $wx->getTemplateMessage();
		$msg = array();
		if (is_array($message)) {
			foreach ($message as $k=>$g) {
				if (strlen($g['primary_industry'])) {
					$name = $g['title'];
					$template_id = $g['template_id'];
					$type = SQL::share('wechat_template_type')->where("name='{$name}'")->row('id');
					if ($type) {
						$type_id = $type->id;
					} else {
						$type_id = SQL::share('wechat_template_type')->insert(compact('name'));
					}
					if (!SQL::share('wechat_template')->where("template_id='{$template_id}'")->exist()) {
						SQL::share('wechat_template')->insert(compact('type_id', 'template_id'));
					}
				}
			}
		}
		header("Location:?app=wechat&act=template");
	}

}
