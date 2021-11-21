<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-27 14:37:56
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_6178f3c474a762_30110806',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd462be07341d19731009db523f6b4977e069328a' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.index.html',
      1 => 1568193030,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_6178f3c474a762_30110806 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar navBar-hidden">
	<!--<a class="left" href="/wap/?app=cart&act=index"><i class="member-cart badge"><sub></sub></i></a>-->
	<a class="left sign" href="/wap/?app=member&act=sign"><i class="member-sign"></i></a>
	<div class="titleView-x">会员</div>
	<a class="right" href="/wap/?app=member&act=set"><i class="member-set"></i></a>
</div>

<div class="member-index main-bottom width-wrap">
	<div class="topView">
		<div class="infoView">
			<div class="avatar" style="<?php if ($_smarty_tpl->tpl_vars['member']->value->id > 0 && $_smarty_tpl->tpl_vars['member']->value->avatar != '') {?>background-image:url(<?php echo $_smarty_tpl->tpl_vars['member']->value->avatar;?>
);<?php }?>"></div>
			<?php if ($_smarty_tpl->tpl_vars['member']->value->id > 0) {?>
			<span><?php if (strlen($_smarty_tpl->tpl_vars['member']->value->nick_name)) {
echo $_smarty_tpl->tpl_vars['member']->value->nick_name;
} else {
echo $_smarty_tpl->tpl_vars['member']->value->name;
}?></span>
			<?php } else { ?>
			<div class="btnView">
				<a href="/wap/?tpl=login">登录</a>
				<a href="/wap/?tpl=register">注册</a>
			</div>
			<?php }?>
		</div>
		<div class="moneyView">
			<a href="/wap/?app=member&act=money"><div><i></i><span>我的余额<small><?php if ($_smarty_tpl->tpl_vars['member']->value->id > 0) {
echo $_smarty_tpl->tpl_vars['member']->value->money;
} else { ?>0.00<?php }?></small></span></div></a>
			<?php if ($_smarty_tpl->tpl_vars['edition']->value > 2) {?>
			<a class="ge-left" href="/wap/?app=member&act=commission"><div><i class="commission"></i><span>我的佣金<small><?php if ($_smarty_tpl->tpl_vars['member']->value->id > 0) {
echo $_smarty_tpl->tpl_vars['member']->value->commission;
} else { ?>0.00<?php }?></small></span></div></a>
			<?php }?>
		</div>
	</div>

	<section>
		<ul class="tableView tableView-light">
			<li><a href="/wap/?app=order&act=index"><h1><big>全部订单</big><em class="ico1"></em>我的订单</h1></a></li>
			<li class="orderList">
				<a class="ico1 badge" href="/wap/?app=order&act=index&status=0"><div><sub></sub></div></a>
				<a class="ico2 badge" href="/wap/?app=order&act=index&status=1"><div><sub></sub></div></a>
				<a class="ico3 badge" href="/wap/?app=order&act=index&status=2"><div><sub></sub></div></a>
				<a class="ico4 badge" href="/wap/?app=order&act=index&status=3"><div><sub></sub></div></a>
			</li>
		</ul>

		<?php if (in_array('shop',$_smarty_tpl->tpl_vars['function']->value)) {?>
		<ul class="tableView tableView-light ge-top ge-light">
			<li><a href="/wap/?app=member&act=business"><h1><em class="ico12"></em>我是商家</h1></a></li>
		</ul>
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['edition']->value > 2) {?>
		<ul class="tableView tableView-light ge-top ge-light">
			<?php if (in_array('groupbuy',$_smarty_tpl->tpl_vars['function']->value)) {?><li><a href="/wap/?app=groupbuy&act=index"><h1><em class="ico13"></em>我的拼团</h1></a></li><?php }?>
			<?php if (in_array('chop',$_smarty_tpl->tpl_vars['function']->value)) {?><li><a href="/wap/?app=chop&act=index"><h1><em class="ico14"></em>我发起的砍价</h1></a></li><?php }?>
			<?php if (in_array('integral',$_smarty_tpl->tpl_vars['function']->value)) {?><li><a href="/wap/?app=member&act=integral"><h1><em class="ico3"></em>我的积分</h1></a></li>
			<li><a href="/wap/?app=goods&act=index&integral=1"><h1><em class="ico5"></em>积分商城</h1></a></li>
			<li><a href="/wap/?app=order&act=index&integral_order=1"><h1><em class="ico2"></em>积分商城订单</h1></a></li><?php }?>
		</ul>
		<?php }?>

		<ul class="tableView tableView-light ge-top ge-light">
			<?php if (in_array('coupon',$_smarty_tpl->tpl_vars['function']->value)) {?><li><a href="/wap/?app=coupon&act=index&status=1"><h1><?php if ($_smarty_tpl->tpl_vars['data']->value['coupon_count'] > 0) {?><big><?php echo $_smarty_tpl->tpl_vars['data']->value['coupon_count'];?>
张</big><?php }?><em class="ico4"></em>我的优惠券</h1></a></li><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['edition']->value > 2) {?><li><a href="/wap/?app=member&act=code"><h1><em class="ico7"></em>分享赚佣金</h1></a></li><?php }?>
			<li><a href="/wap/?app=message&act=index"><h1><em class="ico8"></em>我的消息</h1></a></li>
			<li><a href="/wap/?app=favorite&act=index&type_id=1"><h1><em class="ico9"></em>我的收藏</h1></a></li>
			<li><a href="/wap/?app=member&act=goods_history"><h1><em class="ico10"></em>足迹</h1></a></li>
		</ul>

		<ul class="tableView tableView-light ge-top ge-light">
			<li><a href="/wap/?app=member&act=set"><h1><em class="ico11"></em>设置</h1></a></li>
		</ul>
	</section>
