<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-30 11:17:14
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.code.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617cb93a9bb001_60108966',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8d6494f2ffa6a4f0c71e490e6678b5d6d0b7174c' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/member.code.html',
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
function content_617cb93a9bb001_60108966 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body>

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">我的二维码</div>
</div>

<div class="member-code">
	<div class="img"><img src="/api/?app=member&act=code&code=1" /></div>
	<div class="tip">邀请码：<?php echo $_smarty_tpl->tpl_vars['member']->value->invite_code;?>
</div>
	<div class="memo">邀请好友注册app账号后，该好友成为你下级用户，好友在每次消费后，你可获得佣金。</div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
