<template>
<div>
	<div class="navBar">
		<div class="titleView-x">发现</div>
		<router-link class="right" to="/article/edit"><i class="article-edit"></i></router-link>
	</div>

	<div class="article-index main-padding-bottom">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44+0.49)">
			<slide-view v-if="data && $.isArray(data.flashes) && data.flashes.length" :list="data.flashes" :isAds="true" :height="slideHeight"></slide-view>

			<ul class="list" v-if="data && $.isArray(data.articles) && data.articles.length">
				<li v-for="g in data.articles">
					<router-link :to="{path:'/article/detail', query:{id:g.id}}">
						<div class="title"><div>{{ g.add_time }}</div>{{ g.title }}</div>
						<div class="content">{{ g.content }}</div>
						<ul class="ge-bottom ge-light" v-if="$.isArray(g.pics)">
							<li ref="pic" v-for="e in g.pics" :url="e.pic"></li>
							<div class="clear"></div>
						</ul>
						<div class="bottom">
							<i></i><span>{{ g.likes }}</span>
							<i class="comments"></i><span>{{ g.comments }}</span>
						</div>
					</router-link>
				</li>
			</ul>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import pullRefresh from '../../components/pullRefresh';
import slideView from '../../components/slideView';
export default {
	name:'articles',
	data() {
		return {
			data: null,
			slideHeight: 0,

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
		pullRefresh,
		slideView
	},
	mounted(){
		window.onresize = () => {
			this.resize()
		}
	},
	methods:{
		resize(){
			if ($('.slideView').length) this.slideHeight = $.autoHeight(320, 137, $('.slideView'))
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/article', {offset:(page.num - 1) * page.size, pagesize:page.size}).then(json => {
				if (!$.checkError(json, this)) return scroller.end()
				if (!this.data) {
					this.data = json.data
					this.$nextTick(() => this.resize())
				} else {
					if (!(json.data.articles instanceof Array)) {
						this.data.articles = null
						return scroller.end()
					}
					let data = page.num === 1 ? [] : this.data.articles
					data.push(...json.data.articles)
					this.data.articles = data
				}
				this.$nextTick(() => {
					scroller.end()
					$(this.$refs.pic).loadbackground('url', '100%', '../images/nopic.png')
				})
			})
		}
	}
}
</script>

<style>

</style>