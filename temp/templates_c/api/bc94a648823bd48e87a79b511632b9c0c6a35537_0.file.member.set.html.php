<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-27 15:22:34
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.set.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_6178fe3a2b4242_68742861',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bc94a648823bd48e87a79b511632b9c0c6a35537' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.set.html',
      1 => 1567565420,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_6178fe3a2b4242_68742861 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="/wap/?app=member&act=index"><i class="return"></i></a>
	<div class="titleView-x">更多</div>
</div>

<div class="member-set">
	<section>
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<a href="/wap/?app=member&act=edit"><h1>个人资料</h1></a>
			</li>
			<li>
				<a href="/wap/?app=member&act=password"><h1>修改密码</h1></a>
			</li>
		</ul>
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<a href="/wap/?app=address&act=index"><h1>收货地址管理</h1></a>
			</li>
		</ul>
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<a href="/wap/?tpl=feedback"><h1>我要反馈</h1></a>
			</li>
		</ul>
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<a href="/wap/?app=article&act=detail&id=help"><h1>帮助中心</h1></a>
			</li>
			<!--
			<li>
				<a href="tel://020-36603191"><h1 class="noPush"><big>020-36603191</big>客服电话</h1></a>
			</li>
			-->
			<li>
				<a href="/wap/?app=article&act=detail&id=about"><h1>关于我们</h1></a>
			</li>
			<!--
			<li class="r">
				<a href="/wap/?app=article&act=detail&id=join"><h1>招商加盟</h1></a>
			</li>
			-->
			<li class="r">
				<a href="/api/?app=passport&act=logout&gourl=<?php echo urlencode('/wap/?app=member&act=index');?>
"><h1 class="noPush">退出登录</h1></a>
			</li>
		</ul>
	</section>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
