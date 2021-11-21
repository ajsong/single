<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">我发起的砍价</div>
	</div>

	<div class="order-index chop-index">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
			<header class="ge-bottom ge-light">
				<switch-view :data="switchData" :index="switchIndex"></switch-view>
			</header>

			<ul class="list tableView tableView-noLine" v-if="data">
				<li v-for="g in data">
					<div class="view">
						<router-link :to="{path:'/chop/detail', query:{order_sn:g.order_sn}}">
							<div :class="['status', 'ge-bottom', 'ge-light', 'status'+g.status]"><div>{{ g.status_name }}</div></div>
							<ul>
								<li v-for="p in g.goods">
									<div class="gview">
										<div ref="pic" class="pic" :url="p.goods_pic"></div>
										<div class="name">{{ p.goods_name }}</div>
										<div class="price"><div>￥{{ p.price|round }}</div></div>
									</div>
								</li>
							</ul>
						</router-link>
						<div class="total">
							<router-link v-if="Number(g.status)===0" class="pay" :to="{path:'/chop/detail', query:{order_sn:g.order_sn}}"><span>继续砍价</span></router-link>
							<router-link v-if="Number(g.status)===1" class="pay" :to="{path:'/chop/detail', query:{order_sn:g.order_sn}}"><span>立即购买</span></router-link>
							<router-link v-if="Number(g.status)===-1" class="pay" :to="{path:'/chop/detail', query:{order_sn:g.order_sn}}"><span>重砍一个</span></router-link>
							<router-link class="chop" :to="{path:'/chop/detail', query:{order_sn:g.order_sn}}"><span>砍价详情</span></router-link>
							<div v-if="Number(g.status)!==1">离底价还差 <strong>{{ g.remain_price|round }}</strong> 元</div>
						</div>
					</div>
				</li>
			</ul>
			<div class="norecord" v-else>当前没有任何砍价</div>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import PullRefresh from '../../components/pullRefresh';
import switchView from '../../components/switchView';
export default {
	name: 'chop',
	components:{
		PullRefresh,
		switchView
	},
	data() {
		return {
			status: '',
			data: null,

			switchIndex: 0,
			switchData: [
				{title:'全部', selected:this.switchSelected},
				{title:'砍价中', selected:this.switchSelected},
				{title:'砍价成功', selected:this.switchSelected},
				{title:'砍价失败', selected:this.switchSelected}
			],

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
	created(){
		if (this.$route.query.status) this.status = this.$route.query.status
	},
	methods: {
		switchSelected(index){
			switch (index) {
				case 0:this.status = '';break
				case 1:this.status = '0';break
				case 2:this.status = '1';break
				case 3:this.status = '-1';break
			}
			this.scroller.refreshBegin()
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/chop', {offset:(page.num - 1) * page.size, pagesize:page.size, status:this.status}).then(json => {
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
					$(this.$refs.pic).loadbackground('url', '80%', '../images/nopic.png')
					$('.tableView .total strong').priceFont('bigPrice')
					$('.gview .price div').priceFont('bigPrice')
				})
			})
		}
	}
}
</script>

<style scoped>

</style>