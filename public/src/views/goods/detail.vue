<template>
<div>
	<div ref="navBar" class="navBar navBar-hidden">
		<a class="left" href="javascript:history.back()"><i class="return-back"></i></a>
		<div class="titleView-x">商品详情</div>
	</div>

	<div class="goods-detail main-bottom width-wrap" v-if="data">
		<div class="pics">
			<slideView v-if="isArray(data.pics) && data.pics.length" :list="data.pics" :height="slideHeight" :step="0" :photoBrowser="true"></slideView>
			<div v-else ref="pic" class="pic" :url="data.pic"></div>
		</div>

		<div class="detail">
			<template v-if="integral===0">
			<div class="groupbuy clear-after" v-if="data.groupbuy_show === 1">
				<div class="groupbuyView">
					<div class="groupbuyInfo">已团{{ data.groupbuy_count }}件 - {{ data.groupbuy_number }}人团</div>
					<template v-if="data.groupbuy_end_time > 0">
					<div class="countdown">距结束 <i>00天00时00分00秒</i></div>
					<input type="hidden" class="nowValue" :value="data.groupbuy_now" />
					<input type="hidden" class="countdownValue" :value="data.groupbuy_end_time" />
					</template>
				</div>
				<div class="price">
					<div class="clear-after">
						<span>￥{{ data.price|round }}</span>
						<div class="side">
							<s>￥{{ data.market_price|round }}</s>
							<label><i>限量{{ data.groupbuy_amount }}件</i><b><div>好货限拼，超级低价</div></b></label>
						</div>
					</div>
				</div>
			</div>
			<div class="purchase clear-after" v-else-if="data.purchase_show === 1">
				<div class="purchaseView">
					<div class="progress">
						<div :style="{'width':round(data.purchase_count/data.purchase_amount*100, 1)+'%'}"></div>
						<span>已抢{{ (data.purchase_count/data.purchase_amount*100)|round(1) }}%</span>
					</div>
					<template v-if="data.purchase_end_time > 0">
					<div class="countdown">距结束 <i>00天00时00分00秒</i></div>
					<input type="hidden" class="nowValue" :value="data.purchase_now" />
					<input type="hidden" class="countdownValue" :value="data.purchase_end_time" />
					</template>
				</div>
				<div class="price">
					<div class="clear-after">
						<span>￥{{ data.price|round }}</span>
						<div class="side">
							<s>￥{{ data.origin_price|round }}</s>
							<label><i>限量{{ data.purchase_amount }}件</i><b><div><template v-if="data.purchase_end_time > 0">限时秒杀</template><template v-else>正在秒杀</template></div></b></label>
						</div>
					</div>
				</div>
			</div>
			<div class="chop clear-after" v-else-if="data.chop_show === 1">
				<div class="chopView">
					<div class="chopInfo">已砍{{ data.chop_count }}件</div>
					<template v-if="data.chop_end_time > 0">
					<div class="countdown">距结束 <i>00天00时00分00秒</i></div>
					<input type="hidden" class="nowValue" :value="data.chop_now" />
					<input type="hidden" class="countdownValue" :value="data.chop_end_time" />
					</template>
				</div>
				<div class="price">
					<div class="clear-after">
						<span>￥{{ data.price|round }}</span>
						<div class="side">
							<s>￥{{ data.origin_price|round }}</s>
							<label><i>限量{{ data.chop_amount }}件</i><b><div>限时第一刀半价！</div></b></label>
						</div>
					</div>
				</div>
			</div>
			<div class="price" v-else>
				<div class="clear-after">
					<span>￥{{ data.price|round }}</span>
					<div class="side">
						<s>原价￥{{ data.market_price|round }}</s>
					</div>
				</div>
			</div>
			<div class="name">{{ data.name }}</div>
			<div class="addition">
				<div class="description" v-if="data.description">{{ data.description }}</div>
			</div>
			<div class="shop" v-if="data.in_shop === 1">
				<div class="shop-img"></div>
				<div class="shop-info">此商品线下门店有售</div>
			</div>
			</template>

			<template v-else>
			<div class="integral">{{ data.integral }}积分</div>
			<div class="name">{{ data.name }}</div>
			<div class="addition">
				<div class="description" v-if="data.description">{{ data.description }}</div>
			</div>
			</template>
			<ul class="clear-after">
				<li>月销{{ data.sales }}笔</li>
				<li>人气{{ data.clicks }}</li>
				<li>库存{{ data.stocks }}</li>
				<li v-if="data.shop">{{ data.shop.province }}{{ data.shop.city }}</li>
			</ul>
		</div>

		<div class="groupbuyList" v-if="data.groupbuy_show===1 && isArray(data.groupbuy_list) && data.groupbuy_list.length">
			<div class="title ge-bottom">{{ data.groupbuy_list.length }}位小伙伴正在开团，您可直接参与</div>
			<ul>
				<li v-for="g in data.groupbuy_list">
					<a href="javascript:void(0)" :parent_id="g.id">去拼团</a>
					<div class="info">还差 {{ g.remain }} 人成团<div>剩余 <i>00:00:00</i></div></div>
					<div class="avatar" :style="{'background-image':g.avatar ? 'url('+g.avatar+')' : ''}"></div>
					{{ g.name }}
					<input type="hidden" class="groupbuy_list_now" :value="g.groupbuy_now" />
					<input type="hidden" class="groupbuy_list_countdown" :value="g.groupbuy_end_time" />
				</li>
			</ul>
		</div>

		<a href="javascript:void(0)" class="param-list coupons push-ico clear-before" @click="showCoupon" v-if="integral===0 && isArray(data.coupons)">
			<font>优惠</font>
			<span class="sub-list selected"><strong>优惠券</strong><template v-for="g in data.coupons">{{ g.name }} </template></span>
			<!--<span class="sub-list selected"><strong>本店活动</strong>满2件打7.5折</span>-->
		</a>

		<a href="javascript:void(0)" class="param-list params push-ico" @click="showParam" v-if="isArray(data.params)">
			<font>参数</font>
			<span class="selected"><template v-for="g in data.params">{{ g.name }} </template></span>
		</a>

		<template v-if="integral===0">
		<a href="javascript:void(0)" class="param-list spec-param push-ico" @click="showSpec" v-if="data.spec">
			<font>规格</font>
			<span>选择{{ data.spec }}分类</span>
		</a>

		<router-link :to="{path:'/comment', query:{goods_id:data.id, pagesize:10}}" class="comments push-ico">
			<font>查看评论</font><div>用户评价</div><span>({{ data.comments }})</span>
		</router-link>
		</template>

		<div class="memo">
			<div class="tip">商品详情</div>
			<div class="content" v-html="data.content"></div>
		</div>

		<div class="bottomView toolBar ge-top" v-if="integral===0">
			<template v-if="data.groupbuy_show === 1">
			<a class="btn groupbuyBtn groupbuy" href="javascript:void(0)">￥{{ data.price|round }}<i>{{ data.groupbuy_number }}人拼团</i></a>
			<a class="btn groupbuyBtn buy" href="javascript:void(0)">￥{{ data.origin_price|round }}<i>单独购买</i></a>
			</template>
			<template v-if="data.chop_show === 1">
			<a class="btn chopBtn chop" href="javascript:void(0)">￥{{ data.price|round }}<i>发起砍价</i></a>
			<a class="btn chopBtn buy" href="javascript:void(0)">￥{{ data.origin_price|round }}<i>直接购买</i></a>
			</template>
			<template v-else>
				<a class="btn buy" href="javascript:void(0)"><template v-if="data.purchase_show === 1">立即秒杀</template><template v-else>立即购买</template></a>
			<a class="btn add_cart" href="javascript:void(0)" @click.self="addCart">加入购物车</a>
			</template>
			<!--<a class="im" href="javascript:void(0)" member_id="xxx"></a>-->
			<a :class="['fav', {'fav-x':data.favorited === 1}]" href="javascript:void(0)"></a>
			<router-link class="cart badge" to="/cart"><sub v-html="cartQuantity"></sub></router-link>
		</div>
		<a class="integralBtn" href="javascript:void(0)" @click="integralBuy" v-else>立即兑换</a>
	</div>

	<div class="goods-group goods-spec" v-if="data && data.spec">
		<div class="picView">
			<div>
				<a href="javascript:void(0)" @click="$('.goods-spec').presentView(false)"><b>⊗</b></a>
				<a class="pic" ref="specpic" :href="data.pics[0].pic" :url="data.pics[0].pic" v-if="isArray(data.pics) && data.pics.length"></a>
				<a class="pic" ref="specpic" :href="data.pic" :url="data.pic" v-else></a>
				<strong>￥{{ data.price|round }}</strong>
				<font>库存{{ data.stocks }}件</font>
				<span>选择{{ data.spec }}分类</span>
			</div>
		</div>
		<div class="specView">
			<div class="specGroup clear-after ge-top ge-light" v-for="g in data.specs">
				<div>{{ g.name }}</div>
				<a href="javascript:void(0)" v-for="s in g.sub" :spec_id="s.id"><span @click="selectSpec">{{ s.name }}</span></a>
			</div>
			<div class="specQuantity ge-top ge-light" v-if="data.groupbuy_show === 0 && data.chop_show === 0">
				<div>
					<a href="javascript:void(0)" class="minus" @click="minusQuantity">-</a>
					<input type="tel" id="quantity" v-model="quantity" :stocks="data.stocks" @blur="setQuantity" />
					<a href="javascript:void(0)" class="plus" @click="plusQuantity">+</a>
				</div>
				<span>购买数量</span>
			</div>
		</div>
		<div class="btnView">
			<template v-if="data.groupbuy_show === 1">
			<a class="btn specBtn buy" href="javascript:void(0)">单独购买(￥{{ data.origin_price|round }})</a>
			<a class="btn specBtn groupbuy" href="javascript:void(0)">立即开团</a>
			<a class="btn specBtn mergebuy hidden" href="javascript:void(0)" @click="mergeBuy">立即拼团</a>
			</template>
			<template v-if="data.chop_show === 1">
			<a class="btn specBtn chopBtn buy" href="javascript:void(0)">直接购买(￥{{ data.origin_price|round }})</a>
			<a class="btn specBtn chop" href="javascript:void(0)">发起砍价</a>
			</template>
			<template v-else>
			<a class="btn add_cart spec_cart" href="javascript:void(0)" @click.self="addCart">加入购物车</a>
			<a class="btn buy" href="javascript:void(0)"><template v-if="data.purchase_show === 1">立即秒杀</template><template v-else>立即购买</template></a>
			</template>
		</div>
	</div>

	<div class="goods-group goods-coupons" v-if="data && isArray(data.coupons)">
		<div class="title">优惠券</div>
		<div class="coupons-view">
			<a href="javascript:void(0)" class="coupons-list" @click="lingCoupon(g.id)" v-for="g in data.coupons">
				<div class="text">立即抢</div>
				<div class="info">
					<div>￥<strong>{{ g.coupon_money }}</strong>{{ g.name }}</div>
					<span>{{ g.min_price_memo }}</span>
					<font>{{ g.time_memo }}</font>
				</div>
			</a>
		</div>
		<div class="btnView">
			<a class="btn close" href="javascript:void(0)" @click="$('.goods-coupons').presentView(false)">完成</a>
		</div>
	</div>

	<div class="goods-group goods-params" v-if="data && isArray(data.params)">
		<div class="title">产品参数</div>
		<div class="params-view">
			<div class="params-list ge-bottom ge-light" v-for="g in data.params">
				<font>{{ g.name }}</font>
				<span>{{ g.value }}</span>
			</div>
		</div>
		<div class="btnView">
			<a class="btn close" href="javascript:void(0)" @click="$('.goods-params').presentView(false)">完成</a>
		</div>
	</div>
