<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">银行卡信息</div>
	</div>

	<div class="bank-edit">
		<ul class="tableView tableView-noMargin">
			<li>
				<h1><input type="text" id="bank_name" v-model="bank_name" placeholder="银行名称，如中国银行、工商银行" /></h1>
			</li>
			<li>
				<h1><input type="text" id="address" v-model="address" placeholder="开户地区，输入省市，如广东广州" /></h1>
			</li>
			<li>
				<h1><input type="text" id="bank_branch" v-model="bank_branch" placeholder="支行名称，如深圳支行高新支行" /></h1>
			</li>
			<li>
				<h1><input type="text" id="name" v-model="name" placeholder="账户姓名，输入银行开户人姓名" /></h1>
			</li>
			<li>
				<h1><input type="tel" id="bank_card" v-model="bank_card" placeholder="银行卡号" /></h1>
			</li>
		</ul>
		<!--<span>温馨提示：银行卡信息一般不可更改，如需更改请联系客服</span>-->
	</div>

	<div class="submitView">
		<a href="javascript:void(0)" class="btn" @click="submit">提交</a>
	</div>
</div>
</template>

<script>
export default {
	name: 'bankEdit',
	data() {
		return {
			id: '',
			bank_name: '',
			address: '',
			bank_branch: '',
			name: '',
			bank_card: '',
		}
	},
	created(){
		if (this.$route.query.id) this.id = this.$route.query.id
		if (this.id) {
			this.$ajax.get('/api/bank/edit', {id:this.id}).then(json => {
				if (!$.checkError(json, this)) return
				this.bank_name = json.data.bank_name
				this.address = json.data.address
				this.bank_branch = json.data.bank_branch
				this.name = json.data.name
				this.bank_card = json.data.bank_card
			})
		}
	},
	methods: {
		submit(){
			if (!this.bank_name.length) {
				$.overloadError('请输入银行名称')
				return
			}
			if (!this.address.length) {
				$.overloadError('请输入开户地区')
				return
			}
			if (!this.bank_branch.length) {
				$.overloadError('请输入支行名称')
				return
			}
			if (!this.name.length) {
				$.overloadError('请输入账户姓名')
				return
			}
			if (!this.bank_card.length) {
				$.overloadError('请输入银行卡号')
				return
			}
			this.$ajax.post('/api/bank/edit', {
				id:this.id, bank_name:this.bank_name, address:this.address, bank_branch:this.bank_branch, name:this.name, bank_card:this.bank_card
			}).then(json => {
				if (!$.checkError(json, this)) return
				$.overloadSuccess('提交成功', 3000, () => {
					this.$router.go(-1)
				})
			})
		}
	}
}
</script>

<style scoped>

</style>