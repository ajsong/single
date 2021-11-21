<template>
<div>
	<div class="navBar">
		<router-link class="left" to="/member"><i class="return"></i></router-link>
		<div class="titleView-x">下单成功</div>
	</div>

	<div class="cart-pay">
		<i></i>
		<div class="tip">恭喜您，下单成功</div>
		<div class="font">我们将尽快为您配送</div>

		<div class="buttonView">
			<router-link class="btn" :to="{path:'/order/detail', query:{order_sn:order_sn, integral_order:(integral_order?1:'')}}">查看订单详情</router-link>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name: 'complete',
	data() {
		return {
			integral_order: 0,
			order_sn: ''
		}
	},
	created(){
		this.order_sn = this.$route.query.order_sn
		if (!this.order_sn) {
			alert('missing order_sn')
			this.$router.go(-1)
			return
		}
		if (this.$route.query.integral_order) this.integral_order = Number(this.$route.query.integral_order)
	}
}
</script>

<style scoped>

</style>