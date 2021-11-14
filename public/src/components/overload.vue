<template>
<div ref="view" class="load-view" :class="viewClass" :style="viewStyle">
	<div ref="div" :class="imageClass" :style="imageStyle" v-if="imageShow"></div>
	<span ref="span" :class="textClass" :style="textStyle" v-if="textShow" v-text="text"></span>
</div>
</template>

<script>
export default {
	name:'overload',
	props:{
		text: {
			default: ''
		},
		image: {
			default: ''
		},
		auto: {
			default: 0
		},
		callback: {
			default: null
		},
		hidden: {
			default: false
		}
	},
	data(){
		return {
			timer: null,
			newAuto: 0,
			viewClass: '',
			viewStyle: '',
			imageShow: true,
			imageClass: '',
			imageStyle: '',
			textShow: true,
			textClass: '',
			textStyle: ''
		}
	},
	watch: {
		hidden: function(){
			if(this.hidden){
				this.viewClass = 'load-view-out'
				if(this.$.isFunction(this.callback))this.callback()
			}
		},
		text: function(newVal, oldVal){
			if(newVal !== oldVal)this.newAuto = 0
			let view = this.$refs.view
			let div = this.$refs.div
			if(!this.text){
				this.imageStyle = this.$.extend(this.imageStyle, {'margin-top':((view.clientHeight - div.clientHeight) / 2) + 'px'})
				this.textShow = false
			}else{
				this.imageStyle = this.$.extend(this.imageStyle, {'margin-top':'0.2rem'})
				this.textShow = true
			}
		},
		image: function(newVal, oldVal){
			if(newVal !== oldVal)this.newAuto = 0
			let view = this.$refs.view
			let span = this.$refs.span
			if(!this.image){
				this.imageShow = false
				this.textClass = 'text'
				this.textStyle = {'margin-top':((view.clientHeight - span.clientHeight) / 2) + 'px'}
			}else{
				this.imageShow = true
				if(this.image.substr(0, 1) === '.'){
					this.imageClass = this.image.substr(1)
				}else{
					this.imageStyle = {width:'0.35rem', height:'0.35rem', 'background-image':'url('+this.image+')'}
				}
			}
		},
		auto: function(){
			this.newAuto = this.auto
		},
		newAuto: function(){
			clearTimeout(this.timer)
			if(this.auto){
				this.timer = setTimeout(() => {
					this.$emit('overload', false)
				}, this.auto)
			}
		}
	},
	mounted(){
		this.$nextTick(() => {
			this.updatedInterface()
		})
	},
	methods: {
		updatedInterface(){
			let view = this.$refs.view
			let div = this.$refs.div
			let span = this.$refs.span
			let width = document.documentElement.clientWidth * 0.8
			if(view.clientWidth > width)this.viewStyle = {'min-width':width + 'px', 'max-width':width + 'px'}
			if(!this.image){
				this.imageShow = false
				this.textClass = 'text'
				this.textStyle = {'margin-top':((view.clientHeight - span.clientHeight) / 2) + 'px'}
			}else{
				if(this.image.substr(0, 1) === '.'){
					this.imageClass = this.image.substr(1)
				}else{
					this.imageStyle = {width:'0.35rem', height:'0.35rem', 'background-image':'url('+this.image+')'}
				}
			}
			if(!this.text){
				this.imageStyle = this.$.extend(this.imageStyle, {'margin-top':((view.clientHeight - div.clientHeight) / 2) + 'px'})
				this.textShow = false
			}else{
				this.imageStyle = this.$.extend(this.imageStyle, {'margin-top':'0.2rem'})
			}
			setTimeout(() => this.viewClass = 'load-view-in', 100)
			if(this.auto){
				this.timer = setTimeout(() => {
					this.$emit('overload', false)
				}, this.auto)
			}
		}
	}
}
</script>

<style scoped>
.load-view{min-width:0.8rem; min-height:0.8rem; border-radius:0.08rem; background:rgba(0,0,0,0.7); -webkit-backdrop-filter:blur(15px); backdrop-filter:blur(15px);}
.load-view div{margin:0 auto; width:0.3rem; height:0.3rem;}
.load-view span{line-height:0.18rem; font-size:0.13rem; padding:0.1rem;}
</style>