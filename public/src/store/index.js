import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

export default new Vuex.Store({
	state: {
		transitionName: 'slide-left'
	},
	//this.$store.commit('set', 'item', itemValue)
	mutations: {
		transition(state, value){
			state.transitionName = value
		},
		set(state, obj){
			state[obj.key] = obj.value
		},
		delete(state, key){
			if (typeof state[key] !== 'undefined') delete state[key]
		}
	}
})
