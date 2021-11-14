<template>
<div class="feedback">
	<ul class="tableView tableView-noMargin tableView-light">
		<li><h1><input type="text" name="name" v-model="name" placeholder="请输入用户昵称" /></h1></li>
		<li><h1><input type="tel" name="mobile" v-model="mobile" placeholder="请输入手机号码" /></h1></li>
		<li><h1><textarea name="content" v-model="content" placeholder="请输入你的反馈信息"></textarea></h1></li>
	</ul>
	<div class="buttonView">
		<a class="btn pass" href="javascript:void(0)" @click="submit">提交</a>
	</div>
</div>
</template>

<script>
export default {
	name:'feedback',
	data(){
		return {
			name: '',
			mobile: '',
			content: ''
		}
	},
	methods:{
		submit(){
			if(!this.content.length){
				this.$emit('overloaderror', '请输入内容')
				return
			}
			this.$ajax.post('/api/other/feedback', {
				name: this.name,
				mobile: this.mobile,
				content: this.content
			}).then(json => {
				if (!this.$.checkError(json, this)) return
				alert('非常感谢您的反馈')
				this.$router.push('/member')
			})
		}
	}
}
</script>

<style scoped>

</style>