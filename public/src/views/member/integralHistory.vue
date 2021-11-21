<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">积分明细</div>
	</div>

	<div class="commission-history">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
			<ul class="list" v-if="data">
				<li class="ge-bottom ge-light" v-for="g in data">
					<div :class="{'r':String(g.integral).indexOf('-')>-1}">{{ g.integral }}</div>
					<span>{{ g.memo }}</span>
					<font class="scale10-left">{{ g.add_time }}</font>
				</li>
			</ul>
			<div class="norecord" v-else>暂无明细</div>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import PullRefresh from '../../components/pullRefresh';
export default {
	name:'integralHistory',
	components:{
		PullRefresh
	},
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
				size: 12
			}
		}
	},
	methods:{
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/member/integral_history', {offset:(page.num - 1) * page.size, pagesize:page.size}).then(json => {
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