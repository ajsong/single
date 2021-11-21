<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">拼团详情</div>
	</div>

	<template v-if="data">
	<div class="groupbuy-detail">
		<div :class="['goods', 'status'+data.groupbuy_status]">
			<router-link :to="{path:'/goods/detail', query:{id:data.order_goods.id}}">
				<div ref="pic" class="pic" :url="data.order_goods.pic"></div>
				<div class="name">{{ data.order_goods.name }}</div>
				<div class="price">￥{{ data.order_goods.price|round }} <s>￥{{ data.order_goods.market_price|round }}</s></div>
			</router-link>
		</div>

		<div class="row">
			<div class="member">
				<ul class="clear-after">
					<li v-for="g in data.member_list"><div :style="{'background-image':(g.avatar ? 'url('+g.avatar+')' : '')}"></div><font v-if="g.parent_id===0"><i>团长</i></font></li>
					<li v-for="e in Number(data.remain_number)"><div></div></li>
				</ul>
				<span :class="['status'+data.groupbuy_status]">
					<template v-if="Number(data.groupbuy_status)===0">
					仅剩 <strong>{{ data.order_goods.remain }}</strong> 人 / <font>00:00:00</font> 后结束
					</template>
					<template v-else-if="Number(data.groupbuy_status)===-1">
					很遗憾，拼团失败
					</template>
					<template v-else-if="Number(data.groupbuy_status)===1">
					已有{{ data.order_goods.groupbuy_number }}人参团，<i>拼团成功</i>
					</template>
				</span>
				<template v-if="Number(data.groupbuy_status)===0">
				<input type="hidden" id="now" :value="data.order_goods.groupbuy_now" />
				<input type="hidden" id="countdown" :value="data.order_goods.groupbuy_end_time" />
				</template>
			</div>
			<div class="tip ge-top"><div>好友参团·人满发货·人不满退款</div>拼团须知：</div>
		</div>

		<div class="buttonView" v-if="Number(data.groupbuy_status)===0">
			<a v-if="Number(data.owner)===1" class="btn share" href="javascript:void(0)" @click="$.shareMark()">一键分享给好友</a>
			<a v-else class="btn join" href="javascript:void(0)">一键参团</a>
		</div>
	</div>

	<div class="goods-group goods-spec" v-if="Number(data.owner)===0 && Number(data.groupbuy_status)===0 && data.goods.spec">
		<div class="picView">
			<div>
				<a href="javascript:void(0)"><b>⊗</b></a>
				<a v-if="$.isArray(data.goods.pics) && data.goods.pics.length" ref="pic" class="pic" :href="data.goods.pics[0].pic" :url="data.goods.pics[0].pic"></a>
				<a v-else ref="pic" class="pic" :href="data.goods.pic" :url="data.goods.pic"></a>
				<strong>￥{{ data.goods.price|round }}</strong>
				<font>库存{{ data.goods.stocks }}件</font>
				<span>选择{{ data.goods.spec }}分类</span>
			</div>
		</div>
		<div class="specView">
			<div class="specGroup clear-after ge-top ge-light" v-for="g in data.goods.specs">
				<div>{{ g.name }}</div>
				<a v-for="s in g.sub" href="javascript:void(0)" :spec_id="s.id"><span>{{ s.name }}</span></a>
			</div>
		</div>
		<div class="btnView">
			<a class="btn buy long" href="javascript:void(0)">确定</a>
		</div>
	</div>

	<div class="share-mark" style="display:none;"></div>
	</template>
</div>
</template>

