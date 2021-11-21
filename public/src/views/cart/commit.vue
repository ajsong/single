<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">确认订单</div>
	</div>

	<div class="cart-commit" v-if="data">
		<div class="addressView">
			<router-link to="/address/edit?from=cart" class="addressNo" v-if="Number(data.address.member_id) === 0">+ 请添加收货地址</router-link>
			<router-link to="/address?from=cart" class="address push-ico" v-else>
				<div>
					<span>收货人：{{ data.address.contactman }}　{{ data.address.mobile }}</span>
					<span v-html="data.address.province+(data.address.province!==data.address.city?' '+data.address.city:'')+' '+data.address.district+' '+data.address.address"></span>
				</div>
			</router-link>
		</div>

		<section>
			<ul class="tableView tableView-light cart-goods">
				<template v-for="s in data.shops">
				<li v-for="g in s.goods">
					<h1>
						<div class="item">
							<router-link class="pic" :to="{path:'/goods/detail', query:{id:g.id}}" :style="{'background-image':'url('+g.pic+')'}"></router-link>
							<router-link class="name" :to="{path:'/goods/detail', query:{id:g.id}}">{{ g.name }}</router-link>
							<div class="spec" v-html="(!Number(params.integral_order) && g.spec_name.length) ? g.spec_name : ''"></div>
							<div class="price clear-after" :price="Number(params.integral_order) ? g.integral : g.price" :quantity="g.quantity">
								<div v-if="Number(params.integral_order)">{{ g.integral }}积分</div>
								<template v-else>
								<span>×{{ g.quantity }}</span>
								<div>￥{{ g.price|round }}</div>
								</template>
							</div>
						</div>
					</h1>
				</li>
				</template>
			</ul>

			<template v-if="!Number(params.integral_order)">
			<template v-if="$.isArray(data.coupons) && data.coupons.length && data.type === 0">
			<ul class="tableView tableView-noMargin tableView-light">
				<li class="coupon">
					<a href="javascript:void(0)">
						<h1><big></big>优惠券</h1>
					</a>
				</li>
			</ul>
			<textarea class="coupons hidden" v-html="$.toJsonString(data.coupons)"></textarea>
			</template>

			<ul class="tableView tableView-noMargin tableView-light payMethod" v-if="data.type !== 3">
				<li><h1>支付方式</h1></li>
				<li class="offline" v-if="Number(data.offline) === 1">
					<h1>
						<input type="radio" name="pay" id="offline" value="offline" method_name="线下支付" />
						<label for="offline"><em></em>线下支付</label>
					</h1>
				</li>
				<li class="yue" v-if="data.money >= data.total_price">
					<h1>
						<input type="radio" name="pay" id="yue" value="yue" method_name="余额支付" />
						<label for="yue"><em></em>余额支付</label>
					</h1>
				</li>
				<li class="wxpay">
					<h1>
						<input type="radio" name="pay" id="wxpay" value="wxpay_h5" method_name="微信支付" />
						<label for="wxpay"><em></em>微信支付</label>
					</h1>
				</li>
			</ul>
			</template>

			<ul class="tableView tableView-noMargin tableView-light">
				<li>
					<h1>
						<template v-if="Number(params.integral_order)"><big class="price">{{ data.goods_total_price }}</big>商品积分</template>
						<template v-else><big class="price">{{ data.goods_total_price|round }}</big>商品金额</template>
					</h1>
				</li>
				<li><h1><big class="shipping_fee"><span v-if="data.shipping_fee <= 0">包邮</span><template v-else>￥{{ data.shipping_fee|round }}</template></big>运费</h1></li>
			</ul>

			<ul class="tableView tableView-noMargin tableView-light" v-if="Number(params.integral_order)===0">
				<li>
					<h1><big class="memo"><input placeholder="选填，填写内容已和卖家协商确认" v-model="memo" /></big>买家留言</h1>
				</li>
			</ul>
		</section>

		<div class="bottomView toolBar ge-top">
			<a href="javascript:void(0)" class="btn">提交订单</a>
			<div v-if="Number(params.integral_order)">总积分: <span>{{ data.total_price }}</span></div>
			<div v-else>
				<template v-if="data.type === 3">
					砍至底价后<template v-if="data.total_price > 0">付款 <span>￥{{ data.total_price|round }}</span></template><template v-else>免费获得</template>
				</template>
				<template v-else>
					实付款: <span>￥{{ data.total_price|round }}</span>
				</template>
			</div>
		</div>
	</div>
