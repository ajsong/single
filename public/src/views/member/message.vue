<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x" v-if="type === 'offer'">优惠信息</div>
		<div class="titleView-x" v-if="type === 'logistics'">物流通知</div>
		<div class="titleView-x" v-else>我的消息</div>
	</div>

	<div class="message-index">
		<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit" :marginTop="$.toPx(0.44)">
			<ul class="tableView tableView-noMargin list">
				<template v-if="data">
				<li v-for="(g, index) in data">
					<h1>
						<a href="javascript:void(0)" @click="setReaded(g.id, index)">
							<div :class="['view', {'d':Number(g.readed)===1}]">
								<div>
									<i></i>
									{{ g.content }}
									<span class="scale10-right">{{ g.add_time }}</span>
								</div>
							</div>
						</a>
					</h1>
				</li>
				</template>
			</ul>
		</pull-refresh>
	</div>
</div>
</template>

<script>
import pullRefresh from '../../components/pullRefresh'
export default {
	name:'message',
	data(){
		return {
			type: '',
			data: null,

			scroller: null,
			refresh: {
				callback: this.getData,
				autoLoad: true
			},
			loadmore: {
				callback: this.getData,
				size: 8
			}
		}
	},
	components:{
		pullRefresh
	},
	created(){
		if (this.$route.query.type) this.type = this.$route.query.type
	},
	methods:{
		setReaded(id, index){
			this.$ajax.post('/api/message/read', { id:id }).then(json => {
				if (!$.checkError(json, this)) return
				this.data[index].readed = 1
			})
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/message', {offset:(page.num - 1) * page.size, pagesize:page.size}).then(json => {
				if (!$.checkError(json, this)) return scroller.end()
				if (!(json.data instanceof Array)) {
					this.data = null
					return scroller.end()
				}
				let data = page.num === 1 ? [] : this.data
				data.push(...json.data)
				this.data = data
				this.$nextTick(() => {
					scroller.end()
				})
			})
		}
	}
}
</script>

<style scoped>

</style>