<?php
/* Smarty version 3.1.32-dev-45, created on 2021-10-31 22:06:07
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/cart.index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_617ea2cf0b1a13_42150573',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1ff1e91749648ca226d0018879e21745aac9722d' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/cart.index.html',
      1 => 1568193265,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_617ea2cf0b1a13_42150573 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link type="text/css" href="/js/photoswipe/photoswipe.css" rel="stylesheet" />
<link type="text/css" href="/js/photoswipe/default-skin/default-skin.css" rel="stylesheet" />
<?php echo '<script'; ?>
 type="text/javascript" src="/js/photoswipe/photoswipe.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="/js/photoswipe/photoswipe-ui-default.min.js"><?php echo '</script'; ?>
>
<body class="gr">

<div class="navBar">
	<!--<a class="left" href="javascript:history.back()"><i class="return"></i></a>-->
	<div class="titleView-x">购物车</div>
</div>

<div class="cart-index <?php if (is_array($_smarty_tpl->tpl_vars['data']->value) && count($_smarty_tpl->tpl_vars['data']->value) > 0) {?>padding-bottom<?php }?>">
	<?php if (is_array($_smarty_tpl->tpl_vars['data']->value) && count($_smarty_tpl->tpl_vars['data']->value) > 0) {?>
	<section>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 's');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['s']->value) {
?>
		<!--<ul class="tableView">-->
			<section>
				<?php if ($_smarty_tpl->tpl_vars['s']->value->shop_id > 0) {?>
				<header>
					<a href="?app=shop&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['s']->value->shop_id;?>
">
						<div <?php if ($_smarty_tpl->tpl_vars['s']->value->shop_avatar) {?>style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['s']->value->shop_avatar;?>
);"<?php }?>></div><?php echo $_smarty_tpl->tpl_vars['s']->value->shop_name;?>

					</a>
				</header>
				<?php }?>
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['s']->value->goods, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
				<ul class="tableView tableView-light">
					<li row="<?php echo $_smarty_tpl->tpl_vars['g']->value->goods_id;?>
">
						<div class="view">
							<a href="javascript:void(0)" class="act"></a>
							<div class="tick"><input type="checkbox" name="checkbox" id="tick<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" /><label for="tick<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
"></label></div>
							<a class="pic" href="/wap/?app=goods&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->goods_id;?>
" url="<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
"></a>
							<div class="info">
								<a class="name" href="/wap/?app=goods&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->goods_id;?>
"><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</a>
								<div class="spec"><?php if ($_smarty_tpl->tpl_vars['g']->value->spec_name != '') {
echo $_smarty_tpl->tpl_vars['g']->value->spec_name;
}?></div>
								<?php if ($_smarty_tpl->tpl_vars['g']->value->stock_alert_number >= $_smarty_tpl->tpl_vars['g']->value->stocks) {?><div class="stock_alert">库存紧张</div><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['g']->value->cart_price != $_smarty_tpl->tpl_vars['g']->value->price) {?><div class="price_change">比加入时<?php if ($_smarty_tpl->tpl_vars['g']->value->cart_price > $_smarty_tpl->tpl_vars['g']->value->price) {?>降<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->cart_price-$_smarty_tpl->tpl_vars['g']->value->price,2,'.','');
} else { ?>升<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->price-$_smarty_tpl->tpl_vars['g']->value->cart_price,2,'.','');
}?>元</div><?php }?>
								<div class="price clear-after">
									<span>×<?php echo $_smarty_tpl->tpl_vars['g']->value->quantity;?>
</span>
									<div price="<?php echo $_smarty_tpl->tpl_vars['g']->value->price;?>
">￥<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->price,2,'.','');?>
</div>
								</div>
							</div>
							<div class="edit hidden">
								<a href="javascript:void(0)" class="ok"><span>完成</span></a>
								<div class="num">
									<div>
										<input type="hidden" class="cart_id" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" />
										<input type="hidden" class="pic" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
" />
										<input type="hidden" class="goods_id" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->goods_id;?>
" />
										<input type="hidden" class="spec" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->spec;?>
" />
										<input type="hidden" class="spec_name" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->spec_name;?>
" />
										<input type="hidden" class="price" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->price;?>
" />
										<input type="hidden" class="stocks" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->stocks;?>
" />
										<a href="javascript:void(0)" class="plus ge-left">+</a>
										<a href="javascript:void(0)" class="minus ge-right">-</a>
										<input type="tel" class="quantity" value="<?php echo $_smarty_tpl->tpl_vars['g']->value->quantity;?>
" val="<?php echo $_smarty_tpl->tpl_vars['g']->value->quantity;?>
" />
									</div>
								</div>
								<?php if ($_smarty_tpl->tpl_vars['g']->value->spec_name != '') {?><a href="javascript:void(0)" class="spec-param"><span><?php echo $_smarty_tpl->tpl_vars['g']->value->spec_name;?>
</span></a><?php }?>
							</div>
						</div>
					</li>
				</ul>
				<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			</section>
		<!--</ul>-->
		<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	</section>
	<div class="bottomView toolBar ge-top">
		<a class="btn" href="javascript:void(0)">去结算(0)</a>
		<div>总计：<span>￥0.00</span></div>
		<a class="all" href="javascript:void(0)">全选</a>
	</div>
	<?php } else { ?>
	<div class="norecord"><div></div><span>购物车还是空的哦！</span><a href="/wap/?app=category&act=index">去逛逛</a></div>
	<div class="recommend">
		<div class="tip ge-top"><font class="gr">猜你喜欢</font></div>
		<ul class="list goods-item"></ul>
	</div>
	<?php }?>
</div>

<div class="goods-group goods-spec">
	<div class="picView">
		<div>
			<a href="javascript:void(0)"><b>⊗</b></a>
			<a href="javascript:void(0)" class="pic"></a>
			<strong></strong>
			<font></font>
			<span></span>
		</div>
	</div>
	<div class="specView"></div>
	<div class="btnView">
		<a class="btn ok" href="javascript:void(0)">确定</a>
		<a class="btn detail" href="javascript:void(0)">查看详情</a>
	</div>
</div>

<div class="footer">
	<a class="ico1" href="/wap/"></a>
	<a class="ico2" href="/wap/category"></a>
	<a class="ico3" href="/wap/article"></a>
	<a class="ico4 badge this" href="/wap/cart"><div><?php if ($_smarty_tpl->tpl_vars['cart_notify']->value > 0) {?><sub><b><?php echo $_smarty_tpl->tpl_vars['cart_notify']->value;?>
</b></sub><?php }?></div></a>
	<a class="ico5" href="/wap/member"></a>
</div>

<form action="/wap/?app=cart&act=commit" method="post">
<input type="hidden" name="goods" id="goods" />
</form>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
>
function setTotal(){
	var total = 0, num = 0;
	$(':checked').each(function(){
		var parent = $(this).parent().parent(), quantity = parent.find('.quantity'), price = parent.find('input.price');
		total += Number(price.val()) * Number(quantity.val());
		num += Number(quantity.val());
	});
	$('.bottomView span').html('￥'+total.numberFormat(2)).priceFont('bigPrice');
	$('.bottomView .btn').html('去结算('+num+')');
}
function setCart(parent, num){
	var isEdit = true, quantity = parent.find('.quantity').val();
	if($.isNumeric(num)){
		isEdit = false;
		quantity = num;
	}
	var goods = [{ goods_id:parent.find('.goods_id').val(), quantity:quantity, spec:parent.find('.spec').val() }];
	$.postJSON('/api/?app=cart&act=add', { goods:$.jsonString(goods), edit:isEdit?1:0 }, function(json){
		if(json.error!=0){ $.overloadError(json.msg);return }
	});
}
$(function(){
	$('.view .pic').loadbackground();
	$('.bottomView span, .info .price div:last').priceFont('bigPrice');
	var checked = 0;
	$(':checkbox').each(function(){
		if($(this).is(':checked'))checked++;
	});
	if($(':checkbox').length == checked){
		$('.all').addClass('all-x');
	}else{
		$('.all').removeClass('all-x');
	}
	setTotal();
	$(':checkbox').change(function(){
		var checked = 0, unchecked = 0;
		$(':checkbox').each(function(){
			if($(this).is(':checked'))checked++;
			else unchecked++;
		});
		if($(':checkbox').length == checked){
			$('.all').addClass('all-x');
		}else{
			$('.all').removeClass('all-x');
		}
		setTotal();
	});
	$('.all').click(function(){
		var _this = $(this);
		if(_this.hasClass('all-x')){
			_this.removeClass('all-x');
			$(':checkbox').prop('checked', false);
		}else{
			_this.addClass('all-x');
			$(':checkbox').prop('checked', true);
		}
		setTotal();
	});
	$('.view .act').click(function(){
		$(this).siblings('.edit').removeClass('hidden');
	});
	$('.edit .ok').click(function(){
		$(this).parent().addClass('hidden');
	});
	$('.plus').click(function(){
		var parent = $(this).parent(), quantity = parent.find('.quantity'), stocks = parent.find('.stocks');
		if(Number(quantity.val())+1 > Number(stocks.val())){
			$.overloadError('该商品规格的库存只剩下'+stocks.val()+'件');
			return;
		}
		quantity.val(Number(quantity.val())+1);
		var view = parent.parent().parent().prev(), num = view.find('span');
		num.html('×'+quantity.val());
		setTotal();
		setCart(parent, 1);
	});
	$('.minus').click(function(){
		var parent = $(this).parent(), quantity = parent.find('.quantity'), stocks = parent.find('.stocks');
		if(Number(quantity.val()) <= 1)return;
		quantity.val(Number(quantity.val())-1);
		var view = parent.parent().parent().prev(), num = view.find('span');
		num.html('×'+quantity.val());
		setTotal();
		setCart(parent, -1);
	});
	$('.quantity').focus(function(){
		var parent = $(this).parent(), quantity = $(this), stocks = parent.find('.stocks');
		if(quantity.val().length && !isNaN(quantity.val()) && Number(quantity.val())<=Number(stocks.val()))quantity.data('value', quantity.val());
	}).blur(function(){
		var parent = $(this).parent(), quantity = $(this), stocks = parent.find('.stocks');
		if(!quantity.val().length || isNaN(quantity.val())){
			$.overloadError('请输入数量');
			quantity.focus().select();
			return;
		}
		if(Number(quantity.val()) > Number(stocks.val())){
			$.overloadError('该商品规格的库存只剩下'+stocks.val()+'件');
			quantity.val(quantity.data('value'));
			quantity.focus().select();
			return;
		}
		var view = parent.parent().parent().prev(), num = view.find('span');
		num.html('×'+quantity.val());
		setTotal();
		setCart(parent);
	});
	$('.edit .spec-param').click(function(){
		var parent = $(this).prev(), goods_id = parent.find('.goods_id').val(), pic = parent.find('.pic').val(),
			price = parent.find('.price').val(), stocks = parent.find('.stocks').val(), spec = parent.find('.spec').val(), spec_name = parent.find('.spec_name').val();
		$.overload();
		$.getJSON('/api/?app=goods&act=get_specs', { goods_id:goods_id }, function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			$.overload(false);
			$('.picView .pic').attr('url', pic).css('background-image', 'url('+pic+')').photoBrowser();
			$('.picView strong').attr('price', price).html('￥'+price.numberFormat(2));
			$('.picView font').attr('stocks', stocks).html('库存'+stocks+'件');
			var specId = spec.split(','), specName = spec_name.split(';'), html = '';
			$('.picView span').attr('spec_name', spec_name).html('已选 "'+specName.join('" "')+'"');
			$('.specView').attr('goods_id', goods_id);
			if($.isArray(json.data)){
				$.each(json.data, function(i, g){
					html += '<div class="specGroup clear-after ge-top ge-light">\
						<div>'+g.name+'</div>';
						if($.isArray(g.sub)){
							$.each(g.sub, function(k, s){
								html += '<a href="javascript:void(0)" spec_id="'+s.id+'" '+($.inArray(s.id, specId)>-1?'class="this"':'')+'><span>'+s.name+'</span></a>';
							});
						}
					html += '</div>';
				});
				$('.specView').html(html);
			}
			$('.goods-spec').presentView();
		});
	});
	$('body').on('click', '.specGroup a', function(){
		$(this).addClass('this').siblings('a').removeClass('this');
		var spec_id = [], spec_name = [], count = 0, groupCount = $('.specGroup').length, goods_id = $('.specView').attr('goods_id');
		$('.specGroup a.this').each(function(){
			if(!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0)return false;
			spec_id.push($(this).attr('spec_id'));
			spec_name.push($(this).text());
			count++;
		});
		if(count!=groupCount)return;
		$.getJSON('/api/?app=goods&act=get_spec', { goods_id:goods_id, spec:spec_id.join(',') }, function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			if(!$.isPlainObject(json.data))return;
			if(json.data.pic.length)$('.picView .pic').attr('url', json.data.pic).css('background-image', 'url('+json.data.pic+')').photoBrowser();
			$('.picView strong').attr('price', json.data.price).html('￥'+json.data.price.numberFormat(2));
			$('.picView font').attr('stocks', json.data.stocks).html('库存'+json.data.stocks+'件');
			$('.picView span').attr('spec_name', spec_name.join(';')).html('已选 "'+spec_name.join('" "')+'"');
		});
	});
	$('.goods-spec .detail').click(function(){
		location.href = '/wap/?app=goods&act=detail&id='+$('.specView').attr('goods_id');
	});
	$('.goods-spec .ok').click(function(){
		var spec_id = [], count = 0, groupCount = $('.specGroup').length, goods_id = $('.specView').attr('goods_id');
		$('.specGroup a.this').each(function(){
			if(!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0)return false;
			spec_id.push($(this).attr('spec_id'));
			count++;
		});
		if(count!=groupCount)return;
		if($('[row="'+goods_id+'"] input.spec').val()==spec_id.join(',')){
			$('.goods-spec').presentView(false);
			return;
		}
		var goods = [{ goods_id:goods_id, quantity:$('[row="'+goods_id+'"] input.quantity').val(), spec:spec_id.join(',') }];
		$.postJSON('/api/?app=cart&act=add', { cart_id:$('[row="'+goods_id+'"] input.cart_id').val(), goods:$.jsonString(goods) }, function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			var price = $('.picView strong').attr('price'), stocks = $('.picView font').attr('stocks'), spec_name = $('.picView span').attr('spec_name'),
				pic = $('.picView .pic').attr('url');
			$('[row="'+goods_id+'"] .price div:last').attr('price', price).html('￥'+price.numberFormat(2)).priceFont('bigPrice');
			$('[row="'+goods_id+'"] div.spec').html(spec_name);
			$('[row="'+goods_id+'"] .spec-param span').html(spec_name);
			$('[row="'+goods_id+'"] a.pic').css('background-image', 'url('+pic+')');
			$('[row="'+goods_id+'"] input.pic').val(pic);
			$('[row="'+goods_id+'"] input.price').val(price);
			$('[row="'+goods_id+'"] input.stocks').val(stocks);
			$('[row="'+goods_id+'"] input.spec').val(spec_id.join(','));
			$('[row="'+goods_id+'"] input.spec_name').val(spec_name);
			$('.goods-spec').presentView(false);
			setTotal();
		});
	});
	$('body').on('click', '.picView a:eq(0)', function(){
		$('.goods-spec').presentView(false);
	});

	$('.cart-index .tableView').dragshow({
		title : '<i></i>',
		cls : 'delBtn',
		click : function(row){
			var _delBtn = $(this);
			$.post('/api/?app=cart&act=delete', { cart_id:row.find('.cart_id').val() }, function(){
				var section = row.parent().parent();
				if(section.parent().find('section').length==1){ location.href = location.href;return }
				if(section.find('li').length==1){
					section.slideUp(200, function(){ section.remove() });
				}else{
					row.slideUp(200, function(){ row.remove() });
				}
				_delBtn.delay(90).slideUp(200);
				setTimeout(function(){ setTotal() }, 300);
			});
		}
	});
	$('.bottomView .btn').click(function(){
		if(!$.checklogin())return;
		if(!$(':checked').length){
			$.overloadError('请选择需要购买的商品');
			return;
		}
		var goods = [];
		$(':checked').each(function(){
			var parent = $(this).parent().parent();
			goods.push({ goods_id:parent.find('input.goods_id').val(), quantity:parent.find('input.quantity').val(), spec:parent.find('input.spec').val() });
		});
		$('#goods').val($.jsonString(goods));
		$('form').submit();
	});

	if($('.cart-index .recommend').length){
		$.getJSON('/api/?app=goods&act=index', function(json){
			if(json.error!=0){ $.overloadError(json.msg);return }
			if($.isArray(json.data.goods)){
				var html = '', data = json.data.goods;
				for(var i=0; i<data.length; i++){
					html += '<li>\
						<a href="/wap/?app=goods&act=detail&id='+data[i].id+'">\
							<div class="pic" url="'+data[i].pic+'"></div>\
							<div class="title"><div>'+data[i].name+'</div><font>'+(data[i].purchase_price>0?'正在秒杀中':'')+'</font><span><strong>￥'+data[i].price.numberFormat(2)+'</strong><s>￥'+data[i].market_price.numberFormat(2)+'</s></span></div>\
						</a>\
					</li>';
				}
				$('.cart-index .goods-item').html(html);
				$('.cart-index .goods-item .pic').loadbackground();
			}
		});
	}
});
<?php echo '</script'; ?>
><?php }
}
