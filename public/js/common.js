(function($){
$.fn.priceFont = function(className){
	return this.each(function(){
		let _this = $(this), text = _this.text();
		if(!text.length || (text.indexOf('￥')==-1 && text.indexOf('.')==-1) || _this.children('.'+className).length)return true;
		if(text.indexOf('￥')>-1){
			let ar = text.split('￥'), prefix = ar[0]+'￥', arr = ar[1].split('.'), integer = arr[0], decimal = '';
			if(arr.length==2)decimal = '.'+arr[1];
			text = prefix+'<font class="'+className+'">'+integer+'</font>'+decimal;
		}else{
			let arr = text.split('.'), integer = arr[0], decimal = '';
			if(arr.length==2)decimal = '.'+arr[1];
			text = '<font class="'+className+'">'+integer+'</font>'+decimal;
		};
		_this.html(text);
	});
};
$.fn.checklogin = function(){
	return this.each(function(){
		let _this = $(this);
		if(_this.is('.return, .delete, .confirm, .skip, [href^="javascript:"], [href^="#"]'))return true;
		if(!!_this.data('checklogin'))return true;
		_this.data('checklogin', true).click(function(){
			return $.checklogin(_this.attr('href'), _this);
		});
	});
};
$.fn.checkHistoryBack = function(){
	return this.each(function(){
		let _this = $(this);
		if(!_this.length || _this.attr('href').indexOf('history.back')==-1)return true;
		_this.click(function(){
			let referer = $.referer();
			if(!referer.length || referer.indexOf('?app=passport')>-1){
				location.href = '/wap/';
				return false;
			}
		});
	});
};
$.extend({
	checklogin : function(url, _this){
		if(_this && !!_this.hasClass('skip'))return true;
		if($('#member_id').length && (!$('#member_id').val().length || $('#member_id').val()*1==0)){
			location.href = '/wap/?tpl=login&url=' + (typeof(url)=='undefined' ? location.href.urlencode() : url.urlencode()) + '#presentView';
			return false;
		}
		return true;
	},
	shareMark : function(hidden){
		let mark = $('.share-mark');
		if(!mark.length){
			mark = $('<div class="share-mark" style="display:none;"></div>');
			$(document.body).append(mark);
			mark.on('click', function(){mark.fadeOut(300)});
		}
		if(typeof hidden==='undefined'){
			mark.fadeIn(300);
		}else{
			mark.fadeOut(300);
		}
	}
});
})(jQuery);

