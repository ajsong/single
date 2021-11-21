<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">每日签到</div>
	</div>

	<div class="member-sign-green" v-if="data">
		<ul class="view ge-bottom ge-light">
			<li>
				<div>本月累计签到</div>
				<span v-html="data.days ? data.days.length + '天' : '0天'"></span>
			</li>
			<li>
				<div>已连续签到</div>
				<span>{{ data.times }}天</span>
			</li>
		</ul>
		<div class="sign-integral-bg">
			<div class="sign-integral ge-bottom ge-light"><span class="month-name">{{ month }}</span>月签到表</div>
		</div>
		<div class="cale" :initdate="data.days ? data.days.join(',') : ''"></div>
		<div class="buttonView">
			<a href="javascript:void(0)" :class="{'btn':data.signed !== 1}" v-html="data.signed === 1 ? '已签到' : '立即签到'"></a>
		</div>
	</div>
</div>
</template>

<script>
import '../../../css/datepicker.css'
export default {
	name:'sign',
	data(){
		return {
			data: null,
			month: ''
		}
	},
	created(){
		let months = ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二']
		this.month = months[(new Date()).getMonth()]
		this.$ajax.get('/api/member/sign').then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.$nextTick(() => {
				this.resize()
				this.setDatepicker()
				$('.btn').click(() => {
					this.$ajax.post('/api/member/sign', { }).then(json => {
						if (!$.checkError(json, this)) return
						this.$router.go(0)
					})
				})
			})
		})
	},
	mounted(){
		window.resize = () => this.resize()
	},
	methods:{
		resize(){
			$('.member-sign .view').autoHeight(320, 148)
			//$('.circle-view').css('transform', 'rotate(-5deg) translateX(-3%)' + ($.window().width/320 !== 1 ? ' scale('+($.window().width/320)+')' : ''))
		},
		setDatepicker(){
			$('.cale').datepicker({
				cls: 'calendar',
				always: true,
				multiple: true,
				hiddenNavBar: true,
				disable: true,
				touchMove: false,
				minDate: '+1',
				maxDate: 'today'
			});
		}
	}
}
</script>

<style scoped>

</style>