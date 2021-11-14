<?php
class comment extends core {

	public function __construct() {
		parent::__construct();
	}

	//列出商品评价
	public function index() {
		$goods_id = $this->request->get('goods_id', 0);
		$offset = $this->request->get('offset', 0);
		$pagesize = $this->request->get('pagesize', 8);
		$comments = SQL::share('order_goods og')->left('member m', 'og.member_id=m.id')->where("goods_id='{$goods_id}' AND comment_time>0")
			->sort('comment_time DESC')->limit($offset, $pagesize)
			->find('comment_time, comment_stars, comment_content, m.name as member_name,m.nick_name as member_nick_name, m.avatar as member_avatar');
		if ($comments) {
			foreach ($comments as $key => $comment) {
				$comments[$key]->comment_time = date("Y-m-d H:i:s", $comment->comment_time);
				if(preg_match("/^1[34578]\d{9}$/", $comment->member_name)){
					$comment->member_name = substr_replace($comment->member_name,'*****',3,-3);
				}
			}
		}
		success($comments);
	}

}