let controllerView = $([]), bodyView = $([]), bodyViewClicking = false;
//获取bodyView, 支持3种显示样式, 在链接增加自定义属性type, 值:[right(右侧移入,默认)|bottom(底部移入)|scale(缩放渐显)]
function pushBodyView(link, callback, reload){
	if($('body > *:not(.load-overlay, .load-view, .load-presentView, .pswp, .bodyView)').length!=1)$('body > *').wrapAll('<div class="bodyView-div"></div>');
	controllerView = $('body > div').length==1 ? $('body > div') : $('body');
	bodyView = $('body > div').length==1 ? $('body > div') : $('body');
	let delegate = $('.bodyView:last');
	if(!delegate.length)delegate = $('body > div').length==1 ? $('body > div') : $('body');
	let click = null, data = null, linkElement = null;
	if($.isPlainObject(link)){
		if(typeof(link.link)=='undefined')return;
		if($.isFunction(link.click))click = link.click;
		if($.isFunction(link.data))data = link.data;
		link = link.link;
	}
	if(typeof(link)=='string'){
		linkElement = delegate.find(link);
	}else{
		linkElement = link;
		if(!linkElement.parents('.bodyView').length)delegate = $('body > div').length==1 ? $('body > div') : $('body');
	};
	linkElement.not('.return, .delete, .confirm, [href^="javascript:"], [href^="#"]').each(function(){
		let href = $(this).attr('href');
		if(!!!href || !href.length)return true;
		$(this).data('hasClick', true).on('click touchend', function(e){
			if(bodyViewClicking){e.preventDefault();return false};
			bodyViewClicking = true;
			if(!!$(document.body).data('touchmoved')){bodyViewClicking = false;$(document.body).removeData('touchmoved');return false};
			let _link = $(this), _type = _link.attr('type');
			if(!!!_type || !$.inArray(_type, ['right', 'bottom', 'scale']))_type = 'right';
			if($.isFunction(click)){
				let result = click.call(_link);
				if(typeof(result)=='boolean' && !result){bodyViewClicking = false;e.preventDefault();return false}
			}
			let postData = null;
			if($.isFunction(data))postData = data();
			setTimeout(function(){$.overload(false)}, 5000);
			let url = _link.attr('href'), timer = setTimeout(function(){$.overload()}, 800);
			$.ajax({
				type : !data ? 'GET' : 'POST',
				url : url,
				data : postData,
				success : function(html){
					bodyViewClicking = false;
					clearTimeout(timer); timer = null;
					$.overload(false);
					if(!$.trim(html).length)return;
					if($.isJsonString(html)){
						let json = $.json(html);
						if(json.error!=0)$.overloadError(json.msg);
						return;
					};
					let content = html.match(/<body[^>]*>([\s\S]+)<\/body>/i),
						css = html.match(/<body.*?( class="([^"]+)")?[^>]*>/i),
						style = html.match(/<body.*?( style="([^"]+)")?[^>]*>/i);
					if(content && typeof(content[1])!='undefined'){
						let bodyView = $('<div class="bodyView bodyView-'+_type+'" url="'+url+'"></div>');
						if(css && typeof(css[2])!='undefined')bodyView.addClass(css[2]);
						if(style && typeof(style[2])!='undefined')bodyView.attr('style', style[2]);
						$('body').append(bodyView.height($.window().height));
						bodyView.append(content[1]);
						bodyView.data('delegate', delegate);
						if($.isFunction(callback))callback.call(bodyView, bodyView, _link, delegate);
						if($.isFunction(reload))bodyView.data('reload', {reload:reload, link:_link, delegate:delegate});
						let navBar = bodyView.find('.navBar');
						if(navBar.length)navBar.find('.titleView').addClass('titleView-x');
						setTimeout(function(){bodyView.addClass('bodyView-x')}, 10);
						setTimeout(function(){bodyView.addClass('bodyView-d')}, 350);
						bodyView.find('.navBar .left[href="javascript:history.back()"], .navBar .left[href="javascript:history.go(-1)"]').click(function(e){
							popBodyView(bodyView);
							e.preventDefault();
							return false;
						});
					}
				},
				error : function(){
					bodyViewClicking = false;
				}
			});
			e.preventDefault();
			return false;
		});
	});
}
function presendBodyView(link, callback, reload){
	$(link).attr('type', 'bottom');
	pushBodyView(link, callback, reload);
}
function scaleBodyView(link, callback, reload){
	$(link).attr('type', 'scale');
	pushBodyView(link, callback, reload);
}
//执行页面reload函数
function reloadBodyView(bodyView){
	if(typeof(bodyView)=='undefined' || bodyView==null || bodyView=='')bodyView = '.bodyView:last';
	bodyView = $(bodyView);
	if(!bodyView.length)return;
	let obj = bodyView.data('reload');
	if(!!!obj || !$.isPlainObject(obj) || !$.isFunction(obj.reload))return;
	obj.reload.call(bodyView, obj.delegate, obj.link);
}
//获取代理页面
function delegateBodyView(bodyView){
	if(typeof(bodyView)=='undefined' || bodyView==null || bodyView=='')bodyView = '.bodyView:last';
	bodyView = $(bodyView);
	if(!bodyView.length)return $([]);
	let delegate = bodyView.data('delegate');
	if(!!!delegate)return $([]);
	return delegate;
}
//提交表单
function formBodyView(form, callback){
	if(!$.isFunction(form) && !$(form).parents('.bodyView').length){$(form).submit();return};
	let bodyView = $('.bodyView:last');
	if(!bodyView.length)bodyView = $('body');
	if($.isFunction(form)){callback = form;form = null}
	if(typeof(form)=='undefined' || form==null || form=='')form = bodyView.find('form').eq(0);
	let action = form.attr('action'), method = form.attr('method'), param = form.param();
	if(!!!action || !action.length || action.indexOf('javascript:')>-1 || action.indexOf('#')>-1)return;
	setTimeout(function(){$.overload(false)}, 5000);
	let timer = setTimeout(function(){$.overload()}, 800);
	if(!!!method)method = 'POST';
	switch(method.toUpperCase()){
		case 'POST':
			$.postJSON(action, param, function(json){
				clearTimeout(timer); timer = null;
				$.overload(false);
				if($.isFunction(callback))callback(json);
			});
			break;
		default:
			$.getJSON(action, param, function(json){
				clearTimeout(timer); timer = null;
				$.overload(false);
				if($.isFunction(callback))callback(json);
			});
			break;
	}
}
//移除bodyView
function popBodyView(bodyView){
	if(typeof(bodyView)=='undefined' || bodyView==null || bodyView=='')bodyView = '.bodyView:last';
	bodyView = $(bodyView);
	bodyView.removeClass('bodyView-d').removeClass('bodyView-x');
	let navBar = bodyView.find('.navBar');
	if(navBar.length && navBar.css('position')=='absolute')navBar.css('top', bodyView.scrollTop());
	setTimeout(function(){bodyView.remove()}, 400);
}
//移除bodyView(由最后开始)到指定[url(自定义属性,包含url某部分即可)|index(0为body)]的bodyView
function popToBodyView(urlOrIndex){
	let bodyViews = [], bodyView = $('.bodyView');
	if(typeof(urlOrIndex)=='number'){
		bodyView.each(function(index){
			if(index>=bodyView.length-1)return false;
			if(index>urlOrIndex-1)bodyViews.push(this);
		});
	}else{
		let keepViews = [];
		bodyView.each(function(){
			if($(this).attr('url').indexOf(urlOrIndex)>-1){
				keepViews.push(this);
				return false;
			}
		});
		bodyView.each(function(index){
			if(index>=bodyView.length-1)return false;
			if($.inArray(this, keepViews)==-1)bodyViews.push(this);
		});
	}
	popBodyView();
	$(bodyViews).remove();
}
//移除所有bodyView
function popRootBodyView(){
	popBodyView();
	$('.bodyView:not(:last)').remove();
}

