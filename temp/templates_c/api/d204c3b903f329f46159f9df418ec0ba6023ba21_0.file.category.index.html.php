<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-31 22:06:35
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/category.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617ea2eb1cb919_99501460',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd204c3b903f329f46159f9df418ec0ba6023ba21' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/category.index.html',
      1 => 1568192999,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617ea2eb1cb919_99501460 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<div class="titleView-x">分类</div>
</div>

<div class="category-index main-padding-bottom">
	<div class="pullRefresh sortView">
		<?php if (count($_smarty_tpl->tpl_vars['data']->value['category']) > 0) {?>
		<ul class="list">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['category'], 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li class="ge-bottom ge-light">
				<a href="javascript:void(0)" flashes='<?php echo json_encode($_smarty_tpl->tpl_vars['g']->value->flashes);?>
' categories='<?php echo json_encode($_smarty_tpl->tpl_vars['g']->value->categories);?>
'><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</a>
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</ul>
		<?php }?>
	</div>
	<div class="categoryView"></div>
</div>

<div class="footer">
	<a class="ico1" href="/wap/"></a>
	<a class="ico2 this" href="/wap/category"></a>
	<a class="ico3" href="/wap/article"></a>
	<a class="ico4 badge" href="/wap/cart"><div><?php if ($_smarty_tpl->tpl_vars['cart_notify']->value > 0) {?><sub><b><?php echo $_smarty_tpl->tpl_vars['cart_notify']->value;?>
</b></sub><?php }?></div></a>
	<a class="ico5" href="/wap/member"></a>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
function createHtml(g){
	var html = '<li class="ge-bottom ge-light">\
		<a href="javascript:void(0)" flashes=\''+$.jsonString(g.flashes)+'\' categories=\''+$.jsonString(g.categories)+'\'>'+g.name+'</a>\
	</li>';
	return html;
}
function setCategories(_this){
	if(!_this)_this = $('.sortView a:eq(0)');
	$('.sortView a').removeClass('this');
	_this.addClass('this');
	var html = '', categoryView = $('.categoryView'), flashes = $.json(_this.attr('flashes')), categories = $.json(_this.attr('categories'));
	if($.isArray(flashes) && flashes.length){
		html += '<div class="pageView">\
			<div class="slide">\
				<ul>';
				for(var i=0; i<flashes.length; i++){
					var g = flashes[i];
					html += '<li ad_type="'+g.ad_type+'" ad_content="'+g.ad_content+'" pic="'+g.pic+'"></li>';
				}
				html += '</ul>\
			</div>\
			<div class="pager"></div>\
		</div>';
	}
	if($.isArray(categories) && categories.length){
		html += '<ul class="categoryList">';
		for(var i=0; i<categories.length; i++){
			var g = categories[i];
			html += '<li>\
				<a href="/wap/?app=goods&act=index&category_id='+g.id+'&title='+g.name+'">\
					<i style="background-image:url('+g.pic+');"></i>\
					<span>'+g.name+'</span>\
				</a>\
			</li>';
		}
		html += '</ul>';
	}
	categoryView.html(html);
	if($.isArray(flashes) && flashes.length){
		$('.category-index .pageView').autoHeight(224, 112);
		$('.category-index .pageView li').css({ width:$('.category-index .pageView').width(), height:$('.category-index .pageView').height() });
		$('.category-index .pageView').touchmove({
			pager : '.category-index .pager',
			drag : true,
			auto : 4000,
			autoWait : 4000,
			complete : function(){
				$('.category-index .pager').css('margin-left', -$('.category-index .pager').width()/2);
				setAds('.category-index .pageView .slide li');
			}
		});
	}
}
$(function(){
	setCategories();
	$('.sortView a').click(function(){
		setCategories($(this));
	});
	$('.pullRefresh').pullRefresh({
		header : '<div class="preloader preloader-gray"></div>',
		refresh : function(fn){
			var _this = this;
			$.getJSON('/api/?app=category&act=index', function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data))for(var i=0; i<json.data.length; i++)html += createHtml(json.data[i]);
				_this.find('.list').html(html);
				setCategories();
				fn();
			});
		}
	});
});
<?php echo '</script'; ?>
><?php }
}
