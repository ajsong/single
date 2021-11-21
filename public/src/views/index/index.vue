<template>
	<div>
		<div class="navBar">
			<div class="titleView-x search-input"><input type="search" placeholder="请输入商品关键字" v-model="keyword" @keydown.enter="searchPro" /></div>
		</div>

		<div class="home-index main-padding-bottom">
			<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44+0.49)">
				<slideView v-if="data && data.flashes.length" :list="data.flashes" :isAds="true" :height="'1.53rem'"></slideView>

				<div class="cate" v-if="$.inArray('category', func) > -1">
					<router-link class="groupbuy" to="/goods/groupbuy" v-if="$.inArray('groupbuy', func) > -1"><div></div><span>特价拼团</span></router-link>
					<router-link class="purchase" to="/goods/purchase" v-if="$.inArray('purchase', func) > -1"><div></div><span>限时秒杀</span></router-link>
					<router-link class="chop" to="/goods/chop" v-if="$.inArray('chop', func) > -1"><div></div><span>限量砍价</span></router-link>
					<router-link v-for="g in data.categories" :to="{path:'/goods', query:{category_id:g.id, title:g.name}}"><div ref="pic" data-name="category" :url="g.pic"></div><span>{{ g.name }}</span></router-link>
				</div>
				<template v-if="data && data.recommend.length">
				<div class="tip2 tip"><i></i>好货推荐</div>
				<ul class="list goods-item">
					<li v-for="g in data.recommend">
						<router-link :to="{path:'/goods/detail', query:{id:g.id}}">
							<div class="pic" ref="pic" :url="g.pic"></div>
							<div class="title"><div>{{g.name}}</div><font><div v-if="g.purchase_price>0">正在秒杀中</div></font><span><strong>￥{{g.price|round(2)}}</strong><s>￥{{g.market_price|round(2)}}</s></span></div>
						</router-link>
					</li>
				</ul>
				</template>
			</pull-refresh>
		</div>
	</div>
</template>

<script>
import pullRefresh from '../../components/pullRefresh'
import slideView from '../../components/slideView'
export default {
	name: 'index',
	data(){
		return {
			keyword: '',
			data: null,
			func: [],

			scroller: null,
			refresh: {
				callback: this.getData,
				autoLoad: true
			},
			loadmore: {
				callback: this.getData,
				size: 12
			}
		}
	},
	components:{
		pullRefresh,
		slideView
	},
	methods: {
		searchPro(){
			if(!this.keyword.length){
				//this.$emit('overloaderror', '请输入商品关键字')
				//return
			}
			this.$router.push({path:'/search', query:{keyword:this.keyword}})
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api', {offset:(page.num - 1) * page.size, pagesize:page.size}).then(json => {
				if (!$.checkError(json, this)) return scroller.end()
				if(!this.data){
					$.storage('func', json.function)
					$.storage('edition', json.edition)
					this.func = json.function
					this.data = json.data
				}
				if (!(json.data.recommend instanceof Array)) {
					this.data.recommend = null
					return scroller.end()
				}
				let data = page.num === 1 ? [] : this.data.recommend
				data.push(...json.data.recommend)
				this.data.recommend = data
				this.$nextTick(() => {
					scroller.end()
					//$(this.$refs.pic).loadbackground('url', item => item.getAttribute('data-name')==='category' ? '100%' : '50%', '../images/nopic.png')
					this.$refs.pic.forEach(item => {
						if (!item.getAttribute('url')) return
						$(item).loadpic(item.getAttribute('url'), '../images/nopic.png', (item, pic, state) => {
							item.removeAttribute('url')
							item.style.backgroundImage = `url(${pic})`
							if (!state) {
								item.style.backgroundSize = item.getAttribute('data-name') === 'category' ? '100%' : '50%'
							}
						})
					})
				})
			})
		}
	}
}
</script>

<style scoped>

</style>
