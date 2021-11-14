<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-12 22:26:37
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/goods.detail.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_618e799deea793_00442201',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '77460eb33db4607cca6acf134c40d23269017138' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/goods.detail.html',
      1 => 1636722881,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_618e799deea793_00442201 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link type="text/css" href="/js/photoswipe/photoswipe.css" rel="stylesheet" />
<link type="text/css" href="/js/photoswipe/default-skin/default-skin.css" rel="stylesheet" />
<?php echo '<script'; ?>
 type="text/javascript" src="/js/photoswipe/photoswipe.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="/js/photoswipe/photoswipe-ui-default.min.js"><?php echo '</script'; ?>
>
<body class="gr">

<div class="navBar navBar-hidden">
	<a class="left" href="javascript:history.back()"><i class="return-back"></i></a>
	<div class="titleView-x">商品详情</div>
</div>

<div class="goods-detail main-bottom width-wrap">
	<div class="pics">
		<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->pics) && count($_smarty_tpl->tpl_vars['data']->value->pics)) {?>
		<div class="slide">
			<ul>
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->pics, 'p');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['p']->value) {
?>
				<li title="<?php echo $_smarty_tpl->tpl_vars['p']->value->memo;?>
">
					<div>
						<?php if ($_smarty_tpl->tpl_vars['p']->value->video != '') {?>
						<video width="100%" height="100%" poster="<?php echo $_smarty_tpl->tpl_vars['p']->value->pic;?>
" preload="auto" controls>
						<source src="<?php echo $_smarty_tpl->tpl_vars['p']->value->video;?>
" type="video/mp4" />
						您的浏览器不支持 video 标签。
						</video>
						<?php } else { ?>
						<a href="<?php echo $_smarty_tpl->tpl_vars['p']->value->pic;?>
" title="<?php echo $_smarty_tpl->tpl_vars['p']->value->memo;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['p']->value->pic;?>
" /></a>
						<?php }?>
					</div>
				</li>
				<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			</ul>
		</div>
		<div class="pager"></div>
		<?php } else { ?>
		<div class="pic" url="<?php echo $_smarty_tpl->tpl_vars['data']->value->pic;?>
"></div>
		<?php }?>
	</div>
	
	<div class="detail">
		<?php if (!isset($_smarty_tpl->tpl_vars['integral']->value)) {?>
		<?php if ($_smarty_tpl->tpl_vars['data']->value->groupbuy_show == 1) {?>
		<div class="groupbuy clear-after">
			<div class="groupbuyView">
				<div class="groupbuyInfo">已团<?php echo $_smarty_tpl->tpl_vars['data']->value->groupbuy_count;?>
件 - <?php echo $_smarty_tpl->tpl_vars['data']->value->groupbuy_number;?>
人团</div>
				<?php if ($_smarty_tpl->tpl_vars['data']->value->groupbuy_end_time > 0) {?>
				<div class="countdown">距结束 <i>00天00时00分00秒</i></div>
				<input type="hidden" id="now" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->groupbuy_now;?>
" />
				<input type="hidden" id="countdown" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->groupbuy_end_time;?>
" />
				<?php }?>
			</div>
			<div class="price">
				<div class="clear-after">
					<span>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->price,2,'.','');?>
</span>
					<div class="side">
						<s>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->market_price,2,'.','');?>
</s>
						<label><i>限量<?php echo $_smarty_tpl->tpl_vars['data']->value->groupbuy_amount;?>
件</i><b><div>好货限拼，超级低价</div></b></label>
					</div>
				</div>
			</div>
		</div>
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->purchase_show == 1) {?>
		<div class="purchase clear-after">
			<div class="purchaseView">
				<div class="progress">
					<div style="width:<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->purchase_count/$_smarty_tpl->tpl_vars['data']->value->purchase_amount*100,1,'.','');?>
%;"></div>
					<span>已抢<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->purchase_count/$_smarty_tpl->tpl_vars['data']->value->purchase_amount*100,1,'.','');?>
%</span>
				</div>
				<?php if ($_smarty_tpl->tpl_vars['data']->value->purchase_end_time > 0) {?>
				<div class="countdown">距结束 <i>00天00时00分00秒</i></div>
				<input type="hidden" id="now" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->purchase_now;?>
" />
				<input type="hidden" id="countdown" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->purchase_end_time;?>
" />
				<?php }?>
			</div>
			<div class="price">
				<div class="clear-after">
					<span>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->price,2,'.','');?>