function setAds(lis){
	$(lis).each(function(){
		if(!!$(this).data('setAds'))return true;
		let _this = $(this).data('setAds', true), type = _this.attr('ad_type'), content = _this.attr('ad_content'), pic = _this.attr('pic');
		switch (type) {
			case 'html5':
				_this.html('<a href="'+content+'" url="'+pic+'"></a>');
				break;
			case 'goods':
				_this.html('<a href="/wap/?app=goods&act=detail&id='+content+'" url="'+pic+'"></a>');
				break;
			case 'shop':
				_this.html('<a href="/wap/?app=shop&act=detail&id='+content+'" url="'+pic+'"></a>');
				break;
			case 'article':
				_this.html('<a href="/wap/?app=article&act=detail&id='+content+'" url="'+pic+'"></a>');
				break;
			case 'type':
			case 'subtype':
				_this.html('<a href="/wap/?app=goods&act=index&category_id='+content+'" url="'+pic+'"></a>');
				break;
			case 'brand':
				_this.html('<a href="/wap/?app=goods&act=index&brand_id='+content+'" url="'+pic+'"></a>');
				break;
			case 'coupon':
				_this.html('<a href="javascript:void(0)" mid="'+content+'" url="'+pic+'"></a>');
				_this.find('a').ontouchend(function(){
					if(!$.checklogin())return false;
					$.getJSON('/api/?app=coupon&act=ling', {coupon_id:$(this).attr('mid')}, function(json){
						if(json.error!=0){$.overloadError(json.msg);return}
						$.overloadSuccess('优惠券领取成功');
					});
				});
				break;
			case 'recharge':
				_this.html('<a href="/wap/?app=recharge&act=commit&id='+content+'" url="'+pic+'"></a>');
				_this.find('a').checklogin();
				break;
			case 'country':
				_this.html('<a href="/wap/?app=goods&act=index&country_id='+content+'" url="'+pic+'"></a>');
				break;
			case 'register':
				_this.html('<a href="javascript:void(0)" mid="'+content+'" url="'+pic+'"></a>');
				_this.find('a').ontouchend(function(){
					if($.checklogin()){
						location.href = '/wap/?app=member&act=index';
						return false;
					}
					location.href = '/wap/?tpl=register';
				});
				break;
			default:
                _this.html('<a href="javascript:void(0)" mid="'+content+'" url="'+pic+'"></a>');
				break;
		}
	}).find('a').loadbackground();
}

