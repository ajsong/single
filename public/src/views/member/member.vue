<template>
<div>
	<div ref="navBar" class="navBar navBar-hidden">
		<!--<a class="left" href="/wap/?app=cart&act=index"><i class="member-cart badge"><sub></sub></i></a>-->
		<router-link class="left sign" to="/member/sign"><i class="member-sign"></i></router-link>
		<div class="titleView-x">会员</div>
		<router-link class="right" to="/member/set"><i class="member-set"></i></router-link>
	</div>

	<div class="member-index main-bottom width-wrap">
		<div class="topView">
			<div class="infoView">
				<div ref="avatar" class="avatar" v-if="member.id>0 && member.avatar.length" :url="member.avatar"></div>
				<div class="avatar" v-else></div>
				<span v-if="member.id>0"><template v-if="member.nick_name.length">{{member.nick_name}}</template><template v-else>{{member.name}}</template></span>
				<div class="btnView" v-else>
					<router-link to="/login">登录</router-link>
					<router-link to="/register">注册</router-link>
				</div>
			</div>
			<div class="moneyView">
				<router-link to="/member/money"><div><i></i><span>我的余额<small><template v-if="member.id>0">{{member.money}}</template><template v-else>0.00</template></small></span></div></router-link>
				<router-link v-if="edition>2" class="ge-left" to="/member/commission"><div><i class="commission"></i><span>我的佣金<small><template v-if="member.id>0">{{member.commission}}</template><template v-else>0.00</template></small></span></div></router-link>
			</div>
		</div>

		<section>
			<ul class="tableView tableView-light">
				<li><router-link to="/order"><h1><big>全部订单</big><em class="ico1"></em>我的订单</h1></router-link></li>
				<li class="orderList">
					<router-link class="ico1 badge" to="/order?status=0"><div><sub v-html="data.not_pay>0?data.not_pay:''"></sub></div></router-link>
					<router-link class="ico2 badge" to="/order?status=1"><div><sub v-html="data.not_shipping>0?data.not_shipping:''"></sub></div></router-link>
					<router-link class="ico3 badge" to="/order?status=2"><div><sub v-html="data.not_confirm>0?data.not_confirm:''"></sub></div></router-link>
					<router-link class="ico4 badge" to="/order?status=3"><div><sub v-html="data.not_comment>0?data.not_comment:''"></sub></div></router-link>
				</li>
			</ul>

			<ul class="tableView tableView-light ge-top ge-light" v-if="inArray('shop',func)">
				<li><router-link to="/member/business"><h1><em class="ico12"></em>我是商家</h1></router-link></li>
			</ul>

			<ul class="tableView tableView-light ge-top ge-light" v-if="edition>2">
				<li v-if="inArray('groupbuy',func)"><router-link to="/groupbuy"><h1><em class="ico13"></em>我的拼团</h1></router-link></li>
				<li v-if="inArray('chop',func)"><router-link to="/chop"><h1><em class="ico14"></em>我发起的砍价</h1></router-link></li>
				<template v-if="inArray('integral',func)">
				<li><router-link to="/member/integral"><h1><em class="ico3"></em>我的积分</h1></router-link></li>
				<li><router-link to="/goods?integral=1"><h1><em class="ico5"></em>积分商城</h1></router-link></li>
				<li><router-link to="/order?integral_order=1"><h1><em class="ico2"></em>积分商城订单</h1></router-link></li>
				</template>
			</ul>

			<ul class="tableView tableView-light ge-top ge-light">
				<li v-if="inArray('coupon',func)"><router-link to="/member/coupon?status=1"><h1><big v-if="data.coupon_count>0">{{ data.coupon_count }}张</big><em class="ico4"></em>我的优惠券</h1></router-link></li>
				<li v-if="edition>2"><router-link to="/member/code"><h1><em class="ico7"></em>分享赚佣金</h1></router-link></li>
				<li><router-link to="/member/message"><h1><em class="ico8"></em>我的消息</h1></router-link></li>
				<li><router-link to="/member/favorite?type_id=1"><h1><em class="ico9"></em>我的收藏</h1></router-link></li>
				<li><router-link to="/member/goods_history"><h1><em class="ico10"></em>足迹</h1></router-link></li>
			</ul>

			<ul class="tableView tableView-light ge-top ge-light">
				<li><router-link to="/member/set"><h1><em class="ico11"></em>设置</h1></router-link></li>
			</ul>
		</section>
	</div>
</div>
</template>

<script>
export default {
	name:'member',
	data() {
		return {
			data: {
				coupon_count: 0,
				not_pay: 0,
				not_shipping: 0,
				not_confirm: 0,
				not_comment: 0
			},
			member: {
				id: 0
			},
			func: [],
			edition: 0
		}
	},
	created(){
		this.func = $.storageJSON('func')
		this.edition = Number($.storage('edition'))
		this.member = $.storageJSON('member')
		this.$ajax.get('/api/member').then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.member = json.data.member
			$.storage('member', this.member, 365)
			this.$nextTick(() => {
				$(this.$refs.avatar).loadbackground('url', '100%', '../images/avatar.png')
			})
		})
	},
	mounted(){
		window.addEventListener('scroll', this.scrollFunc)
	},
	beforeDestroy(){
		window.removeEventListener('scroll', this.scrollFunc)
	},
	methods: {
		inArray(search, array){
			for (let i in array) {
				if(array[i] === search)return true
			}
			return false
		},
		scrollFunc(){
			let scrollTop = document.documentElement.scrollTop || document.body.scrollTop
			let navBar = this.$refs.navBar
			//let topView = this.$refs.topView
			let className = navBar.className.replace(/\s*navBar-hidden/ig, '')
			//topView.clientHeight - navBar.clientHeight
			navBar.className = className + (scrollTop < navBar.clientHeight ? ' navBar-hidden' : '')
		}
	}
}
</script>

<style>

</style>