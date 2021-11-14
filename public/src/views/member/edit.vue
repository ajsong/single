<template>
<div>
	<div class="member-edit">
		<ul class="tableView tableView-light tableView-noMargin">
			<li>
				<a ref="avatar" class="avatar" href="javascript:void(0)">
					<h1><big><div :style="{'background-image':(data.avatar && data.avatar.length)?'url('+data.avatar+')':''}"></div></big>头像</h1>
				</a>
			</li>
			<li>
				<a href="javascript:void(0)" @click="changeName">
					<h1><big v-html="data.nick_name"></big>昵称</h1>
				</a>
			</li>
			<li>
				<h1><big>{{ data.sex }}</big>性别
				<select v-model="data.sex" @change="changeSex">
				<option value=""></option>
				<option value="男">男</option>
				<option value="女">女</option>
				</select></h1>
			</li>
			<li>
				<h1><big v-html="date"></big>生日
				<input type="date" id="date" v-model="date" @change="changeDate" /></h1>
			</li>
		</ul>
	</div>
	<modal-view v-if="showModalView" :title="modalViewTitle" :value="modalViewValue" :callback="modalViewCallback" :hidden="modalViewHidden" v-on:modalview="modalView"></modal-view>
</div>
</template>

<script>
import modalView from '../../components/modalView'
export default {
	name:'edit',
	data(){
		return {
			data: null,
			date: '',

			showModalView: false,
			modalViewTitle: '',
			modalViewValue: '',
			modalViewCallback: null,
			modalViewCloseCallback: null,
			modalViewHidden: true
		}
	},
	components:{
		modalView
	},
	created(){
		this.data = JSON.parse(this.$.storage('member'))
		if(parseInt(this.data.birth_year)>0)this.date = this.data.birth_year + '-' + this.data.birth_month + '-' + this.data.birth_day
	},
	mounted(){
		this.$nextTick(() => {
			this.$(this.$refs.avatar).caller(this).ajaxupload({
				url: '/api/member/avatar',
				name: 'avatar',
				before: () => this.$emit('overload'),
				success: json => {
					if(!this.$.checkError(json, this))return
					this.data.avatar = json.data
					this.$.storage('member', this.data)
					setTimeout(() => this.$emit('overload', false), 1000)
				}
			})
		})
	},
	methods:{
		modalView(title, value, callback, closeCallback){
			if(typeof title === 'boolean'){
				this.closeModalView()
				return
			}
			if(this.showModalView)return
			this.showModalView = true
			this.modalViewTitle = title || ''
			this.modalViewValue = value
			this.modalViewCallback = callback || null
			this.modalViewCloseCallback = closeCallback || null
			setTimeout(() => this.modalViewHidden = false, 0)
		},
		closeModalView: function(){
			this.modalViewHidden = true
			setTimeout(() => {
				this.showModalView = false
				if(this.$.isFunction(this.modalViewCloseCallback))this.modalViewCloseCallback()
			}, 300)
		},
		changeAvatar(e){

		},
		changeName(){
			this.modalView('昵称', this.data.nick_name, (value) => {
				if(!value.length){
					this.$emit('overloaderror', '不能为空')
					return
				}
				this.$ajax.post('/api/member/edit', { nick_name:value }).then(json => {
					this.$.storage('member', json.data)
					this.modalView(false);
				})
			});
		},
		changeSex(){
			if(!this.data.sex.length)return
			this.$ajax.post('/api/member/edit', {
				sex: this.data.sex
			}).then(json => {
				if (!this.$.checkError(json, this)) return
				this.$.storage('member', json.data)
			})
		},
		changeDate(){
			let arr = this.date.split('-');
			this.$ajax.post('/api/member/edit', {
				birth_year: arr[0],
				birth_month: arr[1],
				birth_day: arr[2]
			}).then(json => {
				if (!this.$.checkError(json, this)) return
				this.$.storage('member', json.data)
			})
		}
	}
}
</script>

<style scoped>

</style>