function disableBtn(){
	let _this = this;
	if(_this.hasClass('disabled')){
		_this.html(_this.data('text')).removeClass('disabled').removeData('disabled');
	}else{
		if(!!_this.data('disabled'))return false;
		if(!!!_this.data('text'))_this.data('text', _this.text());
		_this.html('<div class="preloader"></div>　').addClass('disabled').data('disabled', true);
	}
	return true;
}

function setTitleView(){
	$('.navBar .titleView:not(.titleView-x)').each(function(){
		$(this).css('margin-left', (-$(this).width()/2).rem());
	});
}

//app绑定表单提交
function bindForm(form, beforeFn, afterFn){
	$(form).submit(function(e){
		if($.isFunction(beforeFn)){
			let result = beforeFn();
			if(typeof result == 'boolean' && !result)return false;
		}
		let form = $(this), action = form.attr('action'), isloginElement = form.find('#islogin'),
			goalertElement = form.find('[name="goalert"]'), gourlElement = form.find('[name="gourl"]'), goalert = '', gourl = '',
			orderPayUrlElement = $('#orderPayUrl'), payMethodElement = $('#payMethod');
		if(!!!action)return false;
		if(goalertElement.length)goalert = goalertElement.val();
		if(gourlElement.length)gourl = gourlElement.val();
		goalertElement.remove();
		gourlElement.remove();
		if((isloginElement.length || gourl.length) && !payMethodElement.length){
			$.overload();
			$.postJSON(action, form.param(), function(json){
				if($.isJson(json) && json.error!=0){$.overloadError(json.msg);return}
				if($.isFunction(afterFn)) afterFn();
				if(goalert.length) $.overloadSuccess(goalert);
				if(isloginElement.length){
					$.overload();
					let div = '<div class="hidden" id="personValue">' + JSON.stringify(json) + '</div>';
					$('body').append(div);
					location.href = 'app-act://act=personValue';
					setTimeout(function(){
						location.href = 'app-act://act=dismissView';
					}, 300);
				}else{
					setTimeout(function(){
						location.href = gourl;
					}, 0);
				}
			});
		}else{
			let json = form.param();
			if(payMethodElement.length && payMethodElement.html().length){
				if(orderPayUrlElement.length && orderPayUrlElement.html().length)action = orderPayUrlElement.html();
				$.overload();
				$.postJSON(url, json, function(json){
					if($.isJson(json) && json.error!=0){$.overloadError(json.msg);return}
					let div = '<div class="hidden" id="orderData">' + JSON.stringify(json) + '</div>';
					$('body').append(div);
					location.href = 'app-act://act=payMethod';
				});
			}else{
				let div = '<div class="hidden" id="postDataUrl">' + action + '</div>\
				<div class="hidden" id="postData">' + JSON.stringify(json) + '</div>';
				$('body').append(div);
				location.href = 'app-act://act=postData';
			}
		}
		return false;
	});
}

function wxShareInit(jssdk, timelineCallback, friendCallback, debug){
	if($(document.body).data('wxShareInit'))return;
	$(document.body).data('wxShareInit', true);
	if(typeof jssdk === 'string')jssdk = jssdk.substr(0, 5) === 'Mario' ? JSON.parse($.base64Decode(jssdk.substr(5))) : JSON.parse(jssdk);
	if(debug)console.log(jssdk);
	wx.config({
		debug: !!debug,
		appId: jssdk.appId,
		timestamp: jssdk.timestamp,
		nonceStr: jssdk.nonceStr,
		signature: jssdk.signature,
		jsApiList: [
			'checkJsApi',
			'hideAllNonBaseMenuItem',
			'showMenuItems',
			//'updateTimelineShareData',
			//'updateAppMessageShareData',
			'onMenuShareTimeline',
			'onMenuShareAppMessage'
		]
	});
	wx.ready(function(){
		//wx.hideAllNonBaseMenuItem();
		//wx.showMenuItems({menuList:['menuItem:share:timeline', 'menuItem:share:appMessage']});
		let config = {
			title: jssdk.share.title,
			desc: jssdk.share.desc,
			link: jssdk.share.link,
			imgUrl: jssdk.share.img,
			type: 'link',
			dataUrl: '',
			cancel: function(){ }
		};
		let timelineConfig = $.extend({}, config, {
			success: function(res){
				if($.isFunction(timelineCallback))timelineCallback(res);
			}
		});
		let appmessageConfig = $.extend({}, config, {
			success: function(res){
				if(!$.isFunction(friendCallback))friendCallback = timelineCallback;
				if($.isFunction(friendCallback))friendCallback(res);
			}
		});
		//wx.updateTimelineShareData(timelineConfig);
		//wx.updateAppMessageShareData(appmessageConfig);
		wx.onMenuShareTimeline(timelineConfig);
		wx.onMenuShareAppMessage(appmessageConfig);
	});
}

