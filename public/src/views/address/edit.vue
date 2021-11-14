<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:void(0)" @click="$router.go(-1)"><i class="return"></i></a>
		<div class="titleView-x" v-html="id > 0 ? '编辑收货地址' : '添加收货地址'"></div>
	</div>

	<div class="address-info">
		<ul class="tableView">
			<li>
				<h1>
					<div class="row">
						<span>收货人姓名</span>
						<input type="text" name="contactman" id="contactman" v-model="data.contactman" placeholder="请输入收货人姓名" />
					</div>
				</h1>
			</li>
			<li>
				<h1>
					<div class="row">
						<span>手机号码</span>
						<input type="tel" name="mobile" id="mobile" v-model="data.mobile" placeholder="请输入手机号码" />
					</div>
				</h1>
			</li>
			<li>
				<h1>
					<div class="row">
						<span>所在地区</span>
						<div>
							<v-distpicker :placeholders="placeholders" :province="data.province" :city="data.city" :area="data.district" @selected="selectDist"></v-distpicker>
							<!--隐藏区 hide-area-->
							<!--只显示省 only-province-->
						</div>
					</div>
				</h1>
			</li>
			<li>
				<h1>
					<div class="row">
						<span>地址</span>
						<input type="text" name="address" id="address" v-model="data.address" placeholder="请输入地址" />
					</div>
				</h1>
			</li>
			<!--<li>
				<h1>
					<div class="row">
						<span>身份证号码</span>
						<input type="tel" name="idcard" id="idcard" v-model="data.idcard" maxlength="18" placeholder="请输入身份证号码" />
					</div>
				</h1>
			</li>-->
		</ul>
		<div class="buttonView">
			<a class="btn" href="javascript:void(0)" @click="submit">确定</a>
		</div>
	</div>
</div>
</template>

<script>
//https://distpicker.unie.fun/
import VDistpicker from 'v-distpicker'
import eventBus from '../../plugins/eventBus';
export default {
	name:'edit',
	data(){
		return {
			from: null,
			data: {
				id: 0,
				contactman: '',
				mobile: '',
				province: '',
				city: '',
				district: '',
				address: '',
				idcard: ''
			},
			placeholders: {
				province: '选择省',
				city: '选择市',
				area: '选择区'
			}
		}
	},
	components:{
		VDistpicker
	},
	created(){
		this.from = this.$route.query.from
		this.data.id = this.$route.query.id || 0
		if (this.data.id) {
			this.$ajax.get('/api/address/edit', {id:this.data.id}).then(json => {
				if(!this.$.checkError(json, this))return
				this.data = json.data
			})
		}
	},
	methods:{
		selectDist(data){
			this.data.province = data.province.value
			this.data.city = data.city.value
			this.data.district = data.area.value
		},
		submit(){
			if (!this.data.contactman.length || !this.data.mobile.length || !this.data.address.length || !this.data.province.length || !this.data.city.length || !this.data.district.length) {
				this.$emit('overloaderror', '请填写完整')
				return
			}
			this.$ajax.post('/api/address/edit', this.data).then(json => {
				if(!this.$.checkError(json, this))return
				if (this.from === 'cart') {
					this.data.id = json.data
					eventBus.$emit('changeAddress', this.data)
					this.$router.go(-1)
				} else if (this.data.id > 0) {
					this.$emit('overloadsuccess', '修改成功', 3000, () => {
						this.$router.push('/address')
					})
				} else {
					this.$router.push('/address')
				}
			})
		}
	}
}
</script>

<style scoped>
>>> .address-info .tableView h1 .row div select{font-size:0.12rem; padding:0; width:100%; height:0.44rem; display:block; border:none;}
</style>