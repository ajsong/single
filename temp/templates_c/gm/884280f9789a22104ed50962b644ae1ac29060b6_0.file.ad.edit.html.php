<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-01 10:46:29
  from '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/ad.edit.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617f55058edfd2_92622820',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '884280f9789a22104ed50962b644ae1ac29060b6' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/ad.edit.html',
      1 => 1585644334,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617f55058edfd2_92622820 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<style>
ol{ margin-left:15px; }
ol li{ font-size:12px; }
</style>
<div class="page-header">
	<h6>
		广告管理
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
			<?php if ($_smarty_tpl->tpl_vars['row']->value->id) {?>编辑<?php } else { ?>添加<?php }?>广告
		</small>
	</h6>
</div>
<div class="row">
<div class="col-xs-12">
	<form class="form-horizontal" role="form" method="post" action="?app=ad&act=edit" enctype="multipart/form-data">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="name">广告标题</label>
			<div class="col-sm-10">
				<input type="text" id="name" name="name" value="<?php echo $_smarty_tpl->tpl_vars['row']->value->name;?>
" class="col-xs-6" />
				<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['row']->value->id;?>
" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="pic">广告图片</label>
			<div class="col-sm-10">
				<input type="hidden" name="origin_pic" id="origin_pic" value="<?php echo $_smarty_tpl->tpl_vars['row']->value->pic;?>
" />
				<div class="col-file col-xs-3"><input type="file" id="pic" name="pic" value="" /></div>
				<?php if ($_smarty_tpl->tpl_vars['row']->value->pic) {?><a href="<?php echo $_smarty_tpl->tpl_vars['row']->value->pic;?>
" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['row']->value->pic;?>
" height="34" onerror="this.src='/images/nopic.png'" /></a><?php }?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="ad_type">广告类型</label>
			<div class="col-sm-10">
				<select name="ad_type" id="ad_type">
					<option value="">请选择类型</option>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['types']->value, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
" memo='<?php if (strlen($_smarty_tpl->tpl_vars['g']->value->memo)) {
echo $_smarty_tpl->tpl_vars['g']->value->memo;
}?>' <?php if ($_smarty_tpl->tpl_vars['row']->value->ad_type == $_smarty_tpl->tpl_vars['g']->value->name) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['g']->value->value;?>
</option>
					<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</select>
				<span class="some-item"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="ad_content">广告内容</label>
			<div class="col-sm-10">
				<input type="text" id="ad_content" name="ad_content" value="<?php echo $_smarty_tpl->tpl_vars['row']->value->ad_content;?>
" class="col-xs-6" />
				<span class="some-line">可填写id、链接或关键词</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="position">广告位置</label>
			<div class="col-sm-10">
				<select name="position" id="position">
					<option value="">请选择位置</option>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['positions']->value, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
" <?php if ($_smarty_tpl->tpl_vars['row']->value->position == $_smarty_tpl->tpl_vars['g']->value->name) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['g']->value->value;?>
</option>
					<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</select>
			</div>
		</div>
		<?php if ($_smarty_tpl->tpl_vars['edition']->value > 2) {?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="begin_time">显示时间</label>
			<div class="col-sm-10">
				<input type="text" name="begin_time" id="begin_time" placeholder="开始时间" value="<?php if ($_smarty_tpl->tpl_vars['row']->value->begin_time > 0) {
echo $_smarty_tpl->tpl_vars['row']->value->begin_time;
}?>" class="col-xs-2" />
				<span class="some-item">-</span>
				<input type="text" name="end_time" id="end_time" placeholder="结束时间" value="<?php if ($_smarty_tpl->tpl_vars['row']->value->end_time > 0) {
