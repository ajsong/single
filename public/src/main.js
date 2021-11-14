import Vue from 'vue'
import router from './router'
import axios from 'axios'
import helper from './plugins/helper'
import App from './App.vue'
import '../css/mobile.css'

Vue.config.productionTip = false
axios.defaults.baseURL = 'http://single.cn'
Object.keys(helper.filters).forEach(key => Vue.filter(key, helper.filters[key]))
Vue.prototype.$ = helper.$
Vue.prototype.$axios = axios
Vue.prototype.$ajax = new helper.Ajax()

let VM
/*
const originalPush = VueRouter.prototype.push
VueRouter.prototype.push = (location, onComplete, onAbort) => {
	for (let i = 0; i < VM.$children.length; i++) {
		const component = VM.$children[i]
		if (component.$el.id === 'app') {
			component.transition = 'slide-right'
			setTimeout(() => component.transition = 'slide-left', 400)
			break
		}
	}
	originalPush.call(router, location, onComplete, onAbort)
}*/

//路由导航守卫拦截
router.beforeEach((to, from, next) => {
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
	if (to.path === '/login') {
		next()
		return
	}
	if (to.meta.requireAuth) {
		const member = helper.$.storage('member')
		if (!member) {
			next({path:'/login', query:{url:from.path}})
			return
		}
	}
	//结合 <keep-alive> 实现记录页面滚动位置
	if (from.meta.keepAlive) {
		from.meta.scrollTop = document.documentElement.scrollTop || document.body.scrollTop
	}
	next()
})
router.afterEach((to, from) => {
	if (!to.meta.keepAlive) {
		setTimeout(() => {
			document.documentElement.scrollTop = 0
			document.body.scrollTop = 0
		}, 0)
	} else {
		setTimeout(() => {
			document.documentElement.scrollTop = to.meta.scrollTop
			document.body.scrollTop = to.meta.scrollTop //document.body.scrollTop 一定要加，否则iOS会失效
		}, 50)
	}
})

VM = new Vue({
	el: '#app',
	router,
	render: h => h(App)
})