<template>
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

		<ul class="tableView tableView-noMargin tableView-light">
			<li>
				<h1><big class="memo"><input placeholder="选填，填写内容已和卖家协商确认" /></big>买家留言</h1>
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
</template>

<script>
import eventBus from '../../plugins/eventBus';
export default {
	name:'commit',
	data(){
		return {
			params: null,
			data: null,
			money: 0,
			goods_total_price: 0,
			origin_price: 0
		}
	},
	created(){
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

			this.$nextTick(() => {
				$('big, .goods .price div, .bottomView span').priceFont('bigPrice')
			})
		})

		eventBus.$on('changeAddress', data => {
			this.setAddress(data)
		})
	},
	methods:{
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
			})
		}
	}
}
</script>

<style scoped>

</style>