<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-30 14:28:51
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/coupon.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617ce6235271f1_95828110',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ce5188c05b8abbd2899f4451ed9f4e2665c20f3b' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/coupon.index.html',
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
function content_617ce6235271f1_95828110 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="/wap/?app=member&act=index"><i class="return"></i></a>
	<div class="titleView-x">优惠券</div>
	<!--<a class="right" href="javascript:void(0)"><span>添加</span></a>-->
</div>

<div class="coupon-index">
	<div class="pullRefresh">
		<header class="ge-bottom ge-light">
			<ul class="switchView">
				<li <?php if (isset($_smarty_tpl->tpl_vars['status']->value) && $_smarty_tpl->tpl_vars['status']->value == 1) {?>class="this"<?php }?>>
					<a href="/wap/?app=coupon&act=index&status=1">有效</a>
				</li>
				<li <?php if (isset($_smarty_tpl->tpl_vars['status']->value) && $_smarty_tpl->tpl_vars['status']->value == 0) {?>class="this"<?php }?>>
					<a href="/wap/?app=coupon&act=index&status=0">无效</a>
				</li>
			</ul>
		</header>
		
		<ul class="list">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li class="type<?php echo $_smarty_tpl->tpl_vars['g']->value->type;?>
 status<?php echo $_smarty_tpl->tpl_vars['g']->value->status;?>
">
				<a href="<?php if ($_smarty_tpl->tpl_vars['g']->value->status == 1) {?>/wap/?app=category&act=index<?php } else { ?>javascript:void(0)<?php }?>">
					<div class="l">
						<div><font>￥</font><?php echo $_smarty_tpl->tpl_vars['g']->value->coupon_money;?>
</div>
						<span><tt><?php echo $_smarty_tpl->tpl_vars['g']->value->min_price_memo;?>
</tt></span>
					</div>
					<div class="r">
						<strong><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</strong>
						<font><tt><?php echo $_smarty_tpl->tpl_vars['g']->value->memo;?>
</tt></font>
						<span><?php if ($_smarty_tpl->tpl_vars['g']->value->status == 1) {?><div><b>立即使用</b></div><?php }?><span><?php echo $_smarty_tpl->tpl_vars['g']->value->time_memo;?>
</span></span>
					</div>
					<?php if ($_smarty_tpl->tpl_vars['g']->value->type == 0) {?><div class="t t0"></div>
					<?php } elseif ($_smarty_tpl->tpl_vars['g']->value->type == 1) {?><div class="t"><span>品牌</span></div>
					<?php } elseif ($_smarty_tpl->tpl_vars['g']->value->type == 2) {?><div class="t"><span>新人</span></div><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['g']->value->status < 1) {?><div class="p p<?php echo $_smarty_tpl->tpl_vars['g']->value->status;?>
"></div><?php }?>
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
	var html = '<li class="status'+g.status+'">\
		<div class="l">\
			<div>￥'+g.coupon_money+'</div>\
			<span>'+g.min_price_memo+'</span>\
		</div>\
		<div class="r">\
			<div>'+g.name+'<br />'+g.memo+'</div>\
			<span>'+(g.end_time!='0'?'有效期至 '+g.end_time:'长期有效')+'</span>\
		</div>\
		'+(g.status!=1?'<div class="t"></div>':'')+'\
	</li>';
	offset++;
	return html;
}
$(function(){
	$('.switchView').switchView({ column:'column', index:$('.switchView .this').index() });
	$('.navBar .right').click(function(){
		var _this = $(this),
		html = '<div class="coupon-sn" cls="overlay-big" delay-cls="overlay-normal" close-cls="overlay-small">\
			<div class="tip"><span>添加优惠券</span></div>\
			<input type="text" placeholder="请输入优惠券号码" />\
			<a href="javascript:void(0)">确定</a>\
		</div>';
		$.overlay(html, 3, function(){
			$('.coupon-sn a').click(function(){
				var val = $(this).prev().val();
				$.postJSON('/api/?app=coupon&act=add', { sn:val }, function(json){
					if(json.error!=0){ $.overloadError(json.msg);return }
					$.overlay(false);
					$.overloadSuccess('添加成功');
					setTimeout(function(){ location.href=location.href }, 3000);
				});
			});
		});
	});
	$('.pullRefresh').pullRefresh({
		header : true,
		footer : true,
		footerNoMoreText : '- END -',
		refresh : function(fn){
			var _this = this;
			offset = 0;
			$.getJSON('/api/?app=coupon&act=index<?php if (isset($_smarty_tpl->tpl_vars['status']->value)) {?>&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;
}?>', function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data))for(var i=0; i<json.data.length; i++)html += createHtml(json.data[i]);
				_this.find('.list').html(html);
				fn();
			});
		},
		load : function(fn){
			var _this = this;
			$.getJSON('/api/?app=coupon&act=index<?php if (isset($_smarty_tpl->tpl_vars['status']->value)) {?>&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;
}?>', { offset:offset }, function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data))for(var i=0; i<json.data.length; i++)html += createHtml(json.data[i]);
				_this.find('.list').append(html);
				fn();
			});
		}
	});
});
<?php echo '</script'; ?>
><?php }
}