</span>
					<div class="side">
						<s>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->origin_price,2,'.','');?>
</s>
						<label><i>限量<?php echo $_smarty_tpl->tpl_vars['data']->value->purchase_amount;?>
件</i><b><div><?php if ($_smarty_tpl->tpl_vars['data']->value->purchase_end_time > 0) {?>限时秒杀<?php } else { ?>正在秒杀<?php }?></div></b></label>
					</div>
				</div>
			</div>
		</div>
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->chop_show == 1) {?>
		<div class="chop clear-after">
			<div class="chopView">
				<div class="chopInfo">已砍<?php echo $_smarty_tpl->tpl_vars['data']->value->chop_count;?>
件</div>
				<?php if ($_smarty_tpl->tpl_vars['data']->value->chop_end_time > 0) {?>
				<div class="countdown">距结束 <i>00天00时00分00秒</i></div>
				<input type="hidden" id="now" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->chop_now;?>
" />
				<input type="hidden" id="countdown" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->chop_end_time;?>
" />
				<?php }?>
			</div>
			<div class="price">
				<div class="clear-after">
					<span>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->price,2,'.','');?>
</span>
					<div class="side">
						<s>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->origin_price,2,'.','');?>
</s>
						<label><i>限量<?php echo $_smarty_tpl->tpl_vars['data']->value->chop_amount;?>
件</i><b><div>限时第一刀半价！</div></b></label>
					</div>
				</div>
			</div>
		</div>
		<?php } else { ?>
		<div class="price">
			<div class="clear-after">
				<span>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->price,2,'.','');?>
</span>
				<div class="side">
					<s>原价￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->market_price,2,'.','');?>
</s>
				</div>
			</div>
		</div>
		<?php }?>
		<div class="name"><?php echo $_smarty_tpl->tpl_vars['data']->value->name;?>
</div>
		<div class="addition">
			<?php if (strlen($_smarty_tpl->tpl_vars['data']->value->description)) {?>
			<div class="description"><?php echo $_smarty_tpl->tpl_vars['data']->value->description;?>
</div>
			<?php }?>
		</div>
		<?php if ($_smarty_tpl->tpl_vars['data']->value->in_shop == 1) {?>
		<div class="shop">
			<div class="shop-img"></div>
			<div class="shop-info">此商品线下门店有售</div>
		</div>
		<?php }?>
		<?php } else { ?>
		<div class="integral"><?php echo $_smarty_tpl->tpl_vars['data']->value->integral;?>
积分</div>
		<div class="name"><?php echo $_smarty_tpl->tpl_vars['data']->value->name;?>
</div>
		<div class="addition">
			<?php if (strlen($_smarty_tpl->tpl_vars['data']->value->description)) {?>
			<div class="description"><?php echo $_smarty_tpl->tpl_vars['data']->value->description;?>
</div>
			<?php }?>
		</div>
		<?php }?>
		<ul class="clear-after">
			<li>月销<?php echo $_smarty_tpl->tpl_vars['data']->value->sales;?>
笔</li>
			<li>人气<?php echo $_smarty_tpl->tpl_vars['data']->value->clicks;?>
</li>
			<li>库存<?php echo $_smarty_tpl->tpl_vars['data']->value->stocks;?>
</li>
			<?php if ($_smarty_tpl->tpl_vars['data']->value->shop) {?><li><?php echo $_smarty_tpl->tpl_vars['data']->value->shop->province;
echo $_smarty_tpl->tpl_vars['data']->value->shop->city;?>
</li><?php }?>
		</ul>
	</div>
	
	<?php if ($_smarty_tpl->tpl_vars['data']->value->groupbuy_show == 1 && is_array($_smarty_tpl->tpl_vars['data']->value->groupbuy_list) && count($_smarty_tpl->tpl_vars['data']->value->groupbuy_list)) {?>
	<div class="groupbuyList">
		<div class="title ge-bottom"><?php echo count($_smarty_tpl->tpl_vars['data']->value->groupbuy_list);?>
位小伙伴正在开团，您可直接参与</div>
		<ul>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->groupbuy_list, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li>
				<a href="javascript:void(0)" parent_id="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
">去拼团</a>
				<div class="info">还差 <?php echo $_smarty_tpl->tpl_vars['g']->value->remain;?>
 人成团<div>剩余 <i>00:00:00</i></div></div>
				<div class="avatar" style="<?php if (strlen($_smarty_tpl->tpl_vars['g']->value->avatar)) {?>background-image:url(<?php echo $_smarty_tpl->tpl_vars['g']->value->avatar;?>
);<?php }?>"></div>
				<?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>

				<input type="hidden" class="groupbuy_list_now" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->groupbuy_now;?>
