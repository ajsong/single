<template>
<div>
	<div class="navBar">
		<!--<a class="left" href="javascript:history.back()"><i class="return"></i></a>-->
		<div class="titleView-x">购物车</div>
	</div>

	<div :class="['cart-index', {'padding-bottom':($.isArray(data) && data.length)}]">
		<template v-if="$.isArray(data) && data.length">
		<section>
			<!--<ul class="tableView" v-for="s in data">-->
				<section v-for="s in data">
					<header v-if="Number(s.shop_id) > 0">
						<router-link :to="{path:'/shop/detail', query:{id:s.shop_id}}">
							<div :style="{'background-image':(s.shop_avatar ? 'url('+s.shop_avatar+')' : '')}"></div>{{ s.shop_name }}
						</router-link>
					</header>
					<ul class="tableView tableView-light" v-for="g in s.goods">
						<li :row="g.goods_id">
							<div class="view">
								<a href="javascript:void(0)" class="act"></a>
								<div class="tick"><input type="checkbox" name="checkbox" :id="'tick'+g.id" :value="g.id" /><label :for="'tick'+g.id"></label></div>
								<router-link ref="pic" class="pic" :to="{path:'/goods/detail', query:{id:g.goods_id}}" :url="g.pic"></router-link>
								<div class="info">
									<router-link class="name" :to="{path:'/goods/detail', query:{id:g.goods_id}}">{{ g.name }}</router-link>
									<div class="spec" v-html="g.spec_name?g.spec_name:''"></div>
									<div class="stock_alert" v-if="g.stock_alert_number >= g.stocks">库存紧张</div>
									<div class="price_change" v-if="g.cart_price!==g.price">比加入时<template v-if="g.cart_price>g.price">降{{ g.cart_price-g.price|round }}</template><template v-else>升{{ g.price-g.cart_price|round }}</template>元</div>
									<div class="price clear-after">
										<span>×{{ g.quantity }}</span>
										<div :price="g.price">￥{{ g.price|round }}</div>
									</div>
								</div>
								<div class="edit hidden">
									<a href="javascript:void(0)" class="ok"><span>完成</span></a>
									<div class="num">
										<div>
											<input type="hidden" class="cart_id" :value="g.id" />
											<input type="hidden" class="pic" :value="g.pic" />
											<input type="hidden" class="goods_id" :value="g.goods_id" />
											<input type="hidden" class="spec" :value="g.spec" />
											<input type="hidden" class="spec_name" :value="g.spec_name" />
											<input type="hidden" class="price" :value="g.price" />
											<input type="hidden" class="stocks" :value="g.stocks" />
											<a href="javascript:void(0)" class="plus ge-left">+</a>
											<a href="javascript:void(0)" class="minus ge-right">-</a>
											<input type="tel" class="quantity" :value="g.quantity" val="{$g->quantity}" />
										</div>
									</div>
									<a href="javascript:void(0)" class="spec-param" v-if="g.spec_name"><span>{{ g.spec_name }}</span></a>
								</div>
							</div>
						</li>
					</ul>
				</section>
			<!--</ul>-->
		</section>
		<div class="bottomView toolBar ge-top">
			<a class="btn" href="javascript:void(0)">去结算(0)</a>
			<div>总计：<span>￥0.00</span></div>
			<a class="all" href="javascript:void(0)">全选</a>
		</div>
		</template>
		<template v-else>
		<template v-if="goods">
		<div class="norecord"><div></div><span>购物车还是空的哦！</span><router-link to="/category">去逛逛</router-link></div>
		<div class="recommend">
			<div class="tip ge-top"><font class="gr">猜你喜欢</font></div>
			<ul class="list goods-item">
				<li v-for="g in goods">
					<router-link :to="{path:'/goods/detail', query:{id:g.id}}">
						<div ref="pic" class="pic" :url="g.pic"></div>
						<div class="title"><div>{{ g.name }}</div><font><template v-if="g.purchase_price > 0">正在秒杀中</template></font><span><strong>￥{{ g.price|round }}</strong><s>￥{{ g.market_price|round }}</s></span></div>
					</router-link>
				</li>
			</ul>
		</div>
		</template>
		</template>
	</div>

	<div class="goods-group goods-spec">
		<div class="picView">
			<div>
				<a href="javascript:void(0)"><b>⊗</b></a>
				<a href="javascript:void(0)" class="pic"></a>
				<strong></strong>
				<font></font>
				<span></span>
			</div>
		</div>
		<div class="specView"></div>
		<div class="btnView">
			<a class="btn ok" href="javascript:void(0)">确定</a>
			<a class="btn detail" href="javascript:void(0)">查看详情</a>
		</div>
	</div>
