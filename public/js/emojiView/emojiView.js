(function($){

var emojiFilename = 'expression.json', emojiPath = document.scripts;
emojiPath = emojiPath[emojiPath.length-1].src.substring(0, emojiPath[emojiPath.length-1].src.lastIndexOf('/')+1);

function emojiView(caller, options){
	this.caller = caller;
	this.options = options;
	this.isAppear = false;
	this.view = null;
	this.pageCount = options.rowCount * options.cellCount - 1;
	this.create();
};
emojiView.prototype = {
	create : function(){
		var _this = this;
		$.getStyle(emojiPath+'emojiView.css');
		getEmojiJSON(function(json){
			var view = $('<div class="emojiView"><section></section><footer><div></div></footer></div>');
			_this.caller.append(view);
			_this.view = view;
			var section = view.find('section'), footer = view.find('footer'), pager = footer.find('div'), ul = null, html = '<ul>',
				width = _this.view.width(), percent = Math.floor(_this.view.width()/_this.options.cellCount)/100, count = 0, i = 0, sendBtn = null,
				deleteBtn = '<li><a href="javascript:void(0)" class="deleteEmoji"><i style="background-image:url('+emojiPath+'expression/emoticon-delete@2x.png);"></i></a></li>';
			$.each(json, function(){count++});
			$.each(json, function(mark, val){
				html += '<li><a href="javascript:void(0)" mark="'+mark+'"><i style="background-image:url('+emojiPath+'expression/'+val+');"></i></a></li>';
				i++;
				if(i>=count){html += deleteBtn;return false}
				if(i%_this.pageCount == 0)html += deleteBtn + '</ul><ul>';
			});
			html += '</ul>';
			section.append(html);
			section.find('ul:empty').remove();
			section.touchmove({list:'ul', pager:pager, autoWH:false, autoH:false, drag:true});
			section.find('a').click(function(){
				var _a = $(this), mark = _a.attr('mark');
				if(!!mark){
					if($.isFunction(_this.options.selectFn))_this.options.selectFn.call(_this, mark);
				}else if(_a.hasClass('deleteEmoji')){
					if($.isFunction(_this.options.deleteFn))_this.options.deleteFn.call(_this);
				}
				return false;
			});
			if($.isFunction(_this.options.sendFn)){
				sendBtn = $('<a href="javascript:void(0)">发送</a>');
				footer.append(sendBtn);
				sendBtn.click(function(){
					_this.options.sendFn.call(_this);
				});
			};
			setTimeout(function(){_this.view.addClass('emojiView-t')}, 100);
		}, this.options.filename);
	},
	show : function(changeObj, before, after){
		if(!this.view)return;
		var _this = this, height = this.view.height();
		if($.isFunction(before))before.call(this);
		this.view.addClass('emojiView-x');
		this.isAppear = true;
		if(changeObj)$(changeObj).css({
			'-webkit-transform':'translateY(-'+height+'px)', 'transform':'translateY(-'+height+'px)',
			'-webkit-transition':'-webkit-transform 200ms ease-out', 'transition':'transform 200ms ease-out'
		});
		if($.isFunction(after))setTimeout(function(){after.call(_this)}, 200);
	},
	close : function(changeObj, before, after){
		if(!this.view)return;
		var _this = this;
		if($.isFunction(before))before.call(this);
		this.view.removeClass('emojiView-x');
		this.isAppear = false;
		if(changeObj)$(changeObj).css({
			'-webkit-transform':'translateY(0)', 'transform':'translateY(0)'
		});
		if($.isFunction(after))setTimeout(function(){after.call(_this)}, 200);
	}
};

function getEmojiJSON(callback, filename){
	var body = $(document.body);
	if(!!body.data('emojiJSON')){
		if($.isFunction(callback))callback(body.data('emojiJSON'));
	}else{
		if(typeof filename=='undefined')filename = emojiFilename;
		$.getJSON(emojiPath+'expression/'+filename, function(json){
			body.data('emojiJSON', json);
			if($.isFunction(callback))callback(json);
		});
	}
};

//options = boolean[false(解析) | true(反解析)] | 否则生成表情控件
$.fn.emojiView = function(options, filename){
	if(typeof options!='undefined' && typeof options!='boolean' && !$.isPlainObject(options))return this;
	if(typeof options=='undefined' || typeof options=='boolean'){
		var ths = this;
		getEmojiJSON(function(json){
			ths.each(function(){
				var _this = $(this), html = _this.html();
				if(!html.length)return true;
				if(options){
					html = html.replace(new RegExp('<img src="'+emojiPath+'expression/([^"]+)"[^>]*>', 'g'), function(m, s){
						for(var k in json)if(typeof json[k]=='string' && s==json[k]){return k;break};
						return m;
					});
				}else{
					html = html.replace(/(\[[a-zA-Z0-9\u4e00-\u9fa5]+\])/g, function(m, s){
						return '<img src="'+emojiPath+'expression/'+json[s]+'" />';
					});
				};
				_this.html(html);
			});
		});
		return this;
	}
	if($('.emojiView').length)return true;
	return new emojiView(this, options);
};

//删除匹配表情名称的字符串段落
$.fn.deleteEmoji = function(){
	return this.each(function(){
		var _this = $(this);
		if(!_this.is(':text') && !_this.is('textarea'))return true;
		_this.val(_this.val().deleteString('[', ']'));
	});
};

$.extend({
	emojiView : function(options){
		options = $.extend({
			filename : emojiFilename, //json文件, 表情对应的名称与文件名, 将使用ajax读取
			rowCount : 3, //行数
			cellCount : 7, //每行表情数
			selectFn : null, //点击表情执行,接受一个参数:mark(表情对应的名称)
			deleteFn : null, //点击删除按钮执行
			sendFn : null //点击发送按钮执行,如不设定即不显示发送按钮
		}, options);
		return $(document.body).emojiView(options);
	}
});

})(jQuery);