" />
				<input type="hidden" class="groupbuy_list_countdown" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->groupbuy_end_time;?>
" />
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</ul>
	</div>
	<?php }?>
	
	<?php if (!isset($_smarty_tpl->tpl_vars['integral']->value)) {?>
	<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->coupons)) {?>
	<a href="javascript:void(0)" class="param-list coupons push-ico clear-before">
		<font>优惠</font>
		<span class="sub-list selected"><strong>优惠券</strong><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->coupons, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
echo $_smarty_tpl->tpl_vars['g']->value->name;?>
 <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></span>
		<!--<span class="sub-list selected"><strong>本店活动</strong>满2件打7.5折</span>-->
	</a>
	<?php }?>
	<?php }?>
	
	<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->params)) {?>
	<a href="javascript:void(0)" class="param-list params push-ico">
		<font>参数</font>
		<span class="selected"><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->params, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
echo $_smarty_tpl->tpl_vars['g']->value['name'];?>
 <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></span>
	</a>
	<?php }?>
	
	<?php if (!isset($_smarty_tpl->tpl_vars['integral']->value)) {?>
	<?php if (strlen($_smarty_tpl->tpl_vars['data']->value->spec)) {?>
	<a href="javascript:void(0)" class="param-list spec-param push-ico">
		<font>规格</font>
		<span>选择<?php echo $_smarty_tpl->tpl_vars['data']->value->spec;?>
分类</span>
	</a>
	<?php }?>
	
	<a href="/wap/?app=comment&act=index&goods_id=<?php echo $_smarty_tpl->tpl_vars['data']->value->id;?>
&pagesize=10" class="comments push-ico">
		<font>查看评论</font><div>用户评价</div><span>(<?php echo $_smarty_tpl->tpl_vars['data']->value->comments;?>
)</span>
	</a>
	<?php }?>
	
	<div class="memo">
		<div class="tip">商品详情</div>
		<div class="content">
			<?php echo $_smarty_tpl->tpl_vars['data']->value->content;?>

		</div>
	</div>
	
	<?php if (!isset($_smarty_tpl->tpl_vars['integral']->value)) {?>
	<div class="bottomView toolBar ge-top">
		<?php if ($_smarty_tpl->tpl_vars['data']->value->groupbuy_show == 1) {?>
		<a class="btn groupbuyBtn groupbuy" href="javascript:void(0)">￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->price,2,'.','');?>
<i><?php echo $_smarty_tpl->tpl_vars['data']->value->groupbuy_number;?>
人拼团</i></a>
		<a class="btn groupbuyBtn buy" href="javascript:void(0)">￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->origin_price,2,'.','');?>
<i>单独购买</i></a>
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->chop_show == 1) {?>
		<a class="btn chopBtn chop" href="javascript:void(0)">￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->price,2,'.','');?>
<i>发起砍价</i></a>
		<a class="btn chopBtn buy" href="javascript:void(0)">￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->origin_price,2,'.','');?>
<i>直接购买</i></a>
		<?php } else { ?>
		<a class="btn buy" href="javascript:void(0)"><?php if ($_smarty_tpl->tpl_vars['data']->value->purchase_show == 1) {?>立即秒杀<?php } else { ?>立即购买<?php }?></a>
		<a class="btn add_cart" href="javascript:void(0)">加入购物车</a>
		<?php }?>
		<!--<a class="im" href="javascript:void(0)" member_id="xxx"></a>-->
		<a class="fav <?php if ($_smarty_tpl->tpl_vars['data']->value->favorited == 1) {?>fav-x<?php }?>" href="javascript:void(0)"></a>
		<a class="cart badge" href="/wap/?app=cart&act=index"><sub></sub></a>
	</div>
	<?php } else { ?>
	<a class="integralBtn" href="javascript:void(0)">立即兑换</a>
	<?php }?>
</div>

