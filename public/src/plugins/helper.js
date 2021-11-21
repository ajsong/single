import Vue from 'vue'
import axios from 'axios'
import coo from './coo'
window.$ = coo.$
let filters = coo.filters

//死循环调试，在怀疑死循环的地方加入__detectLoop()
let __count = 0
const __detectLoop = (loop) => {
	if (typeof loop === 'undefined') loop = 100
	if (__count > loop) {
		console.trace()
		throw new Error('Loop detected')
	}
	__count += 1
}

//把axios封装为快捷请求方法
const AJAX_TIME_OUT = 30000 //超时时间30秒
axios.interceptors.request.use(config => { //请求数据拦截处理
	//console.log(config)
	if (!/^http/.test(config.url)) {
		const account = $.storageJSON('member')
		if (account && account.sign) {
			config.headers['Sign'] = account.sign
		}
	}
	return config
}, error => {
	return Promise.reject(error)
})
axios.interceptors.response.use(response => { //返回数据拦截处理
	//console.log(response)
	//if (axios.caller) {}
	return response.data
}, error => Promise.reject(error.response))
const __request = (method, url, data, responseType, caller) => {
	const headers = {}
	const configData = {
		url, //请求的地址
		timeout: AJAX_TIME_OUT, //超时时间, 单位毫秒
		headers
	}
	if (method === 'get') {
		configData.method = 'get'
		configData.params = data
		if(typeof responseType==='string' && responseType.length) configData.responseType = responseType
	} else if (method === 'post') {
		configData.method = 'post'
		if (data instanceof FormData) {
			configData.headers['Content-Type'] = 'multipart/form-data; charset=UTF-8'
			configData.data = data
		} else {
			configData.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8'
			configData.data = Object.entries(data).map(item => item.join('=')).join('&')
		}
	} else if (method === 'json') {
		configData.method = 'post'
		configData.headers['Content-Type'] = 'application/json; charset=UTF-8'
		configData.data = (() => {
			if (typeof data !== 'string') return data
			let obj = {}
			data.split('&').map(item => obj[item.split('=')[0]] = item.split('=')[1])
			return obj
		})(data)
	}
	if (caller) axios.caller = caller
	return axios(configData)
}
class Ajax {
	constructor () {
		this.outer = null
	}
	get (url, data = {}, responseType = '') {
		return __request('get', url, data, responseType, this.outer)
	}
	post (url, data = {}) {
		return __request('post', url, data, '', this.outer)
	}
	postJSON (url, data = {}) {
		return __request('json', url, data, '', this.outer)
	}
	caller (outer) {
		this.outer = outer
	}
}
//export default new Ajax()
/*
使用方法
import ajax from './ajax'
ajax.get(url, data).then(res => {
	//code
}).catch(err => {
	//code
})
//上传图片
const formData = new FormData()
formData.append('file', this.file) //this.file是input type='file'选中的图片
ajax.post(url, formData).then(res => {
	//code
}).catch(err => {
	//code
})
//直接把挂载在vue.prototype上，就不用每次都去导入
Vue.prototype.$ajax = new Ajax()
this.$ajax.get(url, data).then(res => {
	//code
}).catch(err => {
	//code
})
//做成一个插件的形式，在ajax.js中最后导出一个函数
//ajax.js
export default (Vue) => {
	if (typeof window !== 'undefined' && window.Vue) {
		Vue = window.Vue
	}
	Vue.prototype.$ajax = new Ajax()
}
//main.js
import Vue from 'vue'
import ajax from './ajax'
Vue.use(ajax)
//axios模拟表单提交
this.$axios.post(url, Object.entries(data).map(ent => ent.join('=')).join('&'), {
	headers: {
		'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
	},
}).then(res => {
	//code
})
*/

/*
//async/await用法
//async 是一个修饰符，async 定义的函数会默认的返回一个Promise对象resolve的值，因此对async函数可以直接进行then操作,返回的值即为then方法的传入函数
async function fun() {
	console.log('a')
	return 'a'
}
fun().then( x => { console.log(x) }) //输出 a a
async function fun() {
	console.log('Promise')
	return new Promise(function(resolve, reject){
		resolve('Promise')
	})
}
fun().then( x => { console.log(x) }) //输出 promise promise
//await 只能放在 async 函数内部， await的作用就是获取 Promise 中返回的内容，获取的是Promise函数中resolve或者reject的值
//如果 await 后面并不是一个Promise的返回值，则会按照同步程序返回值处理,为undefined
const bbb = function(){return 'string'}
async function fun() {
	const a = await 1
	const b = await new Promise((resolve, reject) => {
		setTimeout(function(){
			resolve('time')
		}, 3000)
	})
	const c = await bbb()
	console.log(a, b, c)
}
fun() //运行结果是 3秒钟之后，输出 1 time string
*/

