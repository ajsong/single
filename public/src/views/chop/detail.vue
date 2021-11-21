<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">一起砍价</div>
	</div>

	<div class="chop-detail" v-if="data">
		<div class="main">
			<a class="rule" href="javascript:void(0)">活动规则</a>
			<div :class="['goodsView', 'status'+data.order.chop_status]">
				<div class="member"><i :style="{'background-image':(data.order.member.avatar?'url('+data.order.member.avatar+')':'')}"></i>{{ data.order.member.name }}</div>
				<router-link :to="{path:'/goods/detail', query:{id:data.order.order_goods.id}}" class="goods">
					<div ref="pic" class="pic" :url="data.order.order_goods.pic"></div>
					<div class="name">{{ data.order.order_goods.name }}</div>
					<div class="origin_price">原价：￥{{ data.order.order_goods.price|round }}</div>
					<div class="price">底价：<span>￥{{ data.order.order_goods.chop_price|round }}</span></div>
					<div :class="['time', 'status'+data.order.chop_status]"><font>剩余时间：<span>00:00:00</span></font></div>
					<template v-if="data.order.chop_status===0">
					<input type="hidden" id="now" :value="data.order.order_goods.chop_now" />
					<input type="hidden" id="countdown" :value="data.order.order_goods.chop_end_time" />
					</template>
				</router-link>
			</div>
			<div class="progressView">
				<div class="progress"><span :style="{width:((data.order.order_goods.chop_price-data.order.order_goods.remain_price)/data.order.order_goods.chop_price)+'%'}"></span></div>
				<div class="info">
					<font>还差：{{ data.order.order_goods.remain_price|round }}元</font>
					<span>已砍：{{ data.order.order_goods.chop_price-data.order.order_goods.remain_price|round }}元</span>
				</div>
			</div>
			<div class="btnView">
				<template v-if="Number(data.order.owner)===1">
					<a v-if="data.order.chop_status===1" class="btn" href="javascript:void(0)" @click="callpay">立即购买</a>
					<router-link v-else-if="Number(data.order.chop_status)===-1" class="btn" :to="{path:'/goods/detail', query:{id:data.order.order_goods.id}}">重砍一个</router-link>
					<a v-else class="btn share" href="javascript:void(0)">呼朋唤友来砍价</a>
				</template>
				<template v-else>
					<template v-if="data.order.chop_status===0">
					<a class="btn cut" href="javascript:void(0)">帮TA砍一刀</a>
					<router-link class="btn" :to="{path:'/goods/detail', query:{id:data.order.order_goods.id}}">我也参加</router-link>
					</template>
					<router-link v-else class="btn" :to="{path:'/goods/detail', query:{id:data.order.order_goods.id}}">我也砍一个</router-link>
				</template>
			</div>
			<div class="list">
				<div class="ge-bottom">已有{{ data.order.member_list.length-1 }}位小伙伴帮忙砍价</div>
				<ul class="clear-after">
					<li class="ge-bottom" v-for="g in data.order.member_list">
						<i :style="{'background-image':(g.avatar?'url('+g.avatar+')':'')}"></i>
						<font>砍掉<strong>{{ g.price|round }}</strong>元</font>
						<span><div>{{ g.name }}</div><strong>{{ g.memo }}</strong></span>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="share-mark" style="display:none;"></div>

	<div class="chop-alert clear-after ruleView hidden" no-close="true">
		<a class="close" href="javascript:void(0)">╳</a>
		<h5 class="ge-bottom"><span>砍价规则</span></h5>
		<div>1.邀请好友一起砍价，看到最低价格即可购买商品；</div>
		<div>2.对于同一个砍价，您只能帮助砍价一次；</div>
		<div>3.每次金额随机，参与好友越多越容易成功；</div>
		<div>4.砍价完成后，需要在砍价活动结束前支付，逾期失效。</div>
	</div>

	<template v-if="data">
	<div v-if="Number(data.order.order_goods.readed)===0" class="chop-alert clear-after alertView hidden" no-close="true">
		<a class="close" href="javascript:void(0)">╳</a>
		<div class="member">恭喜你第一刀砍掉一半！</div>
		<div class="price">成功砍掉 <span>{{ data.order.order_goods.chop_price-data.order.order_goods.remain_price|round }}</span> 元</div>
		<a class="btn share" href="javascript:void(0)">呼朋唤友来砍价</a>
	</div>
	<div v-else class="chop-alert clear-after alertView hidden" no-close="true">
		<a class="close" href="javascript:void(0)">╳</a>
		<div class="member"></div>
		<div class="price">成功帮TA砍掉 <span>0.00</span> 元</div>
		<router-link class="btn" :to="{path:'/goods/detail', query:{id:data.order.order_goods.id}}">我也来参加</router-link>
	</div>
	</template>
