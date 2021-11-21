import helper from './helper'
import coo from './coo.fn'
import $ from '../../js/jquery-3.4.1.min';
window.$ = coo.$

const version = '13.6.20211121'

if(window.self === window.top)try{(window.console && window.console.log) && (console.log('%c Developed by %c @mario %c v'+version+' ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#999;padding:2px;color:#fff', 'background:#bbb;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Welcome to %c laokema.com ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#dc0431;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Username/Password %c test/test ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#ff9902;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Wechat %c lwf000001 ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#41b883;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c QQ %c 172403414 ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#398bfc;padding:2px;border-radius:0 3px 3px 0;color:#fff'))}catch(e){}

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
	//移动端禁止内容区域滚到顶/底后引起页面整体的滚动, remove取消禁止
	stopBounces: function(remove){
		return this.each(function(){
			let _this = $(this), startX, startY,
				start = function(e){
					startX = $.touches(e).x;
					startY = $.touches(e).y;
				},
				move = function(e){
					//高位表示向上滚动, 底位表示向下滚动, 1容许 0禁止
					let status = '11', ele = this, currentX = $.touches(e).x, currentY = $.touches(e).y;
					if(currentX-startX>=8 || currentX-startX<=-8){e.preventDefault();return false}
					if(ele.scrollTop === 0){ //如果内容小于容器则同时禁止上下滚动
						status = ele.offsetHeight >= ele.scrollHeight ? '00' : '01';
					}else if(ele.scrollTop + ele.offsetHeight >= ele.scrollHeight){ //已经滚到底部了只能向上滚动
						status = '10';
					}
					if(status !== '11'){
						let direction = currentY - startY > 0 ? '10' : '01'; //判断当前的滚动方向
						//操作方向和当前允许状态求与运算, 运算结果为0, 就说明不允许该方向滚动, 则禁止默认事件, 阻止滚动
						if(!(parseInt(status, 2) & parseInt(direction, 2)))e.preventDefault();
					}
				};
			if(remove){
				_this.removeData('stopBounces');
				this.removeEventListener('touchstart', start, true);
				this.removeEventListener('touchmove', move, true);
				return;
			}
			if(!$.browser.mobile || !!_this.data('stopBounces') || !!_this.data('drag') || !!_this.data('dragshow') || !!_this.data('touchmove') || !!_this.data('pullRefresh'))return;
			_this.data('stopBounces', true);
			this.addEventListener('touchstart', start, true);
			this.addEventListener('touchmove', move, true);
		});
	},
	//解除插件
	removePlug: function(dataName){
		if(!!this.data(dataName+'-plug')){
			this.find('*').off().remove();
			let html = this.data(dataName+'-html');
			this.empty().html('').html(html?html:'');
			return true;
		}else{
			this.data(dataName+'-plug', true);
			this.data(dataName+'-html', this.html());
			return false;
		}
	},
	//获取outerHTML
	outerHTML: function(){
		return this.prop('outerHTML');
	},
	//不能选中
	unselect: function(flag){
		if(typeof flag !== 'undefined' && !flag){
			return this.attr('unselectable', '').css({'-webkit-user-select':'', 'user-select':''}).off('selectstart', this.data('unselect'));
		}else{
			this.data('unselect', function(){ return false });
			return this.attr('unselectable', 'on').css({'-webkit-user-select':'none', 'user-select':'none'}).on('selectstart', this.data('unselect'));
		}
	}
})

