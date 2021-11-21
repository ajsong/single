<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">余额充值</div>
	</div>

	<div class="recharge-index" v-if="data">
		<ul>
			<li v-for="g in data.recharges">
				<router-link :to="{path:'/recharge/commit', query:{id:g.id}}">
					<div>马上<br />充值</div>
					<strong>{{ g.price }}</strong>
					<span v-if="g.bonus>0">充值{{ g.price }} 送{{ g.bonus }}</span>
				</router-link>
			</li>
		</ul>
	</div>
</div>
</template>

<script>
export default {
	name: 'recharge',
	data() {
		return {
			data: null
		}
	},
	created(){
		this.$ajax.get('/api/recharge').then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
		})
	}
}
</script>

<style scoped>

</style>