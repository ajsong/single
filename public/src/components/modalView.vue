<template>
<div :class="['modalViewLay', 'load-overlay', {'load-overlay-in':!hidden}]" @click="closeModalView">
	<div :class="['modalView', {'modalView-out':hidden}]" :style="{width:(String(width).length?width+'px':'')}" @click.stop="stopPropagation">
		<div><span>{{ title }}</span></div>
		<template v-for="item in element">
			<label :class="{'label':item.type !== 'textarea'}">
				<textarea v-if="item.type === 'textarea'" :name="item.name" :placeholder="item.placeholder" :style="{height:(item.height?item.height+'px':'')}" :readonly="item.readonly" :disabled="item.disabled" v-model="item.value"></textarea>
				<select v-else-if="item.type === 'select'" :name="item.name" :disabled="item.disabled" v-model="item.value">
					<option v-for="v in item.placeholder.split('#')" :value="(v.split('|'))[0]" v-text="(v.split('|'))[1]"></option>
				</select>
				<input v-else :type="item.type" :name="item.name" :placeholder="item.placeholder" :readonly="item.readonly" :disabled="item.disabled" v-model="item.value" />
			</label>
		</template>
		<a class="ge-top ge-light" href="javascript:void(0)" @click="passBtn">{{ btn }}</a>
	</div>
</div>
</template>

<script>
export default {
	name:'modalView',
	props:{
		title: {
			default: ''
		},
		value: {
			default: ''
		},
		callback: {
			default: null
		},
		hidden: {
			default: true
		}
	},
	data(){
		return {
			overlayClass: '',
			modalViewClass: '',
			btn: '确定',
			element: [],
			width: ''
		}
	},
	mounted(){
		this.$nextTick(() => {
			this.updatedInterface()
		})
	},
	methods:{
		updatedInterface(){
			this.btn = '确定'
			this.element = []
			this.width = ''
			if(this.$.isFunction(this.value)){
				this.callback = this.value
				this.value = ''
			}
			if(this.$.isPlainObject(this.value)){
				if(typeof this.value.width === 'undefined'){
					this.value = [this.value]
				}else{
					this.width = this.value.width //弹框宽度
					this.value = this.value.item
					if(this.$.isPlainObject(this.value))this.value = [this.value]
				}
			}
			if(this.$.isArray(this.value)){
				this.value.forEach((item, i) => {
					if(typeof item.title === 'undefined')item.title = 'input'+(i+1)
					if(typeof item.name === 'undefined')item.name = ''
					if(typeof item.value === 'undefined')item.value = ''
					if(item.name === 'password')item.type = 'password'
					if(typeof item.readonly === 'undefined')item.readonly = false
					if(typeof item.disabled === 'undefined')item.disabled = false
					if(typeof item.type === 'undefined' || !item.type.length)item.type = 'text'
					if(typeof item.placeholder === 'undefined' || !item.placeholder.length)item.placeholder = '请输入'+item.title
					if(typeof item.height === 'undefined')item.height = ''
					if(typeof item.btn !== 'undefined' && typeof item.btn === 'string' && item.btn.length)this.btn = item.btn
					this.element.push(item)
				})
			}else{
				this.element.push({ title:this.title, type:'text', name:'', value:this.value, placeholder:'请输入'+this.title })
			}
		},
		stopPropagation(){
			//子元素绑定一个阻止冒泡的点击事件 @click.stop
		},
		closeModalView(){
			this.$emit('modalview', false)
		},
		passBtn(){
			if(this.$.isFunction(this.callback)){
				let data = ''
				if(this.element.length === 1){
					data = this.value
				}else{
					data = [];
					this.element.forEach(item => {
						if(item.name.length)data.push({title:item.title, name:item.name, value:item.value})
					})
				}
				this.callback.call(this, data)
			}else{
				this.closeModalView()
			}
		}
	}
}
</script>

<style scoped>
.modalViewLay{position:fixed; left:0; top:0; background-color:rgba(0,0,0,0); opacity:1; -webkit-transition:background-color 200ms ease-out; transition:background-color 200ms ease-out;}
.load-overlay-in{background-color:rgba(0,0,0,0.6);}
.modalView{position:absolute; left:50%; top:50%; width:2.8rem; height:auto; overflow:hidden; text-align:center; background:#fff; border-radius:0.05rem; opacity:1; -webkit-transform:translate(-50%,-50%); transform:translate(-50%,-50%); -webkit-transition:opacity 200ms ease-out; transition:opacity 200ms ease-out;}
.modalView-out{opacity:0;}
.modalView div{height:0.3rem; line-height:0.3rem;}
.modalView div span{display:block; height:100%; color:#bbb; font-size:0.12rem; -webkit-transform:scale(0.91); transform:scale(0.91);}
.modalView label{display:block; margin:0 0.15rem; min-height:0.36rem; position:relative;}
.modalView label.label{height:0.36rem;}
.modalView label:nth-child(2){margin-top:0.1rem;}
.modalView label:not(:nth-child(2)):before{content:""; display:block; position:absolute; z-index:99; background:#eaeaea; top:0; left:0; right:0; height:0.01rem; -webkit-transform:scaleY(0.5); transform:scaleY(0.5); -webkit-transform-origin:top; transform-origin:top;}
.modalView label input, .modalView label textarea{width:100%; height:100%; font-size:0.14rem; padding:0; margin:0; background-color:transparent; border:none; text-align:left; resize:none;}
.modalView a{display:block; height:0.44rem; line-height:0.44rem; margin-top:0.1rem; font-size:0.14rem; color:#007aff; font-weight:500; text-decoration:none;}
</style>