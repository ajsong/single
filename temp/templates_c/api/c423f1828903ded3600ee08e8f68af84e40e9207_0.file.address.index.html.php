<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-28 16:23:41
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/address.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617a5e0de5dd48_40726933',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c423f1828903ded3600ee08e8f68af84e40e9207' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/address.index.html',
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
function content_617a5e0de5dd48_40726933 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">收货地址管理</div>
	<a class="right" href="/wap/?app=address&act=add"><span>添加</span></a>
</div>

<div class="address-list">
	<div class="pullRefresh">
		<section>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 'g', false, NULL, 'g', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<ul class="list tableView tableView-noLine">
				<li address_id="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
">
					<h1>
						<div class="name"><?php echo $_smarty_tpl->tpl_vars['g']->value->contactman;?>
　<?php echo $_smarty_tpl->tpl_vars['g']->value->mobile;?>
</div>
						<div class="address"><?php echo $_smarty_tpl->tpl_vars['g']->value->province;
if ($_smarty_tpl->tpl_vars['g']->value->province != $_smarty_tpl->tpl_vars['g']->value->city) {?> <?php echo $_smarty_tpl->tpl_vars['g']->value->city;
}?> <?php echo $_smarty_tpl->tpl_vars['g']->value->district;?>
 <?php echo $_smarty_tpl->tpl_vars['g']->value->address;?>
</div>
					</h1>
					<div class="bottomView ge-top ge-light">
						<form method="post" action="/api/?app=address&act=delete">
						<input type="hidden" name="gourl" value="/wap/?app=address&&act=index" />
						<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" />
						<a href="javascript:void(0)" class="btn delete"><span>删除</span></a>
						</form>
						<a href="/wap/?app=address&act=edit&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" class="btn edit"><span>编辑</span></a>
						<form method="post" action="/api/?app=address&act=set_default">
						<input type="hidden" name="gourl" value="/wap/?app=address&&act=index" />
						<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" />
						<a href="javascript:void(0)" class="default <?php if ($_smarty_tpl->tpl_vars['g']->value->is_default == 1) {?>default-x<?php }?>">设为默认地址</a>
						</form>
					</div>
				</li>
			</ul>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</section>
	</div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
var offset = $('.pullRefresh .list > li').length;
function createHtml(g){
	var html = '<li>\
		<h1>\
			<div class="name">'+g.contactman+'　'+g.mobile+'</div>\
			<div class="address">'+(g.province+(g.province!=g.city?' '+g.city:'')+' '+g.district+' '+g.address)+'</div>\
		</h1>\
		<div class="ge-bottom ge-light"></div>\
		<div class="bottomView">\
			<form method="post" action="/api/?app=address&act=delete">\
			<input type="hidden" name="gourl" value="/wap/?app=address&&act=index" />\
			<input type="hidden" name="id" value="'+g.id+'" />\
			<a href="javascript:void(0)" class="btn delete"><span>删除</span></a>\
			</form>\
			<a href="/wap/?app=address&act=edit&id='+g.id+'" class="btn edit"><span>编辑</span></a>\
			<form method="post" action="/api/?app=address&act=set_default">\
			<input type="hidden" name="gourl" value="/wap/?app=address&&act=index" />\
			<input type="hidden" name="id" value="'+g.id+'" />\
			<a href="javascript:void(0)" class="default">设为默认地址</a>\
			</form>\
		</div>\
	</li>';
	offset++;
	return html;
}
$(function(){
	$('.pullRefresh').pullRefresh({
		header : true,
		footer : true,
		footerNoMoreText : '- END -',
		refresh : function(fn){
			var _this = this;
			offset = 0;
			$.getJSON('/api/?app=address&act=index', function(json){
				if(json.error!=0){ $.overloadError(json.msg);return; }
				var html = '';
				if($.isArray(json.data))for(var i=0; i<json.data.length; i++)html += createHtml(json.data[i]);
				_this.find('.list').html(html);
				fn();
			});
		},
		load : function(fn){
			var _this = this;
			$.getJSON('/api/?app=address&act=index', { offset:offset }, function(json){
				if(json.error!=0){ $.overloadError(json.msg);return; }
				var html = '';
				if($.isArray(json.data))for(var i=0; i<json.data.length; i++)html += createHtml(json.data[i]);
				_this.find('.list').append(html);
				fn();
			});
		}
	});
	$(document).on(window.eventType, '.delete', function(){
		$(this).parent().submit();
	});
	$(document).on('click', '.default', function(){
		$(this).parent().submit();
	});
});
<?php echo '</script'; ?>
><?php }
}
