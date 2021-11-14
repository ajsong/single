<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-22 19:16:43
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/home.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_61729d9b4c5188_83214889',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '41999c19e6f386f5dffec0a0876af1b49ddf25d0' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/home.index.html',
      1 => 1569832537,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_61729d9b4c5188_83214889 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<!--<a class="navDown" href="?tpl=download"><span>下载 APP</span><i></i></a>-->

<div class="navBar">
	<div class="titleView-x search-input"><input type="search" id="keyword" placeholder="请输入商品关键字" /></div>
</div>

<div class="home-index main-padding-bottom">
	<div class="pullRefresh">
		<?php if (count($_smarty_tpl->tpl_vars['data']->value['flashes']) > 0) {?>
		<div class="pageView">
			<div class="slide">
				<ul>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['flashes'], 'g');
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

		<?php if (in_array('category',$_smarty_tpl->tpl_vars['function']->value)) {?>
		<div class="cate">
			<?php if (in_array('groupbuy',$_smarty_tpl->tpl_vars['function']->value)) {?>
			<a class="groupbuy" href="/wap/?app=goods&act=groupbuy"><div></div><span>特价拼团</span></a>
			<?php }?>
			<?php if (in_array('purchase',$_smarty_tpl->tpl_vars['function']->value)) {?>
			<a class="purchase" href="/wap/?app=goods&act=purchase"><div></div><span>限时秒杀</span></a>
			<?php }?>
			<?php if (in_array('chop',$_smarty_tpl->tpl_vars['function']->value)) {?>
			<a class="chop" href="/wap/?app=goods&act=chop"><div></div><span>限量砍价</span></a>
			<?php }?>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['categories'], 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<a href="/wap/?app=goods&act=index&category_id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
&title=<?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
"><div url="<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
"></div><span><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</span></a>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</div>
		<?php }?>

		<div class="tip2 tip"><i></i>好货推荐</div>
		<?php if (count($_smarty_tpl->tpl_vars['data']->value['recommend']) > 0) {?>
		<ul class="list goods-item">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['recommend'], 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li>
				<a href="/wap/?app=goods&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
">
					<div class="pic" url="<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
"></div>
					<div class="title"><div><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</div><font><?php if ($_smarty_tpl->tpl_vars['g']->value->purchase_price > 0) {?>正在秒杀中<?php }?></font><span><strong>￥<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->price,2,'.','');?>
</strong><s>￥<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->market_price,2,'.','');?>
</s></span></div>
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
	<a class="ico1 this" href="/wap/"></a>
	<a class="ico2" href="/wap/category"></a>
	<a class="ico3" href="/wap/article"></a>
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
		<a href="/wap/?app=goods&act=detail&id='+g.id+'">\
	        <div class="pic" url="'+g.pic+'"></div>\
	        <div class="title"><div>'+g.name+'</div><font>'+(g.purchase_price>0?'正在秒杀中':'')+'</font><span><strong>￥'+g.price.numberFormat(2)+'</strong><s>￥'+g.market_price.numberFormat(2)+'</s></span></div>\
	    </a>\
    </li>';
	offset++;
	return html;
}
function setLists(){
	$('.list a .pic').loadbackground();
}
function resize(){
	setLists();
	$('.pageView').autoHeight(320, 153);
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
	$('.cate a div').loadbackground();
	$('#keyword').onkey(function(code){
		if(code==13)location.href = '/wap/?tpl=search&keyword='+$('#keyword').val();
	});
	$('.pullRefresh').pullRefresh({
		header : true,
		footer : true,
		footerNoMoreText : '- END -',
		refresh : function(fn){
			var _this = this;
			offset = 0;
			$.getJSON('/api/?app=home&act=index', function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data.recommend))for(var i=0; i<json.data.recommend.length; i++)html += createHtml(json.data.recommend[i]);
				_this.find('.list').html(html);
				setLists();
				fn();
			});
		},
		load : function(fn){
			var _this = this;
			$.getJSON('/api/?app=home&act=index', { offset:offset }, function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data.recommend))for(var i=0; i<json.data.recommend.length; i++)html += createHtml(json.data.recommend[i]);
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
