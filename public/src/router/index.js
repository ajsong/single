import Vue from 'vue'
import VueRouter from 'vue-router'

import home from '../views/index/index'
import search from '../views/index/search'
import category from '../views/category/category'
import article from '../views/article/article'
import cart from '../views/cart/cart'
import member from '../views/member/member'
import login from '../views/passport/login'
import register from '../views/passport/register'
import Error from '../components/error'

Vue.use(VueRouter)

const router = new VueRouter({
	mode: 'history',//使用 history 模式时，URL 就像正常的 url,
	routes: [
		{
			path: '/',
			index: 0,
			component: home,
			meta: {
				keepAlive: true,
				scrollTop: 0
			}
			//component: resolve => require(['../views/index/index'], resolve)//懒加载，this.$route.path 无效
		},
		{
			path: '/search/:keyword?',
			name: 'search',
			component: search
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
			component: category
		},
		{
			path: '/article',
			index: 2,
			component: article,
			meta: {
				keepAlive: true,
				scrollTop: 0
			}
		},
		{
			path: '/cart',
			index: 3,
			component: cart,
			meta: {
				keepAlive: true,
				scrollTop: 0,
				requireAuth: true
			}
		},
		{
			path: '/cart/commit',
			name: 'cartCommit',
			component: resolve => require(['../views/cart/commit'], resolve),
			meta: {
				keepAlive: true,
				requireAuth: true
			}
		},
		{
			path: '/member',
			index: 4,
			component: member,
			meta: {
				keepAlive: true,
				scrollTop: 0,
				requireAuth: true
			}
		},
		{
			path: '/member/sign',
			component: resolve => require(['../views/member/sign'], resolve),
			meta: {
				requireAuth: true
			}
		},
		{
			path: '/member/set',
			component: resolve => require(['../views/member/set'], resolve),
			meta: {
				requireAuth: true
			}
		},
		{
			path: '/member/password',
			component: resolve => require(['../views/member/password'], resolve),
			meta: {
				requireAuth: true
			}
		},
		{
			path: '/member/edit',
			component: resolve => require(['../views/member/edit'], resolve),
			meta: {
				requireAuth: true
			}
		},
		{
			path: '/member/code',
			component: resolve => require(['../views/member/code'], resolve),
			meta: {
				requireAuth: true
			}
		},
		{
			path: '/member/message',
			component: resolve => require(['../views/member/message'], resolve),
			meta: {
				requireAuth: true
			}
		},
		{
			path: '/member/favorite',
			component: resolve => require(['../views/member/favorite'], resolve),
			meta: {
				requireAuth: true
			}
		},
		{
			path: '/member/goods_history',
			component: resolve => require(['../views/member/goodshistory'], resolve),
			meta: {
				requireAuth: true
			}
		},
		{
			path: '/address',
			component: resolve => require(['../views/address/address'], resolve)
		},
		{
			path: '/address/edit',
			component: resolve => require(['../views/address/edit'], resolve)
		},
		{
			path: '/feedback',
			component: resolve => require(['../views/member/feedback'], resolve)
		},
		{
			path: '/login',
			component: login
		},
		{
			path: '/register',
			component: register
		},
		{
			path: '/passport/forget',
			component: resolve => require(['../views/passport/forget'], resolve)
		},
		{
			path: '/passport/forgetcode',
			name: 'forgetcode',
			component: resolve => require(['../views/passport/forgetcode'], resolve)
		},
		{
			path: '/passport/forgetpassword',
			component: resolve => require(['../views/passport/forgetpassword'], resolve)
		},
		{
			path: '/goods',
			component: resolve => require(['../views/goods/index'], resolve),
		},
		{
			path: '/goods/detail',
			component: resolve => require(['../views/goods/detail'], resolve),
		},
		{
			path: '/goods/groupbuy',
			component: resolve => require(['../views/goods/groupbuy'], resolve),
		},
		{
			path: '/goods/purchase',
			component: resolve => require(['../views/goods/purchase'], resolve),
		},
		{
			path: '/goods/chop',
			component: resolve => require(['../views/goods/chop'], resolve),
		},
		{
			path: '*',
			component: Error
		}
	]
})

const originalPush = VueRouter.prototype.push
VueRouter.prototype.push = function push(location) {
	return originalPush.call(this, location).catch(err => err)
}

//子路由
//https://blog.csdn.net/wuzhe128520/article/details/89788512

//router.replace 跟 router.push 很像，唯一的不同就是，它不会向 history 添加新记录，而是跟它的方法名一样，替换掉当前的 history 记录
//<router-link :to="..." replace>Category</router-link>

/*
//只显示一次guide页面
router.beforeEach((to, from, next) => {
	let store = this.a.app.$store;
	if(store){
		if(from.meta.status > to.meta.status) store.commit('SET_ANIMATE_NAME','vux-pop-out');
		else store.commit('SET_ANIMATE_NAME','vux-pop-in');
	}
	let is_not_first = Tool.dataToLocalStorageOperate.achieve('is_not_first');
	if ( !is_not_first && to.path != '/guide' ) next('/guide');
	else if( is_not_first && to.path == '/guide' ) next('/');
	else next();
});

//不同路由不同页面标题
router.afterEach((to, from, next) => {
	document.title = to.name;
})
*/

export default router