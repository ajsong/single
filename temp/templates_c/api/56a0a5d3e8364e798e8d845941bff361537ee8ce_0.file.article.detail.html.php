<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-27 15:22:53
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/article.detail.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_6178fe4d9feea7_74111190',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '56a0a5d3e8364e798e8d845941bff361537ee8ce' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/article.detail.html',
      1 => 1592472129,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_6178fe4d9feea7_74111190 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body>

<?php if (isset($_smarty_tpl->tpl_vars['id']->value)) {
if ($_smarty_tpl->tpl_vars['is_app']->value != 1) {?>
<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">
	<?php if (!strlen($_smarty_tpl->tpl_vars['data']->value->mark)) {?>发现详情
	<?php } else { ?>
		<?php if ($_smarty_tpl->tpl_vars['data']->value->mark == 'about') {?>关于我们
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->mark == 'help') {?>帮助中心
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->mark == 'useragree') {?>用户协议
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->mark == 'shopagree') {?>开店协议
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->mark == 'commission') {?>如何获得佣金？
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->mark == 'join') {?>招商加盟
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->mark == 'score') {?>积分规则
		<?php } elseif ($_smarty_tpl->tpl_vars['data']->value->mark == 'coupon') {?>优惠券
		<?php } else { ?>详情
		<?php }?>
	<?php }?></div>
</div>
<?php }?>

<?php if (!strlen($_smarty_tpl->tpl_vars['data']->value->mark)) {?>
<div class="discover-detail">
	<div class="pullRefresh">
		<div class="titleView gr">
			<div class="title"><?php echo $_smarty_tpl->tpl_vars['data']->value->title;?>
</div>
			<div class="time"><span><?php echo $_smarty_tpl->tpl_vars['data']->value->add_time;?>
</span></div>
		</div>
		<div class="content">
			<?php echo $_smarty_tpl->tpl_vars['data']->value->content;?>

		</div>
		<div class="zanView gr">
			<div class="view">
				<i class="<?php if (!is_array($_smarty_tpl->tpl_vars['data']->value->likes_list) || count($_smarty_tpl->tpl_vars['data']->value->likes_list) < 5) {?>hidden<?php }?>"></i>
				<a href="javascript:void(0)" class="zan"><span><?php echo $_smarty_tpl->tpl_vars['data']->value->likes;?>
</span></a>
				<ul>
					<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->likes_list) && count($_smarty_tpl->tpl_vars['data']->value->likes_list)) {?>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->likes_list, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
					<li member_id="<?php echo $_smarty_tpl->tpl_vars['g']->value->member_id;?>
" class="scale-animate" <?php if ($_smarty_tpl->tpl_vars['g']->value->avatar != '') {?>style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['g']->value->avatar;?>
);"<?php }?>></li>
					<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
					<?php }?>
				</ul>
			</div>
		</div>
		<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->goods)) {?>
		<ul class="goodsView">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->goods, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li class="ge-bottom ge-light">
				<a href="/wap/?app=goods&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
">
					<div <?php if ($_smarty_tpl->tpl_vars['g']->value->pic != '') {?>style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
);"<?php }?>></div>
					<span><h1><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</h1></span>
					<font><h1>￥<?php echo $_smarty_tpl->tpl_vars['g']->value->price;?>
</h1></font>
				</a>
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			<div class="qi"></div>
		</ul>
		<?php }?>
		<div class="commentView ge-bottom ge-light">用户评论 <span>(<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->comments_list)) {
echo count($_smarty_tpl->tpl_vars['data']->value->comments_list);
} else { ?>0<?php }?>)</span></div>
		<ul class="list">
			<?php if (is_array($_smarty_tpl->tpl_vars['data']->value->comments_list) && count($_smarty_tpl->tpl_vars['data']->value->comments_list)) {?>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value->comments_list, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li class="ge-bottom ge-light">
				<div class="infoView">
					<font><?php echo $_smarty_tpl->tpl_vars['g']->value->add_time;?>
</font>
					<div <?php if ($_smarty_tpl->tpl_vars['g']->value->avatar != '') {?>style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['g']->value->avatar;?>
);"<?php }?>></div>
					<span><?php echo $_smarty_tpl->tpl_vars['g']->value->member_name;?>
</span>
				</div>
				<div class="memo"><?php echo $_smarty_tpl->tpl_vars['g']->value->content;?>
</div>
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			<?php }?>
		</ul>
	</div>

	<div class="commentPost toolBar ge-top">
		<div>
			<a href="javascript:void(0)"></a>
			<form method="post" action="/api/?app=article&act=post_comment">
			<input type="hidden" name="gourl" value="/wap/?app=article&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['data']->value->id;?>
" />
			<input type="hidden" name="article_id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value->id;?>
" />
			<span><input type="text" name="content" id="content" /></span>
			</form>
		</div>
	</div>
