import Vue from 'vue';
import PhotoSwipe from '../../js/photoswipe/photoswipe.min.js'
import PhotoSwipeUI_Default from '../../js/photoswipe/photoswipe-ui-default.min.js'
import $ from '../../js/jquery-3.4.1.min'
window.$ = $

//日期选择器, 需引入datepicker.css
$.fn.datepicker = function(options){
	options = $.extend({
		initDate: new Date(), //默认选定日期
		parent: 'body', //生成的选择器html代码插入到指定容器里的最后, 平板模式无效
		target: '', //选择日期后填写日期的目标对象(不指定即为当前点击对象)
		cls: 'datepicker', //使用指定class
		sep: 4, //y相隔
		just: '', //只显示选择年份或月份(参数只支持两种:year、month)
		next: '', //选择日期后直接跳到下个日期控件, 格式为expr
		always: false, //平板模式, 直接显示在调用者的html内, 一般与target参数同时使用
		fullscreen: false, //全屏展示
		hiddenNavBar: false, //隐藏导航栏
		partner: '', //设置点击不隐藏的伙伴
		reverseTarget: '', //到容器边缘反转显示, 不设置即根据window
		useClick: true, //使用click绑定点击显示, 否则自行调用 .trigger('datepicker.click') 且设置 partner 参数
		breakClick: false, //不执行插件默认的点击选择日期操作
		disable: false, //不执行所有点击操作
		readonly: true, //可否填写
		range: false, //范围选择
		multiple: false, //日期多选, 设置后next无效
		showCal: true, //false即直接显示时分秒, showTime自动变为true
		showTime: false, //显示时分, 返回的日期格式(format)需自己设定
		showHour: true, //显示时, 需设置showTime
		showMinute: true, //显示分, 需设置showTime,showHour
		changeYear: true, //可否更改年份
		changeMonth: true, //可否更改月份
		touchMove: true, //拖曳切换年月
		enText: false, //将所有文字改为英文
		yearText: '年', //一般以英文显示才需修改
		monthText: '月', //同上
		weekText: [], //同上,以星期天起始, 留空即使用默认
		minYear: 1949, //最小年份(数值型)(字符型:this为今年|[+-]数字(以今天作为界限))
		maxYear: new Date().getFullYear()+15, //最大年份(数值型)(字符型:this为今年|[+-]数字(以今天作为界限))
		disMonths: '', //禁用月份(逗号隔开)
		disDays: '', //禁用每月的某些日(逗号隔开)
		disWeeks: '', //禁用每月的某些星期(逗号隔开)(格式:0,1,2,3,4,5,6,星期日为0)
		disDates: '', //禁用某些日(逗号隔开)(today:使用今天作为日期)(格式:年-月-日)
		minDate: '', //只能选择该日以后的日期(格式:年-月-日)(today:使用今天作为日期)([+-]数字[y|m|d](以今天作为界限))
		maxDate: '', //只能选择该日以前的日期(格式:年-月-日)(today:使用今天作为日期)([+-]数字[y|m|d](以今天作为界限))
		format: 'yyyy-m-d', //以逗号分隔的日期数组内每个日期的格式, range需设置为:(#yyyy-m-d)~(#yyyy-m-d),会自动正则替换(#yyyy-m-d)
		mark: {}, //设定日子说明, {'0-8-18':'生日','2017-8-31':''} //年为0即每年,如果为空字符,则只增加标识
		date: null, //每个日期生成后执行
		prevMonth: null, //外部切换到上个月的控件
		nextMonth: null, //外部切换到下个月的控件
		prevMonthCallback: null, //点击上个月箭头后执行
		nextMonthCallback: null, //点击下个月箭头后执行
		shown: null, //显示后执行, 两参数:datepicker,目标对象
		hidden: null, //隐藏后执行
		change: null, //改变年月后执行
		complete: null //插件加载后执行
		//自定义函数名: function(dates){...} //可加入多个自定义函数(只要是函数都会被执行), dates参数为[多个日期元素的数组|range两个日期元素的数组]
	}, $('body').data('datepicker.options'), options);
	return this.each(function(){
		$(this).removePlug('datepicker');
		let _this = $(this).data('datepicker', true), body = _this.parents('body');
		_this.on('makepicker', function(){
			let el = !!_this.attr('target') ? $(_this.attr('target')) : (options.target?$(options.target):_this), mark = el.attr('id'), tmp;
			if(!!!mark){mark = el.attr('name');el.attr('id', mark)}
			if(!!!mark){mark = $.datetimeAndRandom();el.attr('id', mark)}
			if(el.is('input'))el.attr('autocomplete', 'off');
			_this.data({mark:mark, el:el});
			//if(_this.is('input'))_this.addClass('datepicker');
			if(!!_this.attr('parent'))options = $.extend(options, {parent:_this.attr('parent')});
			if(!!_this.attr('cls'))options = $.extend(options, {cls:_this.attr('cls')});
			if(!!_this.attr('just') && (_this.attr('just') === 'year' || _this.attr('just') === 'month'))options = $.extend(options, {just:_this.attr('just')});
			if(!!el.attr('@') || !!_this.attr('@'))options = $.extend(options, {next:el.attr('@')||_this.attr('@')});
			if(!!_this.attr('next'))options = $.extend(options, {next:_this.attr('next')});
			if(options.readonly){el.attr('readonly','readonly').css('cursor','default')}else{el.removeAttr('readonly')}
			if($.inArray(_this.attr('fullscreen'),['true','false'])>-1){tmp=evil(_this.attr('fullscreen'));options = $.extend(options, {fullscreen:tmp})}
			if($.inArray(_this.attr('hiddenNavBar'),['true','false'])>-1){tmp=evil(_this.attr('hiddenNavBar'));options = $.extend(options, {hiddenNavBar:tmp})}
			if($.inArray(_this.attr('breakClick'),['true','false'])>-1){tmp=evil(_this.attr('breakClick'));options = $.extend(options, {breakClick:tmp})}
			if($.inArray(_this.attr('range'),['true','false'])>-1){tmp=evil(_this.attr('range'));options = $.extend(options, {range:tmp})}
			if(!!_this.attr('multiple'))options = $.extend(options, {multiple:true});
			if($.inArray(_this.attr('showTime'),['true','false'])>-1){tmp=evil(_this.attr('showTime'));options = $.extend(options, {showTime:tmp})}
			if($.inArray(_this.attr('showHour'),['true','false'])>-1){tmp=evil(_this.attr('showHour'));options = $.extend(options, {showHour:tmp})}
			if($.inArray(_this.attr('showMinute'),['true','false'])>-1){tmp=evil(_this.attr('showMinute'));options = $.extend(options, {showMinute:tmp})}
			if($.inArray(_this.attr('showCal'),['true','false'])>-1){tmp=evil(_this.attr('showCal'));options = $.extend(options, {showCal:tmp})}
			if($.inArray(_this.attr('changeYear'),['true','false'])>-1){tmp=evil(_this.attr('changeYear'));options = $.extend(options, {changeYear:tmp})}
			if($.inArray(_this.attr('changeMonth'),['true','false'])>-1){tmp=evil(_this.attr('changeMonth'));options = $.extend(options, {changeMonth:tmp})}
			if($.inArray(_this.attr('touchMove'),['true','false'])>-1){tmp=evil(_this.attr('touchMove'));options = $.extend(options, {touchMove:tmp})}
			if($.inArray(_this.attr('enText'),['true','false'])>-1){tmp=evil(_this.attr('enText'));options = $.extend(options, {enText:tmp})}
			if(!!Number(_this.attr('minYear'))){tmp=evil(_this.attr('minYear'));options = $.extend(options, {minYear:tmp})}
			if(!!Number(_this.attr('maxYear'))){tmp=evil(_this.attr('maxYear'));options = $.extend(options, {maxYear:tmp})}
			if(!!_this.attr('disMonths'))options = $.extend(options, {disMonths:_this.attr('disMonths')});
			if(!!_this.attr('disDays'))options = $.extend(options, {disDays:_this.attr('disDays')});
			if(!!_this.attr('disWeeks'))options = $.extend(options, {disWeeks:_this.attr('disWeeks')});
			if(!!_this.attr('disDates'))options = $.extend(options, {disDates:_this.attr('disDates')});
			if(!!_this.attr('minDate') || !!_this.data('minDate'))options = $.extend(options, {minDate:_this.data('minDate')||_this.attr('minDate')});
			if(!!_this.attr('maxDate'))options = $.extend(options, {maxDate:_this.attr('maxDate')});
			if(!!_this.attr('format'))options = $.extend(options, {format:_this.attr('format')});
			if(!!_this.attr('mark')){let marker = evil(_this.attr('mark'));options = $.extend(options, {mark:marker})}
			if(options.enText)options = $.extend(options, {yearText:'', monthText:''});
			if(options.multiple)options = $.extend({}, options, {next:''});
			if(!options.showCal)options = $.extend(options, {showTime:true, format:options.format.indexOf('hh') === -1?'hh:nn:ss':options.format});
			if(options.showTime && !options.showHour)options = $.extend({}, options, {showHour:true});
			if(options.range && options.format.indexOf('(#') === -1)options = $.extend(options, {format:'(#yyyy-m-d)~(#yyyy-m-d)'});
			if(!options.always){
				if(options.useClick)_this.on('click', function(){
					if(!body.find('#' + mark + '_datepicker').length)makepicker();
					else removeControl();
				});
				_this.on('datepicker.click', function(){
					if(!body.find('#' + mark + '_datepicker').length)makepicker();
					else removeControl();
				});
			}else{
				makepicker();
			}
		});
		_this.addClass(options.cls).data('datepicker-options', options).on('datepicker.hidden', function(){
			removeControl();
		}).trigger('makepicker');
		let mark = _this.data('mark');
		function makepicker(){
			let mark = _this.data('mark'), el = _this.data('el'), initdate = [],
				datepicker = $('<div id="' + mark + '_datepicker" class="datepickerView" style="position:relative;font-family:Arial,\'Microsoft YaHei\';"><div class="childrenDIV" style="position:relative;font-size:12px;overflow:hidden;"><div></div></div></div>');
			if(!options.always)datepicker.css({position:'absolute', 'z-index':'888', left:0, top:0}).children('div').css({position:'absolute', left:0, top:0});
			else datepicker.addClass('datepickerViewNotHide');
			if(options.fullscreen)datepicker.addClass('datepickerViewFullscreen');
			if(!!el.data('curYear') || !!el.attr('data-curYear') || !!_this.data('curYear')){
				let curYear = el.data('curYear')||el.attr('data-curYear')||_this.data('curYear'),
					curMonth = el.data('curMonth')||el.attr('data-curMonth')||_this.data('curMonth')||0,
					curDay = el.data('curDay')||el.attr('data-curDay')||_this.data('curDay')||1,
					curHour = el.data('curHour')||el.attr('data-curHour')||_this.data('curHour')||0,
					curMinute = el.data('curMinute')||el.attr('data-curMinute')||_this.data('curMinute')||0,
					curSecond = el.data('curSecond')||el.attr('data-curSecond')||_this.data('curSecond')||0;
				initdate = [new Date(curYear, curMonth, curDay, curHour, curMinute, curSecond)];
			}
			let reg = /^(?:(\d{4})-(\d{1,2})(?:-(\d{1,2}))?)?(?: ?(\d{1,2}))?(?::(\d{1,2}))?(?::(\d{1,2}))?$/;
			if( !!el.attr('initdate') || (el.val().length && reg.test(el.val())) || (el.html().length && reg.test(el.html())) ){
				let val = el.attr('initdate');
				if(!!!val && reg.test(el.val()))val = el.val();
				if(!!!val && reg.test(el.html()))val = el.html();
				if(!!!val)return;
				let datas = val.split(',');
				if(options.range)datas = [datas[0], datas[datas.length-1]];
				initdate = [];
				for(let i=0; i<datas.length; i++){
					initdate.push((datas[i]).date());
				}
			}
			let option = $.extend(options, {
				obj: el,
				initDate: initdate,
				click: function(dates){
					let fn, arr = [], arrFormat = [];
					if(options.range){
						let count = 0, fmt = options.format.replace(/\(#([^)]+)\)/g, function(m, format){
							format = dates[count].formatDate(format, function(date){
								let showTime = '';
								if(options.showTime){
									if(options.showHour)showTime += ' '+date.hour;
									if(options.showHour && options.showMinute)showTime += ':'+date.minute;
								}
								if(options.showCal){
									arr.push(date.year+'-'+date.month+'-'+date.day+showTime);
								}else{
									arr.push($.trim(showTime));
								}
							});
							count++;
							return format;
						});
						arrFormat.push(fmt);
					}else{
						for(let i=0; i<dates.length; i++){
							let format = (dates[i]).formatDate(options.format, function(date){
								let showTime = '';
								if(options.showTime){
									if(options.showHour)showTime += ' '+date.hour;
									if(options.showHour && options.showMinute)showTime += ':'+date.minute;
								}
								if(options.showCal){
									arr.push(date.year+'-'+date.month+'-'+date.day+showTime);
								}else{
									arr.push($.trim(showTime));
								}
							});
							arrFormat.push(format);
						}
					}
					el.removeData('curYear curMonth curDay curHour curMinute curSecond');
					let val = arrFormat.join(',');
					if(el.is('input, textarea'))el.val(val);
					if(el.is('select'))el.selected(val);
					if(!el.is('input, textarea, select'))el.html(val);
					if(!options.always && !options.multiple)removeControl();
					el.attr('initdate', arr.join(','));
					fn = el.attr('fn');
					if(!!fn){
						let func = evil(fn);
						if($.isFunction(func))func.call(el, dates);
					}
					if(!!el.data('checkform-checkHandle'))el.data('checkform-checkHandle').call(el);
					if(!options.multiple && options.next){
						let next = $(options.next), date = dates[0];
						if(!next.length || next.is(el))return;
						next.data({'curYear':date.getFullYear(), 'curMonth':date.getMonth(), 'curDay':date.getDate(), 'curHour':date.getHours(), 'curMinute':date.getMinutes(), 'curSecond':date.getSeconds()});
						next.data('minDate', date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate());
						if(!!!next.data('makedatepicker')){
							next.data('makedatepicker', true);
							if(!!!next.data('datepicker'))next.datepicker($.extend({}, options, {next:''}));
						}
						if(!options.always){
							setTimeout(function(){next.click()}, 0);
						}else{
							next.trigger('makepicker');
						}
					}
				}
			});
			datepicker.calendar(option);
			if(options.cls.length)datepicker.addClass(options.cls);
			if(options.next && options.always){
				let next = $(options.next);
				if(next.length && !next.is(el)){
					next.data('makedatepicker', true);
					if(!!!next.data('datepicker'))next.datepicker($.extend({}, options, {next:''}));
					setTimeout(function(){
						if(!!next.data('datepicker')){
							let ar = next.children().eq(0);
							if(!ar.find('.datepickerMark').length)ar.prepend('<div class="datepickerMark"></div>');
						}
					}, 100);
				}
			}
			if(!options.always){
				let position = {};
				if(options.parent === 'body'){
					body.append(datepicker);
					position = _this.offset();
				}else{
					$(options.parent).append(datepicker);
					position = _this.position();
				}
				if(options.fullscreen){
					setTimeout(function(){datepicker.addClass('datepickerViewFullscreen-x')}, 50);
				}else{
					if($.browser.ie6){
						let iframe = $('<iframe frameborder="0"></iframe>');
						datepicker.append(iframe);
						iframe.css({width:datepicker.children('div').outerWidth(false), height:datepicker.children('div').outerHeight(false)});
					}
					let win = $.window(), reverseTarget = options.reverseTarget.length ? $(options.reverseTarget) : '',
						cl = reverseTarget.length ? reverseTarget.scrollLeft() : 0, ct = reverseTarget.length ? reverseTarget.scrollTop() : 0,
						cw = reverseTarget.length ? reverseTarget.width() : win.scrollLeft + win.width, ch = reverseTarget.length ? reverseTarget.height() : win.scrollTop + win.height,
						posl = cl + position.left, post = ct + position.top + _this.outerHeight(false) + options.sep;
					if(position.left+datepicker.children('div').outerWidth(false)>cw)posl = cl + position.left - datepicker.children('div').outerWidth(false);
					if(position.top+datepicker.children('div').outerHeight(false)>ch){
						post = ct + position.top - datepicker.children('div').outerHeight(false) - options.sep;
						datepicker.attr('reverse-y', post);
					}
					datepicker.css({left:posl, top:post});
				}
				_this.on('keydown', removeControl);
				datepicker.data('removeControl', removeControl);
				body.on(window.eventType, removeControlReg);
			}else{
				_this.children().remove();
				_this.append(datepicker);
			}
			let yul = datepicker.find('ul:eq(0)'), yli = yul.find('li');
			yli.eq(1).width(yul.width()-yli.eq(0).outerWidth(true)-yli.eq(2).outerWidth(true)-($.browser.msie?($.browser.version===8?2:1):0));
			if($.isFunction(options.shown))options.shown.call(_this, datepicker, el);
		}
		function removeControlReg(e){
			let o = $.etarget(e);
			do{
				if(o.id === mark+'_datepicker' || o.id === mark || $(o).is(_this))return;
				if(options.partner.length && $(o).is(options.partner))return true;
				if((/^(html|body)$/i).test(o.tagName)){
					removeControl();
					return;
				}
				o = o.parentNode;
			}while(o.parentNode);
		}
		function removeControl(){
			body.find('.datepickerView:not(.datepickerViewNotHide)').each(function(){
				let _datepicker = $(this);
				if(options.fullscreen){
					_datepicker.removeClass('datepickerViewFullscreen-x');
					setTimeout(function(){
						_datepicker.remove();
						if($.isFunction(options.hidden))options.hidden.call(_this);
					}, 300);
				}else{
					setTimeout(function(){
						_datepicker.remove();
						if($.isFunction(options.hidden))options.hidden.call(_this);
					}, 0);
				}
			});
			body.off(window.eventType, removeControlReg);
			//_this.removeAttr('initdate');
		}
	});
};
$.fn.calendar = function(options){
	options = $.extend({
		obj: null,
		initDate: [],
		cls: '',
		just: '',
		fullscreen: false,
		hiddenNavBar: false,
		breakClick: false,
		disable: false,
		range: false,
		multiple: false,
		showCal: true,
		showTime: false,
		showHour: true,
		showMinute: true,
		changeYear: true,
		changeMonth: true,
		touchMove: true,
		enText: false,
		yearText: '年',
		monthText: '月',
		weekText: [],
		minYear: 1949,
		maxYear: new Date().getFullYear()+15,
		disMonths: '',
		disDays: '',
		disWeeks: '',
		disDates: '',
		minDate: '',
		maxDate: '',
		format: 'yyyy-m-d',
		mark: {},
		click: null,
		date: null,
		prevMonth: null,
		nextMonth: null,
		prevMonthCallback: null,
		nextMonthCallback: null,
		change: null,
		complete: null
	}, options);
	let area = null, oriHeight = 0, dates = options.initDate, beginDate = null, endDate = null,
		monthName = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		weekName = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		weekClass = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
	if(!options.enText){
		monthName = ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'];
		weekName = ['日', '一', '二', '三', '四', '五', '六'];
	}
	if($.isArray(options.weekText) && options.weekText.length === 7)weekName = options.weekText;
	if(options.range && dates.length === 2){beginDate = dates[0]; endDate = dates[dates.length-1]}
	if(typeof options.minYear === 'string'){
		if(options.minYear === 'this'){
			options.minYear = new Date().getFullYear();
		}else{
			let tdy = options.minYear.match(/^([+-]\d+)$/);
			if(tdy)options.minYear = new Date().getFullYear() + tdy[1]*1;
		}
	}
	if(typeof options.maxYear === 'string'){
		if(options.maxYear === 'this'){
			options.maxYear = new Date().getFullYear();
		}else{
			let tdy = options.maxYear.match(/^([+-]\d+)$/);
			if(tdy)options.maxYear = new Date().getFullYear() + tdy[1]*1;
		}
	}
	function MonthInfo(y, m){
		let monthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
			d = (new Date(y, m, 1));
		d.setDate(1);
		if(d.getDate() === 2) d.setDate(0);
		y += 1900;
		return{
			days: m === 1 ? (((y % 4 === 0) && (y % 100 !== 0)) || (y % 400 === 0) ? 29 : 28) : monthDays[m],
			firstDay: d.getDay()
		}
	}
	function InitCalendar(calp, date, position, showYears, showMonths){
		let cal = $('<div style="width:100%;float:left;"></div>'),
			initdate = new Date(), td = new Date(), obj = $(options.obj), attrDate = dates.length ? dates[dates.length-1] : null,
			tdYear = td.getFullYear(), tdMonth = td.getMonth(), tdDay = td.getDate(), tdHour = td.getHours(), tdMinute = td.getMinutes(), tdSecond = td.getSeconds(),
			month = MonthInfo(date.getFullYear(), date.getMonth()), minDate = obj.data('minDate')||options.minDate, maxDate = obj.data('maxDate')||options.maxDate,
			minFullDate = new Date(options.minYear, tdMonth, tdDay, tdHour, tdMinute, tdSecond),
			maxFullDate = new Date(options.maxYear, tdMonth, tdDay, tdHour, tdMinute, tdSecond);
		if(typeof position !== 'undefined'){
			let width = calp.parent().width(), height = calp.parent().height(), speed = 300, easing = 'easeout';
			if(!oriHeight){
				if(calp.find('.dayUL').length === 3){
					oriHeight = calp.find('.yearUL').outerHeight(true) + calp.find('.dayUL').outerHeight(true)*3;
				}else{
					oriHeight = calp.find('.yearUL').outerHeight(true) + calp.find('.weekUL').outerHeight(true) + calp.find('.dayUL').outerHeight(true)*5;
				}
			}
			calp.css({position:'relative', width:width, height:height, overflow:'hidden'}).parent().animate({height:oriHeight}, 200, function(){
				let h = 0;
				switch(position){
					case 0:case 'top':
						calp.append(cal);
						calp.children().width(width);
						calp.css({top:0, left:0, height:height*2}).animate({top:-height}, speed, easing, function(){
							cal.css({width:'100%'}).prev().remove();
							calp.css({position:'', top:'', left:'', width:'', height:''}).parent().css({height:'auto'});
							h = calp.parent().height();
							calp.parent().css({height:oriHeight}).animate({height:h}, 200, function(){
								calp.parent().css({height:'auto'});
								if($.isFunction(options.change))options.change.call(cal.parent(), date);
							});
						});
						break;
					case 1:case 'right':
						calp.prepend(cal);
						calp.children().width(width);
						calp.css({top:0, left:-width, width:width*2}).animate({left:0}, speed, easing, function(){
							cal.css({width:'100%'}).next().remove();
							calp.css({position:'', top:'', left:'', width:'', height:''}).parent().css({height:'auto'});
							h = calp.parent().height();
							calp.parent().css({height:oriHeight}).animate({height:h}, 200, function(){
								calp.parent().css({height:'auto'});
								if($.isFunction(options.change))options.change.call(cal.parent(), date);
							});
						});
						break;
					case 2:case 'bottom':
						calp.prepend(cal);
						calp.children().width(width);
						calp.css({top:-height, left:0, height:height*2}).animate({top:0}, speed, easing, function(){
							cal.css({width:'100%'}).next().remove();
							calp.css({position:'', top:'', left:'', width:'', height:''}).parent().css({height:'auto'});
							h = calp.parent().height();
							calp.parent().css({height:oriHeight}).animate({height:h}, 200, function(){
								calp.parent().css({height:'auto'});
								if($.isFunction(options.change))options.change.call(cal.parent(), date);
							});
						});
						break;
					case 3:case 'left':
						calp.append(cal);
						calp.children().width(width);
						calp.css({top:0, left:0, width:width*2}).animate({left:-width}, speed, easing, function(){
							cal.css({width:'100%'}).prev().remove();
							calp.css({position:'', top:'', left:'', width:'', height:''}).parent().css({height:'auto'});
							h = calp.parent().height();
							calp.parent().css({height:oriHeight}).animate({height:h}, 200, function(){
								calp.parent().css({height:'auto'});
								if($.isFunction(options.change))options.change.call(cal.parent(), date);
							});
						});
						break;
				}
			});
		}else{
			calp.append(cal);
		}
		if(minDate){
			if(typeof minDate === 'string'){
				if(minDate === 'today'){
					minDate = new Date();
				}else{
					let tdn = new Date(), tdt = minDate.match(/^([+-]\d+)\s*(y|year|m|month|d|day)$/);
					if(tdt){
						switch(tdt[2]){
							//case 'y':minDate = new Date(tdn.getTime()+tdt[1]*365*24*3600*1000);break;
							//case 'm':minDate = new Date(tdn.getTime()+tdt[1]*30*24*3600*1000);break;
							//case 'd':minDate = new Date(tdn.getTime()+tdt[1]*24*3600*1000);break;
							case 'y':case 'year':minDate = new Date(tdn.setFullYear(tdn.getFullYear()+Number(tdt[1])));break;
							case 'm':case 'month':minDate = new Date(tdn.setMonth(tdn.getMonth()+Number(tdt[1])));break;
							case 'd':case 'day':minDate = new Date(tdn.setDate(tdn.getDate()+Number(tdt[1])));break;
						}
					}else{
						minDate = (minDate.split(' '))[0];
						minDate = minDate.split('-');
						if(minDate.length === 3)minDate = new Date(minDate[0], minDate[1]-1, minDate[2]);
						else minDate = new Date();
					}
				}
				minDate = new Date(minDate.getFullYear(), minDate.getMonth(), minDate.getDate());
			}
			options.minYear = minDate.getFullYear();
			minFullDate = new Date(minDate.getFullYear(), minDate.getMonth(), minDate.getDate());
		}
		if(maxDate){
			if(typeof maxDate === 'string'){
				if(maxDate === 'today'){
					maxDate = new Date();
				}else{
					let tdn = new Date(), tdt = maxDate.match(/^([+-]\d+)\s*(y|year|m|month|d|day)$/);
					if(tdt){
						switch(tdt[2]){
							case 'y':case 'year':maxDate = new Date(tdn.setFullYear(tdn.getFullYear()+Number(tdt[1])));break;
							case 'm':case 'month':maxDate = new Date(tdn.setMonth(tdn.getMonth()+Number(tdt[1])));break;
							case 'd':case 'day':maxDate = new Date(tdn.setDate(tdn.getDate()+Number(tdt[1])));break;
						}
					}else{
						maxDate = (maxDate.split(' '))[0];
						maxDate = maxDate.split('-');
						if(maxDate.length === 3)maxDate = new Date(maxDate[0], maxDate[1]-1, maxDate[2]);
						else maxDate = new Date();
					}
				}
				maxDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), maxDate.getDate(), 23, 59, 59);
			}
			options.maxYear = maxDate.getFullYear();
			maxFullDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), maxDate.getDate());
		}
		let just = options.just, year = null, week = null;
		if(showYears)just = 'year';
		if(showMonths)just = 'month';
		switch(just){
			case 'year':
				cal.attr('type', 'year');
				tdDay = 1;
				let h, ha;
				if(!!attrDate && $.isDate(attrDate)){
					initdate = new Date(attrDate.getFullYear(), tdMonth,tdDay);
				}
				obj.data({'curYear':initdate.getFullYear(), 'curMonth':initdate.getMonth(), 'curDay':initdate.getDate()});
				h = (initdate.getFullYear() - options.minYear) % 12;
				ha = initdate.getFullYear() - h;
				if(showYears)ha = date.getFullYear();
				let hb = (ha+11>options.maxYear) ? options.maxYear : ha+11;
				year = $('<ul class="yearUL '+(options.hiddenNavBar?'hidden':'')+'"></ul>');
				year.append('<li><a class="pn pnr" href="javascript:void(0)" cal="nextyears" year="' + ha + '"></a><a class="pn pnl" href="javascript:void(0)" cal="prevyears" year="' + ha + '"></a><a class="pc pcy" href="javascript:void(0)" cal="years">' + ha + ' - ' + hb + '</a></li>')
				.append('<p></p>');
				cal.append(year);
				if(options.minYear>=ha){
					let a = year.find('a[cal="prevyears"]');
					a.addClass('disabled');
				}
				if(options.maxYear<=hb){
					let a = year.find('a[cal="nextyears"]');
					a.addClass('disabled');
				}
				if(!options.changeYear)year.find('.pn').addClass('disabled');
				else year.find('li a:eq(2)').addClass('pc');
				for(let i = 0; i <= 2; i++){
					let days = $('<ul class="dayUL dayYearUL"></ul>');
					for(let j = ha+4*i; j <= ha+3+4*i; j++){
						let unDis = true, attr = '', cls = '';
						if((!!!calp.data('curYear') && j===initdate.getFullYear()) || j===calp.data('curYear')){ //current
							attr += ' bg="current"';
							if(j === tdYear)attr += ' today="today"';
							cls = 'current';
						}else{
							if(j === tdYear){ //toyear
								attr += ' bg="today" today="today"';
								cls = 'today';
							}else{ //normal
								attr += '';
								cls = 'normal';
							}
						}
						let curd = new Date(j, tdMonth, tdDay);
						if(options.disDates!==''){
							let disDates = options.disDates, dateSplit = disDates.split('-');
							if(dateSplit.length === 3)disDates = dateSplit[0];
							if(disDates === 'today')disDates = tdYear;
							if((','+disDates+',').indexOf(','+j+',')>-1)unDis = false;
						}
						if(options.minDate.length){
							if(minDate.getFullYear() > curd.getFullYear())unDis = false;
						}
						if(options.maxDate.length){
							if(maxDate.getFullYear() < curd.getFullYear())unDis = false;
						}
						let format = options.format;
						if(showYears || options.range)format = 'yyyy';
						format = curd.formatDate(format);
						if(j>=options.minYear && j<=options.maxYear && unDis === true){
							days.append('<li '+attr+' class="'+cls+'" title="'+format+'" year="' + j + '" month="' + tdMonth + '" date="1"><div>' + j + options.yearText + '</div></li>');
						}else{
							days.append('<li class="disabled '+cls+'" title="'+format+'"><div>' + j + options.yearText + '</div></li>');
						}
					}
					days.append('<p></p>');
					cal.append(days);
				}
				break;
			
			case 'month':
				cal.attr('type', 'month');
				tdDay = 1;
				if(!!attrDate && $.isDate(attrDate)){
					initdate = new Date(attrDate.getFullYear(), attrDate.getMonth(), tdDay);
				}
				obj.data({'curYear':initdate.getFullYear(), 'curMonth':initdate.getMonth(), 'curDay':initdate.getDate()});
				year = $('<ul class="yearUL '+(options.hiddenNavBar?'hidden':'')+'"></ul>');
				year.append('<li><a class="pn pnr" href="javascript:void(0)" cal="nextyear"></a><a class="pn pnl" href="javascript:void(0)" cal="prevyear"></a><a class="pc" href="javascript:void(0)" cal="year" year="' + date.getFullYear() + '">' + date.getFullYear() + options.yearText + '</a></li>')
				.append('<p></p>');
				cal.append(year);
				if(options.minYear>=date.getFullYear()){
					let a = year.find('a[cal="prevyear"]');
					a.addClass('disabled');
				}
				if(options.maxYear<=date.getFullYear()){
					let a = year.find('a[cal="nextyear"]');
					a.addClass('disabled');
				}
				if(!options.changeYear)year.find('.pn').addClass('disabled');
				else year.find('li a:eq(2)').addClass('pc');
				for(let i = 0; i <= 2; i++){
					let days = $('<ul class="dayUL dayYearUL"></ul>');
					for(let j = 1+4*i; j <= 4+4*i; j++){
						let unDis = true, attr = '', cls = '';
						if((!!!calp.data('curYear') && !!!calp.data('curMonth') && date.getFullYear() === initdate.getFullYear() && (j-1) === initdate.getMonth()) || (date.getFullYear() === calp.data('curYear') && (j-1) === calp.data('curMonth'))){ //current
							attr += ' bg="current"';
							if(date.getFullYear() === tdYear && (j-1) === tdMonth)attr += ' today="today"';
							cls = 'current';
						}else{
							if(date.getFullYear() === tdYear && (j-1) === tdMonth){ //tomonth
								attr += ' bg="today" today="today"';
								cls = 'today';
							}else{ //normal
								attr += '';
								cls = 'normal';
							}
						}
						let curd = new Date(date.getFullYear(), j, tdDay);
						if(options.disMonths.length){
							if((','+options.disMonths+',').indexOf(','+j+',')>-1)unDis = false;
						}
						if(options.disDates!==''){
							let disDates = options.disDates, dateSplit = disDates.split('-');
							if(dateSplit.length === 3)disDates = dateSplit[0]+'-'+dateSplit[1];
							if(disDates === 'today')disDates = tdYear+'-'+(tdMonth+1);
							if((','+disDates+',').indexOf(','+date.getFullYear()+'-'+j+',')>-1)unDis = false;
						}
						if(options.minDate.length){
							if(new Date(minDate.getFullYear(),minDate.getMonth(),1) > new Date(curd.getFullYear(),curd.getMonth(),1))unDis = false;
							minFullDate = new Date(minDate.getFullYear(), minDate.getMonth(), minDate.getDate());
						}
						if(options.maxDate.length){
							if(new Date(maxDate.getFullYear(),maxDate.getMonth(),1) < new Date(curd.getFullYear(),maxDate.getMonth(),1))unDis = false;
							maxFullDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), maxDate.getDate());
						}
						let format = options.format;
						if(showMonths || options.range)format = 'yyyy-m';
						let newDat = new Date(date.getFullYear(), j-1, curd.getDate(), curd.getHours(), curd.getMinutes(), curd.getSeconds());
						format = newDat.formatDate(format);
						if(curd>=minFullDate && curd<=maxFullDate && unDis===true){
							days.append('<li '+attr+' class="'+cls+'" title="'+format+'" year="' + date.getFullYear() + '" month="' + (j-1) + '" date="1"><div>' + monthName[j-1] + '</div></li>');
						}else{
							days.append('<li class="disabled '+cls+'" title="'+format+'"><div>' + monthName[j-1] + '</div></li>');
						}
					}
					days.append('<p></p>');
					cal.append(days);
				}
				break;
			
			default:
				cal.attr('type', 'day');
				if(!!attrDate && $.isDate(attrDate)){
					initdate = new Date(attrDate.getFullYear(), attrDate.getMonth(), attrDate.getDate(), attrDate.getHours(), attrDate.getMinutes(), attrDate.getSeconds());
				}
				obj.data({'curYear':initdate.getFullYear(), 'curMonth':initdate.getMonth(), 'curDay':initdate.getDate(), 'curHour':initdate.getHours(), 'curMinute':initdate.getMinutes(), 'curSecond':initdate.getSeconds()});
				year = $('<ul class="yearUL '+(options.hiddenNavBar?'hidden':'')+'"></ul>');
				year.append('<li><a class="pn pnr" href="javascript:void(0)" cal="nextmonth"></a><a class="pn pnl" href="javascript:void(0)" cal="prevmonth"></a><a href="javascript:void(0)" cal="month" month="' + date.getFullYear() + '-' + date.getMonth() + '">' + date.getFullYear() + options.yearText + (options.enText ? ' - ' : '') + (date.getMonth()+1) + options.monthText + '</a></li>')
				.append('<p></p>');
				cal.append(year);
				if(new Date(minFullDate.getFullYear(),minFullDate.getMonth(),1) >= new Date(date.getFullYear(),date.getMonth(),1)){
					let a = year.find('a[cal="prevmonth"]');
					a.addClass('disabled');
				}
				if(new Date(maxFullDate.getFullYear(),maxFullDate.getMonth(),1) <= new Date(date.getFullYear(),date.getMonth(),1)){
					let a = year.find('a[cal="nextmonth"]');
					a.addClass('disabled');
				}
				if(!options.changeMonth)year.find('.pn').addClass('disabled');
				else year.find('li a:eq(2)').addClass('pc');
				week = $('<ul class="weekUL"></ul>');
				for(let i = 0; i < 7; i++){
					let worship = weekClass[i];
					week.append('<li class="'+worship+'"><div>' + weekName[i] + '</div></li>');
				}
				week.append('<p></p>');
				cal.append(week);
				for(let i = 0; i < 6; i++){
					let days = $('<ul class="dayUL"></ul>');
					for(let j = 0; j < 7; j++){
						let d = 7 * i - month.firstDay + j + 1, unDis = true, attr = '', cls = '', worship = weekClass[j];
						let inArray = false, everyDate = new Date(date.getFullYear(), date.getMonth(), d, 0, 0, 0);
						if(options.multiple){
							for(let s=0; s<dates.length; s++){
								if(Date.parse(everyDate) === Date.parse(dates[s])){inArray = true;break}
							}
						}else if(options.range){
							if(dates[0]<=Date.parse(everyDate) && Date.parse(everyDate)<=dates[dates.length-1])inArray = true;
						}else{
							inArray = (date.getFullYear() === initdate.getFullYear() && date.getMonth() === initdate.getMonth() && d === initdate.getDate());
						}
						if(inArray){ //current
							attr += ' bg="current"';
							cls = 'current '+worship;
							if(date.getFullYear() === tdYear && date.getMonth() === tdMonth && d === tdDay){
								attr += ' today="today"';
								cls += ' today';
							}
						}else{
							if(date.getFullYear() === tdYear && date.getMonth() === tdMonth && d === tdDay){ //today
								attr += ' bg="today" today="today"';
								cls = 'today '+worship;
							}else{ //normal
								attr += '';
								cls = 'normal '+worship;
							}
						}
						if(d>0 && d<=month.days){
							let curd = new Date(date.getFullYear(), date.getMonth(), d);
							if(options.disMonths.length){
								if((','+options.disMonths+',').indexOf(','+(date.getMonth()+1)+',')>-1)unDis = false;
							}
							if(options.disDays.length){
								if((','+options.disDays+',').indexOf(','+d+',')>-1)unDis = false;
							}
							if(options.disWeeks.length){
								if((','+options.disWeeks+',').indexOf(','+j+',')>-1)unDis = false;
							}
							if(options.disDates.length){
								let disDates = options.disDates;
								if(disDates === 'today')disDates = tdYear+'-'+(tdMonth+1)+'-'+tdDay;
								if((','+disDates+',').indexOf(','+date.getFullYear()+'-'+(date.getMonth()+1)+'-'+d+',')>-1)unDis = false;
							}
							if(options.minDate.length){
								if(minDate > curd)unDis = false;
							}
							if(options.maxDate.length){
								if(maxDate < curd)unDis = false;
							}
							let format = options.format, text = d+'', badger = '';
							if(options.range)format = 'yyyy-m-d';
							let newDat = new Date(curd.getFullYear(), curd.getMonth(), curd.getDate(), initdate.getHours(), initdate.getMinutes(), initdate.getSeconds());
							format = newDat.formatDate(format);
							for(let o in options.mark){
								if(options.mark.hasOwnProperty(o)){
									let s = o.split('-'), sd = null;
									if(s.length === 3){
										if(/^0+$/.test(s[0])){
											sd = new Date(curd.getFullYear(), Number(s[1])-1, Number(s[2]));
										}else{
											sd = o.date();
										}
										if(sd && sd.getFullYear() === curd.getFullYear() && sd.getMonth() === curd.getMonth() && sd.getDate() === curd.getDate()){
											badger += ' badger';
											if(options.mark[o].length){text = options.mark[o];badger += ' mark'}
										}
									}
								}
							}
							if(curd>=minFullDate && curd<=maxFullDate && unDis === true){
								let li = $('<li '+attr+' class="'+cls+badger+'" title="'+format+'" date="'+(curd.getFullYear()+'-'+(curd.getMonth()+1)+'-'+curd.getDate())+'" year="' + date.getFullYear() + '" month="' + date.getMonth() + '" day="' + d + '"><div>' + text + '</div></li>');
								days.append(li);
								if($.isFunction(options.date))options.date.call(li, curd);
							}else{
								let li = $('<li class="disabled '+cls+badger+'" title="'+format+'" date="'+(curd.getFullYear()+'-'+(curd.getMonth()+1)+'-'+curd.getDate())+'"><div>' + text + '</div></li>');
								days.append(li);
								if($.isFunction(options.date))options.date.call(li, curd);
							}
						}else if(d<=month.days || (d>month.days && j>0 && j<=6)){
							days.append('<li class="notcurrent"><div>&nbsp;</div></li>');
						}else{
							break;
						}
					}
					days.append('<p></p>');
					cal.append(days);
				}
				if(!cal.find('ul:last').find('li').length)cal.find('ul:last').remove();
				if(!options.range && !options.multiple && options.showTime && (options.showHour || options.showMinute))showTime();
				if(options.range && dates.length === 2)betweenDate();
				if(options.fullscreen && !calp.parent().parent().find('.datepickerClose').length){
					let p = calp.parent().parent();
					p.append('<a class="datepickerClose" href="javascript:void(0)"></a>');
					p.find('.datepickerClose').on('click', function(){
						p.removeClass('datepickerViewFullscreen-x');
						p.data('removeControl')();
						setTimeout(function(){p.remove()}, 300);
					});
				}
				break;
		}
		if(options.touchMove)touchMove();
		function showTime(){
			if(calp.parent().find('.hnsView').length)return;
			let hnsView = $('<div class="hnsView" style="'+(options.showCal?'display:none;':'')+'"></div>'), hnsNumber = $('<div class="hnsNumber"></div>'), html = '';
			let placeholderZero = function(str){return (str+'').length === 1 ? '0'+str : str};
			if(options.showHour){
				let _hour = initdate.getHours(), hours = [9, 10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8];
				html = '<div class="number hourNumber numberHidden" data-number="'+_hour+'"><div>';
				$.each(hours, function(i, n){
					let text = n;
					if(_hour>12)text += 12;
					html += '<div class="n '+((n === _hour || (n-12) === _hour || (n+12) === _hour)?'this':'')+'"><span class="m" data-number="'+text+'">'+text+'</span></div>';
				});
				html += '<em class="hnsPointer hnsHour"></em>\
						<span class="t">'+(_hour>12?'PM':'AM')+'</span>\
					</div>\
				</div>';
				hnsNumber.append(html);
				let hourNumber = hnsNumber.find('.hourNumber');
				hourNumber.find('div div').each(function(i){
					$(this).css({'-webkit-transform':'rotate('+(i*30)+'deg)', 'transform':'rotate('+(i*30)+'deg)'});
				});
				hourNumber.find('div div span').each(function(i){
					$(this).css({'-webkit-transform':'rotate('+(i*-30)+'deg)', 'transform':'rotate('+(i*-30)+'deg)'});
				});
				hourNumber.find('.hnsHour').css({'-webkit-transform':'rotate('+((_hour+3)*30)+'deg)', 'transform':'rotate('+((_hour+3)*30)+'deg)'});
				hourNumber.children('div').children('span').on('mouseup touchend', function(){
					let span = $(this), hourSpan = hourNumber.find('div div span');
					if(span.html() === 'AM'){
						span.html('PM');
						let number = Number(hourNumber.attr('data-number'));
						if(number === 12)number = -12;
						hourNumber.attr('data-number', number+12);
						hnsNumber.find('.hourText i').html(number+12);
						hourSpan.each(function(){
							let n = Number($(this).html())+12;
							$(this).attr('data-number', n).html(n);
						});
					}else{
						span.html('AM');
						let number = Number(hourNumber.attr('data-number'));
						if(number === 0)number = 24;
						hourNumber.attr('data-number', number-12);
						hnsNumber.find('.hourText i').html(number-12);
						hourSpan.each(function(){
							let n = Number($(this).html())-12;
							$(this).attr('data-number', n).html(n);
						});
					}
				});
				let isHourStartDrag = false, isHourMoveDrag = false, hourOrigin = {}, hourStartDrag = function(e){
						e.preventDefault();
						let o = e.target;
						if($(o).hasClass('t'))return;
						isHourStartDrag = true;
						hourNumber.on('mousemove', hourMoveDrag);
						if(window.addEventListener)hourNumber[0].addEventListener('touchmove', hourMoveDrag, true);
						let span = hourNumber.children('div').children('span'), spanWidth = span.width(), spanHeight = span.height(), spanOffset = span.offset();
						hourOrigin = {x:spanOffset.left+spanWidth/2, y:spanOffset.top+spanHeight/2}; //当前元素的中心点
						hourMoveDrag(e);
					},
					hourMoveDrag = function(e){
						e.preventDefault();
						isHourMoveDrag = true;
						let isPM = hourNumber.children('div').children('span').html() === 'PM';
						let x = $.touches(e).x, y = $.touches(e).y;
						//计算出当前鼠标相对于元素中心点的坐标
						x = x - hourOrigin.x;
						y = hourOrigin.y - y;
						let unit = Math.PI / 6, radian = Math.atan2(x, y);
						if(radian < 0)radian = Math.PI * 2 + radian;
						let hour = Math.round(radian / unit);
						if(isPM)hour += 12;
						if(!isPM && hour === 0)hour = 12;
						if(hour>=24 || (isPM && hour === 12))hour = 0;
						hourNumber.attr('data-number', hour);
						hnsNumber.find('.hourText i').html(hour);
						hourNumber.find('.hnsHour').css({'-webkit-transform':'rotate('+((hour+3)*30)+'deg)', 'transform':'rotate('+((hour+3)*30)+'deg)'});
						hourNumber.find('div div.this').removeClass('this');
					},
					hourEndDrag = function(e){
						e.preventDefault();
						hourNumber.off('mousemove', hourMoveDrag);
						if(window.addEventListener)hourNumber[0].removeEventListener('touchmove', hourMoveDrag, true);
						if(isHourStartDrag){
							if(options.showMinute){
								setTimeout(function(){
									hnsNumber.find('.hourNumber').addClass('numberFadeOut');
									hnsNumber.find('.minuteNumber').show().removeClass('numberHidden');
									if(!options.showCal)hnsNumber.children('a.ret').fadeIn(200);
								}, 300);
							}
						}
						isHourStartDrag = false;
						isHourMoveDrag = false;
					};
				hourNumber.on('mousedown', hourStartDrag).on('mouseup mouseleave', hourEndDrag);
				if(window.addEventListener){
					hourNumber[0].addEventListener('touchstart', hourStartDrag, true);
					hourNumber[0].addEventListener('touchend', hourEndDrag, true);
					hourNumber[0].addEventListener('touchcancel', hourEndDrag, true);
				}
			}
			if(options.showMinute){
				let _minute = initdate.getMinutes(), minutes = [];
				html = '<div class="number minuteNumber numberHidden" data-number="'+_minute+'" style="display:none;"><div>';
				for(let i=45; i<=59; i+=5)minutes.push(i);
				for(let i=0; i<=44; i+=5)minutes.push(i);
				$.each(minutes, function(i, n){
					let text = n;
					text = placeholderZero(text);
					html += '<div class="n '+(n === _minute?'this':'')+'"><span class="m" data-number="'+n+'">'+text+'</span></div>';
				});
				html += '<em class="hnsPointer hnsMinute"><span></span></em>\
						</div>\
					</div>';
				hnsNumber.append(html);
				let minuteNumber = hnsNumber.find('.minuteNumber');
				minuteNumber.find('div div').each(function(i){
					$(this).css({'-webkit-transform':'rotate('+(i*30)+'deg)', 'transform':'rotate('+(i*30)+'deg)'});
				});
				minuteNumber.find('div div span').each(function(i){
					$(this).css({'-webkit-transform':'rotate('+(i*-30)+'deg)', 'transform':'rotate('+(i*-30)+'deg)'});
				});
				minuteNumber.find('.hnsMinute').css({'-webkit-transform':'rotate('+((_minute+(60-45))*6)+'deg)', 'transform':'rotate('+((_minute+(60-45))*6)+'deg)'});
				let isMinuteStartDrag = false, isMinuteMoveDrag = false, minuteOrigin = {}, minuteStartDrag = function(e){
						e.preventDefault();
						isMinuteStartDrag = true;
						minuteNumber.on('mousemove', minuteMoveDrag);
						if(window.addEventListener)minuteNumber[0].addEventListener('touchmove', minuteMoveDrag, true);
						let span = minuteNumber.find('.hnsMinute span'), spanWidth = span.width(), spanHeight = span.height(), spanOffset = span.offset();
						minuteOrigin = {x:spanOffset.left+spanWidth/2, y:spanOffset.top+spanHeight/2}; //当前元素的中心点
						minuteMoveDrag(e);
					},
					minuteMoveDrag = function(e){
						e.preventDefault();
						isMinuteMoveDrag = true;
						let x = $.touches(e).x, y = $.touches(e).y;
						x = x - minuteOrigin.x;
						y = minuteOrigin.y - y;
						let unit = Math.PI / 30, radian = Math.atan2(x, y);
						if(radian < 0)radian = Math.PI * 2 + radian;
						let minute = Math.round(radian / unit);
						if(minute>=60)minute = 0;
						minuteNumber.attr('data-number', minute);
						hnsNumber.find('.minuteText i').html(placeholderZero(minute));
						//let degree = Math.atan2(y, x) / (Math.PI / 180) + 180;
						//degree = -degree;
						minuteNumber.find('.hnsMinute').css({'-webkit-transform':'rotate('+((minute+(60-45))*6)+'deg)', 'transform':'rotate('+((minute+(60-45))*6)+'deg)'});
						minuteNumber.find('div div.this').removeClass('this');
					},
					minuteEndDrag = function(e){
						e.preventDefault();
						minuteNumber.off('mousemove', minuteMoveDrag);
						if(window.addEventListener)minuteNumber[0].removeEventListener('touchmove', minuteMoveDrag, true);
						if(isMinuteStartDrag){
							let x = $.touches(e).x, y = $.touches(e).y;
							x = x - minuteOrigin.x;
							y = minuteOrigin.y - y;
							let unit = Math.PI / 30, radian = Math.atan2(x, y);
							if(radian < 0)radian = Math.PI * 2 + radian;
							let minute = Math.round(radian / unit);
							if(minute>=60)minute = 0;
							minuteNumber.attr('data-number', minute);
							hnsNumber.find('.minuteText i').html(placeholderZero(minute));
						}
						isMinuteStartDrag = false;
						isMinuteMoveDrag = false;
					};
				minuteNumber.on('mousedown', minuteStartDrag).on('mouseup mouseleave', minuteEndDrag);
				if(window.addEventListener){
					minuteNumber[0].addEventListener('touchstart', minuteStartDrag, true);
					minuteNumber[0].addEventListener('touchend', minuteEndDrag, true);
					minuteNumber[0].addEventListener('touchcancel', minuteEndDrag, true);
				}
			}
			calp.parent().append(hnsView);
			hnsView.append(hnsNumber);
			html = '<a href="javascript:void(0)" class="ret" style="display:none;"></a>\
				<a href="javascript:void(0)" class="ok" style="'+(options.showCal?'display:none;':'')+'"></a>\
				<span style="'+(options.showCal?'display:none;':'')+'">';
			if(options.showMinute)html += '<span class="minuteText"><i>'+placeholderZero(initdate.getMinutes())+'</i></span>';
			if(options.showHour)html += '<span class="hourText"><i>'+initdate.getHours()+'</i></span>';
			html += '</span>';
			hnsNumber.append(html);
			hnsNumber.children('a').on('dragstart', function(e){e.preventDefault()});
			hnsNumber.children('a.ret').on('click', function(){
				let minuteNumber = hnsNumber.find('.minuteNumber');
				if(options.showMinute && minuteNumber.length && !minuteNumber.hasClass('numberHidden')){
					if(!options.showCal)$(this).fadeOut(200);
					hnsNumber.find('.hourNumber').removeClass('numberFadeOut');
					minuteNumber.addClass('numberHidden');
					setTimeout(function(){
						minuteNumber.hide();
					}, 400);
				}else{
					hnsNumber.children('a').hide();
					hnsNumber.children('span').fadeOut(200);
					hnsNumber.find('.number').addClass('numberHidden');
					calp.removeClass('calFadeOut');
					setTimeout(function(){
						hnsView.hide();
					}, 400);
				}
			});
			hnsNumber.children('a.ok').on('click', function(){
				let hourNumber = hnsNumber.find('.hourNumber'), minuteNumber = hnsNumber.find('.minuteNumber');
				let hour = initdate.getHours(), minute = initdate.getMinutes(), second = initdate.getSeconds();
				if(options.showHour)hour = Number(hourNumber.attr('data-number'));
				if(options.showMinute)minute = Number(minuteNumber.attr('data-number'));
				let _obj = calp.find('.dayUL li.current'), toDay = new Date(),
					year = !!_obj.attr('year') ? Number(_obj.attr('year')) : toDay.getFullYear(),
					month = !!_obj.attr('month') ? Number(_obj.attr('month')) : toDay.getMonth(),
					day = !!_obj.attr('day') ? Number(_obj.attr('day')) : toDay.getDate(),
					dates = new Date(year, month, day, hour, minute, second);
				washFunction([dates]);
				if(options.always){
					hnsNumber.children('a').hide();
					hnsNumber.find('.number').removeClass('numberFadeOut').addClass('numberHidden');
					calp.removeClass('calFadeOut');
					setTimeout(function(){
						hnsNumber.find('.minuteNumber').hide();
						hnsView.hide();
					}, 400);
				}
			});
			if(!options.showCal){
				calp.css('opacity', 0);
				setTimeout(function(){
					let hnsWidth = hnsView.outerWidth(false);
					if(hnsWidth > hnsView.outerHeight(false))hnsWidth = hnsView.outerHeight(false);
					hnsView.find('.number').children('div').css({width:hnsWidth-10*2, height:hnsWidth-10*2});
					hnsView.find('.numberHidden:eq(0)').removeClass('numberHidden');
				}, 50);
			}
		}
		function changePrevMonth(){
			if(!options.changeMonth)return false;
			if(options.minYear === date.getFullYear() && minFullDate.getMonth()>date.getMonth()-1)return false;
			date.setDate(1);
			date.setMonth(date.getMonth()-1);
			InitCalendar(cal.parent(), date, 'right');
			if($.isFunction(options.prevMonthCallback))setTimeout(function(){options.prevMonthCallback.call(cal.parent(), date)}, 10);
		}
		function changeNextMonth(){
			if(!options.changeMonth)return false;
			if(options.maxYear === date.getFullYear() && maxFullDate.getMonth()<date.getMonth()+1)return false;
			date.setDate(1);
			date.setMonth(date.getMonth()+1);
			InitCalendar(cal.parent(), date, 'left');
			if($.isFunction(options.nextMonthCallback))setTimeout(function(){options.nextMonthCallback.call(cal.parent(), date)}, 10);
		}
		if(options.prevMonth)$(options.prevMonth).off('click').on('click', changePrevMonth);
		if(options.nextMonth)$(options.nextMonth).off('click').on('click', changeNextMonth);
		cal.find('.yearUL a').on('dragstart', function(e){e.preventDefault()});
		cal.find('.yearUL a, .dayUL li:not(.notcurrent,.disabled)').focus(function(){
			this.blur();
		}).hover(function(){
			if(!$(this).attr('cal') && !$(this).attr('bg'))$(this).addClass('hover');
		}, function(){
			if(!$(this).attr('cal') && !$(this).attr('bg'))$(this).removeClass('hover');
		}).click(function(){
			if($(this).attr('cal') === 'prevyears'){
				if(!options.changeYear)return false;
				if(cal.find('.hns').length && cal.find('.hns').hasClass('cal'))return false;
				let tmpDate = $(this).attr('year')*1-1;
				if(minFullDate.getFullYear()>tmpDate)return false;
				date = new Date($(this).attr('year')*1-12, date.getMonth(), date.getDate());
				InitCalendar(cal.parent(), date, 'right', true);
			}else if($(this).attr('cal') === 'nextyears'){
				if(!options.changeYear)return false;
				if(cal.find('.hns').length && cal.find('.hns').hasClass('cal'))return false;
				let tmpDate = $(this).attr('year')*1+12;
				if(maxFullDate.getFullYear()<tmpDate)return false;
				date = new Date($(this).attr('year')*1+12, date.getMonth(), date.getDate());
				InitCalendar(cal.parent(), date, 'left', true);
			}else if($(this).attr('cal') === 'prevyear'){
				if(!options.changeYear)return false;
				if(cal.find('.hns').length && cal.find('.hns').hasClass('cal'))return false;
				let tmpDate = date.getFullYear()-1;
				if(minFullDate.getFullYear()>tmpDate)return false;
				date.setFullYear(date.getFullYear()-1);
				InitCalendar(cal.parent(), date, 'right', calp.data('selectYear'), calp.data('selectMonth'));
			}else if($(this).attr('cal') === 'nextyear'){
				if(!options.changeYear)return false;
				if(cal.find('.hns').length && cal.find('.hns').hasClass('cal'))return false;
				let tmpDate = date.getFullYear()+1;
				if(maxFullDate.getFullYear()<tmpDate)return false;
				date.setFullYear(date.getFullYear()+1);
				InitCalendar(cal.parent(), date, 'left', calp.data('selectYear'), calp.data('selectMonth'));
			}else if($(this).attr('cal') === 'prevmonth'){
				if(cal.find('.hns').length && cal.find('.hns').hasClass('cal'))return false;
				changePrevMonth();
			}else if($(this).attr('cal') === 'nextmonth'){
				if(cal.find('.hns').length && cal.find('.hns').hasClass('cal'))return false;
				changeNextMonth();
			}else if($(this).attr('cal') === 'years'){
				return false;
			}else if($(this).attr('cal') === 'year'){
				if(!options.changeYear)return false;
				if(cal.find('.hns').length && cal.find('.hns').hasClass('cal'))return false;
				calp.data('curYear', date.getFullYear());
				let h = (date.getFullYear() - options.minYear) % 12, ha = date.getFullYear() - h;
				date.setFullYear(ha);
				calp.data('selectYear', true);
				InitCalendar(cal.parent(), date, 'bottom', true);
			}else if($(this).attr('cal') === 'month'){
				if(!options.changeMonth)return false;
				if(cal.find('.hns').length && cal.find('.hns').hasClass('cal'))return false;
				calp.data('curYear', date.getFullYear());
				calp.data('curMonth', date.getMonth());
				calp.data('selectMonth', true);
				InitCalendar(cal.parent(), date, 'bottom', false, true);
			}else{
				if(options.disable)return false;
				let _obj = $(this), toDay = new Date(), newDate = '',
					year = !!_obj.attr('year') ? Number(_obj.attr('year')) : toDay.getFullYear(),
					month = !!_obj.attr('month') ? Number(_obj.attr('month')) : toDay.getMonth(),
					day = !!_obj.attr('day') ? Number(_obj.attr('day')) : toDay.getDate();
				if(!!calp.data('selectYear')){
					if(!options.changeYear)return false;
					date.setDate(1);
					date.setFullYear(year);
					calp.removeData('selectYear');
					InitCalendar(cal.parent(), date, 'top', false, calp.data('selectMonth'));
					calp.removeData('curYear').removeData('curMonth');
					return false;
				}
				if(!!calp.data('selectMonth')){
					if(!options.changeMonth)return false;
					date.setDate(1);
					date.setMonth(month);
					calp.removeData('selectMonth');
					InitCalendar(cal.parent(), date, 'top');
					calp.removeData('curYear').removeData('curMonth');
					return false;
				}
				if(options.showTime){
					let hnsView = calp.parent().find('.hnsView'), hour = initdate.getHours(), minute = initdate.getMinutes(), second = initdate.getSeconds();
					if(options.showHour)hour = Number(hnsView.find('.hourNumber').attr('data-number'));
					if(options.showMinute)minute = Number(hnsView.find('.minuteNumber').attr('data-number'));
					newDate = new Date(year, month, day, hour, minute, second);
				}else{
					newDate = new Date(year, month, day, 0, 0, 0);
				}
				if(!_obj.hasClass('current') || options.range){
					if(options.range){
						if(endDate){
							dates = [];
							beginDate = null;
							endDate = null;
							calp.find('.current').each(function(){
								$(this).removeAttr('bg').removeClass('current').addClass('normal');
								if(!!$(this).attr('today'))$(this).attr('bg', 'today').addClass('today');
							});
							calp.find('.between').each(function(){
								$(this).removeAttr('bg').removeClass('between').addClass('normal');
								if(!!$(this).attr('today'))$(this).attr('bg', 'today').addClass('today');
							});
						}
						if(!beginDate){
							beginDate = newDate;
							dates.push(newDate);
						}else{
							if(Date.parse(beginDate)>Date.parse(newDate)){
								endDate = beginDate;
								beginDate = newDate;
								dates.unshift(newDate);
							}else{
								endDate = newDate;
								dates.push(newDate);
							}
						}
					}else{
						if(!options.multiple){
							let old = calp.find('.current');
							dates = [];
							if(old.length){
								old.removeAttr('bg').removeClass('current').addClass('normal');
								if(!!old.attr('today'))old.attr('bg', 'today').addClass('today');
							}
						}
						dates.push(newDate);
					}
					_obj.attr('bg', 'current').removeClass('hover').addClass('current');
					if(options.range && dates.length === 2)betweenDate();
				}else if(options.multiple){
					_obj.removeAttr('bg').removeClass('current').addClass('normal');
					if(!!_obj.attr('today'))_obj.attr('bg', 'today').addClass('today');
					for(let d=0; d<dates.length; d++){
						if(dates[d].getTime() === newDate.getTime())dates.splice(d, 1);
					}
				}else{
					dates = [newDate];
				}
				if(!options.range && !options.multiple){
					let hnsView = calp.parent().find('.hnsView');
					if(hnsView.length){
						let hnsWidth = hnsView.outerWidth(false), g = 10;
						if(options.fullscreen)g = 40;
						if(hnsWidth > hnsView.outerHeight(false))hnsWidth = hnsView.outerHeight(false);
						hnsView.find('.number').children('div').css({width:hnsWidth-g*2, height:hnsWidth-g*2});
						hnsView.show();
						calp.addClass('calFadeOut');
						setTimeout(function(){
							hnsView.find('.numberHidden:eq(0)').removeClass('numberHidden');
						}, 0);
						setTimeout(function(){
							hnsView.find('.hnsNumber').children('a').fadeIn(200);
							hnsView.find('.hnsNumber').children('span').fadeIn(200);
						}, 300);
						return false;
					}
				}
				if(options.range){
					if(dates.length === 2)washFunction();
				}else{
					washFunction();
				}
			}
		});
		function washFunction(_dates){
			$.each(options, function(key, fn){
				let isBreakKey = ((key === 'click' && options.breakClick) || $.inArray(key, ['date', 'prevMonthCallback', 'nextMonthCallback', 'shown', 'hidden', 'change', 'complete']) > -1);
				if($.isFunction(fn) && !isBreakKey)fn.call(options.obj, typeof _dates === 'undefined' ? dates : _dates);
			});
		}
		function betweenDate(){
			let current = cal.find('.current');
			if(!current.length){
				let unix = Date.parse(cal.find('.dayUL li[title]').eq(0).attr('date').date());
				if(unix > Date.parse(beginDate) && unix < Date.parse(endDate)){
					cal.find('.dayUL li[title]').each(function(){
						let obj = $(this);
						obj.attr('bg', 'between').removeClass('hover').addClass('between');
					});
				}
			}else{
				let li = current.eq(0), obj = li, liDate = li.attr('date');
				if(Date.parse(liDate.date()) === Date.parse(endDate)){
					if(Date.parse(liDate.date()) !== Date.parse(beginDate)){
						while(obj){
							if(obj.prev().length && !obj.prev().is('.notcurrent')){
								obj = obj.prev();
							}else{
								if(obj.parent().prev().is('.weekUL'))break;
								obj = obj.parent().prev().children().last();
							}
							obj.attr('bg', 'between').removeClass('hover').addClass('between');
						}
					}
				}else{
					while(!obj.is(current.eq(1))){
						if(obj.next().length && obj.next().is('li')){
							obj = obj.next();
						}else{
							if(!obj.parent().next().length)break;
							obj = obj.parent().next().children().eq(0);
						}
						obj.attr('bg', 'between').removeClass('hover').addClass('between');
					}
				}
			}
		}
		function touchMove(){
			if(!options.changeYear || !options.changeMonth)return;
			let type = cal.attr('type'), startX, startY, bindMoveDrag = function(e){
					startX = $.touches(e).x;
					startY = $.touches(e).y;
					cal.on('mousemove touchmove', moveDrag);
				},
				startDrag = function(e){
					let o = $.etarget(e);
					do{
						if($(o).is('.hnsView'))return;
						if($(o).is('.childrenDIV')){bindMoveDrag(e);return}
						o = o.parentNode;
					}while(o.parentNode);
				},
				moveDrag = function(e){
					e.preventDefault();
					return true;
				},
				endDrag = function(e){
					let a, curX, curY, touchX, touchY;
					cal.off('mousemove touchmove', moveDrag);
					curX = $.touches(e).x;
					curY = $.touches(e).y;
					touchX = curX - startX;
					touchY = curY - startY;
					if(touchX>0 && touchY<15 && touchY>-15){ //prev
						switch(type){
							case 'year':a = cal.find('[cal="prevyears"]');break;
							case 'month':a = cal.find('[cal="prevyear"]');break;
							default:a = cal.find('[cal="prevmonth"]');break;
						}
						if(!a.length)return;
						a.click();
					}else if(touchX<0 && touchY<15 && touchY>-15){ //next
						switch(type){
							case 'year':a = cal.find('[cal="nextyears"]');break;
							case 'month':a = cal.find('[cal="nextyear"]');break;
							default:a = cal.find('[cal="nextmonth"]');break;
						}
						if(!a.length)return;
						a.click();
					}else if(touchX<15 && touchX>-15 && touchY>0){ //select
						switch(type){
							case 'year':a = cal.find('[cal="years"]');break;
							case 'month':a = cal.find('[cal="year"]');break;
							default:a = cal.find('[cal="month"]');break;
						}
						if(!a.length)return;
						a.click();
					}else if(touchX<15 && touchX>-15 && touchY<0){
						if(type !== 'day'){
							a = cal.find('.current a');
							if(!a.length)return;
							a.click();
						}
					}
				};
			cal.unselect().on('mousedown touchstart', startDrag).on('mouseup touchend', endDrag);
		}
	}
	return this.each(function(){
		area = $(this);
		let cal = area.children('div').children('div').eq(0), date = dates.length ? (dates[dates.length-1]).Clone() : new Date();
		InitCalendar(cal, date);
		if($.isFunction(options.complete))setTimeout(function(){options.complete.call(cal.parent(), date)}, 10);
	});
};

