<?php
/* Smarty version 3.1.32-dev-45, created on 2021-11-13 15:24:02
  from '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/cart.commit.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-45',
  'unifunc' => 'content_618f68121a2304_59829667',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3b440beca7a00141c659fb3bc378b616f388e9b8' => 
    array (
      0 => '/Users/ajsong/Sites/Web/PHP/website_/application/api/view/default/cart.commit.html',
      1 => 1636788160,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_618f68121a2304_59829667 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body class="gr">

<div class="navBar">
	<a class="left" href="javascript:history.back()"><i class="return"></i></a>
	<div class="titleView-x">确认订单</div>
</div>

<div class="cart-commit">
	<div class="addressView">
		<?php if ($_smarty_tpl->tpl_vars['data']->value['address']->member_id == 0) {?>
		<a href="/wap/?app=address&act=add" class="addressNo">+ 请添加收货地址</a>
		<?php } else { ?>
		<a href="/wap/?app=address&act=index" class="address push-ico">
			<div>
				<span>收货人：<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->contactman;?>
　<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->mobile;?>
</span>
				<span><?php echo $_smarty_tpl->tpl_vars['data']->value['address']->province;
if ($_smarty_tpl->tpl_vars['data']->value['address']->province != $_smarty_tpl->tpl_vars['data']->value['address']->city) {?> <?php echo $_smarty_tpl->tpl_vars['data']->value['address']->city;
}?> <?php echo $_smarty_tpl->tpl_vars['data']->value['address']->district;?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['address']->address;?>
</span>
			</div>
		</a>
		<?php }?>
	</div>
	
	<section>
		<ul class="tableView tableView-light cart-goods">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['shops'], 's');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['s']->value) {
?>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['s']->value->goods, 'g');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['g']->value) {
?>
			<li>
				<h1>
					<div class="item">
						<a class="pic" href="/wap/?app=goods&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
" style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['g']->value->pic;?>
);"></a>
						<a class="name" href="/wap/?app=goods&act=detail&id=<?php echo $_smarty_tpl->tpl_vars['g']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['g']->value->name;?>
</a>
						<div class="spec"><?php if (!isset($_smarty_tpl->tpl_vars['integral_order']->value) && strlen($_smarty_tpl->tpl_vars['g']->value->spec_name)) {
echo $_smarty_tpl->tpl_vars['g']->value->spec_name;
}?></div>
						<div class="price clear-after" price="<?php if (isset($_smarty_tpl->tpl_vars['integral_order']->value)) {
echo $_smarty_tpl->tpl_vars['g']->value->integral;
} else {
echo $_smarty_tpl->tpl_vars['g']->value->price;
}?>" quantity="<?php echo $_smarty_tpl->tpl_vars['g']->value->quantity;?>
">
							<?php if (isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>
							<div><?php echo $_smarty_tpl->tpl_vars['g']->value->integral;?>
积分</div>
							<?php } else { ?>
							<span>×<?php echo $_smarty_tpl->tpl_vars['g']->value->quantity;?>
</span>
							<div>￥<?php echo number_format($_smarty_tpl->tpl_vars['g']->value->price,2,'.','');?>
</div>
							<?php }?>
						</div>
					</div>
				</h1>
			</li>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</ul>
		
		<?php if (!isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>
		<?php if (is_array($_smarty_tpl->tpl_vars['data']->value['coupons']) && count($_smarty_tpl->tpl_vars['data']->value['coupons']) > 0 && $_smarty_tpl->tpl_vars['data']->value['type'] == 0) {?>
		<ul class="tableView tableView-noMargin tableView-light">
			<li class="coupon">
				<a href="javascript:void(0)">
					<h1><big></big>优惠券</h1>
				</a>
			</li>
		</ul>
		<textarea class="coupons hidden"><?php echo json_encode($_smarty_tpl->tpl_vars['data']->value['coupons']);?>
</textarea>
		<?php }?>
		
		<?php if ($_smarty_tpl->tpl_vars['data']->value['type'] != 3) {?>
		<ul class="tableView tableView-noMargin tableView-light payMethod">
			<li><h1>支付方式</h1></li>
			<?php if ($_smarty_tpl->tpl_vars['data']->value['offline'] == 1) {?>
			<li class="offline">
				<h1>
					<input type="radio" name="pay" id="offline" value="offline" method_name="线下支付" />
					<label for="offline"><em></em>线下支付</label>
				</h1>
			</li>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['data']->value['money'] >= $_smarty_tpl->tpl_vars['data']->value['total_price']) {?>
			<li class="yue">
				<h1>
					<input type="radio" name="pay" id="yue" value="yue" method_name="余额支付" />
					<label for="yue"><em></em>余额支付</label>
				</h1>
			</li>
			<?php }?>
			<li class="wxpay">
				<h1>
					<input type="radio" name="pay" id="wxpay" value="wxpay_h5" method_name="微信支付" />
					<label for="wxpay"><em></em>微信支付</label>
				</h1>
			</li>
			<?php if ($_smarty_tpl->tpl_vars['is_app']->value == 1) {?>
			<li class="alipay">
				<h1>
					<input type="radio" name="pay" id="alipay" value="alipay" method_name="支付宝支付" />
					<label for="alipay"><em></em>支付宝支付</label>
				</h1>
			</li>
			<?php }?>
		</ul>
		<?php }?>
		<?php }?>
		
		<ul class="tableView tableView-noMargin tableView-light">
			<li>
				<h1>
					<?php if (isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>
					<big class="price"><?php echo $_smarty_tpl->tpl_vars['data']->value['goods_total_price'];?>
</big>商品积分
					<?php } else { ?>
					<big class="price"><?php echo number_format($_smarty_tpl->tpl_vars['data']->value['goods_total_price'],2,'.','');?>
</big>商品金额
					<?php }?>
				</h1>
			</li>
			<li><h1><big class="shipping_fee"><?php if ($_smarty_tpl->tpl_vars['data']->value['shipping_fee'] <= 0) {?><span>包邮</span><?php } else { ?>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value['shipping_fee'],2,'.','');
}?></big>运费</h1></li>
		</ul>
		
		<ul class="tableView tableView-noMargin tableView-light">
			<li>
				<h1><big class="memo"><input placeholder="选填，填写内容已和卖家协商确认" /></big>买家留言</h1>
			</li>
		</ul>
	</section>
	
	<div class="bottomView toolBar ge-top">
		<a href="javascript:void(0)" class="btn">提交订单</a>
		<?php if (isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>
		<div>总积分: <span><?php echo $_smarty_tpl->tpl_vars['data']->value['total_price'];?>
</span></div>
		<?php } else { ?>
		<div>
			<?php if ($_smarty_tpl->tpl_vars['data']->value['type'] == 3) {?>砍至底价后<?php if ($_smarty_tpl->tpl_vars['data']->value['total_price'] > 0) {?>付款 <span>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value['total_price'],2,'.','');?>
</span><?php } else { ?>免费获得<?php }?>
			<?php } else { ?>实付款: <span>￥<?php echo number_format($_smarty_tpl->tpl_vars['data']->value['total_price'],2,'.','');?>
</span><?php }?>
		</div>
		<?php }?>
	</div>
</div>

<form action="/wap/?app=cart&act=order_pay" method="post">
<input type="hidden" name="contactman" id="contactman" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->contactman;?>
" />
<input type="hidden" name="mobile" id="mobile" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->mobile;?>
" />
<input type="hidden" name="province" id="province" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->province;?>
" />
<input type="hidden" name="city" id="city" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->city;?>
" />
<input type="hidden" name="district" id="district" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->district;?>
" />
<input type="hidden" name="address" id="address" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->address;?>
" />
<input type="hidden" name="idcard" id="idcard" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address']->idcard;?>
" />
<input type="hidden" name="coupon_sn" id="coupon_sn" value="" />
<input type="hidden" name="memo" id="memo" value="" />
<?php if ($_smarty_tpl->tpl_vars['data']->value['type'] == 3) {?>
<input type="hidden" name="pay_method" id="pay_method" value="chop" />
<input type="hidden" name="pay_method_name" id="pay_method_name" value="砍价预支付" />
<?php } else { ?>
<input type="hidden" name="pay_method" id="pay_method" value="<?php if (isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>integral<?php }?>" />
<input type="hidden" name="pay_method_name" id="pay_method_name" value="<?php if (isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>积分兑换<?php }?>" />
<?php }?>
<textarea class="hidden" name="goods" id="goods"><?php echo stripslashes($_smarty_tpl->tpl_vars['goods']->value);?>
</textarea>
<input type="hidden" name="type" id="type" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['type'];?>
" />
<?php if (isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?><input type="hidden" name="integral_order" id="integral_order" value="1" /><?php }?>
<input type="hidden" name="parent_id" value="<?php if (isset($_smarty_tpl->tpl_vars['parent_id']->value)) {
echo $_smarty_tpl->tpl_vars['parent_id']->value;
}?>" />
</form>

<?php $_smarty_tpl->_subTemplateRender("file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 type="text/javascript" src="/js/city.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
var money = Number('<?php echo $_smarty_tpl->tpl_vars['data']->value['money'];?>
'), goods_total_price = Number('<?php echo $_smarty_tpl->tpl_vars['data']->value['goods_total_price'];?>
'), origin_price = Number('<?php echo $_smarty_tpl->tpl_vars['data']->value['total_price'];?>
');
$(function(){
	$('.coupon a').click(function(){
		var coupons = $('.coupons').val().json(), btns = [{
			text : '不使用优惠券',
			click : function(){
				$('.coupon big').html('');
				$('.bottomView span').html('￥'+origin_price.numberFormat(2)).priceFont('bigPrice');
				$('#coupon_sn').val('');
			}
		}];
		if($.isArray(coupons))for(var i=0; i<coupons.length; i++)btns.push({
			text : coupons[i].name+' -￥'+coupons[i].coupon_money.numberFormat(2),
			click : function(index){
				var i = index - 1, money = (origin_price-coupons[i].coupon_money).numberFormat(2);
				if(money<0)money = ('0').numberFormat(2);
				$('.coupon big').html(coupons[i].name+' -￥'+coupons[i].coupon_money.numberFormat(2));
				$('.bottomView span').html('￥'+money).priceFont('bigPrice');
				$('#coupon_sn').val(coupons[i].sn);
			}
		});
		$.actionView('选择优惠券', btns);
	});
	$('.payMethod li').click(function(){
		var input = $(this).find('input'), paymethod = input.val();
		if(window.is_app && paymethod=='wxpay_h5')paymethod = 'wxpay';
		$('#pay_method').val(paymethod);
		$('#pay_method_name').val(input.attr('method_name'));
	});
	$('.bottomView .btn').click(function(){
		$('#memo').val($('.memo input').val());
		if(!$('#contactman').val().length || !$('#mobile').val().length ||
			!$('#province').val().length || !$('#city').val().length || !$('#district').val().length || !$('#address').val().length){
			$.overloadError('请选择收货地址');
			return;
		}
		<?php if (!isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>
		if(!$('#pay_method').val().length){
			$.overloadError('请选择支付方式');
			return;
		}
		if($('#pay_method').val()=='yue' || $('#pay_method').val()=='chop'){
			if($('#pay_method').val()=='yue' && money<origin_price){
				$.overloadError('您的余额不足以支付');
				return;
			}
			/*
			//使用余额支付需要验证密码
			var html = $('<div class="cart-commit-confirm">\
				<font>验证密码</font>\
				<input type="password" id="password" placeholder="请输入登录密码" />\
				<div><a href="javascript:void(0)" class="cancel">取消</a>\
				<a href="javascript:void(0)" class="submit">确定</a></div>\
			</div>');
			$.overlay(html);
			html.find('.cancel').click(function(){
				$.overlay(false);
			});
			html.find('.submit').click(function(){
				var password = html.find('#password').val();
				if(!password.length){
					$.overloadError('请输入登录密码');
					return;
				}
				$.postJSON('/api/?app=member&act=check_password', { password:password }, function(json){
					if(json.error!=0){ $.overloadError('请选择支付方式');return }
					$('form').attr('action', '/api/?app=cart&act=order').ajaxsubmit({
						dataType : 'json',
						success : function(json){
							if(json.error!=0){ $.overloadError(json.msg);return }
							location.href = '/wap/?app=cart&act=order_complete&order_sn='+json.data.order_sn;
						}
					});
				});
			});
			*/
			$('form').attr('action', '/api/?app=cart&act=order').ajaxsubmit({
				dataType : 'json',
				success : function(json){
					if(json.error!=0){ $.overloadError(json.msg);return }
					switch(json.data.order_type){
						case 0:location.href = '/wap/?app=cart&act=order_complete&order_sn='+json.data.order_sn;break;
						case 1:location.href = '/wap/?app=groupbuy&act=detail&order_sn='+json.data.order_sn;break;
						case 2:location.href = '/wap/?app=cart&act=order_complete&order_sn='+json.data.order_sn;break;
						case 3:location.href = '/wap/?app=chop&act=detail&order_sn='+json.data.order_sn;break;
					}
				}
			});
		}else{
			$('form').attr('action', '/wap/?app=cart&act=order_pay').submit();
		}
		<?php } else { ?>
		$('form').attr('action', '/api/?app=cart&act=order').ajaxsubmit({
			dataType : 'json',
			success : function(json){
				if(json.error!=0){ $.overloadError(json.msg);return }
				location.href = '/wap/?app=cart&act=order_complete&integral_order=1&order_sn='+json.data.order_sn;
			}
		});
		<?php }?>
	});
	<?php if (!isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>$('big, .goods .price div, .bottomView span').priceFont('bigPrice');<?php }?>
	addAddress('.addressNo');
	getAddress('.address');
});

