<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">设置{{ title }}</div>
	</div>

	<div class="password-edit">
		<span>请设置新的{{ title }}。</span>
		<input type="tel" v-model="password" id="password" class="hidden" />
	</div>
</div>
</template>

<script>
export default {
	name: 'forgetpassword',
	data() {
		return {
			title: '',
			mobile: '',
			code: '',
			target: '',
			action: '',
			password: ''
		}
	},
	created(){
		if (!this.$route.params.title || !this.$route.params.mobile || !this.$route.params.code || !this.$route.params.target || !this.$route.params.action) {
			this.$emit('overloaderror', '缺少数据')
			this.$router.go(-1)
			return
		}
		this.title = this.$route.params.title
		this.mobile = this.$route.params.mobile
		this.code = this.$route.params.code
		this.target = this.$route.params.target
		this.action = this.$route.params.action
		setTimeout(() => {
			$('#password').removeClass('hidden').passwordView({
				cls: 'underline',
				callback: () => {
					this.$ajax.post(this.action, {forget:'forget', mobile:this.mobile, code:this.code, password:this.password}).then(json => {
						if (!$.checkError(json, this)) return
						$.overloadSuccess('设置'+this.title+'成功', 4000, () => {
							this.$router.push(this.target)
						})
					})
				}
			})
		}, 500)
	}
}
</script>

<style scoped>

</style>