//解决拖曳与点击冲突
$.fn.tapper = function(fn){
	/*
	//模拟点击
	return this.each(function(){
		if($.browser.msie){this.click()}
		else{
			let e = document.createEvent('MouseEvent');
			e.initEvent('click', true, true);
			this.dispatchEvent(e);
		}
	});
	//新建链接并点击
	setTimeout(function(){
		let a = document.createElement('a');
		a.href = href;a.rel = 'noreferrer';a.click();
	}, 1);
	*/
	let isTouch = 'ontouchend' in document.createElement('div'),
		start = isTouch ? 'touchstart' : 'mousedown',
		move = isTouch ? 'touchmove' : 'mousemove',
		end = isTouch ? 'touchend' : 'mouseup',
		cancel = isTouch ? 'touchcancel' : 'mouseout',
		doc = $(document.body);
	if(typeof fn === 'undefined'){
		return this.trigger(start).trigger(end);
	}
	if(typeof fn === 'boolean' && !fn){
		doc.off(move, this.data('tapper.move'));
		return this.off(start, this.data('tapper.start')).off(end, this.data('tapper.end')).off(cancel, this.data('tapper.end'));
	}
	return this.each(function(){
		let _this = $(this), i = {target:this};
		function onStart(e){
			let p = $.browser.mobile ? ((('touches' in e) && e.touches) ? e.touches[0] : (isTouch ? window.event.touches[0] : window.event)) : e;
			i.startX = p.clientX || 0;
			i.startY = p.clientY || 0;
			i.endX = p.clientX || 0;
			i.endY = p.clientY || 0;
			i.startTime = + new Date;
			doc.on(move, onMove);
		}
		function onMove(e){
			let p = $.browser.mobile ? ((('touches' in e) && e.touches) ? e.touches[0] : (isTouch ? window.event.touches[0] : window.event)) : e;
			i.endX = p.clientX;
			i.endY = p.clientY;
		}
		function onEnd(e){
			doc.off(move, onMove);
			if((+ new Date) - i.startTime < 300){
				if(Math.abs(i.endX-i.startX) + Math.abs(i.endY-i.startY) < 20){
					e = e || window.event;
					e.preventDefault();
					fn = _this.data('tapper.fn');
					let res = fn.call(i.target, e);
					if(typeof res === 'boolean')return res;
				}
			}
			i = {target:i.target};
		}
		_this.on(start, onStart).on(end, onEnd).on(cancel, onEnd).data({'tapper.fn':fn, 'tapper.start':onStart, 'tapper.move':onMove, 'tapper.end':onEnd});
	});
};

