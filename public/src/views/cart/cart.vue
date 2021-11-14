<template>
	<div class="cart">
		<div class="div">
			<a class="a1" href="javascript:void(0)"></a>
			<a class="a2" href="javascript:void(0)"></a>
			<div class="div3">
				<a class="c1" href="javascript:void(0)"></a>
				<a class="c2" href="javascript:void(0)"></a>
				<div class="div4">
					<a class="d1" href="javascript:void(0)"></a>
					<a class="d2" href="javascript:void(0)" data-type="link"></a>
				</div>
			</div>
		</div>
		<div class="div2">
			<a class="b1" href="javascript:void(0)" data-type></a>
			<a class="b2" href="javascript:void(0)"></a>
		</div>
		<a class="e1" href="javascript:void(0)" data-type></a>
	</div>
</template>

<script>
export default {
	name:'cart',
	data() {
		return {

		}
	},
	mounted(){
		this.$nextTick(() => {
			//console.log($('.cart .div a').length)
			//$('.cart div a')
			console.log($('.cart').children('div').find("a[data-type='link']").selector)
		})
	}
}
</script>

<style>
#div{ transform:translateY(-100px)}
</style>