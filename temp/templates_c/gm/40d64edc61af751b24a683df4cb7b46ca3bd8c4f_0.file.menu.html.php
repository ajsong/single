<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-01 10:36:35
  from '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/menu.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617f52b35cbee0_46167621',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '40d64edc61af751b24a683df4cb7b46ca3bd8c4f' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/menu.html',
      1 => 1587713424,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_617f52b35cbee0_46167621 (Smarty_Internal_Template $_smarty_tpl) {
?>
		<div class="main-container container" id="main-container">


			<!-- #section:basics/sidebar.horizontal -->
			<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse" style="<?php if ($_smarty_tpl->tpl_vars['admin']->value->menu_direction == 1 && strlen($_smarty_tpl->tpl_vars['admin']->value->bgcolor)) {?>background:<?php echo $_smarty_tpl->tpl_vars['admin']->value->bgcolor;?>
 !important;<?php }?>">
				<?php echo '<script'; ?>
 type="text/javascript">
					try { ace.settings.check('sidebar' , 'fixed') } catch(e) { }
				<?php echo '</script'; ?>
>

				<ul class="nav nav-list">
					<li class="hover home">
						<a href="?">
							<i class="menu-icon fa fa-home"></i>
							<span class="menu-text">首页</span>
						</a>
					</li>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['nav']->value, 'f');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['f']->value) {
?>
					<li class="hover <?php if (strpos(((',').($_smarty_tpl->tpl_vars['f']->value->app)).(','),((',').($_smarty_tpl->tpl_vars['app']->value)).(',')) !== false) {?>active<?php }?> <?php if (!isset($_smarty_tpl->tpl_vars['f']->value->checked)) {?>hidden<?php }?>">
						<a menu-id="<?php echo $_smarty_tpl->tpl_vars['f']->value->id;?>
" href="<?php if ($_smarty_tpl->tpl_vars['f']->value->url != '') {
echo $_smarty_tpl->tpl_vars['f']->value->url;
} else { ?>javascript:void(0)<?php }?>">
							<?php echo $_smarty_tpl->tpl_vars['f']->value->icon;?>

							<span class="menu-text"><?php echo $_smarty_tpl->tpl_vars['f']->value->name;?>
</span>
							<b class="arrow fa fa-angle-down"></b>
						</a>
						<?php if (!is_array($_smarty_tpl->tpl_vars['f']->value->sub) || !count($_smarty_tpl->tpl_vars['f']->value->sub)) {?>
						<b class="arrow"></b>
						<ul class="submenu">
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['f']->value->sub, 's');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['s']->value) {
?>
							<li class="hover <?php if (strpos(((',').($_smarty_tpl->tpl_vars['s']->value->app)).(','),((',').($_smarty_tpl->tpl_vars['app']->value)).(',')) !== false && strpos(((',').($_smarty_tpl->tpl_vars['s']->value->act)).(','),((',').($_smarty_tpl->tpl_vars['act']->value)).(',')) !== false && (gettype($_smarty_tpl->tpl_vars['s']->value->param) == 'NULL' || strpos($_SERVER['REQUEST_URI'],$_smarty_tpl->tpl_vars['s']->value->param) !== false)) {?>active<?php }?> <?php if (!isset($_smarty_tpl->tpl_vars['s']->value->checked)) {?>hidden<?php }?>">
								<a menu-id="<?php echo $_smarty_tpl->tpl_vars['s']->value->id;?>
" href="<?php echo $_smarty_tpl->tpl_vars['s']->value->url;?>
">
									<i class="menu-icon fa fa-caret-right"></i>
									<?php echo $_smarty_tpl->tpl_vars['s']->value->name;?>

								</a>
								<b class="arrow"></b>
							</li>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</ul>
						<?php }?>
					</li>
					<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</ul><!-- /.nav-list -->

				<!-- #section:basics/sidebar.layout.minimize -->

				<!-- /section:basics/sidebar.layout.minimize -->
				<?php echo '<script'; ?>
 type="text/javascript">
					try { ace.settings.check('sidebar' , 'collapsed') } catch(e) { }
				<?php echo '</script'; ?>
>
			</div>

			<!-- /section:basics/sidebar.horizontal -->
			<div class="main-content <?php if ($_smarty_tpl->tpl_vars['nav_sub']->value == 1) {?>nav_sub<?php }?>">
				<div class="main-content-inner">
					<div class="page-content">
						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS --><?php }
}