//AJAX上传文件, $(this.$refs.div).component(this).ajaxupload(options)
$.fn.ajaxupload = function(options) {
	options = $.extend({
		url: '/upload', //上传提交的目标网址
		name: 'filename', //非file控件上传时指定的提交控件名称
		fileType: ['jpg', 'jpeg', 'png', 'gif', 'bmp'], //允许上传文件类型,后缀名,数组或字符串(逗号隔开)
		data: null, //上传时一同提交的数据
		multiple: false, //多文件选择(只支持HTML5浏览器)
		before: null, //上传前执行, 若返回false即终止上传, 接受三个参数: e,选择的文件数量,选择的文件
		cancel: null, //终止上传后执行
		success: null, //上传操作完毕后返回的回调函数
		error: null, //上传操作失败后执行
		complete: null, //上传操作完毕后执行
		debug: false //保留iframe,form
	}, options)
	let fileType = options.fileType, result
	if (typeof fileType === 'string' && fileType.length) fileType = fileType.split(',')
	let component = this.component()
	if ( !component || !(component instanceof Vue) ) {
		alert('Use ajaxupload plugins must be set component')
		return this
	}
	return this.each(function() {
		this.style.position = 'relative'
		let parent = this
		let width = parent.clientWidth, height = parent.clientHeight
		let count = 0
		const insertFileInput = () => {
			count++
			let input = document.createElement('input')
			input.type = 'file'
			input.name = options.name
			input.id = 'input' + count
			input.setAttribute('style', 'position:absolute;z-index:999;top:0;right:0;opacity:0;margin:0;width:'+width+'px;height:'+height+'px;font-size:'+height+'px;cursor:pointer;')
			parent.appendChild(input)
			input.addEventListener('change', (e) => {
				let files = e.target.files
				if($.isFunction(options.before)){
					result = options.before.call($(parent), e, files.length, files);
					if (typeof result === 'boolean' && !result) {
						input.parentNode.removeChild(input)
						insertFileInput()
						if ($.isFunction(options.cancel)) options.cancel.call($(parent))
						return
					}
				}
				let promiseArr = []
				for (let i = 0; i < files.length; i++) {
					promiseArr.push(new Promise((resolve, reject) => {
						if (fileType.length && !new RegExp('\.('+fileType.join('|')+')$', 'i').test(files[i].name.toLowerCase())) {
							component.$emit('overloaderror', '请选择'+fileType.join(',')+'类型的文件')
							input.parentNode.removeChild(input)
							insertFileInput()
							if ($.isFunction(options.error)) options.error.call($(parent))
							reject()
							return
						}
						fileHandler(files[i])
						resolve()
					}))
				}
				Promise.all(promiseArr).then(() => {
					component.$emit('overload', false)
					if ($.isFunction(options.complete)) options.complete.call($(parent))
				}).catch(() => {})
			})
		}
		const fileHandler = item => {
			const formData = new FormData()
			formData.append(options.name, item)
			if ($.isPlainObject(options.data)) {
				for (let key in options.data) {
					formData.append(key, options.data[key])
				}
			}
			component.$ajax.post(options.url, formData).then(res => {
				if ($.isFunction(options.success)) options.success.call($(parent), res)
			}).catch(() => {
				if ($.isFunction(options.error)) options.error.call($(parent))
			})
		}
		insertFileInput()
	})
}

