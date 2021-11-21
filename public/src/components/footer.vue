<template>
<div class="footer">
	<router-link :class="['ico1', 'badge', {'this':index === 0}]" to="/" @click="setIndex(0)"><div v-if="badges[0]"><sub v-html="badges[0]"></sub></div></router-link>
	<router-link :class="['ico2', 'badge', {'this':index === 1}]" to="/category" @click="setIndex(1)"><div v-if="badges[1]"><sub v-html="badges[1]"></sub></div></router-link>
	<router-link :class="['ico3', 'badge', {'this':index === 2}]" to="/article" @click="setIndex(2)"><div v-if="badges[2]"><sub v-html="badges[2]"></sub></div></router-link>
	<router-link :class="['ico4', 'badge', {'this':index === 3}]" to="/cart" @click="setIndex(3)"><div v-if="badges[3]"><sub v-html="badges[3]"></sub></div></router-link>
	<router-link :class="['ico5', 'badge', {'this':index === 4}]" to="/member" @click="setIndex(4)"><div v-if="badges[4]"><sub v-html="badges[4]"></sub></div></router-link>
</div>
</template>

<script>
export default {
	name: 'tabController',
	data() {
		return {
			index: 0,
			badges: []
		}
	},
	created(){
		let badges = $.storageJSON('badges')
		if (badges) this.badges = badges
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
		},
		setBadge(index, html){
			let badges = $.storageJSON('badges')
			if (!badges || !(badges instanceof Array)) {
				badges = []
				let children = this.$el.childNodes
				for (let i = 0; i < children.length; i++) {
					if (children[i].nodeType === 1) badges.push('')
				}
			}
			for (let i = 0; i < badges.length; i++) {
				if (i === index) badges[i] = html
				else if (typeof badges[i] === 'undefined' || badges[i] === null) badges[i] = ''
			}
			$.storage('badges', badges)
			this.badges = badges
		}
	}
}
</script>

<style scoped>

</style>