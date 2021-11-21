<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">订单详情</div>
	</div>
	
	<div class="order-detail" v-if="data">
		<ul class="groupView">
			<li>
				<div class="top status{data.status}">
					<div><template v-if="Number(data.status)===2">卖家</template>{{ data.status_name }}</div>
					<span v-if="data.auto_shouhuo">{{ data.auto_shouhuo }}</span>
				</div>
				<router-link v-if="$.isArray(data.express)" :to="{path:'/order/express', query:{id:data.id}}" class="express ge-bottom ge-light push-ico">
					<div>
						<font>{{ data.express[0].context }}</font>
						<span>{{ data.express[0].time }}</span>
					</div>
				</router-link>
				<div class="address">
					<div>
						<font>{{ data.mobile }}</font>
						<span>收货人: {{ data.name }}</span>
						<span>收货地址：{{ data.province }}<template v-if="data.province!==data.city"> {{ data.city }}</template> {{ data.district }} {{ data.address }}</span>
					</div>
				</div>
			</li>
			
			<li>
				<ul>
					<li class="ge-bottom ge-light" v-for="g in data.goods">
						<div class="gview" :price="g.price" :quantity="g.quantity">
							<router-link ref="pic" :to="{path:'/goods/detail', query:{id:g.goods_id}}" class="pic" :url="g.goods_pic"></router-link>
							<router-link :to="{path:'/goods/detail', query:{id:g.goods_id}}" class="name right">{{ g.goods_name }}</router-link>
							<template v-if="integral_order===0">
							<div class="spec">{{ g.spec }}</div>
							<div class="price"><span>×{{ g.quantity }}</span><div>￥{{ g.single_price|round }}</div></div>
							<template v-if="Number(data.status)===3 || Number(data.status)===4">
							<div class="comment" v-if="Number(g.comment_time)===0"><router-link :to="{path:'/order/comment', query:{id:data.id}}"><span>评价</span></router-link></div>
							<div class="comment" v-else><font>已评价</font></div>
							</template>
							</template>
						</div>
					</li>
				</ul>
				<div class="view">
					<div class="trans"><strong><template v-if="data.shipping_price>0">￥{{ data.shipping_price|round }}</template><template v-else>免运费</template></strong><span>运费</span></div>
					<div class="total" v-if="integral_order===0"><strong>￥{{ data.total_price|round }}</strong><span>实付款<template v-if="data.shipping_price>0">(含运费)</template></span></div>
					<div class="total" v-else><strong v-html="parseInt(data.total_price)+'积分'"></strong><span>总积分</span></div>
				</div>
				<!--
				<div class="opr ge-top ge-light">
					<a class="im ge-right ge-light" href="javascript:void(0)" member_id="yanglanzi"><div><span>联系客服</span></div></a>
					<a class="tel" :href="'tel://'+data.shop_mobile"><div><span>拨打电话</span></div></a>
				</div>
				-->
			</li>

			<li v-if="data.shipping_number">
				<div class="info">
					<div>物流公司: {{ data.shipping_company }}</div>
					<div>物流单号: {{ data.shipping_number }}</div>
				</div>
			</li>
			
			<li>
				<div class="info">
					<div>订单状态: <span :class="['status', 'status'+data.status]"><font>{{ data.status_name }}</font><template v-if="data.status>0 && data.ask_refund_time>0"> <span>(退货退款中)</span></template></span></div>
					<div>订单编号: {{ data.order_sn }}</div>
					<div>创建时间: {{ data.add_time_word }}</div>
					<div v-if="integral_order===0 && data.pay_time>0">付款时间: {{ data.pay_time_word }}</div>
					<div v-if="data.shipping_time>0">发货时间: {{ data.shipping_time_word }}</div>
					<div v-if="data.shouhuo_time>0">成交时间: {{ data.shouhuo_time_word }}</div>
				</div>
			</li>
		</ul>

		<div class="bottomView" v-if="(integral_order===0 && data.status>=0) || (integral_order===0 && Number(data.status)===2)">
			<div class="ge-top ge-light">
				<template v-if="Number(data.status)===0">
				<a class="pay" href="javascript:void(0)" :pay_method="data.pay_method"><span>立即支付</span></a>
				<a class="cancel" href="javascript:void(0)"><span>取消订单</span></a>
				</template>
				<template v-if="Number(data.status)===1">
				<a class="refund" href="javascript:void(0)" v-if="integral_order===0 && data.ask_refund_time===0"><span>我要退款</span></a>
				<a class="ask" href="javascript:void(0)"><span>提醒商家发货</span></a>
				</template>
				<template v-if="Number(data.status)===2">
				<a class="ok" href="javascript:void(0)"><span>确认收货</span></a>
				<a class="delay" href="javascript:void(0)" v-if="integral_order===0 && data.delay_shouhuo_time===0"><span>延长收货</span></a>
				<a class="refund" href="javascript:void(0)" v-if="integral_order===0 && data.ask_refund_time===0"><span>我要退款</span></a>
				</template>
				<template v-if="data.status>=3">
				<a class="again" href="javascript:void(0)"><span>再次购买</span></a>
				</template>
			</div>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name:'orderDetail',
	data(){
		return {
			id: 0,
			for_pay: 0,
			integral_order: 0,
			data: null,
			jsApiParameters: null
		}
	},
	created(){
		let id = this.$route.query.id
		if (!id) {
			alert('missing id')
			this.$router.go(-1)
			return
		}
		this.id = id
		if (this.$route.query.for_pay) this.for_pay = Number(this.$route.query.for_pay)
		if (this.$route.query.integral_order) this.integral_order = Number(this.$route.query.integral_order)
		this.getData(() => {
			let vm = this
			$('.im').click(function(){
				vm.$router.push({path:'/chat/talk', query:{member:$(this).attr('member_id')}})
			})
			$('a.pay').click(function(){
				if($.browser.wechat && $(this).attr('pay_method')==='alipay'){
					alert('在微信上只能使用微信支付\n该订单下单时使用了支付宝方式')
					return
				}
				if (vm.jsApiParameters) {
					this.callpay()
				} else {
					vm.$ajax.post('/api/order/pay', {order_sn:vm.data.order_sn}).then(json => {
						if (!$.checkError(json, vm)) return
						vm.$router.push('/order/pay')
					})
				}
			})
			$('a.cancel').click(function(){
				if(!confirm('真的要取消吗？'))return
				vm.$ajax.post('/api/order/cancel', {id:vm.data.id}).then(json => {
					if (!$.checkError(json, vm)) return
					vm.getData()
				})
			})
			$('a.refund').click(function(){
				vm.$router.push({path:'/order/refund', query:{id:vm.data.id}})
			})
			$('a.ask').click(function(){
				vm.$ajax.post('/api/order/ask_shipping', {id:vm.data.id}).then(json => {
					if (!$.checkError(json, vm)) return
					alert('已提醒商家尽快发货，请耐心等待')
					vm.getData()
				})
			})
			$('a.delay').click(function(){
				if(!confirm('确定需要延迟收货吗？'))return
				vm.$ajax.post('/api/order/delay_shouhuo', {id:vm.data.id}).then(json => {
					if (!$.checkError(json, vm)) return
					alert('已执行延迟收货操作')
					vm.getData()
				})
			})
			$('a.ok').click(function(){
				if(!confirm(integral_order===0?'请收到货后，再确认收货！\n否则您可能钱货两空':'确认收货吗？'))return
				vm.$ajax.post('/api/order/shouhuo', {id:vm.data.id}).then(json => {
					if (!$.checkError(json, vm)) return
					vm.getData()
				})
			})
			$('a.again').click(function(){
				let count = 0, data = vm.data.goods
				if(!$.isArray(data))return
				$.each(data, function(){
					let goods = JSON.stringify([{ goods_id:this.goods_id, spec:this.spec_linkage, quantity:1 }])
					vm.$ajax.post('/api/cart/add', {goods:goods}).then(json => {
						if (!$.checkError(json, vm)) return
						count++
						if(count === data.length){
							vm.$router.push('/cart')
						}
					})
				});
			})
		})
	},
	methods:{
		getData(callback){
			this.$ajax.get('/api/order/detail', {id:this.id}).then(json => {
				if (!$.checkError(json, this)) return
				this.data = json.data
				this.jsApiParameters = json.jsApiParameters ? json.jsApiParameters : null
				this.$nextTick(() => {
					$(this.$refs.pic).loadbackground('url', '80%', '../images/nopic.png')
					if (this.integral_order === 0) $('.view .total strong, .price div').priceFont('bigPrice')
					if (callback) callback()
				})
			})
		},
		//调用微信JS api 支付
		jsApiCall(){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				this.jsApiParameters,
				res => {
					WeixinJSBridge.log(res.err_msg)
					if (res.err_msg === 'get_brand_wcpay_request:ok') {
						this.$router.push({path:'/cart/order_complete', query:{order_sn:this.data.order_sn}})
					} else {
						//alert('支付失败， 原因：\n'+res.err_desc);
						$('a.pay').html('<span>重新支付</span>')
					}
				}
			)
		},
		callpay(){
			if (typeof WeixinJSBridge === 'undefined') {
				if (document.addEventListener) {
					document.addEventListener('WeixinJSBridgeReady', this.jsApiCall, false)
				} else if (document.attachEvent) {
					document.attachEvent('WeixinJSBridgeReady', this.jsApiCall)
					document.attachEvent('onWeixinJSBridgeReady', this.jsApiCall)
				}
			} else {
				this.jsApiCall()
			}
		}
	}
}
</script>

<style scoped>

</style>