</div>
</template>

<script>
export default {
	name:'cart',
	data() {
		return {
			data: null,
			goods: null
		}
	},
	created(){
		this.$ajax.get('/api/cart').then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.$nextTick(() => {
				let vm = this
				$(this.$refs.pic).loadbackground('url', '80%', '../images/nopic.png')
				$('.bottomView span, .info .price div').priceFont('bigPrice')
				let checked = 0
				$(':checkbox').each(function(){
					if ($(this).is(':checked')) checked++
				});
				if ($(':checkbox').length === checked) {
					$('.all').addClass('all-x')
				} else {
					$('.all').removeClass('all-x')
				}
				this.setTotal()
				$(':checkbox').change(function(){
					let checked = 0, unchecked = 0;
					$(':checkbox').each(function(){
						if($(this).is(':checked'))checked++;
						else unchecked++;
					});
					if($(':checkbox').length === checked){
						$('.all').addClass('all-x');
					}else{
						$('.all').removeClass('all-x');
					}
					vm.setTotal()
				});
				$('.all').click(function(){
					let _this = $(this);
					if(_this.hasClass('all-x')){
						_this.removeClass('all-x');
						$(':checkbox').prop('checked', false);
					}else{
						_this.addClass('all-x');
						$(':checkbox').prop('checked', true);
					}
					vm.setTotal();
				});
				$('.view .act').click(function(){
					$(this).siblings('.edit').removeClass('hidden');
				});
				$('.edit .ok').click(function(){
					$(this).parent().addClass('hidden');
				});
				$('.plus').click(function(){
					let parent = $(this).parent(), quantity = parent.find('.quantity'), stocks = parent.find('.stocks');
					if(Number(quantity.val())+1 > Number(stocks.val())){
						vm.$emit('overloaderror', '该商品规格的库存只剩下'+stocks.val()+'件');
						return;
					}
					quantity.val(Number(quantity.val())+1);
					let view = parent.parent().parent().prev(), num = view.find('span');
					num.html('×'+quantity.val());
					vm.setTotal();
					vm.setCart(parent, 1);
				});
				$('.minus').click(function(){
					let parent = $(this).parent(), quantity = parent.find('.quantity');
					if(Number(quantity.val()) <= 1)return;
					quantity.val(Number(quantity.val())-1);
					let view = parent.parent().parent().prev(), num = view.find('span');
					num.html('×'+quantity.val());
					vm.setTotal();
					vm.setCart(parent, -1);
				});
				$('.quantity').focus(function(){
					let parent = $(this).parent(), quantity = $(this), stocks = parent.find('.stocks');
					if(quantity.val().length && !isNaN(quantity.val()) && Number(quantity.val())<=Number(stocks.val()))quantity.data('value', quantity.val());
				}).blur(function(){
					let parent = $(this).parent(), quantity = $(this), stocks = parent.find('.stocks');
					if(!quantity.val().length || isNaN(quantity.val())){
						vm.$emit('overloaderror', '请输入数量');
						quantity.focus().select();
						return;
					}
					if(Number(quantity.val()) > Number(stocks.val())){
						vm.$emit('overloaderror', '该商品规格的库存只剩下'+stocks.val()+'件');
						quantity.val(quantity.data('value'));
						quantity.focus().select();
						return;
					}
					let view = parent.parent().parent().prev(), num = view.find('span');
					num.html('×'+quantity.val());
					vm.setTotal();
					vm.setCart(parent);
				});
				$('.edit .spec-param').click(function(){
					let parent = $(this).prev(), goods_id = parent.find('.goods_id').val(), pic = parent.find('.pic').val(),
						price = parent.find('.price').val(), stocks = parent.find('.stocks').val(), spec = parent.find('.spec').val(), spec_name = parent.find('.spec_name').val();
					vm.$emit('overload')
					vm.$ajax.get('/api/goods/get_specs', { goods_id:goods_id }).then(json => {
						if(!$.checkError(json, vm)) return
						vm.$emit('overload', false)
						$('.picView .pic').attr('url', pic).css('background-image', 'url('+pic+')');
						$('.picView strong').attr('price', price).html('￥'+$.round(price, 2));
						$('.picView font').attr('stocks', stocks).html('库存'+stocks+'件');
						let specId = spec.split(','), specName = spec_name.split(';'), html = '';
						$('.picView span').attr('spec_name', spec_name).html('已选 "'+specName.join('" "')+'"');
						$('.specView').attr('goods_id', goods_id);
						if($.isArray(json.data)){
							$.each(json.data, function(i, g){
								html += '<div class="specGroup clear-after ge-top ge-light">\
						<div>'+g.name+'</div>';
								if($.isArray(g.sub)){
									$.each(g.sub, function(k, s){
										html += '<a href="javascript:void(0)" spec_id="'+s.id+'" '+($.inArray(s.id, specId)>-1?'class="this"':'')+'><span>'+s.name+'</span></a>';
									});
								}
								html += '</div>';
							});
							$('.specView').html(html);
						}
						$('.goods-spec').presentView();
					});
				});
				$('body').on('click', '.specGroup a', function(){
					$(this).addClass('this').siblings('a').removeClass('this');
					let spec_id = [], spec_name = [], count = 0, groupCount = $('.specGroup').length, goods_id = $('.specView').attr('goods_id');
					$('.specGroup a.this').each(function(){
						if(!!!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0)return false;
						spec_id.push($(this).attr('spec_id'));
						spec_name.push($(this).text());
						count++;
					});
					if(count!==groupCount)return;
					vm.$ajax.get('/api/goods/get_spec', { goods_id:goods_id, spec:spec_id.join(',') }).then(json => {
						if(!$.checkError(json, vm)) return
						if(!$.isPlainObject(json.data))return;
						if(json.data.pic.length)$('.picView .pic').attr('url', json.data.pic).css('background-image', 'url('+json.data.pic+')');
						$('.picView strong').attr('price', json.data.price).html('￥'+$.round(json.data.price, 2));
						$('.picView font').attr('stocks', json.data.stocks).html('库存'+json.data.stocks+'件');
						$('.picView span').attr('spec_name', spec_name.join(';')).html('已选 "'+spec_name.join('" "')+'"');
					});
				});
				$('.goods-spec .detail').click(function(){
					vm.$router.push({path:'/goods/detail', query:{id:$('.specView').attr('goods_id')}})
				});
				$('.goods-spec .ok').click(function(){
					let spec_id = [], count = 0, groupCount = $('.specGroup').length, goods_id = $('.specView').attr('goods_id');
					$('.specGroup a.this').each(function(){
						if(!$(this).attr('spec_id') || Number($(this).attr('spec_id'))<=0)return false;
						spec_id.push($(this).attr('spec_id'));
						count++;
					});
					if(count!==groupCount)return;
					if($('[row="'+goods_id+'"] input.spec').val()===spec_id.join(',')){
						$('.goods-spec').presentView(false);
						return;
					}
					let goods = JSON.stringify([{ goods_id:goods_id, quantity:$('[row="'+goods_id+'"] input.quantity').val(), spec:spec_id.join(',') }]);
					vm.$ajax.post('/api/cart/add', { cart_id:$('[row="'+goods_id+'"] input.cart_id').val(), goods:goods }).then(json => {
						if(!$.checkError(json, vm)) return
						let price = $('.picView strong').attr('price'), stocks = $('.picView font').attr('stocks'), spec_name = $('.picView span').attr('spec_name'),
							pic = $('.picView .pic').attr('url');
						$('[row="'+goods_id+'"] .price div:last').attr('price', price).html('￥'+$.round(price, 2)).priceFont('bigPrice');
						$('[row="'+goods_id+'"] div.spec').html(spec_name);
						$('[row="'+goods_id+'"] .spec-param span').html(spec_name);
						$('[row="'+goods_id+'"] a.pic').css('background-image', 'url('+pic+')');
						$('[row="'+goods_id+'"] input.pic').val(pic);
						$('[row="'+goods_id+'"] input.price').val(price);
						$('[row="'+goods_id+'"] input.stocks').val(stocks);
						$('[row="'+goods_id+'"] input.spec').val(spec_id.join(','));
						$('[row="'+goods_id+'"] input.spec_name').val(spec_name);
						$('.goods-spec').presentView(false);
						vm.setTotal();
					});
				});
				$('body').on('click', '.picView a:eq(0)', function(){
					$('.goods-spec').presentView(false);
				});

				$('.cart-index .tableView').dragshow({
					cls : 'delBtn',
					click : function(row){
						vm.$ajax.post('/api/cart/delete', { cart_id:row.find('.cart_id').val() }).then(() => {
							if(!$.checkError(json, vm)) return
							let section = row.parent().parent();
							if(section.parent().find('section').length===1 && section.find('li').length===1){
								vm.$router.go(0);
								return;
							}
							if(section.find('li').length===1){
								section.slideUp(300, function(){ section.remove() });
							}else{
								row.parent().slideUp(300, function(){ row.parent().remove() });
							}
							setTimeout(function(){ vm.setTotal() }, 300);
						});
					}
				});
				$('.bottomView .btn').click(function(){
					if(!$.checklogin())return;
					if(!$(':checked').length){
						vm.$emit('overloaderror', '请选择需要购买的商品');
						return;
					}
					let type = 1, parent_id = 0, integral_order = 0
					let goods = [];
					$(':checked').each(function(){
						let parent = $(this).parent().parent();
						goods.push({ goods_id:parent.find('input.goods_id').val(), quantity:Number(parent.find('input.quantity').val()), spec:parent.find('input.spec').val() });
					});
					goods = JSON.stringify(goods)
					vm.$router.push({name:'cartCommit', query:{
							type,
							parent_id,
							goods,
							integral_order
						}
					})
				});

				if(!$.isArray(json.data) || !json.data.length){
					vm.$ajax.get('/api/goods').then(json => {
						if(!$.checkError(json, vm)) return
						if($.isArray(json.data.goods)){
							vm.goods = json.data.goods
							vm.$nextTick(() => {
								$(vm.$refs.pic).loadbackground('url', '50%', '../images/nopic.png');
							})
						}
					});
				}
			})
		})
	},
	methods:{
		setTotal(){
			let total = 0, num = 0
			$(':checked').each(function(){
				let parent = $(this).parent().parent(), quantity = parent.find('.quantity'), price = parent.find('input.price')
				total += Number(price.val()) * Number(quantity.val())
				num += Number(quantity.val())
			});
			$('.bottomView span').html('￥'+$.round(total, 2)).priceFont('bigPrice')
			$('.bottomView .btn').html('去结算('+num+')')
		},
		setCart(parent, num){
			let isEdit = true, quantity = parent.find('.quantity').val();
			if($.isNumeric(num)){
				isEdit = false;
				quantity = num;
			}
			let goods = JSON.stringify([{ goods_id:parent.find('.goods_id').val(), quantity:quantity, spec:parent.find('.spec').val() }]);
			this.$ajax.post('/api/cart/add', { goods:goods, edit:isEdit?1:0 }).then(json => {
				$.checkError(json, this)
			});
		}
	}
}
</script>

<style>
#div{ transform:translateY(-100px)}
</style>