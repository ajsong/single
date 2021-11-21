<?php
class article extends core {
	public $article_mod;
	
	public function __construct() {
		parent::__construct();
		$this->article_mod = m('article');
	}

	//index
	public function index() {
		$where = '';
		$sort = 'a.mark ASC, a.id DESC';
		$id = $this->request->get('id', 0);
		$keyword = $this->request->get('keyword');
		$category_id = $this->request->get('category_id');
        $ext_property = $this->request->get('ext_property');
		$sortby = $this->request->get('sortby');
		if ($id) {
			$where .= " AND a.id='{$id}'";
		}
		if ($keyword) {
			$where .= " AND (a.title LIKE '%{$keyword}%' OR a.content LIKE '%{$keyword}%')";
		}
		if ($category_id) {
			$where .= " AND a.category_id='{$category_id}'";
		}
		if ($ext_property) {
			if (!is_array($ext_property)) $ext_property = explode(',', $ext_property);
			$where .= " AND (";
			foreach ($ext_property as $e) {
				$where .= "CONCAT(',',a.ext_property,',') LIKE '%,{$e},%' OR ";
				//$where .= "FIND_IN_SET('{$e}', a.ext_property) OR ";
			}
			$where = trim($where, ' OR ').")";
		}
		if ($sortby) {
			$sort = 'a.'.str_replace(',', ' ', $sortby).', '.$sort;
		}
		$rs = SQL::share('article a')->left('article_category ac', 'a.category_id=ac.id')
			->where($where)->isezr()->setpages(compact('id', 'keyword', 'category_id', 'ext_property', 'sortby'))
			->sort($sort)->find('a.*, ac.name as category_name');
		$sharepage = SQL::share()->page;
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->smarty->assign('categories', $this->article_mod->categories());
		$this->display();
	}
	
	public function add() {
		$this->edit();
	}
	public function edit() {
		$id = $this->request->get('id', 0);
		if (IS_POST) { //添加
			$id = $this->request->post('id', 0);
			$category_id = $this->request->post('category_id', 0);
			$title = $this->request->post('title');
			$content = $this->request->post('content', '', '\\');
			$memo = $this->request->post('memo');
			$status = $this->request->post('status', 1);
			$sort = $this->request->post('sort', 0);
			$pics = $this->request->post('pics');
			$goods = $this->request->post('goods');
			$ext_property = $this->request->post('ext_property', '', 'origin');
			$pic = $this->request->file('article', 'pic', UPLOAD_LOCAL);
			if (is_array($ext_property)) $ext_property = implode(',', $ext_property);
			$data = compact('title', 'pic', 'content', 'memo', 'sort', 'status', 'category_id', 'ext_property');
			if ($id>0) {
				SQL::share('article')->where($id)->update($data);
			} else {
				$data['add_time'] = time();
				$id = SQL::share('article')->insert($data);
			}
			//文章图片
			SQL::share('article_pic')->delete("article_id='{$id}'");
			if (is_array($pics)) {
				$dpic = array();
				foreach ($pics as $k=>$g) {
					if (!strlen($g)) continue;
					$dpic[] = $g;
				}
				SQL::share('article_pic')->insert(array('article_id'=>$id, 'pic'=>$dpic, 'add_time'=>time()), 'pic');
			}
			//关联商品
			SQL::share('article_goods')->delete("article_id='{$id}'");
			if (is_array($goods)) {
				$dgoods = array();
				foreach ($goods as $k=>$g) {
					if (!strlen($g)) continue;
					$dgoods[] = $g;
				}
				SQL::share('article_goods')->insert(array('article_id'=>$id, 'goods_id'=>$dgoods), 'goods_id');
			}
			location("?app=article&act=edit&id={$id}&msg=1");
		} else if ($id>0) { //显示
			$row = SQL::share('article')->where($id)->row();
			
			$pics = SQL::share('article_pic')->where("article_id='{$id}'")->find();
			
			$goods = SQL::share('goods g')->left('article_goods ag', 'ag.goods_id=g.id')->where("ag.article_id='{$id}'")->find('g.*');
			
			$comments = SQL::share('article_comment c')->left('member m', 'c.member_id=m.id')->where("article_id='{$id}' AND c.parent_id=0")
				->sort('c.id ASC')->find('c.*, m.avatar, m.name as member_name, m.nick_name as member_nick_name');
			if ($comments) {
				foreach ($comments as $k=>$g) {
					$comments[$k]->add_time = get_time_word($g->add_time);
					$replys = SQL::share('article_comment c')->left('member m', 'c.member_id=m.id')->where("c.parent_id='{$g->id}'")
						->sort('c.id ASC')->find('c.id, c.member_id, c.content, c.add_time, m.name as member_name');
					if ($replys) {
						foreach ($replys as $rk=>$rg) {
							$replys[$rk]->add_time = get_time_word($rg->add_time);
							if ($rg->member_id==0) $replys[$rk]->member_name = '客服';
						}
					}
					$comments[$k]->replys = $replys;
				}
			}
		} else {
			$row = t('article');
			$pics = $goods = $comments = array();
		}
		$this->smarty->assign('row', $row);
		$this->smarty->assign('pics', $pics);
		$this->smarty->assign('goods', $goods);
		$this->smarty->assign('comments', $comments);
		$this->smarty->assign('categories', $this->article_mod->categories());
		$this->display('article.edit.html');
	}

