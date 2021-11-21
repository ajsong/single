<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-01 10:36:35
  from '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/footer.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617f52b35df291_48599140',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '141721d89fb641df72739bfb1a5a7903e579159f' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/gm/view/footer.html',
      1 => 1567394314,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_617f52b35df291_48599140 (Smarty_Internal_Template $_smarty_tpl) {
?>								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<div class="footer">
				<div class="footer-inner">
					<!-- #section:basics/footer -->
					<div class="footer-content" style="text-align:center">
						<span class="bigger">
							<?php echo $_smarty_tpl->tpl_vars['WEB_NAME']->value;?>
 &copy; <?php echo date('Y');?>

							<?php if (strlen($_smarty_tpl->tpl_vars['configs']->value['GLOBAL_SUPPORT'])) {?>&nbsp; | &nbsp; <copyright>技术支持：<a href="<?php echo $_smarty_tpl->tpl_vars['configs']->value['GLOBAL_SUPPORT_URL'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['configs']->value['GLOBAL_SUPPORT'];?>
</a></copyright><?php }?>
						</span>
					</div>

					<!-- /section:basics/footer -->
				</div>
			</div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

	</body>
</html><?php }
}
