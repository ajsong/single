<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-14 11:55:16
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/goods.groupbuy.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_619088a47ebf48_30261135',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5a5cd88617016a3649ca29f4b05791ede5abab62' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/goods.groupbuy.html',
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
function content_619088a47ebf48_30261135 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">特价拼团</div>
</div>

<div class="goods-activity goods-groupbuy">
	<div class="pullRefresh">
		<?php if (is_array($_smarty_tpl->tpl_vars['data']->value['goods']) && count($_smarty_tpl->tpl_vars['data']->value['goods'])) {?>
		<ul class="list">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['goods'], 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li>
				<a href="/wap/?app=goods&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
">
					<div class="pic" url="<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
"></div>
					<div class="title"><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</div>
					<div class="content">
						<div class="groupbuy">
							<?php if (is_array($_smarty_tpl->tpl_vars['g']->value->groupbuy_list)) {?>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['g']->value->groupbuy_list, 'm');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['m']->value) {
?>
							<i style="<?php if (strlen($_smarty_tpl->tpl_vars['m']->value->avatar)) {?>background-image:url(<?php echo $_smarty_tpl->tpl_vars['m']->value->avatar;?>
);<?php }?>"></i>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
							<?php }?>
							<span>已团<?php echo $_smarty_tpl->tpl_vars['g']->value->groupbuy_count;?>
件</span>
						</div>
					</div>
					<font class="btn"><b>去拼团</b></font>
					<span class="price"><strong>￥<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->groupbuy_price,2,'.','');?>
</strong><s>￥<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->market_price,2,'.','');?>
</s></span>
				</a>
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</ul>
		<?php } else { ?>
		<div class="norecord">暂时没有任何商品</div>
		<?php }?>
	</div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
var offset = $('.pullRefresh .list > li').length;
function createHtml(g){
	var html = '<li>\
		<a href="/wap/?app=goods&act=detail&id='+g.id+'">\
			<div class="pic" url="'+g.pic+'"></div>\
			<div class="title">'+g.name+'</div>\
			<div class="content">\
				<div class="groupbuy">';
					if($.isArray(g.groupbuy_list)){
						$.each(g.groupbuy_list, function(i, m){
							html += '<i style="'+(m.avatar.length?'background-image:url('+m.avatar+');':'')+'"></i>';
						});
					}
					html += '<span>已团'+g.groupbuy_count+'件</span>\
				</div>\
			</div>\
			<font class="btn"><b>去拼团</b></font>\
			<span class="price"><strong>￥'+g.groupbuy_price.numberFormat(2)+'</strong><s>￥'+g.market_price.numberFormat(2)+'</s></span>\
		</a>\
	</li>';
	offset++;
	return html;
}
function setLists(){
	$('.list a .pic').loadbackground();
}
$(window).resize(setLists);
$(function(){
	setLists();
	$('.pullRefresh').pullRefresh({
		header : true,
		footer : true,
		footerNoMoreText : '- END -',
		refresh : function(fn){
			var _this = this;
			offset = 0;
			$.getJSON('/api/?app=goods&act=groupbuy', function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data.goods))for(var i=0; i<json.data.goods.length; i++)html += createHtml(json.data.goods[i]);
				_this.find('.list').html(html);
				setLists();
				fn();
			});
		},
		load : function(fn){
			var _this = this;
			$.getJSON('/api/?app=goods&act=groupbuy', { offset:offset }, function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data.goods))for(var i=0; i<json.data.goods.length; i++)html += createHtml(json.data.goods[i]);
				_this.find('.list').append(html);
				setLists();
				fn();
			});
		}
	});
});
<?php echo '</script'; ?>
><?php }
}
