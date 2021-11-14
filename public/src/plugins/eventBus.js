import Vue from 'vue'
//定义空Vue实例，作为实现非父子组件之间的传值
let eventBus = new Vue({})
export default eventBus

/*
A页面
import eventBus from '../eventBus.js'
//每次激活时
activated(){
    //根据key名获取传递回来的参数
    eventBus.$on('thekey', data => {
        //code
    })
}

B页面
import eventBus from '../eventBus.js'
eventBus.$emit('thekey', value)
//这时返回到A页面就能获取到值
this.$router.go(-1)
*/