<template>
<div id="app">
	<transition :name="transitionName">
	<keep-alive>
	<router-view v-if="$route.meta.keepAlive" v-on:overload="overload" v-on:overloadsuccess="overloadSuccess" v-on:overloaderror="overloadError" v-on:overloadproblem="overloadProblem" v-on:overloadwarning="overloadWarning"></router-view>
	</keep-alive>
	</transition>

	<transition :name="transitionName">
	<router-view v-if="!$route.meta.keepAlive" v-on:overload="overload" v-on:overloadsuccess="overloadSuccess" v-on:overloaderror="overloadError" v-on:overloadproblem="overloadProblem" v-on:overloadwarning="overloadWarning"></router-view>
	</transition>

	<transition name="footer">
	<app-footer ref="footer" v-show="footerShow"></app-footer>
	</transition>

	<overload @click.native="closeOverload" v-if="showOverload" :text="overloadText" :image="overloadImage" :auto="overloadAuto" :callback="overloadCallback" :hidden="overloadHidden" v-on:overload="overload"></overload>
</div>
</template>

<script>
import appFooter from './components/footer'
import overload from './components/overload'
export default {
	name: 'app',
	data(){
		return {
			footerShow: false,
			showOverload: false,
			overloadText: '',
			overloadImage: '',
			overloadAuto: 0,
			overloadCallback: null,
			overloadHidden: false
		}
	},
	components: {
		appFooter,
		overload
	},
	computed:{
		transitionName(){
			return this.$store.state.transitionName
		}
	},
	beforeCreate(){
		document.body.className = 'gr'
	},
	mounted(){
		this.$nextTick(() => {
			$.component(this) //因为 cartCommit、articleEdit 要用到页面返回传值
			this.$router.options.routes.forEach(item => {
				let routePath = [this.$route.path.replace(/(^\/)|(\/$)/g, '')]
				let isMatch = false
				let paramOptional = false
				let itemPath = item.path.replace(/(^\/)|(\/$)/g, '').split('/')
				if (itemPath.length > 1) {
					routePath = this.$route.path.replace(/(^\/)|(\/$)/g, '').split('/')
					for (let path in itemPath) {
						if (/\??/.test(path)) {
							paramOptional = true
							break
						}
					}
					isMatch = (itemPath[0] === routePath[0]) && paramOptional
				}
				if (typeof item.index !== 'undefined') {
					if (itemPath[0] === routePath[0]) this.footerShow = true
				}
			})
		})
	},
	updated(){
		let footerShow = false
		let index = 0
		this.$router.options.routes.forEach(item => {
			let routePath = [this.$route.path.replace(/(^\/)|(\/$)/g, '')]
			let isMatch = false
			let paramOptional = false
			let itemPath = item.path.replace(/(^\/)|(\/$)/g, '').split('/')
			if (itemPath.length > 1) {
				routePath = this.$route.path.replace(/(^\/)|(\/$)/g, '').split('/')
				for (let path in itemPath) {
					if (/\??/.test(path)) {
						paramOptional = true
						break
					}
				}
				isMatch = (itemPath[0] === routePath[0]) && paramOptional
			}
			if (typeof item.index !== 'undefined') {
				if (itemPath[0] === routePath[0]) footerShow = true
				if (item.path === this.$route.path || isMatch) index = item.index
			}
		})
		this.footerShow = footerShow
		if (this.$refs.footer) this.$refs.footer.index = index
	},
	methods: {
		setBadge(index, html){
			if (this.$refs.footer) this.$refs.footer.setBadge(index, html)
		},
		overload: function(text, image, auto, callback){
			if (typeof text === 'boolean' && !text) {
				this.closeOverload()
				return
			}
			if (typeof text === 'boolean') text = ''
			if (typeof image === 'number') {
				if (typeof auto === 'function') callback = auto
				auto = image
				image = null
			}
			if (typeof image === 'undefined' || (typeof image === 'string' && !image.length)) image = '.load-animate'
			if (typeof auto === 'function' && typeof callback === 'undefined') {
				callback = auto
				auto = 3000
			}
			this.overloadText = text || ''
			this.overloadImage = image || ''
			setTimeout(() => this.overloadAuto = auto || 0, 0)
			this.overloadCallback = callback || null
			this.showOverload = true
		},
		overloadSuccess: function(text, auto, callback){
			if (typeof auto === 'undefined') auto = 3000
			setTimeout(() => this.overload(text, '.load-success', auto, callback), 0)
		},
		overloadError: function(text, auto, callback){
			if (typeof auto === 'undefined') auto = 3000
			setTimeout(() => this.overload(text, '.load-error', auto, callback), 0)
		},
		overloadProblem: function(text, auto, callback){
			if (typeof auto === 'undefined') auto = 3000
			setTimeout(() => this.overload(text, '.load-problem', auto, callback), 0)
		},
		overloadWarning: function(text, auto, callback){
			if (typeof auto === 'undefined') auto = 3000
			setTimeout(() => this.overload(text, '.load-warning', auto, callback), 0)
		},
		closeOverload: function(){
			this.overloadHidden = true
			setTimeout(() => this.showOverload = false, 500)
		}
	}
}
</script>