//加载图片, $(this.$refs.pic).loadpic(url)
$.fn.loadpic = function(url, errorpic, complete) {
	if (typeof errorpic === 'undefined' && typeof complete === 'undefined') errorpic = '../images/nopic.png'
	if (typeof errorpic === 'function' && typeof complete === 'undefined') {
		complete = errorpic
		errorpic = '../images/nopic.png'
	}
	let component = this.component()
	return this.each(function() {
		let item = this
		if (item.$el) item = item.$el
		if (item.tagName.toLowerCase() === 'img') {
			if ( !component || !(component instanceof Vue) ) {
				alert('IMG use loadpic plugins must be set component')
				return
			}
			component.$ajax.get(url, {}, 'blob').then(blob => {
				item.onload = () => {
					complete && complete(item, url, true)
				}
				item.onerror = () => {
					item.src = errorpic
					complete && complete(item, errorpic, false)
				}
				let objectURL = window.URL || window.webkitURL
				item.src = objectURL.createObjectURL(blob)
			}).catch(() => {
				item.src = errorpic
				complete && complete(item, errorpic, false)
			})
			return
		}
		item.style.position = 'relative'
		$(item).children().hide()
		$(item).append('<div class="preloader-gray"></div>')
		const callback = (item, url, state) => {
			complete && complete(item, url, state)
			item.style.position = ''
			$(item).find('.preloader-gray').remove()
			$(item).children().css('display', '')
		}
		let img = new Image()
		img.onload = () => {
			callback(item, url, true)
		}
		img.onerror = () => {
			callback(item, errorpic, false)
		}
		img.src = url
	})
}

