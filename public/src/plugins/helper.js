import Vue from 'vue'
import axios from 'axios'
import $ from '../../js/jquery-3.4.1.min'
window.$ = $

const version = '13.3.20211112'

if(window.self === window.top)try{(window.console && window.console.log) && (console.log('%c Developed by %c @mario %c v'+version+' ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#999;padding:2px;color:#fff', 'background:#bbb;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Welcome to %c laokema.com ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#dc0431;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Username/Password %c test/test ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#ff9902;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Wechat %c lwf000001 ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#41b883;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c QQ %c 172403414 ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#398bfc;padding:2px;border-radius:0 3px 3px 0;color:#fff'))}catch(e){}

//死循环调试，在怀疑死循环的地方加入__detectLoop()
let __count = 0
const __detectLoop = (loop) => {
	if (typeof loop === 'undefined') loop = 100
	if (__count > loop) {
		console.trace()
		throw new Error('Loop detected')
	}
	__count += 1
}

//把axios封装为快捷请求方法
const AJAX_TIME_OUT = 30000 //超时时间30秒
axios.interceptors.request.use(config => { //请求数据拦截处理
	//console.log(config)
	if (!/^http/.test(config.url)) {
		const account = JSON.parse($.storage('member'))
		if (account && account.sign) {
			config.headers['Sign'] = account.sign
		}
	}
	return config
}, error => {
	return Promise.reject(error)
})
axios.interceptors.response.use(response => { //返回数据拦截处理
	//console.log(response)
	//if (axios.caller) {}
	return response.data
}, error => Promise.reject(error.response))
const __request = (method, url, data, responseType, caller) => {
	const headers = {}
	const configData = {
		url, //请求的地址
		timeout: AJAX_TIME_OUT, //超时时间, 单位毫秒
		headers
	}
	if (method === 'get') {
		configData.method = 'get'
		configData.params = data
		if (typeof responseType === 'string' && responseType.length) configData.responseType = responseType
	} else if (method === 'post') {
		configData.method = 'post'
		if (data instanceof FormData) {
			configData.headers['Content-Type'] = 'multipart/form-data; charset=UTF-8'
			configData.data = data
		} else {
			configData.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8'
			configData.data = Object.entries(data).map(item => item.join('=')).join('&')
		}
	} else if (method === 'json') {
		configData.method = 'post'
		configData.headers['Content-Type'] = 'application/json; charset=UTF-8'
		configData.data = (() => {
			if (typeof data !== 'string') return data
			let obj = {}
			data.split('&').map(item => obj[item.split('=')[0]] = item.split('=')[1])
			return obj
		})(data)
	}
	if (caller) axios.caller = caller
	return axios(configData)
}
class Ajax {
	constructor () {
		this.outer = null
	}
	get (url, data = {}, responseType = '') {
		return __request('get', url, data, responseType, this.outer)
	}
	post (url, data = {}) {
		return __request('post', url, data, '', this.outer)
	}
	postJSON (url, data = {}) {
		return __request('json', url, data, '', this.outer)
	}
	caller (outer) {
		this.outer = outer
	}
}
//export default new Ajax()
/*
使用方法
import ajax from './ajax'
ajax.get(url, data).then(res => {
	//code
}).catch(err => {
	//code
})
//上传图片
const formData = new FormData()
formData.append('file', this.file) //this.file是input type='file'选中的图片
ajax.post(url, formData).then(res => {
	//code
}).catch(err => {
	//code
})
//直接把挂载在vue.prototype上，就不用每次都去导入
Vue.prototype.$ajax = new Ajax()
this.$ajax.get(url, data).then(res => {
	//code
}).catch(err => {
	//code
})
//做成一个插件的形式，在ajax.js中最后导出一个函数
//ajax.js
export default (Vue) => {
	if (typeof window !== 'undefined' && window.Vue) {
		Vue = window.Vue
	}
	Vue.prototype.$ajax = new Ajax()
}
//main.js
import Vue from 'vue'
import ajax from './ajax'
Vue.use(ajax)
//axios模拟表单提交
this.$axios.post(url, Object.entries(data).map(ent => ent.join('=')).join('&'), {
	headers: {
		'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
	},
}).then(res => {
	//code
})
*/

/*
//async/await用法
//async 是一个修饰符，async 定义的函数会默认的返回一个Promise对象resolve的值，因此对async函数可以直接进行then操作,返回的值即为then方法的传入函数
async function fun() {
	console.log('a')
	return 'a'
}
fun().then( x => { console.log(x) }) //输出 a a
async function fun() {
	console.log('Promise')
	return new Promise(function(resolve, reject){
		resolve('Promise')
	})
}
fun().then( x => { console.log(x) }) //输出 promise promise
//await 只能放在 async 函数内部， await的作用就是获取 Promise 中返回的内容，获取的是Promise函数中resolve或者reject的值
//如果 await 后面并不是一个Promise的返回值，则会按照同步程序返回值处理,为undefined
const bbb = function(){return 'string'}
async function fun() {
	const a = await 1
	const b = await new Promise((resolve, reject) => {
		setTimeout(function(){
			resolve('time')
		}, 3000)
	})
	const c = await bbb()
	console.log(a, b, c)
}
fun() //运行结果是 3秒钟之后，输出 1 time string
*/