<style lang="scss">
a, a:hover{text-decoration:none!important;}

.footer-enter{-webkit-transform:translate3d(0, 100%, 0); transform:translate3d(0, 100%, 0);}
.footer-enter-to{-webkit-transform:translate3d(0, 0, 0); transform:translate3d(0, 0, 0);}
.footer-leave-to{-webkit-transform:translate3d(0, 100%, 0); transform:translate3d(0, 100%, 0);}
.footer-enter-active, .footer-leave-active{-webkit-transition:all 0.3s ease-out; transition:all 0.3s ease-out;}

.fade-enter, .fade-leave-to{opacity:0;}
.fade-enter-active, .fade-leave-active{position:fixed; left:0; top:0; width:100%; height:100%; -webkit-transition:opacity 0.3s ease-out; transition:opacity 0.3s ease-out;}

.slide-left-enter{-webkit-transform:translate3d(100%, 0, 0); transform:translate3d(100%, 0, 0);}
.slide-left-enter-to{-webkit-transform:translate3d(0, 0, 0); transform:translate3d(0, 0, 0);}
/*.slide-left-leave{-webkit-transform:translate3d(0, 0, 0); transform:translate3d(0, 0, 0);}*/
.slide-left-leave-to{-webkit-transform:translate3d(-100%, 0, 0); transform:translate3d(-100%, 0, 0);}

.slide-right-enter{-webkit-transform:translate3d(-100%, 0, 0); transform:translate3d(-100%, 0, 0);}
.slide-right-enter-to{-webkit-transform:translate3d(0, 0, 0); transform:translate3d(0, 0, 0);}
.slide-right-leave-to{-webkit-transform:translate3d(100%, 0, 0); transform:translate3d(100%, 0, 0);}

.slide-up-enter{-webkit-transform:translate3d(0, 100%, 0); transform:translate3d(0, 100%, 0);}
.slide-up-enter-to{-webkit-transform:translate3d(0, 0, 0); transform:translate3d(0, 0, 0);}
.slide-up-leave-to{-webkit-transform:translate3d(0, -100%, 0); transform:translate3d(0, -100%, 0);}

.slide-down-enter{-webkit-transform:translate3d(0, -100%, 0); transform:translate3d(0, -100%, 0);}
.slide-down-enter-to{-webkit-transform:translate3d(0, 0, 0); transform:translate3d(0, 0, 0);}
.slide-down-leave-to{-webkit-transform:translate3d(0, 100%, 0); transform:translate3d(0, 100%, 0);}

.slide-left-enter-active, .slide-left-leave-active,
.slide-right-enter-active, .slide-right-leave-active,
.slide-up-enter-active, .slide-up-leave-active,
.slide-down-enter-active, .slide-down-leave-active{position:fixed; left:0; top:0; width:100%; height:100%; -webkit-transition:all 0.4s ease-out; transition:all 0.4s ease-out;}
</style>