</div>
<?php } else { ?>
<div class="article-detail <?php if ($_smarty_tpl->tpl_vars['is_app']->value != 1) {?>main-top<?php }?> <?php if ($_smarty_tpl->tpl_vars['id']->value == 12) {?>course<?php }?>">
	<?php echo $_smarty_tpl->tpl_vars['data']->value->content;?>

</div>
<?php if ($_smarty_tpl->tpl_vars['id']->value == 12) {?>
<div class="course-bottom">
	<?php if ($_smarty_tpl->tpl_vars['member']->value->id > 0) {?>
	<a href="javascript:void(0)" class="confirms">确认教程</a>
	<?php } else { ?>
	<a href="<?php if ($_smarty_tpl->tpl_vars['is_app']->value != 1) {?>/wap/?tpl=register<?php } else { ?>bangfang://register<?php }?>" class="register">注册账号</a>
	<?php }?>
</div>
<?php }
}?>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
if (!strlen($_smarty_tpl->tpl_vars['data']->value->mark)) {
echo '<script'; ?>
 type="text/javascript" src="/js/emojiView/emojiView.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
var emojiView = null;
var offset = $('.pullRefresh .list > li').length;
function createHtml(g){
	var html = '<li class="ge-bottom ge-light">\
		<div class="infoView">\
			<font>'+g.add_time+'</font>\
			<div '+(g.avatar!=''?'style="background-image:url('+g.avatar+');"':'')+'></div>\
			<span>'+g.member_name+'</span>\
		</div>\
		<div class="memo">'+g.content+'</div>\
	</li>';
	offset++;
	return html;
}
function setEmoji(){
	$('.list .memo').emojiView();
	//setTimeout(function(){ $('.list .memo').emojiView(true) }, 3000); //反解析
}
function myFn(){
	if(!$.checklogin())return;
	if(!$('#content').val().length){
		$.overloadError('请输入您的评论');
		return;
	}
	$('form').submit();
}
$(function(){
	setEmoji();
	emojiView = $.emojiView({
		selectFn : function(mark){
			$('#content').val($('#content').val()+mark);
		},
		deleteFn : function(){
			$('#content').deleteEmoji();
		},
		sendFn : function(){
			myFn();
		}
	});
	$('.commentPost a').click(function(){
		if(emojiView.isAppear){
			emojiView.close('.commentPost');
		}else{
			emojiView.show('.commentPost');
		}
	});
	$('.content img').each(function(){
		if($(this).width()>300)$(this).removeAttr('width').removeAttr('height').css({ width:'100%', height:'' });
	});
	$('.zan').click(function(){
		if(!$('#member_id').val().length || Number($('#member_id').val())<=0){
			location.href = '/wap/?tpl=login&url=' + location.href.urlencode();
			return;
		}
		var _this = $(this);
		$.postJSON('/api/?app=article&act=like', { article_id:'<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
' }, function(json){
			_this.find('span').html(json.data);
			var ul = _this.next(), li = ul.find('[member_id="<?php echo $_smarty_tpl->tpl_vars['member']->value->id;?>
"]');
			if(li.length){
				li.addClass('scale-animate-0');
				setTimeout(function(){ li.remove() }, 300);
			}else{
				li = $('<li member_id="<?php echo $_smarty_tpl->tpl_vars['member']->value->id;?>
" class="scale-animate scale-animate-0" <?php if ($_smarty_tpl->tpl_vars['member']->value->avatar != '') {?>style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['member']->value->avatar;?>
);"<?php }?>></li>');
				ul.prepend(li);
				setTimeout(function(){ li.removeClass('scale-animate-0') }, 0);
			}
			if(ul.find('li').length>4){
				_this.prev().removeClass('hidden');
			}else{
				_this.prev().addClass('hidden');
			}
		});
	});
	$('#content').placeholder('请输入您的评论').onkey({
		callback : function(code){
			if(code==13)myFn();
		}
	});
	$('.pullRefresh').pullRefresh({
		header : true,
		footer : true,
		footerNoMoreText : '- END -',
		refresh : function(fn){
			var _this = this;
			offset = 0;
			$.getJSON('/api/?app=article&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
', function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data.comments_list))for(var i=0; i<json.data.comments_list.length; i++)html += createHtml(json.data.comments_list[i]);
				_this.find('.list').html(html);
				setEmoji();
				fn();
			});
		},
		load : function(fn){
			var _this = this;
			$.getJSON('/api/?app=article&act=index&id=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
', { offset:offset }, function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data.comments_list))for(var i=0; i<json.data.comments_list.length; i++)html += createHtml(json.data.comments_list[i]);
				_this.find('.list').append(html);
				setEmoji();
				fn();
			});
		}
	});
});
<?php echo '</script'; ?>
>
<?php } else {
if ($_smarty_tpl->tpl_vars['id']->value == 12 && $_smarty_tpl->tpl_vars['member']->value->id > 0) {
echo '<script'; ?>
>
$(function(){
	$('.confirms').click(function(){
		$.getJSON('/api/?app=coupon&act=ling&coupon_id=6', function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			$.overloadSuccess('新人优惠券领取成功');
			setTimeout(function(){
				location.href = '<?php if ($_smarty_tpl->tpl_vars['is_app']->value != 1) {?>/wap/?app=coupon&act=index&status=1<?php } else { ?>bangfang://coupon<?php }?>';
			}, 3000);
		});
	});
});
<?php echo '</script'; ?>
>
<?php }
}
}
}
}
