<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-28 23:19:42
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/other.wxtool.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617abf8e7ba511_15051242',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '78eda7e824e576d357ba35a7b3ec8b9e1511fd86' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/other.wxtool.html',
      1 => 1620872188,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_617abf8e7ba511_15051242 (Smarty_Internal_Template $_smarty_tpl) {
?><!doctype html>

<html lang="en">
<head>
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=0" />
<meta charset="UTF-8">
<title>Mobile Unit</title>
<?php echo '<script'; ?>
 type="text/javascript" src="/js/jquery-3.4.1.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="/js/coo.js"><?php echo '</script'; ?>
>
<!--<?php echo '<script'; ?>
 type="text/javascript" src="/js/Packer.js"><?php echo '</script'; ?>
>-->
<style>
body{margin:0; padding:10px; text-align:center;}
p{margin:0; padding:0;}
.hidden{display:none;}
.view{position:relative; width:900px; overflow:hidden; border-radius:5px; box-sizing:border-box; border:1px solid #ddd;}
.view textarea{float:left; width:100%; height:400px; font-size:12px; border:none; background-color:transparent; outline:none; resize:none; padding:10px; box-sizing:border-box;}
.view p{float:left; width:100%; height:auto; overflow:hidden; border-top:1px solid #ddd; background:#f1f1f1;}
.view p:after{content:""; display:block; clear:both;}
.view p > input, .view p > span, .view p > a{display:block; text-decoration:none; float:left; width:14.2%; height:40px; overflow:hidden; position:relative; border-radius:0; box-sizing:border-box; background-color:transparent; border:none; color:#333; font-size:14px; outline:none; cursor:pointer; text-align:center; border-right:1px solid #ddd;}
.view p:nth-child(3) > input, .view p:nth-child(3) > span, .view p:nth-child(3) > a{width:25%;}
.view p > span, .view p > a{line-height:40px;}
.view p > *:last-child{border:none !important;}
.view p > span input{width:100%; height:100%; font-size:24px; position:absolute; left:0; top:0; z-index:1; opacity:0; outline:none; cursor:pointer;}
.view em{position:absolute; left:0; top:0; z-index:1; display:none; width:100%; height:100%;}
.view > em{background-color:rgba(0,0,0,0.05);}
.view em em{left:50%; top:50%; margin-left:-15px; margin-top:-15px; width:30px; height:30px; -webkit-animation:preloader-spin 1s steps(12, end) infinite; animation:preloader-spin 1s steps(12, end) infinite; background-repeat:no-repeat; background-position:center center; background-size:30px 30px; background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20viewBox%3D'0%200%20120%20120'%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20xmlns%3Axlink%3D'http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink'%3E%3Cdefs%3E%3Cline%20id%3D'l'%20x1%3D'60'%20x2%3D'60'%20y1%3D'7'%20y2%3D'27'%20stroke%3D'%236c6c6c'%20stroke-width%3D'11'%20stroke-linecap%3D'round'%2F%3E%3C%2Fdefs%3E%3Cg%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(30%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(60%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(90%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(120%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(150%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.37'%20transform%3D'rotate(180%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.46'%20transform%3D'rotate(210%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.56'%20transform%3D'rotate(240%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.66'%20transform%3D'rotate(270%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.75'%20transform%3D'rotate(300%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.85'%20transform%3D'rotate(330%2060%2C60)'%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E");}
@-webkit-keyframes preloader-spin{100%{-webkit-transform:rotate(360deg);}}
@keyframes preloader-spin{100%{transform:rotate(360deg);}}
@media (max-width: 768px) {
.view{width:100%;}
.view textarea{height:280px;}
.view p > input, .view p > span, .view p > a{width:100%; border-right:none; border-bottom:1px solid #ddd;}
}
/* 提示框 */
.load-overlay{position:fixed; left:0; top:0; z-index:998; width:100%; height:100%; overflow:hidden; text-align:center; opacity:0; -webkit-transition:opacity 200ms ease-out; transition:opacity 200ms ease-out; -webkit-backface-visibility:hidden; backface-visibility:hidden; -webkit-transform-style:preserve-3d; transform-style:preserve-3d;}
.load-overlay-in{opacity:1;}
.load-view{position:fixed; top:50%; left:50%; z-index:1101; margin-left:-60px; margin-top:-60px; min-width:120px; min-height:120px; overflow:hidden; background:rgba(0,0,0,0.6); border-radius:10px; opacity:0; -webkit-transform:scale(1.185); transform:scale(1.185); -webkit-transition:opacity 200ms ease-out,-webkit-transform 200ms ease-out; transition:opacity 200ms ease-out,transform 200ms ease-out; -webkit-backface-visibility:hidden; backface-visibility:hidden; -webkit-transform-style:preserve-3d; transform-style:preserve-3d;}
.load-view-in{-webkit-transform:scale(1); transform:scale(1); opacity:1;}
.load-view-out{-webkit-transform:scale(0.815); transform:scale(0.815); opacity:0;}
.load-view div{display:block; width:40px; height:40px; margin:27px auto 0 auto; background:no-repeat center center; background-size:cover;}
.load-view span{display:block; color:#fff; line-height:18px; font-size:14px; padding:10px; text-align:center;}
.load-view span.text{font-size:14px;}
/* 提示框.加载动画 */
.preloader, .preloader-gray, .load-view .load-animate, .load-view .load-success, .load-view .load-error{display:block; width:30px; height:30px; -webkit-animation:preloader-spin 1s steps(12, end) infinite; animation:preloader-spin 1s steps(12, end) infinite; background-repeat:no-repeat; background-position:center center; background-size:cover; background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20viewBox%3D'0%200%20120%20120'%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20xmlns%3Axlink%3D'http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink'%3E%3Cdefs%3E%3Cline%20id%3D'l'%20x1%3D'60'%20x2%3D'60'%20y1%3D'7'%20y2%3D'27'%20stroke%3D'%23fff'%20stroke-width%3D'11'%20stroke-linecap%3D'round'%2F%3E%3C%2Fdefs%3E%3Cg%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(30%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(60%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(90%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(120%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(150%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.37'%20transform%3D'rotate(180%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.46'%20transform%3D'rotate(210%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.56'%20transform%3D'rotate(240%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.66'%20transform%3D'rotate(270%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.75'%20transform%3D'rotate(300%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.85'%20transform%3D'rotate(330%2060%2C60)'%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E") !important;}
.load-view .load-animate-gray, .preloader-gray{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20viewBox%3D'0%200%20120%20120'%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20xmlns%3Axlink%3D'http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink'%3E%3Cdefs%3E%3Cline%20id%3D'l'%20x1%3D'60'%20x2%3D'60'%20y1%3D'7'%20y2%3D'27'%20stroke%3D'%236c6c6c'%20stroke-width%3D'11'%20stroke-linecap%3D'round'%2F%3E%3C%2Fdefs%3E%3Cg%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(30%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(60%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(90%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(120%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(150%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.37'%20transform%3D'rotate(180%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.46'%20transform%3D'rotate(210%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.56'%20transform%3D'rotate(240%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.66'%20transform%3D'rotate(270%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.75'%20transform%3D'rotate(300%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.85'%20transform%3D'rotate(330%2060%2C60)'%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E") !important;}
@-webkit-keyframes preloader-spin{100%{-webkit-transform:rotate(360deg);}}
@keyframes preloader-spin{100%{transform:rotate(360deg);}}
/* 提示框.成功,错误,问题,注意图标 */
.load-view .load-success{animation:none; -webkit-animation:none; background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20width%3D%2264%22%20height%3D%2264%22%20viewBox%3D%220%200%2064%2064%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cg%20class%3D%22transform-group%22%3E%3Cg%20transform%3D%22scale(0.0625%2C%200.0625)%22%3E%3Cpath%20d%3D%22M464.247573%20677.487844C474.214622%20686.649009%20489.665824%20686.201589%20499.086059%20676.479029L798.905035%20367.037897C808.503379%20357.131511%20808.253662%20341.319802%20798.347275%20331.721455%20788.44089%20322.12311%20772.62918%20322.372828%20763.030833%20332.279215L463.211857%20641.720346%20498.050342%20640.711531%20316.608838%20473.940461C306.453342%20464.606084%20290.653675%20465.271735%20281.319298%20475.427234%20271.984922%20485.582733%20272.650573%20501.382398%20282.806071%20510.716774L464.247573%20677.487844ZM1024%20512C1024%20229.230208%20794.769792%200%20512%200%20229.230208%200%200%20229.230208%200%20512%200%20794.769792%20229.230208%201024%20512%201024%20629.410831%201024%20740.826187%20984.331046%20830.768465%20912.686662%20841.557579%20904.092491%20843.33693%20888.379234%20834.742758%20877.590121%20826.148587%20866.801009%20810.43533%20865.021658%20799.646219%20873.615827%20718.470035%20938.277495%20618.001779%20974.048781%20512%20974.048781%20256.817504%20974.048781%2049.951219%20767.182496%2049.951219%20512%2049.951219%20256.817504%20256.817504%2049.951219%20512%2049.951219%20767.182496%2049.951219%20974.048781%20256.817504%20974.048781%20512%20974.048781%20599.492834%20949.714859%20683.336764%20904.470807%20755.960693%20897.177109%20767.668243%20900.755245%20783.071797%20912.462793%20790.365493%20924.170342%20797.659191%20939.573897%20794.081058%20946.867595%20782.373508%20997.013826%20701.880796%201024%20608.898379%201024%20512Z%22%20fill%3D%22%23ffffff%22%3E%3C%2Fpath%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E") !important;}
.load-view .load-error{animation:none; -webkit-animation:none; background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20width%3D%2264%22%20height%3D%2264%22%20viewBox%3D%220%200%2064%2064%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cg%20class%3D%22transform-group%22%3E%3Cg%20transform%3D%22scale(0.0625%2C%200.0625)%22%3E%3Cpath%20d%3D%22M1024%20512C1024%20229.230208%20794.769792%200%20512%200%20229.230208%200%200%20229.230208%200%20512%200%20794.769792%20229.230208%201024%20512%201024%20629.410831%201024%20740.826187%20984.331046%20830.768465%20912.686662%20841.557579%20904.092491%20843.33693%20888.379234%20834.742758%20877.590121%20826.148587%20866.801009%20810.43533%20865.021658%20799.646219%20873.615827%20718.470035%20938.277495%20618.001779%20974.048781%20512%20974.048781%20256.817504%20974.048781%2049.951219%20767.182496%2049.951219%20512%2049.951219%20256.817504%20256.817504%2049.951219%20512%2049.951219%20767.182496%2049.951219%20974.048781%20256.817504%20974.048781%20512%20974.048781%20599.492834%20949.714859%20683.336764%20904.470807%20755.960693%20897.177109%20767.668243%20900.755245%20783.071797%20912.462793%20790.365493%20924.170342%20797.659191%20939.573897%20794.081058%20946.867595%20782.373508%20997.013826%20701.880796%201024%20608.898379%201024%20512ZM331.838918%20663.575492C322.174057%20673.416994%20322.317252%20689.230029%20332.158756%20698.894891%20342.000258%20708.559753%20357.813293%20708.416557%20367.478155%20698.575053L717.473766%20342.182707C727.138628%20332.341205%20726.995433%20316.528171%20717.153931%20306.863309%20707.312427%20297.198447%20691.499394%20297.341643%20681.834532%20307.183147L331.838918%20663.575492ZM681.834532%20698.575053C691.499394%20708.416557%20707.312427%20708.559753%20717.153931%20698.894891%20726.995433%20689.230029%20727.138628%20673.416994%20717.473766%20663.575492L367.478155%20307.183147C357.813293%20297.341643%20342.000258%20297.198447%20332.158756%20306.863309%20322.317252%20316.528171%20322.174057%20332.341205%20331.838918%20342.182707L681.834532%20698.575053Z%22%20fill%3D%22%23ffffff%22%3E%3C%2Fpath%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E") !important;}
.load-view .load-problem{animation:none; -webkit-animation:none; background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20width%3D%2264%22%20height%3D%2264%22%20viewBox%3D%220%200%2064%2064%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cg%20class%3D%22transform-group%22%3E%3Cg%20transform%3D%22scale(0.0625%2C%200.0625)%22%3E%3Cpath%20d%3D%22M1024%20512C1024%20229.230208%20794.769792%200%20512%200%20229.230208%200%200%20229.230208%200%20512%200%20794.769792%20229.230208%201024%20512%201024%20629.410831%201024%20740.826187%20984.331046%20830.768465%20912.686662%20841.557579%20904.092491%20843.33693%20888.379234%20834.742758%20877.590121%20826.148587%20866.801009%20810.43533%20865.021658%20799.646219%20873.615827%20718.470035%20938.277495%20618.001779%20974.048781%20512%20974.048781%20256.817504%20974.048781%2049.951219%20767.182496%2049.951219%20512%2049.951219%20256.817504%20256.817504%2049.951219%20512%2049.951219%20767.182496%2049.951219%20974.048781%20256.817504%20974.048781%20512%20974.048781%20599.492834%20949.714859%20683.336764%20904.470807%20755.960693%20897.177109%20767.668243%20900.755245%20783.071797%20912.462793%20790.365493%20924.170342%20797.659191%20939.573897%20794.081058%20946.867595%20782.373508%20997.013826%20701.880796%201024%20608.898379%201024%20512ZM337.170731%20499.512194C371.654852%20499.512194%20399.609756%20471.557291%20399.609756%20437.073171%20399.609756%20402.58905%20371.654852%20374.634146%20337.170731%20374.634146%20302.686611%20374.634146%20274.731708%20402.58905%20274.731708%20437.073171%20274.731708%20471.557291%20302.686611%20499.512194%20337.170731%20499.512194ZM711.804879%20499.512194C746.288998%20499.512194%20774.243902%20471.557291%20774.243902%20437.073171%20774.243902%20402.58905%20746.288998%20374.634146%20711.804879%20374.634146%20677.320757%20374.634146%20649.365854%20402.58905%20649.365854%20437.073171%20649.365854%20471.557291%20677.320757%20499.512194%20711.804879%20499.512194ZM352.788105%20704.784525C396.165222%20670.082831%20453.151987%20649.360371%20524.487806%20649.360371%20595.823622%20649.360371%20652.810387%20670.082831%20696.187505%20704.784525%20722.700531%20725.994946%20738.882517%20747.570927%20746.631548%20763.068988%20752.800254%20775.406402%20747.799529%20790.408576%20735.462114%20796.577284%20723.124702%20802.74599%20708.122526%20797.745265%20701.953818%20785.407851%20701.03616%20783.572535%20698.492224%20779.382524%20694.165854%20773.614029%20686.602473%20763.529523%20676.927317%20753.345148%20664.983226%20743.789875%20630.311565%20716.052544%20584.273939%20699.31159%20524.487806%20699.31159%20464.70167%20699.31159%20418.664045%20716.052544%20383.992384%20743.789875%20372.048292%20753.345148%20362.373137%20763.529523%20354.809756%20773.614029%20350.483386%20779.382524%20347.93945%20783.572535%20347.021792%20785.407851%20340.853084%20797.745265%20325.850908%20802.74599%20313.513495%20796.577284%20301.176081%20790.408576%20296.175356%20775.406402%20302.344062%20763.068988%20310.093092%20747.570927%20326.275078%20725.994946%20352.788105%20704.784525Z%22%20fill%3D%22%23ffffff%22%3E%3C%2Fpath%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E") !important;}
.load-view .load-warning{animation:none; -webkit-animation:none; background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20width%3D%2264%22%20height%3D%2264%22%20viewBox%3D%220%200%2064%2064%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cg%20class%3D%22transform-group%22%3E%3Cg%20transform%3D%22scale(0.0625%2C%200.0625)%22%3E%3Cpath%20d%3D%22M598.272514%20158.17909C545.018272%2071.994036%20451.264177%2071.951401%20397.724122%20158.397341L25.049726%20760.118586C-28.93569%20847.283607%2014.324655%20927.325257%20116.435565%20929.308966L891.057077%20929.313666C993.88467%20931.315989%201036.926865%20868.038259%20983.25955%20781.189694%20980.374633%20776.521099%20980.374633%20776.521099%20971.719878%20762.515313%20967.393745%20755.514432%20967.393745%20755.514432%20963.78822%20749.679695%20956.511588%20737.90409%20941.113263%20734.285867%20929.3951%20741.59817%20917.676937%20748.910473%20914.076365%20764.384279%20921.352996%20776.159885%20924.958522%20781.994622%20924.958522%20781.994622%20929.284655%20788.995503%20937.939409%20803.001289%20937.939409%20803.001289%20940.824326%20807.669884%20972.284602%20858.581314%20957.441559%20880.402549%20891.539823%20879.122276L116.918309%20879.117577C54.037254%20877.891296%2033.95555%20840.735497%2067.458075%20786.642217L440.132471%20184.920971C474.112981%20130.055931%20522.112175%20130.077759%20556.029583%20184.965509L857.08969%20656.83971C864.534622%20668.508595%20879.98329%20671.9032%20891.595253%20664.421773%20903.207217%20656.940343%20906.585263%20641.415949%20899.140331%20629.747063L598.272514%20158.17909ZM474.536585%20619.793346C474.536585%20633.654611%20485.718547%20644.891386%20499.512194%20644.891386%20513.305843%20644.891386%20524.487806%20633.654611%20524.487806%20619.793346L524.487806%20299.793346C524.487806%20285.932082%20513.305843%20274.695307%20499.512194%20274.695307%20485.718547%20274.695307%20474.536585%20285.932082%20474.536585%20299.793346L474.536585%20619.793346ZM474.465781%20776.736145C474.565553%20790.597047%20485.828105%20801.75225%20499.621393%20801.651987%20513.414679%20801.551725%20524.515467%20790.233967%20524.415695%20776.373065L523.955031%20712.375667C523.855258%20698.514767%20512.592708%20687.359563%20498.79942%20687.459825%20485.006133%20687.560087%20473.905346%20698.877847%20474.005118%20712.738748L474.465781%20776.736145Z%22%20fill%3D%22%23ffffff%22%3E%3C%2Fpath%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E") !important;}
</style>
</head>

<body>
<div class="view">
	<label><textarea placeholder="这里粘贴代码"></textarea></label>
	<p>
		<input type="button" class="rem" value="转rem" />
		<input type="button" class="px" value="转px" />
		<input type="button" class="rpx" value="转rpx" />
		<input type="button" class="svg" value="svg转data" />
		<input type="button" class="nl" value="清除换行" />
		<span>图片转base64<input type="file" class="base64" name="file" /></span>
		<span>上传图片<input type="file" class="online" name="file[]" multiple /></span>
		<!--<a href="javascript:void(0)" class="js">js解压缩</a>-->
	</p>
	<p class="hidden">
		<input type="button" class="compress" value="压缩" />
		<input type="button" class="pack" value="加密" />
		<input type="button" class="run" value="运行" />
		<a href="javascript:void(0)" class="css">css转换</a>
	</p>
	<em><em></em></em>
</div>
</body>
</html>
<?php echo '<script'; ?>
>
$(function(){
	$('.view .js').click(function(){
		$('.view p:eq(0)').addClass('hidden');
		$('.view p:eq(1)').removeClass('hidden');
	});
	$('.view .css').click(function(){
		$('.view p:eq(0)').removeClass('hidden');
		$('.view p:eq(1)').addClass('hidden');
	});
	$('.view textarea').focus().select().onkey({
		callback : function(code, key, e){
			if(e.meta && code===13){
				$('.view .rem').click();
			}else if(e.alt && code===13){
				$('.view .px').click();
			}else if(e.ctrl && code===13){
				$('.view .rpx').click();
			}else if(e.shift && code===13){
				$('.view .svg').click();
				return false;
			}
		}
	});
	$('.view :button').click(function(){
		let code = $('.view textarea'), val = code.val();
		if(!val.length)return;
		if($(this).is('.rem')){
			val = val.replace(/(-?[\d.]+)px\b/ig, function($, $1){
				return (Number($1) / 100) + 'rem';
			}).replace(/(-?[\d.]+)rpx\b/ig, function($, $1){
				return (Math.ceil(Number($1) * 320 / 750) / 100) + 'rem';
			});
		}else if($(this).is('.px')){
			val = val.replace(/(-?[\d.]+)rpx\b/ig, function($, $1){
				return (Math.ceil(Number($1)*320/750)) + 'px';
			}).replace(/(-?[\d.]+)rem\b/ig, function($, $1){
				return Math.ceil(Number($1)*100) + 'px';
			});
		}else if($(this).is('.rpx')){
			if(!/<\/?\w+>/.test(val)){
				val = val.replace(/(-?[\d.]+)(px|rem)\b/ig, function($, $1, $2){
					let number = Number($1);
					if($2==='rem')number = number*100;
					return Math.floor((750/320)*number) + 'rpx';
				})
				.replace(/\b(?<![.\/])a\b/ig, 'navigator').replace(/\b(?<![.\/])div\b/ig, 'view')
				.replace(/\b(?<![.\/])select\b/ig, 'picker').replace(/\b(?<![.\/])img\b/ig, 'image')
				.replace(/\b(?<![.\/])((ul|ol|li|font|em|h1|h2|h3|h4|h5|h6|i|b|strong|s|span|p|big|small|tt|section|header|footer)[,.{:\s\b]+)/ig, '.$1');
			}else{
				val = val.replace(/{([^}]+)}/ig, function($, $1){
					return '{{'+$1.replace(/\$(\w+)(->|\.)/g, function($, _$1){
						//return _$1=='g' ? 'item.' : _$1+'.';
						return _$1+'.';
					}).replace(/->/g, '.')+'}}';
				})
				.replace(/number_format/ig, 'helper.round')
				.replace(/\\$/img, '')
				.replace(/<(a)([^>]*)>([\s\S]*?)<\/\1>/ig, function($, $1, $2, $3){
					let wxtag = 'navigator', param = $2;
					if(param.indexOf('javascript:void(0)')>-1){
						wxtag = 'view';
						param = param.replace(/ href=(['"])[^'"]*\1/, '');
						param += ' bindtap="click"';
					}else{
						param = param.replace(' href="javascript:void(0)"', '').replace('href', 'url')
						.replace(/url="(wap.php)?\?app=(\w+)&act=(\w+)([^"]*)"/, function($, $1, $2, $3, $4){
							return 'url="/pages/'+$2+'/'+$3+($4.length?'?'+$4.substr(1):'')+'"';
						});
					}
					return '<'+wxtag+param+'>'+$3+'</'+wxtag+'>';
				})
				.replace(/<(div)[^>]+?class="pageView"[^>]*>(<\1[^>]*>[\s\S]*?<\/\1>|[\s\S])*?<\/\1>/ig, function(){
					return '<import src="../../common/template.wxml" /><template is="swiper" data="{{flashes}}" />';
				})
				.replace(/<(\/?)div([^>]*)>/ig, function($, $1, $2){
					let param = $2.replace(/( main-top| main-padding-top| main-bottom| main-padding-bottom| width-wrap)/ig, '');
					return '<'+$1+'view'+(!$1.length?param:'')+'>';
				})
				/*.replace(/<(\/?)(?:span)([^>]*)>/ig, function($, $1, $2){
					return '<'+$1+'text'+(!$1.length?$2:'')+'>';
				})*/
				.replace(/<(\/?)(?:form)([^>]*)>/ig, function($, $1){
					return '<'+$1+'form'+(!$1.length?' bindsubmit="postForm"':'')+'>';
				})
				.replace(/<(\/?)(?:input)([^>]*)>/ig, function($, $1, $2){
					let param = $2.replace(/type="hidden"/, 'type="text" class="hidden"').replace(/type="tel"/, 'type="number"').replace(/ id="\w*"/, '');
					return '<'+$1+'input'+(!$1.length?param:'')+'>';
				})
				.replace(/<(select)([^>]*)>([\s\S]*)<\/\1>/ig, function($, $1, $2, $3){
					return '<picker bindchange="change" range="{{data}}" range-key="name">'+$3+'</picker>';
				})
				.replace(/<(\/?)((?!navigator|view|text|label|form|input|textarea|picker|button)\w+)\b([^>]*)>/ig, function($, $1, $2, $3){
					let tag = $2, param = $3, wxtag = 'view';
					if(/class=(['"])[^'"]*\1/.test(param))param = param.replace(/class=(['"])([^'"]*)\1/, 'class=$1'+tag+' $2$1');
					else param += ' class="'+tag+'"';
					if(jQuery.inArray(tag, ['ul', 'ol', 'li', 'section', 'header', 'footer', 'i', 'b', 'strong', 's'])===-1)wxtag = 'text';
					return '<'+$1+wxtag+(!$1.length?param:'')+'>';
				});
			}
		}else if($(this).is('.svg')){
			if(val.length<30)return;
			val = val.replace(/ class=(['"])[^'"]*\1/g, '').replace(/ version=(['"])[^'"]*\1/g, '')
			.replace(/ t="\d+"/g, '').replace(/ p-id="\d+"/g, '')
			.replace(/ width=(['"])\d+\1/g, '').replace(/ height=(['"])\d+\1/g, '')
			.replace(/<title>.*?<\/title>/g, '');
			val = 'data:image/svg+xml;charset=utf-8,'+encodeURIComponent(val);
		}else if($(this).is('.nl')){
			val = val.replace(/[\r\n]/g, '');
		}else if($(this).is('.compress')){
			if(/^eval\(function\(p,a,c,k,e,r\)/.test(val))return;
			$('.view em').show();
			let packer = new Packer;
			val = packer.pack(val, false, false); //packer.pack(code代码, base62加密, shrink混淆参数)
		}else if($(this).is('.pack')){
			if(/^eval\(function\(p,a,c,k,e,r\)/.test(val))return;
			$('.view em').show();
			let packer = new Packer;
			val = packer.pack(val, true, true);
			val = new CLASS_CONFUSION(val).confusion();
		}else if($(this).is('.run')){
			if(!/^<?php echo '<script'; ?>
>/.test(val))val = '<?php echo '<script'; ?>
>' + val + '<\/script>';
			let r = window.open('', '', '');
			r.opener = null;
			r.document.write(val);
			r.document.close();
			return;
		}
		code.val(val);
		$('.view em').hide();
		setTimeout(function(){ code.focus().select() }, 100);
	});
	$('.view .base64').ajaxupload({
		url: '/api/other/image_base64',
		dataType: 'text',
		before: function(){ $('.view em').show() },
		callback: {
			success: function(base64){
				$('.view textarea').val(base64);
				setTimeout(function(){ $('.view textarea').focus().select() }, 100);
			},
			error: function(data, status, e){
				alert('Upload error\n'+e);
			},
			complete: function(){
				$('.view em').hide();
			}
		}
	}).parent().html5upload({
		url: '/api/other/image_base64',
		name: 'file',
		dataType: 'text',
		before: function(){ $('.view em').show() },
		success: function(base64){
			$('.view em').hide();
			$('.view textarea').val(base64);
			setTimeout(function(){ $('.view textarea').focus().select() }, 100);
		},
		error: function(data, status, e){
			$('.view em').hide();
			alert('Upload error\n'+e);
		}
	});
	let online = $('.view .online');
	online.ajaxupload({
		url: '/api/other/image_online' + (!!online.attr('data-type') ? '?type='+online.attr('data-type') : ''),
		fileType: !!online.attr('data-type') ? online.attr('data-type') : 'jpg,jpeg,png,gif,bmp',
		before: function(){ $('.view em').show() },
		callback: {
			success: function(urls){
				$('.view textarea').val($.isArray(urls) ? urls.join('\n') : urls);
				setTimeout(function(){ $('.view textarea').focus().select() }, 100);
			},
			error: function(data, status, e){
				alert('Upload error\n'+e);
			},
			complete: function(){
				$('.view em').hide();
			}
		}
	}).parent().html5upload({
		url: '/api/other/image_online' + (!!online.attr('data-type') ? '?type='+online.attr('data-type') : ''),
		name: 'file',
		fileType: !!online.attr('data-type') ? online.attr('data-type') : 'jpg,jpeg,png,gif,bmp',
		before: function(){ $('.view em').show() },
		success: function(urls){
			$('.view em').hide();
			$('.view textarea').val($.isArray(urls) ? urls.join('\n') : urls);
			setTimeout(function(){ $('.view textarea').focus().select() }, 100);
		},
		error: function(data, status, e){
			$('.view em').hide();
			alert('Upload error\n'+e);
		}
	});
});
<?php echo '</script'; ?>
>
<?php }
}
