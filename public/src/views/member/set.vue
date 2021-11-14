<template>
<div class="member-set">
	<section>
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<router-link to="/member/edit"><h1>个人资料</h1></router-link>
			</li>
			<li>
				<router-link to="/member/password"><h1>修改密码</h1></router-link>
			</li>
		</ul>
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<router-link to="/address"><h1>收货地址管理</h1></router-link>
			</li>
		</ul>
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<router-link to="/feedback"><h1>我要反馈</h1></router-link>
			</li>
		</ul>
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<router-link to="/article/detail?id=help"><h1>帮助中心</h1></router-link>
			</li>
			<!--
			<li>
				<a href="tel://020-36603191"><h1 class="noPush"><big>020-36603191</big>客服电话</h1></a>
			</li>
			-->
			<li>
				<router-link to="/article/detail?id=about"><h1>关于我们</h1></router-link>
			</li>
			<!--
			<li class="r">
				<router-link to="/article/detail?id=join"><h1>招商加盟</h1></a>
			</li>
			-->
			<li class="r">
				<a href="javascript:void(0)" @click="logout"><h1 class="noPush">退出登录</h1></a>
			</li>
		</ul>
	</section>
</div>
</template>

<script>
export default {
	name:'set',
	methods:{
		logout(){
			this.$.get('/api/passport/logout').then(() => {
				this.$.storage('member', null)
				this.$router.push('/')
			})
		}
	}
}
</script>

<style scoped>

</style>