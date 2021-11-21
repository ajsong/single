<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">忘记{{ title }}</div>
	</div>

	<div class="forget-index">
		<input type="hidden" v-model="code" />
		<div class="tip">请输入您注册时的手机号码</div>
		<div class="inputView">
			<div><i></i><input type="text" v-model="mobile" placeholder="手机号码" /></div>
		</div>
		<div class="buttonView">
			<a href="javascript:void(0)" class="btn" @click="submit">发送验证码</a>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name:'forget',
	data(){
		return {
			title: '账号密码',
			mobile: '',
			code: '',
			routername: 'forgetpassword',
			target: '/login',
			action: '/api/passport/forget'
		}
	},
	created(){
		if (this.$route.query.title) this.title = this.$route.query.title
		if (this.$route.query.routername) this.routername = this.$route.query.routername
		if (this.$route.query.target) this.target = this.$route.query.target
		if (this.$route.query.action) this.action = this.$route.query.action
	},
	methods:{
		submit(){
			if(!this.mobile.length){
				this.$emit('overloaderror', '请输入手机号码')
				return
			}
			this.$ajax.post('/api/passport/forget_sms', { mobile:this.mobile }).then(json => {
				if (!$.checkError(json, this)) return
				this.code = json.data.code
				this.$router.push({name:'forgetcode', params:{
					title: this.title,
					mobile: this.mobile,
					code: this.code,
					routername: this.routername,
					target: this.target,
					action: this.action,
					code_num: json.configs.GLOBAL_MOBILE_CODE_NUM
				}})
			})
		}
	}
}
</script>

<style scoped>

</style>