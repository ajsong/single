<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-04 20:09:14
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/goods.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_6183cd6acbd0f8_20782253',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dad59881c996e3538eba76795155d986d57d1ac1' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/goods.index.html',
      1 => 1636027148,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_6183cd6acbd0f8_20782253 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">
		<?php if (isset($_smarty_tpl->tpl_vars['groupbuy']->value) && $_smarty_tpl->tpl_vars['groupbuy']->value == 1) {?>特价拼团
		<?php } elseif (isset($_smarty_tpl->tpl_vars['data']->value['category']->name)) {
echo $_smarty_tpl->tpl_vars['data']->value['category']->name;?>

		<?php } elseif (isset($_smarty_tpl->tpl_vars['ext_property']->value) && $_smarty_tpl->tpl_vars['ext_property']->value == 4) {?>新品发售
		<?php } elseif (isset($_smarty_tpl->tpl_vars['integral']->value) && $_smarty_tpl->tpl_vars['integral']->value == 1) {?>积分商城
		<?php } else { ?>商品列表
		<?php }?>
	</div>
	<?php if (isset($_smarty_tpl->tpl_vars['integral']->value) && $_smarty_tpl->tpl_vars['integral']->value == 1) {?><a class="right" href="/wap/?app=article&act=detail&id=7"><span>积分规则</span></a><?php }?>
</div>

<div class="goods-index">
	<div class="pullRefresh" category_id="<?php if (isset($_smarty_tpl->tpl_vars['category_id']->value)) {
echo $_smarty_tpl->tpl_vars['category_id']->value;
}?>">
		<?php if ((!isset($_smarty_tpl->tpl_vars['integral']->value) || $_smarty_tpl->tpl_vars['integral']->value != 1) && (!isset($_smarty_tpl->tpl_vars['groupbuy']->value) || $_smarty_tpl->tpl_vars['groupbuy']->value != 1)) {?>
		<header class="<?php if (isset($_smarty_tpl->tpl_vars['brand_id']->value) && $_smarty_tpl->tpl_vars['brand_id']->value > 0) {?>brandHeader<?php }?>">
			<?php if (isset($_smarty_tpl->tpl_vars['brand_id']->value) && $_smarty_tpl->tpl_vars['brand_id']->value > 0) {?>
			<div class="brand ge-bottom ge-light">
				<div class="banner" style="background-image: url(<?php echo $_smarty_tpl->tpl_vars['data']->value['brand']->banner;?>
);"></div>
				<div class="title"><div class="pic" style="background-image: url(<?php echo $_smarty_tpl->tpl_vars['data']->value['brand']->pic;?>
);"></div><span><?php echo $_smarty_tpl->tpl_vars['data']->value['brand']->name;?>
</span></div>
			</div>
			<?php }?>
			<ul class="switchView ge-bottom ge-light">
				<li <?php if (!isset($_smarty_tpl->tpl_vars['order_field']->value)) {?>class="this"<?php }?>>
					<a href="/wap/?app=goods&act=index<?php if (isset($_smarty_tpl->tpl_vars['category_id']->value)) {?>&category_id=<?php echo $_smarty_tpl->tpl_vars['category_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['ext_property']->value)) {?>&ext_property=<?php echo $_smarty_tpl->tpl_vars['ext_property']->value;
}
if (isset($_smarty_tpl->tpl_vars['title']->value)) {?>&title=<?php echo $_smarty_tpl->tpl_vars['title']->value;
}?>">综合</a>
				</li>
				<li <?php if (isset($_smarty_tpl->tpl_vars['order_field']->value) && $_smarty_tpl->tpl_vars['order_field']->value == 'sales') {?>class="this"<?php }?>>
					<a href="/wap/?app=goods&act=index<?php if (isset($_smarty_tpl->tpl_vars['category_id']->value)) {?>&category_id=<?php echo $_smarty_tpl->tpl_vars['category_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['ext_property']->value)) {?>&ext_property=<?php echo $_smarty_tpl->tpl_vars['ext_property']->value;
}?>&order_field=sales<?php if (isset($_smarty_tpl->tpl_vars['order_sort']->value)) {?>&order_sort=<?php if ($_smarty_tpl->tpl_vars['order_sort']->value == 'desc') {?>asc<?php } else { ?>desc<?php }
}
if (isset($_smarty_tpl->tpl_vars['title']->value)) {?>&title=<?php echo $_smarty_tpl->tpl_vars['title']->value;
}?>">销量</a>
				</li>
				<li <?php if (isset($_smarty_tpl->tpl_vars['order_field']->value) && $_smarty_tpl->tpl_vars['order_field']->value == 'price') {?>class="this"<?php }?>>
					<a href="/wap/?app=goods&act=index<?php if (isset($_smarty_tpl->tpl_vars['category_id']->value)) {?>&category_id=<?php echo $_smarty_tpl->tpl_vars['category_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['ext_property']->value)) {?>&ext_property=<?php echo $_smarty_tpl->tpl_vars['ext_property']->value;
}?>&order_field=price<?php if (isset($_smarty_tpl->tpl_vars['order_sort']->value)) {?>&order_sort=<?php if ($_smarty_tpl->tpl_vars['order_sort']->value == 'desc') {?>asc<?php } else { ?>desc<?php }
} else { ?>&order_sort=asc<?php }
if (isset($_smarty_tpl->tpl_vars['title']->value)) {?>&title=<?php echo $_smarty_tpl->tpl_vars['title']->value;
}?>">价格</a>
				</li>
				<li>
					<a class="filter" href="javascript:void(0)">筛选</a>
				</li>
			</ul>
		</header>
		<?php }?>
		<ul class="list goods-item">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['goods'], 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li>
				<a href="/wap/?app=goods&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;
