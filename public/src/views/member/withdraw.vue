<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">申请提现</div>
	</div>

	<div class="withdraw-index" v-if="data">
		<div class="view">
			<router-link to="/bank?from=withdraw" class="bank push-ico"><span>请选择提现账户</span>提现银行卡</router-link>
			<div class="title">提现金额</div>
			<div class="money ge-bottom ge-light"><span>￥</span><input type="number" v-model="withdraw_money" id="withdraw_money" /></div>
			<!--onkeyup="value=(parseInt((value=value.replace(/\D/g,''))==''||parseInt((value=value.replace(/\D/g,''))==0)?'{$data.min_money}':value,10))" onafterpaste="value=(parseInt((value=value.replace(/\D/g,''))==''||parseInt((value=value.replace(/\D/g,''))==0)?'{$data.min_money}':value,10))"-->
			<div class="tip"><span>可提现金额￥{{ data.money|round }}，<a href="javascript:void(0)" @click="getAll">全部提现</a></span></div>
			<a class="btn" href="javascript:void(0)" @click="submit">提现</a>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name:'withdraw',
	data(){
		return {
			data: null,
			bank_id: '',
			withdraw_money: ''
		}
	},
	created(){
		$.paramAlive(this, '/bank').then(data => {
			this.bank_id = data.id
			$('.withdraw-index .bank span').addClass('x').html(data.bank_name+' 尾号'+data.bank_card.substr(-4)+' '+data.name)
		}).catch(() => {
			this.$ajax.get('/api/withdraw').then(json => {
				if (!$.checkError(json, this)) return
				this.data = json.data
			})
		})
	},
	methods:{
		getAll(){
			this.withdraw_money = this.data.money
		},
		submit(){
			if(!this.withdraw_money.length){
				$.overloadError('请输入提现金额');
				return;
			}
			if (Number(this.withdraw_money) <= 0) {
				$.overloadError('提现金额不合法');
				return;
			}
			if (Number(this.withdraw_money) > Number(this.data.money)) {
				$.overloadError('输入金额超过可提现金额');
				return;
			}
			if(!this.bank_id.length){
				$.overloadError('请选择提现账户');
				return;
			}
			this.$ajax.post('/api/withdraw/apply', {bank_id:this.bank_id, withdraw_money:this.withdraw_money}).then(json => {
				if (!$.checkError(json, this)) return
				this.data.money -= Number(this.withdraw_money)
				$.paramAlive(this.$route.path, null)
				$.overloadSuccess(this.data.transfers_appid?'提现成功':'提交成功，我们将会尽快审核', 4000, () => {
					this.$router.push('/member/withdraw_history')
				})
			})
		}
	}
}
</script>

<style scoped>

</style>