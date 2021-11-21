<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-01 10:36:35
  from '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/home.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617f52b350ef68_71204963',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e8e5dae514e381b29df2d941869aa377b0bf8d1b' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/home.index.html',
      1 => 1615184070,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617f52b350ef68_71204963 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="home-view">
	<div class="home-name">
		<div><?php echo $_smarty_tpl->tpl_vars['WEB_NAME']->value;?>
</div>
		<?php if ($_smarty_tpl->tpl_vars['defines']->value->WX_LOGIN_GM == 1 && $_smarty_tpl->tpl_vars['admin']->value->openid == '') {?><a href="?app=core&act=weixin_auth&active=1">绑定微信</a><?php }?>
	</div>
	<div class="col-overview">
		上次登录IP：<?php echo $_smarty_tpl->tpl_vars['admin']->value->last_ip;?>
<br />
		上次登录时间：<?php echo date('Y-m-d H:i:s',$_smarty_tpl->tpl_vars['admin']->value->last_time);?>

	</div>
	<?php if ($_smarty_tpl->tpl_vars['has_order']->value && core::check_permission('order','index')) {?>
	<ul class="col-overview home-overview">
		<li><a href="?app=order&act=index&status=1"><?php echo $_smarty_tpl->tpl_vars['order_status1']->value;?>
</a><span>待发货订单</span></li>
		<li><a href="?app=order&act=index"><?php echo $_smarty_tpl->tpl_vars['order_yesterday']->value;?>
</a><span>昨日订单</span></li>
		<li><a href="?app=order&act=index"><i>￥</i><?php echo number_format($_smarty_tpl->tpl_vars['order_yesterday_money']->value,2,'.','');?>
</a><span>昨日交易额</span></li>
		<li><a href="?app=order&act=index"><?php echo $_smarty_tpl->tpl_vars['order_pay']->value;?>
</a><span>已支付订单</span></li>
	</ul>
	<?php }?>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
