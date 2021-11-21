<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">修改密码</div>
	</div>

	<div class="member-password">
		<ul class="tableView tableView-noMargin tableView-light">
			<li>
				<h1><div><i :class="showClass" @click="togglePass"></i><input type="password" name="origin_password" v-model="origin_password" placeholder="请输入旧密码" v-if="isHidden" /><input type="text" name="origin_password" v-model="origin_password" placeholder="请输入旧密码" v-else /></div></h1>
			</li>
			<li>
				<h1><input type="password" name="new_password" v-model="new_password" placeholder="请输入新密码" v-if="isHidden" /><input type="text" name="new_password" v-model="new_password" placeholder="请输入新密码" v-else /></h1>
			</li>
			<li>
				<h1><input type="password" name="repass" v-model="repass" placeholder="确认新密码" v-if="isHidden" /><input type="text" name="repass" v-model="repass" placeholder="确认新密码" v-else /></h1>
			</li>
		</ul>
		<div class="buttonView">
			<a class="btn pass" href="javascript:void(0)" @click="submit">确定</a>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name:'password',
	data(){
		return {
			isHidden: true,
			showClass: '',
			origin_password: '',
			new_password: '',
			repass: ''
		}
	},
	methods:{
		submit(){
			if(!this.origin_password.length || !this.new_password.length || !this.repass.length){
				this.$emit('overloaderror', '请填写完整')
				return
			}
			if(this.new_password.length !== this.repass){
				this.$emit('overloaderror', '两次新密码不相同')
				return
			}
			this.$ajax.post('/api/member/password', {
				origin_password: this.origin_password,
				new_password: this.new_password,
				repass: this.repass
			}).then(json => {
				if(json.error > 0){
					this.$emit('overloaderror', json.msg)
					return
				}
				this.$router.push('/member/set')
			})
		},
		togglePass(){
			this.isHidden = !this.isHidden
			this.showClass = this.isHidden ? '' : 'x'
		}
	}
}
</script>

<style scoped>

</style>