<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">我的佣金</div>
	</div>

	<div class="member-money member-commission">
		<ul class="groupView">
			<li class="topView">
				<a href="javascript:void(0)">
					<div>佣金金额</div>
					<span v-if="data">￥{{ data }}</span>
				</a>
			</li>
			<li class="ge-bottom ge-light">
				<router-link to="/member/commission_history" class="push-ico">佣金收益明细</router-link>
			</li>
			<li class="ge-bottom ge-light">
				<router-link to="/member/my_invite_user" class="push-ico">我邀请的用户</router-link>
			</li>
			<li class="ge-bottom ge-light">
				<router-link to="/member/withdraw" class="push-ico">申请提现</router-link>
			</li>
			<li class="ge-bottom ge-light">
				<router-link to="/bank" class="push-ico">提现账户管理</router-link>
			</li>
			<li>
				<router-link to="/member/withdraw_history" class="push-ico">提现历史记录</router-link>
			</li>
		</ul>
		<router-link to="/article/detail?id=5">如何获得佣金？</router-link>
	</div>
</div>
</template>

<script>
export default {
	name:'commission',
	data(){
		return {
			data: null
		}
	},
	created(){
		this.$ajax.get('/api/member/commission').then(json => {
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