//动画加载背景图, attribute、backgroundSize可为返回字符串的函数, $(this.$refs.pic).loadbackground('url', '50%', '../images/nopic.png')
$.fn.loadbackground = function(attribute, backgroundSize, errorpic) {
	if (typeof attribute === 'undefined') attribute = 'url'
	if (typeof backgroundSize === 'undefined') backgroundSize = '100%'
	if (typeof errorpic === 'undefined') errorpic = '../images/nopic.png'
	return this.each(function() {
		let item = this
		if (item.$el) item = item.$el
		if (item.getAttribute('loadbackground')) return true
		item.style.backgroundImage = ''
		let attr = attribute
		if (typeof attr === 'function') attr = attr(item)
		if (!item.getAttribute(attr)) return true
		attr = item.getAttribute(attr)
		if (!attr.length) return true
		$(item).loadpic(attr, errorpic, (item, pic, state) => {
			$(item).fadeIn()
			item.style.backgroundImage = `url(${pic})`
			if (!state) {
				let size = backgroundSize
				if (typeof size === 'function') size = size.call(item, state, item)
				item.style.backgroundSize = size
			} else {
				item.style.backgroundSize = ''
			}
			item.setAttribute('loadbackground', 'complete')
		})
	})
}

//分页滚动
$.fn.touchmove = function(options){
	options = $.extend({
		list: null, //滚动列表
		type: 0, //切换效果, 0:滚动切换, 1:渐显切换
		index: 0, //默认显示
		dir: 0, //拖拽(滚动)方向, 水平[0|x|left|right], 垂直[1|y|top|bottom]
		visible: 1, //显示个数, [visible<scroll ? scroll : visible]
		scroll: 1, //滚动个数
		mouseWheel: false, //使用鼠标滚轮
		autoWH: true, //自动设置宽高, 为了一页只显示一个列表元素
		autoW: true, //自动设置宽, autoWH为true时无效
		autoH: true, //自动设置高, autoWH为true时无效
		title: '', //显示list的title为标题的标题类名, 留空即不显示
		opacity: 0.7, //标题容器透明度
		titleAnimate: 'move', //标题容器显示动画, [move|opacity]
		hide: true, //标题是否隐藏(产生动画显示,否则一直显示)
		keydown: false, //箭头键控制滚动分页列表, [上:第一页|下:最后一页|左:上一页|右:下一页]
		prev: '', //滚动到上一个分页列表的expr按钮
		next: '', //滚动到下一个分页列表的expr按钮
		disprev: '', //已经没有上一分页即增加这个样式, unlimit:false 时有效
		disnext: '', //已经没有下一分页即增加这个样式, unlimit:false 时有效
		pager: '', //存放分页按钮的容器的expr, 留空即不显示
		curpager: 'this', //当前分页按钮类名
		pagerText: [], //按钮文字,为空即使用数字,若使用,元素数量必须与list数量相同
		autoPagerWH: true, //按钮容器自动宽高
		autoPager: true, //只有一个列表时自动隐藏按钮容器
		offset: '', //按钮容器位置, [left|center|right]
		offsetW: 10, //按钮容器距离左右边
		offsetH: 10, //按钮容器距离上下边
		section: true, //分页滚动
		act: 'click', //分页按钮的操作方式
		easing: 'easeout', //切换效果, 可使用 $.easing 扩展
		speed: 500, //切换速度
		auto: 0, //自动切换速度(0:不自动切换), auto==speed无限滚动
		autoWait: 0, //自动滚动前停留
		progress: '', //自动切换时在容器下面显示时间动画的样式, 为空即不显示
		progressPager: false, //自动切换时分页按钮显示时间动画
		unlimit: false, //无限滚动
		hoverStop: true, //自动切换时鼠标移到停止滚动
		drag: false, //可否拖拽
		bounces: true, //回弹效果
		pagerAction: null, //分页按钮操作时执行
		autoReload: true, //窗口改变大小自动重载
		beforeForLast: null, //滚动前执行, this:前一次的li对象
		before: null, //滚动前执行
		move: null, //滚动时执行
		afterLeft: null, //向左滚动后执行
		afterRight: null, //向右滚动后执行
		after: null, //滚动后执行
		complete: null //插件加载后执行
	}, $('body').data('touchmove.options'), options);
	if(options.autoReload)(function(ths){
		setTimeout(function(){$.resize(function(){ths.touchmove(options)})}, 1000);
	})(this);
	return this.each(function(){
		$(this).stopBounces(true).removePlug('touchmove');
		let _this = $(this), width = _this.width(), height = _this.height(), list = options.list?_this.find(options.list):_this.find('li'), count = list.length,
			wrapper, title, pager = [], index = -Math.abs(options.index), indexLast = null, hovering = false, moved = false,
			direction = 0, startD = 0, d, red, lw, lh, time = 0, touchpx, lastX = 0, lastY = 0,
			dirs = {'0':'x', 'left':'x', 'right':'x', '1':'y', 'top':'y', 'bottom':'y'}, dir = dirs[options.dir] ? dirs[options.dir] : options.dir, progress = null,
			scroll = options.scroll, visible = options.visible<scroll ? scroll : options.visible, draging = false, moving = false, auto = 0, autoHandle = null, autoWaitHandle = null;
		if(count<=0)return true;
		if(options.index<0)index = 0;
		if(Math.abs(options.index)>=count)index = -(count-1);
		_this.css({overflow:'hidden'}).data({width:width, height:height});
		if(_this.css('position') === 'static')_this.css({position:'relative'});
		if(!list.parent().is('ul'))list.wrapAll('<div></div>');
		wrapper = list.parent().css({position:'relative', overflow:'hidden'});
		let padding = list.padding(), margin = list.margin();
		if(options.type === 0){
			if(dir === 'x'){
				wrapper.css({left:(options.unlimit?index-scroll:index)*(width/visible), top:0, width:(width/visible)*count});
				lw = (width/visible) - padding.left - padding.right - margin.left - margin.right;
				lh = height - padding.top - padding.bottom;
			}else{
				wrapper.css({left:0, top:(options.unlimit?index-scroll:index)*(height/visible), height:(height/visible)*count});
				lw = width - padding.left - padding.right;
				lh = (height/visible) - padding.top - padding.bottom - margin.top - margin.bottom;
			}
			if(options.autoWH)list.css({width:lw, height:lh});
			else{
				if(options.autoW)list.width(lw);
				if(options.autoH)list.height(lh);
			}
			list.css({'float':'left'}).each(function(){
				if(!!$(this).attr('title')){
					$(this).data('title', $(this).attr('title'));
					$(this).removeAttr('title');
				}
			});
			if(options.unlimit){
				wrapper.prepend(list.last().clone()).append(list.eq(0).clone());
				if(dir === 'x'){
					wrapper.width(wrapper.width()+(width/visible)*2);
				}else{
					wrapper.height(wrapper.height()+(height/visible)*2);
				}
			}
			if(dir === 'x'){
				if(wrapper.width() === width)count = 1;
			}else{
				if(wrapper.height() === height)count = 1;
			}
		}else{
			wrapper.css({height:height});
			list.css({display:'none',position:'absolute',top:0,left:0}).eq(Math.abs(index)).css({display:'','z-index':505});
			list.each(function(){
				if(!!$(this).attr('title')){
					$(this).data('title', $(this).attr('title'));
					$(this).removeAttr('title');
				}
			});
		}
		if(options.title.length){
			_this.find('.'+options.title).remove();
			title = $('<div></div>').addClass(options.title);
			wrapper.after(title);
			let tpadding = title.padding();
			title.css({
				position: 'absolute',
				'z-index': 555,
				left: 0,
				top: height,
				width: width - tpadding.left - tpadding.right,
				opacity: options.opacity
			});
		}
		if(options.progress.length){
			progress = $('<div></div>').addClass(options.progress);
			wrapper.after(progress);
			progress.css({
				position: 'absolute',
				'z-index': 556,
				left: 0,
				width: 0,
				overflow: 'hidden'
			});
		}
		if(options.pager.length)pager = $(options.pager);
		if(pager.length){
			if(!pager.find('a').length){
				for(let i=0; i<count; i++){
					let text = options.pagerText.length ? options.pagerText[i] : i+1;
					if(i === Math.abs(index))pager.append('<a href="javascript:void(0)" class="'+options.curpager+'"><span></span><font>'+text+'</font></a>');
					else pager.append('<a href="javascript:void(0)"><span></span><font>'+text+'</font></a>');
				}
			}
			if(options.autoPagerWH){
				setTimeout(function(){
					let tm = 0, pagerW = 0, pagerH = 0;
					pager.find('a').each(function(){
						pagerW += $(this).outerWidth(true);
						tm = $(this).outerHeight(true);
						pagerH = tm>pagerH ? tm : pagerH;
					});
					//pager.css({width:pagerW, height:pagerH});
					if(options.offset.length){
						let offsetW = options.offsetW||0, offsetH = options.offsetH||0, uw = 0, uh = height - pagerH - offsetH;
						switch(options.offset){
							case 'left':uw = offsetW;break;
							case 'center':uw = Number((width-pagerW)/2);break;
							default:uw = width - pagerW - offsetW;
						}
						pager.css({position:'absolute', 'z-index':10, left:uw, top:uh});
					}
				}, 300);
			}
		}
		if(options.autoPager && count === 1)pager.hide();
		let tit = list.eq(Math.abs(index)).data('title');
		if(options.title.length && !!tit)title.html(tit).animate({top:height-title.outerHeight(false)}, 200);
		let clearEvent = function(){},
			startDrag = function(e){
				if(autoHandle){clearInterval(autoHandle);autoHandle = null}
				if((e.type === 'mousedown' && e.button !== 0) || (e.type === 'touchstart' && e.which !== 0))return false;
				beforeMove();
				lastX = 0;
				lastY = 0;
				moved = false;
				moving = false;
				time = new Date().getTime();
				if(dir === 'x'){
					startD = wrapper.position().left;
					d = $.touches(e).x;
					red = $.touches(e).y;
				}else{
					startD = wrapper.position().top;
					d = $.touches(e).y;
					red = $.touches(e).x;
					e.preventDefault();
				}
				wrapper.stop(true, false).on('mousemove', moveDrag).css('cursor', 'move');
				if(window.addEventListener)wrapper[0].addEventListener('touchmove', moveDrag, true);
				draging = true;
				return false;
			},
			moveDrag = function(e){
				e.preventDefault();
				if((e.touches && e.touches.length>1) || (e.scale && e.scale !== 1))return;
				if(!!_this.data('stopMove'))return false;
				let movepx, curpx, recurpx;
				moved = true;
				direction = 0;
				if(dir === 'x'){
					curpx = $.touches(e).x;
					if(!moving){
						recurpx = $.touches(e).y;
						if(recurpx>red+5 || recurpx<red-5)return false;
						else e.preventDefault();
					}
					touchpx = movepx = lastX = curpx - d;
					if(movepx>0 && movepx>=(width/5))direction = 1;
					else if(movepx<0 && Math.abs(movepx)>=(width/5))direction = -1;
				}else{
					curpx = $.touches(e).y;
					if(!moving){
						recurpx = $.touches(e).x;
						if(recurpx>red+5 || recurpx<red-5)return false;
					}
					touchpx = movepx = lastY = curpx - d;
					if(movepx>0 && movepx>=(height/5))direction = 1;
					else if(movepx<0 && Math.abs(movepx)>=(height/5))direction = -1;
				}
				moving = true;
				if( (index>=0 && movepx>0) || ((Math.abs(index)+visible)>=count && movepx<0) )movepx *= 0.2;
				if( !options.bounces && ((index>=0 && movepx>0) || ((Math.abs(index)+visible)>=count && movepx<0)) )movepx = 0;
				if(dir === 'x')wrapper.css('left', startD+movepx);
				else wrapper.css('top', startD+movepx);
				if($.isFunction(options.move))options.move.call(list.eq(Math.abs(index)), Math.abs(index));
				return false;
			},
			endDrag = function(e){
				if(moved)e.preventDefault();
				if((e.type === 'mousedown' && e.button !== 0) || (e.type === 'touchstart' && e.which !== 0))return false;
				if(draging){
					wrapper.off('mousemove', moveDrag).css('cursor', '');
					if(window.addEventListener)wrapper[0].removeEventListener('touchmove', moveDrag, true);
					if(!moved)return;
					if(direction === 1)index += scroll;
					else if(direction === -1)index -= scroll;
					if(index>=0 && (direction === 1))index = 0;
					if((Math.abs(index)+visible)>=count && (direction === -1))index = (-count + visible);
					draging = false;
					time = new Date().getTime() - time;
					moveWrapper();
				}
				return false;
			},
			beforeMove = function(){
				if($.isFunction(options.beforeForLast) && indexLast!=null)options.beforeForLast.call(list.eq(Math.abs(indexLast)), Math.abs(indexLast));
				if($.isFunction(options.before))options.before.call(list.eq(Math.abs(index)), Math.abs(index));
				if(options.title.length && options.hide){
					options.titleAnimate === 'opacity' ? title.stop(true, false).animate({opacity:0}, 200) : title.stop(true, false).animate({top:height}, 200);
				}
			},
			moveWrapper = function(){
				if(options.type === 0){
					let width = _this.data('width'), height = _this.data('height'), tit = list.eq(Math.abs(index)).data('title'), px, v = 1,
						prop = dir === 'x' ? {left:index*(width/visible)} : {top:index*(height/visible)};
					if(!options.section){
						if(time<300)v = 1.5;
						if(dir === 'x'){
							px = wrapper.position().left + touchpx * v;
							if(touchpx>0 && px>0)px = 0;
							else if(touchpx<0 && px<width-wrapper.width())px = width-wrapper.width();
							prop = {left:px};
						}else{
							px = wrapper.position().top + touchpx * v;
							if(touchpx>0 && px>0)px = 0;
							else if(touchpx<0 && px<height-wrapper.height())px = height-wrapper.height();
							prop = {top:px};
						}
					}
					touchpx = 0;
					wrapper.stop(true, false).animate(prop, options.speed, options.easing, function(){
						if(options.section){
							if(pager.length){
								let idx = (options.unlimit?index+1:index), curA = pager.find('a');
								if(options.unlimit && Math.abs(idx) === count)idx = 0;
								curA.removeClass(options.curpager);
								curA.eq(Math.abs(idx)).addClass(options.curpager);
								if(options.auto>0 && options.progressPager && !hovering)progressPager((Math.abs(idx)+1>=count?0:Math.abs(idx)+1));
							}
							if(options.title.length && !!tit){
								title.html(tit);
								if(options.hide){
									if(options.titleAnimate === 'opacity'){
										title.stop(true, false).animate({opacity:options.opacity}, 200);
									}else{
										title.stop(true, false).animate({top:height-title.outerHeight(false)}, 200);
									}
								}
							}
							if(!options.unlimit){
								if(options.prev.length && options.disprev.length){
									if(index >= 0)$(options.prev).addClass(options.disprev);
									else $(options.prev).removeClass(options.disprev);
								}
								if(options.next.length && options.disnext.length){
									if((Math.abs(index)+visible) >= count)$(options.next).addClass(options.disnext);
									else $(options.next).removeClass(options.disnext);
								}
							}
						}
						if(direction === -1){
							if($.isFunction(options.afterLeft))options.afterLeft.call(list.eq(Math.abs(index)), Math.abs(index));
						}else{
							if($.isFunction(options.afterRight))options.afterRight.call(list.eq(Math.abs(index)), Math.abs(index));
						}
						if($.isFunction(options.after))options.after.call(list.eq(Math.abs(index)), Math.abs(index));
						if(options.auto>0 && !hovering){
							wrapper.stop(true, false);
							clearInterval(autoHandle);autoHandle = null;
							autoHandle = setInterval(function(){autoMove()}, auto);
							if(options.progress.length)progressHandle();
						}
						_this.removeData('prev').removeData('next').removeData('mousewheel');
						indexLast = index;
						direction = 0;
					});
				}else{
					let height = _this.height(), tit = list.eq(Math.abs(index)).data('title'),
						curList = list.filter(':visible'), nextList = list.eq(Math.abs(index));
					nextList.css({display:'', 'z-index':504, opacity:1});
					curList.fadeOut(options.speed, function(){
						nextList.css({'z-index':505});
						if(pager.length){
							let idx = index, curA = pager.find('a');
							curA.removeClass(options.curpager);
							curA.eq(Math.abs(idx)).addClass(options.curpager);
							if(options.auto>0 && options.progressPager && !hovering)progressPager((Math.abs(idx)+1>=count?0:Math.abs(idx)+1));
						}
						if(options.title.length && !!tit){
							title.html(tit);
							if(options.hide){
								if(options.titleAnimate === 'opacity'){
									title.stop(true, false).animate({opacity:options.opacity}, 200);
								}else{
									title.stop(true, false).animate({top:height-title.outerHeight(false)}, 200);
								}
							}
						}
						if(direction === -1){
							if($.isFunction(options.afterLeft))options.afterLeft.call(list.eq(Math.abs(index)), Math.abs(index));
						}else{
							if($.isFunction(options.afterRight))options.afterRight.call(list.eq(Math.abs(index)), Math.abs(index));
						}
						if($.isFunction(options.after))options.after(Math.abs(index));
						if(options.auto>0 && !hovering){
							clearInterval(autoHandle);autoHandle = null;
							autoHandle = setInterval(function(){autoMove()}, auto);
							if(options.progress.length)progressHandle();
						}
						_this.removeData('prev').removeData('next').removeData('mousewheel');
						indexLast = index;
						direction = 0;
					});
				}
			};
		if($.isFunction(options.complete))options.complete.call(_this);
		if(options.keydown)$(document).keydown(function(e){
			e = e||window.event;
			let code = e.which||e.keyCode;
			if(code === 37 || code === 38){
				if(!!!_this.data('prev')){
					if(index>=0)return;
					else{
						cancelProgress();
						beforeMove();
						if(code === 37){
							if(index+scroll >= 0)index = 0;
							else index += scroll;
						}else index = 0;
					}
					_this.data('prev', true);
					direction = -1;
					moveWrapper();
					if(e.preventDefault)e.preventDefault();
					e.returnValue = false;
					return false;
				}
			}else if(code === 39 || code === 40){
				if(!!!_this.data('next')){
					if(Math.abs(index)+visible >= count)return;
					else{
						cancelProgress();
						beforeMove();
						if(code === 39){
							if(Math.abs(index)+visible+scroll >= count)index -= count+index-visible;
							else index -= scroll;
						}else index = -count+1;
					}
					_this.data('next', true);
					direction = 1;
					moveWrapper();
					if(e.preventDefault)e.preventDefault();
					e.returnValue = false;
					return false;
				}
			}
		});
		if(!options.unlimit){
			if(options.prev.length && options.disprev.length){
				if(index >= 0)$(options.prev).addClass(options.disprev);
			}
			if(options.next.length && options.disnext.length){
				if((Math.abs(index)+visible) >= count)$(options.next).addClass(options.disnext);
			}
		}
		if(options.prev.length)$(options.prev).on('click', function(){
			if(!!!_this.data('prev')){
				if(index>=0)return false;
				else{
					cancelProgress();
					beforeMove();
					if(index+scroll >= 0)index = 0;
					else index += scroll;
				}
				_this.data('prev', true);
				direction = -1;
				moveWrapper();
				//if(e.preventDefault)e.preventDefault();
				//e.returnValue = false;
				return false;
			}
		});
		if(options.next.length)$(options.next).on('click', function(){
			if(!!!_this.data('next')){
				if(Math.abs(index)+visible >= count)return false;
				else{
					cancelProgress();
					beforeMove();
					if(Math.abs(index)+visible+scroll >= count)index -= count+index-visible;
					else index -= scroll;
				}
				_this.data('next', true);
				direction = 1;
				moveWrapper();
				//if(e.preventDefault)e.preventDefault();
				//e.returnValue = false;
				return false;
			}
		});
		if(options.mouseWheel)_this.mousewheel(function(e, d){
			if(!!!_this.data('mousewheel')){
				if(d>0){
					if(index>=0)return;
					else{
						cancelProgress();
						beforeMove();
						if(index+scroll>=0)index += -index;
						else index += scroll;
					}
					direction = 1;
				}else{
					if(Math.abs(index)+visible>=count)return;
					else{
						cancelProgress();
						beforeMove();
						if(Math.abs(index)+visible+scroll>=count)index -= count+index-visible;
						else index -= scroll;
					}
					direction = -1;
				}
				_this.data('mousewheel', true);
				moveWrapper();
			}
		});
		if(mobileDevice())options.act = 'click';
		if(pager.length && options.act.length){
			pager.find('a').on(options.act, function(){
				let thisIndex = $(this).index();
				if(!!!_this.data('pager.a'+thisIndex)){
					_this.data('pager.a'+thisIndex, true);
					cancelProgress();
					beforeMove();
					index = -thisIndex;
					if(visible>1 && index<=-(count-visible))index = -(count-visible);
					if(options.unlimit){
						if(index === 0)index = -1;
						else if(index === -count)index = -count-1;
						else index--;
					}
					if(indexLast !== null){
						if(indexLast<index)direction = -1;
						else direction = 1;
					}
					if($.isFunction(options.pagerAction))options.pagerAction.call($(this), Math.abs(index));
					moveWrapper();
					//if(e.preventDefault)e.preventDefault();
					//e.returnValue = false;
					return false;
				}
			});
		}
		if(options.auto>0){
			auto = options.auto;
			if(options.dir === 'right' || options.dir === 'bottom')direction = 1;
			else direction = -1;
			if(auto<options.speed)auto = options.speed;
			if(options.progress.length && options.autoWait>0)progressHandle();
			if(options.progressPager && pager.length){
				pager.find('a').css('position', 'relative');
				progressPager((Math.abs(index)+1>=count?0:Math.abs(index)+1));
			}
			autoWaitHandle = setTimeout(function(){
				autoMove();
				autoHandle = setInterval(function(){autoMove()}, auto);
			}, options.autoWait);
		}
		if(options.hoverStop){
			_this.hover(function(){hoverHandle(true)},function(){hoverHandle(false)});
			if(options.title.length)title.hover(function(){hoverHandle(true)},function(){hoverHandle(false)});
		}
		if(options.drag && options.type === 0){
			_this.unselect().on('mouseup mouseleave', endDrag);
			wrapper.on('mousedown', startDrag).on('mouseup mouseleave', endDrag).on('click', clearEvent);
			wrapper.on('dragstart', 'img, a', function(e){e.preventDefault()});
			if(window.addEventListener){
				_this[0].addEventListener('touchend', endDrag, true);
				wrapper[0].addEventListener('touchstart', startDrag, true);
				wrapper[0].addEventListener('touchend', endDrag, true);
				wrapper[0].addEventListener('touchcancel', endDrag, true);
			}
		}
		function hoverHandle(bool){
			if(options.auto>0){
				hovering = bool;
				if(bool){
					clearInterval(autoWaitHandle);autoWaitHandle = null;
					clearInterval(autoHandle);autoHandle = null;
					if(pager.length)pager.find('a span').stop(true, false).fadeOut(200);
					if(options.progress.length)progress.stop(true, false).fadeOut(200);
				}else{
					autoHandle = setInterval(function(){autoMove()}, auto);
					if(options.progressPager && pager.length)progressPager((Math.abs(index)+1>=count?0:Math.abs(index)+1));
					if(options.progress.length)progressHandle();
				}
			}
		}
		function autoMove(){
			beforeMove();
			if(Math.abs(index)+visible>=count+(options.unlimit?3:0)){
				index = 0;
			}else{
				if(Math.abs(index)+visible+scroll>=count+(options.unlimit?3:0)){
					if(options.unlimit){
						index = count+index-visible;
						wrapper.stop(true, false);
						if(dir === 'x'){
							wrapper.css('left', -(width/visible));
						}else{
							wrapper.css('top', -(height/visible));
						}
					}else index -= count+index-visible;
				}else if(options.unlimit && index === 0){
					index = -(count-1);
					wrapper.stop(true, false);
					if(dir === 'x'){
						wrapper.css('left', -(wrapper.width()-(width/visible)*2));
					}else{
						wrapper.css('top', -(wrapper.height()-(height/visible)*2));
					}
				}else{
					if(options.unlimit){
						index = (direction === -1 ? index-scroll : index+scroll);
					}else index -= scroll;
				}
			}
			moveWrapper();
		}
		function cancelProgress(){
			clearInterval(autoWaitHandle);
			if(options.auto>0 && options.progress.length)progress.stop(true, false).hide();
		}
		function progressHandle(){
			progress.width(0).show().animate({width:width}, options.auto, 'linear');
		}
		function progressPager(idx){
			pager.find('a span').hide();
			let curA = pager.find('a').eq(idx), span = curA.find('span').width(0).show();
			span.animate({width:curA.width()+1}, options.auto, 'linear');
		}
		function mobileDevice(mark){
			let na = navigator.userAgent.toLowerCase();
			if(typeof mark !== 'undefined'){
				return na.match(new RegExp(mark,'i')) === mark;
			}else{
				return $.browser.mobile;
			}
		}
	});
};

