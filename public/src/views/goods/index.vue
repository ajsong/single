<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x" v-if="groupbuy">特价拼团</div>
		<div class="titleView-x" v-else-if="data && data.category && data.category.name">{{ data.category.name }}</div>
		<div class="titleView-x" v-else-if="ext_property && Number(ext_property) === 4">新品发售</div>
		<div class="titleView-x" v-else-if="integral">积分商城</div>
		<div class="titleView-x" v-else>商品列表</div>
		<router-link v-if="integral" class="right" :to="{path:'/article/detail', query:{id:7}}"><span>积分规则</span></router-link>
	</div>

	<div class="goods-index">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
			<header :class="{'brandHeader':brand_id>0}" v-if="integral!==1 && groupbuy!==1">
				<div class="brand ge-bottom ge-light" v-if="data && data.brand">
					<div ref="brandbanner" class="banner" :url="data.brand.banner"></div>
					<div class="title"><div ref="brandpic" class="pic" :url="data.brand.pic"></div><span>{{ data.brand.name }}</span></div>
				</div>

				<switch-view class="ge-bottom ge-light" :data="switchData"></switch-view>
			</header>

			<ul class="list goods-item" v-if="data && data.goods">
				<li v-for="g in data.goods">
					<router-link :to="{path:'/goods/detail', query:{id:g.id, integral:integral}}">
						<div ref="pic" class="pic" :url="g.pic"></div>
						<div class="title">
							<div>{{ g.name }}</div>
							<template v-if="integral===1">
							<font class="btn"><b>立即兑换</b></font>
							<span class="integral">{{ g.integral }}积分</span>
							</template>
							<template v-else>
								<font><template v-if="integral!==1 && groupbuy!==1 && g.purchase_price>0">正在秒杀中</template></font>
							<span><strong>￥{{ g.price|round }}</strong><s>￥{{ g.market_price|round }}</s></span>
							</template>
						</div>
					</router-link>
				</li>
			</ul>
		</pull-refresh>

		<div ref="filter" class="filterView hidden" v-if="data">
			<div class="navBar">
				<div class="titleView-x">筛选</div>
			</div>
			<form ref="form">
			<ul class="tableView tableView-noMargin tableView-light">
				<li v-if="$.inArray('category', func)">
					<h1>
						<div class="label">类别</div>
						<div>
							<template v-if="data.categories">
							<span v-for="g in data.categories"><input type="radio" name="category_id" :id="'category'+g.id" :value="g.id" v-model="category_id" /><label :for="'category'+g.id"><div>{{ g.name|replace("'","\'") }}</div></label></span>
							</template>
							<template v-else>
							<span><input type="radio" name="category_id" :id="'category'+category_id" :value="category_id" v-model="category_id" /><label :for="'category'+category_id"><div>{{ title|replace("'","\'") }}</div></label></span>
							</template>
							<div class="clear"></div>
						</div>
					</h1>
				</li>
				<li>
					<h1>
						<div class="label">价格</div>
						<div>
							<font>价格区间(元)</font><font><input type="tel" v-model="min_price" placeholder="最低价" /> - <input type="tel" v-model="max_price" placeholder="最高价" /></font>
							<div class="clear"></div>
						</div>
					</h1>
				</li>
				<li v-if="data.brand">
					<h1>
						<div class="label">品牌</div>
						<div>
							<span v-for="g in data.brands"><input type="radio" name="brand_id" :id="'brand'+g.id" :value="g.id" v-model="brand_id" /><label :for="'brand'+g.id"><div>{{ g.name|replace("'","\'") }}</div></label></span>
							<div class="clear"></div>
						</div>
					</h1>
				</li>
			</ul>
			</form>
			<div class="bottomView ge-top ge-light">
				<a class="btn" href="javascript:void(0)" @click="search">确定</a><a class="reset gr" href="javascript:void(0)" @click="reset">重置</a>
			</div>
		</div>
	</div>
</div>
</template>

