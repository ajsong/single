<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-22 19:16:43
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/footer.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_61729d9b4f1515_22588253',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5af30ed63116b7d3ae252abdcf2fff5eb7d1f239' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/footer.html',
      1 => 1586325838,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_61729d9b4f1515_22588253 (Smarty_Internal_Template $_smarty_tpl) {
?><input type="hidden" id="member_id" value="<?php echo $_smarty_tpl->tpl_vars['member']->value->id;?>
" />
<input type="hidden" id="member_money" value="<?php if ($_smarty_tpl->tpl_vars['member']->value->id > 0) {
echo $_smarty_tpl->tpl_vars['member']->value->money;
} else { ?>0<?php }?>" />
</body>
</html>
<?php if (isset($_smarty_tpl->tpl_vars['jssdk']->value)) {
echo '<script'; ?>
 src="//res.wx.qq.com/open/js/jweixin-1.3.2.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
wxShareInit('<?php echo $_smarty_tpl->tpl_vars['jssdk']->value;?>
');
<?php echo '</script'; ?>
>
<?php }
}
}
