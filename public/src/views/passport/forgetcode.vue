<template>
<div v-if="action">
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">输入验证码</div>
	</div>

	<div class="forget-index">
		<input type="hidden" id="forget" name="forget" value="forget" />
		<input type="hidden" id="mobile" name="mobile" v-model="mobile" />
		<input type="hidden" id="code" name="code" v-model="code" />
		<input type="tel" id="confirm" v-model="confirm" class="hidden" />
	</div>
</div>
</template>

<script>
export default {
	name:'forgetcode',
	data(){
		return {
			mobile: '',
			code: '',
			target: '',
			action: '',
			code_num: '4',
			confirm: ''
		}
	},
	created(){
		if (!this.$route.params.mobile || !this.$route.params.code || !this.$route.params.target || !this.$route.params.action) {
			this.$emit('overloaderror', '缺少数据')
			return
		}
		if (this.$route.params.code_num) this.code_num = this.$route.params.code_num
		this.mobile = this.$route.params.mobile
		this.code = this.$route.params.code
		this.target = this.$route.params.target
		this.action = this.$route.params.action
	},
	mounted(){
		this.$nextTick(() => {
			$('#confirm').passwordView({
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
					this.$ajax.post(this.action, {forget:'forget', mobile:this.mobile, code:this.code}).then(json => {
						if (!$.checkError(json, this)) return
						this.$router.push({path:this.action})
					})
				}
			})
		})
	}
}
</script>

<style scoped>

</style>