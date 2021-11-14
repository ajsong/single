<template>
<div class="member-code">
	<div class="img"><img ref="img" /></div>
	<div class="tip">邀请码：{{ member.invite_code }}</div>
	<div class="memo">邀请好友注册app账号后，该好友成为你下级用户，好友在每次消费后，你可获得佣金。</div>
</div>
</template>

<script>
export default {
	name:'membercode',
	data(){
		return {
			member: null
		}
	},
	created(){
		this.member = this.$.storageJSON('member')
		this.$(document.body).removeClass('gr')
		this.$nextTick(() => {
			this.$(this.$refs.img).caller(this).loadpic('/api/member/code?code=1', '../images/nopic.png')
		})
	},
	beforeDestroy(){
		this.$(document.body).addClass('gr')
	}
}
</script>

<style scoped>

</style>