//简化版遮罩层与展示层, this.$.presentView.call(this.$refs.div, options)
$.fn.presentView = function(options) {
	if (typeof options === 'undefined') options = 0
	if (typeof options === 'boolean' && !options) {
		options = this.data('presentView-options')
		switch (options.type) {
			case 0:
				this.css({'-webkit-transform':'translate(0,100%)', transform:'translate(0,100%)'})
				break
			case 1:
				this.css({'-webkit-transform':'translate(100%,0)', transform:'translate(100%,0)'})
				break
			case 2:
				this.css({'-webkit-transform':'translate(0,-100%)', transform:'translate(0,-100%)'})
				break
			case 3:
				this.css({'-webkit-transform':'translate(-100%,0)', transform:'translate(-100%,0)'})
				break
		}
		this.parent().css({'background-color':'rgba(0,0,0,0)'})
		setTimeout(() => {
			this.css({display:'none', '-webkit-transition-duration':'0s', 'transition-duration':'0s'})
			setTimeout(() => this.parent().hide(), 100)
			if ($.isFunction(options.closeCallback)) options.closeCallback.call(this)
		}, 300);
		return this;
	}
	if (typeof options === 'function') options = { callback: options }
	if (typeof options === 'number') options = { type: options }
	options = $.extend({
		type: 0, //0下往上, 1右往左, 2上往下, 3左往右
		callback: null,
		closeCallback: null
	}, options)
	return this.each(function() {
		let _this = $(this).addClass('load-presentView')
		_this.data('presentView-options', options)
		_this.css({display:'none', position:'fixed', 'z-index':9999, '-webkit-transition':'transform 0s ease-out', transition:'transform 0s ease-out'}).removeClass('hidden')
		let overlay = _this.parent()
		if (!overlay.hasClass('load-overlay')) {
			_this.wrap('<div class="load-overlay" style="position:fixed;left:0;top:0;z-index:998;opacity:1;background-color:rgba(0,0,0,0);-webkit-transition:background-color 0.3s ease-out;transition:background-color 0.3s ease-out;"></div>')
			overlay = _this.parent()
			overlay.on('click', e => {
				let o = e.target || e.srcElement
				do {
					if ($(o).hasClass('load-presentView')) return
					if ($(o).hasClass('load-overlay')) break
					o = o.parentNode
				} while(o.parentNode)
				_this.presentView(false)
			})
		}
		overlay.css('display', 'block')
		setTimeout(() => overlay.css({'background-color':'rgba(0,0,0,0.6)'}), 0)
		switch (options.type) {
			case 0:
				_this.css({bottom:0, '-webkit-transform':'translate(0,100%)', transform:'translate(0,100%)'})
				break
			case 1:
				_this.css({right:0, '-webkit-transform':'translate(100%,0)', transform:'translate(100%,0)'})
				break
			case 2:
				_this.css({top:0, '-webkit-transform':'translate(0,-100%)', transform:'translate(0,-100%)'})
				break
			case 3:
				_this.css({left:0, '-webkit-transform':'translate(-100%,0)', transform:'translate(-100%,0)'})
				break
		}
		setTimeout(() => {
			_this.css({display:'block', '-webkit-transition-duration':'0.3s', 'transition-duration':'0.3s'})
			setTimeout(() => _this.css({'-webkit-transform':'translate(0,0)', transform:'translate(0,0)'}), 50)
			if ($.isFunction(options.callback)) options.callback.call(_this)
		}, 0)
	})
}