if (isset($_smarty_tpl->tpl_vars['integral']->value) && $_smarty_tpl->tpl_vars['integral']->value == 1) {?>&integral=1<?php }?>">
					<div class="pic" url="<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
"></div>
					<div class="title">
						<div><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</div>
						<?php if (isset($_smarty_tpl->tpl_vars['integral']->value) && $_smarty_tpl->tpl_vars['integral']->value == 1) {?>
						<font class="btn"><b>立即兑换</b></font>
						<span class="integral"><?php echo $_smarty_tpl->tpl_vars['g']->value->integral;?>
积分</span>
						<?php } else { ?>
						<font><?php if ((!isset($_smarty_tpl->tpl_vars['integral']->value) || $_smarty_tpl->tpl_vars['integral']->value != 1) && (!isset($_smarty_tpl->tpl_vars['groupbuy']->value) || $_smarty_tpl->tpl_vars['groupbuy']->value != 1)) {
if ($_smarty_tpl->tpl_vars['g']->value->purchase_price > 0) {?>正在秒杀中<?php }
}?></font>
						<span><strong>￥<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->price,2,'.','');?>
</strong><s>￥<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->market_price,2,'.','');?>
</s></span>
						<?php }?>
					</div>
				</a>
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</ul>
	</div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
var offset = $('.pullRefresh .list > li').length;
function createHtml(g){
	var html = '<li>\
		<a href="/wap/?app=goods&act=detail&id='+g.id+'<?php if (isset($_smarty_tpl->tpl_vars['integral']->value) && $_smarty_tpl->tpl_vars['integral']->value == 1) {?>&integral=1<?php }?>">\
			<div class="pic" url="'+g.id+'"></div>\
			<div class="title">\
				<div>'+g.name+'</div>\
				<?php if (isset($_smarty_tpl->tpl_vars['integral']->value) && $_smarty_tpl->tpl_vars['integral']->value == 1) {?><font class="btn"><b>立即兑换</b></font>\
				<span class="integral">'+g.integral+'积分</span><?php } else { ?>\
				<font><?php if ((!isset($_smarty_tpl->tpl_vars['integral']->value) || $_smarty_tpl->tpl_vars['integral']->value != 1) && (!isset($_smarty_tpl->tpl_vars['groupbuy']->value) || $_smarty_tpl->tpl_vars['groupbuy']->value != 1)) {?>'+(g.purchase_price>0?'正在秒杀中':'')+'<?php }?></font>\
				<span><strong>￥'+g.price.numberFormat(2)+'</strong><s>￥'+g.market_price.numberFormat(2)+'</s></span><?php }?>\
			</div>\
		</a>\
	</li>';
	offset++;
	return html;
}
function setLists(){
	//var width = Math.floor(($('.pullRefresh').width()-4) / 2);
	//$('.list li').width(width);
	$('.list a .pic').loadbackground();
}
$(window).resize(setLists);
$(function(){
	setLists();
	//$('header').sticky({ scroller:$('.pullRefresh') });
	$('.switchView').switchView({ column:'column', index:$('.switchView .this').index() });
	<?php if ((!isset($_smarty_tpl->tpl_vars['integral']->value) || $_smarty_tpl->tpl_vars['integral']->value != 1) && (!isset($_smarty_tpl->tpl_vars['groupbuy']->value) || $_smarty_tpl->tpl_vars['groupbuy']->value != 1)) {?>$('.filter').on(window.eventType, getFilter);<?php }?>
	$('.pullRefresh').pullRefresh({
		header : true,
		footer : true,
		footerNoMoreText : '- END -',
		refresh : function(fn){
			var _this = this;
			offset = 0;
			$.getJSON('/api/?app=goods&act=index<?php if (isset($_smarty_tpl->tpl_vars['integral']->value) && $_smarty_tpl->tpl_vars['integral']->value == 1) {?>&integral=1<?php }
if (isset($_smarty_tpl->tpl_vars['category_id']->value)) {?>&category_id=<?php echo $_smarty_tpl->tpl_vars['category_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['ext_property']->value)) {?>&ext_property=<?php echo $_smarty_tpl->tpl_vars['ext_property']->value;
}
if (isset($_smarty_tpl->tpl_vars['brand_id']->value)) {?>&brand_id=<?php echo $_smarty_tpl->tpl_vars['brand_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['shop_id']->value)) {?>&shop_id=<?php echo $_smarty_tpl->tpl_vars['shop_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['min_price']->value)) {?>&min_price=<?php echo $_smarty_tpl->tpl_vars['min_price']->value;
}
if (isset($_smarty_tpl->tpl_vars['max_price']->value)) {?>&max_price=<?php echo $_smarty_tpl->tpl_vars['max_price']->value;
}
if (isset($_smarty_tpl->tpl_vars['order_field']->value)) {?>&order_field=<?php echo $_smarty_tpl->tpl_vars['order_field']->value;
}
if (isset($_smarty_tpl->tpl_vars['order_sort']->value)) {?>&order_sort=<?php echo $_smarty_tpl->tpl_vars['order_sort']->value;
}
if (isset($_smarty_tpl->tpl_vars['groupbuy']->value)) {?>&groupbuy=<?php echo $_smarty_tpl->tpl_vars['groupbuy']->value;
}?>', function(json){
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
			$.getJSON('/api/?app=goods&act=index<?php if (isset($_smarty_tpl->tpl_vars['integral']->value) && $_smarty_tpl->tpl_vars['integral']->value == 1) {?>&integral=1<?php }
if (isset($_smarty_tpl->tpl_vars['category_id']->value)) {?>&category_id=<?php echo $_smarty_tpl->tpl_vars['category_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['ext_property']->value)) {?>&ext_property=<?php echo $_smarty_tpl->tpl_vars['ext_property']->value;
}
if (isset($_smarty_tpl->tpl_vars['brand_id']->value)) {?>&brand_id=<?php echo $_smarty_tpl->tpl_vars['brand_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['shop_id']->value)) {?>&shop_id=<?php echo $_smarty_tpl->tpl_vars['shop_id']->value;
}
if (isset($_smarty_tpl->tpl_vars['min_price']->value)) {?>&min_price=<?php echo $_smarty_tpl->tpl_vars['min_price']->value;
}
if (isset($_smarty_tpl->tpl_vars['max_price']->value)) {?>&max_price=<?php echo $_smarty_tpl->tpl_vars['max_price']->value;
}
if (isset($_smarty_tpl->tpl_vars['order_field']->value)) {?>&order_field=<?php echo $_smarty_tpl->tpl_vars['order_field']->value;
}
if (isset($_smarty_tpl->tpl_vars['order_sort']->value)) {?>&order_sort=<?php echo $_smarty_tpl->tpl_vars['order_sort']->value;
}
if (isset($_smarty_tpl->tpl_vars['groupbuy']->value)) {?>&groupbuy=<?php echo $_smarty_tpl->tpl_vars['groupbuy']->value;
}?>', { offset:offset }, function(json){
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

<?php if ((!isset($_smarty_tpl->tpl_vars['integral']->value) || $_smarty_tpl->tpl_vars['integral']->value != 1) && (!isset($_smarty_tpl->tpl_vars['groupbuy']->value) || $_smarty_tpl->tpl_vars['groupbuy']->value != 1)) {?>
function getFilter(){
	var html = $('.filterView'), has = true;
	if(!html.length){
		has = false;
		html = $('<div class="filterView">\
			<div class="navBar">\
				<div class="titleView-x">筛选</div>\
			</div>\
			<form onSubmit="return false">\
			<ul class="tableView tableView-noMargin tableView-light">\
				<?php if (in_array('category',$_smarty_tpl->tpl_vars['function']->value)) {?><li><h1>\
					<div class="label">类别</div>\
					<div>\
						<?php if (is_array($_smarty_tpl->tpl_vars['data']->value['categories']) && count($_smarty_tpl->tpl_vars['data']->value['categories'])) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['categories'], 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?><span><input type="radio" name="category_id" id="category<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" title="<?php echo str_replace("'","\'",$_smarty_tpl->tpl_vars['g']->value->name);?>
" /><label for="category<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
"><div><?php echo str_replace("'","\'",$_smarty_tpl->tpl_vars['g']->value->name);?>
</div></label></span><?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
} else { ?><span><input type="radio" name="category_id" id="category<?php echo $_smarty_tpl->tpl_vars['category_id']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['category_id']->value;?>
" title="<?php if (isset($_smarty_tpl->tpl_vars['title']->value)) {
echo str_replace("'","\'",$_smarty_tpl->tpl_vars['title']->value);
}?>" /><label for="category<?php echo $_smarty_tpl->tpl_vars['category_id']->value;?>
"><div><?php if (isset($_smarty_tpl->tpl_vars['title']->value)) {
echo str_replace("'","\'",$_smarty_tpl->tpl_vars['title']->value);
}?></div></label></span><?php }?>\
						<div class="clear"></div>\
					</div>\
				</h1></li><?php }?>\
				<li><h1>\
					<div class="label">价格</div>\
					<div>\
						<font>价格区间(元)</font><font><input type="tel" name="min_price" id="min_price" placeholder="最低价" /> - <input type="tel" name="max_price" id="max_price" placeholder="最高价" /></font>\
						<div class="clear"></div>\
					</div>\
				</h1></li>\
				<?php if (is_array($_smarty_tpl->tpl_vars['data']->value['brands'])) {?><li><h1>\
					<div class="label">品牌</div>\
					<div>\
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['brands'], 'g', false, NULL, 'g', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?><span><input type="radio" name="brand_id" id="brand<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" /><label for="brand<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
"><div><?php echo str_replace("'","\'",$_smarty_tpl->tpl_vars['g']->value->name);?>
</div></label></span><?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>\
						<div class="clear"></div>\
					</div>\
				</h1></li><?php }?>\
			</ul>\
			</form>\
			<div class="bottomView ge-top ge-light">\
				<a class="btn" href="javascript:void(0)">确定</a><a class="reset gr" href="javascript:void(0)">重置</a>\
			</div>\
		</div>');
	}
	html.presentView(1);
	if(!has){
		var filterView = $('.filterView');
		filterView.find('.reset').click(function(){
			filterView.find('form')[0].reset();
		});
		filterView.find('.btn').click(function(){
			var category = filterView.find('[name="category_id"]:checked'), brand = filterView.find('[name="brand_id"]:checked'),
				title = category.length ? category.attr('title') : '<?php if (isset($_smarty_tpl->tpl_vars['title']->value)) {
echo $_smarty_tpl->tpl_vars['title']->value;
}?>',
				min_price = filterView.find('#min_price').val(), max_price = filterView.find('#max_price').val(),
				category_id = category.length ? category.val() : '<?php if (isset($_smarty_tpl->tpl_vars['category_id']->value)) {
echo $_smarty_tpl->tpl_vars['category_id']->value;
}?>',
				brand_id = brand.length ? brand.val() : '';
			location.href = '/wap/?app=goods&act=index&category_id='+category_id+'<?php if (isset($_smarty_tpl->tpl_vars['ext_property']->value)) {?>&ext_property=<?php echo $_smarty_tpl->tpl_vars['ext_property']->value;
}?>&brand_id='+brand_id+'&min_price='+min_price+'&max_price='+max_price+'&title='+title.urlencode();
		});
	}
}
<?php }
echo '</script'; ?>
><?php }
}
