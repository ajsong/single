<template>
<div ref="scroller" class="pullRefresh">
	<div ref="wraper" class="pull-wrap">
		<div ref="header" class="pull-header" v-if="refreshParams.callback">
			<div class="pull-preloader" v-if="simple"></div>
			<div class="pull-preloader-white" v-else-if="simpleWhite"></div>
			<div class="pull-message pull-refresh-message" v-else>
				<div :class="['pull-icon', 'pull-refresh-icon', {'pull-icon-rotate':iconRotate}]" v-if="!loading"></div>
				<div class="pull-icon pull-icon-loading pull-refresh-loading" v-else></div>
				<div class="pull-text-wrap">
					<div class="pull-text pull-refresh-text">{{ refreshText }}</div>
					<div class="pull-time pull-refresh-time">{{ refreshTime }}</div>
				</div>
			</div>
		</div>

		<slot></slot>

		<div ref="footer" class="pull-footer" v-if="isFooterCreated && loadmoreParams.callback">
			<div class="pull-preloader" v-if="simple"></div>
			<div class="pull-preloader-white" v-else-if="simpleWhite"></div>
			<template v-else>
			<div class="pull-message pull-loadmore-message" v-if="!noMoreData">
				<div class="pull-icon pull-loadmore-icon" v-if="!loading"></div>
				<div class="pull-icon pull-icon-loading pull-loadmore-loading" v-else></div>
				<div class="pull-text-wrap">
					<div class="pull-text pull-loadmore-text">{{ loadmoreText }}</div>
					<div class="pull-time pull-loadmore-time">{{ loadmoreTime }}</div>
				</div>
			</div>
			<div class="pull-nomore" v-else>{{ loadmoreParams.noMoreText }}</div>
			</template>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name:'pullRefresh',
	props:{
		init: { //初始化,一般作为导出scroller实例用
			type: Function,
			default: null
		},
		refresh: { //下拉刷新参数,不设置即不能下拉
			type: Object,
			default() {
				return {}
			}
		},
		loadmore: { //加载更多参数,滚动到底部自动加载,不设置即不能加载更多
			type: Object,
			default() {
				return {}
			}
		},
		marginTop: { //与页面顶部距离,单位px,需要计算好与rem的对应数值
			type: Number,
			default: 0
		},
		simple: { //使用简单加载动画,只有一个灰色菊花
			type: Boolean,
			default: false
		},
		simpleWhite: { //使用简单加载动画,只有一个白色菊花
			type: Boolean,
			default: false
		}
	},
	data(){
		return {
			scroller: null,
			wraper: null,
			pagenum: 0, //当前页
			refreshParams: {
				callback: null, //下拉刷新回调,两参数 ({num:当前页加1, size:每页数量}, scroller实例)
				text: '下拉可以刷新', //顶部默认文字
				tipText: '松开立即刷新', //顶部下拉时的提示文字
				updatingText: '正在刷新中...', //顶部加载中的文字
				updatedTime: '最后更新 %t', //顶部更新时间, %t将替换为当前时间
				autoLoad: false //组件创建完成后立即调用顶部callback
			},
			loadmoreParams: {
				callback: null, //加载更多回调,两参数 ({num:当前页加1, size:每页数量}, scroller实例)
				text: '上拉加载更多', //底部默认文字
				updatingText: '正在加载中...', //底部加载中的文字
				updatedTime: '最后加载 %t', //底部更新时间, %t将替换为当前时间
				noMoreText: '- END -', //加载后滚动条高度没有变化时显示的文字, 为空即不显示
				size: 12 //每页数量
			},

			isFooterCreated: false,
			prevScrollHeight: 0,
			isRefresh: false,
			refreshHeight: 0,
			loadmoreHeight: 0,
			refreshText: '',
			loadmoreText: '',
			refreshTime: '',
			loadmoreTime: '',
			iconRotate: false,
			loading: false,
			noMoreData: false,

			prepareLoad: false,
			distance: 0,
			start: 0,
			transformY: 0
		}
	},
	created(){
		if (this.init) {
			this.init(this)
		}
		if (this.refresh && typeof this.refresh === 'object' && Object.prototype.toString.call(this.refresh).toLowerCase() === '[object object]') {
			for (let key in this.refresh) {
				this.refreshParams[key] = this.refresh[key]
			}
		}
		if (this.loadmore && typeof this.loadmore === 'object' && Object.prototype.toString.call(this.loadmore).toLowerCase() === '[object object]') {
			for (let key in this.loadmore) {
				this.loadmoreParams[key] = this.loadmore[key]
			}
		}
	},
	mounted(){
		this.$nextTick(() => {
			this.scroller = this.$refs.scroller
			this.wraper = this.$refs.wraper
			this.scroller.style.height = (window.screen.height - this.marginTop) + 'px'
			this.refreshText = this.refreshParams.text
			this.loadmoreText = this.loadmoreParams.text
			this.prevScrollHeight = this.scrollHeight()
			this.scroller.addEventListener('scroll', this.scroll, { passive: false })
			if (this.scrollTop() === 0) this.scroll()
			if (this.refreshParams.callback) {
				this.refreshHeight = this.$refs.header.clientHeight
				if (this.refreshParams.autoLoad) this.refreshBegin()
			} else if (this.loadmoreParams.callback) {
				setTimeout(() => {
					this.isFooterCreated = this.scrollHeight() > 0
					if (this.isFooterCreated) setTimeout(() => this.loadmoreHeight = this.$refs.footer.clientHeight, 100)
				}, 100)
			}
		})
	},
	beforeDestroy(){
		this.unbind()
	},
	methods:{
		setPageNum(num){
			this.pagenum = num
		},
		setPageSize(num){
			this.loadmoreParams.size = num
		},
		scrollTop(){
			return this.scroller.scrollTop
		},
		scrollHeight(){
			return this.scroller.scrollHeight - this.scroller.clientHeight
		},
		transform(){
			const currentStyle = (el) => {return el.currentStyle || document.defaultView.getComputedStyle(el, null)}
			let transform = currentStyle(this.wraper)['transform']
			let x = 0, y = 0
			if (transform !== 'none') {
				let matcher = transform.match(/matrix\(-?\d+[.\d+]*, -?\d+[.\d+]*, -?\d+[.\d+]*, -?\d+[.\d+]*, (-?\d+(?:\.\d+)?), (-?\d+(?:\.\d+)?)\)/)
				if (matcher instanceof Array) {
					x = Number(matcher[1])
					y = Number(matcher[2])
				}
			}
			return { x: x, y: y }
		},
		setStyle(arg){
			for (let key in arg) {
				let prop = key.replace(/-([a-z])/ig, (d, m) => {return m.toUpperCase()})
				this.wraper.style[prop] = arg[key]
			}
		},
		scroll(){
			if (this.scrollTop() <= 0) {
				if (!this.loading && this.refreshParams.callback) {
					this.scroller.addEventListener('mousedown', this.startDrag, { passive: false })
					this.scroller.addEventListener('touchstart', this.startDrag, { passive: false })
				}
			} else {
				this.unbind()
				if (this.isFooterCreated && !this.noMoreData && !this.loading && this.loadmoreParams.callback && this.scrollTop() >= this.scrollHeight()) {
					this.loadmoreBegin()
				}
			}
		},
		startDrag(e){ //顶部下拉监听
			this.prepareLoad = false
			this.distance = 0
			this.start = e.touches ? e.touches[0].pageY : e.pageY
			this.transformY = this.transform().y
			this.scroller.addEventListener('mousemove', this.moveDrag, { passive: false })
			this.scroller.addEventListener('mouseup', this.endDrag, { passive: false })
			this.scroller.addEventListener('touchmove', this.moveDrag, { passive: false })
			this.scroller.addEventListener('touchend', this.endDrag, { passive: false })
		},
		moveDrag(e){
			e.preventDefault()
			if (this.start === 0) this.start = e.touches ? e.touches[0].pageY : e.pageY
			let current = e.touches ? e.touches[0].pageY : e.pageY
			this.distance = (this.transformY + (current - this.start)) * 0.3
			if (this.distance > 0) {
				this.setStyle({
					transform:'translate3d(0,'+this.distance+'px,0)', '-webkit-transform':'translate3d(0,'+this.distance+'px,0)',
					'transition-duration':'0s', '-webkit-transition-duration':'0s'
				})
				if (this.distance >= this.refreshHeight) {
					this.refreshText = this.refreshParams.tipText
					this.iconRotate = true
					this.prepareLoad = true
				} else {
					this.refreshText = this.refreshParams.text
					this.iconRotate = false
					this.prepareLoad = false
				}
				return
			}
			this.restore()
		},
		endDrag(){
			if (this.prepareLoad) {
				this.isRefresh = true
				this.refreshText = this.refreshParams.updatingText
				this.loading = true
				this.unbind()
				this.refreshBegin()
				return
			}
			this.end(true)
		},
		restore(){
			this.end(true)
			this.unbind()
		},
		unbind(){
			this.scroller.removeEventListener('mousedown', this.startDrag, false)
			this.scroller.removeEventListener('touchstart', this.startDrag, false)
			this.scroller.removeEventListener('mousemove', this.moveDrag, { passive: false })
			this.scroller.removeEventListener('mouseup', this.endDrag, { passive: false })
			this.scroller.removeEventListener('touchmove', this.moveDrag, { passive: false })
			this.scroller.removeEventListener('touchend', this.endDrag, { passive: false })
		},
		refreshBegin(){ //开始刷新动作
			if (!this.refreshParams.callback) return
			this.setStyle({
				transform:'translate3d(0,'+this.refreshHeight+'px,0)', '-webkit-transform':'translate3d(0,'+this.refreshHeight+'px,0)',
				'transition-duration':'', '-webkit-transition-duration':''
			})
			this.refreshNow()
		},
		refreshNow(){ //立刻刷新无动画
			if (!this.refreshParams.callback) return
			this.pagenum = 1
			this.refreshParams.callback({num:this.pagenum, size:this.loadmoreParams.size}, this)
		},
		loadmoreBegin(){ //开始加载动作
			if (!this.loadmoreParams.callback) return
			this.loading = true
			this.pagenum++
			this.loadmoreParams.callback({num:this.pagenum, size:this.loadmoreParams.size}, this)
		},
		end(notUpdateTime){ //结束动作
			this.setStyle({transform:'translate3d(0,0,0)', '-webkit-transform':'translate3d(0,0,0)', 'transition-duration':'', '-webkit-transition-duration':''})
			this.refreshText = this.refreshParams.text
			this.iconRotate = false
			setTimeout(() => {
				this.loading = false
				this.scroll()
				this.noMoreData = this.scrollHeight() <= 0 || this.prevScrollHeight === this.scrollHeight()
				if (this.loadmoreParams.callback) {
					this.isFooterCreated = this.scrollHeight() > 0
					if (this.isFooterCreated) setTimeout(() => this.loadmoreHeight = this.$refs.footer.clientHeight, 100)
				}
				if (!notUpdateTime) {
					let getNow = () => {
						let fillZero = (num, prec) => {
							for (let i = 0; i < prec; i++)num = '0' + '' + num
							return num.substr(-prec)
						}
						let now = new Date()
						return now.getFullYear() + '-' + fillZero(now.getMonth()+1, 2) + '-' + fillZero(now.getDate(), 2) + ' ' +
							fillZero(now.getHours(), 2) + ':' + fillZero(now.getMinutes(), 2) + ':' + fillZero(now.getSeconds(), 2)
					}
					if (this.isRefresh) {
						this.refreshTime = this.refreshParams.updatedTime.replace(/%t/ig, getNow())
					} else if (!this.noMoreData) {
						this.loadmoreTime = this.loadmoreParams.updatedTime.replace(/%t/ig, getNow())
						if (this.prevScrollHeight === this.scrollHeight()) {
							this.noMoreData = true
						} else {
							this.prevScrollHeight = this.scrollHeight()
						}
					}
				}
			}, 300)
		}
	}
}
</script>