<!--<form action="/wap/?app=cart&act=order_pay" method="post">
<input type="hidden" name="contactman" id="contactman" value="{$data.address->contactman}" />
<input type="hidden" name="mobile" id="mobile" value="{$data.address->mobile}" />
<input type="hidden" name="province" id="province" value="{$data.address->province}" />
<input type="hidden" name="city" id="city" value="{$data.address->city}" />
<input type="hidden" name="district" id="district" value="{$data.address->district}" />
<input type="hidden" name="address" id="address" value="{$data.address->address}" />
<input type="hidden" name="idcard" id="idcard" value="{$data.address->idcard}" />
<input type="hidden" name="coupon_sn" id="coupon_sn" value="" />
<input type="hidden" name="memo" id="memo" value="" />
{if $data.type==3}
<input type="hidden" name="pay_method" id="pay_method" value="chop" />
<input type="hidden" name="pay_method_name" id="pay_method_name" value="砍价预支付" />
{else}
<input type="hidden" name="pay_method" id="pay_method" value="{if isset($integral_order)}integral{/if}" />
<input type="hidden" name="pay_method_name" id="pay_method_name" value="{if isset($integral_order)}积分兑换{/if}" />
{/if}
<textarea class="hidden" name="goods" id="goods">{stripslashes($goods)}</textarea>
<input type="hidden" name="type" id="type" value="{$data.type}" />
{if isset($integral_order)}<input type="hidden" name="integral_order" id="integral_order" value="1" />{/if}
<input type="hidden" name="parent_id" value="{if isset($parent_id)}{$parent_id}{/if}" />
</form>-->
</div>
</template>

