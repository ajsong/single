<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">银行卡列表</div>
		<router-link class="right" to="/bank/set" v-if="!from"><i class="bank-set"></i></router-link>
	</div>

	<div class="bank-index">
		<ul class="list" v-if="data">
			<li v-for="(g, i) in data">
				<a href="javascript:void(0)" @click="selectBank(i)">
					<div>
						<span class="name">{{ g.bank_name }}</span>
						<span class="type">储蓄卡</span>
						<big v-html="'**** **** **** '+g.bank_card.substr(-4)"></big>
					</div>
				</a>
			</li>
		</ul>
		<div v-else class="norecord">当前没有任何记录</div>

		<div class="submitView" v-if="!from">
			<router-link to="/bank/edit" class="btn">添加银行卡</router-link>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name:'bank',
	data(){
		return {
			from: '',
			data: null
		}
	},
	created(){
		if (this.$route.query.from) this.from = this.$route.query.from
		this.$ajax.get('/api/bank').then(json => {
			if(!$.checkError(json, this)) return
			this.data = json.data
		})
	},
	methods:{
		selectBank(index){
			let data = this.data[index]
			if (this.from === 'withdraw') {
				$.paramAlive(this.$route.path, data)
				this.$router.go(-1)
			} else {
				this.$router.push({path:'/bank/edit', query:{id:data.id}})
			}
		}
	}
}
</script>

<style scoped>

</style>