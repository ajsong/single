<template>
<ul class="switchView" v-if="data.length">
	<div ref="wrap">
		<li v-for="(item, i) in data" :ref="'tab' + (i + 1)" :class="{'this':curIndex === i}">
			<a href="javascript:void(0)" :class="item.className" @click="selectIndex(i)">{{ item.title }}</a>
		</li>
		<div ref="column" v-if="column.length" :class="['switchColumn', column]"></div>
	</div>
</ul>
</template>

<script>
export default {
	name:'switchView',
	props:{
		index:{
			type: Number,
			default: 0
		},
		data:{
			type: Array,
			default: []
			//{title:'选项', className:'', selected:()=>FunctionCode} selected返回false不移动column
		},
		column:{//
			type: String,
			default: ''
		}
	},
	data(){
		return {
			curIndex: 0
		}
	},
	watch:{
		index(){
			this.curIndex = this.index
		},
		curIndex(){
			this.setColumn()
		}
	},
	mounted(){
		this.$nextTick(() => {
			this.setWidth()
			this.setColumn()
		})
	},
	methods:{
		setWidth(){
			let wrapWidth = this.$refs.wrap.clientWidth
			let totalWidth = 0
			this.data.forEach((item, i) => totalWidth += this.$refs['tab' + (i + 1)][0].clientWidth)
			if (wrapWidth >= totalWidth) {
				let percent = 100 / this.data.length
				this.data.forEach((item, i) => {
					let ref = this.$refs['tab' + (i + 1)]
					ref[0].style.width = percent + '%'
				})
			}
		},
		setColumn(){
			let totalLeft = 0
			this.data.forEach((item, i) => {
				if (i >= this.curIndex) return
				totalLeft += this.$refs['tab' + (i + 1)][0].clientWidth
			})
			let width = this.$refs['tab' + (this.curIndex + 1)][0].clientWidth
			let column = this.$refs.column
			column.style.width = width + 'px'
			column.style.left = totalLeft + 'px'
		},
		selectIndex(index){
			if (this.data.length <= index) return
			let item = this.data[index]
			let moveColumn = true
			if (item.selected && this.$.isFunction(item.selected)) {
				let result = item.selected(index, this.$refs['tab' + (index + 1)])
				if (typeof result === 'boolean') moveColumn = result
			}
			if (moveColumn) this.curIndex = index
		}
	}
}
</script>

<style scoped>
.switchView > div{position:relative; width:100%; height:100%; overflow:auto; overflow-y:hidden; white-space:nowrap;}
.switchView > div > li{float:none; display:inline-block; box-sizing:border-box; white-space:nowrap;}
.switchView > div > li > a{white-space:nowrap;}
.switchColumn{position:absolute; left:0; bottom:0; z-index:1; width:0; transform:translate3d(0,0,0); -webkit-transform:translate3d(0,0,0); -webkit-transition:all 0.2s ease-out; transition:all 0.2s ease-out;}
</style>