<script>
export default {
	name:'commit',
	data(){
		return {
			params: null,
			data: null,
			money: 0,
			goods_total_price: 0,
			origin_price: 0,

			contactman: '',
			mobile: '',
			province: '',
			city: '',
			district: '',
			address: '',
			idcard: '',
			coupon_sn: '',
			memo: '',
			pay_method: '',
			pay_method_name: '',
			goods: '',
			type: '',
			integral_order: 0,
			parent_id: 0
		}
	},
	created(){
		$.paramAlive(this, '/address').then(data => {
			this.setAddress(data)
			this.create()
		}).catch(() => {
			this.params = this.$route.params
			if ($.isEmptyObject(this.params)) this.params = this.$route.query
			if ($.isEmptyObject(this.params)) {
				this.$emit('overloaderror', '请选择商品')
				this.$router.go(-1)
				return
			}
			this.$ajax.post('/api/cart/commit', this.params).then(json => {
				if (!$.checkError(json, this)) return
				this.data = json.data
				this.money = Number(json.data.money)
				this.goods_total_price = Number(json.data.goods_total_price)
				this.origin_price = Number(json.data.total_price)

				this.goods = this.params.goods
				this.type = this.params.type
				this.integral_order = this.params.integral_order
				this.parent_id = this.params.parent_id

				this.contactman = this.data.address.contactman
				this.mobile = this.data.address.contactman
				this.province = this.data.address.contactman
				this.city = this.data.address.contactman
				this.district = this.data.address.contactman
				this.address = this.data.address.contactman
				this.idcard = this.data.address.contactman

				if (this.type === 3) {
					this.pay_method = 'chop'
					this.pay_method_name = '砍价预支付'
				} else {
					if (this.integral_order) {
						this.pay_method = 'integral'
						this.pay_method_name = '积分兑换'
					}
				}

				this.create()
			})
		})
	},
	methods:{
		create(){
			let vm = this
			this.$nextTick(() => {
				$('big, .goods .price div, .bottomView span').priceFont('bigPrice')
				$('.coupon a').click(function(){
					let coupons = vm.data.coupons, btns = [{
						text : '不使用优惠券',
						click : function(){
							$('.coupon big').html('');
							$('.bottomView span').html('￥'+$.round(vm.origin_price, 2)).priceFont('bigPrice');
							vm.coupon_sn = ''
						}
					}];
					if($.isArray(coupons))for(let i=0; i<coupons.length; i++)btns.push({
						text : coupons[i].name+' -￥'+$.round(coupons[i].coupon_money, 2),
						click : function(index){
							let i = index - 1, money = $.round(vm.origin_price-coupons[i].coupon_money, 2);
							if(money<0)money = '0.00';
							$('.coupon big').html(coupons[i].name+' -￥'+$.round(coupons[i].coupon_money, 2));
							$('.bottomView span').html('￥'+money).priceFont('bigPrice');
							vm.coupon_sn = coupons[i].sn
						}
					});
					$.actionView('选择优惠券', btns);
				});
				$('.payMethod li').click(function(){
					let input = $(this).find('input'), paymethod = input.val();
					//if(paymethod==='wxpay_h5')paymethod = 'wxpay';
					vm.pay_method = paymethod
					vm.pay_method_name = input.attr('method_name')
				});
				$('.bottomView .btn').click(function(){
					if(!vm.contactman.length || !vm.mobile.length ||
						!vm.province.length || !vm.city.length || !vm.district.length || !vm.address.length){
						$.overloadError('请选择收货地址');
						return;
					}
					if (!Number(vm.params.integral_order)) {
						if(!vm.pay_method.length){
							$.overloadError('请选择支付方式');
							return;
						}
						if(vm.pay_method==='yue' || vm.pay_method==='chop'){
							if(vm.pay_method==='yue' && money < origin_price){
								$.overloadError('您的余额不足以支付');
								return;
							}
							/*
							//使用余额支付需要验证密码
							let html = $('<div class="cart-commit-confirm">\
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
								let password = html.find('#password').val();
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
							vm.$ajax.post('/api/cart/order', {
								contactman: vm.contactman,
								mobile: vm.mobile,
								province: vm.province,
								city: vm.city,
								district: vm.district,
								address: vm.address,
								idcard: vm.idcard,
								coupon_sn: vm.coupon_sn,
								memo: vm.memo,
								pay_method: vm.pay_method,
								pay_method_name: vm.pay_method_name,
								goods: vm.goods,
								type: vm.type,
								integral_order: vm.integral_order,
								parent_id: vm.parent_id
							}).then(json => {
								if (!$.checkError(json, vm)) return
								switch (Number(json.data.order_type)) {
									case 0:vm.$router.push({path:'/cart/order_complete', query:{order_sn:json.data.order_sn}});break;
									case 1:vm.$router.push({path:'/groupbuy/detail', query:{order_sn:json.data.order_sn}});break;
									case 2:vm.$router.push({path:'/cart/order_complete', query:{order_sn:json.data.order_sn}});break;
									case 3:vm.$router.push({path:'/chop/detail', query:{order_sn:json.data.order_sn}});break;
								}
							})
						}else{
							vm.$router.push({name:'orderpay', params:{
									contactman: vm.contactman,
									mobile: vm.mobile,
									province: vm.province,
									city: vm.city,
									district: vm.district,
									address: vm.address,
									idcard: vm.idcard,
									coupon_sn: vm.coupon_sn,
									memo: vm.memo,
									pay_method: vm.pay_method,
									pay_method_name: vm.pay_method_name,
									goods: vm.goods,
									type: vm.type,
									integral_order: vm.integral_order,
									parent_id: vm.parent_id
								}
							})
						}
					} else {
						vm.$ajax.post('/api/cart/order', {
							contactman: vm.contactman,
							mobile: vm.mobile,
							province: vm.province,
							city: vm.city,
							district: vm.district,
							address: vm.address,
							idcard: vm.idcard,
							coupon_sn: vm.coupon_sn,
							memo: vm.memo,
							pay_method: vm.pay_method,
							pay_method_name: vm.pay_method_name,
							goods: vm.goods,
							type: vm.type,
							integral_order: vm.integral_order,
							parent_id: vm.parent_id
						}).then(json => {
							if (!$.checkError(json, vm)) return
							vm.$router.push({path:'/cart/order_complete', query:{order_sn:json.data.order_sn, integral_order:1}})
						})
					}
				})
			})
		},
		setAddress(address){
			this.$ajax.post('/api/cart/change_address', { id:address.id, goods:this.params.goods, integral_order:this.params.integral_order }).then(json => {
				if (!$.checkError(json, this)) return
				let shipping_fee = Number(json.data)
				this.origin_price = this.data.goods_total_price + shipping_fee
				if (shipping_fee>0) {
					$('.shipping_fee').html('￥'+$.round(shipping_fee, 2)).priceFont('bigPrice')
				} else {
					$('.shipping_fee').html('<span>包邮</span>')
				}
				$('.bottomView span').html('￥'+$.round(this.origin_price, 2)).priceFont('bigPrice')
				if (this.money < this.origin_price) {
					$('.yue').addClass('hidden')
					$('#yue').checked(false)
				} else $('.yue').removeClass('hidden')
				this.data.address = address
				this.contactman = this.data.address.contactman
				this.mobile = this.data.address.contactman
				this.province = this.data.address.contactman
				this.city = this.data.address.contactman
				this.district = this.data.address.contactman
				this.address = this.data.address.contactman
				this.idcard = this.data.address.contactman
			})
		}
	}
}
</script>

<style scoped>

</style>