<?php if (strlen($_smarty_tpl->tpl_vars['data']->value->spec)) {?>
<div class="goods-group goods-spec">
	<div class="picView">
		<div>
			<a href="javascript:void(0)"><b>⊗</b></a>
			<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->pics) && count($_smarty_tpl->tpl_vars['data']->value->pics)) {?>
			<a class="pic" href="<?php echo $_smarty_tpl->tpl_vars['data']->value->pics[0]->pic;?>
" url="<?php echo $_smarty_tpl->tpl_vars['data']->value->pics[0]->pic;?>
" style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['data']->value->pics[0]->pic;?>
);"></a>
			<?php } else { ?>
			<a class="pic" href="<?php echo $_smarty_tpl->tpl_vars['data']->value->pic;?>
" url="<?php echo $_smarty_tpl->tpl_vars['data']->value->pic;?>
" style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['data']->value->pic;?>
);"></a>
			<?php }?>
			<strong>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->price,2,'.','');?>
</strong>
			<font>库存<?php echo $_smarty_tpl->tpl_vars['data']->value->stocks;?>
件</font>
			<span>选择<?php echo $_smarty_tpl->tpl_vars['data']->value->spec;?>
分类</span>
		</div>
	</div>
	<div class="specView">
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->specs, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
		<div class="specGroup clear-after ge-top ge-light">
			<div><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</div>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['g']->value->sub, 's');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['s']->value) {
?>
			<a href="javascript:void(0)" spec_id="<?php echo $_smarty_tpl->tpl_vars['s']->value->id;?>
"><span><?php echo $_smarty_tpl->tpl_vars['s']->value->name;?>
</span></a>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</div>
		<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		<?php if ($_smarty_tpl->tpl_vars['data']->value->groupbuy_show == 0 && $_smarty_tpl->tpl_vars['data']->value->chop_show == 0) {?>
		<div class="specQuantity ge-top ge-light">
			<div>
				<a href="javascript:void(0)" class="minus">-</a>
				<input type="tel" id="quantity" value="1" stocks="<?php echo $_smarty_tpl->tpl_vars['data']->value->stocks;?>
" />
				<a href="javascript:void(0)" class="plus">+</a>
			</div>
			<span>购买数量</span>
		</div>
		<?php }?>
	</div>
	<div class="btnView">
		<?php if ($_smarty_tpl->tpl_vars['data']->value->groupbuy_show == 1) {?>
		<a class="btn specBtn buy" href="javascript:void(0)">单独购买(￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->origin_price,2,'.','');?>
)</a>
		<a class="btn specBtn groupbuy" href="javascript:void(0)">立即开团</a>
		<a class="btn specBtn mergebuy hidden" href="javascript:void(0)">立即拼团</a>
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->chop_show == 1) {?>
		<a class="btn specBtn chopBtn buy" href="javascript:void(0)">直接购买(￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value->origin_price,2,'.','');?>
)</a>
		<a class="btn specBtn chop" href="javascript:void(0)">发起砍价</a>
		<?php } else { ?>
		<a class="btn add_cart spec_cart" href="javascript:void(0)">加入购物车</a>
		<a class="btn buy" href="javascript:void(0)"><?php if ($_smarty_tpl->tpl_vars['data']->value->purchase_show == 1) {?>立即秒杀<?php } else { ?>立即购买<?php }?></a>
		<?php }?>
	</div>
</div>
<?php }?>

<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->coupons)) {?>
<div class="goods-group goods-coupons">
	<div class="title">优惠券</div>
	<div class="coupons-view">
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->coupons, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
		<a href="javascript:void(0)" mid="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" class="coupons-list">
			<div class="text">立即抢</div>
			<div class="info">
				<div>￥<strong><?php echo $_smarty_tpl->tpl_vars['g']->value->coupon_money;?>
</strong><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</div>
				<span><?php echo $_smarty_tpl->tpl_vars['g']->value->min_price_memo;?>
</span>
				<font><?php echo $_smarty_tpl->tpl_vars['g']->value->time_memo;?>
</font>
			</div>
		</a>
		<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	</div>
	<div class="btnView">
		<a class="btn close" href="javascript:void(0)">完成</a>
	</div>
</div>
<?php }?>

