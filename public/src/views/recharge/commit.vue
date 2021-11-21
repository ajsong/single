<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">充值</div>
	</div>
	
	<div class="recharge-order" v-if="data">
		<div class="view">
			<div class="price">
				金额　<span>￥{{ data.price }}</span>
				<div v-if="data.bonus>0">充值{{ data.price }} 送{{ data.bonus }}</div>
			</div>
		</div>
		
		<div class="group">
			<div class="view f">
				<div class="tip ge-bottom ge-light">支付方式</div>
			</div>
			<div class="view f pay">
				<a href="javascript:void(0)" class="wx" pay_method="wxpay_h5"><i></i>微信支付</a>
			</div>
		</div>
		
		<div class="buttonView">
			<a class="btn pass" href="javascript:void(0)">确定</a>
		</div>
	</div>
	
	<form action="/wap/?app=recharge&act=order_pay" method="post">
	<input type="hidden" name="id" id="id" value="{data.id}" />
	<input type="hidden" name="price" value="{data.price}" />
	<input type="hidden" name="bonus" value="{data.bonus}" />
	<input type="hidden" name="pay_method" id="pay_method" value="wxpay_h5" />
	<input type="hidden" name="pay_method_name" value="微信支付" />
	</form>
</div>
</template>

<script>
export default {
	name: 'rechargeCommit',
	data() {
		return {
			id: 0,
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
		this.$ajax.get('/api/recharge/commit', {id:id}).then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.$nextTick(() => {
				$('.price span').priceFont('bigPrice');
				$('.pay a').click(function(){
					$('.pay a').removeClass('x');
					$(this).addClass('x');
					$('#pay_method').val($(this).attr('pay_method'));
				});
				$('.btn').click(() => {
					if (!$('#pay_method').val().length) {
						$.overloadError('请选择支付方式');
						return;
					}
					this.$ajax.post('/api/recharge/order_pay', {id:id, price:json.data.price, bonus:json.data.bonus, pay_method:'wxpay_h5', pay_method_name:'微信支付'}).then(json => {
						if (!$.checkError(json, this)) return
						this.jsApiParameters = json.data
						this.callpay()
					})
				});
			})
		})
	},
	methods: {
		jsApiCall(){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				this.jsApiParameters,
				res => {
					WeixinJSBridge.log(res.err_msg)
					if (res.err_msg && res.err_msg === 'get_brand_wcpay_request:ok') {
						this.$router.push('/recharge')
					} else if (res.err_msg && res.err_msg === 'get_brand_wcpay_request:cancel') {
						$('.recharge-order .buttonView .pass').addClass('wx').html('重新支付');
					} else if (res.errMsg) {
						alert('支付失败，原因：\n'+res.errMsg);
					} else {
						alert('支付失败，原因：未知');
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