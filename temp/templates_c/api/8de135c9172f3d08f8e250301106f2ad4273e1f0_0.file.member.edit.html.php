<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-28 12:30:28
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.edit.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617a27647b6139_72503881',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8de135c9172f3d08f8e250301106f2ad4273e1f0' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.edit.html',
      1 => 1585185927,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617a27647b6139_72503881 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">个人资料</div>
</div>

<div class="member-edit">
	<ul class="tableView tableView-light tableView-noMargin">
		<li>
			<a class="avatar" href="javascript:void(0)">
				<h1><big><div <?php if (strlen($_smarty_tpl->tpl_vars['data']->value->avatar)) {?>style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['data']->value->avatar;?>
);"<?php }?>></div></big>头像</h1>
			</a>
		</li>
		<li>
			<a id="name" href="javascript:void(0)">
				<h1><big><?php echo $_smarty_tpl->tpl_vars['data']->value->nick_name;?>
</big>昵称</h1>
			</a>
		</li>
		<li>
			<h1><big><?php echo $_smarty_tpl->tpl_vars['data']->value->sex;?>
</big>性别
			<select id="sex">
			<option value="男" <?php if ($_smarty_tpl->tpl_vars['data']->value->sex == '男') {?>selected<?php }?>>男</option>
			<option value="女" <?php if ($_smarty_tpl->tpl_vars['data']->value->sex == '女') {?>selected<?php }?>>女</option>
			</select></h1>
		</li>
		<li>
			<h1><big><?php if ($_smarty_tpl->tpl_vars['data']->value->birth_year > 0) {
echo $_smarty_tpl->tpl_vars['data']->value->birth_year;?>
-<?php echo $_smarty_tpl->tpl_vars['data']->value->birth_month;?>
-<?php echo $_smarty_tpl->tpl_vars['data']->value->birth_day;
}?></big>生日
			<input type="date" id="date" value="<?php if ($_smarty_tpl->tpl_vars['data']->value->birth_year > 0) {
echo $_smarty_tpl->tpl_vars['data']->value->birth_year;?>
-<?php echo $_smarty_tpl->tpl_vars['data']->value->birth_month;?>
-<?php echo $_smarty_tpl->tpl_vars['data']->value->birth_day;
}?>" /></h1>
		</li>
	</ul>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
function selectImage(){
	$('.avatar').ajaxupload({
		url : '/api/?app=member&act=avatar',
		name : 'avatar',
		before : function(){ $.overload() },
		callback : {
			success : function(json, status){
				if(json.error!=0){ $.overloadError(json.msg);return }
				$('.avatar big div').css('background-image', 'url('+json.data+')');
			},
			error : function(xml, status, e){
				alert('Upload error\n'+e);
			}
		}
	});
}
$(function(){
	if(!$.browser.wx)selectImage();
	$('#name').click(function(){
		var _this = $(this);
		$.modalView('昵称', _this.parent().find('big').html(), function(value){
			if(!value.length){
				$.overloadError('不能为空');
				return;
			}
			$.postJSON('/api/?app=member&act=edit', { nick_name:value }, function(json){
				_this.parent().find('big').html(val);
				$.modalView(false);
			});
		});
	});
	$('#sex').change(function(){
		var _this = $(this), val = _this.val();
		$.postJSON('/api/?app=member&act=edit', { sex:val }, function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			_this.parent().find('big').html(val);
		});
	});
	$('#date').change(function(){
		var _this = $(this), val = _this.val(), arr = val.split('-');
		$.postJSON('/api/?app=member&act=edit', { birth_year:arr[0], birth_month:arr[1], birth_day:arr[2] }, function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			_this.parent().find('big').html(val);
		});
	});
});
<?php if (isset($_smarty_tpl->tpl_vars['weixin_sign_package']->value)) {?>
wx.ready(function(){
	$('.avatar').click(function(){
		var _this = $(this);
		wx.chooseImage({
			count : 1,
			success : function(res){
				var localId = res.localIds; //返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
				//$.overload();
				setTimeout(function(){
					wx.uploadImage({
						localId: localId.toString(), //需要上传的图片的本地ID，由chooseImage接口获得
						isShowProgressTips: 0, //默认为1，显示进度提示
						success: function(res){
							var serverId = res.serverId; //返回图片的服务器端ID
							$.postJSON('/api/?app=member&act=avatar', { avatar:serverId }, function(json){
								_this.find('big div').css('background-image', 'url('+json.data+')');
							});
						},
						fail: function(res){
							$.overload(false);
							alert($.debug(res));
						}
					});
				}, 100);
			}
		});
	});
});
<?php }
echo '</script'; ?>
><?php }
}
