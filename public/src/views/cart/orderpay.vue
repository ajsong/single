<template>
<div>
	<div class="navBar">
		<router-link class="left" to="/member"><i class="return"></i></router-link>
		<div class="titleView-x">下单成功</div>
	</div>

	<div class="cart-pay">
		<i></i>
		<div class="tip">恭喜您，下单成功！</div>
		<div class="font">支付后我们将尽快为您配送</div>
		<div class="buttonView">
			<router-link class="btn" to="/member">返回会员中心</router-link>
			<a class="wx" href="javascript:void(0)" @click="callpay">微信支付</a>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name:'orderpay',
	data(){
		return {
			params: null,
			data: null
		}
	},
	created(){
		if (!this.$route.params) {
			alert('missing params')
			this.$router.go(-1)
			return
		}
		this.params = this.$route.params
		this.$ajax.post('/api/cart/order_pay', this.params).then(json => {
			if (!$.checkError(json, this)) return
			this.data = {
				order_sn: json.order_sn,
				total_price: json.total_price,
				order_type: json.order_type,
				pay_method_name: json.pay_method_name
			}
			this.$nextTick(() => {
				this.callpay()
			})
		})
	},
	methods:{
		callpay(){
			this.orderPay({
				order_sn: this.data.order_sn,
				total_price: Number(this.data.total_price),
				success: () => {
					switch (Number(this.data.order_type)) {
						case 0:this.$router.push({path:'/cart/order_complete', query:{order_sn:this.data.order_sn}});break;
						case 1:this.$router.push({path:'/groupbuy/detail', query:{order_sn:this.data.order_sn}});break;
						case 2:this.$router.push({path:'/cart/order_complete', query:{order_sn:this.data.order_sn}});break;
						case 3:this.$router.push({path:'/chop/detail', query:{order_sn:this.data.order_sn}});break;
					}
				},
				cancel: () => {
					$('.cart-pay a.wx').html('重新支付');
				},
				error: res => {
					alert('支付失败，原因：\n'+res.errMsg);
				}
			})
		},
		orderPay(options){
			if (typeof options.pay_method === 'undefined') options.pay_method = 'wxpay'
			this.$ajax.post('/api/other/pay', options).then(json => {
				if (!$.checkError(json, this)) return
				if (options.pay_method === 'wxpay') {
					let jsApiCall = () => {
						WeixinJSBridge.invoke(
							'getBrandWCPayRequest',
							json.data,
							res => {
								//WeixinJSBridge.log(res.err_msg);
								if (res.err_msg && res.err_msg === 'get_brand_wcpay_request:ok') {
									if ($.isFunction(options.success)) options.success(res)
								} else if(res.err_msg && res.err_msg === 'get_brand_wcpay_request:cancel') {
									if ($.isFunction(options.cancel)) options.cancel(res)
								} else if(res.errMsg) {
									console.log('支付失败，原因：'+res.errMsg)
									if ($.isFunction(options.error)) options.error(res)
								} else {
									console.log('支付失败，原因：未知')
									if ($.isFunction(options.error)) options.error({errMsg:'未知'})
								}
							}
						)
					}
					if (typeof WeixinJSBridge === 'undefined') {
						if (document.addEventListener) {
							document.addEventListener('WeixinJSBridgeReady', jsApiCall, false)
						} else if (document.attachEvent) {
							document.attachEvent('WeixinJSBridgeReady', jsApiCall)
							document.attachEvent('onWeixinJSBridgeReady', jsApiCall)
						}
					} else {
						jsApiCall()
					}
				} else if (options.pay_method === 'alipay') {
					location.href = json.data
				}
			})
		}
	}
}
</script>

<style scoped>

</style>