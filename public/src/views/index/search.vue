<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x search-input"><input type="search" v-model="keyword" placeholder="请输入商品关键字" @keydown.enter="searchPro" /></div>
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
					<a href="javascript:void(0)" :data-id="g.id">
						<div ref="pic" class="pic" :url="g.pic"></div>
						<div class="title"><div>{{ g.name }}</div><font></font><span><strong>￥</strong>{{ g.price|round }}</span></div>
					</a>
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
			from: '',
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
				size: 12
			}
		}
	},
	watch:{
		isSearch(){
			if (this.isSearch) {
				$(document.body).addClass('gr')
			} else {
				$(document.body).removeClass('gr')
			}
		}
	},
	components:{
		pullRefresh
	},
	created(){
		$(document.body).removeClass('gr')
		if (this.$route.query.from) this.from = this.$route.query.from
		if (this.$route.query.keyword) {
			this.keyword = this.$route.query.keyword
			this.$ajax.get('/api/goods', {keyword:this.keyword}).then(json => {
				if (!$.checkError(json, this)) return
				this.data = json.data
				this.isSearch = true
				this.$nextTick(() => {
					this.scroller.setPageNum(1)
					setTimeout(() => {
						this.scroller.end()
						$(this.$refs.pic).loadbackground('url', '50%', '../images/nopic.png')
						this.setLink()
					}, 100)
				})
			})
		} else {
			this.getHistory()
		}
	},
	beforeDestroy(){
		$(document.body).addClass('gr')
	},
	methods:{
		setLink(){
			let vm = this
			$('.goods-item a').not('[setlink]').attr('setlink', 'complete').on('click', function() {
				if (vm.from === 'article') {
					let index = $(this).parent().index()
					$.paramAlive(this.$route.path, vm.data.goods[index])
					vm.$router.go(-1)
					return
				}
				vm.$router.push({path:'/goods/detail', query:{id:$(this).attr('data-id')}})
			})
		},
		searchPro(){
			if (this.keyword.length) {
				this.isSearch = true
				setTimeout(() => this.scroller.refreshBegin(), 100)
			} else {
				this.getHistory()
			}
		},
		getHistory(){
			this.$ajax.get('/api/home/search_history').then(json => {
				if (!$.checkError(json, this)) return
				this.isSearch = false
				this.history = json.data.history
				this.hot = json.data.hot
			})
		},
		setKeyword(content){
			this.keyword = content
			this.searchPro()
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/goods', {offset:(page.num - 1) * page.size, pagesize:page.size, keyword:this.keyword}).then(json => {
				if (!$.checkError(json, this)) return scroller.end()
				if(!this.data){
					this.data = json.data
				}
				if (!(json.data.goods instanceof Array)) {
					this.data.goods = null
					return scroller.end()
				}
				let data = page.num === 1 ? [] : this.data.goods
				data.push(...json.data.goods)
				this.data.goods = data
				this.$nextTick(() => {
					scroller.end()
					$(this.$refs.pic).loadbackground('url', '50%', '../images/nopic.png')
					this.setLink()
				})
			})
		}
	}
}
</script>

<style scoped>

</style>