<template>
<div>
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x">申请退款</div>
	</div>

	<template v-if="data">
	<div class="order-refund">
		<ul>
			<li class="ge-bottom ge-light"><strong>退款类型</strong></li>
			<li class="w l ge-bottom ge-light">
				<div class="ge-bottom ge-light" type="1">我要退款 <span>(无需退货)</span></div>
				<div type="2">我要退货</div>
			</li>

			<li class="m ge-bottom ge-light"><strong>退款/货原因</strong></li>
			<li class="w r ge-bottom ge-light"><select v-model="reason" id="reason">
				<option v-for="(g, i) in reasons" :value="i === 0 ? '' : g">{{ g }}</option>
			</select></li>

			<li class="m ge-bottom ge-light type0"><strong>退款金额</strong></li>
			<li class="w ge-bottom ge-light type0"><input type="tel" v-model="price" id="price" placeholder="请输入退款金额" readonly /></li>

			<li class="m ge-bottom ge-light"><strong>退款/货说明</strong> <span>(可不填)</span></li>
			<li class="w ge-bottom ge-light"><input type="text" v-model="memo" id="memo" placeholder="请输入退款说明" /></li>
		</ul>
		<div class="view type1">
			<span>上传凭证 最多3张</span>
			<ul></ul>
			<input type="file" name="filename" multiple />
		</div>
		<div class="buttonView">
			<a href="javascript:void(0)" class="btn pass">提交申请</a>
		</div>
	</div>
	</template>
</div>
</template>

<script>
export default {
	name: 'refund',
	data() {
		return {
			id: 0,
			data: null,
			total_price: 0,
			images: [],
			reasons: ['请选择退款原因', '卖家态度差', '物流太慢了', '货品损坏或缺少', '不想买了'],

			type: '',
			reason: '',
			price: '',
			pics: '',
			memo: '',
		}
	},
	created(){
		let id = this.$route.query.id
		if (!id) {
			alert('missing id')
			this.$router.go(-1)
			return
		}
		this.id = id
		this.$ajax.get('/api/order/refund', {id:id}).then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.total_price = Number(json.data.total_price)
			this.price = String(this.total_price)
			let vm = this
			this.$nextTick(() => {
				vm.changeType($('.l div:eq(0)'));
				$('.l div').click(function(){
					vm.changeType($(this));
				});
				$('.view').on('change', ':file', function(){
					let files = this.files;
					if(files.length + vm.images.length > 3){
						$.overloadError('最多只能上传3张图片');
						return;
					}
					let images = [];
					$(this).ajaxupload({
						url : '/api/other/uploadfile',
						data : { dir:'refund' },
						rightnow : true,
						callback : function(json){
							images.push(json.data);
							vm.images.push(json.data);
							if(files.length===images.length){
								$('.view span').hide();
								let html = '';
								for(let i=0; i<vm.images.length; i++){
									html += '<li style="background-image:url('+vm.images[i]+');"></li>';
								}
								$('.view ul').html(html).show();
							}
						}
					});
				});
				$('.buttonView .btn').click(() => {
					if (!this.type.length) {
						$.overloadError('请选择退款类型')
						return
					}
					if (!this.reason.length) {
						$.overloadError('请选择退款原因')
						return
					}
					if (!this.price.length) {
						$.overloadError('请输入退款金额')
						return
					}
					if (this.price > this.total_price) {
						$.overloadError('退款金额不能大于订单金额')
						return
					}
					if (Number(this.type)===2) {
						if(!this.images.length){
							$.overloadError('请选择图片凭证');
							return
						}
						this.pics = JSON.stringify(this.images)
					}
					if (!this.memo.length) {
						//$.overloadError('请输入退款说明')
						//return
					}
					this.$ajax.post('/api/order/refund', {id:this.id, type:this.type, reason:this.reason, price:this.price, pics:this.pics, memo:this.memo}).then(json => {
						if (!$.checkError(json, this)) return
						$.overload('申请已提交，请耐心等待商家处理', 5000, () => {
							this.$router.go(-1)
						})
					})
				});
			})
		})
	},
	methods: {
		changeType(div){
			$('.l div').find('i').remove();
			let type = div.prepend('<i></i>').attr('type')
			this.type = type
			if(type === '1'){
				//$('.type0').slideDown(300);
				$('.type1').slideUp(300);
			}else{
				//$('.type0').slideUp(300);
				$('.type1').slideDown(300);
			}
		}
	}
}
</script>

<style scoped>
.order-refund li select{display:block; width:100%; height:100%; font-size:0.12rem; border:none; background-color:transparent; -webkit-appearance:none; appearance:none;}
</style>