<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">特价拼团</div>
	</div>

	<div class="goods-activity goods-groupbuy">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit">
			<ul class="list" v-if="data && $.isArray(data.goods) && data.goods.length">
				<li v-for="g in data.goods">
					<router-link :to="{path:'/goods/detail', query:{id:g.id}}">
						<div ref="pic" class="pic" :url="g.pic"></div>
						<div class="title">{{ g.name }}</div>
						<div class="content">
							<div class="groupbuy">
								<template v-if="$.isArray(g.groupbuy_list)">
								<i v-for="m in g.groupbuy_list" :style="{'background-image':(m.avatar?'url('+m.avatar+')':'')}"></i>
								</template>
								<span>已团{{ g.groupbuy_count }}件</span>
							</div>
						</div>
						<font class="btn"><b>去拼团</b></font>
						<span class="price"><strong>￥{{ g.groupbuy_price|round }}</strong><s>￥{{ g.market_price|round }}</s></span>
					</router-link>
				</li>
			</ul>
			<div class="norecord" v-else>暂时没有任何商品</div>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import pullRefresh from '../../components/pullRefresh'
export default {
	name:'groupbuy',
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
			let params = $.extend(this.params, {
				offset: (page.num - 1) * page.size,
				pagesize: page.size
			})
			this.$ajax.get('/api/goods/groupbuy', params).then(json => {
				if (!$.checkError(json, this)) return scroller.end()
				if (!this.data) {
					this.data = json.data
				} else {
					if (!(json.data.goods instanceof Array)) {
						this.data.goods = null
						return scroller.end()
					}
					let data = page.num === 1 ? [] : this.data.goods
					data.push(...json.data.goods)
					this.data.goods = data
				}
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