//拖拽显示
$.fn.dragshow = function(options){
	options = $.extend({
		list: 'li', //拖动列表
		title: '', //显示按钮内的内容(支持html代码)(支持函数,接受一个参数:当前行)
		cls: '.title', //显示按钮的类名称
		useTransform: true, //使用CSS3特性来移动, list的css样式需要增加transform:translate3d(0,0,0);transition-duration:200ms;
		click: null, //点击显示按钮时执行, this:显示按钮, 接受两个参数:当前行, 显示按钮点击的element(为区别显示区域内有多个html标签)
		before: null //拖动前执行
	}, options);
	return this.each(function(){
		$(this).stopBounces(true);
		let _this = $(this), width = _this.width(), originalX, dx, dy, moved = false, originBtnWidth = 0, btnWidth = 0,
			editingFix = 0, curX = 0, list = _this.find(options.list), btn = _this.children(options.cls), useTransform = options.useTransform;
		//if(list.css('transform')=='none')useTransform = false;
		if(!useTransform)_this.css('position', 'relative');
		if(!btn.length){
			btn = $('<div class="'+options.cls.replace('.','').replace('#','')+'"></div>');
			_this.append(btn);
			if($.isFunction(options.click)){
				btn.tapper(function(e){
					let _b = $(this), o = $.etarget(e);
					if(btn.children().length){
						do{
							if($(o).is(_b.children())){
								options.click.call(_b, _b.data('curRow'), $(o));
								setTimeout(function(){_b.removeData('curRow');_this.removeData('lastEdit')}, 100);
								return;
							}
							o = o.parentNode;
						}while(o.parentNode);
					}else{
						options.click.call(_b, _b.data('curRow'), _b);
						setTimeout(function(){_b.removeData('curRow');_this.removeData('lastEdit')}, 100);
					}
				});
			}
		}
		originBtnWidth = btnWidth = btn.width();
		btn.hide();
		list.css({position:'relative', 'z-index':2}).each(function(){
			let _l = $(this);
			if(!!_l.data('dragshow'))return true;
			_l.data('dragshow', true);
			let startDrag = function(e){
					let o = e.target || e.srcElement;
					if($(o).is('input'))$(o).focus();
					if(!!_this.data('lastEdit') && _this.data('lastEdit')[0]!==_l[0]){cancelLast(e);return false}
					if($.isFunction(options.before)){
						let before = options.before.call(_l);
						if(typeof before === 'boolean' && !before)return;
					}
					moved = false;
					let event = 'click dragstart';
					if($.browser.mobile)event = 'touchend dragstart';
					_this.find('a').on(event, stop);
					let title = options.title;
					if($.isFunction(title))title = title.call(btn, _l);
					btn.width(originBtnWidth).html(title).data('curRow', _l)
					.css({top:_l.position().top, height:_l.outerHeight(false), 'line-height':_l.outerHeight(false)+'px'});
					if(btn.children().length){
						btnWidth = 0;
						btn.children().each(function(){btnWidth+=$(this).outerWidth(false)});
						let minWidth = $.unit(btn.css('min-width'));
						if(btnWidth<minWidth)btnWidth = minWidth;
						btn.width(btnWidth);
					}
					btnWidth = btn.outerWidth(false);
					originalX = useTransform ? _l.transform().translate.x : _l.position().left;
					dx = $.touches(e).x;
					dy = $.touches(e).y;
					editingFix = !!_this.data('lastEdit') ? btnWidth : 0;
					_l.stop(true, false);
					_l.on('mousemove', moveDrag).css('cursor', 'move');
					if(window.addEventListener)_l[0].addEventListener('touchmove', moveDrag, true);
					return false;
				},
				moveDrag = function(e){
					if(btn.children().length){
						btnWidth = 0;
						btn.children().each(function(){btnWidth+=$(this).outerWidth(false)});
						let minWidth = $.unit(btn.css('min-width'));
						if(btnWidth<minWidth)btnWidth = minWidth;
						btn.width(btnWidth);
					}
					let newPosition = 0, moveX = $.touches(e).x, moveY = $.touches(e).y;
					if(moveY-dy>10 || moveY-dy<-10)return false;
					if(moveX-dx>10 || moveX-dx<-10)e.preventDefault(); //终止屏幕拖动, 使用后页面不能上下拖动动
					btn.css({display:''});
					moveX -= dx;
					moved = true;
					if(!_l.find('.overlay-div').length){
						list.each(function(){
							$(this).append('<div class="overlay-div" style="position:absolute;z-index:999;top:0;left:0;width:100%;height:100%;"></div>');
						});
					}
					if(moveX < 0){
						if(moveX <= -(width-btnWidth)/3-btnWidth+editingFix){
							newPosition = curX + (moveX+(width-btnWidth)/3+btnWidth-editingFix) * 0.2;
						}else{
							newPosition = curX = originalX + moveX;
						}
					}else{
						if(moveX <= Number(editingFix)){
							newPosition = originalX + moveX;
						}else{
						
						}
					}
					if(useTransform){
						_l.css({transform:'translate3d('+newPosition+'px,0,0)', '-webkit-transform':'translate3d('+newPosition+'px,0,0)', 'transition-duration':'0s', '-webkit-transition-duration':'0s'});
					}else{
						_l.css('left', newPosition);
					}
					return false;
				},
				endDrag = function(e){
					if(moved)e.preventDefault();
					_l.off('mousemove', moveDrag).css('cursor', '');
					if(window.removeEventListener)_l[0].removeEventListener('touchmove', moveDrag, true);
					if(!!_this.data('lastEdit')){
						if(!moved){cancelLast(e);return false}
					}
					if(!moved){
						let event = 'click dragstart';
						if($.browser.mobile)event = 'touchend dragstart';
						_this.find('a').off(event, stop);
						return;
					}
					let left = useTransform ? _l.transform().translate.x : _l.position().left;
					if(left<=-btnWidth/2){
						if(useTransform){
							_l.css({transform:'translate3d('+(-btnWidth)+'px,0,0)', '-webkit-transform':'translate3d('+(-btnWidth)+'px,0,0)',
								'transition-duration':'200ms', '-webkit-transition-duration':'200ms'});
						}else{
							_l.animate({left:-btnWidth}, 200, 'easeout');
						}
						_this.data('lastEdit', _l);
					}else{
						if(useTransform){
							_l.css({transform:'', '-webkit-transform':'', 'transition-duration':'200ms', '-webkit-transition-duration':'200ms'});
							setTimeout(function(){btn.hide().removeData('curRow')}, 200);
						}else{
							_l.animate({left:0}, 200, 'easeout', function(){btn.hide().removeData('curRow')});
						}
						_this.removeData('lastEdit');
						list.find('.overlay-div').remove();
					}
					return false;
				};
			_l.unselect().on('mousedown', startDrag).on('mouseup', endDrag)
			.on('dragstart', 'img, a', function(e){e.preventDefault()});
			if(window.addEventListener){
				this.addEventListener('touchstart', startDrag, true);
				this.addEventListener('touchend', endDrag, true);
				this.addEventListener('touchcancel', endDrag, true);
			}
		});
		_this.data('reset', cancelLast);
		function stop(e){
			if(e.preventDefault)e.preventDefault();
			e.returnValue = false;
			return false;
		}
		function cancelLast(e){
			if(e)e.preventDefault();
			let lastEdit = _this.data('lastEdit');
			_this.removeData('lastEdit');
			if(useTransform){
				lastEdit.css({transform:'', '-webkit-transform':'', 'transition-duration':'200ms', '-webkit-transition-duration':'200ms'});
				setTimeout(function(){btn.hide().removeData('curRow')}, 200);
			}else{
				lastEdit.animate({left:0}, 200, 'easeout', function(){btn.hide().removeData('curRow')});
			}
			list.find('.overlay-div').remove();
			let event = 'click dragstart';
			if($.browser.mobile)event = 'touchend dragstart';
			_this.find('a').off(event, stop);
		}
	});
};

//遮罩层与展示层, target:expr|对象|html代码(内容)|空字符串只显示背景遮罩层|false(删除),
//type:浮动控件位置类型(0:居中|1:底部|2:全屏居中(不随滚动)|3:居中(不自动opacity)|funciton:自定义)
//调用前 caller.data('overlay-no', true) 或  caller.data('overlay-no-overlay', true) 可不加遮罩层
//target可增加以下自定义属性, target-width:指定宽度, target-height:指定高度, overlay-opacity:遮罩层背景色透明度, no-close:点击遮罩层不关闭, show-close:显示右上角关闭按钮, css:增加样式到face(字面量格式), add-class:增加样式名到face, delay-class:延迟增加样式名到face, delay-close:指定关闭时长(默认300), close-class:关闭前增加样式到face, no-animate:不使用动画
$.fn.overlay = function(target, type, callback, closeCallback){
	let _this = this;
	if(typeof target === 'boolean' && !target){
		let overlay = $('.load-overlay', _this), face = $('.load-face', _this), target = face.data('overlay.target'),
			bgDelay = (target && !!!target.attr('no-animate')) ? 400 : 0;
		if(!target)return;
		setTimeout(function(){
			if($('.load-face, .load-view, .load-presentView, .dialog-action, .dialog-alert, .dialog-popover', _this).length)return;
			overlay.removeClass('load-overlay-in');
			setTimeout(function(){overlay.remove()}, bgDelay);
		}, bgDelay);
		if(face.length){
			let closeCallback = face.data('overlay.callback'),
				origin = target.removeData('overlay.overlay').data('overlay.origin'),
				cls = face.data('overlay.closeClass'), delay = face.data('overlay.delayClose')||(!!!target.attr('no-animate')?300:0);
			if(typeof type === 'undefined' || (!$.isNumeric(type) && !$.isFunction(type)))type = face.data('overlay.type');
			if(!!cls)face.addClass(cls);
			if($.isFunction(type)){
				type.call(face);
			}else{
				if(!!!type || type !== 1){
					if(type !== 3 && !!!target.attr('no-animate'))face.animate({opacity:0}, delay);
					setTimeout(function(){
						if(!!origin){origin.after(target.css('display', target.data('overlay.display')));origin.remove()}
						if($.isFunction(closeCallback))closeCallback.call(target);
						face.remove();
					}, delay);
				}else{
					if(!!!target.attr('no-animate')){
						face.animate({bottom:-face.height()}, delay, function(){
							if(!!origin){origin.after(target.css('display', target.data('overlay.display')));origin.remove()}
							if($.isFunction(closeCallback))closeCallback.call(target);
							face.remove();
						});
					}else{
						if(!!origin){origin.after(target.css('display', target.data('overlay.display')));origin.remove()}
						if($.isFunction(closeCallback))closeCallback.call(target);
						face.remove();
					}
				}
			}
		}
		return;
	}
	let t = $([]), isUrl = false;
	if(typeof target !== 'undefined' && typeof target !== 'boolean' && target.length){
		if(/^https?:\/\//.test(target)){
			isUrl = true;
			let s = target.split('||'), iframeWidth = 800, iframeHeight = 500, scrolling = '';
			for(let i=1; i<s.length; i++){
				if(s[i].indexOf('scrolling')>-1){
					let o = s[i].split('=');
					scrolling = 'scrolling="'+o[1]+'"';
				}else if(/\d+%?(\*\d+%?)?/.test(s[i])){
					let o = s[i].split('*');
					iframeWidth = o[0];
					if(o.length>1)iframeHeight = o[1];
				}
			}
			if((iframeWidth+'').indexOf('%')>-1)iframeWidth = $.window().width * (iframeWidth.replace(/%/g, '') / 100);
			if((iframeHeight+'').indexOf('%')>-1)iframeHeight = $.window().height * (iframeHeight.replace(/%/g, '') / 100);
			if(!isNaN(iframeWidth))iframeWidth += 'px';
			if(!isNaN(iframeHeight))iframeHeight += 'px';
			target = '<iframe src="'+s[0]+'" frameborder="0" style="width:'+iframeWidth+';height:'+iframeHeight+';" '+scrolling+'></iframe>\
				<div class="circle-db-ico" style="position:absolute;left:0;top:0;right:0;bottom:0;background:#fff no-repeat center center;background-size:64px 64px;"></div>';
		}
		t = $(target);
		if(t.parent().length){
			let display = t.css('display');
			let origin = $('<div style="display:'+display+';opacity:0;width:'+t.outerHeight(false)+'px;height:'+t.outerHeight(false)+'px;"></div>');
			t.after(origin);
			t.data({'overlay.origin':origin, 'overlay.display':display});
			t.removeClass('hidden');
		}
	}
	let win = $.window(), winHeight = win.height, overlay = $('.load-overlay', _this), face = $('.load-face', _this);
	if(!overlay.length && !!!_this.data('overlay-no') && !!!_this.data('overlay-no-overlay'))overlay = $('<div class="load-overlay"></div>');
	if(!face.length)face = $('<div class="load-face"></div>');
	else{
		face.removeClass(face.data('overlay.addClass')).removeClass(face.data('overlay.delayClass'));
		let tar = face.data('overlay.target'), origin = tar.data('overlay.origin');
		if(!!origin){origin.after(tar.css('display', tar.data('overlay.display')));origin.remove()}
		face.html('');
	}
	if(!!!_this.data('overlay-no') && !!!_this.data('overlay-no-overlay'))_this.append(overlay.css({background:'rgba(0,0,0,'+(t.attr('overlay-opacity')||0.6)+')'}));
	if(overlay.height() === 0)overlay.css({position:'fixed', top:0, left:0, 'z-index':998, width:'100%', height:win.height, overflow:'hidden'});
	if(!!!t.attr('no-animate'))setTimeout(function(){overlay.addClass('load-overlay-in')}, 0);
	else overlay.css({opacity:1, '-webkit-transition-duration':'0s', 'transition-duration':'0s'});
	if(!!!t.attr('no-close'))overlay.on(window.eventType, function(){_this.overlay(false)});
	if(!t.length)return;
	_this.append(face);
	if(typeof type === 'undefined' || (!$.isNumeric(type) && !$.isFunction(type)))type = 0;
	face.data({'overlay.target':t.data('overlay.overlay', true), 'overlay.type':type, 'overlay.callback':closeCallback}).append(t);
	t.eq(0).css('display', 'block');
	face.css({position:'fixed', 'z-index':999, '-webkit-transform':'translateY(-9999px)', transform:'translateY(-9999px)'});
	if(!!t.attr('css'))face.data({'overlay.css':t.attr('css')}).css($.json(t.attr('css')));
	if(!!t.attr('add-class'))face.data({'overlay.addClass':t.attr('add-class')}).addClass(t.attr('add-class'));
	if(!!t.attr('delay-class'))setTimeout(function(){face.data({'overlay.delayClass':t.attr('delay-class')}).addClass(t.attr('delay-class'))}, 100);
	if(!!t.attr('delay-close'))face.data({'overlay.delayClose':Number(t.attr('delay-close'))});
	if(!!t.attr('close-class'))face.data({'overlay.closeClass':t.attr('close-class')});
	if(!!t.attr('show-close')){
		let close = $('<a href="javascript:void(0)">×</a>').css({position:'absolute', right:0, top:0, 'z-index':999, width:30, height:30, 'line-height':'24px', overflow:'hidden', background:'rgba(0,0,0,0.6)', color:'#fff', 'font-size':'22px', 'text-align':'center', 'padding-left':'5px', 'box-sizing':'border-box', 'font-family':'arial', 'text-decoration':'none', '-moz-border-radius-bottomleft':'30px', '-webkit-border-bottom-left-radius':'30px', 'border-bottom-left-radius':'30px'}).click(function(){_this.overlay(false)});
		face.append(close);
	}
	if(isUrl){
		let iframe = face.find('iframe');
		iframe.on('load', function(){
			iframe.next().remove();
		});
	}
	setTimeout(function(){
		if(!!t.attr('target-width'))t.width(t.attr('target-width'));
		if(!!t.attr('target-height'))t.height(t.attr('target-height'));
		face.css({width:t.outerWidth(true)});
		if($.isFunction(type)){
			type.call(face);
			if($.isFunction(callback))setTimeout(function(){callback.call(t)}, 100);
		}else{
			if(type !== 1){
				if(type === 2){
					winHeight = win.maxHeight;
					face.css({position:'absolute'});
				}
				face.css({left:'50%', top:'50%', '-webkit-transform':'translate(-50%, -50%)', transform:'translate(-50%, -50%)'});
				if(type === 0 && !!!t.attr('no-animate')){
					face.css({opacity:0});
					setTimeout(function(){
						face.animate({opacity:1}, 300, 'easeout', function(){
							if($.isFunction(callback))callback.call(t);
						});
					}, 300);
				}else{
					if($.isFunction(callback))callback.call(t);
				}
			}else{
				face.css({'-webkit-transform':'translateY(0%)', transform:'translateY(0%)'});
				face.css({left:0, top:'', bottom:-face.height(), width:win.width});
				if(!!!t.attr('no-animate')){
					face.animate({bottom:0}, 300, function(){
						if($.isFunction(callback))callback.call(t);
					});
				}else{
					face.css({bottom:0});
					if($.isFunction(callback))callback.call(t);
				}
			}
		}
	}, 100);
	return face;
};

