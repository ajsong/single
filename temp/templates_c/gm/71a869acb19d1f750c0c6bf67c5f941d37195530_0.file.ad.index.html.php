<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-01 10:45:14
  from '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/ad.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617f54bac4a1b1_67077065',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '71a869acb19d1f750c0c6bf67c5f941d37195530' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/ad.index.html',
      1 => 1594619030,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617f54bac4a1b1_67077065 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="page-header">
	<h6>
		广告管理
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
			广告列表
		</small>
		<div>
			<a href="?app=ad&act=add" class="iframe-layer">添加广告</a>
		</div>
	</h6>
</div>

<form class="form-inline" action="?" method="get">
<input type="hidden" name="app" id="app" value="<?php echo $_smarty_tpl->tpl_vars['app']->value;?>
" />
<input type="hidden" name="act" id="act" value="<?php echo $_smarty_tpl->tpl_vars['act']->value;?>
" />
<input type="text" name="keyword" value="<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
" placeholder="关键词" />
<?php if ($_smarty_tpl->tpl_vars['edition']->value > 2) {?><input type="text" class="form-control" name="begin_time" id="begin_time" placeholder="开始时间" value="<?php echo $_smarty_tpl->tpl_vars['begin_time']->value;?>
" initdate="" style="width:100px;" />
<span class="some-span">-</span>
<input type="text" class="form-control" name="end_time" id="end_time" placeholder="结束时间" value="<?php echo $_smarty_tpl->tpl_vars['end_time']->value;?>
" initdate="" style="width:100px;" /><?php }?>
<select name="ad_type" id="ad_type" class="form-control">
	<option value="">类型</option>
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['types']->value, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
	<option value="<?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
" <?php if ($_smarty_tpl->tpl_vars['ad_type']->value == $_smarty_tpl->tpl_vars['g']->value->name) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['g']->value->value;?>
</option>
	<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</select>
<select name="position" id="position" class="form-control">
	<option value="">位置</option>
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['positions']->value, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
	<option value="<?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
" <?php if ($_smarty_tpl->tpl_vars['position']->value == $_smarty_tpl->tpl_vars['g']->value->name) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['g']->value->value;?>
</option>
	<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</select>
<button type="submit" class="btn btn-info btn-sm">
	<i class="ace-icon fa fa-search bigger-110"></i>搜索
</button>
</form>

<div class="table-content">
<table id="simple-table" class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>图片</th>
			<th>广告名称</th>
			<th>类型</th>
			<th>位置</th>
			<?php if ($_smarty_tpl->tpl_vars['edition']->value > 2) {?><th>开始时间</th>
			<th>结束时间</th><?php }?>
			<th>操作</th>
		</tr>
	</thead>

	<tbody>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['rs']->value, 'row', false, NULL, 'row', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value->id;?>
</td>
			<td style="text-align:center;">
				<a href="?app=ad&act=edit&id=<?php echo $_smarty_tpl->tpl_vars['row']->value->id;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['row']->value->pic;?>
" height="50" onerror="this.src='/images/nopic.png'" /></a>
			</td>
			<td>
				<a href="?app=ad&act=edit&id=<?php echo $_smarty_tpl->tpl_vars['row']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value->name;?>
</a>
			</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value->ad_type;?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value->position;?>
</td>
			<?php if ($_smarty_tpl->tpl_vars['edition']->value > 2) {?><td><?php if ($_smarty_tpl->tpl_vars['row']->value->begin_time) {
echo date('Y-m-d H:i',$_smarty_tpl->tpl_vars['row']->value->begin_time);
} else { ?>-<?php }?></td>
			<td><?php if ($_smarty_tpl->tpl_vars['row']->value->end_time) {
echo date('Y-m-d H:i',$_smarty_tpl->tpl_vars['row']->value->end_time);
} else { ?>-<?php }?></td><?php }?>
			<td>
				<a href="?app=ad&act=edit&id=<?php echo $_smarty_tpl->tpl_vars['row']->value->id;?>
">
					<button type="button" class="btn btn-xs btn-info">
					<i class="ace-icon fa fa-pencil bigger-120"></i>
				</button>
				</a>
				<a href="?app=ad&act=delete&id=<?php echo $_smarty_tpl->tpl_vars['row']->value->id;?>
" class="delete">
					<button type="button" class="btn btn-xs btn-danger">
					<i class="ace-icon far fa-trash-alt bigger-120"></i>
				</button>
				</a>
			</td>
		</tr>
		<?php
}
} else {
?>
		<tr bgcolor="white"><td colspan="13" height="50">暂无记录</td></tr>
		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

	</tbody>
</table>
</div>
<div class="mypage">
	共 <?php echo $_smarty_tpl->tpl_vars['sharepage']->value['num_records'];?>
 个记录  <?php echo $_smarty_tpl->tpl_vars['sharepage']->value['current_page'];?>
 / <?php echo $_smarty_tpl->tpl_vars['sharepage']->value['num_pages'];?>
 页  <?php echo $_smarty_tpl->tpl_vars['sharepage']->value['first_page'];?>
 <?php echo $_smarty_tpl->tpl_vars['sharepage']->value['prev'];?>
 <?php echo $_smarty_tpl->tpl_vars['sharepage']->value['nav'];?>
 <?php echo $_smarty_tpl->tpl_vars['sharepage']->value['next'];?>
 <?php echo $_smarty_tpl->tpl_vars['sharepage']->value['last_page'];?>

</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
$(function(){
	$('#begin_time').datepicker({ readonly:false });
	$('#end_time').datepicker({ readonly:false });
});
<?php echo '</script'; ?>
><?php }
}