<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->params)) {?>
<div class="goods-group goods-params">
	<div class="title">产品参数</div>
	<div class="params-view">
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->params, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
		<div class="params-list ge-bottom ge-light">
			<font><?php echo $_smarty_tpl->tpl_vars['g']->value['name'];?>
</font>
			<span><?php echo $_smarty_tpl->tpl_vars['g']->value['value'];?>
</span>
		</div>
		<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	</div>
	<div class="btnView">
		<a class="btn close" href="javascript:void(0)">完成</a>
	</div>
</div>
<?php }?>

<input type="hidden" id="goods_id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->id;?>
" />

<form action="/wap/?app=cart&act=commit" method="post">
<?php if (!isset($_smarty_tpl->tpl_vars['integral']->value)) {
if ($_smarty_tpl->tpl_vars['data']->value->groupbuy_show == 1) {?><input type="hidden" name="type" id="type" value="1" />
<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->purchase_show == 1) {?><input type="hidden" name="type" id="type" value="2" />
<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->chop_show == 1) {?><input type="hidden" name="type" id="type" value="3" />
<?php } else { ?><input type="hidden" name="type" id="type" value="0" /><?php }
} else { ?><input type="hidden" name="type" id="type" value="0" /><?php }?>
<input type="hidden" name="parent_id" id="parent_id" value="0" />
<input type="hidden" name="goods" id="goods" />
<?php if (isset($_smarty_tpl->tpl_vars['integral']->value)) {?><input type="hidden" name="integral_order" value="1" /><?php }?>
</form>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
(function($){
//抛物线动画
$.fn.parabola = function(options){
	options = $.extend({
		maxTop: 20, //默认顶点高度top值
		speed: 1.0, //移动速度
		start: { }, //top, left, width, height
		end: { }, //参数同上
		after: null //动画完成后执行
	}, options);
	return this.each(function(){
		let _this = $(this), settings = $.extend({}, options), start = settings.start, end = settings.end;
		if(!_this.parent().length)$(document.body).append(_this);
		//运动过程中有改变大小
		if(start.width==null || start.height==null)start = $.extend(start, { width:_this.width(), height:_this.height() });
		_this.css({ margin:'0', position:'fixed', 'z-index':9999, width:start.width, height:start.height });
		//运动轨迹最高点top值
		let vertex_top = Math.min(start.top, end.top) - Math.abs(start.left - end.left) / 3;
		//可能出现起点或者终点就是运动曲线顶点的情况
		if(vertex_top < settings.maxTop)vertex_top = Math.min(settings.maxTop, Math.min(start.top, end.top));
		else vertex_top = Math.min(start.top, $(window).height()/2);
		//运动轨迹在页面中的top值可以抽象成函数 a = curvature, b = vertex_top, y = a * x*x + b;
		let distance = Math.sqrt(Math.pow(start.top - end.top, 2) + Math.pow(start.left - end.left, 2)),
			steps = Math.ceil(Math.min(Math.max(Math.log(distance) / 0.05 - 75, 30), 100) / settings.speed), //元素移动次数
			ratio = start.top === vertex_top ? 0 : -Math.sqrt((end.top - vertex_top) / (start.top - vertex_top)),
			vertex_left = (ratio * start.left - end.left) / (ratio - 1),
			//特殊情况, 出现顶点left==终点left, 将曲率设置为0, 做直线运动
			curvature = end.left === vertex_left ? 0 : (end.top - vertex_top) / Math.pow(end.left - vertex_left, 2);
		settings = $.extend(settings, {
			count: -1, //每次重置为-1
			steps: steps,
			vertex_left: vertex_left,
			vertex_top: vertex_top,
			curvature: curvature
		});
		move();
		function move(){
			let opt = settings, start = opt.start, count = opt.count, steps = opt.steps, end = opt.end;
			//计算left top值
			let left = start.left + (end.left - start.left) * count / steps,
				top = opt.curvature === 0 ? start.top + (end.top - start.top) * count / steps : opt.curvature * Math.pow(left - opt.vertex_left, 2) + opt.vertex_top;
			//运动过程中有改变大小
			if(end.width!==null && end.height!==null){
				let i = steps / 2,
					width = end.width - (end.width - start.width) * Math.cos(count < i ? 0 : (count - i) / (steps - i) * Math.PI / 2),
					height = end.height - (end.height - start.height) * Math.cos(count < i ? 0 : (count - i) / (steps - i) * Math.PI / 2);
				_this.css({ width:width, height:height, 'font-size':Math.min(width, height)+'px' });
			}
			_this.css({ left:left, top:top });
			opt.count++;
			let time = window.requestAnimationFrame(move);
			if (count === steps) {
				window.cancelAnimationFrame(time);
				if($.isFunction(opt.after))opt.after.call(_this);
			}
		}
	});
};
})(jQuery);
function getCart(){
	$.getJSON('/api/?app=cart&act=total', function(json){
		$('.badge sub').html(json.data.quantity>0?'<b>'+json.data.quantity+'</b>':'');
	});
}
function resize(){
	$('.goods-detail .pics, .goods-detail .pics .pic, .goods-detail .slide').autoHeight(640, 640);
}
resize();
$(window).resize(resize);
$(function(){
	if($('.pics .pic').length)$('.pics .pic').loadbackground().photoBrowser();
	else $('.goods-detail .slide').touchmove({
		title : 'title',
		drag : true,
		keydown : true,
		pager : '.pics .pager',
		offset : 'center',
		complete : function(){
			$('.pics li div').loadpic({ fill:this.height() });
			setTimeout(function(){ $('.slide a').photoBrowser() }, 0);
		}
	});
	$(window).scroll(function(){
		if($(window).scrollTop()>=$('.pics').height()-$('.navBar').height()){
			$('.navBar').removeClass('navBar-hidden');
		}else{
			$('.navBar').addClass('navBar-hidden');
		}
	});
	
	if($('.countdown').length){
		var countdown = Number($('#countdown').val()), now = Number($('#now').val()),
			countdownFn = function(){
				var result = countdown - now, r = result;
				if(result<=0){
					$('.countdown i').html('00天00时00分00秒');
					clearInterval(timer);
					timer = null;
					location.href = location.href;
					return;
				}
				var day = Math.floor(r/(60*60*24));
				r = result - day*60*60*24;
				var hour = Math.floor(r/(60*60));
				r -= hour*60*60;
				var minute = Math.floor(r/60);
				r -= minute*60;
				var second = r;
				$('.countdown i').html($.fillZero(day,2)+'天'+$.fillZero(hour,2)+'时'+$.fillZero(minute,2)+'分'+$.fillZero(second,2)+'秒');
				now += 1;
			},
			timer = setInterval(countdownFn, 1000);
		countdownFn();
	}
	
	$('.groupbuy_list_countdown').each(function(){
		var _this = $(this), countdown = Number(_this.val()), now = Number(_this.prev().val()),
			countdownFn = function(){
				var result = countdown - now, r = result;
				if(result<=0){
					_this.parent().find('.info i').html('00:00:00');
					clearInterval(timer);
					timer = null;
					location.href = location.href;
					return;
				}
				var day = Math.floor(r/(60*60*24));
				r = result - day*60*60*24;
				var hour = Math.floor(r/(60*60));
				r -= hour*60*60;
				var minute = Math.floor(r/60);
				r -= minute*60;
				var second = r;
				_this.parent().find('.info i').html($.fillZero(hour,2)+':'+$.fillZero(minute,2)+':'+$.fillZero(second,2));
				now += 1;
			},
			timer = setInterval(countdownFn, 1000);
		countdownFn();
	});
	
	$('.memo .content img').each(function(){
		if($(this).width()>300)$(this).removeAttr('width').removeAttr('height').css({ width:'100%', height:'' });
	});
	
	<?php if (!isset($_smarty_tpl->tpl_vars['integral']->value)) {?>
	getCart();
	$('.price span').priceFont('bigPrice');
	
	$('.spec-param').click(function(){
		$('.goods-spec').presentView();
	});
	$('.specGroup a').click(function(){
		$(this).addClass('this').siblings('a').removeClass('this');
		var spec_id = [], spec_name = [], count = 0, groupCount = $('.specGroup').length;
		$('.specGroup a.this').each(function(){
			if(!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0)return false;
			spec_id.push($(this).attr('spec_id'));
			spec_name.push($(this).text());
			count++;
		});
		if(count!=groupCount)return;
		$.getJSON('/api/?app=goods&act=get_spec', { goods_id:$('#goods_id').val(), spec:spec_id.join(',') }, function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			if(!$.isPlainObject(json.data))return;
			if(json.data.pic.length)$('.picView .pic').attr('url', json.data.pic).css('background-image', 'url('+json.data.pic+')').photoBrowser();
			$('.picView strong').html('￥'+json.data.price.numberFormat(2));
			$('.picView font').html('库存'+json.data.stocks+'件');
			$('.picView span').html('已选 "'+spec_name.join('" "')+'"');
			$('#quantity').attr('stocks', json.data.stocks);
			$('.price span').html('￥'+json.data.price.numberFormat(2)).priceFont('bigPrice');
			$('.spec-param span').addClass('selected').html('已选 "'+spec_name.join('" "')+'"');
		});
	});
	$('.specGroup').each(function(){
		if($(this).find('a').length==1)$(this).find('a').click();
	});
	$('.picView a:eq(0)').click(function(){
		$('.goods-spec').presentView(false);
	});
	if($('.picView .pic').length)$('.picView .pic').photoBrowser();
	
	$('.minus').click(function(){
		var quantity = $('#quantity');
		if(Number(quantity.val())<=1)return;
		quantity.val(Number(quantity.val())-1);
	});
	$('.plus').click(function(){
		var quantity = $('#quantity');
		if(Number(quantity.val())>Number(quantity.attr('stocks'))){
			$.overloadError('该商品规格的库存只剩下'+quantity.attr('stocks')+'件');
			return;
		}
		quantity.val(Number(quantity.val())+1);
	});
	$('#quantity').focus(function(){
		var quantity = $(this);
		if(quantity.val().length)quantity.data('quantity', quantity.val());
	}).blur(function(){
		var quantity = $(this);
		if(!quantity.val().length){
			$.overloadError('请填写数量');
			quantity.focus();
			return;
		}
		if(Number(quantity.val())>Number(quantity.attr('stocks'))){
			$.overloadError('该商品规格的库存只剩下'+quantity.attr('stocks')+'件');
			quantity.val(quantity.data('quantity'));
			quantity.focus();
			return;
		}
	});
	
	<?php if ($_smarty_tpl->tpl_vars['data']->value->purchase_show == 1) {?>
	$.getJSON('/api/?app=goods&act=get_spec', { goods_id:$('#goods_id').val() }, function(json){
		if(json.error!=0){ $.overloadError(json.msg);return }
		if(!$.isPlainObject(json.data))return;
		var spec = json.data.spec.split(',');
		$.each(spec, function(){
			$('.specGroup a[spec_id="'+this+'"]').addClass('this');
		});
		var spec_name = [], count = 0, groupCount = $('.specGroup').length;
		$('.specGroup a.this').each(function(){
			if(!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0)return false;
			spec_name.push($(this).text());
			count++;
		});
		if(count!=groupCount)return;
		if(json.data.pic.length)$('.picView .pic').attr('url', json.data.pic).css('background-image', 'url('+json.data.pic+')').photoBrowser();
		$('.picView strong').html('￥'+json.data.price.numberFormat(2));
		$('.picView font').html('库存'+json.data.stocks+'件');
		$('.picView span').html('已选 "'+spec_name.join('" "')+'"');
		$('#quantity').attr('stocks', json.data.stocks);
		$('.price span').html('￥'+json.data.price.numberFormat(2)).priceFont('bigPrice');
		$('.spec-param span').addClass('selected').html('已选 "'+spec_name.join('" "')+'"');
	});
	<?php }?>
	
	$('.im').click(function(){
		location.href = '/wap/?app=chat&act=talk&member_id='+$(this).attr('member_id');
	});
	
	$('.fav').click(function(){
		var _this = $(this);
		$.postJSON('/api/?app=favorite&act=add', { item_id:'<?php echo $_smarty_tpl->tpl_vars['data']->value->id;?>
', type_id:1 }, function(){
			if(_this.hasClass('fav-x'))_this.removeClass('fav-x');
			else _this.addClass('fav-x');
		});
	});
	
	$('.add_cart').click(function(){
		var spec_id = [], quantity = 1;
		if($('.specGroup').length){
			var count = 0, groupCount = $('.specGroup').length;
			$('.specGroup a.this').each(function(){
				if(!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0) return false;
				spec_id.push($(this).attr('spec_id'));
				count++;
			});
			if(count!=groupCount){
				//$.overloadError('请先把规格选择完整');
				if(!$(this).parents('.goods-spec').length)$('.goods-spec').presentView();
				return;
			}
		}
		if($('#quantity').length){
			if(!$('#quantity').val().length){
				$.overloadError('请填写数量');
				return;
			}
			quantity = $('#quantity').val();
		}
		var _this = $(this), goods = [{ goods_id:$('#goods_id').val(), quantity:quantity, spec:spec_id.join(',') }];
		$.postJSON('/api/?app=cart&act=add', { goods:$.jsonString(goods) }, function(json){
			var dot = $('<div class="badge-dot"></div>'), sub = $('.badge sub');
			sub.show();
			var height = sub.height(), offset = sub.offset(), scrollTop = $(window).scrollTop();
			sub.css('display', '');
			dot.parabola({
				start : {
					left : _this.offset().left + _this.width()/2,
					top : _this.offset().top + _this.height()/2 - scrollTop,
					width : height,
					height : height
				},
				end : {
					left : offset.left,
					top : offset.top - scrollTop,
				},
				after : function(){
					sub.html(json.data.quantity>0?'<b>'+json.data.quantity+'</b>':'');
					this.remove();
				}
			});
		});
	});
	
	$('.buy, .btn.groupbuy, .btn.chop').click(function(){
		if(!$.checklogin())return;
		var spec_id = [], quantity = 1;
		$('#parent_id').val('0');
		$('.mergebuy').addClass('hidden').siblings().removeClass('hidden');
		$('#quantity').val('1');
		if($('.specGroup').length){
			var count = 0, groupCount = $('.specGroup').length;
			$('.specGroup a.this').each(function(){
				if(!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0) return false;
				spec_id.push($(this).attr('spec_id'));
				count++;
			});
			if(count!=groupCount){
				if(!$(this).parents('.goods-spec').length)$('.goods-spec').presentView();
				return;
			}
		}
		if($('#quantity').length){
			if(!$('#quantity').val().length){
				$.overloadError('请填写数量');
				return;
			}
			quantity = $('#quantity').val();
		}
		var goods = [{ goods_id:$('#goods_id').val(), quantity:quantity, spec:spec_id.join(',') }];
		$('#goods').val($.jsonString(goods));
		if($(this).is('.buy') && $(this).is('.specBtn')){
			$('#type').val('0');
		}else if($(this).is('.groupbuy')){
			$('#type').val('1');
		}else if($(this).is('.purchase')){
			$('#type').val('2');
		}else if($(this).is('.chop')){
			$('#type').val('3');
		}
		$('form').submit();
	});
	
	$('.groupbuyList li a, .mergebuy').click(function(){
		if(!$.checklogin())return;
		var spec_id = [], quantity = 1;
		if(!$(this).is('.mergebuy'))$('.mergebuy').attr('parent_id', $(this).attr('parent_id'));
		$('#parent_id').val($(this).attr('parent_id'));
		$('#quantity').val('1');
		$('.mergebuy').removeClass('hidden').siblings().addClass('hidden');
		if($('.specGroup').length){
			var count = 0, groupCount = $('.specGroup').length;
			$('.specGroup a.this').each(function(){
				if(!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0) return false;
				spec_id.push($(this).attr('spec_id'));
				count++;
			});
			if(count!=groupCount){
				if(!$(this).parents('.goods-spec').length)$('.goods-spec').presentView();
				return;
			}
		}
		if($('#quantity').length){
			if(!$('#quantity').val().length){
				$.overloadError('请填写数量');
				return;
			}
			quantity = $('#quantity').val();
		}
		var goods = [{ goods_id:$('#goods_id').val(), quantity:quantity, spec:spec_id.join(',') }];
		$('#goods').val($.jsonString(goods));
		$('#type').val('1');
		$('form').submit();
	});
	
	<?php } else { ?>
	$('.integralBtn').click(function(){
		if(!$.checklogin())return;
		var goods = [{ goods_id:$('#goods_id').val(), quantity:1 }];
		$('#goods').val($.jsonString(goods));
		$('form').submit();
	});
	<?php }?>
	
	$('.coupons').click(function(){
		$('.goods-coupons').presentView();
	});
	$('.goods-coupons .close').click(function(){
		$('.goods-coupons').presentView(false);
	});
	$('.goods-coupons .coupons-list').click(function(){
		var _this = $(this), id = _this.attr('mid');
		$.getJSON('/api/?app=coupon&act=ling&id='+id, function(json){
			$.overloadSuccess(json.msg);
		});
	});
	
	$('.params').click(function(){
		$('.goods-params').presentView();
	});
	$('.goods-params .close').click(function(){
		$('.goods-params').presentView(false);
	});
});
<?php echo '</script'; ?>
><?php }
}
