<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-14 15:06:05
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/forget.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_6190b55d1eae61_71305284',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a47a2b2d346f2eb3ff711809addfd97f9ade89a7' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/forget.html',
      1 => 1567565419,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_6190b55d1eae61_71305284 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">忘记密码</div>
</div>

<div class="forget-index">
	<form action="?app=forget&act=code" method="post">
	<input type="hidden" name="action" value="<?php if (isset($_smarty_tpl->tpl_vars['action']->value) && strlen($_smarty_tpl->tpl_vars['action']->value)) {
echo $_smarty_tpl->tpl_vars['action']->value;
} else { ?>?app=forget&act=password<?php }?>" />
	<input type="hidden" id="code" name="code" />
	<div class="tip">请输入您注册时的手机号码</div>
	<div class="inputView">
		<div><i></i><input type="text" name="mobile" id="mobile" placeholder="手机号码" /></div>
	</div>
	<!--<div class="code">
		<a href="javascript:void(0)"><span>发送短信</span></a>
		<div class="inputView"><div><i></i><input type="text" name="code" id="code" placeholder="验证码" /></div></div>
	</div>-->
	<div class="buttonView">
		<a href="javascript:void(0)" class="btn">发送验证码</a>
	</div>
	</form>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
var _mobileText = '', _codeText = '', _getCode = false, _count = 0, _timer = null;
$(function(){
	/*$('.code a').click(function(){
		if(_getCode)return false;
		if(!$('#mobile').val().length){
			$.overloadError('请输入手机号码');
			return false;
		}
		var _this = $(this).html('<div class="preloader"></div>');
		_getCode = true;
		_mobileText = _codeText = '';
		$.postJSON('/api/?app=passport&act=forget_sms', { mobile:$('#mobile').val() }, function(json){
			if(json.error!=0){
				$.overloadError(json.msg);
				return;
			}
			console.log(json.data.code);
			_mobileText = json.data.mobile;
			_codeText = json.data.code;
			_count = 60;
			_timer = setInterval(function(){
				_count--;
				if (_count<=0) {
					clearInterval(_timer);_getCode = false;
					_this.removeClass('disabled').html('<span>再次发送</span>');
				} else {
					_this.addClass('disabled').html('<span>'+_count+'s后重发</span>');
				}
			}, 1000);
		});
	});*/
	$('.btn').click(function(){
		if(!$('#mobile').val().length){
			$.overloadError('请输入手机号码');
			return false;
		}
		/*
		if(!$('#code').val().length){
			$.overloadError('请输入验证码');
			return false;
		}
		if($('#code').val()!=_codeText){
			$.overloadError('验证码不正确');
			return false;
		}
		$('form').submit();
		*/
		$.postJSON('/api/?app=passport&act=forget_sms', { mobile:$('#mobile').val() }, function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			$('#code').val(json.data.code);
			$('form').submit();
		});
	});
});
<?php echo '</script'; ?>
><?php }
}
