<template>
<div>
	<div class="navBar navBar-transparent">
		<router-link class="left" to="/"><i class="return"></i></router-link>
		<div class="titleView-x">登录</div>
		<router-link class="right" :to="{ path:'/register', query:{url:url} }"><span>注册</span></router-link>
	</div>

	<div class="login-index width-wrap">
		<ul class="inputView">
			<li class="ge-bottom"><div><i></i><input type="tel" name="mobile" placeholder="请输入手机号码" @keydown.enter="login" v-model="mobile" /></div></li>
			<li class="ge-bottom"><div class="password"><em :class="showClass" @click="togglePass"></em><i></i><input type="password" placeholder="请输入密码" @keydown.enter="login" v-if="isHidden" v-model="password" /><input type="text" placeholder="请输入密码" @keydown.enter="login" v-model="password" v-else /></div></li>
			<div class="buttonView">
				<a href="javascript:void(0)" class="btn" @click="login">登录</a>
			</div>
			<router-link to="/passport/forget" class="forget">忘记密码</router-link>
		</ul>
	</div>
</div>
</template>

<script>
export default {
	name:'login',
	data(){
		return {
			url: '/member',
			elParent: null,
			isHidden: true,
			showClass: '',
			mobile: '',
			password: ''
		}
	},
	methods: {
		login() {
			if(!this.mobile.length || !this.password.length){
				this.$emit('overloaderror', '请输入手机与密码')
				return
			}
			this.$ajax.post('/api/passport/login', {
				mobile: this.mobile,
				password: this.password
			}).then(json => {
				if(json.error !== 0){
					this.$emit('overloaderror', json.msg)
					return
				}
				$.storage('member', json.data, 365)
				this.$router.push(this.url)
			})
		},
		togglePass(){
			this.isHidden = !this.isHidden
			this.showClass = this.isHidden ? '' : 'x'
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
	}
}
</script>

<style scoped>

</style>