function addAddress(expr){
	pushBodyView(expr, function(bodyView, link, delegate){
		bodyView.find('#goalert, #gourl').remove();
		bodyView.find('.buttonView .btn').click(function(){
			var contactman = bodyView.find('#contactman').val(),
				mobile = bodyView.find('#mobile').val(),
				province = bodyView.find('#province').selected().val(),
				city = bodyView.find('#city').selected().val(),
				district = bodyView.find('#district').selected().val(),
				address = bodyView.find('#address').val();
				//idcard = bodyView.find('#idcard').val();
			if (!contactman.length || !mobile.length || !address.length) {
				$.overloadError('请填写完整');
				return;
			}
			bodyView.find('form').ajaxsubmit({
				dataType : 'json',
				success : function(json){
					if(delegate.find('.address-list').length){
						var ul = $('<ul class="list tableView tableView-noLine">\
							<li address_id="'+json.data+'">\
								<h1>\
									<div class="name">'+contactman+'　'+mobile+'</div>\
									<div class="address">'+showCity().comboCity(province, city, district, address, ' ')+'</div>\
								</h1>\
							</li>\
						</ul>');
						delegate.find('.address-list section').prepend(ul);
						ul.find('li').click(function(){
							setAddress($(this).attr('address_id'));
						});
					}
					setAddress(json.data);
				}
			});
		});
		firstValue = false;
		tipShow = false;
		showCity({ province:bodyView.find('#province'), city:bodyView.find('#city'), district:bodyView.find('#district'), applyName:true, getLocation:true });
		bodyView.find('.selects').onclick(function(){
			$.actionpicker({
				title : '请选择地区',
				rightBtn : {
					text : '确定',
					click : function(){
						$.actionpicker();
					}
				},
				before : function(){
					this.selectpicker({
						objs : [bodyView.find('#province'), bodyView.find('#city'), bodyView.find('#district')],
						cls : 'areapicker',
						autoWidth : false,
						select : function(component, row){
							var html = showCity().comboCity(bodyView.find('#province').selected().val(), bodyView.find('#city').selected().val(), bodyView.find('#district').selected().val());
							bodyView.find('.address-info .selects').addClass('selects-x');
							bodyView.find('.address-info .selects div').html(html);
						}
					});
				}
			});
		});
	});
};

