<template>
<div>
	<template v-if="id > 0 && data">
	<div class="navBar">
		<a class="left" href="javascript:history.back()"><i class="return"></i></a>
		<div class="titleView-x" v-if="!data.mark">发现详情</div>
		<div class="titleView-x" v-else-if="data.mark === 'about'">关于我们</div>
		<div class="titleView-x" v-else-if="data.mark === 'help'">帮助中心</div>
		<div class="titleView-x" v-else-if="data.mark === 'useragree'">用户协议</div>
		<div class="titleView-x" v-else-if="data.mark === 'shopagree'">开店协议</div>
		<div class="titleView-x" v-else-if="data.mark === 'commission'">如何获得佣金？</div>
		<div class="titleView-x" v-else-if="data.mark === 'join'">招商加盟</div>
		<div class="titleView-x" v-else-if="data.mark === 'score'">积分规则</div>
		<div class="titleView-x" v-else-if="data.mark === 'coupon'">优惠券</div>
		<div class="titleView-x" v-else>详情</div>
	</div>

	<div class="discover-detail" v-if="!data.mark">
		<div class="pullRefresh">
			<div class="titleView gr">
				<div class="title">{{ data.title }}</div>
				<div class="time"><span>{{ data.add_time }}</span></div>
			</div>
			<div class="content" v-html="data.content"></div>
			<div class="zanView gr">
				<div class="view">
					<i :class="{'hidden':(!$.isArray(data.likes_list) || data.likes_list.length < 5)}"></i>
					<a href="javascript:void(0)" class="zan"><span>{{ data.likes }}</span></a>
					<ul>
						<template v-if="$.isArray(data.likes_list) && data.likes_list.length">
						<li v-for="g in data.likes_list" :member_id="g.member_id" class="scale-animate" :style="{'background-image':(g.avatar ? 'url('+g.avatar+')' : '')}"></li>
						</template>
					</ul>
				</div>
			</div>
			<ul class="goodsView" v-if="$.isArray(data.goods)">
				<li class="ge-bottom ge-light" v-for="g in data.goods">
					<router-link :to="{path:'/goods/detail', query:{id:g.id}}">
						<div :style="{'background-image':(g.pic ? g.pic : '')}"></div>
						<span><h1>{{ g.name }}</h1></span>
						<font><h1>￥{{ g.price }}</h1></font>
					</router-link>
				</li>
				<div class="qi"></div>
			</ul>
			<div class="commentView ge-bottom ge-light">用户评论 <span v-html="$.isArray(data.comments_list) ? '('+data.comments_list.length+')' : '(0)'"></span></div>
			<ul class="list">
				<template v-if="$.isArray(data.comments_list) && data.comments_list.length">
				<li class="ge-bottom ge-light" v-for="g in data.comments_list">
					<div class="infoView">
						<font>{{ g.add_time }}</font>
						<div :style="{'background-image':(g.avatar ? g.avatar : '')}"></div>
						<span>{{ g.member_name }}</span>
					</div>
					<div class="memo">{{ g.content }}</div>
				</li>
				</template>
			</ul>
		</div>

		<div class="commentPost toolBar ge-top">
			<div>
				<a href="javascript:void(0)"></a>
				<span><input type="text" name="content" id="content" v-model="comment" placeholder="请输入您的评论" @keydown.enter="sendComment" /></span>
			</div>
		</div>
	</div>
	<template v-else>
	<div :class="['article-detail', 'main-top', {'course':id === 12}]" v-html="data.content"></div>
	<div class="course-bottom" v-if="id === 12">
		<a v-if="member && member.id > 0" href="javascript:void(0)" class="confirms">确认教程</a>
		<router-link v-else to="/register" class="register">注册账号</router-link>
	</div>
	</template>
	</template>
</div>
</template>

<script>
import EmojiView from '../../../js/emojiView/emojiView'
import emojiJSON from '../../../js/emojiView/expression/expression.json'
export default {
	name:'articleDetail',
	data(){
		return {
			id: 0,
			data: null,
			member: null,
			comment: ''
		}
	},
	created(){
		let id = this.$route.query.id
		if (!id) {
			alert('missing id')
			this.$router.go(-1)
			return
		}
		$(document.body).removeClass('gr')
		this.id = id
		this.member = $.storageJSON('member')
		this.$ajax.get('/api/article/detail', {id:id}).then(json => {
			if (!$.checkError(json, this)) return
			this.data = json.data
			this.$nextTick(() => {
				let vm = this
				this.setEmoji();
				let emojiView = $.emojiView({
					emojiJSON: emojiJSON,
					selectFn : function(mark){
						$('#content').val($('#content').val()+mark);
					},
					deleteFn : function(){
						$('#content').deleteEmoji();
					},
					sendFn : function(){
						vm.sendComment();
					}
				});
				$('.commentPost a').click(function(){
					if(emojiView.isAppear){
						emojiView.close('.commentPost');
					}else{
						emojiView.show('.commentPost');
					}
				});
				$('.content img').each(function(){
					if($(this).width()>300)$(this).removeAttr('width').removeAttr('height').css({ width:'100%', height:'' });
				});
				$('.zan').click(function(){
					if(!$.checklogin())return
					let _this = $(this);
					vm.$ajax.post('/api/article/like', { article_id:vm.id }).then(json => {
						_this.find('span').html(json.data);
						let ul = _this.next(), li = ul.find('[member_id="'+vm.member.id+'"]');
						if(li.length){
							li.addClass('scale-animate-0');
							setTimeout(function(){ li.remove() }, 300);
						}else{
							li = $('<li member_id="'+vm.member.id+'" class="scale-animate scale-animate-0" '+(vm.member.avatar?'style="background-image:url('+vm.member.avatar+');"':'')+'></li>');
							ul.prepend(li);
							setTimeout(function(){ li.removeClass('scale-animate-0') }, 0);
						}
						if(ul.find('li').length>4){
							_this.prev().removeClass('hidden');
						}else{
							_this.prev().addClass('hidden');
						}
					});
				});
			})
		})
	},
	beforeDestroy(){
		$(document.body).addClass('gr')
	},
	methods:{
		setEmoji(){
			$('.list .memo').emojiView({
				emojiJSON: emojiJSON
			});
			//setTimeout(function(){ $('.list .memo').emojiView(true) }, 3000); //反解析
		},
		sendComment(){
			if(!$.checklogin())return;
			if(!this.comment.length){
				this.$emit('overloaderror', '请输入您的评论');
				return;
			}
			this.$ajax.post('/api/article/post_comment', {article_id:this.id, content:this.comment}).then(json => {
				if (!$.checkError(json, this)) return
				this.$router.go(0)
			})
		}
	}
}
</script>

<style scoped>

</style>