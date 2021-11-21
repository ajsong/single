import Vue from 'vue'
import axios from 'axios'
import router from './router'
import store from './store'
import helper from './plugins/helper'
import App from './App.vue'
import '../css/mobile.css'

Vue.config.productionTip = false
axios.defaults.baseURL = 'http:'+'//single.cn'
Object.keys(helper.filters).forEach(key => Vue.filter(key, helper.filters[key]))
Vue.prototype.$ = helper.$
Vue.prototype.$axios = axios
Vue.prototype.$ajax = new helper.Ajax()

new Vue({
	el: '#app',
	router,
	store,
	render: h => h(App)
})