	public function upload_pic(){
        $result = $this->request->file('article', 'pic', UPLOAD_LOCAL);
		success($result);
    }

	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		if (SQL::share('article')->delete($id)) {
			SQL::share('article_pic')->delete("article_id='{$id}'");
			//删除该商店被收藏
			SQL::share('favorite')->delete("item_id='{$id}' AND type_id=4");
		}
		header("Location:?app=article&act=index");
	}
	
	//添加评论回复
	public function reply_add() {
		$article_id = $this->request->post('article_id', 0);
		$parent_id = $this->request->post('parent_id', 0);
		$content = $this->request->post('content');
		if ($article_id<=0) error('缺失文章id', -1, 1, true);
		if ($parent_id<=0) error('缺失父评论id', -1, 1, true);
		if (!$content) error('请填写回复内容', -1, 1, true);
		$time = time();
		$id = SQL::share('article_comment')->insert(array('member_id'=>0, 'article_id'=>$article_id, 'parent_id'=>$parent_id, 'content'=>$content,
			'ip'=>$this->ip, 'add_time'=>$time));
		success(array('id'=>$id, 'member_name'=>'客服', 'content'=>$content, 'add_time'=>get_time_word($time)));
	}
	
	//删除评论回复
	public function reply_delete() {
		$id = $this->request->post('id', 0);
		SQL::share('article_comment')->delete($id);
		success('ok');
	}

	//like
	public function likes(){
		$where = '';
		$article_id = $this->request->get('article_id', 0);
		$keyword = $this->request->get('keyword');
		if ($keyword) {
			$where .= " AND (m.name LIKE '%{$keyword}%' OR l.ip LIKE '%{$keyword}%')";
		}
		if ($article_id) {
			$where .= " AND (l.article_id LIKE '%{$article_id}%')";
		}
		$rs = SQL::share('article_like l')->left('member m', 'l.member_id=m.id')->where($where)->isezr()->setpages(compact('article_id', 'keyword'))
			->sort('l.id DESC')->find('l.*, m.name');
		$sharepage = SQL::share()->page;
		$this->smarty->assign('val', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
	//delete
	public function delete_like() {
		$id = $this->request->get('article_id', 0);
		$all = $this->request->get('all', 0);
		$like = $this->request->get('like', 0);
		if ($all==1 && $id>0) {
			if (SQL::share('article_like')->delete("article_id='{$id}'")) {
				SQL::share('article')->where($id)->update(array('likes'=>0));
			} else {
				error('删除失败');
			}
		}
		if ($like>0) {
			if (SQL::share('article_like')->delete("id='{$like}'")) {
				SQL::share('article')->where($id)->update(array('likes'=>array('-1')));
			} else {
				error('删除失败');
			}
		}
		header("Location:?app=article&act=likes&article_id={$id}");
	}
	//commemt
	public function comment(){
		$where = '';
		$article_id = $this->request->get('article_id', 0);
		$keyword = $this->request->get('keyword');
		if ($keyword) {
			$where .= " AND (m.name LIKE '%{$keyword}%' OR l.ip LIKE '%{$keyword}%')";
		}
		if ($article_id) {
			$where .= " AND (l.article_id LIKE '%{$article_id}%' )";
		}
		$rs = SQL::share('article_comment l')->left('member m', 'l.member_id=m.id')->where($where)->isezr()->setpages(compact('article_id', 'keyword'))
			->sort('l.id DESC')->find('l.*, m.name');
		$sharepage = SQL::share()->page;
		$this->smarty->assign('val', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$this->display();
	}
	//comment
	public function delete_comment() {
		$id = $this->request->get('article_id', 0);
		$all = $this->request->get('all', 0);
		$comment = $this->request->get('comment', 0);
		if ($all==1 && $id>0) {
			if (SQL::share('article_comment')->delete("article_id='{$id}'")) {
				SQL::share('article')->where($id)->update(array('comments'=>0));
			} else {
				error('删除失败');
			}
		}
		if ($comment>0) {
			if (SQL::share('article_comment')->delete($comment)) {
				SQL::share('article')->where($id)->update(array('comments'=>array('-1')));
			} else {
				error('删除失败');
			}
		}
		header("Location:?app=article&act=comment&article_id={$id}");
	}
}
