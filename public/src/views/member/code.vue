<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">我的二维码</div>
	</div>

	<div class="member-code">
		<div class="img"><img ref="img" /></div>
		<div class="tip">邀请码：{{ member.invite_code }}</div>
		<div class="memo">邀请好友注册app账号后，该好友成为你下级用户，好友在每次消费后，你可获得佣金。</div>
	</div>
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
		$(document.body).removeClass('gr')
		this.member = $.storageJSON('member')
		this.$nextTick(() => {
			$(this.$refs.img).component(this).loadpic('/api/member/code?code=1', '../images/nopic.png')
		})
	},
	beforeDestroy(){
		$(document.body).addClass('gr')
	}
}
</script>

<style scoped>

</style>