</div>
</template>

<script>
import '../../../js/photoswipe/photoswipe.css'
import '../../../js/photoswipe/default-skin/default-skin.css'
import slideView from '../../components/slideView'
export default {
	name:'detail',
	data(){
		return {
			id: 0,
			data: null,
			integral: 0,
			cartQuantity: '',
			quantity: 1,
			slideHeight: ''
		}
	},
	components:{
		slideView
	},
	created(){
		let id = this.$route.query.id
		if (!id) {
			alert('missing id')
			this.$router.go(-1)
			return
		}
		this.id = id
		let _vm = this
		if (this.$route.query.integral) this.integral = Number(this.$route.query.integral)
		this.$ajax.get('/api/goods/detail', {id:id}).then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.$nextTick(() => {
				$(this.$refs.pic).loadbackground('url', '30%', '../images/nopic.png').photoBrowser()
				this.resize()
				if (this.integral===0){
					this.getCartTotal()
					$('.price span').priceFont('bigPrice')
				}
				$('.memo .content img').each(function(){
					if ($(this).width()>300) $(this).removeAttr('height').attr('width', '100%')
				})

				if ((this.data.groupbuy_show === 1 && this.data.groupbuy_end_time > 0) ||
					(this.data.purchase_show === 1 && this.data.purchase_end_time > 0) ||
					(this.data.chop_show === 1 && this.data.chop_end_time > 0)) {
					let countdown = Number($('.countdownValue').val()), now = Number($('.nowValue').val()), timer = null,
						countdownFn = () => {
							let result = countdown - now, r = result
							if (result<=0) {
								$('.countdown i').html('00天00时00分00秒')
								clearInterval(timer)
								this.$router.go(0)
								return
							}
							let day = Math.floor(r/(60*60*24))
							r = result - day*60*60*24
							let hour = Math.floor(r/(60*60))
							r -= hour*60*60
							let minute = Math.floor(r/60)
							r -= minute*60
							let second = r
							$('.countdown i').html($.preZero(day, 2)+'天'+$.preZero(hour, 2)+'时'+$.preZero(minute, 2)+'分'+$.preZero(second, 2)+'秒')
							now += 1
						}
					countdownFn()
					timer = setInterval(countdownFn, 1000)
				}

				if (this.data.groupbuy_show === 1 && $.isArray(this.data.groupbuy_list) && this.data.groupbuy_list.length) {
					$('.groupbuy_list_countdown').each((i, item) => {
						let _this = $(item), countdown = Number(_this.val()), now = Number(_this.prev().val()), timer = null,
							countdownFn = () => {
								let result = countdown - now, r = result
								if (result<=0) {
									_this.parent().find('.info i').html('00:00:00')
									clearInterval(timer)
									this.$router.go(0)
									return
								}
								let day = Math.floor(r/(60*60*24))
								r = result - day*60*60*24
								let hour = Math.floor(r/(60*60))
								r -= hour*60*60
								let minute = Math.floor(r/60)
								r -= minute*60
								let second = r
								_this.parent().find('.info i').html($.preZero(hour, 2)+':'+$.preZero(minute, 2)+':'+$.preZero(second, 2))
								now += 1
							}
						countdownFn()
						timer = setInterval(countdownFn, 1000)
					});
				}

				if (Number(this.data.purchase_show) === 1) {
					this.$ajax.get('/api/goods/get_spec', { goods_id:this.id }).then(json => {
						if (!$.checkError(json, this)) return
						if (!$.isPlainObject(json.data)) return
						let spec = json.data.spec.split(',')
						$.each(spec, function(){
							$('.specGroup a[spec_id="'+this+'"]').addClass('this')
						})
						let spec_name = [], count = 0, groupCount = $('.specGroup').length
						$('.specGroup a.this').each(function() {
							if (!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0) return false
							spec_name.push($(this).text())
							count++
						});
						if (count !== groupCount) return
						if (json.data.pic.length) $('.picView .pic').attr({href:json.data.pic, url:json.data.pic}).loadbackground('url', '100%', '../images/nopic.png').photoBrowser()
						$('.picView strong').html('￥'+$.round(json.data.price, 2))
						$('.picView font').html('库存'+json.data.stocks+'件')
						$('.picView span').html('已选 "'+spec_name.join('" "')+'"')
						$('#quantity').attr('stocks', json.data.stocks)
						$('.price span').html('￥'+$.round(json.data.price, 2)).priceFont('bigPrice')
						$('.spec-param span').addClass('selected').html('已选 "'+spec_name.join('" "')+'"')
					})
				}

				$('.im').click(function() {
					location.href = '/wap/?app=chat&act=talk&member_id='+$(e.target).attr('member_id')
				})

				$('.fav').click(function() {
					let _this = $(this)
					_vm.$ajax.post('/api/favorite/add', { item_id:_vm.id, type_id:1 }).then(json => {
						if (!$.checkError(json, _vm)) return
						_this.toggleClass('fav-x')
					})
				})

				$('.buy, .btn.groupbuy, .btn.chop').click(function() {
					_vm.buy($(this))
				})

				$('.groupbuyList li a, .mergebuy').click(function() {
					_vm.mergeBuy($(this))
				})

				$('.integralBtn').click(function() {
					_vm.integralBuy($(this))
				})
			})
		})
	},
	mounted(){
		window.addEventListener('scroll', this.scrollFunc)
		window.onresize = () => {
			this.resize()
		}
	},
	beforeDestroy(){
		window.removeEventListener('scroll', this.scrollFunc)
	},
	methods:{
		resize(){
			$('.goods-detail .pics, .goods-detail .pics .pic, .goods-detail .slideView').autoHeight(640, 640)
			this.slideHeight = $.autoHeight(640, 640, $('.goods-detail .pics'))
		},
		round(value, num){
			return $.round(value, num)
		},
		isArray(arr){
			return $.isArray(arr)
		},
		checkLogin(){
			let member = $.storageJSON('member')
			if(!member || !member.id){
				this.$router.push({path:'/login', query:{url:this.$route.fullPath}, hash:'#presentView'})
				return false
			}
			return true
		},
		getCartTotal(){
			this.$ajax.get('/api/cart/total').then(json => {
				if (!$.checkError(json, this)) return
				this.cartQuantity = json.data.quantity > 0 ? '<b>' + json.data.quantity + '</b>' : ''
			})
		},
		scrollFunc(){
			let scrollTop = document.documentElement.scrollTop || document.body.scrollTop
			let navBar = this.$refs.navBar
			//let topView = this.$refs.topView
			let className = navBar.className.replace(/\s*navBar-hidden/ig, '')
			//topView.clientHeight - navBar.clientHeight
			navBar.className = className + (scrollTop < navBar.clientHeight ? ' navBar-hidden' : '')
		},
		showCoupon(){
			$('.goods-coupons').presentView()
		},
		showParam(){
			$('.goods-params').presentView()
		},
		showSpec(){
			$('.goods-spec').presentView(() => {
				$(this.$refs.specpic).loadbackground('url', '100%', '../images/nopic.png')
				$('.specGroup').each((i, item) => {
					if ($(item).find('a').length === 1) this.selectSpec({target:$(item).find('a')[0]})
				})
			})
		},
		selectSpec(e){
			$(e.target.parentNode).addClass('this').siblings('a').removeClass('this')
			let spec_id = [], spec_name = [], count = 0, groupCount = this.data.specs.length
			$('.specGroup a.this').each((i, item) => {
				if (!!!$(item).attr('spec_id') || Number($(item).attr('spec_id'))<=0) return false
				spec_id.push($(item).attr('spec_id'))
				spec_name.push($(item).text())
				count++
			});
			if (count !== groupCount) return
			this.$ajax.get('/api/goods/get_spec', { goods_id:this.id, spec:spec_id.join(',') }).then(json => {
				if (!$.checkError(json, this)) return
				if (!$.isPlainObject(json.data)) return
				if (json.data.pic.length) $('.picView .pic').attr({href:json.data.pic, url:json.data.pic}).loadbackground('url', '100%', '../images/nopic.png').photoBrowser()
				$('.picView strong').html('￥'+$.round(json.data.price, 2))
				$('.picView font').html('库存'+json.data.stocks+'件')
				$('.picView span').html('已选 "'+spec_name.join('" "')+'"')
				$('#quantity').attr('stocks', json.data.stocks)
				$('.price span').html('￥'+$.round(json.data.price, 2)).priceFont('bigPrice')
				$('.spec-param span').addClass('selected').html('已选 "'+spec_name.join('" "')+'"')
			});
		},
		minusQuantity(){
			if (this.quantity <= 1) return
			this.quantity--
		},
		plusQuantity(){
			let stocks = Number($('#quantity').attr('stocks'))
			if (this.quantity > stocks) {
				this.$emit('overloaderror', '该商品规格的库存只剩下'+stocks+'件')
				return
			}
			this.quantity++
		},
		setQuantity(){
			if (isNaN(this.quantity)) {
				this.$emit('overloaderror', '请填写数量')
				$('#quantity').focus()
				return
			}
			let stocks = Number($('#quantity').attr('stocks'))
			if (this.quantity > stocks) {
				this.$emit('overloaderror', '该商品规格的库存只剩下'+stocks+'件')
				this.quantity = 1
				$('#quantity').focus()
			}
		},
		lingCoupon(id){
			this.$ajax.get('/api/coupon/ling', {id:id}).then(json => {
				if (!$.checkError(json, this)) return
				this.$emit('overloadsuccess', json.msg)
			})
		},
		addCart(e){
			let _vm = this, _this = $(e.target), spec_id = []
			if ($('.specGroup').length) {
				let count = 0, groupCount = $('.specGroup').length
				$('.specGroup a.this').each(function(){
					if (!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0) return false
					spec_id.push($(this).attr('spec_id'))
					count++
				});
				if (count !== groupCount) {
					if (!_this.parents('.goods-spec').length) this.showSpec()
					return
				}
			}
			if (this.quantity <= 0) {
				this.$emit('overloadError', '请填写数量')
				return
			}
			let goods = [{ goods_id:this.id, quantity:this.quantity, spec:spec_id.join(',') }]
			this.$ajax.post('/api/cart/add', { goods:JSON.stringify(goods) }).then(json => {
				if (!$.checkError(json, this)) return
				let dot = $('<div class="badge-dot"></div>'), sub = $('.badge sub')
				sub.show()
				let height = sub.height(), offset = sub.offset(), scrollTop = $(window).scrollTop()
				sub.css('display', '')
				dot.parabola({
					//maxTop: (document.documentElement.clientHeight || document.body.clientHeight) / 2,
					start : {
						left : _this.offset().left + _this.width()/2,
						top : _this.offset().top + _this.height()/2 - scrollTop,
						width : height,
						height : height
					},
					end : {
						left : offset.left,
						top : offset.top - scrollTop,
					},
					after : function(){
						_vm.quantity = 1
						_vm.cartQuantity = json.data.quantity > 0 ? '<b>' + json.data.quantity + '</b>' : ''
						this.remove()
					}
				});
			});
		},
		buy(_this){
			if (!this.checkLogin()) return
			let spec_id = [], parent_id = 0, type = 0, integral_order = this.integral
			$('.mergebuy').addClass('hidden').siblings().removeClass('hidden')
			if ($('.specGroup').length) {
				let count = 0, groupCount = $('.specGroup').length
				$('.specGroup a.this').each(function(){
					if (!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0) return false
					spec_id.push($(this).attr('spec_id'))
					count++
				});
				if (count !== groupCount) {
					if (!_this.parents('.goods-spec').length) this.showSpec()
					return
				}
			}
			if (this.quantity <= 0) {
				this.$emit('overloadError', '请填写数量')
				return
			}
			let goods = JSON.stringify([{ goods_id:this.id, quantity:this.quantity, spec:spec_id.join(',') }])
			if (_this.is('.buy') && _this.is('.specBtn')) {
				type = 0
			} else if (_this.is('.groupbuy')) {
				type = 1
			} else if (_this.is('.purchase')) {
				type = 2
			} else if (_this.is('.chop')) {
				type = 3
			}
			if (this.integral) type = 0
			this.$router.push({name:'cartCommit', query:{
					type,
					parent_id,
					goods,
					integral_order
				}
			})
		},
		mergeBuy(_this){
			if (!this.checkLogin()) return
			let spec_id = [], parent_id = 0, type = 1, integral_order = this.integral
			if (!_this.is('.mergebuy')) $('.mergebuy').attr('parent_id', _this.attr('parent_id'))
			parent_id = Number(_this.attr('parent_id'))
			this.quantity = 1
			$('.mergebuy').removeClass('hidden').siblings().addClass('hidden')
			if ($('.specGroup').length) {
				let count = 0, groupCount = $('.specGroup').length
				$('.specGroup a.this').each(function(){
					if (!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0) return false
					spec_id.push($(this).attr('spec_id'))
					count++
				});
				if(count !== groupCount){
					if (!_this.parents('.goods-spec').length) this.showSpec()
					return
				}
			}
			let goods = JSON.stringify([{ goods_id:this.id, quantity:this.quantity, spec:spec_id.join(',') }])
			this.$router.push({name:'cartCommit', query:{
					type,
					parent_id,
					goods,
					integral_order
				}
			})
		},
		integralBuy(){
			if (!this.checkLogin()) return
			let parent_id = 0, type = 0, integral_order = this.integral
			let goods = JSON.stringify([{ goods_id:this.id, quantity:1 }])
			this.$router.push({name:'cartCommit', query:{
					type,
					parent_id,
					goods,
					integral_order
				}
			})
		}
	}
}

