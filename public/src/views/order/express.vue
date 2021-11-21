<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">物流情况</div>
	</div>
	
	<div class="order-express" v-if="data">
		<div class="info ge-bottom ge-light">
			<div class="view">
				<div ref="pic" :url="$.isArray(data.goods) ? data.goods[0].goods_pic : ''"></div>
				<span>物流公司：{{ data.shipping_company }}</span>
				<span>快递单号：{{ data.shipping_number }}</span>
			</div>
		</div>
		
		<div class="express ge-top ge-light">
			<ul>
				<li>
					<div class="view">
						<div class="nav"><span class="ge-right"></span><div></div></div>
						<div class="content">[收货地址] {{ data.province }}<template v-if="data.province!==data.city"> {{ data.city }}</template> {{ data.district }} {{ data.address }}</div>
					</div>
				</li>
				<template v-if="$.isArray(data.express)">
				<li v-for="g in data.express">
					<div class="view">
						<div class="time">{{ g.day }}<span>{{ g.hour }}</span></div>
						<div class="nav"><span class="ge-right"></span><div></div></div>
						<div class="content">{{ g.context }}</div>
					</div>
				</li>
				</template>
				<div v-else class="no-record">暂时没有物流信息</div>
			</ul>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name: 'express',
	data() {
		return {
			id: 0,
			data: null
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
		this.$ajax.get('/api/order/express', {id:id}).then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.$nextTick(() => {
				$(this.$refs.pic).loadbackground('url', '100%', '../images/nopic.png')
			})
		})
	},
	methods: {
		
	}
}
</script>

<style scoped>

</style>