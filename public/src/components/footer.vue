<template>
	<div class="footer">
		<router-link :class="['ico1', {'this':index===0}]" to="/" @click.native="setIndex(0)"></router-link>
		<router-link :class="['ico2', {'this':index===1}]" to="/category" @click.native="setIndex(1)"></router-link>
		<router-link :class="['ico3', {'this':index===2}]" to="/article" @click.native="setIndex(2)"></router-link>
		<router-link :class="['ico4', {'this':index===3}]" to="/cart" @click.native="setIndex(3)"><!--<div>{if $cart_notify>0}<sub><b>{$cart_notify}</b></sub>{/if}</div>--></router-link>
		<router-link :class="['ico5', {'this':index===4}]" to="/member" @click.native="setIndex(4)"></router-link>
	</div>
</template>

<script>
export default {
	name: 'tabController',
	data() {
		return {
			index: 0
		}
	},
	created(){
		this.$router.options.routes.forEach(item => {
			let routePath = [this.$route.path.replace(/(^\/)|(\/$)/g, '')]
			let isMatch = false
			let paramOptional = false
			let itemPath = item.path.replace(/(^\/)|(\/$)/g, '').split('/')
			if (itemPath.length > 1) {
				routePath = this.$route.path.replace(/(^\/)|(\/$)/g, '').split('/')
				for (let path in itemPath) {
					if (/\??/.test(path)) {
						paramOptional = true
						break
					}
				}
				isMatch = (itemPath[0] === routePath[0]) && paramOptional
			}
			if (typeof item.index !== 'undefined') {
				if (item.path === this.$route.path || isMatch) this.index = item.index
			}
		})
	},
	methods: {
		setIndex(index) {
			this.index = index
		}
	}

}
</script>

<style scoped>

</style>