function orderPay(options){
	if(typeof options.pay_method === 'undefined')options.pay_method = 'wxpay';
	$.postJSON('/api/other/pay', options, function(json){
		if(options.pay_method === 'wxpay'){
			let jsApiCall = function(){
				WeixinJSBridge.invoke(
					'getBrandWCPayRequest',
					json.data,
					function(res){
						//WeixinJSBridge.log(res.err_msg);
						if(res.err_msg && res.err_msg === 'get_brand_wcpay_request:ok'){
							if($.isFunction(options.success))options.success(res);
						}else if(res.err_msg && res.err_msg === 'get_brand_wcpay_request:cancel'){
							if($.isFunction(options.cancel))options.cancel(res);
						}else if(res.errMsg){
							console.log('支付失败，原因：'+res.errMsg);
							if($.isFunction(options.error))options.error(res);
						}else{
							console.log('支付失败，原因：未知');
							if($.isFunction(options.error))options.error({errMsg:'未知'});
						}
					}
				);
			};
			if(typeof WeixinJSBridge === 'undefined'){
				if(document.addEventListener){
					document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
				}else if(document.attachEvent){
					document.attachEvent('WeixinJSBridgeReady', jsApiCall);
					document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
				}
			}else{
				if(typeof wx === 'undefined' || typeof wx.miniProgram === 'undefined'){
					jsApiCall();
					return;
				}
				wx.miniProgram.getEnv(function(res){
					if(res.miniprogram){ //在微信小程序中
						//json.data.url = '/pages/member/index'; //指定支付成功后跳转页面
						let path = '/pages/global/outlet?payParam=' + encodeURIComponent(JSON.stringify(json.data)); //定义path与小程序的支付页面的路径相对应
						wx.miniProgram.redirectTo({url:path});
					}else{
						jsApiCall();
					}
				});
			}
		}else if(options.pay_method === 'alipay'){
			location.href = json.data;
		}
	});
}

function configs(){
	setTitleView();
	$('*:not(.bodyView) .navBar a.left').checkHistoryBack();
	if($.browser.wx){
		$('a[weixin]').click(function(){
			$.overlay('', 0, function(){
				$('.position-overlay').css('background', 'none').html('<div class="wx-share"></div>');
			});
			return false;
		});
	}
	//检查浏览器本地storage与数据库sign是否一样,一样即自动登录
	let member = $.localStorage('member');
	if(member){
		member = $.json(member);
		$.getJSON('/api/?app=passport&act=check_storage&sign='+(member.sign?member.sign:''), {}, {
			success : function(json){
				if(!$.isPlainObject(json.data))return;
				$.localStorage('member', JSON.stringify(json.data), 365);
				let reloadPage = window.sessionStorage.getItem('reloadPage');
				if(!reloadPage || reloadPage==0){
					window.sessionStorage.setItem('reloadPage', 1);
					if(!/^\?app=home(&act=index)?$/.test(location.search))location.href = location.href;
				}
			},
			error : function(){
				$.localStorage('member', null);
				window.sessionStorage.removeItem('reloadPage');
			}
		});
	}
}
$(function(){
	if(window.isWX){
		$('.navBar').addClass('navBar-hidden');
	}
	if(window.isApp){
		$('.navBar').addClass('navBar-hidden');
		$('.footer').addClass('hidden');
		window.history = new Object();
		window.history.back = function(){
			setTimeout(function(){
				location.href = 'app-act://act=popView';
			}, 300);
		};
		window.history.go = function(delta){
			setTimeout(function(){
				location.href = 'app-act://act=popView';
			}, 300);
		};
	}
	$(document.body).data('overlay-no-overload', true);
	controllerView = $('body');
	bodyView = $('body');
	configs();
});