<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-01 10:36:35
  from '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/header.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617f52b3537f59_34578874',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fa7d9332e9daa051f23687df0d44fcea8595f178' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/header.html',
      1 => 1616462462,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:menu.html' => 1,
  ),
),false)) {
function content_617f52b3537f59_34578874 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php echo $_smarty_tpl->tpl_vars['WEB_NAME']->value;?>
 - BOARD</title>

		<meta name="description" content="top menu &amp; navigation" />
		<meta name="viewport" content="width=320, initial-scale=1.0, maximum-scale=1.0" />
		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="/css/ace/css/bootstrap.css" />
		<link rel="stylesheet" href="/css/ace/css/font-awesome.css" />
		<link rel="stylesheet" href="/css/ace/fontawesome-5.7.2/css/all.css" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="/css/ace/css/ace-fonts.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="/css/ace/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="/css/ace/css/ace-part2.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="/css/ace/css/ace-ie.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<?php echo '<script'; ?>
 src="/css/ace/js/ace-extra.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/js/jquery-3.4.1.min.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/js/coo.js?import=coo.pc,coo.mobile"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/css/gm/js/common.js"><?php echo '</script'; ?>
>
		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->
		<?php echo '<script'; ?>
 src="/css/ace/js/ace/elements.scroller.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/css/ace/js/ace.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/css/ace/js/bootstrap.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/js/ckeditor/ckeditor.js"><?php echo '</script'; ?>
>

		<!--[if lte IE 8]>
		<?php echo '<script'; ?>
 src="/css/ace/js/html5shiv.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/css/ace/js/respond.js"><?php echo '</script'; ?>
>
		<![endif]-->
		<link rel="stylesheet" href="/css/gm/css/custom.css" />
		<?php if ($_smarty_tpl->tpl_vars['admin']->value->menu_direction == 1) {?><link rel="stylesheet" href="/css/gm/css/theme.css" /><?php }?>

        <?php echo '<script'; ?>
 src="/js/city.js"><?php echo '</script'; ?>
>
		<link rel="stylesheet" href="/css/alertUI.css" />
		<link rel="stylesheet" href="/css/datepicker.css" />

		<link type="text/css" href="/js/photoswipe/photoswipe.css" rel="stylesheet" />
		<link type="text/css" href="/js/photoswipe/default-skin/default-skin.css" rel="stylesheet" />
		<?php echo '<script'; ?>
 type="text/javascript" src="/js/photoswipe/photoswipe.min.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 type="text/javascript" src="/js/photoswipe/photoswipe-ui-default.min.js"><?php echo '</script'; ?>
>

	</head>

	<body class="no-skin">
		<!-- #section:basics/navbar.layout -->
		<div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar">
			<?php echo '<script'; ?>
 type="text/javascript">
				try { ace.settings.check('navbar' , 'fixed') } catch(e) { }
			<?php echo '</script'; ?>
>

			<div class="navbar-container container" id="navbar-container">
				<div class="navbar-header pull-left">
					<!-- #section:basics/navbar.layout.brand -->
					<a href="?" class="navbar-brand" style="<?php if ($_smarty_tpl->tpl_vars['admin']->value->menu_direction == 1 && strlen($_smarty_tpl->tpl_vars['admin']->value->bgcolor)) {?>background:<?php echo $_smarty_tpl->tpl_vars['admin']->value->bgcolor;?>
 !important;<?php }?>">
						<small>
							<?php echo $_smarty_tpl->tpl_vars['WEB_NAME']->value;?>

						</small>
					</a>

					<a href="javascript:void(0)" class="side-hidden el-icon-d-arrow-left"></a>
					<a href="javascript:void(0)" class="refresh el-icon-refresh-right"></a>

					<!-- /section:basics/navbar.layout.brand -->

					<!-- #section:basics/navbar.toggle -->
					<button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse" data-target=".navbar-buttons,.navbar-menu">
						<span class="sr-only">Toggle user menu</span>

						<img src="/css/ace/images/avatar.png" alt="Jason's Photo" />
					</button>

					<button class="pull-right navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#sidebar">
						<span class="sr-only">Toggle sidebar</span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>
					</button>

					<!-- /section:basics/navbar.toggle -->
				</div>

				<!-- #section:basics/navbar.dropdown -->
				<div class="navbar-buttons navbar-header pull-right  collapse navbar-collapse" role="navigation">
					<ul class="nav ace-nav">
						<li class="transparent">
							<a  href="?app=home&act=info">
								欢迎您，<?php if (strlen($_smarty_tpl->tpl_vars['admin']->value->real_name)) {
echo $_smarty_tpl->tpl_vars['admin']->value->real_name;
} else {
echo $_smarty_tpl->tpl_vars['admin']->value->name;
}?>
							</a>
						</li>
						<!-- #section:basics/navbar.user_menu -->
						<!--
						<li class="light-blue user-min">
							<a href="?app=home&act=message">
								<i class="ace-icon fa fa-envelope"></i>
							</a>
							<?php echo '<script'; ?>
 type="text/javascript">
								function polling_message(){
									$.getJSON('<?php echo $_smarty_tpl->tpl_vars['GM_PATH']->value;?>
api/home/polling_message', function(json){
										var envelope = $('.navbar-buttons .fa-envelope'), parent = envelope.parent();
										if(json.data.count>0){
											if(!parent.find('span').length)parent.append('<span>'+json.data.count+'</span>');
											else parent.find('span').html(json.data.count);
											envelope.addClass('icon-animated-vertical');
										}
										else{
											parent.find('span').remove();
											envelope.removeClass('icon-animated-vertical');
										}
										if(json.data.alert.length)alert(json.data.alert);
										setTimeout(polling_message, 3000);
									});
								}
								if(window.top.document==window.document){
									$(function(){ polling_message() });
								}
							<?php echo '</script'; ?>
