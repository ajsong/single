<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-30 11:05:26
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/message.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617cb6765744f8_95133072',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e605eef748290b798fed18cf2bc2c2f6bf6d6e7f' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/message.index.html',
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
function content_617cb6765744f8_95133072 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">
		<?php if (isset($_smarty_tpl->tpl_vars['type']->value) && $_smarty_tpl->tpl_vars['type']->value == 'offer') {?>优惠信息
		<?php } elseif (isset($_smarty_tpl->tpl_vars['type']->value) && $_smarty_tpl->tpl_vars['type']->value == 'logistics') {?>物流通知
		<?php } else { ?>我的消息<?php }?>
	</div>
</div>

<div class="message-index">
	<div class="pullRefresh">
		<ul class="tableView tableView-noMargin list">
			<?php if (count($_smarty_tpl->tpl_vars['data']->value) > 0) {?>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 'g', false, NULL, 'g', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li>
				<h1>
					<a href="javascript:void(0)" mid="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
">
						<div class="view <?php if ($_smarty_tpl->tpl_vars['g']->value->readed == 1) {?>d<?php }?>">
							<div>
								<i></i>
								<?php echo $_smarty_tpl->tpl_vars['g']->value->content;?>

								<span class="scale10-right"><?php echo $_smarty_tpl->tpl_vars['g']->value->add_time;?>
</span>
							</div>
						</div>
					</a>
				</h1>
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			<?php }?>
		</ul>
	</div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
var offset = $('.pullRefresh .list > li').length;
function createHtml(g){
	var html = '<li>\
		<h1>\
			<a href="javascript:void(0)" mid="'+g.id+'">\
				<div class="view '+(g.readed==1?'d':'')+'">\
					<div>\
						<i></i>\
						'+g.content+'\
						<span class="scale10-right">'+g.add_time+'</span>\
					</div>\
				</div>\
			</a>\
		</h1>\
	</li>';
	offset++;
	return html;
}
function setDelete(){
	$('.message-index .tableView').dragshow({
		title : '<i></i>',
		cls : 'delBtn',
		click : function(row){
			var _this = $(this);
			$.post('/api/?app=message&act=delete', { id:row.find('a').attr('mid') }, function(){
				var section = row.parent();
				if(section.find('li').length==1){
					location.href = location.href;
					return;
				}else{
					row.slideUp(200, function(){ row.remove() });
				}
				_this.delay(90).slideUp(200);
			});
		}
	});
}
$(function(){
	setDelete();
	$('.message-index').on('click', '.tableView a', function(){
		var _this = $(this);
		$.postJSON('/api/?app=message&act=read', { id:_this.attr('mid') }, function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			_this.find('.view').addClass('d');
		});
	});
	$('.pullRefresh').pullRefresh({
		header : true,
		footer : true,
		footerNoMoreText : '- END -',
		refresh : function(fn){
			var _this = this;
			offset = 0;
			$.getJSON('/api/?app=message&act=index', function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data))for(var i=0; i<json.data.length; i++)html += createHtml(json.data[i]);
				_this.find('.list').html(html);
				setDelete();
				fn();
			});
		},
		load : function(fn){
			var _this = this;
			$.getJSON('/api/?app=message&act=index', { offset:offset }, function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				var html = '';
				if($.isArray(json.data))for(var i=0; i<json.data.length; i++)html += createHtml(json.data[i]);
				_this.find('.list').append(html);
				setDelete();
				fn();
			});
		}
	});
});
<?php echo '</script'; ?>
><?php }
}
