<?php
class article extends core {
	private $article_mod;

	public function __construct() {
		parent::__construct();
		$this->article_mod = m('article');
	}

	//发现首页
	public function index() {
		$where = '';
		$category_id = $this->request->get('type_id');
		//关键词搜索
		$keyword = $this->request->get('keyword');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		if ($keyword) {
			$where .= " AND (title like '%{$keyword}%' OR content like '%{$keyword}%')";
		}
		if ($category_id) {
			$where .= " AND category_id='{$category_id}'";
		}
		$rs = SQL::share('article')->where("status=1 AND (mark='' OR mark IS NULL) {$where}")->sort('sort ASC, id DESC')->limit($offset, $pagesize)->find();
		if ($rs) {
			foreach ($rs as $k=>$g) {
				$rs[$k]->pics = $this->article_mod->_pics($g->id, 3);
				//$rs[$k]->goods = $this->article_mod->_goods($g->id);
				$rs[$k]->content = preg_replace('/[\n\r]+/', '', preg_replace('/<\/?[^>]+>/', '', $g->content));
				$rs[$k]->add_time = get_time_word($g->add_time);
			}
		}

		$flashes = $this->_flash();
		success(array("flashes"=>$flashes, "articles"=>$rs));
	}

	//轮播图
	private function _flash() {
		$flashes = SQL::share('ad')->where("(begin_time=0 OR begin_time<='{$this->now}') AND (end_time=0 OR end_time>='{$this->now}')
			AND status=1 AND position='faxian'")->sort('sort ASC, id DESC')->pagesize(5)->find();
		if ($flashes) {
			$flashes = add_domain_deep($flashes, array('pic'));
		}
		return $flashes;
	}

	//添加/修改
	public function add() {
		//error_tip('该功能尚未开通');
		if ($this->member_id<=0) error('请登录', -100);
		if (IS_POST) {
			$id = $this->request->post('id', 0);
			$title = $this->request->post('title');
			$content = $this->request->post('content');
			$pics = $this->request->post('pics', '', 'xg');
			$goods = $this->request->post('goods', '', 'xg');
			if (!$title || !$content || !$pics) error('缺少资料');
			if ($id) {
				SQL::share('article')->where("member_id='{$this->member_id}' AND id='{$id}'")->update(array('title'=>$title, 'content'=>$content));
			} else {
				$id = SQL::share('article')->insert(array('member_id'=>$this->member_id, 'title'=>$title, 'content'=>$content, 'add_time'=>time()));
			}
			//保存图片
			SQL::share('article_pic')->delete("article_id='{$id}'");
			if ($pics) {
				$pics = json_decode($pics);
				foreach ($pics as $p) {
					SQL::share('article_pic')->insert(array('article_id'=>$id, 'pic'=>$p, 'add_time'=>time()));
				}
			}
			//保存关联产品
			SQL::share('article_goods')->delete("article_id='{$id}'");
			if ($goods) {
				$goods = json_decode($goods);
				foreach ($goods as $g) {
					SQL::share('article_goods')->insert(array('article_id'=>$id, 'goods_id'=>$g));
				}
			}
			$rs = SQL::share('article')->where("member_id='{$this->member_id}' AND id='{$id}'")->row();
			success($rs);
		} else {
			$id = $this->request->get('id', 0);
			if ($id) {
				$rs = SQL::share('article')->where("member_id='{$this->member_id}' AND id='{$id}'")->row();
				success($rs);
			}
			success('ok');
		}
	}
	
	public function upload_pic(){
		$local = $this->request->request('local', UPLOAD_LOCAL);
		if ($this->is_wx && !$this->is_mini) {
			$result = $this->request->post('pic');
			//下载微信图片
			$wxapi = new wechatCallbackAPI();
			$json = $wxapi->access_token();
			$access_token = $json['access_token'];
			$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$result}";
			$data = $wxapi->downloadFile($url);
			$result = upload_obj_file($data, 'article', 'body', $local);
		} else {
			$result = $this->request->file('article', 'pic', $local);
		}
		success($result);
	}

	//文章详情
	public function detail() {
		$id = $this->request->get('id');
		if (!strlen($id)) error('缺少参数');
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$where = "status=1";
		if (is_numeric($id)) {
			$where .= " AND id='{$id}'";
		} else {
			$where .= " AND mark='{$id}'";
		}
		$row = SQL::share('article')->where($where)->row();
		if (!$row) error('文章不存在');
		$id = $row->id;
		$row->content = preg_replace('/(width|height):\s*\d+px;?/', '', stripslashes($row->content));
		$row->add_time = date("Y-m-d H:i:s", $row->add_time);
		$row->pics = $this->article_mod->_pics($id); //关联图片
		$row->goods = $this->article_mod->_goods($id); //关联商品
		$row->liked = $this->article_mod->_liked($id); //是否点赞
		$row->likes_list = $this->likes($id);
		$row->comments_list = $this->_comments($id, $offset, $pagesize);
		SQL::share('article')->where($id)->update(array('clicks'=>array('clicks', '+1')));
		success($row);
	}