$.extend($.fn, {
	//获取填充
	padding: function() {
		if (!this.length) return { top: 0, left: 0, bottom: 0, right: 0 }
		let top = (Number(this.css('padding-top').replace(/px/,''))||0), left = (Number(this.css('padding-left').replace(/px/,''))||0),
			bottom = (Number(this.css('padding-bottom').replace(/px/,''))||0), right = (Number(this.css('padding-right').replace(/px/,''))||0)
		return { top: top, left: left, bottom: bottom, right: right }
	},
	//获取间距
	margin: function() {
		if (!this.length) return { top: 0, left: 0, bottom: 0, right: 0 }
		let top = (Number(this.css('margin-top').replace(/px/,''))||0), left = (Number(this.css('margin-left').replace(/px/,''))||0),
			bottom = (Number(this.css('margin-bottom').replace(/px/,''))||0), right = (Number(this.css('margin-right').replace(/px/,''))||0)
		return { top: top, left: left, bottom: bottom, right: right }
	},
	//获取边宽
	border: function() {
		if (!this.length) return { top: 0, left: 0, bottom: 0, right: 0 }
		let top = (Number(this.css('border-top-width').replace(/px/,''))||0), left = (Number(this.css('border-left-width').replace(/px/,''))||0),
			bottom = (Number(this.css('border-bottom-width').replace(/px/,''))||0), right = (Number(this.css('border-right-width').replace(/px/,''))||0)
		return { top: top, left: left, bottom: bottom, right: right }
	},
	//获取transform
	transform: function() {
		if (!this.length) return { scale: 0, rotate: 0, translate: { x: 0, y: 0 } }
		if (this.css('transform') === 'none') return { scale: 0, rotate: 0, translate: { x: 0, y: 0 } }
		let matcher = this.css('transform').split('(')[1].split(')')[0].split(',')
		let scale = Math.sqrt(parseFloat(matcher[0]) * parseFloat(matcher[0]) + parseFloat(matcher[1]) * parseFloat(matcher[1]))
		let rotate = Math.round(Math.atan2(parseFloat(matcher[1]), parseFloat(matcher[0])) * (180 / Math.PI))
		let translate = { x: parseFloat(matcher[4]), y: parseFloat(matcher[5]) }
		return { scale: parseFloat(scale), rotate: parseFloat(rotate), translate: translate }
	},
	//获取选中的radio或checkbox/选中指定值的radio或checkbox(val:[字符|数字(索引选中)|数组|有返回值的函数])(isTrigger:自动执行change操作,默认true)
	checked: function(val, isTrigger){
		if(typeof val === 'undefined'){
			if(!this.length)return $([]);
			let name = this.attr('name');
			if(!!!name)name = this.attr('id');
			if(!!!name)return $([]);
			let box = this.parents('body').find('[name="'+name.replace(/\[]/,'\\[\\]')+'"]:checked');
			if(!box.length)box = _this.parents('body').find('[id="'+name.replace(/\[]/,'\\[\\]')+'"]:checked');
			if(!box.length)box = _this.parents('body').find('[id="'+name.replace(/\[]/,'\\[\\]')+'"][checked]');
			return box;
		}else{
			if(typeof isTrigger === 'undefined')isTrigger = true;
			if(val === null || (typeof val === 'string' && !val.length))return this;
			return this.each(function(){
				let _this = $(this), vals = [];
				let name = _this.attr('name');
				if(!!!name)name = _this.attr('id');
				//if(!!!name)return true;
				if($.isFunction(val)){
					let s = val.call(_this);
					$.isArray(s) ? vals = s : vals.push(s);
				}else{
					$.isArray(val) ? vals = val : vals.push(val);
				}
				let box = [];
				if(!!name){
					box = _this.parents('body').find('[name="'+name.replace(/\[]/,'\\[\\]')+'"]');
					if(!box.length)box = _this.parents('body').find('[id="'+name.replace(/\[]/,'\\[\\]')+'"]');
				}
				if(!box.length)box = _this;
				box.prop('checked', false);
				$.each(vals, function(i, v){
					if(typeof v === 'number'){
						box.filter(':eq('+v+')').prop('checked', true);
					}else if(typeof v === 'string'){
						box.filter('[value="'+v.replace(/"/g,'\"')+'"]').prop('checked', true);
					}else if(typeof v === 'boolean'){
						if(v)box.prop('checked', true);
						else box.prop('checked', false);
					}
				});
				if(isTrigger)box.trigger('change');
			});
		}
	},
	//获取选中的option/选中指定值的option(val:[字符|数字(索引选中)|数组|有返回值的函数])(isTrigger:自动执行change操作,默认true)
	selected: function(val, isTrigger){
		if(typeof val === 'undefined' || val === null || (typeof val === 'string' && !val.length)){
			if(!this.find('option').length)return $([]);
			let option = this.find('option:selected');
			if(!option.length)option = this.find('option[selected]');
			if(!option.length)option = this.find('option:eq(0)');
			return option;
		}else{
			if(typeof isTrigger === 'undefined')isTrigger = true;
			return this.each(function(){
				let _this = $(this), multiple = _this.is('[multiple]'), vals = [];
				if($.isFunction(val)){
					let s = val.call(_this);
					$.isArray(s) ? vals = s : vals.push(s);
				}else{
					$.isArray(val) ? vals = val : vals.push(val);
				}
				$.each(vals, function(i, v){
					if(!multiple)_this.find('option').prop('selected', false);
					if(typeof v === 'number'){
						_this.find('option:eq('+v+')').prop('selected', true);
					}else if(typeof v === 'string'){
						_this.find('option[value="'+v.replace(/"/g,'\"')+'"]').prop('selected', true);
					}
				});
				if(isTrigger)_this.trigger('change');
			});
		}
	},
	//scroll开始时执行
	scrollstart: function(callback){
		if(!$.isFunction(callback))return this;
		return this.each(function(){
			let _this = $(this);
			_this.on('scroll', function(e){
				if(!!_this.data('scrollstart'))return;
				_this.data('scrollstart', true);
				callback.call(_this[0], e);
			});
		});
	},
	//scroll停止时执行
	scrollstop: function(callback){
		if(!$.isFunction(callback))return this;
		return this.each(function(){
			let _this = $(this), timer = null,
				touchstart = function(){_this.data('scrollstop', true)},
				touchend = function(e){
					_this.removeData('scrollstop');
					if(!!!_this.data('skip-scrollstop.outside'))scroll(e);
				}, scroll = function(e){
					if(!!_this.data('skip-scrollstop'))return true;
					if(timer){clearTimeout(timer);timer = null}
					if(!!_this.data('scrollstop'))return true;
					timer = setTimeout(function(){
						clearTimeout(timer);timer = null;
						_this.removeData('scrollstart').removeData('scrollstop');
						callback.call(_this[0], e);
					}, 300);
				};
			_this.on('touchstart', touchstart).on('touchend', touchend).on('scroll', scroll);
		});
	},
	//设置/获取call者
	caller: function(caller){
		return arguments.length ? this.data('caller', caller) : this.data('caller')
	}
})

const filters = {
	//是否中文
	isCN: function(str) {
		return /^[\u4e00-\u9fa5]+$/.test(str)
	},
	//是否固话
	isTel: function(str) {
		return /^((\d{3,4}-)?\d{8}(-\d+)?|(\(\d{3,4}\))?\d{8}(-\d+)?)$/.test(str)
	},
	//是否手机
	isMobile: function(str) {
		return /^(\+?86)?1[3-8]\d{9}$/.test(str)
	},
	//是否邮箱
	isEmail: function(str) {
		return /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(str)
	},
	//是否日期字符串
	isDate: function(str) {
		return /^(?:(?!0000)[0-9]{4}[\/-](?:(?:0?[1-9]|1[0-2])[\/-](?:0?[1-9]|1[0-9]|2[0-8])|(?:0?[13-9]|1[0-2])[\/-](?:29|30)|(?:0?[13578]|1[02])[\/-]31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)[\/-]0?2[\/-]29)$/.test(str)
	},
	//是否身份证(严格)
	isIdCard: function(str) {
		let Wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1], //加权因子
			ValideCode = [1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2] //身份证验证位值,10代表X
		function idCardValidate(idCard) {
			if (idCard.length === 15) {
				return is15IdCard(idCard) //进行15位身份证的验证
			} else if (idCard.length === 18) {
				return is18IdCard(idCard) && isTrue18IdCard(idCard.split('')) //进行18位身份证的基本验证和第18位的验证
			} else {
				return false
			}
		}
		function isTrue18IdCard(idCard) {
			let sum = 0
			if (idCard[17].toLowerCase() === 'x') idCard[17] = 10 //将最后位为x的验证码替换为10方便后续操作
			for (let i = 0; i < 17; i++) sum += Wi[i] * idCard[i] //加权求和
			let valCodePosition = sum % 11 //得到验证码所位置
			return idCard[17] === ValideCode[valCodePosition]
		}
		function is18IdCard(idCard) {
			let year = idCard.substring(6, 10),
				month = idCard.substring(10, 12),
				day = idCard.substring(12, 14),
				date = new Date(year, parseInt(month) - 1, parseInt(day))
			return !(date.getFullYear() !== parseInt(year) || date.getMonth() !== parseInt(month) - 1 || date.getDate() !== parseInt(day))
		}
		function is15IdCard(idCard) {
			let year = idCard.substring(6, 8),
				month = idCard.substring(8, 10),
				day = idCard.substring(10, 12),
				date = new Date(year, parseInt(month) - 1, parseInt(day))
			return !(date.getYear() !== parseInt(year) || date.getMonth() !== parseInt(month) - 1 || date.getDate() !== parseInt(day))
		}
		return idCardValidate(str)
	},
	//检测JSON对象
	isJson: function(obj) {
		return $.isPlainObject(obj)
	},
	//obj转json字符串
	toJsonString: function(obj) {
		return JSON.stringify(obj)
	},
	//json字符串转obj
	toJson: function(str) {
		return JSON.parse(str)
	},
	//清除字符串两端指定字符
	trim: function(str, symbol) {
		if (typeof symbol === 'undefined' || !symbol.length) symbol = '\\s'
		symbol = symbol.replace(/([()\[\]*.?|^$]\\)/g, '\\$1');
		return String(str).replace(new RegExp('(^'+symbol+'+)|('+symbol+'+$)', 'g'), '')
	},
	//URL编码
	urlencode: function(str) {
		if (!str.length) return ''
		return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+')
	},
	//URL解密
	urldecode: function(url) {
		url = String(url)
		if (!url.length) return ''
		url = url.replace(/%25/g, '%').replace(/%21/g, '!').replace(/%27/g, "'").replace(/%28/g, '(').replace(/%29/g, ')').replace(/%2A/g, '*')
		return decodeURIComponent(url)
	},
	//保留两位小数
	round: function(value, prec) {
		prec = !isNaN(prec = Math.abs(prec)) ? prec : 2
		let res = Math.round(value * Math.pow(10, prec)) / Math.pow(10, prec)
		if (String(res).indexOf('.') < 0) {
			res += '.'
			for (let i = 0; i < prec; i++) res += '0'
		}
		return res
		//Math.ceil() 小数进一
		//Math.floor() 取整数部分
		//Math.round() 四舍五入
	},
	//增加前导零
	preZero: function(str, prec) {
		return (Array(prec).join('0') + '' + str).slice(-prec)
	},
	//加密base64
	/*base64Encode: function(str){
		return window.btoa(str);
	},
	//解密base64
	base64Decode: function(str){
		return window.atob(str);
	},*/
	base64: function() {
		let BASE64_MAPPING = [
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
			'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
			'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
			'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f',
			'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
			'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
			'w', 'x', 'y', 'z', '0', '1', '2', '3',
			'4', '5', '6', '7', '8', '9', '+', '/'
		];
		let URLSAFE_BASE64_MAPPING = [
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
			'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
			'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
			'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f',
			'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
			'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
			'w', 'x', 'y', 'z', '0', '1', '2', '3',
			'4', '5', '6', '7', '8', '9', '-', '_'
		];
		let _toBinary = function (ascii) {
			let binary = [];
			while (ascii > 0) {
				let b = ascii % 2;
				ascii = Math.floor(ascii / 2);
				binary.push(b);
			}
			binary.reverse();
			return binary;
		};
		let _toDecimal = function (binary) {
			let dec = 0;
			let p = 0;
			for (let i = binary.length - 1; i >= 0; --i) {
				let b = binary[i];
				if (b === 1) {
					dec += Math.pow(2, p);
				}
				++p;
			}
			return dec;
		};
		let _toUTF8Binary = function (c, binaryArray) {
			let mustLen = (8 - (c + 1)) + ((c - 1) * 6);
			let fatLen = binaryArray.length;
			let diff = mustLen - fatLen;
			while (--diff >= 0) {
				binaryArray.unshift(0);
			}
			let binary = [];
			let _c = c;
			while (--_c >= 0) {
				binary.push(1);
			}
			binary.push(0);
			let i = 0, len = 8 - (c + 1);
			for (; i < len; ++i) {
				binary.push(binaryArray[i]);
			}
			for (let j = 0; j < c - 1; ++j) {
				binary.push(1);
				binary.push(0);
				let sum = 6;
				while (--sum >= 0) {
					binary.push(binaryArray[i++]);
				}
			}
			return binary;
		};
		let _toBinaryArray = function (str) {
			let binaryArray = [];
			for (let i = 0, len = str.length; i < len; ++i) {
				let unicode = str.charCodeAt(i);
				let _tmpBinary = _toBinary(unicode);
				if (unicode < 0x80) {
					let _tmpdiff = 8 - _tmpBinary.length;
					while (--_tmpdiff >= 0) {
						_tmpBinary.unshift(0);
					}
					binaryArray = binaryArray.concat(_tmpBinary);
				} else if (unicode >= 0x80 && unicode <= 0x7FF) {
					binaryArray = binaryArray.concat(_toUTF8Binary(2, _tmpBinary));
				} else if (unicode >= 0x800 && unicode <= 0xFFFF) {//UTF-8 3byte
					binaryArray = binaryArray.concat(_toUTF8Binary(3, _tmpBinary));
				} else if (unicode >= 0x10000 && unicode <= 0x1FFFFF) {//UTF-8 4byte
					binaryArray = binaryArray.concat(_toUTF8Binary(4, _tmpBinary));
				} else if (unicode >= 0x200000 && unicode <= 0x3FFFFFF) {//UTF-8 5byte
					binaryArray = binaryArray.concat(_toUTF8Binary(5, _tmpBinary));
				} else if (unicode >= 4000000 && unicode <= 0x7FFFFFFF) {//UTF-8 6byte
					binaryArray = binaryArray.concat(_toUTF8Binary(6, _tmpBinary));
				}
			}
			return binaryArray;
		};
		let _toUnicodeStr = function (binaryArray) {
			let unicode;
			let unicodeBinary = [];
			let str = "";
			for (let i = 0, len = binaryArray.length; i < len;) {
				if (binaryArray[i] === 0) {
					unicode = _toDecimal(binaryArray.slice(i, i + 8));
					str += String.fromCharCode(unicode);
					i += 8;
				} else {
					let sum = 0;
					while (i < len) {
						if (binaryArray[i] === 1) {
							++sum;
						} else {
							break;
						}
						++i;
					}
					unicodeBinary = unicodeBinary.concat(binaryArray.slice(i + 1, i + 8 - sum));
					i += 8 - sum;
					while (sum > 1) {
						unicodeBinary = unicodeBinary.concat(binaryArray.slice(i + 2, i + 8));
						i += 8;
						--sum;
					}
					unicode = _toDecimal(unicodeBinary);
					str += String.fromCharCode(unicode);
					unicodeBinary = [];
				}
			}
			return str;
		};
		let _encode = function (str, url_safe) {
			let base64_Index = [];
			let binaryArray = _toBinaryArray(str);
			let dictionary = url_safe ? URLSAFE_BASE64_MAPPING : BASE64_MAPPING;
			let extra_Zero_Count = 0;
			for (let i = 0, len = binaryArray.length; i < len; i += 6) {
				let diff = (i + 6) - len;
				if (diff === 2) {
					extra_Zero_Count = 2;
				} else if (diff === 4) {
					extra_Zero_Count = 4;
				}
				let _tmpExtra_Zero_Count = extra_Zero_Count;
				while (--_tmpExtra_Zero_Count >= 0) {
					binaryArray.push(0);
				}
				base64_Index.push(_toDecimal(binaryArray.slice(i, i + 6)));
			}
			let base64 = '';
			for (let i = 0, len = base64_Index.length; i < len; ++i) {
				base64 += dictionary[base64_Index[i]];
			}
			for (let i = 0, len = extra_Zero_Count / 2; i < len; ++i) {
				base64 += '=';
			}
			return base64;
		};
		let _decode = function (_base64Str, url_safe) {
			let _len = _base64Str.length;
			let extra_Zero_Count = 0;
			let dictionary = url_safe ? URLSAFE_BASE64_MAPPING : BASE64_MAPPING;
			if (_base64Str.charAt(_len - 1) === '=') {
				if (_base64Str.charAt(_len - 2) === '=') {//两个等号说明补了4个0
					extra_Zero_Count = 4;
					_base64Str = _base64Str.substring(0, _len - 2);
				} else {//一个等号说明补了2个0
					extra_Zero_Count = 2;
					_base64Str = _base64Str.substring(0, _len - 1);
				}
			}
			let binaryArray = [];
			for (let i = 0, len = _base64Str.length; i < len; ++i) {
				let c = _base64Str.charAt(i);
				for (let j = 0, size = dictionary.length; j < size; ++j) {
					if (c === dictionary[j]) {
						let _tmp = _toBinary(j);
						/*不足6位的补0*/
						let _tmpLen = _tmp.length;
						if (6 - _tmpLen > 0) {
							for (let k = 6 - _tmpLen; k > 0; --k) {
								_tmp.unshift(0);
							}
						}
						binaryArray = binaryArray.concat(_tmp);
						break;
					}
				}
			}
			if (extra_Zero_Count > 0) {
				binaryArray = binaryArray.slice(0, binaryArray.length - extra_Zero_Count);
			}
			let str = _toUnicodeStr(binaryArray);
			return str;
		};
		return {
			encode: function (str) {
				return _encode(str, false);
			},
			decode: function (base64Str) {
				return _decode(base64Str, false);
			}
		};
	},
	//md5加密
	md5: function(str) {
		let hexcase=0;let chrsz=8;function hex_md5(s){return binl2hex(core_md5(str2binl(s),s.length*chrsz))}function core_md5(x,len){x[len>>5]|=0x80<<((len)%32);x[(((len+64)>>>9)<<4)+14]=len;let a=1732584193;let b=-271733879;let c=-1732584194;let d=271733878;for(let i=0;i<x.length;i+=16){let olda=a;let oldb=b;let oldc=c;let oldd=d;a=md5_ff(a,b,c,d,x[i],7,-680876936);d=md5_ff(d,a,b,c,x[i+1],12,-389564586);c=md5_ff(c,d,a,b,x[i+2],17,606105819);b=md5_ff(b,c,d,a,x[i+3],22,-1044525330);a=md5_ff(a,b,c,d,x[i+4],7,-176418897);d=md5_ff(d,a,b,c,x[i+5],12,1200080426);c=md5_ff(c,d,a,b,x[i+6],17,-1473231341);b=md5_ff(b,c,d,a,x[i+7],22,-45705983);a=md5_ff(a,b,c,d,x[i+8],7,1770035416);d=md5_ff(d,a,b,c,x[i+9],12,-1958414417);c=md5_ff(c,d,a,b,x[i+10],17,-42063);b=md5_ff(b,c,d,a,x[i+11],22,-1990404162);a=md5_ff(a,b,c,d,x[i+12],7,1804603682);d=md5_ff(d,a,b,c,x[i+13],12,-40341101);c=md5_ff(c,d,a,b,x[i+14],17,-1502002290);b=md5_ff(b,c,d,a,x[i+15],22,1236535329);a=md5_gg(a,b,c,d,x[i+1],5,-165796510);d=md5_gg(d,a,b,c,x[i+6],9,-1069501632);c=md5_gg(c,d,a,b,x[i+11],14,643717713);b=md5_gg(b,c,d,a,x[i],20,-373897302);a=md5_gg(a,b,c,d,x[i+5],5,-701558691);d=md5_gg(d,a,b,c,x[i+10],9,38016083);c=md5_gg(c,d,a,b,x[i+15],14,-660478335);b=md5_gg(b,c,d,a,x[i+4],20,-405537848);a=md5_gg(a,b,c,d,x[i+9],5,568446438);d=md5_gg(d,a,b,c,x[i+14],9,-1019803690);c=md5_gg(c,d,a,b,x[i+3],14,-187363961);b=md5_gg(b,c,d,a,x[i+8],20,1163531501);a=md5_gg(a,b,c,d,x[i+13],5,-1444681467);d=md5_gg(d,a,b,c,x[i+2],9,-51403784);c=md5_gg(c,d,a,b,x[i+7],14,1735328473);b=md5_gg(b,c,d,a,x[i+12],20,-1926607734);a=md5_hh(a,b,c,d,x[i+5],4,-378558);d=md5_hh(d,a,b,c,x[i+8],11,-2022574463);c=md5_hh(c,d,a,b,x[i+11],16,1839030562);b=md5_hh(b,c,d,a,x[i+14],23,-35309556);a=md5_hh(a,b,c,d,x[i+1],4,-1530992060);d=md5_hh(d,a,b,c,x[i+4],11,1272893353);c=md5_hh(c,d,a,b,x[i+7],16,-155497632);b=md5_hh(b,c,d,a,x[i+10],23,-1094730640);a=md5_hh(a,b,c,d,x[i+13],4,681279174);d=md5_hh(d,a,b,c,x[i],11,-358537222);c=md5_hh(c,d,a,b,x[i+3],16,-722521979);b=md5_hh(b,c,d,a,x[i+6],23,76029189);a=md5_hh(a,b,c,d,x[i+9],4,-640364487);d=md5_hh(d,a,b,c,x[i+12],11,-421815835);c=md5_hh(c,d,a,b,x[i+15],16,530742520);b=md5_hh(b,c,d,a,x[i+2],23,-995338651);a=md5_ii(a,b,c,d,x[i],6,-198630844);d=md5_ii(d,a,b,c,x[i+7],10,1126891415);c=md5_ii(c,d,a,b,x[i+14],15,-1416354905);b=md5_ii(b,c,d,a,x[i+5],21,-57434055);a=md5_ii(a,b,c,d,x[i+12],6,1700485571);d=md5_ii(d,a,b,c,x[i+3],10,-1894986606);c=md5_ii(c,d,a,b,x[i+10],15,-1051523);b=md5_ii(b,c,d,a,x[i+1],21,-2054922799);a=md5_ii(a,b,c,d,x[i+8],6,1873313359);d=md5_ii(d,a,b,c,x[i+15],10,-30611744);c=md5_ii(c,d,a,b,x[i+6],15,-1560198380);b=md5_ii(b,c,d,a,x[i+13],21,1309151649);a=md5_ii(a,b,c,d,x[i+4],6,-145523070);d=md5_ii(d,a,b,c,x[i+11],10,-1120210379);c=md5_ii(c,d,a,b,x[i+2],15,718787259);b=md5_ii(b,c,d,a,x[i+9],21,-343485551);a=safe_add(a,olda);b=safe_add(b,oldb);c=safe_add(c,oldc);d=safe_add(d,oldd)}return Array(a,b,c,d)}function md5_cmn(q,a,b,x,s,t){return safe_add(bit_rol(safe_add(safe_add(a,q),safe_add(x,t)),s),b)}function md5_ff(a,b,c,d,x,s,t){return md5_cmn((b&c)|((~b)&d),a,b,x,s,t)}function md5_gg(a,b,c,d,x,s,t){return md5_cmn((b&d)|(c&(~d)),a,b,x,s,t)}function md5_hh(a,b,c,d,x,s,t){return md5_cmn(b^c^d,a,b,x,s,t)}function md5_ii(a,b,c,d,x,s,t){return md5_cmn(c^(b|(~d)),a,b,x,s,t)}function safe_add(x,y){let lsw=(x&0xFFFF)+(y&0xFFFF);let msw=(x>>16)+(y>>16)+(lsw>>16);return(msw<<16)|(lsw&0xFFFF)}function bit_rol(num,cnt){return(num<<cnt)|(num>>>(32-cnt))}function str2binl(str){let bin=Array();let mask=(1<<chrsz)-1;for(let i=0;i<str.length*chrsz;i+=chrsz)bin[i>>5]|=(str.charCodeAt(i/chrsz)&mask)<<(i%32);return bin}function binl2hex(binarray){let hex_tab=hexcase?'0123456789ABCDEF':'0123456789abcdef';let str="";for(let i=0;i<binarray.length*4;i++){str+=hex_tab.charAt((binarray[i>>2]>>((i%4)*8+4))&0xF)+hex_tab.charAt((binarray[i>>2]>>((i%4)*8))&0xF)}return str}return hex_md5(str);
	}
}

$.extend(filters)


//判断浏览器
$.uaMatch = function(ua) {
	let match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
		/(webkit)[ \/]([\w.]+)/.exec(ua) ||
		/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
		/(msie) ([\w.]+)/.exec(ua) ||
		ua.indexOf('compatible') === -1 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
		[]
	return {
		browser: match[1] || '',
		version: match[2] || '0'
	}
}
let browser = { ua: navigator.userAgent }, uaMatch = $.uaMatch(browser.ua)
if (uaMatch.browser) {
	browser[uaMatch.browser] = true
	browser.version = uaMatch.version
}
if (browser.ua.match(/windows mobile/i)) browser.wm = true
else if (browser.ua.match(/windows ce/i)) browser.wince = true
else if (browser.ua.match(/ucweb/i)) browser.ucweb = true
else if (browser.ua.match(/rv:1.2.3.4/i)) browser.uc7 = true
else if (browser.ua.match(/midp/i)) browser.midp = true
else if (browser.msie) {
	if (browser.version < 7) browser.ie6 = true
	else if (browser.version < 8) browser.ie7 = true
	else if (browser.version < 9) browser.ie8 = true
	else if (browser.version < 10) browser.ie9 = true
}
else if (browser.chrome) browser.webkit = true
else if (browser.webkit) {
	browser.safari = true
	let matcher = /safari\/([\d._]+)/.exec(browser.ua)
	if (matcher instanceof Array) browser.version = matcher[1].replace(/_/g, '.')
}
else if (browser.mozilla) browser.firefox = true
if (browser.ua.match(/iphone/i) || browser.ua.match(/ipad/i)) {
	if (browser.ua.match(/iphone/i)) browser.iphone = true
	if (browser.ua.match(/ipad/i)) browser.ipad = true
	let matcher = / os ([\d._]+) /.exec(browser.ua)
	if (matcher instanceof Array) browser.version = matcher[1].replace(/_/g, '.')
}
if (browser.ua.match(/android/i)) {
	browser.android = true
	let matcher = /android ([\d.]+)/.exec(browser.ua)
	if (matcher instanceof Array) browser.version = matcher[1]
}
if (browser.iphone || browser.ipad) browser.ios = true
if (browser.ua.match(/micromessenger/i) && (browser.ios || browser.android)) browser.wechat = browser.weixin = browser.wx = true
if (browser.ios || browser.android || browser.wm || browser.wince || browser.ucweb || browser.uc7 || browser.midp || browser.wx) browser.mobile = true

//自定义方法
$.extend({
	browser: browser,
	//get请求
	get: function(url, data, responseType) {
		return new Ajax().get(url, data, responseType)
	},
	//post请求
	post: function(url, data) {
		return new Ajax().post(url, data)
	},
	//post json请求
	postJSON: function(url, data) {
		return new Ajax().postJSON(url, data)
	},
	// 浏览器本地存储, time:单位天,默认一天
	// storage(); 返回window.localStorage
	// storage('key'); 获取
	// storage('key', 'value'); 设置
	// storage('key', 'value', 1/24); 设置,过期时间为1小时
	// storage('key', null); 删除
	// storage(null); 删除所有
	storage: function(key, data, time) {
		if (typeof key === 'undefined') return window.localStorage
		let prefix = 'storage'
		if (key === null) {
			for (let i = 0; i < window.localStorage.length; i++) {
				if ((window.localStorage.key(i).split('_') || [''])[0] === prefix) {
					window.localStorage.removeItem(name)
				}
			}
			return null
		}
		key = {data:prefix+'_data_'+encodeURIComponent(key), time:prefix+'_time_'+encodeURIComponent(key)}
		if (window.localStorage) {
			if (typeof data === 'undefined') {
				data = window.localStorage.getItem(key.data)
				if(data){
					if (Number(window.localStorage.getItem(key.time)) > (new Date()).getTime()) {
						//value = JSON.parse(value);
						return data
					} else {
						window.localStorage.removeItem(key.data)
						window.localStorage.removeItem(key.time)
					}
				}
			} else if (data === null) {
				window.localStorage.removeItem(key.data)
				window.localStorage.removeItem(key.time)
			} else {
				if (typeof time === 'undefined') time = 1
				time = (new Date()).getTime() + Number(time) * 24*60*60*1000
				if (typeof data !== 'string') data = JSON.stringify(data)
				window.localStorage.setItem(key.data, data)
				window.localStorage.setItem(key.time, time)
			}
		} else {
			if (typeof data === 'undefined') {
				data = $.cookie(key.data)
				if (data) {
					if (Number($.cookie(key.time)) > (new Date()).getTime()) {
						//value = JSON.parse(value);
						return data
					} else {
						$.cookie(key.data, null)
						$.cookie(key.time, null)
					}
				}
			} else if (data === null) {
				$.cookie(key.data, null)
				$.cookie(key.time, null)
			} else {
				if (typeof time === 'undefined') time = 1
				if (typeof data !== 'string') data = JSON.stringify(data)
				$.cookie(key.data, data, {expires:time})
				$.cookie(key.time, time, {expires:time})
			}
		}
		return null
	},
	// cookie(); //返回document.cookie
	// cookie('name'); //获取
	// cookie('name', 'value'); //保存
	// cookie('name', 'value', { expires:7, path:'/', domain:'jquery.com', secure:true }); //保存带有效期(单位天),路径,域名,安全协议
	// cookie('name', '', { expires:-1 }); or cookie('name', null); //删除
	cookie: function(name, value, options) {
		if (typeof name === 'undefined') return document.cookie
		if (typeof value !== 'undefined') {
			options = options || {}
			if (value === null) {
				value = ''
				options.expires = -1
			}
			if (typeof value !== 'string') value = JSON.stringify(value)
			let expires = '';
			if (!isNaN(options)) {
				let date = new Date()
				date.setTime(date.getTime() + (options * 24*60*60*1000) + (8*60*60*1000))
				expires = ';expires=' + date.toUTCString()
				options = {}
			} else if (options.expires && (typeof options.expires === 'number' || options.expires.toUTCString)) {
				let date = ''
				if (typeof options.expires === 'number') {
					date = new Date()
					date.setTime(date.getTime() + (options.expires * 24*60*60*1000) + (8*60*60*1000))
				} else {
					date = options.expires
				}
				expires = ';expires=' + date.toUTCString()
			}
			let path = options.path ? ';path='+options.path : ''
			let domain = options.domain ? ';domain='+options.domain : ''
			let secure = options.secure ? ';secure' : ''
			document.cookie = [name, '=', value, expires, path, domain, secure].join('')
			return true
		} else {
			value = null
			if (document.cookie.length) {
				let cookies = document.cookie.split(';')
				for (let i = 0; i < cookies.length; i++) {
					let cookie = trim(cookies[i])
					if (cookie.substring(0, name.length+1) === (name + '=')) {
						value = cookie.substring(name.length+1)
						break
					}
				}
			}
			return value
		}
	},
	//获取浏览器本地存储且转对象
	storageJSON: function(key) {
		let data = $.storage(key)
		return !data ? null : JSON.parse(data)
	},
	//获取cookie且转对象
	cookieJSON: function(name) {
		let data = $.cookie(name)
		return !data ? null : JSON.parse(data)
	},
	//判断当前是否开发模式
	isDevelopment: function() {
		return location.href.indexOf('localhost') > -1
	},
	//封装接口数据是否提示错误，if (!this.$.checkError(json, this)) return
	checkError: function(json, outer) {
		if (typeof json.error !== 'undefined' && typeof json.msg !== 'undefined' && json.error > 0) {
			if (outer instanceof Vue) outer.$emit('overloaderror', json.msg)
			else if (outer !== null) alert(json.msg)
			return false
		}
		return true
	},
	//rem转px
	toPx: function(rem) {
		let size = $.changeRem(true)
		return rem * size
	},
	//设置rem
	changeRem: function(getReturn) {
		__detectLoop()
		const docEl = document.documentElement
		let size = 100 * (docEl.clientWidth / 320)
		if (docEl.clientWidth >= 768) size = 100
		if (!getReturn) docEl.style.fontSize = size + 'px'
		else return size
	}
})

if (/iPhone|iPad|iPod|Android|Windows Phone|Windows CE|Windows Mobile|Midp|rv:1.2.3.4|UcWeb|webOS|BlackBerry/i.test(navigator.userAgent.toLowerCase())) {
	$(document.body.parentNode).addClass('wapWeb')
}
$.changeRem()
window.onresize = function() {
	$.changeRem() //改变窗口大小时重新设置rem
}

//AJAX上传文件, this.$(this.$refs.div).caller(this).ajaxupload(options)
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
	let core = this
	return this.each(function() {
		let parent = this
		let width = parent.clientWidth, height = parent.clientHeight
		let count = 0
		const insertFileInput = () => {
			count++
			let input = document.createElement('input')
			input.type = 'file'
			input.name = options.name
			input.id = 'input'+count
			input.setAttribute('style', 'position:absolute;z-index:999;top:0;right:0;opacity:0;margin:0;width:'+width+'px;height:'+height+'px;font-size:'+height+'px;cursor:pointer;')
			parent.appendChild(input)
			input.addEventListener('change', (event) => {
				let files = event.target.files
				if($.isFunction(options.before)){
					result = options.before.call(core.outer, event, files.length, files);
					if (typeof result === 'boolean' && !result) {
						input.parentNode.removeChild(input)
						insertFileInput()
						if ($.isFunction(options.cancel)) options.cancel.call(core.outer)
						return
					}
				}
				let promiseArr = []
				for (let i=0; i<files.length; i++) {
					promiseArr.push(new Promise((resolve, reject) => {
						if (fileType.length && !RegExp('\.('+fileType.join('|')+')$', 'i').test(files[i].name.toLowerCase())) {
							core.outer.$emit('overloaderror', '请选择'+fileType.join(',')+'类型的文件')
							input.parentNode.removeChild(input)
							insertFileInput()
							if ($.isFunction(options.error)) options.error.call(core.outer)
							reject()
							return
						}
						fileHandler(files[i])
						resolve()
					}))
				}
				Promise.all(promiseArr).then(() => {
					if ($.isFunction(options.complete)) options.complete.call(core.outer)
				}).catch(() => {})
			})
		}
		const fileHandler = (item) => {
			const formData = new FormData()
			formData.append(options.name, item)
			if ($.isPlainObject(options.data)) {
				for (let key in options.data) {
					formData.append(key, options.data[key])
				}
			}
			core.outer.$ajax.post(options.url, formData).then(options.success).catch(options.error)
		}
		insertFileInput()
	})
}

