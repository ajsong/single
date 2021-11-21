<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">我的余额</div>
	</div>

	<div class="member-money">
		<ul class="groupView">
			<li class="topView">
				<a href="javascript:void(0)">
					<div>账户余额</div>
					<span v-if="data">￥{{ data }}</span>
				</a>
			</li>
			<li class="ge-bottom ge-light" v-if="$.inArray('recharge', func) > -1">
				<router-link to="/recharge" class="push-ico">余额充值</router-link>
			</li>
			<li>
				<router-link to="/member/money_history" class="push-ico">余额明细</router-link>
			</li>
		</ul>
	</div>
</div>
</template>

<script>
export default {
	name:'money',
	data(){
		return {
			data: null,
			func: []
		}
	},
	created(){
		this.func = $.storageJSON('func')
		this.$ajax.get('/api/member/money').then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.$nextTick(() => {
				$('.topView span').priceFont('bigPrice')
			})
		})
	}
}
</script>

<style scoped>

</style>