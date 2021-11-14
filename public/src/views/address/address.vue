<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:void(0)" @click="$router.go(-1)"><i class="return"></i></a>
		<div class="titleView-x" v-html="from ? '收货地址' : '收货地址管理'"></div>
		<router-link class="right" to="/address/add" v-if="!from"><span>添加</span></router-link>
	</div>

	<div class="address-list">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
			<section v-if="data.length">
				<ul class="list tableView tableView-noLine" v-for="g in data">
					<li>
						<h1>
							<div class="name">{{ g.contactman }}　{{ g.mobile }}</div>
							<div class="address">{{ g.province }}<template v-if="g.province !== g.city"> {{ g.city }}</template> {{ g.district }} {{ g.address }}</div>
						</h1>
						<div class="bottomView ge-top ge-light" v-if="!from">
							<a href="javascript:void(0)" class="btn delete" @click="deleteAddress(g.id)"><span>删除</span></a>
							<router-link :to="{path:'/address/edit', query:{id:g.id}}" class="btn edit"><span>编辑</span></router-link>
							<a href="javascript:void(0)" :class="['default', {'default-x':Number(g.is_default)===1}]" @click="setDefault(g.id)">设为默认地址</a>
						</div>
					</li>
				</ul>
			</section>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import pullRefresh from '../../components/pullRefresh'
import eventBus from '../../plugins/eventBus';
export default {
	name:'index',
	data(){
		return {
			from: null,
			data: [],

			scroller: null,
			refresh: {
				callback: this.getData,
				autoLoad: true
			},
			loadmore: {
				callback: this.getData,
				pagesize: 8
			}
		}
	},
	components:{
		pullRefresh
	},
	created(){
		this.from = this.$route.query.from
	},
	methods:{
		deleteAddress(id){
			this.$ajax.post('/api/address/delete', {id:id}).then(json => {
				if(!this.$.checkError(json, this))return
				this.$router.go(0)
			})
		},
		setDefault(id){
			this.$ajax.post('/api/address/set_default', {id:id}).then(json => {
				if(!this.$.checkError(json, this))return
				this.$router.go(0)
			})
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/address', {offset:(page.num - 1) * page.size, pagesize:page.size}).then(json => {
				if(!this.$.checkError(json, this))return
				if (!(json.data instanceof Array)) return scroller.end()
				let data = page.num === 1 ? [] : this.data
				data.push(...json.data)
				this.data = data
				this.$nextTick(() => {
					scroller.end()
					if (this.from === 'cart') {
						let _vm = this
						$('.pullRefresh ul').not('[click]').attr('click', 'click').find('h1').on('click', function(){
							let index = $(this).parent().parent().index()
							eventBus.$emit('changeAddress', _vm.data[index])
							_vm.$router.go(-1)
						})
					}
				})
			})
		}
	}
}
</script>

<style scoped>

</style>