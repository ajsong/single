<?php
class category extends core {
	public $goods_mod;
	
	public function __construct() {
		parent::__construct();
		$this->goods_mod = m('goods');
	}

	//index
	public function index() {
		$where = '';
		$id = $this->request->get('id', 0);
		$keyword = $this->request->get('keyword');
		$parent_id = $this->request->get('parent_id', 0);
		$all = $this->request->get('all', 0); //显示所有分类
		if ($id) {
			$where .= "AND id='{$id}'";
		}
		if ($keyword) {
			$where .= "AND (name LIKE '%{$keyword}%' OR keywords LIKE '%{$keyword}%')";
		}
		if (!$all) {
			$where .= " AND parent_id='{$parent_id}'";
		}
		$rs = SQL::share('goods_category')->where($where)->isezr()->setpages(compact('id', 'keyword', 'parent_id', 'all'))->sort('sort ASC, id ASC')
			->find("*, '' as parent_name, NULL as categories, NULL as parents");
		$sharepage = SQL::share()->page;
		if ($rs) {
			foreach ($rs as $key => $g) {
				if ($g->parent_id>0) {
					$rs[$key]->parent_name = SQL::share('goods_category')->where($g->parent_id)->value('name');
				}
				
				$rs[$key]->categories = SQL::share('goods_category')->where("parent_id='{$g->id}'")->find('id, name');
				
				$ids = $this->goods_mod->get_category_parents_tree($g->id);
				$rs[$key]->parents = SQL::share('goods_category')->where("id IN ({$ids})")->find('id, name');
			}
		}
		$this->smarty->assign('rs', $rs);
		$this->smarty->assign('sharepage', $sharepage);
		$categories = $this->goods_mod->get_categories();
		$this->smarty->assign('categories', $this->goods_mod->set_categories_option($categories, $parent_id));
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
			$memo = $this->request->post('memo');
			$keywords = $this->request->post('keywords');
			$status = $this->request->post('status', 0);
			$sort = $this->request->post('sort', 0);
			$parent_id = $this->request->post('parent_id', 0);
			$pic = $this->request->file('cate', 'pic', UPLOAD_LOCAL);
			$data = compact('name', 'memo', 'keywords', 'status', 'sort', 'parent_id', 'pic');
			if ($id > 0) {
				SQL::share('goods_category')->where($id)->update($data);
			} else {
				$id = SQL::share('goods_category')->insert($data);
			}
			location("?app=category&act=index");
		} else if ($id>0) {
			$row = SQL::share('goods_category')->where($id)->row();
		} else {
			$row = t('goods_category');
		}
		$this->smarty->assign('row', $row);
		$categories = $this->goods_mod->get_categories();
		$this->smarty->assign('categories', $this->goods_mod->set_categories_option($categories, $row->parent_id));
		$this->display('category.edit.html');
	}

	//delete
	public function delete() {
		$id = $this->request->get('id', 0);
		SQL::share('goods_category')->delete($id);
		header("Location:?app=category&act=index");
		exit;
	}
}