	//文章的点赞
	public function likes($article_id) {
		$likes = SQL::share('article_like l')->left('member m', 'l.member_id=m.id')
			->where("l.article_id='{$article_id}'")->pagesize(5)->sort('l.id DESC')->find('m.id as member_id, m.avatar, m.nick_name');
		return $likes;
	}

	//点赞
	public function like() {
		$article_id = $this->request->post('article_id', 0);
		$likes = 0;
		if ($this->member_id && $article_id) {
			$exists = intval(SQL::share('article_like')->where("article_id='{$article_id}' AND member_id='{$this->member_id}'")->count());
			if ($exists) {
				SQL::share('article_like')->delete("article_id='{$article_id}' AND member_id='{$this->member_id}'");
			} else {
				SQL::share('article_like')->insert(array('article_id'=>$article_id, 'member_id'=>$this->member_id, 'add_time'=>time(), 'ip'=>$this->ip));
			}
			$likes = SQL::share('article_like')->where("article_id='{$article_id}'")->count();
			SQL::share('article')->where($article_id)->update(array('likes'=>$likes));
		}
		success($likes);
	}

	//发表评论
	public function post_comment() {
		if ($this->member_id<=0) error('请先登录', -100);
		$article_id = $this->request->post('article_id', 0);
		$content = $this->request->post('content');
		SQL::share('article_comment')->insert(array('article_id'=>$article_id, 'member_id'=>$this->member_id, 'content'=>$content, 'add_time'=>time(), 'ip'=>$this->ip));
		$comments = SQL::share('article_comment c')->left('member m', 'c.member_id=m.id') ->where("article_id='{$article_id}' AND c.parent_id=0 AND m.status=1")->count();
		SQL::share('article')->where($article_id)->update(array('comments'=>$comments));
		success($article_id);
	}

	//列出评论
	public function comments() {
		$article_id = $this->request->get('article_id', 0);
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$comments = $this->_comments($article_id, $offset, $pagesize);
		success($comments);
	}

	public function _comments($article_id, $offset, $pagesize) {
		$comments = SQL::share('article_comment c')->left('member m', 'c.member_id=m.id')
			->where("article_id='{$article_id}' AND c.parent_id=0 AND m.status=1")->sort('c.id ASC')->limit($offset, $pagesize)
			->find('c.*, m.avatar, m.name as member_name, m.nick_name as member_nick_name');
		if ($comments) {
			$comments = add_domain_deep($comments, array("avatar"));
			foreach ($comments as $k=>$g) {
				$comments[$k]->add_time = get_time_word($g->add_time);
				$comments[$k]->member_name = get_mobile_mark($g->member_name);
				$replys = SQL::share('article_comment c')->left('member m', 'c.member_id=m.id')
					->where("c.parent_id='{$g->id}'")->sort('c.id ASC')->find('c.id, c.member_id, c.content, c.add_time, m.name as member_name');
				if ($replys) {
					foreach ($replys as $rk=>$rg) {
						$replys[$rk]->add_time = get_time_word($rg->add_time);
						if ($rg->member_id==0) $replys[$rk]->member_name = '客服';
						$replys[$rk]->member_name = get_mobile_mark($rg->member_name);
					}
				}
				$comments[$k]->replys = $replys;
			}
		}
		return $comments;
	}
	
	//添加评论回复
	public function reply_comment() {
		if ($this->member_id<=0) error('请先登录', -100);
		$article_id = $this->request->post('article_id', 0);
		$parent_id = $this->request->post('parent_id', 0);
		$content = $this->request->post('content');
		if ($article_id<=0 || $parent_id<=0) error('数据错误');
		if (!$content) error('请填写回复内容');
		$time = time();
		$id = SQL::share('article_comment')->insert(array('member_id'=>$this->member_id, 'article_id'=>$article_id, 'parent_id'=>$parent_id, 'content'=>$content,
			'ip'=>$this->ip, 'add_time'=>$time));
		success(array('id'=>$id, 'member_name'=>get_mobile_mark($this->member_name), 'content'=>$content, 'add_time'=>get_time_word($time)));
	}

}
