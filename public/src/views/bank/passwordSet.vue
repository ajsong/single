<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">设置提现密码</div>
	</div>

	<div class="password-edit">
		<span>请设置提现密码，请勿与银行卡密码相同。</span>
		<input type="tel" v-model="password" id="password" class="hidden" />
	</div>
</div>
</template>

<script>
export default {
	name: 'passwordSet',
	data() {
		return {
			password: ''
		}
	},
	mounted(){
		setTimeout(() => {
			$('#password').removeClass('hidden').passwordView({
				cls: 'underline',
				callback: () => {
					this.$router.push({name:'bankRepassword', params:{password:this.password}})
				}
			})
		}, 500)
	}
}
</script>

<style scoped>

</style>