//相册(仿APP,支持移动端), options为true即重新构建
/*需要引入样式
import '../../../js/photoswipe/photoswipe.css'
import '../../../js/photoswipe/default-skin/default-skin.css'
*/
$.fn.photoBrowser = function(options){
	if(typeof options === 'undefined'){
		this.each(function(){
			let _this = $(this), swiper = _this.data('photoBrowser-swiper');
			if(typeof(swiper) !== 'undefined' && swiper !== null && typeof(swiper.destroy) === 'function')swiper.destroy();
		});
		if(!!this.data('tapper'))this.tapper(false);
		if(!!this.data('photoBrowser-options'))options = this.data('photoBrowser-options');
	}
	options = $.extend({
		minimal: true, //精简版相册
		spacing: 0, //图片之间的距离(百分比,0~1)
		loop: false //循环拖曳滚动
	}, $('body').data('photoBrowser.options'), options);
	let id = this.data('pswp-id');
	if(!!!id || !$('.'+id).length){
		id = 'pswp_' + parseInt(String(Math.random() * 1000));
		let html = $('<div class="pswp '+id+'" tabindex="-1" role="dialog" aria-hidden="true">\
			<div class="pswp__bg"></div>\
			<div class="pswp__scroll-wrap">\
				<div class="pswp__container"><div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div>\
				<div class="pswp__ui pswp__ui--hidden">\
					<div class="pswp__top-bar">\
						<div class="pswp__counter"></div>\
						<button class="pswp__button pswp__button--close" title="Close (Esc)"></button>\
						<button class="pswp__button pswp__button--share" title="Share"></button>\
						<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>\
						<button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>\
						<div class="pswp__preloader">\
							<div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div>\
						</div>\
					</div>\
					<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"><div class="pswp__share-tooltip"></div></div>\
					<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>\
					<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>\
					<div class="pswp__caption"><div class="pswp__caption__center"></div></div>\
				</div>\
			</div>\
		</div>');
		$(document.body).append(html);
	}
	let ths = this.data('pswp-id', id), pswpElement = $('.'+id)[0], items = [];
	this.each(function(i){
		let _this = $(this), url = _this.attr('url')||_this.attr('href')||_this.attr('src'), title = _this.attr('alt')||_this.attr('title')||'';
		items.push({
			src: url,
			title: title,
			el: this
		});
		let image = new Image();
		if($.browser.ie8){
			image.onload = function(){
				items[i].w = image.width;
				items[i].h = image.height;
			};
			image.src = url;
		}else{
			image.src = url;
			$(image).on('load', function(){
				items[i].w = image.width;
				items[i].h = image.height;
			});
		}
	});
	//http://photoswipe.com/documentation/options.html
	let opt = {
		index: 0,
		bgOpacity: 0.9,
		history: false,
		pinchToClose: false,
		closeOnScroll: false,
		closeOnVerticalDrag: false,
		getThumbBoundsFn: function(index){
			let thumb = items[index].el, pageYScroll = window.pageYOffset||document.documentElement.scrollTop, rect = thumb.getBoundingClientRect();
			return {x:rect.left, y:rect.top+pageYScroll, w:rect.width};
		},
		spacing: options.spacing,
		loop: options.loop
	};
	if(options.minimal){
		//opt.mainClass = 'pswp--minimal--dark';
		opt.barsSize = {top:0, bottom:0};
		opt.captionEl = true;
		opt.fullscreenEl = false;
		opt.shareEl = false;
		opt.tapToClose = true;
		opt.tapToToggleControls = false;
	}
	return this.data('photoBrowser-options', options).click(function(){
		opt.index = ths.index(this);
		let swiper = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, opt);
		$(this).data('photoBrowser-swiper', swiper);
		swiper.init();
		return false;
	});
};

//移动端样式密码框, 调用者必须为input:text
$.fn.passwordView = function(options){
	let opt = {
		cls: 'ring', //附加样式
		placeholder: '●', //占位符,为空即显示字符串
		length: 6, //位数
		empty: null, //值为空时执行
		input: null, //值不为空且未输入所有位数时执行
		callback: null //输入所有位数后执行,返回false清空值
	}, _ths = this;
	if($.isFunction(options)){
		opt.callback = options;
		options = $.extend({}, $('body').data('passwordView.options'), opt);
	}else{
		options = $.extend(opt, $('body').data('passwordView.options'), options);
	}
	setTimeout(function(){_ths.select().focus()}, 10);
	return this.each(function(){
		if(!!$(this).data('passwordView'))return true;
		let length = Number(options.length),
			_this = $(this).attr('maxlength', length).addClass('inp').removeClass('hidden').css('display', 'block').data('passwordView', true);
		_this.wrap('<div class="passwordView"><div class="'+options.cls+'"></div></div>');
		let view = _this.parent(), w = 100/length, html = '<ul>';
		for(let i=0; i<length; i++)html += '<li style="width:'+w+'%;padding-top:'+w+'%;"><span><input type="text" /></span></li>';
		html += '</ul><font></font>';
		view.append(html);
		let font = view.find('font'), placeholders = view.find('ul input');
		_this.on('input propertychange', function(){
			fillPlaceholder();
			if(_this.val().trim().length && _this.val().trim().length<length && $.isFunction(options.input))options.input.call(_this);
			if(_this.val().trim().length === length && $.isFunction(options.callback)){
				let ret = options.callback.call(_this, placeholders, font);
				if(typeof ret === 'boolean' && !ret){
					_this.val('')
					placeholders.removeClass('this').val('')
					font.show().css('left', placeholders.eq(0).position().left+placeholders.eq(0).width()/2);
					if($.isFunction(options.empty))options.empty.call(_this);
				}
			}
		});
		fillPlaceholder();
		function fillPlaceholder(){
			let length = _this.val().trim().length, li = placeholders.eq(length).parent().parent();
			if(!length && $.isFunction(options.empty))options.empty.call(_this);
			for(let i=0; i<length; i++)placeholders.eq(i).addClass('this').val(options.placeholder.length?options.placeholder:_this.val().substr(i, 1));
			placeholders.each(function(index){if(index>=length)$(this).removeClass('this').val('')});
			if(li.length)font.show().css('left', li.position().left+li.width()/2);
			else font.hide();
		}
	});
};

//仿iOS的UIActionSheet
$.fn.actionView = function(title, btns, e){
	let _this = this, tablet = $.window().width>=1024, overlay = $('.load-overlay', _this), dialog = $('.dialog-action', _this), group;
	if(typeof title === 'boolean'){
		let height = dialog.height();
		dialog.removeClass('dialog-action-x');
		if(!dialog.hasClass('dialog-action-popover'))dialog.css({transform:'translate3d(0,'+height+'px,0)', '-webkit-transform':'translate3d(0,'+height+'px,0)'});
		setTimeout(function(){dialog.remove()}, 400);
		setTimeout(function(){
			if($('.load-face, .load-view, .load-presentView, .dialog-action, .dialog-alert, .dialog-popover', _this).length)return;
			overlay.removeClass('load-overlay-in');
			setTimeout(function(){overlay.remove()}, 400);
		}, 400);
		return;
	}
	if(!$.isArray(btns) || !btns.length)return dialog;
	if(!overlay.length && !!!_this.data('overlay-no') && !!!_this.data('overlay-no-actionView')){
		overlay = $('<div class="load-overlay"></div>');
		_this.append(overlay.css({background:'rgba(0,0,0,0.6)'}));
		if(tablet && e){
			overlay.on(window.eventType, function(){_this.popoverView(false)});
		}else{
			overlay.on(window.eventType, function(){_this.actionView(false)});
		}
	}
	setTimeout(function(){overlay.addClass('load-overlay-in')}, 0);
	dialog = $('<div class="dialog-action"></div>').css('z-index', 999);
	group = $('<div class="dialog-action-group"><div class="dialog-action-box"></div></div>');
	dialog.append(group);
	let inner = group.find('.dialog-action-box').stopBounces();
	if(title.length)inner.append('<div class="dialog-action-label">'+title+'</div>');
	for(let i=0; i<btns.length; i++){
		let text = btns[i].text||'btn'+(i+1), btn = $('<a href="javascript:void(0)" class="dialog-action-button">'+text+'</a>');
		inner.append(btn);
		if($.isFunction(btns[i].click)){
			btn.data('click', btns[i].click);
			(function(j){
				btn.click(function(){
					$(this).data('click').call(dialog, j);
					_this.actionView(false);
				});
			})(i);
		}else{
			if(tablet && e){
				btn.click(function(){_this.popoverView(false)});
			}else{
				btn.click(function(){_this.actionView(false)});
			}
		}
	}
	group = $('<div class="dialog-action-group"><div class="dialog-action-box"><a href="javascript:void(0)" class="dialog-action-button dialog-action-bold">取消</a></div></div>');
	dialog.append(group);
	if(tablet && e){
		group.find('a').click(function(){_this.popoverView(false)});
		_this.popoverView(e, dialog.addClass('dialog-action-popover'));
	}else{
		group.find('a').click(function(){_this.actionView(false)});
		_this.append(dialog);
		let height = dialog.height();
		dialog.css({transform:'translate3d(0,'+height+'px,0)', '-webkit-transform':'translate3d(0,'+height+'px,0)', 'transition-duration':'0s', '-webkit-transition-duration':'0s'});
	}
	setTimeout(function(){
		dialog.css({transform:'', '-webkit-transform':'', 'transition-duration':'', '-webkit-transition-duration':''}).addClass('dialog-action-x');
	}, 10);
	return dialog;
};

//popoverView
$.fn.popoverView = function(e, target){
	let _this = $(this), win = $.window(), overlay = $('.load-overlay', _this), dialog = $('.dialog-popover', _this);
	if(!target){
		dialog.removeClass('dialog-popover-x');
		setTimeout(function(){
			let child = dialog.find('.dialog-popover-box > *:eq(0)');
			if(!!child.data('parent'))child.data('parent').append(child);
			if(!!child.data('originnext'))child.data('originnext').before(child);
			dialog.remove();
		}, 400);
		setTimeout(function(){
			if($('.load-face, .load-view, .load-presentView, .dialog-action, .dialog-alert, .dialog-popover', _this).length)return;
			overlay.removeClass('load-overlay-in');
			setTimeout(function(){overlay.remove()}, 400);
		}, 400);
		return;
	}
	if(!e)return;
	let o = $.etarget(e);
	if(!overlay.length && !!!_this.data('overlay-no') && !!!_this.data('overlay-no-popoverView')){
		overlay = $('<div class="load-overlay"></div>');
		_this.append(overlay.css({background:'rgba(0,0,0,0.6)'}));
		overlay.on(window.eventType, function(){_this.popoverView(false)});
	}
	setTimeout(function(){overlay.addClass('load-overlay-in')}, 0);
	dialog = $('<div class="dialog-popover"><div class="dialog-popover-inner"><div class="dialog-popover-box"></div></div><div class="dialog-popover-angle"></div></div>');
	_this.append(dialog);
	let inner = dialog.find('.dialog-popover-box').stopBounces();
	if(typeof target !== 'object'){
		let htm = target+'', object = $(htm);
		if(object.length){
			if(object.next().length)object.data('originnext', object.next());
			else object.data('parent', object.parent());
			inner.append(object);
		}else{
			inner.html(htm);
		}
	}else{
		target = $(target);
		if(target.length){
			if(target.next().length)target.data('originnext', target.next());
			else target.data('parent', target.parent());
			inner.append(target);
		}
	}
	let ge = 4, width = dialog.width(), height = inner.height()>44*6-10 ? inner.height(44*6-10).height() : inner.height(), offset = o.offset(),
		angle = inner.parent().next(), angleWidth = angle.width(), angleHeight = angle.height(), left, top, scrollTop = $.scroll().top;
	left = offset.left + (o.width() - width) / 2;
	if(left < ge)left = ge;
	if(left+width > win.width-ge)left = win.width - width - ge;
	top = offset.top - height - angleHeight/2;
	if(top < ge){
		top = offset.top + o.height() + angleHeight/2;
		if(top+height > scrollTop+win.height-ge){
			let halfTop = offset.top - scrollTop - ge - angleHeight/2,
				halfBottom = scrollTop + win.height - offset.top - o.height() - ge - angleHeight/2;
			if(halfTop > halfBottom){
				inner.height(halfTop);
				top = offset.top - halfTop - angleHeight/2;
				angle.addClass('on-bottom');
			}else{
				inner.height(halfBottom);
				top = offset.top + o.height() + angleHeight/2;
				angle.addClass('on-top');
			}
		}else{
			angle.addClass('on-top');
		}
	}else{
		angle.addClass('on-bottom');
	}
	angle.css('left', offset.left + (o.width()-angleWidth)/2 - left);
	dialog.css({left:left, top:top});
	setTimeout(function(){
		dialog.addClass('dialog-popover-x');
	}, 10);
	return dialog;
};

export default {
	$
}