<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-01 10:36:19
  from '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/home.login.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617f52a31ebe38_50123951',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd5d907723abc2995836b3b71be788365cdd52130' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/home.login.html',
      1 => 1615272482,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_617f52a31ebe38_50123951 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php echo $_smarty_tpl->tpl_vars['WEB_NAME']->value;?>
登录</title>

		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=320, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="/css/ace/css/bootstrap.css" />
		<link rel="stylesheet" href="/css/ace/css/font-awesome.css" />

		<!-- text fonts -->
		<link rel="stylesheet" href="/css/ace/css/ace-fonts.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="/css/ace/css/ace.css" />

		<!--[if lte IE 9]>
		<link rel="stylesheet" href="/css/ace/css/ace-part2.css" />
		<![endif]-->
		<link rel="stylesheet" href="/css/ace/css/ace-rtl.css" />

		<!--[if lte IE 9]>
		<link rel="stylesheet" href="/css/ace/css/ace-ie.css" />
		<![endif]-->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

		<!--[if lt IE 9]>
		<?php echo '<script'; ?>
 src="/css/ace/js/html5shiv.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/css/ace/js/respond.js"><?php echo '</script'; ?>
>
		<![endif]-->

		<?php echo '<script'; ?>
 src="/js/jquery-3.4.1.min.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/js/coo.js?import=coo.pc"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/css/gm/js/common.js"><?php echo '</script'; ?>
>
		<link rel="stylesheet" href="/css/gm/css/custom.css" />

		<style>
		body{ -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
		input{ -webkit-appearance:none; appearance:none; }
		</style>
	</head>

	<body class="login-layout blur-login">
		<div class="main-container">
			<div class="main-content">
				<div>
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="space-6"></div>
							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue bigger">请登录</h4>
											<div class="space-6"></div>
											<form action="?app=home&act=login" method="post">
												<fieldset>
													<span class="block clearfix ra-4">
														<input type="text" name="name" id="name" class="form-control" />
														<label class="block" for="name">用户名</label>
													</span>

													<span class="block clearfix ra-4">
														<input type="password" name="password" id="password" class="form-control" />
														<label class="block" for="password">密码</label>
													</span>

													<div class="checkbox">
														<label style="margin:0;padding:0;">
															<input type="checkbox" name="remember" value="1" class="ace" /><span class="lbl" style="margin:0;color:#999;"> 记住登录</span>
														</label>
													</div>

													<div class="space"></div>

													<div class="clearfix">
														<button type="submit" class="ra-4 pull-right btn btn-sm btn-primary">
															<span class="bigger-110">登录</span>
														</button>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>
											<div class="space-6"></div>
										</div><!-- /.widget-main -->
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->
							</div><!-- /.position-relative -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->
		<!-- basic scripts -->
	</body>
</html>
<?php echo '<script'; ?>
>
$(function(){
	$('.form-control').on('input propertychange', function(){
		let _this = $(this);
		if(_this.val().length){
			_this.next().addClass('inputed');
		}else{
			_this.next().removeClass('inputed');
		}
	}).trigger('input');
});
<?php echo '</script'; ?>
><?php }
}
