<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">我的积分</div>
	</div>

	<div class="member-money member-integral">
		<ul class="groupView">
			<li class="topView">
				<a href="javascript:void(0)">
					<div>积分余额</div>
					<span>{{ data }}</span>
				</a>
			</li>
			<li>
				<router-link to="/member/integral_history" class="push-ico">积分明细</router-link>
			</li>
		</ul>
	</div>
</div>
</template>

<script>
export default {
	name:'integral',
	data(){
		return {
			data: null
		}
	},
	created(){
		this.$ajax.get('/api/member/integral').then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
		})
	}
}
</script>

<style scoped>

</style>