<style scoped>
.pullRefresh{position:relative; top:auto; width:100%; height:auto; overflow:auto; overflow-x:hidden; -webkit-overflow-scrolling:touch;}
.pullRefresh .pull-wrap{position:relative; box-sizing:border-box; -webkit-transform:translateY(0); transform:translateY(0); -webkit-transition:-webkit-transform 300ms ease-out,padding 300ms ease-out; transition:transform 300ms ease-out,padding 300ms ease-out;}
.pullRefresh .pull-header, .pullRefresh .pull-footer{position:relative; width:100%; height:0.64rem; overflow:hidden;}
.pullRefresh .pull-header{margin-top:-0.64rem;}
.pullRefresh .pull-message{margin:0.14rem auto; width:2.02rem; height:0.36rem;}
.pullRefresh .pull-icon{float:left; margin-top:0.07rem; width:0.22rem; height:0.22rem; -webkit-transform:rotate(0deg); transform:rotate(0deg); -webkit-transition:-webkit-transform 200ms ease-out; transition:transform 200ms ease-out; background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAYAAAAehFoBAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6ODU3QTAxNUE2RDRFMTFFMzk5RkE4NDNFMUIxNzE3MDAiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6ODU3QTAxNUI2RDRFMTFFMzk5RkE4NDNFMUIxNzE3MDAiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo4NTdBMDE1ODZENEUxMUUzOTlGQTg0M0UxQjE3MTcwMCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo4NTdBMDE1OTZENEUxMUUzOTlGQTg0M0UxQjE3MTcwMCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pokd7WMAAAESSURBVHja7NnRDYIwEAZgatyARcAV1BV0GJ990o10Bx1EZqhtvEsIIeF6/AWb3J9ceOSDlN41OO99VVJcsWB3+W+ov/6um6qw5AJ7qmLAxb1hAxvYwAY2sIENbGADG5iyC/UMVa/oqsnQSsC3UPtQj5XQNd07Gu4S8DnUm55uaTRjWzKcJOBPqMMK6CE2GjrpR9ctjBZhp3aJpdBirGRby41Owkr34VzoZGxK40CjVdjUTjdEa5uLGqtpzX10o0D3O1gyVjtLaNGMbbTYOcNPKhqCnTutSdEwLGK8nEJDsah5eAzNgWKRAzyjXwTkQLHoE0cEHQnNgWJjtuBBhtG8LKDYHGBGt3YIpTj7E5o5XwEGAIX+d0d81LfiAAAAAElFTkSuQmCC") no-repeat center center; background-size:100% 100%;}
.pullRefresh .pull-icon.pull-icon-rotate{-webkit-transform:rotate(180deg); transform:rotate(180deg);}
.pullRefresh .pull-loadmore-icon{-webkit-transform:rotate(180deg); transform:rotate(180deg);}
.pullRefresh .pull-icon-loading{-webkit-animation:pullrefresh-spin 1s steps(12, end) infinite; animation:pullrefresh-spin 1s steps(12, end) infinite; background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20viewBox%3D'0%200%20120%20120'%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20xmlns%3Axlink%3D'http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink'%3E%3Cdefs%3E%3Cline%20id%3D'l'%20x1%3D'60'%20x2%3D'60'%20y1%3D'7'%20y2%3D'27'%20stroke%3D'%236c6c6c'%20stroke-width%3D'11'%20stroke-linecap%3D'round'%2F%3E%3C%2Fdefs%3E%3Cg%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(30%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(60%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(90%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(120%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(150%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.37'%20transform%3D'rotate(180%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.46'%20transform%3D'rotate(210%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.56'%20transform%3D'rotate(240%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.66'%20transform%3D'rotate(270%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.75'%20transform%3D'rotate(300%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.85'%20transform%3D'rotate(330%2060%2C60)'%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E");}
.pullRefresh .pull-text-wrap{float:left; width:1.8rem; height:0.36rem; overflow:hidden; text-align:center;}
.pullRefresh .pull-text{width:auto; height:0.22rem; line-height:0.22rem; overflow:hidden; font-size:0.14rem; color:#777;}
.pullRefresh .pull-time{width:auto; height:0.14rem; line-height:0.14rem; overflow:hidden; font-size:0.12rem; color:#ccc; -webkit-transform:scale(0.84); transform:scale(0.84);}
.pullRefresh .pull-nomore{width:100%; height:0.64rem; line-height:0.64rem; text-align:center; font-size:0.12rem; color:#ddd;}
.pullRefresh .pull-wrap .pull-preloader, .pullRefresh .pull-wrap .pull-preloader-white{position:absolute; left:50%; top:50%; width:0.26rem; height:0.26rem; margin-left:-0.13rem; margin-top:-0.13rem; -webkit-animation:pullrefresh-spin 1s steps(12, end) infinite; animation:pullrefresh-spin 1s steps(12, end) infinite; background-repeat:no-repeat; background-position:center center; background-size:cover; background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20viewBox%3D'0%200%20120%20120'%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20xmlns%3Axlink%3D'http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink'%3E%3Cdefs%3E%3Cline%20id%3D'l'%20x1%3D'60'%20x2%3D'60'%20y1%3D'7'%20y2%3D'27'%20stroke%3D'%236c6c6c'%20stroke-width%3D'11'%20stroke-linecap%3D'round'%2F%3E%3C%2Fdefs%3E%3Cg%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(30%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(60%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(90%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(120%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(150%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.37'%20transform%3D'rotate(180%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.46'%20transform%3D'rotate(210%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.56'%20transform%3D'rotate(240%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.66'%20transform%3D'rotate(270%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.75'%20transform%3D'rotate(300%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.85'%20transform%3D'rotate(330%2060%2C60)'%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E") !important;}
.pullRefresh .pull-wrap .pull-preloader-white{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg%20viewBox%3D'0%200%20120%20120'%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20xmlns%3Axlink%3D'http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink'%3E%3Cdefs%3E%3Cline%20id%3D'l'%20x1%3D'60'%20x2%3D'60'%20y1%3D'7'%20y2%3D'27'%20stroke%3D'%23fff'%20stroke-width%3D'11'%20stroke-linecap%3D'round'%2F%3E%3C%2Fdefs%3E%3Cg%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(30%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(60%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(90%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(120%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.27'%20transform%3D'rotate(150%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.37'%20transform%3D'rotate(180%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.46'%20transform%3D'rotate(210%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.56'%20transform%3D'rotate(240%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.66'%20transform%3D'rotate(270%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.75'%20transform%3D'rotate(300%2060%2C60)'%2F%3E%3Cuse%20xlink%3Ahref%3D'%23l'%20opacity%3D'.85'%20transform%3D'rotate(330%2060%2C60)'%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E") !important;}
@-webkit-keyframes pullrefresh-spin{100%{-webkit-transform:rotate(360deg);}}
@keyframes pullrefresh-spin{100%{transform:rotate(360deg);}}
</style>