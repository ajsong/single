<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">确认密码</div>
	</div>

	<div class="password-edit">
		<span>请再次输入密码以确认。</span>
		<input type="tel" v-model="confirm" id="confirm" class="hidden" />
	</div>
</div>
</template>

<script>
export default {
	name: 'repassword',
	data() {
		return {
			password: '',
			confirm: ''
		}
	},
	created(){
		this.password = this.$route.params.password
		if (!this.password) {
			alert('missing password')
			this.$router.go(-1)
			return
		}
		setTimeout(() => {
			$('#confirm').removeClass('hidden').passwordView({
				cls: 'underline',
				input: () => {
					$('#confirm').removeClass('error');
				},
				callback: () => {
					if (this.password !== this.confirm) {
						$.overloadError('两次密码不一致')
						$('#confirm').addClass('error')
						return
					}
					this.$ajax.post('/api/bank/password_set', {password:this.password}).then(json => {
						if (!$.checkError(json, this)) return
						$.overloadSuccess('设置提现密码成功', 4000, () => {
							this.$router.go(-2)
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