$.extend({
	//判断当前是否开发模式
	isDevelopment: function() {
		return location.href.indexOf('localhost') > -1
	},
	//设置/获取组件Vue实例
	component: function(component){
		return arguments.length ? $(document).data('component', component) : $(document).data('component')
	},
	//判断是否已登录
	checklogin: function(url, caller){
		const member = $.storage('member')
		if (!member) {
			if (caller instanceof Vue) caller.$router['push']({path:'/login', query:{url:(typeof url === 'undefined' ? location.pathname+location.search : url)}, hash:'#presentView'})
			return false
		}
		return true
	},
	//简易显示分享箭头
	shareMark: function(hidden){
		let mark = $('.share-mark')
		if (!mark.length) {
			mark = $('<div class="share-mark" style="display:none;"></div>')
			$('#app').children().not('.footer').append(mark)
		}
		if (!mark.attr('shareMark')) {
			mark.attr('shareMark', 'complete')
			mark.on('click', () => mark.fadeOut(300))
		}
		mark[typeof hidden === 'undefined' ? 'fadeIn' : 'fadeOut'](300)
	},
	//rem转px
	toPx: function(rem) {
		let size = $.changeRem(true)
		return rem * size
	},
	//设置rem
	changeRem: function(isReturn) {
		__detectLoop()
		let size = 100 * (window.screen.width / 320)
		if (window.screen.width >= 768) size = 100
		if (!isReturn) {
			if ($.browser.mobile) document.documentElement.style.fontSize = size + 'px'
		}
		else return size
	},
	//页面返回传值
	paramAlive: function(key, value) {
		let component = $.component()
		if ( !component || !(component instanceof Vue) ) {
			alert('Use paramAlive plugins must be set component')
			return this
		}
		if (typeof value === 'undefined') {
			return new Promise((resolve, reject) => {
				let data = component.$store.state[key]
				if (data) setTimeout(() => resolve(data), 0)
				else reject()
			})
		} else if (typeof value === 'function') {
			let data = component.$store.state[key]
			if (data) setTimeout(() => value(data), 0)
		} else if (value === null) {
			component.$store.commit('delete', key)
		} else {
			if (key instanceof Vue) {
				return new Promise((resolve, reject) => {
					let data = component.$store.state[key.$route.path]
					if (data) {
						for (let k in key._data) key[k] = data[k]
						if (key.$route.meta.scrollTop) {
							let scrollTop = key.$route.meta.scrollTop
							setTimeout(() => {
								if (key.scroller) {
									key.scroller.scrollTop(scrollTop)
								} else {
									document.documentElement.scrollTop = scrollTop
									document.body.scrollTop = scrollTop
								}
							}, 500)
						}
						data = component.$store.state[value]
						if (data) setTimeout(() => {
							component.$nextTick(() => resolve(data))
						}, 0)
					}
					else reject()
				})
			}
			if (value instanceof Vue) value = value._data
			component.$store.commit('set', {key, value})
		}
	},
	//封装接口数据是否提示错误，if (!this.$.checkError(json, this)) return
	checkError: function(json, component) {
		if (typeof component === 'undefined') component = $.component()
		if (typeof json.error !== 'undefined' && typeof json.msg !== 'undefined' && json.error > 0) {
			if (component instanceof Vue) {
				if (json.msg_type === -10) component.$route['push']({ path: '/login', query: { url: location.pathname + location.search } })
				else {
					if (component.overloadError) component.overloadError(json.msg)
					else component.$emit('overloaderror', json.msg)
				}
			}
			else if (component !== null) alert(json.msg)
			return false
		}
		if (json.badge && (component instanceof Vue)) {
			if (typeof json.badge.cart !== 'undefined') {
				if (component.setBadge) component.setBadge(3, '<b>' + json.badge.cart + '</b>')
				else component.$parent.setBadge(3, '<b>' + json.badge.cart + '</b>')
			}
		}
		return true
	}
})

$.extend($.fn, {
	//设置/获取jQuery插件Vue实例
	component: function(component){
		return arguments.length ? this.data('component', component) : this.data('component')
	},
	//设置价格数字样式
	priceFont: function(className){
		return this.each(function(){
			let _this = $(this), text = _this.text()
			if (!text.length || (text.indexOf('￥') === -1 && text.indexOf('.') === -1) || _this.children('.'+className).length) return true
			if (text.indexOf('￥')>-1) {
				let ar = text.split('￥'), prefix = ar[0]+'￥', arr = ar[1].split('.'), integer = arr[0], decimal = ''
				if (arr.length===2) decimal = '.'+arr[1]
				text = prefix + '<font class="'+className+'">' + integer + '</font>' + decimal
			} else {
				let arr = text.split('.'), integer = arr[0], decimal = ''
				if (arr.length === 2) decimal = '.'+arr[1]
				text = '<font class="'+className+'">' + integer + '</font>' + decimal
			}
			_this.html(text)
		})
	}
})

//按原宽高的比例自动设定高度, percent是按屏幕宽度作为参考对象
$.autoHeight = $.fn.autoHeight = function(originWidth, originHeight, percent){
	let fn = function(isReturn) {
		if (isReturn) return Math.floor($(this).width() * originHeight / originWidth)
		let _this = $(this)
		//if (!_this.is(':visible')) return true
		if (percent) {
			_this.width( Math.floor((document.documentElement.clientWidth || document.body.clientWidth) * percent) )
		}
		if ((originWidth+'').indexOf('%') > -1) {
			_this.width( originWidth )
		}
		if ((originHeight+'').indexOf('%') > -1) {
			_this.height( originHeight )
		} else {
			_this.height( Math.floor(_this.width() * originHeight / originWidth) )
		}
	}
	if (this instanceof $) {
		return this.each(function(){
			fn.call(this)
		})
	} else if (percent) {
		return fn.call(percent, true)
	}
}

if ($.browser.mobile) $(document.body.parentNode).addClass('wapWeb')
$.changeRem()
window.onresize = function() {
	$.changeRem() //改变窗口大小时重新设置rem
}

export default {
	$,
	filters,
	Ajax
}