function getAddress(expr){
	pushBodyView(expr, function(bodyView, link, delegate){
		bodyView.find('.bottomView').remove();
		addAddress(bodyView.find('.navBar .right'));
		bodyView.find('.tableView li').click(function(){
			setAddress($(this).attr('address_id'));
		});
	});
}
function setAddress(address_id){
	$.overload();
	$.postJSON('/api/?app=cart&act=change_address', { id:address_id, goods:$('#goods').val(), integral_order:'<?php if (isset($_smarty_tpl->tpl_vars['integral_order']->value)) {?>1<?php }?>' }, function(json){
		var shipping_fee = Number(json.data);
		origin_price = goods_total_price + shipping_fee;
		if(shipping_fee>0){
			$('.shipping_fee').html('￥'+shipping_fee.numberFormat(2)).priceFont('bigPrice');
		}else{
			$('.shipping_fee').html('<span>包邮</span>');
		}
		$('.bottomView span').html('￥'+origin_price.numberFormat(2)).priceFont('bigPrice');
		if(money<origin_price){
			$('.yue').addClass('hidden');
			$('#yue').checked(false);
		}else $('.yue').removeClass('hidden');
		$.getJSON('/api/?app=address&act=edit', { id:address_id }, function(json){
			var data = json.data;
			$('.addressView a').replaceWith('<a href="/wap/?app=address&act=index" class="address push-ico">\
				<div>\
					<span>收货人：'+data.contactman+'　'+data.mobile+'</span>\
					<span>'+showCity().comboCity(data.province, data.city, data.district, data.address, '')+'</span>\
				</div>\
			</a>');
			controllerView.find('#contactman').val(data.contactman);
			controllerView.find('#mobile').val(data.mobile);
			controllerView.find('#province').val(data.province);
			controllerView.find('#city').val(data.city);
			controllerView.find('#district').val(data.district);
			controllerView.find('#address').val(data.address);
			controllerView.find('#idcard').val(data.idcard);
			getAddress(controllerView.find('.address'));
			popBodyView();
		});
	});
}
<?php echo '</script'; ?>
><?php }
}
