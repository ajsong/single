<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">我是商家</div>
	</div>

	<div class="business-index main-padding-top">
		<div class="topView">
			<i></i>
			<span>累计收入</span>
			<span class="money">￥{{ member.commission }}</span>
		</div>

		<section>
			<ul class="tableView tableView-light ge-top ge-light">
				<!--<li><router-link href="#"><h1><em class="ico1"></em>店铺信息</h1></router-link></li>-->
				<!--<li><router-link href="#"><h1><em class="ico2"></em>商品信息</h1></router-link></li>-->
				<li><router-link to="/member/code"><h1><em class="ico3"></em>邀请用户</h1></router-link></li>
				<li><router-link to="/member/money"><h1><em class="ico4"></em>我的钱包</h1></router-link></li>
			</ul>
		</section>

		<!--<div class="nobusiness">
			<span>您还不是商户，去升级为商户吧～</span>
			<div class="buttonView">
				<a href="javascript:void(0)" class="btn">我要合作</a>
			</div>
		</div>-->
	</div>
</div>
</template>

<script>
export default {
	name:'business',
	data(){
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