</div>
</template>

<script>
export default {
	name: 'chopDetail',
	data() {
		return {
			order_sn: '',
			data: null,
			jsApiParameters: null
		}
	},
	created(){
		let order_sn = this.$route.query.order_sn
		if (!order_sn) {
			alert('missing order_sn')
			this.$router.go(-1)
			return
		}
		this.order_sn = order_sn
		this.$ajax.get('/api/chop/detail', {order_sn:order_sn}).then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.jsApiParameters = json.data.jsApiParameters
			if (json.data.client.navigation_bgcolor.toLowerCase() !== '#fff' &&
				json.data.client.navigation_bgcolor.toLowerCase() !== '#ffffff' &&
				json.data.client.navigation_bgcolor.toLowerCase() !== '#ecc360') {
				$(document.body).css('background-color', json.data.client.navigation_bgcolor)
			}
			this.$nextTick(() => {
				$(this.$refs.pic).loadbackground('url', '80%', '../images/nopic.png')
				if (Number(json.data.order.order_goods.readed)===0) {
					setTimeout(function(){ $.overlay('.alertView') }, 1000);
				}
				$('.rule').click(function(){
					$.overlay('.ruleView');
				});
				$('.chop-alert .close').click(function(){
					$.overlay(false);
				});

				if (Number(json.data.order.chop_status)===0) {
					$('.share').click(function(){
						if($('.load-face').length){
							$.overlay(false);
							setTimeout(() => $.shareMark(), 400);
						}else{
							$.shareMark();
						}
					});

					if($('#countdown').length){
						let countdown = Number($('#countdown').val()), now = Number($('#now').val()), timer = null,
							countdownFn = () => {
								let result = countdown - now, r = result;
								if(result<=0){
									$('.goodsView .time span').html('00:00:00');
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
								$('.goodsView .time span').html($.preZero(hour, 2)+':'+$.preZero(minute, 2)+':'+$.preZero(second, 2));
								now += 1;
							}
						timer = setInterval(countdownFn, 1000);
						countdownFn();
					}

					$('.cut').click(() => {
						this.$ajax.post('/api/chop/cut', { order_sn:this.order_sn }).then(json => {
							if (!$.checkError(json, this)) return
							$('.alertView .member').html('<i '+(json.data.avatar.length?'style="background-image:url('+json.data.avatar+');"':'')+'></i>'+json.data.memo);
							$('.alertView .price span').html($.round(json.data.price, 2));
							$(this.$el).append('<div class="chop-cut hidden" no-close="true"></div>');
							$.overlay('.chop-cut', 0, function(){
								let _this = this.addClass('this');
								setTimeout(function(){
									_this.removeClass('this');
									setTimeout(function(){
										$.overlay('.alertView');
										setTimeout(function(){ $('.chop-cut').remove() }, 500);
									}, 2000);
								}, 200);
							});
						});
					});
				}
			})
		})
	},
	beforeDestroy(){
		$(document.body).css('background-color', '')
	},
	methods: {
		jsApiCall(){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				this.jsApiParameters,
				res => {
					WeixinJSBridge.log(res.err_msg)
					if (res.err_msg === 'get_brand_wcpay_request:ok') {
						this.$router.push({path:'/cart/order_complete', query:{order_sn:this.order_sn}})
					}
				}
			)
		},
		callpay(){
			if (typeof WeixinJSBridge === 'undefined') {
				if (document.addEventListener) {
					document.addEventListener('WeixinJSBridgeReady', this.jsApiCall, false)
				} else if (document.attachEvent) {
					document.attachEvent('WeixinJSBridgeReady', this.jsApiCall)
					document.attachEvent('onWeixinJSBridgeReady', this.jsApiCall)
				}
			} else {
				this.jsApiCall()
			}
		}
	}
}
</script>

<style scoped>
.chop-detail .goodsView .goods .pic{background-image:none;}
</style>