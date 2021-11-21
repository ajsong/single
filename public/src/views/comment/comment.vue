<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">商品评价</div>
	</div>

	<div class="comment-index">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
			<ul class="list" v-if="data">
				<li class="ge-bottom" v-for="g in data">
					<div class="info">
						<div class="time">{{ g.comment_time }}</div>
						<div class="avatar" :style="{'background-image':(g.member_avatar ? 'url('+g.member_avatar+')' : '')}"></div>
						<div class="name">{{ g.member_name }}</div>
						<div class="star"><i v-for="e in Number(g.comment_stars)"></i></div>
					</div>
					<div class="content">{{ g.comment_content }}</div>
				</li>
			</ul>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import PullRefresh from '../../components/pullRefresh';
export default {
	name:'comment',
	data(){
		return {
			id: 0,
			data: null,

			scroller: null,
			refresh: {
				callback: this.getData,
				//autoLoad: true
			},
			loadmore: {
				callback: this.getData,
				size: 8
			}
		}
	},
	components:{
		PullRefresh
	},
	created(){
		let id = this.$route.query.goods_id
		if (!id) {
			alert('missing id')
			this.$router.go(-1)
			return
		}
		this.id = id
	},
	mounted(){
		this.$nextTick(() => {
			if (this.$route.query.pagesize) this.scroller.setPageSize(this.$route.query.pagesize)
			this.scroller.refreshBegin()
		})
	},
	methods:{
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/comment', {goods_id:this.id, offset:(page.num - 1) * page.size, pagesize:page.size}).then(json => {
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
				})
			})
		}
	}
}
</script>

<style scoped>

</style>