//动画过渡效果
$.extend($.easing, {
	linear: function(x){
		return x
	},
	swing: function(x){
		return 0.5 - Math.cos( x*Math.PI ) / 2
	},
	easeout: function(x, t, b, c, d){
		return -c * (t /= d) * (t - 2) + b
	},
	bounceout: function(x, t, b, c, d){
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b
		}
	},
	backout: function(x, t, b, c, d){
		let s = 1.70158
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b
	}
})

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
	etarget: function(e){return e.target||e.srcElement},
	ebubble: function(e, breaker, callback){
		if (!$.isFunction(breaker) || !$.isFunction(callback)) return
		let o = $.etarget(e);
		do {
			if (breaker(o)) return
			if ((/^(html|body)$/i).test(o.tagName)) {
				callback()
				return
			}
			o = o.parentNode
		} while(o.parentNode)
	},
	//get请求
	get: function(url, data, responseType) {
		return new helper.Ajax().get(url, data, responseType)
	},
	//post请求
	post: function(url, data) {
		return new helper.Ajax().post(url, data)
	},
	//post json请求
	postJSON: function(url, data) {
		return new helper.Ajax().postJSON(url, data)
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
	//去除单位px
	unit: function(unit){
		unit = unit.toString();
		if(!unit)return 0;
		if(unit !== '0')unit = /^-?\d+$/.test(unit) ? unit : unit.replace(/px/g, '');
		return Number(unit);
	},
	resize: function(resize){
		if(!$.isFunction(resize))return;
		let fn = $(document).data(resize.toString());
		if(!!fn)return;
		$(document).data(resize.toString(), resize);
		(function (doc, win, resize){
			let resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize';
			if(!doc.addEventListener)return;
			win.addEventListener(resizeEvt, function(){setTimeout(resize, 200)}, false);
			doc.addEventListener('DOMContentLoaded', function(){setTimeout(resize, 200)}, false);
		})(document, window, resize);
	},
	//获取event对象的屏幕距离
	touches: function(e, type){
		let x = 0, y = 0, pageX = 0, pageY = 0,
			changedTouches = [{'pageX':0, 'pageY':0}], targetTouches = [{'pageX':0, 'pageY':0}], touches = [{'pageX':0, 'pageY':0}];
		if(typeof e.changedTouches !== 'undefined')changedTouches = e.changedTouches;
		if(typeof e.targetTouches !== 'undefined')targetTouches = e.targetTouches;
		if(typeof e.touches !== 'undefined')touches = e.touches;
		if(typeof e.pageX !== 'undefined'){pageX = e.pageX; pageY = e.pageY}
		if(typeof e.changedTouches !== 'undefined'){
			x = e.changedTouches[0].pageX; y = e.changedTouches[0].pageY;
		}else if(typeof e.targetTouches !== 'undefined'){
			x = e.targetTouches[0].pageX; y = e.targetTouches[0].pageY;
		}else if(typeof e.touches !== 'undefined'){
			x = e.touches[0].pageX; y = e.touches[0].pageY;
		}else if(typeof e.pageX !== 'undefined'){
			x = e.pageX; y = e.pageY;
		}
		if(typeof type !== 'undefined'){
			switch(type){
				case 1:x = changedTouches[0].pageX; y = changedTouches[0].pageY;break;
				case 2:x = targetTouches[0].pageX; y = targetTouches[0].pageY;break;
				case 3:x = touches[0].pageX; y = touches[0].pageY;break;
				case 4:x = pageX; y = pageY;break;
				case 5:x = e.clientX; y = e.clientY;break;
			}
			return {x:x, y:y};
		}
		return {changedTouches:changedTouches, targetTouches:targetTouches, touches:touches, pageX:pageX, pageY:pageY, clientX:e.clientX, clientY:e.clientY, x:x, y:y};
	},
	random: function(min, max){
		return Math.floor(Math.random() * (max - min + 1) + min)
	},
	//生成由数字,大写字母,小写字母组合的指定位数的随机字符串, s:指定字符(可使用中文字), randomCode(8,'')
	randomCode: function(n, s){
		let o, codes = ''
		if (!s) s = 'EabXYcde12OP3FBADCUijk45WZlt6GHLMvwIJKfgh90TxNQRSmnopyzqrs78Vu'
		if (isNaN(n)) return ''
		o = s.split('')
		for(let i = 0; i < n; i++){
			let id = Math.ceil(Math.random()*o.length)
			codes += o[id]
		}
		return codes
	},
	datetimeAndRandom: function(){
		return (new Date()).formatDate('yyyymmddhhnnss') + Math.ceil(Math.random()*8999+1000)
	},
	window: function(father){
		let docEl = window.document.documentElement, doc = $.document(father)
		return {
			width:doc.clientWidth, height:doc.clientHeight,
			scrollLeft:doc.scrollLeft, scrollTop:doc.scrollTop,
			scrollWidth:doc.scrollWidth, scrollHeight:doc.scrollHeight,
			minWidth:Math.min(doc.clientWidth, doc.scrollWidth), minHeight:Math.min(doc.clientHeight, doc.scrollHeight),
			maxWidth:Math.max(doc.clientWidth, doc.scrollWidth), maxHeight:Math.max(doc.clientHeight, doc.scrollHeight),
			screenWidth:docEl.getBoundingClientRect()?docEl.getBoundingClientRect().width:window.screen.width,
			screenHeight:docEl.getBoundingClientRect()?docEl.getBoundingClientRect().height:window.screen.height,
			ratio:window.devicePixelRatio?window.devicePixelRatio:1
		}
	},
	document: function(father){
		let doc = null
		switch(father){
			case 'top':doc = top.document[top.document.compatMode === 'CSS1Compat' ? 'documentElement' : 'body'];break;
			case 'parent':doc = parent.document[parent.document.compatMode === 'CSS1Compat' ? 'documentElement' : 'body'];break;
			default:doc = document[document.compatMode === 'CSS1Compat' ? 'documentElement' : 'body'];break;
		}
		return doc
	},
	//遮罩层与展示层
	overlay: function(target, type, callback, closeCallback){
		return $('#app').children().not('.footer').overlay(target, type, callback, closeCallback);
	},
	//加载动画遮罩层
	overload: function(text, image, auto, callback){
		let app = $.component()
		if (app) app.overload(text, image, auto, callback)
	},
	//成功遮罩层
	overloadSuccess: function(text, auto, callback){
		if (typeof auto === 'undefined') auto = 3000
		setTimeout(() => $.overload(text, '.load-success', auto, callback), 0)
	},
	//失败遮罩层
	overloadError: function(text, auto, callback){
		if (typeof auto === 'undefined') auto = 3000
		setTimeout(() => $.overload(text, '.load-error', auto, callback), 0)
	},
	//问题遮罩层
	overloadProblem: function(text, auto, callback){
		if (typeof auto === 'undefined') auto = 3000
		setTimeout(() => $.overload(text, '.load-problem', auto, callback), 0)
	},
	//警告遮罩层
	overloadWarning: function(text, auto, callback){
		if (typeof auto === 'undefined') auto = 3000
		setTimeout(() => $.overload(text, '.load-warning', auto, callback), 0)
	},
	//手机端摇动, 为避免多次调用 callback, 需要在操作页面增加一个全局变量来控制, 例如:
	//let shake = false; $.shake(function(){ if(!shake){shake=true; ... } });
	shake: function(callback){
		if(window.DeviceMotionEvent && $.isFunction(callback)){
			let speed = 20, x = 0, y = 0, lastX = 0, lastY = 0;
			window.addEventListener('devicemotion', function(e){
				let acceleration = e.accelerationIncludingGravity;
				x = acceleration.x;
				y = acceleration.y;
				if(Math.abs(x - lastX)>speed || Math.abs(y - lastY)>speed) callback();
				lastX = x;
				lastY = y;
			}, false);
		}
	},
	//仿iOS的UIActionSheet
	actionView: function(title, btns, e){
		return $('#app').children().not('.footer').actionView(title, btns, e);
	},
	//popView
	popoverView: function(e, target){
		return $('#app').children().not('.footer').popoverView(e, target);
	}
})

