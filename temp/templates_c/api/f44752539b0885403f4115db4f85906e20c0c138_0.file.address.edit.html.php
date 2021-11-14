<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-29 20:16:41
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/address.edit.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617be629c7fcf8_93058870',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f44752539b0885403f4115db4f85906e20c0c138' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/address.edit.html',
      1 => 1615355420,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617be629c7fcf8_93058870 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">编辑收货地址</div>
</div>

<div class="address-info">
	<form action="/api/?app=address&act=edit" method="post">
	<input type="hidden" name="goalert" id="goalert" value="修改成功" />
	<input type="hidden" name="gourl" id="gourl" value="/wap/?app=address&act=index" />
	<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->id;?>
" />
	<ul class="tableView">
		<li>
			<h1>
				<div class="row">
					<span>收货人姓名</span>
					<input type="text" name="contactman" id="contactman" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->contactman;?>
" placeholder="请输入收货人姓名" />
				</div>
			</h1>
		</li>
		<li>
			<h1>
				<div class="row">
					<span>手机号码</span>
					<input type="tel" name="mobile" id="mobile" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->mobile;?>
" placeholder="请输入手机号码" />
				</div>
			</h1>
		</li>
		<li>
			<h1 class="push-ico">
				<div class="row">
					<span>所在地区</span>
					<div class="selects selects-x">
						<div><?php if (strlen($_smarty_tpl->tpl_vars['data']->value->province)) {
echo $_smarty_tpl->tpl_vars['data']->value->province;
if ($_smarty_tpl->tpl_vars['data']->value->province != $_smarty_tpl->tpl_vars['data']->value->city) {
echo $_smarty_tpl->tpl_vars['data']->value->city;
}
echo $_smarty_tpl->tpl_vars['data']->value->district;
} else { ?>请选择所在地区<?php }?></div>
						<select name="province" id="province"></select>
						<select name="city" id="city"></select>
						<select name="district" id="district"></select>
					</div>
				</div>
			</h1>
		</li>
		<li>
			<h1>
				<div class="row">
					<span>地址</span>
					<input type="text" name="address" id="address" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->address;?>
" placeholder="请输入地址" />
				</div>
			</h1>
		</li>
		<!--<li>
			<h1>
				<div class="row">
					<span>身份证号码</span>
					<input type="tel" name="idcard" id="idcard" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->idcard;?>
" maxlength="18" placeholder="请输入身份证号码" />
				</div>
			</h1>
		</li>-->
	</ul>
	</form>
	<div class="buttonView">
		<a class="btn" href="javascript:void(0)">确定</a>
	</div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 type="text/javascript" src="/js/city.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
$(function(){
	$('.buttonView .btn').click(function(){
		if (!$('#contactman').val().length || !$('#mobile').val().length || !$('#address').val().length) {
			$.overloadError('请填写完整');
			return;
		}
		$('form').submit();
	});
	tipShow = false;
	showCity({ province:'#province', city:'#city', district:'#district', applyName:true, provinceName:'<?php echo $_smarty_tpl->tpl_vars['data']->value->province;?>
', cityName:'<?php echo $_smarty_tpl->tpl_vars['data']->value->city;?>
', districtName:'<?php echo $_smarty_tpl->tpl_vars['data']->value->district;?>
' });
	$('.address-info .selects').onclick(function(){
		$.actionpicker({
			title : '请选择地区',
			rightBtn : {
				text : '确定',
				click : function(){
					$.actionpicker();
				}
			},
			before : function(){
				this.selectpicker({
					objs : [$('#province'), $('#city'), $('#district')],
					cls : 'areapicker',
					autoWidth : false,
					select : function(component, row){
						var html = comboCity($('#province').selected().val(), $('#city').selected().val(), $('#district').selected().val());
						$('.address-info .selects').addClass('selects-x');
						$('.address-info .selects div').html(html);
					}
				});
			}
		});
	});
});
<?php echo '</script'; ?>
><?php }
}