//加载图片, this.$(this.$refs.pic).loadpic(url)
$.fn.loadpic = function(url, errorpic, complete) {
	if (typeof errorpic === 'undefined' && typeof complete === 'undefined') errorpic = '/images/nopic.png'
	if (typeof errorpic === 'function' && typeof complete === 'undefined') {
		complete = errorpic
		errorpic = '/images/nopic.png'
	}
	let core = this
	return this.each(function() {
		let element = this
		if (element.tagName.toLowerCase() === 'img') {
			if ( !core.caller() || !(core.caller() instanceof Vue) ) {
				alert("IMG use loadpic plugins must be set caller")
				return
			}
			core.caller().$ajax.get(url, {}, 'blob').then(blob => {
				element.onload = () => {
					complete && complete(element, url, true)
				}
				element.onerror = () => {
					element.src = errorpic
					complete && complete(element, errorpic, false)
				}
				let objectURL = window.URL || window.webkitURL
				element.src = objectURL.createObjectURL(blob)
			}).catch(() => {
				element.src = errorpic
				complete && complete(element, errorpic, false)
			})
			return
		}
		element.style.position = 'relative'
		$(element).children().hide()
		$(element).append('<div class="preloader-gray"></div>')
		const callback = (item, url, state) => {
			complete && complete(item, url, state)
			item.style.position = ''
			$(item).find('.preloader-gray').remove()
			$(item).children().css('display', '')
		}
		let img = new Image()
		img.onload = () => {
			callback(element, url, true)
		}
		img.onerror = () => {
			callback(element, errorpic, false)
		}
		img.src = url
	})
}