echo $_smarty_tpl->tpl_vars['row']->value->end_time;
}?>" class="col-xs-2" />
				<span class="some-line">不设置即不限制</span>
			</div>
		</div>
		<?php }?>
		<?php if (is_array($_smarty_tpl->tpl_vars['shop']->value) && count($_smarty_tpl->tpl_vars['shop']->value)) {?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="shop_id">所属商家</label>
			<div class="col-sm-10">
				<select name="shop_id" id="shop_id">
					<option value="-1">请选择所属商家</option>
						<option value="0" <?php if ($_smarty_tpl->tpl_vars['row']->value->shop_id == 0) {?>selected<?php }?>>全站广告</option>
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['shop']->value, 's');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['s']->value) {
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['s']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['row']->value->shop_id == $_smarty_tpl->tpl_vars['s']->value->id) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['s']->value->name;?>
</option>
						<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</select>
				<span class="some-line" style="color:red;">（当shop_id>0，为该商家内的广告，shop_id=0，为全站广告）</span>
			</div>
		</div>
		<?php }?>
		<!--
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="channel"> 投放渠道</label>
			<div class="col-sm-2">
				<select name="channel" id="channel" class="form-control">
					<option value="">请选择投放渠道</option>
					<option value="0" <?php if ($_smarty_tpl->tpl_vars['row']->value->channel == 0) {?>selected<?php }?>>全渠道</option>
					<option value="1" <?php if ($_smarty_tpl->tpl_vars['row']->value->channel == 1) {?>selected<?php }?>>苹果+安卓</option>
					<option value="2" <?php if ($_smarty_tpl->tpl_vars['row']->value->channel == 2) {?>selected<?php }?>>苹果</option>
					<option value="3" <?php if ($_smarty_tpl->tpl_vars['row']->value->channel == 3) {?>selected<?php }?>>安卓</option>
					<option value="4" <?php if ($_smarty_tpl->tpl_vars['row']->value->channel == 4) {?>selected<?php }?>>微信</option>
					<option value="5" <?php if ($_smarty_tpl->tpl_vars['row']->value->channel == 5) {?>selected<?php }?>>web</option>
				</select>
			</div>
		</div>
		-->
		<?php if ($_smarty_tpl->tpl_vars['edition']->value > 1) {?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="offer"></label>
			<div class="col-sm-10">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="offer" value="1" class="ace" />
						<span class="lbl"> 同时发送站内消息 </span>
					</label>
				</div>
			</div>
		</div>
		<?php }?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="status">状态</label>
			<div class="col-sm-10">
				<div class="radio">
					<label>
						<input type="radio" name="status" value="1" class="ace" <?php if ($_smarty_tpl->tpl_vars['row']->value->status == 1) {?>checked<?php }?>/>
						<span class="lbl"> 显示 </span>
					</label>
				</div>
				<div class="radio">
					<label>
						<input type="radio" name="status" value="2" class="ace"  <?php if ($_smarty_tpl->tpl_vars['row']->value->status == 2) {?>checked<?php }?>/>
						<span class="lbl"> 隐藏 </span>
					</label>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right" for="sort">排序</label>
			<div class="col-sm-10">
				<input type="text" id="sort" name="sort" value="<?php echo $_smarty_tpl->tpl_vars['row']->value->sort;?>
" class="col-xs-2" />
			</div>
		</div>

		<div class="clearfix form-actions">
			<div class="col-md-offset-3 col-md-9">
				<button class="btn btn-info" type="submit">
					<i class="ace-icon fa fa-check bigger-110"></i>
					提交
				</button>

				&nbsp; &nbsp; &nbsp;
				<button class="btn" type="reset">
					<i class="ace-icon fa fa-undo bigger-110"></i>
					重置
				</button>
			</div>
		</div>
	</form>
</div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
$(function(){
	$('#begin_time').datepicker();
	$('#end_time').datepicker();
	$('#ad_type').change(function(){
		var selected = $(this).selected(), memo = '';
		if(!!selected.attr('memo'))memo = selected.attr('memo');
		$(this).next().html(memo);
	}).change();
});
<?php echo '</script'; ?>
>

<?php }
}