<script>
import '../../../js/photoswipe/photoswipe.css'
import '../../../js/photoswipe/default-skin/default-skin.css'
export default {
	name: 'groupbuyDetail',
	data() {
		return {
			id: 0,
			order_sn: '',
			data: null
		}
	},
	created(){
		let id = this.$route.query.id
		let order_sn = this.$route.query.order_sn
		if (!id) {
			alert('missing id')
			this.$router.go(-1)
			return
		}
		if (!order_sn) {
			alert('missing order_sn')
			this.$router.go(-1)
			return
		}
		this.id = id
		this.order_sn = order_sn
		this.$ajax.get('/api/groupbuy/detail', {id:id, order_sn:order_sn}).then(json =>{
			if(!$.checkError(json, this)) return
			this.data = json.data
			let vm = this
			this.$nextTick(() => {
				let ul = $('.groupbuy-detail .member ul');
				ul.css('margin-left', (($('.groupbuy-detail .member').width()-ul.width())/2)+'px');

				if (json.data.groupbuy_status !== 0) return

				if($('#countdown').length){
					let countdown = Number($('#countdown').val()), now = Number($('#now').val()), timer = null,
						countdownFn = () => {
							let result = countdown - now, r = result;
							if(result<=0){
								$('.row .member > span font').html('00:00:00');
								clearInterval(timer);
								timer = null;
								this.$router.go(0)
								return;
							}
							let day = Math.floor(r/(60*60*24));
							r = result - day*60*60*24;
							let hour = Math.floor(r/(60*60));
							r -= hour*60*60;
							let minute = Math.floor(r/60);
							r -= minute*60;
							let second = r;
							$('.row .member > span font').html($.preZero(hour, 2)+':'+$.preZero(minute, 2)+':'+$.preZero(second, 2));
							now += 1;
						}
					timer = setInterval(countdownFn, 1000);
					countdownFn();
				}

				$('.join').click(function(){
					$('.goods-spec').presentView();
				});
				$('.buy').click(function(){
					let spec_id = [], quantity = 1;
					if($('.specGroup').length){
						let count = 0, groupCount = $('.specGroup').length;
						$('.specGroup a.this').each(function(){
							if(!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0) return false;
							spec_id.push($(this).attr('spec_id'));
							count++;
						});
						if(count!==groupCount){
							if(!$(this).parents('.goods-spec').length)$('.goods-spec').presentView();
							return;
						}
					}
					let type = 1, parent_id = 0, integral_order = 0
					let goods = JSON.stringify([{ goods_id:json.data.goods.id, quantity:quantity, spec:spec_id.join(',') }])
					vm.$router.push({name:'cartCommit', query:{
							type,
							parent_id,
							goods,
							integral_order
						}
					})
				});

				$('.specGroup a').click(function(){
					$(this).addClass('this').siblings('a').removeClass('this');
					let spec_id = [], spec_name = [], count = 0, groupCount = $('.specGroup').length;
					$('.specGroup a.this').each(function(){
						if(!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0)return false;
						spec_id.push($(this).attr('spec_id'));
						spec_name.push($(this).text());
						count++;
					});
					if(count!==groupCount)return;
					vm.$ajax.get('/api/goods/get_spec', { goods_id:json.data.goods.id, spec:spec_id.join(',') }).then(json => {
						if(!$.checkError(json, this)) return
						if(!$.isPlainObject(json.data))return
						if(json.data.pic.length)$('.picView .pic').attr({href:json.data.pic, url:json.data.pic}).loadbackground('url', '100%', '../images/nopic.png').photoBrowser();
						$('.picView strong').html('￥'+$.round(json.data.price, 2));
						$('.picView font').html('库存'+json.data.stocks+'件');
						$('.picView span').html('已选 "'+spec_name.join('" "')+'"');
						$('.spec-param span').addClass('selected').html('已选 "'+spec_name.join('" "')+'"');
					});
				});
				$('.specGroup').each(function(){
					if($(this).find('a').length===1)$(this).find('a').click();
				});
				$('.picView a:eq(0)').click(function(){
					$('.goods-spec').presentView(false);
				});
				if($('.picView .pic').length)$('.picView .pic').photoBrowser();
			})
		})
	}
}
</script>

<style scoped>

</style>