<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">发表评价</div>
	</div>

	<div class="order-comment" v-if="data">
		<div class="view" v-for="(g, i) in data.goods" :data-index="i">
			<div class="info ge-bottom ge-light">
				<div ref="pic" class="pic" :url="g.goods_pic"></div>
				<div class="stars"><i></i><i></i><i></i><i></i><i></i></div>
				<input type="hidden" v-model="stars[i]" class="stars" />
				<input type="hidden" v-model="order_goods_id[i]" />
				<div class="tip"></div>
			</div>
			<div class="content clear-after">
				<textarea v-model="content[i]" placeholder="宝贝满足你的期待吗？你的评论能帮助其他小伙伴哦"></textarea>
				<a href="javascript:void(0)"><i></i><span>添加图片</span></a>
			</div>
		</div>
		<div class="buttonView">
			<a href="javascript:void(0)" class="btn pass" @click="submit">发表评价</a>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name: 'orderComment',
	data() {
		return {
			id: 0,
			data: null,
			tips: ['非常差', '差', '一般', '好', '非常好'],

			stars: [],
			order_goods_id: [],
			content: [],
			pic: [],
		}
	},
	created(){
		this.id = this.$route.query.id
		if (!this.id) {
			alert('missing id')
			this.$router.go(-1)
			return
		}
		this.$ajax.get('/api/order/comment', {id:this.id}).then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			if ($.isArray(this.data.goods)) {
				this.data.goods.forEach(item => {
					this.stars.push('5')
					this.order_goods_id.push(item.id)
					this.content.push('')
					this.pic.push('')
				})
			}
			let vm = this
			this.$nextTick(() => {
				this.selectImage()
				$(this.$refs.pic).loadbackground('url', '100%', '../images/nopic.png')
				$('input.stars').each(function(){
					vm.setStars($(this).prev().find('i:last'))
				});
				$('div.stars i').click(function(){
					vm.setStars($(this))
				})
			})
		})
	},
	methods: {
		selectImage(){
			let vm = this
			$('.content a').component(this).ajaxupload({
				url: '/api/other/uploadfile',
				before: function(){
					this.find('i').addClass('preloader-gray')
				},
				success: function(json){
					if (!$.checkError(json, vm)) return
					this.find('i').removeClass('preloader-gray')
					this.css('background-image', 'url('+json.data+')')
					let index = Number(this.parents('.view').last().attr('data-index'))
					vm.pic[index] = json.data
				}
			})
		},
		setStars(i){
			let idx = i.index();
			i.parents('.info').find('.tip').html(this.tips[idx])
			i.parent().find('i').removeClass('this').eq(idx).addClass('this').prevAll().addClass('this')
			let index = Number(i.parents('.view').last().attr('data-index'))
			this.stars[index] = idx + 1
		},
		submit(){
			/*if (!this.content.length) {
				$.overloadError('请输入评价内容')
				return
			}*/
			this.$ajax.post('/api/order/comment', {
				id: this.id,
				stars: JSON.stringify(this.stars),
				order_goods_id: JSON.stringify(this.order_goods_id),
				content: JSON.stringify(this.content),
				pic: JSON.stringify(this.pic)
			}).then(json => {
				if (!$.checkError(json, this)) return
				$.overloadSuccess('感谢你的评价', 4000, () => {
					this.$router.push({path:'/order/detail', query:{id:this.id}})
				})
			})
		}
	}
}
</script>

<style scoped>
.order-comment .view .content a i.preloader-gray{margin-left:-0.12rem; margin-top:-0.12rem;}
</style>