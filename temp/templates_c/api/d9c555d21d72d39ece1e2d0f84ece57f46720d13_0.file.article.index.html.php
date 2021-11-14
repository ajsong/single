<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-31 22:06:00
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/article.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617ea2c87ee715_35596086',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd9c555d21d72d39ece1e2d0f84ece57f46720d13' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/article.index.html',
      1 => 1571210438,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617ea2c87ee715_35596086 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<div class="titleView-x">发现</div>
	<a class="right" href="/wap/?app=article&act=edit"><i class="article-edit"></i></a>
</div>

<div class="article-index main-padding-bottom">
	<div class="pullRefresh">
		<?php if (count($_smarty_tpl->tpl_vars['data']->value['flashes']) > 0) {?>
		<div class="pageView">
			<div class="slide">
				<ul>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['flashes'], 'g', false, NULL, 'g', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
					<li ad_type="<?php echo $_smarty_tpl->tpl_vars['g']->value->ad_type;?>
" ad_content="<?php echo $_smarty_tpl->tpl_vars['g']->value->ad_content;?>
" pic="<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
"></li>
					<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</ul>
			</div>
			<div class="pager"></div>
		</div>
		<?php }?>

		<?php if (count($_smarty_tpl->tpl_vars['data']->value['articles']) > 0) {?>
		<ul class="list">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['articles'], 'g', false, NULL, 'g', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li>
				<a href="/wap/?app=article&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
">
					<div class="title"><div><?php echo $_smarty_tpl->tpl_vars['g']->value->add_time;?>
</div><?php echo $_smarty_tpl->tpl_vars['g']->value->title;?>
</div>
					<div class="content"><?php echo $_smarty_tpl->tpl_vars['g']->value->content;?>
</div>
					<?php if (is_array($_smarty_tpl->tpl_vars['g']->value->pics)) {?>
					<ul class="ge-bottom ge-light">
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['g']->value->pics, 'e');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['e']->value) {
?>
						<li url="<?php echo $_smarty_tpl->tpl_vars['e']->value->pic;?>
"></li>
						<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						<div class="clear"></div>
					</ul>
					<?php }?>
					<div class="bottom">
						<i></i><span><?php echo $_smarty_tpl->tpl_vars['g']->value->likes;?>
</span>
						<i class="comments"></i><span><?php echo $_smarty_tpl->tpl_vars['g']->value->comments;?>
</span>
					</div>
				</a>
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</ul>
		<?php }?>
	</div>
</div>

<div class="footer">
	<a class="ico1" href="/wap/"></a>
	<a class="ico2" href="/wap/category"></a>
	<a class="ico3 this" href="/wap/article"></a>
	<a class="ico4 badge" href="/wap/cart"><div><?php if ($_smarty_tpl->tpl_vars['cart_notify']->value > 0) {?><sub><b><?php echo $_smarty_tpl->tpl_vars['cart_notify']->value;?>
</b></sub><?php }?></div></a>
	<a class="ico5" href="/wap/member"></a>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
var offset = $('.pullRefresh .list > li').length;
function createHtml(g){
	var html = '<li>\
		<a href="/wap/?app=article&act=detail&id='+g.id+'">\
			<div class="title"><div>'+g.add_time+'</div>'+g.title+'</div>\
			<div class="content">'+g.content.replace(/<\/?[^>]+>/g, '').replace(/(\n)+|(\r\n)+/g, '')+'</div>';
			if($.isArray(g.pics)){
			html += '<ul class="ge-bottom ge-light">';
				for(var i=0; i<g.pics.length; i++)html += '<li url="'+g.pics[i].pic+'"></li>';
				html += '<div class="clear"></div>\
			</ul>';
			}
			html += '<div class="bottom">\
				<i></i><span>'+g.likes+'</span>\
				<i class="comments"></i><span>'+g.comments+'</span>\
			</div>\
		</a>\
	</li>';
	offset++;
	return html;
}
function setPics(){
	$('.list a ul').each(function(){
		var _this = $(this);
		if(!!_this.data('changedSize'))return true;
		_this.data('changedSize', true);
		var li = _this.find('li'), width = (_this.outerWidth(true)-10*4) / 3;
		li.css({ width:width, height:width }).loadbackground();
	});
}
function resize(){
	$('.pageView').autoHeight(320, 137);
	$('.pageView li').css({ width:$('.pageView').width(), height:$('.pageView').height() });
	$('.pageView').touchmove({
		pager : '.pager',
		drag : true,
		auto : 4000,
		autoWait : 4000,
		complete : function(){
			$('.pager').css('margin-left', -$('.pager').width()/2);
			setAds('.pageView .slide li');
		}
	});
}
$(window).resize(resize);
$(function(){
	resize();
	setAds('.pageView .slide li');
	setPics();
	$('.navBar .right').checklogin();
	$('.pullRefresh').pullRefresh({
		header : true,
		footer : true,
		footerNoMoreText : '- END -',
		refresh : function(fn){
			var _this = this;
			offset = 0;
			$.getJSON('/api/?app=article&act=index', function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data.articles))for(var i=0; i<json.data.articles.length; i++)html += createHtml(json.data.articles[i]);
				_this.find('.list').html(html);
				setPics();
				fn();
			});
		},
		load : function(fn){
			var _this = this;
			$.getJSON('/api/?app=article&act=index', { offset:offset }, function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data.articles))for(var i=0; i<json.data.articles.length; i++)html += createHtml(json.data.articles[i]);
				_this.find('.list').append(html);
				setPics();
				fn();
			});
		}
	});
});
<?php echo '</script'; ?>
><?php }
}
