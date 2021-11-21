<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">提现密码管理</div>
	</div>

	<div class="bank-set">
		<ul class="tableView tableView-noMargin">
			<li v-if="!member.withdraw_password">
				<router-link to="/bank/password_set">
					<h1><span>设置提现密码</span></h1>
				</router-link>
			</li>
			<template v-else>
			<li>
				<router-link to="/bank/password_edit">
					<h1><span>修改提现密码</span></h1>
				</router-link>
			</li>
			<li>
				<router-link :to="{path:'/passport/forget', query:{title:'提现密码', target:'/bank/set', action:'/api/bank/password_set'}}">
					<h1><span>忘记提现密码</span></h1>
				</router-link>
			</li>
			</template>
		</ul>
	</div>
</div>
</template>

<script>
export default {
	name: 'bankSet',
	data() {
		return {
			member: null
		}
	},
	created(){
		this.member = $.storageJSON('member')
	}
}
</script>

<style scoped>

</style>