//时间戳转日期
String.prototype.toDate = function(formatStr){
	let number = this * 1;
	return number.toDate(formatStr);
};
Number.prototype.toDate = function(formatStr){
	let date = new Date(this * 1000);
	if(typeof formatStr === 'undefined'){
		return date;
	}else{
		return date.formatDate(formatStr);
	}
};

//日期转时间戳
String.prototype.time = function(){
	let date = this.date();
	return date.time();
};
Date.prototype.time = function(){
	return this.getTime()/1000;
};

//日期字符串转日期
String.prototype.date = function(){
	let m = /^(?:(\d{4})-(\d{1,2})(?:-(\d{1,2}))?)?(?: ?(\d{1,2}))?(?::(\d{1,2}))?(?::(\d{1,2}))?$/.exec(this);
	if(!m || !m.length)return null;
	let date = this.split(/\D/);
	if(m[1]){
		--date[1];
	}else{
		let d = new Date();
		date.unshift(String(d.getDate()));
		date.unshift(String(d.getMonth()));
		date.unshift(String(d.getFullYear()));
	}
	let count = date.length;
	for(let i=0; i<6-count; i++){
		date.push('0');
	}
	for(let i=0; i<date.length; i++){
		date[i] = (i === 2 && Number(date[i])<=0) ? 1 : Number(date[i]);
	}
	date = evil('new Date('+date.join(',')+')');
	return date;
};