//动画加载背景图, attribute、backgroundSize可为返回字符串的函数, this.$(this.$refs.pic).loadbackground('url', '50%', '../images/nopic.png')
$.fn.loadbackground = function(attribute, backgroundSize, errorpic) {
	if (typeof backgroundSize === 'undefined') backgroundSize = '50%'
	if (typeof errorpic === 'undefined') errorpic = '/images/nopic.png'
	let core = this
	return this.each(function() {
		let item = this
		if (item.getAttribute('loadbackground')) return true
		item.style.backgroundImage = ''
		let attr = attribute
		if (typeof attr === 'function') attr = attr(item)
		if (!item.getAttribute(attr)) return true
		$(item).caller(core.caller()).loadpic(item.getAttribute(attr), errorpic, (item, pic, state) => {
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

//设置价格数字样式
$.fn.priceFont = function(className){
	return this.each(function(){
		let _this = $(this), text = _this.text()
		if (!text.length || (text.indexOf('￥') === -1 && text.indexOf('.') === -1) || _this.children('.'+className).length) return true
		if (text.indexOf('￥')>-1) {
			let ar = text.split('￥'), prefix = ar[0]+'￥', arr = ar[1].split('.'), integer = arr[0], decimal = ''
			if (arr.length===2) decimal = '.'+arr[1]
			text = prefix + '<font class="'+className+'">' + integer + '</font>' + decimal
		} else {
			let arr = text.split('.'), integer = arr[0], decimal = ''
			if (arr.length === 2) decimal = '.'+arr[1]
			text = '<font class="'+className+'">' + integer + '</font>' + decimal
		}
		_this.html(text)
	})
}

//按原宽高的比例自动设定高度, percent是按屏幕宽度作为参考对象
$.autoHeight = $.fn.autoHeight = function(originWidth, originHeight, percent){
	let fn = function(isReturn){
		if (isReturn) return Math.floor($(this).width() * originHeight / originWidth)
		let _this = $(this);
		//if(!_this.is(':visible'))return true;
		if(percent){
			_this.width( Math.floor((document.documentElement.clientWidth || document.body.clientWidth) * percent) );
		}
		if((originWidth+'').indexOf('%')>-1){
			_this.width( originWidth );
		}
		if((originHeight+'').indexOf('%')>-1){
			_this.height( originHeight );
		}else{
			_this.height( Math.floor(_this.width() * originHeight / originWidth) );
		}
	}
	if (this instanceof $) {
		return this.each(function(){
			fn.call(this)
		})
	} else {
		return fn.call(percent, true)
	}
}

export default {
	$,
	filters,
	Ajax
}