<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-25 21:17:34
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/login.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_6176ae6ee673f2_55980038',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8ce0cd48e56c3f28a90c0a484b25dc54edb76d79' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/login.html',
      1 => 1615269793,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_6176ae6ee673f2_55980038 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body>

<div class="navBar navBar-transparent">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">登录</div>
	<a class="right" href="?tpl=register&url=<?php echo urlencode($_smarty_tpl->tpl_vars['url']->value);?>
"><span>注册</span></a>
</div>

<div class="login-index width-wrap">
	<form action="/api/?app=passport&act=login" method="post">
	<input type="hidden" name="gourl" value="<?php if ($_smarty_tpl->tpl_vars['url']->value == '') {?>/wap/?app=member&act=index<?php } else {
echo $_smarty_tpl->tpl_vars['url']->value;
}?>" />
	<ul class="inputView">
		<li class="ge-bottom"><div><i></i><input type="tel" name="mobile" id="mobile" placeholder="请输入手机号码" /></div></li>
		<li class="ge-bottom"><div class="password"><em></em><i></i><input type="password" name="password" id="password" placeholder="请输入密码" /></div></li>
		<div class="buttonView">
			<a href="javascript:void(0)" class="btn">登录</a>
		</div>
		<a href="?tpl=forget" class="forget">忘记密码</a>
	</ul>
	</form>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
function myFn(){
	if(!$('#mobile').val().length || !$('#password').val().length){
		$.overloadError('请输入手机号码与密码');
		return;
	}
	$('form').submit();
}
$(function(){
	$('html, body').addClass('height-wrap');
	$('#password').onkey({
		callback : function(code){
			if(code===13)myFn();
		}
	});
	$('.password em').click(function(){
		var input = $('#password');
		if(!!input.data('show')){
			input.removeData('show');
			input.prop('type', 'password');
			$(this).removeClass('x');
		}else{
			input.data('show', true);
			input.prop('type', 'text');
			$(this).addClass('x');
		}
	});
	$('.btn').click(function(){
		myFn();
		return false;
	});
});
<?php echo '</script'; ?>
><?php }
}
