<template>
<div>
	<div class="navBar navBar-transparent">
		<router-link class="left" to="/"><i class="return"></i></router-link>
		<div class="titleView-x">注册</div>
		<router-link class="right" :to="{ path:'/login', query:{url:url} }"><span>登录</span></router-link>
	</div>

	<div class="login-index width-wrap">
		<ul class="inputView">
			<li class="ge-bottom"><div><i></i><input type="tel" placeholder="请输入手机号码(注册后不可更改)" @keydown.enter="register" v-model="mobile" /></div></li>
			<li class="ge-bottom"><div class="code"><a href="javascript:void(0)" @click="getCodeText" v-html="codeHtml" :class="codeClass"></a><i></i><input type="text" placeholder="请输入验证码" @keydown.enter="register" v-model="code" /></div></li>
			<li class="ge-bottom"><div class="password"><em :class="emClass" @click="togglePass"></em><i></i><input type="password" placeholder="请输入密码" @keydown.enter="register" v-if="isHidden" v-model="password" /><input type="text" placeholder="请输入密码" @keydown.enter="login" v-model="password" v-else /></div></li>
			<li class="ge-bottom"><div class="invite"><i></i><input type="text" placeholder="请输入邀请码" v-model="invite_code" /></div></li>
			<div class="buttonView">
				<a href="javascript:void(0)" class="btn" @click="register">注册</a>
			</div>
			<router-link to="/article/detail?id=useragree">注册即视为同意《服务协议》</router-link>
		</ul>
	</div>
</div>
</template>

<script>
export default {
	name:'register',
	data(){
		return {
			url: '/member',
			elParent: null,
			isHidden: true,
			emClass: '',
			mobile: '',
			password: '',
			code: '',
			invite_code: '',

			codeHtml: '<span>发送短信</span>',
			codeClass: '',
			codeText: '',
			getCode: false,
			count: 0,
			timer: null
		}
	},
	created(){
		if(this.$route.query.url)this.url = this.$route.query.url
	},
	mounted(){
		document.querySelector('body').style.height = '100%'
		this.$nextTick(() => {
			this.elParent = this.$el.parentNode
			$(this.$el.parentNode).add(this.$el).addClass('height-wrap')
		})
	},
	beforeDestroy(){
		document.querySelector('body').style.height = ''
		$(this.elParent).removeClass('height-wrap')
	},
	methods:{
		register(){
			if(!this.mobile.length){
				this.$emit('overloaderror', '请输入手机号码')
				return
			}
			if(!this.code.length){
				this.$emit('overloaderror', '请输入验证码')
				return
			}
			if(!this.password.length){
				this.$emit('overloaderror', '请输入密码')
				return
			}
			if(this.code !== this.codeText){
				this.$emit('overloaderror', '验证码不正确')
				return
			}
			this.$ajax.post('/api/passport/register', {
				mobile: this.mobile,
				password: this.password,
				code: this.code,
				invite_code: this.invite_code
			}).then(json => {
				if(json.error !== 0){
					this.$emit('overloaderror', json.msg)
					return
				}
				this.$.storage('member', json.data, 365)
				this.$router.push({ path:this.url })
			})
		},
		togglePass(){
			this.isHidden = !this.isHidden
			this.emClass = this.isHidden ? '' : 'x'
		},
		getCodeText(){
			if(this.getCode)return
			if(!this.mobile.length){
				this.$emit('overloaderror', '请输入手机号码')
				return
			}
			this.codeHtml = '<div class="preloader"></div>'
			this.getCode = true
			this.codeText = ''
			this.$ajax.post('/api/passport/check_mobile', { mobile:this.mobile }).then(json => {
				if(json.error !== 0){
					this.getCode = false
					this.$emit('overloaderror', json.msg)
					return
				}
				//_mobileText = json.data.mobile
				this.codeText = json.data.code
				this.count = 60
				this.timer = setInterval(() => {
					this.count--;
					if (this.count <= 0) {
						clearInterval(this.timer)
						this.getCode = false
						this.codeClass = ''
						this.codeHtml = '<span>再次发送</span>'
					} else {
						this.codeClass = 'disabled'
						this.codeHtml = '<span>'+this.count+'s后重发</span>'
					}
				}, 1000);
			})
		}
	}
}
</script>

<style scoped>

</style>