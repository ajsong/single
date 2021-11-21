<template>
<div v-if="action">
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">输入验证码</div>
	</div>

	<div class="forget-index">
		<input type="tel" id="confirm" v-model="confirm" class="hidden" />
	</div>
</div>
</template>

<script>
export default {
	name:'forgetcode',
	data(){
		return {
			title: '',
			mobile: '',
			code: '',
			routername: '',
			target: '',
			action: '',
			code_num: '4',
			confirm: ''
		}
	},
	created(){
		if (!this.$route.params.title || !this.$route.params.mobile || !this.$route.params.code || !this.$route.params.routername || !this.$route.params.target || !this.$route.params.action) {
			this.$emit('overloaderror', '缺少数据')
			this.$router.go(-1)
			return
		}
		if (this.$route.params.code_num) this.code_num = this.$route.params.code_num
		this.title = this.$route.params.title
		this.mobile = this.$route.params.mobile
		this.code = this.$route.params.code
		this.routername = this.$route.params.routername
		this.target = this.$route.params.target
		this.action = this.$route.params.action
	},
	mounted(){
		setTimeout(() => {
			$('#confirm').removeClass('hidden').passwordView({
				cls : 'underline',
				placeholder : '',
				length : this.code_num,
				input : function(){
					$('#confirm').removeClass('error')
				},
				callback : function(){
					if (this.code !== this.confirm) {
						$('#confirm').addClass('error')
						return
					}
					this.$router.push({name:this.routername, params:{
						title: this.title,
						mobile: this.mobile,
						code: this.code,
						target: this.target,
						action: this.action
					}})
				}
			})
		}, 500)
	}
}
</script>

<style scoped>

</style>