<script>
import pullRefresh from '../../components/pullRefresh'
import switchView from '../../components/switchView'
export default {
	name:'goodsIndex',
	data(){
		return {
			data: null,
			func: [],
			title: '',
			params: {},
			category_id: 0,
			brand_id: 0,
			shop_id: 0,
			integral: 0,
			groupbuy: 0,
			order_field: '',
			order_sort: '',
			ext_property: '',
			min_price: '',
			max_price: '',

			switchData: [
				{title:'综合', selected:this.switchSelected},
				{title:'销量', selected:this.switchSelected},
				{title:'价格', selected:this.switchSelected},
				{title:'筛选', className:'filter', selected:this.switchSelected}
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
	components:{
		pullRefresh,
		switchView
	},
	created(){
		this.func = $.storageJSON('func')
		if (this.$route.query.title) this.title = this.$route.query.title
		if (this.$route.query.category_id) this.category_id = Number(this.$route.query.category_id)
		if (this.$route.query.brand_id) this.brand_id = Number(this.$route.query.brand_id)
		if (this.$route.query.shop_id) this.shop_id = Number(this.$route.query.shop_id)
		if (this.$route.query.integral) this.integral = Number(this.$route.query.integral)
		if (this.$route.query.groupbuy) this.groupbuy = Number(this.$route.query.groupbuy)
		if (this.$route.query.order_field) this.order_field = this.$route.query.order_field
		if (this.$route.query.order_sort) this.order_sort = this.$route.query.order_sort
		if (this.$route.query.ext_property) this.ext_property = this.$route.query.ext_property
		if (this.$route.query.min_price) this.min_price = Number(this.$route.query.min_price)
		if (this.$route.query.max_price) this.max_price = Number(this.$route.query.max_price)
		this.params = {
			integral: this.integral,
			category_id: this.category_id,
			ext_property: this.ext_property
		}
	},
	filters:{
		replace(str, search, replacement){
			return str.replace(search, replacement)
		}
	},
	methods: {
		switchSelected(index){
			if (index < this.switchData.length - 1) {
				switch (index) {
					case 0:
						this.params = {
							category_id: this.category_id,
							ext_property: this.ext_property
						}
						break
					case 1:
						this.params = {
							category_id: this.category_id,
							ext_property: this.ext_property,
							order_field: 'sales',
							order_sort: this.order_sort === 'asc' ? 'desc' : 'asc'
						}
						break
					case 2:
						this.params = {
							category_id: this.category_id,
							ext_property: this.ext_property,
							order_field: 'price',
							order_sort: this.order_sort === 'asc' ? 'desc' : 'asc'
						}
						break
				}
				this.scroller.refreshBegin()
			} else {
				$(this.$refs.filter).presentView(1)
				return false
			}
		},
		reset(){
			this.$refs.form.reset()
		},
		search(){
			$(this.$refs.filter).presentView(false)
			this.params = {
				integral: this.integral,
				category: this.category_id,
				brand_id: this.brand_id,
				min_price: this.min_price,
				max_price: this.max_price,
				ext_property: this.ext_property,
				order_field: '',
				order_sort: ''
			}
			this.scroller.refreshBegin()
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			let params = $.extend(this.params, {
				offset: (page.num - 1) * page.size,
				pagesize: page.size
			})
			this.$ajax.get('/api/goods', params).then(json => {
				if (!$.checkError(json, this)) return scroller.end()
				let isInit = false
				if (!this.data) {
					isInit = true
					this.data = json.data
				} else {
					if (!(json.data.goods instanceof Array)) {
						this.data.goods = null
						return scroller.end()
					}
					let data = page.num === 1 ? [] : this.data.goods
					data.push(...json.data.goods)
					this.data.goods = data
				}
				this.$nextTick(() => {
					scroller.end()
					if (isInit && json.data.brand) {
						$(this.$refs.brandbanner).loadbackground('url', '50%', '../images/nopic.png')
						$(this.$refs.brandpic).loadbackground('url', '50%', '../images/nopic.png')
					}
					$(this.$refs.pic).loadbackground('url', '50%', '../images/nopic.png')
				})
			})
		}
	}
}
</script>

<style scoped>

</style>