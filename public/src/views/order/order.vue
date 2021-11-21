<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x" v-html="integral_order===0 ? '我的订单' : '积分商城订单'"></div>
	</div>

	<div class="order-index">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
			<header class="ge-bottom ge-light" v-if="integral_order===0">
				<switch-view :data="switchData" :index="switchIndex"></switch-view>
			</header>

			<ul class="list tableView tableView-noLine" v-if="data">
				<li v-for="g in data">
					<div class="view">
						<router-link :to="{path:'/order/detail', query:{id:g.id, for_pay:1, integral_order:integral_order===1?1:''}}">
							<div :class="['status', 'ge-bottom', 'ge-light', 'status'+g.status]"><font>订单号：{{ g.order_sn }}</font><div>{{ g.status_name }}</div><span v-if="g.status>0 && g.ask_refund_time>0">(退货退款中)</span></div>
							<ul>
								<li v-for="p in g.goods">
									<div class="gview">
										<div ref="pic" class="pic" :url="p.goods_pic"></div>
										<div class="name">{{ p.goods_name }}</div>
										<template v-if="integral_order===0">
										<div class="spec" v-html="p.spec ? p.spec : ''"></div>
										<div class="price"><span>×{{ p.quantity }}</span><div>￥{{ p.price|round }}</div></div>
										</template>
										<div class="price" v-else><div v-html="parseInt(p.integral)+'积分'"></div></div>
									</div>
								</li>
							</ul>
						</router-link>
						<div class="total" v-if="integral_order===0">
							<router-link v-if="Number(g.status)===0" class="pay" :to="{path:'/order/detail', query:{id:g.id, for_pay:1}}"><span>立即支付</span></router-link>
							<a v-if="Number(g.status)===1 && Number(g.ask_refund_time)===0" class="refund" href="javascript:void(0)" @click="refund(g.id)"><span>我要退款</span></a>
							<a v-if="Number(g.status)===2" class="ok" href="javascript:void(0)" @click="receive(g.id)"><span>确认收货</span></a>
							<div>总计：<strong>￥{{ g.total_price|round }}</strong> (<template v-if="g.shipping_price>0">含运费￥{{ g.shipping_price|round }}</template><template v-else>免运费</template>)</div>
						</div>
					</div>
				</li>
			</ul>
			<div class="norecord" v-else>当前没有任何订单</div>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import PullRefresh from '../../components/pullRefresh';
import switchView from '../../components/switchView';
export default {
	name:'order',
	components:{
		PullRefresh,
		switchView
	},
	data(){
		return {
			integral_order: 0,
			status: '',
			data: null,

			switchIndex: '',
			switchData: [
				{title:'全部', selected:this.switchSelected},
				{title:'待付款', selected:this.switchSelected},
				{title:'待发货', selected:this.switchSelected},
				{title:'待收货', selected:this.switchSelected},
				{title:'待评价', selected:this.switchSelected},
				{title:'完成', selected:this.switchSelected},
				{title:'取消', selected:this.switchSelected},
				{title:'退货/退款', selected:this.switchSelected}
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
		if (this.$route.query.integral_order) this.integral_order = Number(this.$route.query.integral_order)
		if (this.$route.query.status) this.status = this.$route.query.status
		switch (this.status) {
			case '0':this.switchIndex = 1;break
			case '1':this.switchIndex = 2;break
			case '2':this.switchIndex = 3;break
			case '3':this.switchIndex = 4;break
			case '4':this.switchIndex = 5;break
			case '-1':this.switchIndex = 6;break
			case '-2,-3':this.switchIndex = 7;break
			default:this.switchIndex = 0;break
		}
	},
	methods:{
		switchSelected(index){
			switch (index) {
				case 0:this.status = '';break
				case 1:this.status = '0';break
				case 2:this.status = '1';break
				case 3:this.status = '2';break
				case 4:this.status = '3';break
				case 5:this.status = '4';break
				case 6:this.status = '-1';break
				case 7:this.status = '-2,-3';break
			}
			this.scroller.refreshBegin()
		},
		refund(id){
			this.$router.push({path:'/order/refund', query:{id:id}})
		},
		receive(id){
			if (!confirm('请收到货后，再确认收货！\n否则您可能钱货两空')) return
			this.$ajax.post('/api/order/shouhuo', { id:id }).then(json => {
				if (!$.checkError(json, this)) return
				this.scroller.refreshBegin()
			})
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/order', {offset:(page.num - 1) * page.size, pagesize:page.size, status:this.status, integral_order:this.integral_order}).then(json => {
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