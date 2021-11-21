<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">已邀请用户</div>
	</div>
	
	<div class="commission-invite">
		<ul v-if="data">
			<li v-for="g in data">
				<span class="scale9-right">{{ g.reg_time }}</span>
				<div :style="{'background-image':'url('+g.avatar+')'}"></div>
				<font><template v-if="g.name">{{ g.name }}</template><template v-else>{{ g.nick_name }}</template></font>
			</li>
		</ul>
		<div v-else class="norecord">还没邀请过任何人</div>
	</div>
</div>
</template>

<script>
export default {
	name:'myInviteUser',
	data(){
		return {
			data:null
		}
	},
	created(){
		this.$ajax.get('/api/member/my_invite_user').then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
		})
	}
}
</script>

<style scoped>

</style>