//加上天数得到日期
String.prototype.dateAdd = function(t, number){
	let date = this.date();
	return date.dateAdd(t, number);
};
Date.prototype.dateAdd = function(t, number){
	number = parseInt(number);
	let date = this.Clone();
	switch (t) {
		case 's':return new Date(Date.parse(date) + (1000 * number));
		case 'n':return new Date(Date.parse(date) + (60000 * number));
		case 'h':return new Date(Date.parse(date) + (3600000 * number));
		case 'd':return new Date(Date.parse(date) + (86400000 * number));
		case 'w':return new Date(Date.parse(date) + ((86400000 * 7) * number));
		case 'q':return new Date(date.getFullYear(), (date.getMonth()) + number*3, date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds());
		case 'm':return new Date(date.getFullYear(), (date.getMonth()) + number, date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds());
		case 'y':return new Date((date.getFullYear() + number), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds());
	}
	return date;
};

//减去天数得到日期
String.prototype.dateDiff = function(t, number){
	let date = this.date();
	return date.dateDiff(t, number);
};
Date.prototype.dateDiff = function(t, number){
	let d = this.Clone(), k = { 'd':24*60*60*1000, 'h':60*60*1000, 'n':60*1000, 's':1000 };
	d = d.getTime();
	d = d - number * k[t];
	return new Date(d);
};

//两个日期的时间差
String.prototype.dateDiffNum = function(t, dtEnd){
	let date = this.date();
	return date.dateDiffNum(t, dtEnd);
};
Date.prototype.dateDiffNum = function(t, dtEnd){
	let dtStart = this.Clone();
	if(typeof dtEnd === 'string')dtEnd = dtEnd.date();
	switch(t){
		case 'y':return dtEnd.getFullYear() - dtStart.getFullYear();
		case 'm':return (dtEnd.getMonth() + 1) + ((dtEnd.getFullYear() - dtStart.getFullYear()) * 12) - (dtStart.getMonth() + 1);
		case 'd':return parseInt(String((dtEnd - dtStart) / 86400000));
		case 'w':return parseInt(String((dtEnd - dtStart) / (86400000 * 7)));
		case 'h':return parseInt(String((dtEnd - dtStart) / 3600000));
		case 'n':return parseInt(String((dtEnd - dtStart) / 60000));
		case 's':return parseInt(String((dtEnd - dtStart) / 1000));
	}
	return 0;
};

