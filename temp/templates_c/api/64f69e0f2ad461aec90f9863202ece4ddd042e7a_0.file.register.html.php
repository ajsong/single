<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-27 15:22:40
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/register.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_6178fe40b394f3_36116124',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '64f69e0f2ad461aec90f9863202ece4ddd042e7a' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/register.html',
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
function content_6178fe40b394f3_36116124 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body>

<div class="navBar navBar-transparent">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">注册</div>
	<a class="right" href="?tpl=login&url=<?php echo urlencode($_smarty_tpl->tpl_vars['url']->value);?>
"><span>登录</span></a>
</div>

<div class="login-index width-wrap">
	<form action="/api/?app=passport&act=register" method="post">
	<input type="hidden" name="gourl" value="<?php if ($_smarty_tpl->tpl_vars['url']->value == '') {?>/wap/?app=member&act=index<?php } else {
echo $_smarty_tpl->tpl_vars['url']->value;
}?>" />
	<ul class="inputView">
		<li class="ge-bottom"><div><i></i><input type="tel" name="mobile" id="mobile" /></div></li>
		<li class="ge-bottom"><div class="code"><a href="javascript:void(0)"><span>发送短信</span></a><i></i><input type="text" name="code" id="code" /></div></li>
		<li class="ge-bottom"><div class="password"><em></em><i></i><input type="password" name="password" id="password" /></div></li>
		<li class="ge-bottom"><div class="invite"><i></i><input type="text" name="invite_code" id="invite_code" /></div></li>
		<div class="buttonView">
			<a href="javascript:void(0)" class="btn">注册</a>
		</div>
		<a href="?app=article&act=detail&id=useragree">注册即视为同意《服务协议》</a>
	</ul>
	</form>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
var _mobileText = '', _codeText = '', _getCode = false, _count = 0, _timer = null;
function myFn(){
	if(!$('#mobile').val().length){
		$.overloadError('请输入手机号码');
		return;
	}
	if(!$('#code').val().length){
		$.overloadError('请输入验证码');
		return;
	}
	if(!$('#password').val().length){
		$.overloadError('请输入密码');
		return;
	}
	if($('#code').val()!=_codeText){
		$.overloadError('验证码不正确');
		return;
	}
	$('form').submit();
}
$(function(){
	$('html, body').addClass('height-wrap');
	$('#mobile').placeholder('请输入手机号码(注册后不可更改)');
	$('#code').placeholder('请输入验证码');
	$('#password').placeholder('请输入密码').onkey({
		callback : function(code){
			if(code==13)myFn();
		}
	});
	$('#invite_code').placeholder('请输入邀请码');
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
	$('.code a').click(function(){
		if(_getCode)return false;
		if(!$('#mobile').val().length){
			$.overloadError('请输入手机号码');
			return false;
		}
		var _this = $(this).html('<div class="preloader"></div>');
		_getCode = true;
		_mobileText = _codeText = '';
		$.postJSON('/api/?app=passport&act=check_mobile', { mobile:$('#mobile').val() }, function(json){
			_mobileText = json.data.mobile;
			_codeText = json.data.code;
			_count = 60;
			_timer = setInterval(function(){
				_count--;
				if (_count<=0) {
					clearInterval(_timer);_timer = null;_getCode = false;
					_this.removeClass('disabled').html('<span>再次发送</span>');
				} else {
					_this.addClass('disabled').html('<span>'+_count+'s后重发</span>');
				}
			}, 1000);
		});
	});
	$('.btn').click(function(){
		myFn();
		return false;
	});
});
<?php echo '</script'; ?>
><?php }
}
