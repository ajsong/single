<template>
<div class="height-wrap">
	<div class="navBar">
		<div class="titleView-x">分类</div>
	</div>

	<div class="category-index main-padding-bottom height-wrap">
		<template v-if="data.category.length">
		<div class="sortView height-wrap">
			<ul class="list">
				<li class="ge-bottom ge-light" v-for="(g, i) in data.category">
					<a href="javascript:void(0)" :class="{'this':index === i}" @click="setCategories(i)">{{g.name}}</a>
				</li>
			</ul>
		</div>
		<div class="categoryView">
			<slideView v-if="isArray(data.category[index].flashes) && data.category[index].flashes.length" :list="data.category[index].flashes" :height="'1.12rem'"></slideView>
			<ul class="categoryList" v-if="isArray(data.category[index].categories) && data.category[index].categories.length">
				<li v-for="g in data.category[index].categories">
					<router-link :to="{path:'/goods', query:{category_id:g.id, title:g.name}}">
						<i ref="pic" :url="g.pic"></i>
						<span>{{ g.name }}</span>
					</router-link>
				</li>
			</ul>
		</div>
		</template>
	</div>
</div>
</template>

<script>
import slideView from '../../components/slideView'
export default {
	name:'category',
	data() {
		return {
			id: 0,
			index: 0,
			data: {
				category: []
			}
		}
	},
	components:{
		slideView
	},
	watch:{
		//响应路由参数的变化，to表示即将要进入的那个组件，from表示从哪个组件过来的
		$route(to, from) {
			this.id = to.params.id
		}
	},
	created(){
		this.$ajax.get('/api/category').then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.setCategories(0)
		})
	},
	mounted(){
		$([document.body, this.$el.parentNode]).addClass('height-wrap')
	},
	beforeDestroy(){
		$([document.body, this.$el.parentNode]).removeClass('height-wrap')
	},
	methods:{
		setCategories(index){
			if(!this.data.category.length)return
			this.index = index
			this.$nextTick(() => {
				$(this.$refs.pic).loadbackground('url', '100%', '../images/nopic.png')
			})
		},
		isArray(obj){
			return $.isArray(obj)
		}
	}

}
</script>

<style>

</style>