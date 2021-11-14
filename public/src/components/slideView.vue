<template>
<div ref="slideView" class="slideView pageView" :style="{height:parentHeight}" >
	<div class="slide">
		<ul ref="ul" :style="{width:(dir===0?(imgWidth*(imgList.length+1)):imgWidth)+'px', height:(dir!==0?(imgHeight*(imgList.length+1)):imgHeight)+'px', 'transition-duration':moveSpeed+'ms', left:left, top:top}">
			<template v-if="!isAds">
			<li v-for="g in imgList" :style="{width:imgWidth+'px', height:parentHeight}">
				<video v-if="g.video && g.video.length" width="100%" height="100%" :poster="g.pic" preload="auto" controls>
				<source :src="g.video" type="video/mp4" />
				您的浏览器不支持 video 标签
				</video>
				<a v-else ref="pic" :href="g.slide_link" :url="g.pic"><span v-if="titleField && g[titleField]">{{ g[titleField] }}</span></a>
			</li>
			</template>
			<template v-else>
			<li class="slide-li" v-for="g in imgList" :style="{width:imgWidth+'px', height:parentHeight}" v-html="g.slide_html"></li>
			</template>
		</ul>
	</div>
	<div class="pager" v-if="pager">
		<a href="javascript:void(0)" v-for="(g, k) in list" :class="{'this':k === index}" @click="setIndex(k, g)"></a>
	</div>
</div>
</template>

