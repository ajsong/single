<template>
<div class="favorite-index">
	<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit">
		<ul class="list goods-item" v-if="data">
			<li v-for="g in data">
				<router-link :to="{path:'/goods/detail', query:{id:g.id}}">
					<div ref="pic" class="pic" :url="g.pic"></div>
					<div class="title"><div>{{ g.name }}</div><font><template v-if="g.purchase_price>0">正在秒杀中</template></font><span><strong>￥{{ g.price|round }}</strong><s>￥{{ g.market_price|round }}</s></span></div>
				</router-link>
			</li>
		</ul>
	</pull-refresh>
</div>
</template>

<script>
import pullRefresh from '../../components/pullRefresh'
export default {
	name:'favorite',
	data(){
		return {
			data: null,

			scroller: null,
			refresh: {
				callback: this.getData,
				autoLoad: true
			},
			loadmore: {
				callback: this.getData,
				pagesize: 8
			}
		}
	},
	components:{
		pullRefresh
	},
	methods:{
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/favorite', {offset:(page.num - 1) * page.size, pagesize:page.size}).then(json => {
				if(!this.$.checkError(json, this))return
				if (!(json.data instanceof Array)) return scroller.end()
				let data = page.num === 1 ? [] : this.data
				data.push(...json.data)
				this.data = data
				this.$nextTick(() => {
					scroller.end()
					this.$(this.$refs.pic).loadbackground('url', '50%', '../images/nopic.png')
				})
			})
		}
	}
}
</script>

<style scoped>

</style>