</div>

<div class="footer">
	<a class="ico1" href="/wap/"></a>
	<a class="ico2" href="/wap/category"></a>
	<a class="ico3" href="/wap/article"></a>
	<a class="ico4 badge" href="/wap/cart"><div><?php if ($_smarty_tpl->tpl_vars['cart_notify']->value > 0) {?><sub><b><?php echo $_smarty_tpl->tpl_vars['cart_notify']->value;?>
</b></sub><?php }?></div></a>
	<a class="ico5 this" href="/wap/member"></a>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
$(function(){
	$('.navBar .badge sub').html('<?php if ($_smarty_tpl->tpl_vars['data']->value['cart_total'] > 0) {?><b><?php echo $_smarty_tpl->tpl_vars['data']->value['cart_total'];?>
</b><?php }?>');
	$('.orderList .ico1 sub').html('<?php if ($_smarty_tpl->tpl_vars['data']->value['not_pay'] > 0) {
echo $_smarty_tpl->tpl_vars['data']->value['not_pay'];
}?>');
	$('.orderList .ico2 sub').html('<?php if ($_smarty_tpl->tpl_vars['data']->value['not_shipping'] > 0) {
echo $_smarty_tpl->tpl_vars['data']->value['not_shipping'];
}?>');
	$('.orderList .ico3 sub').html('<?php if ($_smarty_tpl->tpl_vars['data']->value['not_confirm'] > 0) {
echo $_smarty_tpl->tpl_vars['data']->value['not_confirm'];
}?>');
	$('.orderList .ico4 sub').html('<?php if ($_smarty_tpl->tpl_vars['data']->value['not_comment'] > 0) {
echo $_smarty_tpl->tpl_vars['data']->value['not_comment'];
}?>');
	$('.navBar .sign, .navBar .right, .moneyView a, section a').checklogin();
	$(window).scroll(function(){
		if($(window).scrollTop()>=$('.topView').height()-$('.navBar').height()){
			$('.navBar').removeClass('navBar-hidden');
		}else{
			$('.navBar').addClass('navBar-hidden');
		}
	});
});
<?php echo '</script'; ?>
><?php }
}