//抛物线动画
$.fn.parabola = function(options){
	options = $.extend({
		maxTop: 20, //默认顶点高度top值
		speed: 0.6, //移动速度
		start: { }, //top, left, width, height
		end: { }, //参数同上
		after: null //动画完成后执行
	}, options);
	return this.each(function(){
		let _this = $(this), settings = $.extend({}, options), start = settings.start, end = settings.end;
		if(!_this.parent().length)$(document.body).append(_this);
		//运动过程中有改变大小
		if(start.width==null || start.height==null)start = $.extend(start, { width:_this.width(), height:_this.height() });
		_this.css({ margin:'0', position:'fixed', 'z-index':9999, width:start.width, height:start.height });
		//运动轨迹最高点top值
		let vertex_top = Math.min(start.top, end.top) - Math.abs(start.left - end.left) / 3;
		//可能出现起点或者终点就是运动曲线顶点的情况
		if(vertex_top < settings.maxTop)vertex_top = Math.min(settings.maxTop, Math.min(start.top, end.top));
		else vertex_top = Math.min(start.top, $(window).height()/2);
		//运动轨迹在页面中的top值可以抽象成函数 a = curvature, b = vertex_top, y = a * x*x + b;
		let distance = Math.sqrt(Math.pow(start.top - end.top, 2) + Math.pow(start.left - end.left, 2)),
			steps = Math.ceil(Math.min(Math.max(Math.log(distance) / 0.05 - 75, 30), 100) / settings.speed), //元素移动次数
			ratio = start.top === vertex_top ? 0 : -Math.sqrt((end.top - vertex_top) / (start.top - vertex_top)),
			vertex_left = (ratio * start.left - end.left) / (ratio - 1),
			//特殊情况, 出现顶点left==终点left, 将曲率设置为0, 做直线运动
			curvature = end.left === vertex_left ? 0 : (end.top - vertex_top) / Math.pow(end.left - vertex_left, 2);
		settings = $.extend(settings, {
			count: -1, //每次重置为-1
			steps: steps,
			vertex_left: vertex_left,
			vertex_top: vertex_top,
			curvature: curvature
		});
		move();
		function move(){
			let opt = settings, start = opt.start, count = opt.count, steps = opt.steps, end = opt.end;
			//计算left top值
			let left = start.left + (end.left - start.left) * count / steps,
				top = opt.curvature === 0 ? start.top + (end.top - start.top) * count / steps : opt.curvature * Math.pow(left - opt.vertex_left, 2) + opt.vertex_top;
			//运动过程中有改变大小
			if(end.width!==null && end.height!==null){
				let i = steps / 2,
					width = end.width - (end.width - start.width) * Math.cos(count < i ? 0 : (count - i) / (steps - i) * Math.PI / 2),
					height = end.height - (end.height - start.height) * Math.cos(count < i ? 0 : (count - i) / (steps - i) * Math.PI / 2);
				_this.css({ width:width, height:height, 'font-size':Math.min(width, height)+'px' });
			}
			_this.css({ left:left, top:top });
			opt.count++;
			let time = window.requestAnimationFrame(move);
			if (count === steps) {
				window.cancelAnimationFrame(time);
				if($.isFunction(opt.after))opt.after.call(_this);
			}
		}
	});
}
</script>

<style scoped>
.goods-spec .specView .specQuantity div a{text-decoration:none;}
</style>