<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">发表攻略</div>
	</div>

	<div class="discover-edit main-bottom">
		<div class="view ge-bottom ge-light">
			<div class="picView">
				<a v-for="g in images" href="javascript:void(0)" :style="{'background-image':'url('+g+')'}"></a>
				<a class="plus" href="javascript:void(0)"></a>
			</div>
			<div class="tip"><span>提示：请上传攻略图片</span></div>
		</div>

		<div class="view ge-bottom ge-light">
			<div class="title">
				<font>标题</font>
				<input type="text" v-model="title" placeholder="请输入标题" />
			</div>
		</div>

		<div class="view">
			<router-link class="goods push-ico" to="/search?from=article">添加关联商品</router-link>
		</div>
		<ul class="view goodsView">
			<li v-if="goods" v-for="g in goods" :data-id="g.id" :key="g.id">
				<div class="row">
					<a class="cl" href="javascript:void(0)"><span @click="deleteGoods">－</span></a>
					<router-link class="bo" :to="{path:'/goods/detail', query:{id:g.id}}">
						<div ref="pic" class="pic" :url="g.pic"></div>
						<div class="name">{{ g.name }}</div>
						<div class="price">￥{{ g.price|round }}</div>
					</router-link>
				</div>
			</li>
		</ul>

		<div class="view con ge-top ge-light">
			攻略内容
		</div>
		<div class="view">
			<div class="content">
				<textarea v-model="content" placeholder="请输入攻略内容"></textarea>
			</div>
		</div>

		<a class="btn" href="javascript:void(0)" @click="submit">发表</a>
	</div>
</div>
</template>

<script>
export default {
	name:'articleEdit',
	data(){
		return {
			maxCount: 3,
			title: '',
			content: '',
			images: [],
			goods: []
		}
	},
	created(){
		$.paramAlive(this, '/search').then(data => {
			let isExist = false
			for (let i = 0; i < this.goods.length; i++) {
				if (Number(this.goods[i].id) === Number(data.id)) {
					isExist = true
					break
				}
			}
			if (!isExist) this.goods.push(data)
			this.create()
		}).catch(() => {
			this.create()
		})
	},
	methods:{
		create(){
			this.$nextTick(() => {
				$('.plus').component(this).ajaxupload({
					url: '/api/article/upload_pic',
					name: 'pic',
					before: () => {
						if (this.images.length >= this.maxCount) {
							this.$emit('overloadwarning', '最多只能选择'+this.maxCount+'张')
							return false
						}
						this.$emit('overload')
					},
					success: json => {
						this.images.push(json.data)
					}
				});
				setTimeout(() => {
					$(this.$refs.pic).loadbackground('url', '80%', '../images/nopic.png')
				}, 100)
			})
		},
		deleteGoods(e){
			let li = $(e.target).parent().parent().parent()
			let id = Number(li.attr('data-id'))
			li.animate({ height:0 }, 200, () => {
				let goods = []
				this.goods.forEach(item => {
					if (Number(item.id) !== id) goods.push(item)
				})
				this.goods = goods
			})
		},
		submit(){
			if (!this.images.length) {
				//this.$emit('overloaderror', '请选择攻略图片')
				//return
			}
			if (!this.title.length) {
				this.$emit('overloaderror', '请输入标题')
				return
			}
			if (!this.content.length) {
				this.$emit('overloaderror', '请输入攻略内容')
				return
			}
			let pics = this.images.length ? JSON.stringify(this.images) : ''
			let goods = ''
			if (this.goods.length) {
				goods = []
				this.goods.forEach(item => {
					goods.push(item.id)
				})
				goods = JSON.stringify(goods)
			}
			this.$ajax.post('/api/article/add', {title:this.title, content:this.content, pics:pics, goods:goods}).then(json => {
				if (!$.checkError(json, this)) return
				this.$emit('overloadsuccess', '提交成功', 3000, () => {
					this.$router.push('/article')
				})
			})
		}
	}
}
</script>

<style scoped>

</style>