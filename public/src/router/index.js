import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import home from '../views/index/index'
import category from '../views/category/category'
import article from '../views/article/article'
import cart from '../views/cart/cart'
import member from '../views/member/member'
import login from '../views/passport/login'
import register from '../views/passport/register'
import Error from '../components/error'

const router = new VueRouter({
	mode: 'history',//使用 history 模式时，URL 就像正常的 url,
	routes: [
		{
			path: '/',
			index: 0,
			component: home,
			meta: {
				level: 0,
				//keepAlive: true,
				scrollTop: 0
			}
			//component: resolve => require(['../views/index/index'], resolve)//懒加载
		},
		{
			path: '/search',
			component: resolve => require(['../views/index/search'], resolve),
			meta: {
				level: 1
			}
		},
		{
			path: '/category/:id(\\d*)?',
			//正则表达式(\\d+)指定id只能为数字形式，参数值会被设置到this.$route.params中
			//index/search?name=keyword，this.$route.query.name
			name: 'category',
			//name作用，this.$router.push({ name:'category', params:{ id:123 }}) params 只能通过 name 传递，而且页面刷新会立刻丢失 params
			//router-link传参，<router-link :to="{ name:'category', params:{ id:123 }}">Category</router-link>
			alias: '/cate',
			//别名，<router-link to="/cate">Category</router-link>
			//alias在path为'/'中不起作用
			index: 1,
			component: category,
			meta: {
				level: 0
			}
		},
		{
			path: '/article',
			index: 2,
			component: article,
			meta: {
				level: 0,
				//keepAlive: true,
				scrollTop: 0
			}
		},
		{
			path: '/article/detail',
			component: resolve => require(['../views/article/detail'], resolve),
			meta: {
				level: 3
			}
		},
		{
			path: '/article/edit',
			component: resolve => require(['../views/article/edit'], resolve),
			meta: {
				level: 2,
				paramAlive: true
			}
		},
		{
			path: '/cart',
			index: 3,
			component: cart,
			meta: {
				level: 0,
				//keepAlive: true,
				scrollTop: 0,
				requireAuth: true
			}
		},
		{
			path: '/cart/commit',
			name: 'cartCommit',
			component: resolve => require(['../views/cart/commit'], resolve),
			meta: {
				level: 3,
				paramAlive: true,
				requireAuth: true
			}
		},
		{
			path: '/cart/order_pay',
			name: 'orderpay',
			component: resolve => require(['../views/cart/orderpay'], resolve),
			meta: {
				level: 4,
				requireAuth: true
			}
		},
		{
			path: '/cart/order_complete',
			name: 'orderpay',
			component: resolve => require(['../views/cart/complete'], resolve),
			meta: {
				level: 5,
				requireAuth: true
			}
		},
		{
			path: '/member',
			index: 4,
			component: member,
			meta: {
				level: 0,
				//keepAlive: true,
				scrollTop: 0,
				requireAuth: true
			}
		},
		{
			path: '/member/sign',
			component: resolve => require(['../views/member/sign'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/member/set',
			component: resolve => require(['../views/member/set'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/member/password',
			component: resolve => require(['../views/member/password'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/member/edit',
			component: resolve => require(['../views/member/edit'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/member/code',
			component: resolve => require(['../views/member/code'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/member/message',
			component: resolve => require(['../views/member/message'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/member/favorite',
			component: resolve => require(['../views/member/favorite'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/member/goods_history',
			component: resolve => require(['../views/member/goodshistory'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/member/coupon',
			component: resolve => require(['../views/member/coupon'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/chop',
			component: resolve => require(['../views/chop/chop'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/chop/detail',
			component: resolve => require(['../views/chop/detail'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/groupbuy',
			component: resolve => require(['../views/groupbuy/groupbuy'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/groupbuy/detail',
			component: resolve => require(['../views/groupbuy/detail'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/member/business',
			component: resolve => require(['../views/member/business'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/member/integral',
			component: resolve => require(['../views/member/integral'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/member/integral_history',
			component: resolve => require(['../views/member/integralHistory'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/member/money',
			component: resolve => require(['../views/member/money'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/member/money_history',
			component: resolve => require(['../views/member/moneyHistory'], resolve),
			meta: {
				level: 3,
				requireAuth: true
			}
		},
		{
			path: '/member/commission',
			component: resolve => require(['../views/member/commission'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/member/commission_history',
			component: resolve => require(['../views/member/commissionHistory'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/member/my_invite_user',
			component: resolve => require(['../views/member/myInviteUser'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/member/withdraw',
			component: resolve => require(['../views/member/withdraw'], resolve),
			meta: {
				level: 2,
				paramAlive: true,
				requireAuth: true
			}
		},
		{
			path: '/member/withdraw_history',
			component: resolve => require(['../views/member/withdrawHistory'], resolve),
			meta: {
				level: 3,
				requireAuth: true
			}
		},
		{
			path: '/bank',
			component: resolve => require(['../views/bank/bank'], resolve),
			meta: {
				level: 4,
				requireAuth: true
			}
		},
		{
			path: '/bank/edit',
			component: resolve => require(['../views/bank/edit'], resolve),
			meta: {
				level: 5,
				requireAuth: true
			}
		},
		{
			path: '/bank/set',
			component: resolve => require(['../views/bank/set'], resolve),
			meta: {
				level: 5,
				requireAuth: true
			}
		},
		{
			path: '/bank/password_set',
			component: resolve => require(['../views/bank/passwordSet'], resolve),
			meta: {
				level: 7,
				requireAuth: true
			}
		},
		{
			path: '/bank/repassword',
			name: 'bankRepassword',
			component: resolve => require(['../views/bank/repassword'], resolve),
			meta: {
				level: 8,
				requireAuth: true
			}
		},
		{
			path: '/bank/password_edit',
			component: resolve => require(['../views/bank/passwordEdit'], resolve),
			meta: {
				level: 6,
				requireAuth: true
			}
		},
		{
			path: '/address',
			component: resolve => require(['../views/address/address'], resolve),
			meta: {
				level: 4,
				requireAuth: true
			}
		},
		{
			path: '/address/edit',
			component: resolve => require(['../views/address/edit'], resolve),
			meta: {
				level: 5,
				requireAuth: true
			}
		},
		{
			path: '/feedback',
			component: resolve => require(['../views/member/feedback'], resolve),
			meta: {
				level: 2
			}
		},
		{
			path: '/login',
			component: login,
			meta: {
				level: 1
			}
		},
		{
			path: '/register',
			component: register,
			meta: {
				level: 2
			}
		},
		{
			path: '/passport/forget',
			component: resolve => require(['../views/passport/forget'], resolve),
			meta: {
				level: 6
			}
		},
		{
			path: '/passport/forgetcode',
			name: 'forgetcode',
			component: resolve => require(['../views/passport/forgetcode'], resolve),
			meta: {
				level: 7
			}
		},
		{
			path: '/passport/forgetpassword',
			name: 'forgetpassword',
			component: resolve => require(['../views/passport/forgetpassword'], resolve),
			meta: {
				level: 8
			}
		},
		{
			path: '/goods',
			component: resolve => require(['../views/goods/index'], resolve),
			meta: {
				level: 1
			}
		},
		{
			path: '/goods/detail',
			component: resolve => require(['../views/goods/detail'], resolve),
			meta: {
				level: 2
			}
		},
		{
			path: '/goods/groupbuy',
			component: resolve => require(['../views/goods/groupbuy'], resolve),
			meta: {
				level: 1
			}
		},
		{
			path: '/goods/purchase',
			component: resolve => require(['../views/goods/purchase'], resolve),
			meta: {
				level: 1
			}
		},
		{
			path: '/goods/chop',
			component: resolve => require(['../views/goods/chop'], resolve),
			meta: {
				level: 1
			}
		},
		{
			path: '/comment',
			component: resolve => require(['../views/comment/comment'], resolve),
			meta: {
				level: 4
			}
		},
		{
			path: '/order',
			component: resolve => require(['../views/order/order'], resolve),
			meta: {
				level: 1,
				requireAuth: true
			}
		},
		{
			path: '/order/detail',
			component: resolve => require(['../views/order/detail'], resolve),
			meta: {
				level: 2,
				requireAuth: true
			}
		},
		{
			path: '/order/refund',
			component: resolve => require(['../views/order/refund'], resolve),
			meta: {
				level: 3,
				requireAuth: true
			}
		},
		{
			path: '/order/express',
			component: resolve => require(['../views/order/express'], resolve),
			meta: {
				level: 3,
				requireAuth: true
			}
		},
		{
			path: '/order/comment',
			component: resolve => require(['../views/order/comment'], resolve),
			meta: {
				level: 3,
				requireAuth: true
			}
		},
		{
			path: '/recharge',
			component: resolve => require(['../views/recharge/recharge'], resolve),
			meta: {
				level: 4,
				requireAuth: true
			}
		},
		{
			path: '/recharge/commit',
			component: resolve => require(['../views/recharge/commit'], resolve),
			meta: {
				level: 5,
				requireAuth: true
			}
		},
		{
			path: '*',
			component: Error
		}
	]
})

const getComponent = app => {
	let component = null
	for (let i = 0; i < app.$children[0].$children.length; i++) {
		if (app.$children[0].$children[i].$el.className !== 'footer') {
			component = app.$children[0].$children[i]
			break
		}
	}
	return component
}

const getRoute = function(location) {
	let route = null
	if (location instanceof Vue) location = location.$route.path
	if (typeof location === 'string') location = { path: (location.split('?'))[0] }
	let routes = this.options.routes
	for (let i = 0; i < routes.length; i++) {
		if (location.name) {
			if (routes[i].name && routes[i].name === location.name) {
				route = routes[i]
				break
			}
		} else {
			if (routes[i].path === location.path) {
				route = routes[i]
				break
			}
		}
	}
	return route
}

const originalPush = VueRouter.prototype.push
VueRouter.prototype.push = function push(location) {
	let component = getComponent(this.app)
	let from = component.$route
	if (from.meta.paramAlive) {
		$.paramAlive(from.path, component)
		let scrollTop = 0
		if (component.scroller) {
			scrollTop = component.scroller.scrollTop()
		} else {
			scrollTop = document.documentElement.scrollTop || document.body.scrollTop
		}
		for (let i = 0; i < this.options.routes.length; i++) {
			if (this.options.routes[i].path === from.path) {
				this.options.routes[i].meta.scrollTop = scrollTop
				break
			}
		}
	}
	let to = getRoute.call(this, location)
	$.paramAlive(to.path, null)
	return originalPush.call(this, location).catch(err => err)
}

//子路由
//https://blog.csdn.net/wuzhe128520/article/details/89788512

//router.replace 跟 router.push 很像，唯一的不同就是，它不会向 history 添加新记录，而是跟它的方法名一样，替换掉当前的 history 记录
//<router-link :to="..." replace>Category</router-link>

/*
//只显示一次guide页面
router.beforeEach((to, from, next) => {
	let is_not_first = this.a.app.$.storage('is_not_first')
	if (!is_not_first && to.path !== '/guide') next('/guide')
	else if (is_not_first && to.path === '/guide') next('/')
	else next()
})
*/

//路由导航守卫拦截
router.beforeEach((to, from, next) => {
	let app = this.a.app
	let store = app.$store
	//设置 head meta
	let head = document.querySelector('head')
	let headMeta = [
		{name:'viewport', content:'width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=0'},
		{name:'format-detection', content:'telephone=no'},
		{name:'format-detection', content:'email=no'},
		{name:'format-detection', content:'address=no'},
		{name:'apple-mobile-web-app-capable', content:'yes'}
	]
	headMeta.forEach(item => {
		let meta = document.createElement('meta')
		meta.name = item.name
		meta.content = item.content
		head.appendChild(meta)
	})
	if (to.meta.name && to.meta.content) {
		let meta = document.createElement('meta')
		meta.name = to.meta.name
		meta.content = to.meta.content
		head.appendChild(meta)
	}
	if (to.meta.title) document.title = to.meta.title
	if (to.path === '/login' || (from.path !== '/login' && to.path === '/register')) {
		if (store) store.commit('transition', 'slide-up')
		next()
		return
	}
	//检测目标页面是否需要登录
	if (to.meta.requireAuth) {
		const member = app.$.storage('member')
		if (!member) {
			if (store) store.commit('transition', 'slide-up')
			next({path:'/login', query:{url:to.path}})
			return
		}
	}
	//设置页面过渡动画名称
	if (store) {
		if (from.path === '/login' || (to.path !== '/login' && from.path === '/register')) store.commit('transition', 'slide-down')
		else if (from.meta.level > to.meta.level) store.commit('transition', 'slide-right')
		else if (from.meta.level < to.meta.level) store.commit('transition', 'slide-left')
		else store.commit('transition', 'fade')
	}
	//结合 <keep-alive> 实现记录页面滚动位置
	if (from.meta.keepAlive) {
		let component = getComponent(app)
		if (component && component.scroller) {
			from.meta.scrollTop = component.scroller.scrollTop()
		} else {
			from.meta.scrollTop = document.documentElement.scrollTop || document.body.scrollTop
		}
	}
	next()
})
router.afterEach((to, from) => {
	let app = this.a.app
	if (!to.meta.keepAlive || !to.meta.scrollTop) {
		setTimeout(() => {
			document.documentElement.scrollTop = 0
			document.body.scrollTop = 0
		}, 0)
	} else {
		setTimeout(() => {
			let component = getComponent(app)
			if (component && component.scroller) {
				component.scroller.scrollTop(to.meta.scrollTop)
			} else {
				document.documentElement.scrollTop = to.meta.scrollTop
				document.body.scrollTop = to.meta.scrollTop //document.body.scrollTop 一定要加，否则iOS会失效
			}
		}, 50)
	}
})

export default router