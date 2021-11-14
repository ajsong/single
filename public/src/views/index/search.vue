<template>
<div>
	<div class="navBar">
		<router-link class="left" to="/"><i class="return"></i></router-link>
		<div class="titleView-x search-input"><input type="search" id="keyword" v-model="keyword" placeholder="请输入商品关键字" @keydown.enter="searchPro" /></div>
		<a class="right" href="javascript:void(0)" @click="searchPro"><span>搜索</span></a>
	</div>

	<div class="search-list height-wrap">
		<div class="searchHistory" v-if="!isSearch">
			<div class="title"><a href="javascript:void(0)" v-if="!history"></a>搜索历史</div>
			<ul class="list">
				<li class="norecord" v-if="!history">暂无搜索历史</li>
				<li v-else v-for="g in history"><a href="javascript:void(0)" @click="setKeyword(g)">{{ g }}</a></li>
			</ul>
			<template v-if="hot">
			<div class="title">热门搜索</div>
			<ul class="list">
				<li v-for="g in hot"><a href="javascript:void(0)" @click="setKeyword(g)">{{ g }}</a></li>
			</ul>
			</template>
		</div>
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)" v-else>
			<ul class="list goods-item" v-if="data.goods">
				<li v-for="g in data.goods">
					<router-link :to="{path:'/goods/detail', query:{id:g.id}}">
						<div ref="pic" class="pic" :url="g.pic"></div>
						<div class="title"><div>{{ g.name }}</div><font></font><span><strong>￥</strong>{{ g.price|round }}</span></div>
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
	name:'search',
	data(){
		return {
			keyword: '',
			data: {
				goods: null
			},

			isSearch: false,
			history: null,
			hot: null,

			scroller: null,
			refresh: {
				callback: this.getData,
				//autoLoad: true
			},
			loadmore: {
				callback: this.getData,
				pagesize: 12
			}
		}
	},
	watch:{
		isSearch(){
			if (this.isSearch) {
				this.$(document.body).addClass('gr')
			} else {
				this.$(document.body).removeClass('gr')
			}
		}
	},
	components:{
		pullRefresh
	},
	created(){
		this.$(document.body).removeClass('gr')
		if (this.$route.params.keyword) {
			this.keyword = this.$route.params.keyword
			this.$ajax.get('/api/goods', {keyword:this.keyword}).then(json => {
				if (!this.$.checkError(json, this)) return
				this.data = json.data
				this.isSearch = true
				this.$nextTick(() => {
					this.scroller.setPageNum(1)
					setTimeout(() => {
						this.scroller.end()
						this.$(this.$refs.pic).loadbackground('url', '50%', '../images/nopic.png')
					}, 100)
				})
			})
		} else {
			this.getHistory()
		}
	},
	beforeDestroy(){
		this.$(document.body).addClass('gr')
	},
	methods:{
		searchPro(){
			if (this.keyword.length) {
				this.$router.push('/search/' + this.keyword)
				this.isSearch = true
				setTimeout(() => this.scroller.refreshBegin(), 100)
			} else {
				this.getHistory()
			}
		},
		getHistory(){
			this.$ajax.get('/api/home/search_history').then(json => {
				if (!this.$.checkError(json, this)) return
				this.isSearch = false
				this.$router.push('/search')
				this.history = json.data.history
				this.hot = json.data.hot
			})
		},
		setKeyword(content){
			this.keyword = content
			this.$router.push('/search/' + this.keyword)
			this.searchPro()
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/goods', {offset:(page.num - 1) * page.size, pagesize:page.size, keyword:this.keyword}).then(json => {
				if(!this.$.checkError(json, this))return
				if(!this.data){
					this.data = json.data
				}
				if (!(json.data.goods instanceof Array)) return scroller.end()
				let data = page.num === 1 ? [] : this.data.goods
				data.push(...json.data.goods)
				this.data.goods = data
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