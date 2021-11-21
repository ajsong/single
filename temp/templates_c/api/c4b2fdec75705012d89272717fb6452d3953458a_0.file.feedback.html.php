<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-28 12:46:26
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/feedback.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617a2b22113178_36288339',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c4b2fdec75705012d89272717fb6452d3953458a' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/feedback.html',
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
function content_617a2b22113178_36288339 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">意见反馈</div>
	<a class="right" href="javascript:void(0)"><span>提交</span></a>
</div>

<div class="feedback">
	<form action="/api/?app=other&act=feedback" method="post">
	<input type="hidden" name="goalert" value="非常感谢您的反馈" />
	<input type="hidden" name="gourl" value="/wap/?app=member&act=index" />
	<ul class="tableView tableView-noMargin tableView-light">
		<li><h1><input type="text" name="name" id="name" placeholder="请输入用户昵称" /></h1></li>
		<li><h1><input type="tel" name="mobile" id="mobile" placeholder="请输入手机号码" /></h1></li>
		<li><h1><textarea name="content" id="content" placeholder="请输入你的反馈信息"></textarea></h1></li>
	</ul>
	</form>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
$(function(){
	$('.navBar .right').click(function(){
		if(!$('#content').val().length){
			$.overloadError('请输入内容');
			return false;
		}
		$('form').submit();
	});
});
<?php echo '</script'; ?>
><?php }
}