//日期格式化, callback:接受1个参数date(date为对象字面量,包含year, month, day, hour, minute, second, week)
String.prototype.formatDate = function(formatStr, callback){
	let date = this.date();
	return date.formatDate(formatStr, callback);
};
Number.prototype.formatDate = function(formatStr, callback){
	let date = this.toDate();
	return date.formatDate(formatStr, callback);
};
Date.prototype.formatDate = function(formatStr, callback){
	let format = formatStr ? formatStr : 'yyyy-mm-dd hh:nn:ss',
		monthName = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		monthFullName = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
		weekName = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		weekFullName = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		monthNameCn = ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
		monthFullNameCn = monthNameCn,
		weekNameCn = ['日', '一', '二', '三', '四', '五', '六'],
		weekFullNameCn = ['星期天', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
		getYearWeek = function(y, m, d){
			let dat = new Date(y, m, d), firstDay = new Date(y, 0, 1),
				day = Math.round((dat.valueOf()-firstDay.valueOf()) / 86400000);
			return Math.ceil( (day + ((firstDay.getDay()+1)-1)) / 7 );
		},
		year = this.getFullYear()+'', month = (this.getMonth()+1)+'', day = this.getDate()+'', week = this.getDay(),
		hour = this.getHours()+'', minute = this.getMinutes()+'', second = this.getSeconds()+'',
		yearWeek = getYearWeek(this.getFullYear(), this.getMonth(), this.getDate())+'';
	format = format.replace(/yyyy/g, year);
	format = format.replace(/yy/g, (this.getYear()%100)>9 ? (this.getYear()%100)+'' : '0'+(this.getYear()%100));
	format = format.replace(/Y/g, year);
	format = format.replace(/mme/g, monthFullName[month-1]);
	format = format.replace(/me/g, monthName[month-1]);
	format = format.replace(/mmc/g, monthFullNameCn[month-1]);
	format = format.replace(/mc/g, monthNameCn[month-1]);
	format = format.replace(/mm/g, month.length<2?'0'+month:month);
	format = format.replace(/m/g, month);
	format = format.replace(/dd/g, day.length<2?'0'+day:day);
	format = format.replace(/d/g, day);
	format = format.replace(/hh/g, hour.length<2?'0'+hour:hour);
	format = format.replace(/h/g, hour);
	format = format.replace(/H/g, hour);
	format = format.replace(/G/g, hour);
	format = format.replace(/nn/g, minute.length<2?'0'+minute:minute);
	format = format.replace(/n/g, minute);
	format = format.replace(/ii/g, minute.length<2?'0'+minute:minute);
	format = format.replace(/i/g, minute);
	format = format.replace(/ss/g, second.length<2?'0'+second:second);
	format = format.replace(/s/g, second);
	format = format.replace(/wwe/g, weekFullName[week]);
	format = format.replace(/we/g, weekName[week]);
	format = format.replace(/ww/g, weekFullNameCn[week]);
	format = format.replace(/w/g, weekNameCn[week]);
	format = format.replace(/WW/g, yearWeek.length<2?'0'+yearWeek:yearWeek);
	format = format.replace(/W/g, yearWeek);
	format = format.replace(/a/g, hour<12?'am':'pm');
	format = format.replace(/A/g, hour<12?'AM':'PM');
	if($.isFunction(callback))callback.call(this, {year:year, month:month, day:day, hour:hour, minute:minute, second:second, week:week});
	return format;
	//d.toLocaleDateString() 获取当前日期
	//d.toLocaleTimeString() 获取当前时间
	//d.toLocaleString() 获取日期与时间
};

//友好时间形式
String.prototype.timeWord = function(){
	let date = this.date();
	if(!date)return this;
	return date.timeWord();
};
Date.prototype.timeWord = function(){
	let date = this, d1 = date.getTime(), d2 = new Date().getTime(), between = Math.floor(d2/1000) - Math.floor(d1/1000);
	if(between < 60)return '刚刚';
	if(between < 3600)return Math.floor(between/60) + '分钟前';
	if(between < 86400)return Math.floor(between/3600) + '小时前';
	if(between <= 864000)return Math.floor(between/86400) + '天前';
	if(between > 864000)return this.formatDate('yyyy-mm-dd');
};

//日期复制
Date.prototype.Clone = function(){return new Date(this.valueOf())};

export default {
	$,
	filters
}