<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">我的收藏</div>
	</div>

	<div class="favorite-index">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
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
				size: 8
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
				if (!$.checkError(json, this)) return scroller.end()
				if (!(json.data instanceof Array)) {
					this.data = null
					return scroller.end()
				}
				let data = page.num === 1 ? [] : this.data
				data.push(...json.data)
				this.data = data
				this.$nextTick(() => {
					scroller.end()
					$(this.$refs.pic).loadbackground('url', '50%', '../images/nopic.png')
				})
			})
		}
	}
}
</script>

<style scoped>

</style>