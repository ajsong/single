<template>
<div class="message-index">
	<pull-refresh :refresh="refresh" :loadmore="loadmore" :init="scrollerInit">
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
</template>

<script>
import pullRefresh from '../../components/pullRefresh'
export default {
	name:'message',
	data(){
		return {
			data: null,

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
	methods:{
		setReaded(id, index){
			this.$ajax.post('/api/message/read', { id:id }).then(json => {
				if(!this.$.checkError(json, this))return
				this.data[index].readed = 1
			})
		},
		scrollerInit(scroller){
			this.scroller = scroller
		},
		getData(page, scroller){
			this.$ajax.get('/api/message', {offset:(page.num - 1) * page.size, pagesize:page.size}).then(json => {
				if(!this.$.checkError(json, this))return
				if (!(json.data instanceof Array)) return scroller.end()
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