>
						</li>
						-->
						<?php if ($_smarty_tpl->tpl_vars['admin']->value->super == 1) {?>
						<li class="light-blue user-min">
							<a class="setting_clear" href="javascript:void(0)">
								<i class="ace-icon far fa-trash-alt"></i>
								缓存
							</a>
						</li>
						<?php echo '<script'; ?>
>
						$('.setting_clear').on('click', function(){
							$.overload();
							$.get('<?php echo $_smarty_tpl->tpl_vars['GM_PATH']->value;?>
api/setting/clear', function(){
								$.overloadSuccess('清除完毕');
							});
						});
						<?php echo '</script'; ?>
>
						<?php }?>
						<li class="light-blue user-min">
							<a href="?app=home&act=info">
								<i class="ace-icon far fa-user"></i>
								资料
							</a>
						</li>
						<li class="light-blue user-min">
							<a href="?app=home&act=password">
								<i class="ace-icon far fa-lock"></i>
								密码
							</a>
						</li>
						<li class="light-blue user-min">
							<a href="?app=home&act=logout">
								<i class="ace-icon fa fa-power-off"></i>
								退出
							</a>
						</li>
						<!-- /section:basics/navbar.user_menu -->
					</ul>
				</div>

				<!-- /section:basics/navbar.dropdown -->
				<nav role="navigation" class="navbar-menu pull-left collapse navbar-collapse">
					<!-- #section:basics/navbar.nav -->
					<!--
					<ul class="nav navbar-nav">
						<li>
							<a href="?app=order&act=index">
								<i class="ace-icon fa fa-envelope"></i>
								新订单
								<span class="badge badge-warning">0</span>
							</a>
						</li>
					</ul>
					-->
					<!-- /section:basics/navbar.nav -->
					<?php if ($_smarty_tpl->tpl_vars['has_order']->value && core::check_permission('order','index')) {?>
					<!-- #section:basics/navbar.form -->
					<form class="navbar-form navbar-left form-search" role="search" name="nav-search" id="nav-search" action="?" method="get">
						<div class="form-group">
							<input type="text" name="keyword" placeholder="搜索订单" />
							<input type="hidden" name="app" value="order" />
							<input type="hidden" name="act" value="index" />
						</div>
						<button type="submit" class="btn btn-mini btn-info2">
							<i class="ace-icon fa fa-search icon-only bigger-110"></i>
						</button>
					</form>
					<!-- /section:basics/navbar.form -->
					<?php }?>
				</nav>
			</div><!-- /.navbar-container -->
		</div>
		<?php echo '<script'; ?>
>
		$('.navbar-header .side-hidden').on('click', function(){
			let _body = $('body');
			if(_body.hasClass('side-hidden')){
				_body.removeClass('side-hidden');
			}else{
				_body.addClass('side-hidden');
			}
		});
		$('.navbar-header .refresh').on('click', function(){
			location.reload();
		});
		<?php echo '</script'; ?>
>
		<!-- /section:basics/navbar.layout -->

		<?php $_smarty_tpl->_subTemplateRender("file:menu.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
