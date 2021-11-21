<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">修改提现密码</div>
	</div>

	<div class="password-edit">
		<span>请输入提现密码，以验证身份。</span>
		<input type="tel" v-model="password" id="password" class="hidden" />
	</div>
</div>
</template>

<script>
export default {
	name: 'passwordEdit',
	data() {
		return {
			password: ''
		}
	},
	mounted(){
		setTimeout(() => {
			$('#password').removeClass('hidden').passwordView({
				cls: 'underline',
				callback: (placeholders, font) => {
					this.$ajax.post('/api/bank/password_edit', {password:this.password}).then(json => {
						if (!$.checkError(json, this)) {
							$('#password').val('').focus()
							placeholders.removeClass('this').val('')
							font.show().css('left', placeholders.eq(0).position().left+placeholders.eq(0).width()/2);
							this.password = ''
							return
						}
						this.$router.push('/bank/password_set')
					})
				}
			})
		}, 500)
	}
}
</script>

<style scoped>

</style>