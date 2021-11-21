<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">优惠券</div>
		<!--<a class="right" href="javascript:void(0)"><span>添加</span></a>-->
	</div>

	<div class="coupon-index">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
			<header class="ge-bottom ge-light">
				<switch-view :data="switchData" :index="status !== -1 ? (status === 1 ? 0 : 1) : -1"></switch-view>
			</header>

			<ul class="list" v-if="data">
				<li :class="['type'+g.type, 'status'+g.status]" v-for="g in data">
					<a href="javascript:void(0)" @click="toUse(g.status)">
						<div class="l">
							<div><font>￥</font>{{ g.coupon_money }}</div>
							<span><tt>{{ g.min_price_memo }}</tt></span>
						</div>
						<div class="r">
							<strong>{{ g.name }}</strong>
							<font><tt>{{ g.memo }}</tt></font>
							<span><div v-if="Number(g.status)===1"><b>立即使用</b></div><span>{{ g.time_memo }}</span></span>
						</div>
						<div class="t t0" v-if="Number(g.type) === 0"></div>
						<div class="t" v-else-if="Number(g.type) === 1"><span>品牌</span></div>
						<div class="t" v-else-if="Number(g.type) === 2"><span>新人</span></div>
						<div :class="['p', 'p'+g.status]" v-if="Number(g.status) < 1"></div>
					</a>
				</li>
			</ul>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import PullRefresh from '../../components/pullRefresh';
import switchView from '../../components/switchView';
export default {
	name:'coupon',
	components:{
		PullRefresh,
		switchView
	},
	data(){
		return {
			status: -1,
			data: null,

			switchData: [
				{title:'有效', selectedClassName:'this', selected:this.changeStatus},
				{title:'无效', selectedClassName:'this', selected:this.changeStatus}
			],

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
	created(){
		if (this.$route.query.status) this.status = Number(this.$route.query.status)
	},
	methods:{
		changeStatus(index){
			this.status = index === 0 ? 1 : 0
			this.scroller.refreshBegin()
		},
		toUse(status){
			if (Number(status) === 1) this.$router.push('/category')
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/coupon', {offset:(page.num - 1) * page.size, pagesize:page.size, status:(this.status === -1 ? '' : this.status)}).then(json => {
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