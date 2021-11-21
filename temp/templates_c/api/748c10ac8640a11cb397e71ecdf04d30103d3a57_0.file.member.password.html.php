<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-28 12:30:31
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.password.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617a2767c891c6_62617089',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '748c10ac8640a11cb397e71ecdf04d30103d3a57' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.password.html',
      1 => 1615355048,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617a2767c891c6_62617089 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">修改密码</div>
</div>

<div class="member-password">
	<form action="/api/?app=member&act=password" method="post">
	<input type="hidden" name="gourl" value="/wap/?app=member&act=set" />
	<ul class="tableView tableView-noMargin tableView-light">
		<li>
			<h1><div><i></i><input type="password" name="origin_password" id="origin_password" placeholder="请输入旧密码" /></div></h1>
		</li>
		<li>
			<h1><input type="password" name="new_password" id="new_password" placeholder="请输入新密码" /></h1>
		</li>
		<li>
			<h1><input type="password" name="repass" id="repass" placeholder="确认新密码" /></h1>
		</li>
	</ul>
	</form>
	<div class="buttonView">
		<a class="btn pass" href="javascript:void(0)">确定</a>
	</div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
$(function(){
	$('.member-password i').click(function(){
		var input = $('#origin_password');
		if(!!input.data('show')){
			input.removeData('show');
			$('#origin_password, #new_password, #repass').prop('type', 'password');
			$(this).removeClass('x');
		}else{
			input.data('show', true);
			$('#origin_password, #new_password, #repass').prop('type', 'text');
			$(this).addClass('x');
		}
	});
	$('.btn').click(function(){
		if(!$('#origin_password').val().length || !$('#new_password').val().length || !$('#repass').val().length){
			$.overloadError('请填写完整');
			return;
		}
		if($('#new_password').val() != $('#repass').val()){
			$.overloadError('两次新密码不相同');
			return;
		}
		$('form').submit();
	});
});
<?php echo '</script'; ?>
><?php }
}
