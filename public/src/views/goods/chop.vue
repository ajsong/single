<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">限量砍价</div>
	</div>

	<div class="goods-activity goods-purchase">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit">
			<ul class="list" v-if="data && $.isArray(data.goods) && data.goods.length">
				<li v-for="g in data.goods">
					<router-link :to="{path:'/goods/detail', query:{id:g.id}}">
						<div ref="pic" class="pic" :url="g.pic"></div>
						<div class="title">{{ g.name }}</div>
						<div class="content">
							<div class="chop">参与人数：{{ g.chop_amount }}</div>
						</div>
						<font class="btn"><b>发起砍价</b></font>
						<span class="price"><span>底价：</span><strong>￥{{ g.chop_price|round }}</strong></span>
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
	name:'chop',
	data () {
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
			let params = $.extend(this.params, {
				offset: (page.num - 1) * page.size,
				pagesize: page.size
			})
			this.$ajax.get('/api/goods/chop', params).then(json => {
				if (!$.checkError(json, this)) return
				if (!this.data) {
					this.data = json.data
				} else {
					if (!(json.data.goods instanceof Array)) return scroller.end()
					let data = page.num === 1 ? [] : this.data.goods
					data.push(...json.data.goods)
					this.data.goods = data
				}
				this.$nextTick(() => {
					scroller.end()
					$(this.$refs.pic).caller(this).loadbackground('url', '50%', '../images/nopic.png')
				})
			})
		}
	}
}
</script>

<style scoped>

</style>