<script>
import '../../js/photoswipe/photoswipe.css'
import '../../js/photoswipe/default-skin/default-skin.css'
import PhotoSwipe from '../../js/photoswipe/photoswipe.min.js'
import PhotoSwipeUI_Default from '../../js/photoswipe/photoswipe-ui-default.min.js'
export default {
	name:'slideView',
	//props:['list'],
	//props:{list:Array},
	props:{
		list:{ //数据体
			type: Array,
			required: true,
			default(){ //数组、对象必须函数返回
				return []
			}
		},
		height:{ //高度
			type: [String, Number], //多种类型
			default: ''
		},
		speed:{ //滚动速度,单位ms
			type: Number,
			default: 300
		},
		step: { //滚动时间间隔, 0为不自动滚动
			type: Number,
			default: 4000,
			validator: function(value) {return value >= 0} //自定义验证
		},
		dir:{ //滚动方向, 0:向左滚, 1:向上滚
			type: Number,
			default: 0
		},
		pager: { //显示页点
			type: Boolean,
			default: true
		},
		isAds: { //li参数自动生成
			type: Boolean,
			default: false
		},
		drag: { //可否拖拽
			type: Boolean,
			default: true
		},
		href: { //链接地址, 标识例如 [id] 会自动替换为 g.id
			type: String,
			default: ''
		},
		titleField: { //标题值, 为空即不显示
			type: String,
			default: ''
		},
		photoBrowser: { //使用相册功能
			type: Boolean,
			default: false
		}
	},
	data(){
		return {
			slideView: null,
			ul: null,
			imgWidth: document.documentElement.clientWidth || document.body.clientWidth, //宽度
			imgHeight: 0, //高度
			parentHeight: '', //容器高度
			index: 0, //当前显示索引
			left: '0',
			top: '0',
			imgList: [],
			moveSpeed: 0,
			timer: null,
			isScrolling: false,

			position: 0,
			originPosition: 0,
			nowPosition: 0,
			distance: 0,
			start: 0,
			isMoved: false,
			isBounces: false,
			imgArea: 0,
			isFirstItem: false,
			isLastItem: false
		}
	},
	watch:{
		index: {
			handler(){
				this.isScrolling = true
				if (this.dir === 0) {
					this.left = -(this.imgWidth * this.index) + 'px'
				} else {
					this.top = -(this.imgHeight * this.index) + 'px'
				}
				setTimeout(() => this.isScrolling = false, this.speed)
				if(this.index === this.imgList.length - 1 && this.step > 0){
					setTimeout(() => {
						this.moveSpeed = 0
						this.index = 0
						this.left = 0
						this.top = 0
						setTimeout(() => this.moveSpeed = this.speed, this.speed)
					}, this.speed)
				}
			}
		},
		height(){
			if (typeof this.height === 'string') {
				this.parentHeight = this.height
				if (/px$/.test(this.height)) {
					this.imgHeight = Number(this.height.replace(/px/, '')) || 0
				} else if (/rem$/.test(this.height)) {
					this.imgHeight = this.$.toPx(Number(this.height.replace(/rem/, '')) || 0)
				}
			} else if (typeof this.height === 'number') {
				this.parentHeight += 'px'
				this.imgHeight = this.height
			}
		}
	},
	created(){
		if (typeof this.height === 'string') {
			this.parentHeight = this.height
			if (/px$/.test(this.height)) {
				this.imgHeight = Number(this.height.replace(/px/, '')) || 0
			} else if (/rem$/.test(this.height)) {
				this.imgHeight = this.$.toPx(Number(this.height.replace(/rem/, '')) || 0)
			}
		} else if (typeof this.height === 'number') {
			this.parentHeight += 'px'
			this.imgHeight = this.height
		}
		this.list.forEach(item => {
			if (this.isAds) {
				let html = ''
				let type = item.ad_type, content = item.ad_content, pic = item.pic
				switch (type) {
					case 'html5':
						html = '<a type="'+type+'" href="'+content+'" url="'+pic+'"></a>'
						break;
					case 'goods':
						html = '<a type="'+type+'" href="/goods/detail?id='+content+'" url="'+pic+'"></a>'
						break;
					case 'shop':
						html = '<a type="'+type+'" href="/shop/detail&id='+content+'" url="'+pic+'"></a>'
						break;
					case 'article':
						html = '<a type="'+type+'" href="/article/detail&id='+content+'" url="'+pic+'"></a>'
						break;
					case 'type':case 'subtype':
						html = '<a type="'+type+'" href="/goods?category_id='+content+'" url="'+pic+'"></a>'
						break;
					case 'brand':
						html = '<a type="'+type+'" href="/goods?brand_id='+content+'" url="'+pic+'"></a>'
						break;
					case 'country':
						html = '<a type="'+type+'" href="/goods?country_id='+content+'" url="'+pic+'"></a>'
						break;
					case 'coupon':
						html = '<a type="'+type+'" href="javascript:void(0)" url="'+pic+'" data-click="this.getCoupon('+content+')"></a>'
						break;
					case 'recharge':
						html = '<a type="'+type+'" href="/recharge/commit?id='+content+'" url="'+pic+'"></a>'
						break
					case 'register':
						html = '<a type="'+type+'" href="'+(this.$.storage('member') ? '/member' : '/register')+'" url="'+pic+'"></a>'
						break
					default:
						html = '<a type="'+type+'" href="javascript:void(0)" mid="'+content+'" url="'+pic+'"></a>'
						break
				}
				item['slide_html'] = html
			} else {
				let href = this.href
				if (href.length) {
					for (let key in item) {
						href = href.replace(new RegExp('\\[(' + key + ')\\]', 'ig'), item[key])
					}
				} else {
					href = 'javascript:void(0)'
				}
				item['slide_link'] = href
			}
			this.imgList.push(item)
		})
		if (this.step > 0) this.imgList.push(this.list[0])
	},
	mounted(){
		this.moveSpeed = this.speed
		//this.nextTick 数据完成执行
		this.$nextTick(() => {//页面渲染完成执行
			this.slideView = this.$refs.slideView
			this.ul = this.$refs.ul
			if (this.drag) {
				this.slideView.addEventListener('mousedown', this.startDrag, { passive: false })
				this.slideView.addEventListener('touchstart', this.startDrag, { passive: false })
			}
			this.imgWidth = this.$el.clientWidth
			setTimeout(() => {
				if (this.$refs.pic) this.$refs.pic.forEach(item => {
					if (item.getAttribute('loadbackground')) return
					if (!item.getAttribute('url')) return
					$(item).loadpic(item.getAttribute('url'), '../../images/nopic.png', (item, pic, state) => {
						item.style.backgroundImage = `url(${pic})`
						if (!state) item.style.backgroundSize = '30%'
						item.setAttribute('loadbackground', 'complete')
					})
				})
				$('.slide-li a').loadbackground('url', '30%', '../../images/nopic.png').filter('[data-click]').on('click', (e) => {
					eval((e.target||e.srcElement).getAttribute('data-click'))
				})
			}, 300)
			this.continueAuto()
			if (this.photoBrowser && $ && $.fn.photoBrowser) {
				$(this.slideView).find('.slide a').photoBrowser()
			}
		})
		window.onresize = () => {
			this.moveSpeed = 0
			setTimeout(() => this.moveSpeed = this.speed, this.speed)
			this.imgWidth = this.$el.clientWidth
			if (typeof this.height === 'string' && /rem$/.test(this.height)) {
				this.imgHeight = this.$.toPx(Number(this.height.replace(/rem/, '')) || 0)
			}
			if (this.dir === 0) {
				this.left = -(this.imgWidth * this.index) + 'px'
			} else {
				this.top = -(this.imgHeight * this.index) + 'px'
			}
		}
	},
	methods: {
		continueAuto(){
			if (this.step > 0) this.timer = setInterval(() => {
				this.index++
			}, this.step)
		},
		setIndex(index){
			this.index = index
			if (this.step > 0) {
				clearInterval(this.timer)
				this.timer = setInterval(() => {
					this.index++
				}, this.step)
			}
		},
		startDrag(e){
			if (this.isScrolling) return
			if (this.timer) clearInterval(this.timer)
			this.moveSpeed = 0
			this.isMoved = false
			this.isBounces = false
			this.distance = 0
			if (this.dir === 0) {
				this.start = e.touches ? e.touches[0].pageX : e.pageX
				this.imgArea = this.imgWidth
			} else {
				this.start = e.touches ? e.touches[0].pageY : e.pageY
				this.imgArea = this.imgHeight
			}
			this.position = this.dir === 0 ? Number((this.left+'').replace('px', '')) : Number((this.top+'').replace('px', ''))
			this.originPosition = this.position
			this.isFirstItem = this.originPosition === 0
			this.isLastItem = this.originPosition === -(this.imgArea * (this.list.length - 1))
			this.slideView.addEventListener('mousemove', this.moveDrag, { passive: false })
			this.slideView.addEventListener('mouseup', this.endDrag, { passive: false })
			this.slideView.addEventListener('touchmove', this.moveDrag, { passive: false })
			this.slideView.addEventListener('touchend', this.endDrag, { passive: false })
		},
		moveDrag(e){
			e.preventDefault()
			this.isMoved = true
			let move = 0
			let current = 0
			if (this.dir === 0) {
				current = e.touches ? e.touches[0].pageX : e.pageX
			} else {
				current = e.touches ? e.touches[0].pageY : e.pageY
			}
			move = current - this.start
			this.isBounces = ((move > 0 && this.isFirstItem) || (this.step === 0 && move < 0 && this.isLastItem))
			this.distance = this.position + move * (this.isBounces ? 0.1 : 1)
			this.nowPosition = this.distance
			if (this.dir === 0) {
				this.ul.style.left = this.nowPosition + 'px'
			} else {
				this.ul.style.top = this.nowPosition + 'px'
			}
		},
		endDrag(){
			this.moveSpeed = this.speed
			if (this.isMoved) {
				this.isScrolling = true
				if (this.isBounces) {
					if (this.dir === 0) {
						this.ul.style.left = this.isFirstItem ? 0 : -(this.imgArea * (this.list.length - 1)) + 'px'
					} else {
						this.ul.style.top = this.isFirstItem ? 0 : -(this.imgArea * (this.list.length - 1)) + 'px'
					}
				} else {
					if (Math.abs(this.originPosition - this.nowPosition) >= this.imgArea / (this.dir === 0 ? 4 : 2)) {
						this.index = this.index + (this.originPosition < this.nowPosition ? -1 : 1)
					} else {
						if (this.dir === 0) {
							this.ul.style.left = this.originPosition + 'px'
						} else {
							this.ul.style.top = this.originPosition + 'px'
						}
					}
				}
				setTimeout(() => this.isScrolling = false, this.speed)
			}
			this.slideView.removeEventListener('mousemove', this.moveDrag, { passive: false })
			this.slideView.addEventListener('mouseup', this.endDrag, { passive: false })
			this.slideView.removeEventListener('touchmove', this.moveDrag, { passive: false })
			this.slideView.removeEventListener('touchend', this.endDrag, { passive: false })
			this.continueAuto()
		},
		getCoupon(id){
			if (!this.$.storage('member')) return this.$router.push('/login')
			this.$ajax.get('/api/coupon/ling', {coupon_id:id}).then(json => {
				if (!this.$.checkError(json, this)) return
				alert('优惠券领取成功')
			})
		}
	}
}
</script>

<style scoped>
.pageView{overflow:hidden; background-color:#f3f3f3;}
.pageView .slide{position:relative; height:100%;}
.pageView ul{height:100%; position:relative; -webkit-transition:left 0.3s ease-out, top 0.3s ease-out; transition:left 0.3s ease-out, top 0.3s ease-out;}
.pageView li{float:left; height:100%;}
.pageView li a{display:block; width:100%; height:100%; position:relative; text-decoration:none; background:no-repeat center center; background-size:cover;}
.pageView li a span{display:block; position:absolute; left:0; right:0; bottom:0; text-align:left; box-sizing:border-box; padding:0.03rem 0.05rem; background:rgba(0,0,0,0.5); color:#fff; font-size:0.12rem;}
.pageView .pager{-webkit-transform:translateX(-50%); transform:translateX(-50%);}
</style>