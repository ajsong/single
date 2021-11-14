/*
Developed by @mario
*/
const version = '13.3.20211112'

if(window.self === window.top)try{(window.console && window.console.log) && (console.log('%c Developed by %c @mario %c v'+version+' ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#999;padding:2px;color:#fff', 'background:#bbb;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Welcome to %c laokema.com ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#dc0431;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Username/Password %c test/test ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#ff9902;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c Wechat %c lwf000001 ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#41b883;padding:2px;border-radius:0 3px 3px 0;color:#fff'), console.log('%c QQ %c 172403414 ', 'background:#35495e;padding:2px;border-radius:3px 0 0 3px;color:#fff', 'background:#398bfc;padding:2px;border-radius:0 3px 3px 0;color:#fff'))}catch(e){}

const jCore = function(selector, context) {
	return new jCore.fn.init(selector, context)
}
jCore.fn = jCore.prototype = {
	//构造者标记
	constructor: jCore,
	//元素集
	selector: [],
	//元素数量
	length: 0,
	//jCore.fn.funtion 的 call者
	outer: null,
	//设置call者
	caller: function(caller) {
		this.outer = caller
		return this
	},
	//串联this.selector
	add: function(selector, context) {
		let elArray = this.selector
		jCore(selector, context).selector.forEach(item => {
			elArray.push(item)
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取索引指定原生元素
	get: function(index) {
		if (!this.selector.length) return this
		return (index < 0 || index >= this.selector.length) ? this.selector[this.selector.length-1] : this.selector[index]
	},
	//获取索引指定jCore元素
	eq: function(index) {
		if (!this.selector.length) return null
		jCore.setPrevSelector(this.selector)
		return (index < 0 || index >= this.selector.length) ? jCore(this.selector[this.selector.length-1]) : jCore(this.selector[index])
	},
	//选取一个匹配的子集, start为负数则可以从集合的尾部开始选起
	slice: function(start, end) {
		let arr = this.selector.slice(start, end)
		jCore.setPrevSelector(this.selector)
		return jCore(arr)
	},
	//返回对象所在索引
	index: function(selector) {
		let index = -1
		if (typeof selector === 'undefined') {
			let el = this.selector[0].parentNode.childNodes
			let children = []
			for (let i = 0; i < el.length; i++) {
				if (el[i].nodeType === 1) children.push(el[i])
			}
			for (let i = 0; i < children.length; i++) {
				if (children[i] === this.selector[0]) {
					index = i
					break
				}
			}
		} else {
			for (let i = 0; i < this.selector.length; i++) {
				if (this.selector[i] === selector) {
					index = i
					break
				}
			}
		}
		return index
	},
	//设置/获取缓存数据
	data: function(key, value) {
		if (typeof value === 'undefined' && typeof key !== 'object') {
			if (!this.selector.length) return null
			let itemIndex = jCore.inArray(this.selector[0], jCore.cacheKey)
			if (itemIndex > -1) {
				if (typeof key === 'undefined') {
					value = jCore.cacheValue[itemIndex].data
				} else {
					value = jCore.cacheValue[itemIndex].data[key.replace(/\s+/, '')]
				}
			} else {
				if (typeof key === 'undefined') {
					let arr = []
					for (let i = 0; i < this.selector[0].attributes.length; i++) {
						if (this.selector[0].attributes[i].nodeName.search(/^data-/) > -1) {
							if (this.selector[0].attributes[i].nodeValue.length) arr.push(this.selector[0].attributes[i].nodeValue)
						}
					}
					if (arr.length) value = arr
				} else {
					value = this.selector[0].getAttribute('data-' + key)
				}
			}
			return value
		} else {
			if (!this.selector.length) return this
			let obj = key
			if ( !(obj && typeof obj === 'object' && Object.prototype.toString.call(obj).toLowerCase() === '[object object]') ) {
				obj = {}
				obj[key.replace(/\s+/, '')] = value
			}
			this.selector.forEach(item => {
				for (let key in obj) {
					let itemIndex = jCore.inArray(item, jCore.cacheKey)
					if (itemIndex > -1) {
						jCore.cacheValue[itemIndex].data[key.replace(/\s+/, '')] = obj[key]
					} else {
						jCore.cacheKey.push(item)
						jCore.cacheValue.push({
							data: {
								[key.replace(/\s+/, '')]: obj[key]
							}
						})
						//let itemObj = { data:{} }
						//itemObj.data[key.replace(/\s+/, '')] = obj[key]
						//jCore.cacheValue.push(itemObj)
					}
				}
			})
			return this
		}
	},
	//删除缓存数据
	removeData: function(key) {
		if (!this.selector.length) return this
		this.selector.forEach(item => {
			let itemIndex = jCore.inArray(item, jCore.cacheKey)
			if (itemIndex > -1) {
				if (jCore.cacheValue[itemIndex].data.hasOwnProperty(key.replace(/\s+/, ''))) delete jCore.cacheValue[itemIndex].data[key.replace(/\s+/, '')]
				if (!Object.keys(jCore.cacheValue[itemIndex].data).length) {
					delete jCore.cacheKey[itemIndex]
					delete jCore.cacheValue[itemIndex]
				}
			}
		})
		return this
	},
	//判断是否指定元素
	is: function(selector) {
		if (!this.selector.length) return false
		if (typeof selector === 'undefined') return false
		if (typeof selector !== 'string') {
			for (let i = 0; i < this.selector.length; i++) {
				if (this.selector[i] === selector) return true
			}
			return false
		}
		let res = jCore.event.assignSelector(selector, this.selector, 1)
		return res
	},
	//过滤元素
	filter: function(selector) {
		if (!this.selector.length) return this
		if (typeof selector === 'undefined') return this
		let elArray = []
		this.selector.forEach(item => {
			if (jCore(item).is(selector)) elArray.push(item)
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//排除元素
	not: function(selector) {
		if (!this.selector.length) return this
		if (typeof selector === 'undefined') return this
		let elArray = []
		this.selector.forEach(item => {
			if (!jCore(item).is(selector)) elArray.push(item)
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//保留包含指定后代的元素
	has: function(selector) {
		if (!this.selector.length) return this
		if (typeof selector === 'undefined') return this
		let elArray = []
		this.selector.forEach(item => {
			if (jCore(item).find(selector).length) elArray.push(item)
		})
		return jCore(elArray)
	},
	//查找子元素
	children: function(selector) {
		if (!this.selector.length) return this
		if (typeof selector === 'undefined' || (typeof selector === 'string' && !selector.length)) {
			let children = []
			this.selector.forEach(item => {
				let el = item.childNodes
				for (let i = 0; i < el.length; i++) {
					if (el[i].nodeType === 1) children.push(el[i])
				}
			})
			return jCore(children)
		}
		if (typeof selector !== 'string') return this
		let elArray = []
		let items = selector.split(',')
		for (let t = 0; t < items.length; t++) {
			let parent = []
			let select = (jCore.trim(items[t]).split('>'))[0]
			select = (jCore.trim(select).split(/\s+/))[0]
			jCore.event.assignSelector(select, this.selector, 2, item => {
				if (item) {
					if (jCore.inArray(item, parent) < 0) parent.push(item)
				}
			})
			select = jCore.trim(items[t]).replace(new RegExp('^' + select.replace(/([\[\].-])/g, '\\$1')), '')
			if (jCore.trim(select)) {
				jCore.event.middleArror(select, parent).selector.forEach(item => {
					if (item) {
						if (jCore.inArray(item, elArray) < 0) elArray.push(item)
					}
				})
			} else {
				parent.forEach(item => {
					if (item) {
						if (jCore.inArray(item, elArray) < 0) elArray.push(item)
					}
				})
			}
		}
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//查找后代元素
	find: function(selector) {
		if (!this.selector.length) return this
		if (typeof selector === 'undefined' || (typeof selector === 'string' && !selector.length)) return this
		if (typeof selector !== 'string') return this
		let elArray = []
		let items = selector.split(',')
		for (let t = 0; t < items.length; t++) {
			let parent = []
			let select = (jCore.trim(items[t]).split('>'))[0]
			select = (jCore.trim(select).split(/\s+/))[0]
			jCore.event.assignSelector(select, this.selector, 3, item => {
				if (item) {
					if (jCore.inArray(item, parent) < 0) parent.push(item)
				}
			})
			console.log(select, this.selector)
			select = jCore.trim(items[t]).replace(new RegExp('^' + select.replace(/([\[\].-])/g, '\\$1')), '')
			if (jCore.trim(select)) {
				jCore.event.middleArror(select, parent).selector.forEach(item => {
					if (item) {
						if (jCore.inArray(item, elArray) < 0) elArray.push(item)
					}
				})
			} else {
				parent.forEach(item => {
					if (item) {
						if (jCore.inArray(item, elArray) < 0) elArray.push(item)
					}
				})
			}
		}
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取父元素
	parent: function(selector) {
		if (!this.selector.length) return this
		let elArray = []
		this.selector.forEach(item => {
			if (typeof selector === 'undefined') {
				if (item.parentNode) elArray.push(item.parentNode)
			} else {
				let parent = jCore(selector).selector
				for (let i = 0; i < parent.length; i++) {
					if (item.parentNode === parent[i]) {
						elArray.push(item.parentNode)
						break
					}
				}
			}
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取所有父辈元素
	parents: function(selector) {
		if (!this.selector.length) return this
		let elArray = []
		let isFind = false
		for (let i = 0; i < this.selector.length; i++) {
			let item = this.selector[i].parentNode
			do {
				elArray.push(item)
				if (typeof selector !== 'undefined') {
					if (jCore(item).is(selector)) {
						isFind = true
						break
					}
				}
				if ((/^(html|body)$/i).test(item.tagName)) break
				item = item.parentNode
			} while(item.parentNode)
		}
		if (!isFind && typeof selector !== 'undefined') elArray = []
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取所有父辈元素, 不包含selector
	parentsUntil: function(selector) {
		if (!this.selector.length) return this
		let items = this.parents(selector)
		if (typeof selector !== 'undefined' && items.selector.length) items.selector.pop()
		return items
	},
	//把selector添加到匹配元素
	append: function(selector) {
		if (!this.selector.length) return this
		if (typeof selector === 'function') selector = selector()
		this.selector[0].appendChild(jCore(selector).selector[0])
		jCore.onEventsNewElement(this.selector[0])
		return this
	},
	//把匹配元素添加到selector
	appendTo: function(selector) {
		if (!this.selector.length) return this
		jCore(selector).selector[0].appendChild(this.selector[0])
		jCore.onEventsNewElement(jCore(selector).selector[0])
		return this
	},
	//把selector添加到匹配元素的第一个子元素的前面
	prepend: function(selector) {
		if (!this.selector.length) return this
		if (typeof selector === 'function') selector = selector()
		if (this.selector[0].firstChild) this.selector[0].insertBefore(jCore(selector).selector[0], this.selector[0].firstChild)
		else this.selector[0].appendChild(jCore(selector).selector[0])
		jCore.onEventsNewElement(this.selector[0])
		return this
	},
	//把匹配元素添加到selector的第一个子元素的前面
	prependTo: function(selector) {
		if (!this.selector.length) return this
		if (jCore(selector).selector[0].firstChild) jCore(selector).selector[0].insertBefore(this.selector[0], jCore(selector).selector[0].firstChild)
		else jCore(selector).selector[0].appendChild(this.selector[0])
		jCore.onEventsNewElement(jCore(selector).selector[0])
		return this
	},
	//把selector添加到匹配元素的前面
	before: function(selector) {
		if (!this.selector.length) return this
		if (!this.selector[0].parentNode) return this
		if (typeof selector === 'function') selector = selector()
		this.selector[0].parentNode.insertBefore(jCore(selector).selector[0], this.selector[0])
		jCore.onEventsNewElement(this.selector[0].parentNode)
		return this
	},
	//把selector添加到匹配元素的后面
	after: function(selector) {
		if (!this.selector.length) return this
		if (!this.selector[0].parentNode) return this
		if (typeof selector === 'function') selector = selector()
		if (this.next().length) this.selector[0].parentNode.insertBefore(jCore(selector).selector[0], this.next())
		else this.selector[0].parentNode.appendChild(jCore(selector).selector[0])
		jCore.onEventsNewElement(this.selector[0].parentNode)
		return this
	},
	//把匹配元素添加到selector的前面
	insertBefore: function(selector) {
		if (!this.selector.length) return this
		selector = jCore(selector)
		if (!selector.selector[0].parentNode) return this
		this.selector.forEach(item => {
			selector.selector[0].parentNode.insertBefore(item, selector.selector[0])
		})
		jCore.onEventsNewElement(selector.selector[0])
		return this
	},
	//把匹配元素添加到selector的后面
	insertAfter: function(selector) {
		if (!this.selector.length) return this
		selector = jCore(selector)
		if (!selector.selector[0].parentNode) return this
		this.selector.forEach(item => {
			if (selector.next().length) selector.selector[0].parentNode.insertBefore(item, selector.next())
			else selector.selector[0].parentNode.appendChild(item)
		})
		jCore.onEventsNewElement(selector.selector[0])
		return this
	},
	//使用元素包裹
	wrap: function(newElement) {
		if (!this.selector.length) return this
		newElement = jCore(newElement)
		if (!newElement.parent().length) {
			this.before(newElement)
			newElement = this.prev().eq(0)
		}
		newElement.append(this.selector[0])
		jCore.onEventsNewElement(newElement.selector[0].parentNode)
		return this
	},
	//使用元素包裹全部
	wrapAll: function(newElement) {
		if (!this.selector.length) return this
		newElement = jCore(newElement)
		if (!newElement.parent().length) {
			this.before(newElement)
			newElement = this.prev().eq(0)
		}
		this.selector.forEach(item => newElement.append(item))
		jCore.onEventsNewElement(newElement.selector[0].parentNode)
		return this
	},
	//把子内容(包括文本节点)用HTML结构包裹起来
	wrapInner: function(newElement) {
		if (!this.selector.length) return this
		if (typeof newElement === 'undefined') return this
		this.selector.forEach(item => {
			let tmpParent = document.createElement('div')
			let el = Array.prototype.slice.call(item.childNodes)
			for (let i = 0; i < el.length; i++) {
				let parent = newElement
				if (typeof newElement === 'string') {
					parent = jCore(newElement).selector[0]
				} else if (typeof newElement === 'function') {
					parent = newElement()
				}
				parent.appendChild(el[i])
				tmpParent.appendChild(parent)
			}
			item.innerHTML = ''
			el = Array.prototype.slice.call(tmpParent.childNodes)
			for (let i = 0; i < el.length; i++) {
				item.appendChild(el[i])
			}
			jCore.onEventsNewElement(item)
		})
		return this
	},
	//移除父元素
	unwrap: function() {
		if (!this.selector.length) return jCore()
		let selectorParent = []
		let selectorEl = []
		this.selector.forEach(item => {
			let el = item.parentNode
			if (jCore.inArray(el, selectorParent) < 0) {
				selectorEl.push(item)
				selectorParent.push(el)
			}
		})
		selectorEl.forEach(item => {
			let parent = item.parentNode
			let el = parent.childNodes
			for (let i = 0; i < el.length; i++) {
				if (el[i].nodeType === 1) parent.parentNode.insertBefore(el[i], parent)
			}
			jCore.removeEvents(parent)
			parent.parentNode.removeChild(parent)
		})
		return this
	},
	//匹配元素替换成新元素
	replaceWith: function(newElement) {
		if (typeof newElement === 'undefined') return this
		if (!this.selector.length) return this
		if (typeof newElement === 'function') newElement = newElement()
		let elArray = []
		this.selector.forEach(item => {
			let parentNode = item.parentNode
			let el = jCore(newElement).selector
			jCore.removeEventsNewElement(item)
			item.parentNode.insertBefore(el, item)
			item.parentNode.removeChild(item)
			jCore.onEventsNewElement(parentNode)
			elArray.push(el)
		})
		jCore.setPrevSelector([])
		return jCore(elArray)
	},
	//使用新元素替换匹配元素
	replaceAll: function(selector) {
		if (typeof selector === 'undefined') return this
		if (!this.selector.length) return this
		let html = this.selector[0].outerHTML
		jCore(selector).selector.forEach(item => {
			let parentNode = item.parentNode
			let el = jCore(html).selector
			jCore.removeEventsNewElement(item)
			item.parentNode.insertBefore(el, item)
			item.parentNode.removeChild(item)
			jCore.onEventsNewElement(parentNode)
			elArray.push(el)
		})
		jCore.setPrevSelector([])
		return jCore(elArray)
	},
	//移除元素
	remove: function() {
		if (!this.selector.length) return jCore()
		this.selector.forEach(item => {
			if (!item.parentNode) return
			jCore.removeEvents(item)
			jCore.removeEventsNewElement(item)
			item.parentNode.removeChild(item)
		})
		jCore.setPrevSelector([])
		return jCore()
	},
	//移除所有子元素
	empty: function() {
		if (!this.selector.length) return this
		this.selector.forEach(item => {
			let el = item.childNodes
			for (let i = 0; i < el.length; i++) {
				if (el[i].nodeType === 1) {
					jCore.removeEvents(el[i])
					jCore.removeEventsNewElement(el[i])
				}
			}
			item.innerHTML = ''
		})
		return this
	},
	//获取匹配元素的第一个
	first: function() {
		if (!this.selector.length) return this
		jCore.setPrevSelector(this.selector)
		return jCore(this.selector[0])
	},
	//获取匹配元素的最后一个
	last: function() {
		if (!this.selector.length) return this
		jCore.setPrevSelector(this.selector)
		return jCore(this.selector[this.selector.length-1])
	},
	//获取第一个子元素
	firstChild: function() {
		if (!this.selector.length) return this
		let elArray = []
		this.selector.forEach(item => {
			let el = item.firstChild
			while (el !== null && el.nodeType !== 1) el = el.nextSibling
			elArray.push(el)
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取最后一个子元素
	lastChild: function() {
		if (!this.selector.length) return this
		let elArray = []
		this.selector.forEach(item => {
			let el = item.lastChild
			while (el !== null && el.nodeType !== 1) el = el.previousSibling
			elArray.push(el)
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取上一个同辈元素
	prev: function(selector) {
		if (!this.selector.length) return this
		let elArray = []
		this.selector.forEach(item => {
			let el = item.previousSibling
			while (el !== null && el.nodeType !== 1) el = el.previousSibling
			if (typeof selector === 'undefined') elArray.push(el)
			else if (jCore(el).is(selector)) elArray.push(el)
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取元素之前的所有同辈元素
	prevAll: function(selector) {
		if (!this.selector.length) return this
		let elArray = []
		this.selector.forEach(item => {
			let isMe = false
			let el = item.parentNode.childNodes
			for (let i = el.length - 1; i >= 0; i--) {
				if (el[i].nodeType === 1) {
					if (!isMe && el[i] !== item) continue
					if (el[i] === item) {
						isMe = true
						continue
					}
					if (typeof selector === 'undefined') elArray.push(el[i])
					else if (jCore(el[i]).is(selector)) elArray.push(el[i])
				}
			}
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取元素之前的所有同辈元素, 不包含selector
	prevUntil: function(selector) {
		if (!this.selector.length) return this
		let items = this.prevAll(selector)
		if (typeof selector !== 'undefined' && items.selector.length) items.selector.pop()
		return items
	},
	//获取下一个同辈元素
	next: function(selector) {
		if (!this.selector.length) return this
		let elArray = []
		this.selector.forEach(item => {
			let el = item.nextSibling
			while (el !== null && el.nodeType !== 1) el = el.nextSibling
			if (typeof selector === 'undefined') elArray.push(el)
			else if (jCore(el).is(selector)) elArray.push(el)
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取元素之后的所有同辈元素
	nextAll: function(selector) {
		if (!this.selector.length) return this
		let elArray = []
		this.selector.forEach(item => {
			let isMe = false
			let el = item.parentNode.childNodes
			for (let i = 0; i < el.length; i++) {
				if (el[i].nodeType === 1) {
					if (!isMe && el[i] !== item) continue
					if (el[i] === item) {
						isMe = true
						continue
					}
					if (typeof selector === 'undefined') elArray.push(el[i])
					else if (jCore(el[i]).is(selector)) elArray.push(el[i])
				}
			}
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//获取元素之后的所有同辈元素, 不包含selector
	nextUntil: function(selector) {
		if (!this.selector.length) return this
		let items = this.nextAll(selector)
		if (typeof selector !== 'undefined' && items.selector.length) items.selector.pop()
		return items
	},
	//获取所有同辈元素
	siblings: function(selector) {
		if (!this.selector.length) return this
		let elArray = []
		this.selector.forEach(item => {
			let el = item.parentNode.childNodes
			let children = []
			for (let i = 0; i < el.length; i++) {
				if (el[i].nodeType === 1) children.push(el[i])
			}
			if (typeof selector === 'undefined') {
				elArray = children
			} else {
				for (let i = 0; i < children.length; i++) {
					if (children[i] !== item && jCore(children[i]).is(selector)) elArray.push(children[i])
				}
			}
		})
		jCore.setPrevSelector(this.selector)
		return jCore(elArray)
	},
	//是否存在className
	hasClass: function(name) {
		if (!this.selector.length) return false
		if (typeof name !== 'string' || !name.length) return false
		if (typeof this.selector[0].className === 'undefined') return false
		return new RegExp('\\b(' + name + ')\\b', 'ig').test(this.selector[0].className)
	},
	//添加className
	addClass: function(name) {
		if (!this.selector.length) return this
		if (typeof name !== 'string' || !name.length) return this
		this.selector.forEach(item => {
			let arr = []
			if (typeof name === 'function') {
				name = name.call(item, this.index(item), item.className)
			}
			arr = (name instanceof Array) ? name : name.split(/\s+/)
			let className = item.className
			arr.forEach(n => {
				if (!new RegExp('\\b(' + n + ')\\b', 'ig').test(className)) className += (className.length ? ' ' : '') + n
			})
			item.className = jCore.trim(className).replace(/\s+/, ' ')
		})
		return this
	},
	//删除className
	removeClass: function(name) {
		if (!this.selector.length) return this
		if (typeof name !== 'string' || !name.length) return this
		this.selector.forEach(item => {
			let arr = []
			if (typeof name === 'function') {
				name = name.call(item, this.index(item), item.className)
			}
			arr = (name instanceof Array) ? name : name.split(/\s+/)
			let className = item.className
			arr.forEach(n =>{
				className = className.replace(new RegExp('\\b(' + n + ')\\b', 'ig'), '')
			})
			if (className.length) {
				item.className = jCore.trim(className).replace(/\s+/, ' ')
			} else {
				item.removeAttribute('class')
			}
		})
		return this
	},
	//添加/删除className,有则删除,无则添加
	toggleClass: function(name) {
		if (!this.selector.length) return this
		if (typeof name !== 'string' || !name.length) return this
		this.selector.forEach(item => {
			if (jCore(item).hasClass(name)) {
				jCore(item).removeClass(name)
			} else {
				jCore(item).addClass(name)
			}
		})
		return this
	},
	//获取/设置html内容
	html: function(html) {
		if (!this.selector.length) return this
		if (typeof html === 'undefined') return this.selector[0].innerHTML
		this.selector.forEach(item => {
			let res = (html instanceof Function) ? html.call(item) : html
			if (typeof res === 'string' && !res.length) {
				let el = item.childNodes
				for (let i = 0; i < el.length; i++) {
					if (el[i].nodeType === 1) {
						jCore.removeEvents(el[i])
						jCore.removeEventsNewElement(el[i])
					}
				}
			}
			item.innerHTML = res
			if (res) {
				jCore.onEventsNewElement(item)
			}
		})
		return this
	},
	//获取/设置value值
	val: function(value) {
		if (!this.selector.length) return this
		if (typeof value === 'undefined') return this.selector[0].value
		this.selector.forEach(item => {
			if (item.tagName.toLowerCase() === 'select') {
				let val = value
				if (value instanceof Function) val = value.call(item)
				let option = jCore(item).find('option')
				for (let i = 0; i < option.length; i++) {
					if (option.selector[i].getAttribute('value') === val) {
						option.selector[i].setAttribute('selected', 'selected')
						break
					}
				}
			} else {
				item.value = (value instanceof Function) ? value.call(item) : value
			}
		})
		return this
	},
	//获取/设置内部字符串
	text: function(text) {
		if (!this.selector.length) return this
		if (typeof text === 'undefined') return this.selector[0].innerText || this.selector[0].textContent
		this.selector.forEach(item => {
			item.innerHTML = (text instanceof Function) ? text.call(item) : text
		})
		return this
	},
	//获取outerHTML
	outerHTML: function() {
		return this.selector[0].outerHTML
	},
	//修复IE的设置html代码不执行script脚本的问题
	evalScripts: function() {
		if (!this.selector.length) return this
		let hasParent = true
		if (!this.selector[0].parentNode) {
			hasParent = false
			document.body.appendChild(this.selector[0])
		}
		let html = this.selector[0].innerHTML
		if (jCore.browser.ie) {
			html = '<div style="display:none">for IE</div>' + html
			html = html.replace(/<script([^>]*)>/ig, '<script$1 defer="true">')
			this.selector[0].innerHTML = html.substring(38)
		}
		if (!hasParent) this.selector[0].parentNode.removeChild(this.selector[0])
	},
	//获取/设置宽度
	width: function(num) {
		if (typeof num === 'undefined') {
			if (!this.selector.length) return 0
			let width = this.selector[0].offsetWidth, padding = jCore(this.selector[0]).padding(), border = jCore(this.selector[0]).border()
			return width - padding.left - padding.right - border.left - border.right
		}
		this.selector.forEach(item => {
			item.style.width = ((num+'').indexOf('%') > -1 ? (item.parentNode.clientWidth * (parseFloat((num+'').replace('%', '')) / 100)) : num) + 'px'
		})
		return this
	},
	//获取/设置高度
	height: function(num) {
		if (typeof num === 'undefined') {
			if (!this.selector.length) return 0
			let height = this.selector[0].offsetHeight, padding = jCore(this.selector[0]).padding(), border = jCore(this.selector[0]).border()
			return height - padding.top - padding.bottom - border.top - border.bottom
		}
		this.selector.forEach(item => {
			item.style.height = ((num+'').indexOf('%') > -1 ? (item.parentNode.clientHeight * (parseFloat((num+'').replace('%', '')) / 100)) : num) + 'px'
		})
		return this
	},
	//获取内宽度
	innerWidth: function() {
		if (!this.selector.length) return 0
		let width = this.selector[0].offsetWidth, border = jCore(this.selector[0]).border()
		return width - border.left - border.right
	},
	//获取内高度
	innerHeight: function() {
		if (!this.selector.length) return 0
		let height = this.selector[0].offsetHeight, border = jCore(this.selector[0]).border()
		return height - border.top - border.bottom
	},
	//获取外宽度
	outerWidth: function(includeMargin) {
		if (!this.selector.length) return 0
		let width = this.selector[0].offsetWidth
		if (includeMargin) {
			let margin = jCore(this.selector[0]).margin()
			width += margin.left + margin.right
		}
		return width
	},
	//获取外高度
	outerHeight: function(includeMargin) {
		if (!this.selector.length) return 0
		let height = this.selector[0].offsetHeight
		if (includeMargin) {
			let margin = jCore(this.selector[0]).margin()
			height += margin.top + margin.bottom
		}
		return height
	},
	//获取/设置属性
	attr: function(name, value) {
		if (!this.selector.length) return this
		if (typeof name === 'string' && !name.length) return this
		if (typeof name === 'string' && typeof value === 'undefined') {
			//o.attributes['value'].nodeName; //返回属性的名称
			//o.attributes[0].nodeValue; //返回第一个属性的值
			return this.selector[0].getAttribute(name)
		}
		let arg = name
		if ( !(arg && typeof arg === 'object' && Object.prototype.toString.call(arg).toLowerCase() === '[object object]') ) {
			arg = {}
			arg[name.replace(/\s+/, '')] = value
		}
		this.selector.forEach(item => {
			for (let key in arg) {
				value = arg[key]
				if (typeof value === 'function') value = value.call(item, jCore(item).index(), item.getAttribute(key))
				item.setAttribute(key, value)
			}
		})
		return this
	},
	//删除属性
	removeAttr: function(name) {
		if (!this.selector.length) return this
		if (typeof name !== 'string' || !name.length) return this
		this.selector.forEach(item => {
			item.removeAttribute(name)
		})
		return this
	},
	//获取/设置style
	css: function(arg, value) {
		const currentStyle = (el) => {return el.currentStyle || document.defaultView.getComputedStyle(el, null)}
		if (typeof arg === 'undefined') {
			if (!this.selector.length) return []
			let arr = []
			for (let props in currentStyle(this.selector[0])) arr.push([props, currentStyle(this.selector[0])[props]])
			return arr
		} else {
			let prop
			let pxItems = ('width height top left right bottom font-size line-height border-radius ' +
				'margin margin-top margin-left margin-right margin-bottom padding padding-top padding-left padding-right padding-bottom ' +
				'border-width border-top-width border-left-width border-right-width border-bottom-width ' +
				'border-radius border-top-left-radius border-top-right-radius border-bottom-right-radius border-bottom-left-radius').split(' ')
			let setPx = (item, value) => value + ((!isNaN(value) && jCore.inArray(item, pxItems) > -1) ? 'px' : '')
			if (typeof arg === 'string') {
				let items = arg.split(';'), item = arg.split(':')
				if (items.length === 1 && item.length === 1 && typeof value === 'undefined') {
					if (!this.selector.length) return ''
					prop = arg.replace(/-([a-z])/ig, (d, m) => {return m.toUpperCase()})
					return currentStyle(this.selector[0])[prop]
				} else {
					this.selector.forEach(item => {
						for (let i = 0; i < items.length; i++) {
							if (jCore.trim(items[i]).length) {
								if (typeof value === 'undefined') {
									let _item = items[i].split(':')
									if (jCore.trim(_item[0]) === 'opacity') {
										prop = isNaN(jCore.trim(_item[1])) ? '' : jCore.trim(_item[1])
										item.style.opacity = prop
										if (jCore.browser.ie) item.style.filter = !prop ? '' : 'alpha(opacity=' + (prop * 100) + ')'
									} else {
										prop = jCore.trim(_item[0]).replace(/-([a-z])/ig, (d, m) => {return m.toUpperCase()})
										item.style[prop] = setPx(jCore.trim(_item[0]), jCore.trim(_item[1]))
									}
								} else {
									if (typeof value === 'function') value = value.call(item, this.index(item), jCore(item).css(items[i]))
									item.style[items[i]] = setPx(jCore.trim(items[i]), jCore.trim(value))
								}
							}
						}
					})
				}
			} else if (arg && typeof arg === 'object' && Object.prototype.toString.call(arg).toLowerCase() === '[object object]') {
				this.selector.forEach(item => {
					Object.keys(arg).forEach(key => {
						key = jCore.trim(key)
						if (key === 'opacity') {
							prop = isNaN(arg[key]) ? '' : arg[key]
							item.style.opacity = prop
							if (jCore.browser.ie) item.style.filter = !prop ? '' : 'alpha(opacity=' + (prop * 100) + ')'
						} else {
							prop = key.replace(/-([a-z])/ig, (d, m) => {return m.toUpperCase()})
							item.style[prop] = setPx(key, jCore.trim(arg[key]))
						}
					})
				})
			}
			return this
		}
	},
	//显示
	show: function(speed, callback) {
		if (typeof speed === 'undefined') speed = null
		return this.fadeIn(speed, callback)
	},
	//隐藏
	hide: function(speed, callback) {
		if (typeof speed === 'undefined') speed = null
		return this.fadeOut(speed, callback)
	},
	//获取匹配元素在当前视口的相对偏移
	offset: function(coordinates) {
		if (!this.selector.length) return { top: 0, left: 0 }
		if (typeof coordinates !== 'undefined') {
			this.selector.forEach(item => {
				let res = coordinates
				if (typeof coordinates === 'function') res = res.call(item, this.index(item), jCore(item).offset())
				item.style.top = res.top
				item.style.left = res.left
			})
			return this
		}
		let o = this.selector[0], left = o.offsetLeft, top = o.offsetTop
		if (jCore(o).css('display') === 'none') return { top: 0, left: 0 }
		while ((o = o.offsetParent)) {
			left += o.offsetLeft || 0
			top += o.offsetTop || 0
		}
		return { top: top, left: left }
	},
	//获取匹配元素相对父元素的偏移
	position: function() {
		if (!this.selector.length) return { top: 0, left: 0 }
		let o = this.selector[0], left = o.offsetLeft, top = o.offsetTop
		if (jCore(o).css('display') === 'none') return { top: 0, left: 0 }
		while ((o = o.offsetParent)) {
			if (jCore(o).css('position') !== 'initial' && jCore(o).css('position') !== 'static') break
			left += o.offsetLeft || 0
			top += o.offsetTop || 0
		}
		return { top: top, left: left }
	},
	//获取/设置scrollTop
	scrollTop: function(num) {
		if (typeof num !== 'undefined') {
			this.selector.forEach(item => {
				item.scrollTop = num
			})
			return this
		} else {
			if (!this.selector.length) return 0
			return this.selector[0].scrollTop
		}
	},
	//获取/设置scrollLeft
	scrollLeft: function(num) {
		if (typeof num !== 'undefined') {
			this.selector.forEach(item => {
				item.scrollLeft = num
			})
			return this
		} else {
			if (!this.selector.length) return 0
			return this.selector[0].scrollLeft
		}
	},
	//获取填充
	padding: function() {
		if (!this.selector.length) return { top: 0, left: 0, bottom: 0, right: 0 }
		let el = jCore(this.selector[0]),
			top = (Number(el.css('padding-top').replace(/px/,''))||0), left = (Number(el.css('padding-left').replace(/px/,''))||0),
			bottom = (Number(el.css('padding-bottom').replace(/px/,''))||0), right = (Number(el.css('padding-right').replace(/px/,''))||0)
		return { top: top, left: left, bottom: bottom, right: right }
	},
	//获取间距
	margin: function() {
		if (!this.selector.length) return { top: 0, left: 0, bottom: 0, right: 0 }
		let el = jCore(this.selector[0]),
			top = (Number(el.css('margin-top').replace(/px/,''))||0), left = (Number(el.css('margin-left').replace(/px/,''))||0),
			bottom = (Number(el.css('margin-bottom').replace(/px/,''))||0), right = (Number(el.css('margin-right').replace(/px/,''))||0)
		return { top: top, left: left, bottom: bottom, right: right }
	},
	//获取边宽
	border: function() {
		if (!this.selector.length) return { top: 0, left: 0, bottom: 0, right: 0 }
		let el = jCore(this.selector[0]),
			top = (Number(el.css('border-top-width').replace(/px/,''))||0), left = (Number(el.css('border-left-width').replace(/px/,''))||0),
			bottom = (Number(el.css('border-bottom-width').replace(/px/,''))||0), right = (Number(el.css('border-right-width').replace(/px/,''))||0)
		return { top: top, left: left, bottom: bottom, right: right }
	},
	//获取transform
	transform: function() {
		if (!this.selector.length) return { scale: 0, rotate: 0, translate: { x: 0, y: 0 } }
		if (this.css('transform') === 'none') return { scale: 0, rotate: 0, translate: { x: 0, y: 0 } }
		let matcher = this.css('transform').split('(')[1].split(')')[0].split(',')
		let scale = Math.sqrt(parseFloat(matcher[0]) * parseFloat(matcher[0]) + parseFloat(matcher[1]) * parseFloat(matcher[1]))
		let rotate = Math.round(Math.atan2(parseFloat(matcher[1]), parseFloat(matcher[0])) * (180 / Math.PI))
		let translate = { x: parseFloat(matcher[4]), y: parseFloat(matcher[5]) }
		return { scale: parseFloat(scale), rotate: parseFloat(rotate), translate: translate }
	},
	//创建动画
	animate: function(params, speed, easing, callback) {
		if (!this.selector.length) return this
		if (typeof easing === 'function' && typeof callback === 'undefined') {
			callback = easing
			easing = 'ease-out'
		}
		this.selector.forEach(item => {
			item.style.transition = 'all ' + speed + 'ms ' + easing
			Object.keys(params).forEach(key => {
				item.style[key] = params[key]
			})
			if (typeof callback === 'function') {
				setTimeout(() => {
					item.style.transition = ''
					callback.call(item)
				}, speed + 1)
			}
		})
		return this
	},
	//匹配元素淡入
	fadeIn: function(speed, callback) {
		if (typeof speed === 'undefined') speed = 400
		if (typeof speed === 'string') {
			if (speed === 'slow') speed = 600
			else if (speed === 'fast') speed = 200
			else speed = 400
		}
		if (speed) {
			this.selector.forEach(item => {
				item.style.opacity = 0
				item.style.display = 'inherit'
			})
			setTimeout(() => {
				this.animate({ opacity: 1 }, speed, function() {
					if (typeof callback !== 'undefined') callback.call(this)
				})
			}, 0)
		} else {
			this.selector.forEach(item => {
				item.style.display = 'inherit'
			})
		}
		return this
	},
	//匹配元素淡出
	fadeOut: function(speed, callback) {
		if (typeof speed === 'undefined') speed = 400
		if (typeof speed === 'string') {
			if (speed === 'slow') speed = 600
			else if (speed === 'fast') speed = 200
			else speed = 400
		}
		if (speed) {
			this.animate({ opacity: 0 }, speed, function() {
				this.style.display = 'none'
				if (typeof callback !== 'undefined') callback.call(this)
			})
		} else {
			this.selector.forEach(item => {
				item.style.display = 'none'
			})
		}
		return this
	},
	//向下增大元素高度
	slideDown: function(speed, callback) {
		if (typeof speed === 'undefined') speed = 400
		if (typeof speed === 'string') {
			if (speed === 'slow') speed = 600
			else if (speed === 'fast') speed = 200
			else speed = 400
		}
		this.css({ overflow: 'hidden', height: 0 })
		setTimeout(() => {
			this.animate({ height: '' }, speed, function() {
				this.style.overflow = ''
				if (typeof callback !== 'undefined') callback.call(this)
			})
		}, 0)
	},
	//向上减少元素高度
	slideUp: function(speed, callback) {
		if (typeof speed === 'undefined') speed = 400
		if (typeof speed === 'string') {
			if (speed === 'slow') speed = 600
			else if (speed === 'fast') speed = 200
			else speed = 400
		}
		this.css({ overflow: 'hidden' }).animate({ height: 0 }, speed, function() {
			if (typeof callback !== 'undefined') callback.call(this)
		})
	},
	//注册事件
	on: function(event, selector, callback) {
		if (!this.selector.length) return this
		if (typeof selector === 'function') {
			callback = selector
			selector = null
		}
		if (typeof callback !== 'function') return this
		let caller = this
		let handler = this.selector
		if (selector) {
			if (typeof selector === 'string') {
				this.selector.forEach(parent => {
					jCore.setEventsNewElement(parent, selector, event, callback)
				})
			}
			caller = this.find(selector)
			handler = caller.selector
		}
		handler.forEach(item => {
			let eventFn
			let el = jCore(item)
			if (event === 'tap') {
				let doc = jCore(document.body),
					isTouch = 'ontouchend' in document.createElement('div'),
					start = isTouch ? 'touchstart' : 'mousedown',
					move = isTouch ? 'touchmove' : 'mousemove',
					end = isTouch ? 'touchend' : 'mouseup',
					cancel = isTouch ? 'touchcancel' : 'mouseout'
				let i = { target: item },
					onStart = (e) => {
						let p = jCore.browser.mobile ? ((('touches' in e) && e.touches) ? e.touches[0] : (isTouch ? window.event.touches[0] : window.event)) : e
						i.startX = p.clientX || 0
						i.startY = p.clientY || 0
						i.endX = p.clientX || 0
						i.endY = p.clientY || 0
						i.startTime = + new Date
						doc.on(move, onMove)
					},
					onMove = (e) => {
						let p = jCore.browser.mobile ? ((('touches' in e) && e.touches) ? e.touches[0] : (isTouch ? window.event.touches[0] : window.event)) : e
						i.endX = p.clientX
						i.endY = p.clientY
					},
					onEnd = (e) => {
						doc.off(move, onMove)
						if ((+ new Date) - i.startTime < 300) {
							if (Math.abs(i.endX - i.startX) + Math.abs(i.endY - i.startY) < 20) {
								e = e || window.event
								e.preventDefault()
								callback = el.data('tap.callback')
								let res = callback.call(i.target, e)
								if (typeof res === 'boolean' && !res) e.preventDefault()
							}
						}
						i = { target: i.target }
					}
				el.on(start, onStart).on(end, onEnd).on(cancel, onEnd).data({ 'tap.callback': callback, 'tap.start': onStart, 'tap.move': onMove, 'tap.end': onEnd })
			} else if (event === 'scrollstart') {
				eventFn = (e) => {
					if (el.getAttribute('scrollstart')) return
					el.setAttribute('scrollstart', 'scrollstart')
					let res = callback.call(item, e)
					if (typeof res === 'boolean' && !res) e.preventDefault()
				}
				if (window.addEventListener) {
					item.addEventListener('scroll', eventFn, false)
				} else if (window.attachEvent) {
					item.attachEvent('onscroll', eventFn)
				}
			} else if (event === 'scrollstop') {
				let touchstart = () => item.setAttribute('scrollstop', 'scrollstop'),
					touchend = (e) => {
						item.removeAttribute('scrollstop')
						if (!item.getAttribute('skip-scrollstop')) scroll(e)
					},
					scroll = (e) => {
						if (item.getAttribute('skip-scrollstop')) return
						let timer = el.data('scrollstop-timer')
						if (timer) clearTimeout(timer)
						if (item.getAttribute('scrollstop')) return
						timer = setTimeout(() => {
							clearTimeout(timer)
							item.removeAttribute('scrollstart')
							item.removeAttribute('scrollstop')
							let res = callback.call(item, e)
							if (typeof res === 'boolean' && !res) e.preventDefault()
						}, 300)
						el.data('scrollstop-timer', timer)
					}
				if (window.addEventListener) {
					item.addEventListener('touchstart', touchstart, false)
					item.addEventListener('touchend', touchend, false)
					item.addEventListener('scroll', scroll, false)
				} else if (window.attachEvent) {
					item.attachEvent('ontouchstart', touchstart)
					item.attachEvent('ontouchend', touchend)
					item.attachEvent('onscroll', scroll)
				}
				eventFn = {
					touchstart: touchstart,
					touchend: touchend,
					scroll: scroll
				}
			} else {
				eventFn = (e) => {
					let res = callback.call(item, e)
					if (typeof res === 'boolean' && !res) e.preventDefault()
				}
				el.data(event + '-listener', eventFn)
				if (window.addEventListener) {
					item.addEventListener(event, eventFn, false)
				} else if (window.attachEvent) {
					item.attachEvent('on' + event, eventFn)
				}
			}
			jCore.setEvents(item, event, eventFn)
		})
		return caller
	},
	//解除事件
	off: function(event, selector, callback) {
		if (!this.selector.length) return this
		if (typeof selector === 'function') {
			callback = selector
			selector = null
		}
		let caller = this
		let handler = this.selector
		if (selector) {
			if (typeof selector === 'string') {
				this.selector.forEach(parent => {
					jCore.removeEventsNewElement(parent, selector, event)
				})
			}
			caller = this.find(selector)
			handler = caller.selector
		}
		handler.forEach(item => {
			let el = jCore(item)
			if (typeof event === 'undefined') {
				jCore.eventsArray.forEach(e => {
					el.off(e, jCore.events(item, e))
				})
				return
			}
			if (event === 'tap') {
				let doc = jCore(document.body),
					isTouch = 'ontouchend' in document.createElement('div'),
					start = isTouch ? 'touchstart' : 'mousedown',
					move = isTouch ? 'touchmove' : 'mousemove',
					end = isTouch ? 'touchend' : 'mouseup',
					cancel = isTouch ? 'touchcancel' : 'mouseout'
				doc.off(move, el.data('tap.move'))
				el.off(start, el.data('tap.start')).off(end, el.data('tap.end')).off(cancel, el.data('tap.end'))
			} else if (event === 'scrollstart') {
				let eventFn = jCore.events(item, event)
				if (window.removeEventListener) {
					item.removeEventListener('scroll', eventFn, false)
				} else if (window.detachEvent) {
					item.detachEvent('onscroll', eventFn)
				}
			} else if (event === 'scrollstop') {
				let eventFn = jCore.events(item, event)
				if (window.removeEventListener) {
					item.removeEventListener('touchstart', eventFn.touchstart, false)
					item.removeEventListener('touchend', eventFn.touchend, false)
					item.removeEventListener('scroll', eventFn.scroll, false)
				} else if (window.detachEvent) {
					item.detachEvent('ontouchstart', eventFn.touchstart)
					item.detachEvent('ontouchend', eventFn.touchend)
					item.detachEvent('onscroll', eventFn.scroll)
				}
			} else {
				let eventFn = jCore.events(item, event)
				if (window.removeEventListener) {
					item.removeEventListener(event, eventFn, false)
				} else if (window.detachEvent) {
					item.detachEvent('on' + event, eventFn)
				}
			}
			jCore.removeEvents(item, event)
		})
		return caller
	},
	//绑定mouseover、mouseout动作
	hover: function(callbackOver, callbackOut) {
		if (!this.selector.length) return this
		this.mouseover(callbackOver).mouseout(callbackOut)
		return this
	},
	//转array
	toArray: function() {
		return this.selector
	},
	//触发
	trigger: function(event) {
		if (event === 'tap'){
			let isTouch = 'ontouchend' in document.createElement('div'),
				end = isTouch ? 'touchend' : 'mouseup'
			event = end
		} else if (event === 'scrollstart' || event === 'scrollstop') {
			event = 'scroll'
		}
		this.selector.forEach(item => {
			if (jCore.browser.ie) {
				item[event.toLowerCase()]()
			} else {
				let e = document.createEvent('MouseEvent')
				e.initEvent(event.toLowerCase(), true, true)
				item.dispatchEvent(e)
			}
		})
		return this
	},
	//将一组元素转换成其他数组
	map: function(callback) {
		if (!this.selector.length) return []
		if (!(callback instanceof Function)) return []
		let array = []
		this.selector.forEach(item => {
			array.push(callback.call(item))
		})
		return array
	},
	//回到最近的一个"破坏性"操作之前。即，将匹配的元素列表变为前一次的状态
	end: function() {
		return jCore(jCore.prevSelector)
	}
}

jCore.event = {
	lastMark: function(selector) {
		let last = '', self = '', mark = '', name = ''
		if (selector.substring(0, 1) === '<') {
			self = selector
			mark = self.substring(0, 1)
			name = self
		} else {
			let selectors = selector.split('>')
			if (selectors.length === 1) {
				selectors = selector.split(/\s+/)
				last = jCore.trim(selectors[selectors.length-1])
				self = /^[#.[:]/.test(last) ? (last.substring(1).split(/[#.[:]/))[0] : (last.split(/[#.[:]/))[0]
				mark = last.substring(0, 1)
				name = last.substring(1)
			} else {
				last = jCore.trim(selectors[selectors.length-1])
				selectors = last.split(/\s+/)
				last = jCore.trim(selectors[selectors.length-1])
				self = /^[#.[:]/.test(last) ? (last.substring(1).split(/[#.[:]/))[0] : (last.split(/[#.[:]/))[0]
				mark = last.substring(0, 1)
				name = last.substring(1)
			}
		}
		return {self:self, mark:mark, name:name}
	},
	assignSelector: function(selector, elArray, callerType, callback) {
		//callerType = 1:is(elArray为自己的元素集), 2:children(elArray为父母的元素集), 3:find(elArray为父母的元素集)
		let res = false, matcher, el, item
		let items = selector.split(',')
		for (let t = 0; t < items.length; t++) {
			let select = jCore.trim(items[t])
			let marks = jCore.event.lastMark(select), self = marks.self, mark = marks.mark, name = marks.name
			switch (mark) {
				case '#':
					for (let k = 0; k < elArray.length; k++) {
						item = elArray[k]
						if (callerType === 3) {
							el = item.getElementById(self)
							if (jCore.event.middleMark(elArray, el, name)) callback(el)
						} else if (callerType === 2) {
							item.childNodes.forEach(child => {
								if (child.nodeType === 1 && child.getAttribute('id') === self) {
									if (jCore.event.middleMark(elArray, child, name)) callback(child)
								}
							})
						} else {
							if (item.getAttribute('id') === self) {
								res = true
								break
							}
						}
					}
					break
				case '.':
					for (let k = 0; k < elArray.length; k++) {
						item = elArray[k]
						if (callerType === 3) {
							el = item.getElementsByClassName(self)
							el = Array.prototype.slice.call(el)
							el.forEach(e => {
								if (jCore.event.middleMark(el, e, name)) callback(e)
							})
						} else if (callerType === 2) {
							item.childNodes.forEach(child => {
								if (child.nodeType === 1 && typeof child.className !== 'undefined' && new RegExp('\\b(' + self + ')\\b', 'ig').test(child.className)) {
									if (jCore.event.middleMark(elArray, child, name)) callback(child)
								}
							})
						} else {
							if (typeof item.className !== 'undefined' && new RegExp('\\b(' + self + ')\\b', 'ig').test(item.className)) {
								res = true
								break
							}
						}
					}
					break
				case '[':
					let attr = name.substring(0, name.indexOf(']'))
					for (let k = 0; k < elArray.length; k++) {
						item = elArray[k]
						let attrCount = 0
						let _callback = (el, e, _attr) => {
							matcher = jCore.trim(_attr).match(/^([\w-]+)(\s*(=|!=|\^=|\$=|\*=)\s*(['"]?)(.*)\4)?$/)
							for (let j = 0; j < e.attributes.length; j++) {
								if (e.attributes[j].nodeName === jCore.trim(matcher[1])) {
									if (matcher[2]) {
										switch (matcher[3]) {
											case '=':
												if (e.attributes[j].nodeValue === matcher[5]) {
													if (callerType === 1) {
														attrCount++
													} else {
														if (jCore.event.middleMark(el, e, name)) callback(e)
													}
												}
												break
											case '!=':
												if (e.attributes[j].nodeValue !== matcher[5]) {
													if (callerType === 1) {
														attrCount++
													} else {
														if (jCore.event.middleMark(el, e, name)) callback(e)
													}
												}
												break
											case '^=':
												if (new RegExp('^' + matcher[5].replace(/([()|\\^$*[\]])/g, '\\$1')).test(e.attributes[j].nodeValue)) {
													if (callerType === 1) {
														attrCount++
													} else {
														if (jCore.event.middleMark(el, e, name)) callback(e)
													}
												}
												break
											case '$=':
												if (new RegExp(matcher[5].replace(/([()|\\^$*[\]])/g, '\\$1') + '$').test(e.attributes[j].nodeValue)) {
													if (callerType === 1) {
														attrCount++
													} else {
														if (jCore.event.middleMark(el, e, name)) callback(e)
													}
												}
												break
											case '*=':
												if (e.attributes[j].nodeValue.indexOf(matcher[5]) > -1) {
													if (callerType === 1) {
														attrCount++
													} else {
														if (jCore.event.middleMark(el, e, name)) callback(e)
													}
												}
												break
										}
									} else {
										if (callerType === 1) {
											attrCount++
										} else {
											if (jCore.event.middleMark(el, e, name)) callback(e)
										}
									}
									break
								}
							}
						}
						if (callerType === 1) {
							let is = false
							let arr = attr.split(/]\s*\[/g)
							for (let i = 0; i < arr.length; i++) {
								_callback(elArray, item, arr[i])
								if (attrCount >= arr.length) {
									is = true
									break
								}
							}
							res = is
						} else if (callerType === 2) {
							item.childNodes.forEach(child => {
								if (child.nodeType === 1) _callback(elArray, child, attr)
							})
						} else {
							el = item.getElementsByTagName('*')
							el = Array.prototype.slice.call(el)
							el.forEach(e => {
								_callback(el, e, attr)
							})
						}
					}
					break
				case ':':
					let childNodes = []
					for (let k = 0; k < elArray.length; k++) {
						item = elArray[k]
						if (callerType === 3) {
							el = item.getElementsByTagName('*')
							el = Array.prototype.slice.call(el)
							el.forEach(e => {
								childNodes.push(e)
							})
						} else if (callerType === 2) {
							el = []
							item.childNodes.forEach(child => {
								if (child.nodeType === 1) childNodes.push(child)
							})
						} else {
							childNodes.push(item)
						}
					}
					let base = ['first', 'last', 'eq(', 'not(', 'has(', 'contains(', 'empty', 'parent', 'checked', 'selected', 'hidden', 'visible', 'enabled', 'disabled']
					if (new RegExp('(' + base.join('|').replace(/\(/g, '\\(') + ')').test(self)) {
						for (let k = 0; k < childNodes.length; k++) {
							let e = childNodes[k]
							for (let i = 0; i < base.length; i++) {
								if (new RegExp(base[i].replace(/\(/g, '\\(')).test(self)) {
									switch (base[i]) {
										case 'first':
											if (childNodes[0] === e) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'last':
											if (childNodes[childNodes.length-1] === e) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'eq(':
											matcher = self.match(/^eq\((\d+)\)$/)
											if (!matcher) break
											let index = parseInt(matcher[1])
											if (index < 0 || index > childNodes.length - 1) break
											if (childNodes[index] === e) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'not(':
											matcher = name.match(/^not\(([^)]+)\)$/)
											if (!matcher) break
											if (!jCore(e).is(matcher[1])) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'has(':
											matcher = name.match(/^has\(([^)]+)\)$/)
											if (!matcher) break
											if (jCore(e).has(matcher[1])) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'contains(':
											matcher = name.match(/^contains\(([^)]+)\)$/)
											if (!matcher) break
											if (e.innerText.indexOf(matcher[1]) > 1) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'empty':
											if (e.innerHTML.length === 0) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'parent':
											if (e.innerHTML.length > 0) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'checked':
											if (jCore.inArray(e.getAttribute('type'), ['radio', 'checkbox']) > -1 && e.getAttribute('checked')) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'selected':
											if (e.tagName.toLowerCase() === 'select' && e.innerHTML.indexOf('selected') > -1) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'hidden':
											if (e.style.display === 'none' || e.getAttribute('type') === 'hidden') {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'visible':
											if (e.style.display !== 'none' && e.getAttribute('type') !== 'hidden') {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'enabled':
											if (e.getAttribute('enabled')) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
										case 'disabled':
											if (e.getAttribute('disabled')) {
												if (callback) {
													if (jCore.event.middleMark(childNodes, e, name)) callback(e)
												} else {
													res = true
												}
											}
											break
									}
								}
								if (res) break
							}
							if (res) break
						}
					} else {
						let types = ['input', 'button', 'text', 'password', 'hidden', 'radio', 'checkbox', 'file', 'submit', 'image', 'reset']
						for (let k = 0; k < childNodes.length; k++) {
							let e = childNodes[k]
							let type = e.getAttribute('type')
							if (type) type = type.toLowerCase()
							if (jCore.inArray(type, ['tel', 'number', 'date']) > -1) type = 'text'
							for (let i = 0; i < types.length; i++) {
								if (types[i] === self) {
									if (types[i] === 'input') {
										if (jCore.inArray(type, ['text', 'password', 'hidden', 'radio', 'checkbox', 'file', 'submit', 'image', 'reset']) > -1) {
											if (callback) {
												if (jCore.event.middleMark(childNodes, e, name)) callback(e)
											} else {
												res = true
											}
											break
										}
									} else if (types[i] === 'button') {
										if (e.tagName.toLowerCase() === types[i].toLowerCase()) {
											if (callback) {
												if (jCore.event.middleMark(childNodes, e, name)) callback(e)
											} else {
												res = true
											}
											break
										}
									} else {
										if (type === types[i].toLowerCase()) {
											if (callback) {
												if (jCore.event.middleMark(childNodes, e, name)) callback(e)
											} else {
												res = true
											}
											break
										}
									}
								}
							}
							if (res) break
						}
					}
					break
				default:
					for (let k = 0; k < elArray.length; k++) {
						item = elArray[k]
						if (callerType === 3) {
							el = item.getElementsByTagName(self)
							el = Array.prototype.slice.call(el)
							el.forEach(e => {
								if (jCore.event.middleMark(el, e, name)) callback(e)
							})
						} else if (callerType === 2) {
							item.childNodes.forEach(child => {
								if (child.nodeType === 1 && child.tagName.toUpperCase() === self.toUpperCase()) {
									if (jCore.event.middleMark(elArray, child, name)) callback(child)
								}
							})
						} else {
							if (item.tagName.toUpperCase() === self.toUpperCase()) {
								res = true
								break
							}
						}
					}
					break
			}
			if (res) break
		}
		return res
	},
	middleMark: function(selectors, e, name) {
		if (!/[#.\[:]/.test(name)) return true
		name = name.substring(name.search(/[#.\[:]/))
		let matcher
		let mark = name.substring(0, 1), select = name.substring(1, name.substring(1).search(/([#.\[:]|$)/) + 1)
		name = name.substring(1)
		switch (mark) {
			case '#':
				if (e.getAttribute('id') === select) return jCore.event.middleMark(selectors, e, name)
				break
			case '.':
				if (typeof e.className !== 'undefined' && new RegExp('\\b(' + select + ')\\b', 'ig').test(e.className)) return jCore.event.middleMark(selectors, e, name)
				break
			case '[':
				let attr = name.substring(0, name.indexOf(']'))
				matcher = jCore.trim(attr).match(/^([\w-]+)(\s*(=|!=|\^=|\$=|\*=)\s*(['"]?)(.*)\4)?$/)
				for (let i = 0; i < e.attributes.length; i++) {
					if (e.attributes[i].nodeName === jCore.trim(matcher[1])) {
						if (matcher[2]) {
							switch (matcher[3]) {
								case '=':
									if (e.attributes[i].nodeValue === matcher[5]) return jCore.event.middleMark(selectors, e, name)
									break
								case '!=':
									if (e.attributes[i].nodeValue !== matcher[5]) return jCore.event.middleMark(selectors, e, name)
									break
								case '^=':
									if (new RegExp('^' + matcher[5].replace(/([()|\\^$*[\]])/g, '\\$1')).test(e.attributes[i].nodeValue)) return jCore.event.middleMark(selectors, e, name)
									break
								case '$=':
									if (new RegExp(matcher[5].replace(/([()|\\^$*[\]])/g, '\\$1') + '$').test(e.attributes[i].nodeValue)) return jCore.event.middleMark(selectors, e, name)
									break
								case '*=':
									if (e.attributes[i].nodeValue.indexOf(matcher[5]) > -1) return jCore.event.middleMark(selectors, e, name)
									break
							}
						} else {
							return jCore.event.middleMark(selectors, e, name)
						}
						break
					}
				}
				break
			case ':':
				let base = ['first', 'last', 'eq(', 'not(', 'has(', 'contains(', 'empty', 'parent', 'checked', 'selected', 'hidden', 'visible', 'enabled', 'disabled']
				if (new RegExp('(' + base.join('|').replace(/\(/g, '\\(') + ')').test(select)) {
					for (let i = 0; i < base.length; i++) {
						if (new RegExp(base[i].replace(/\(/g, '\\(')).test(select)) {
							switch (base[i]) {
								case 'first':
									if (selectors[0] === e) return jCore.event.middleMark(selectors, e, name)
									break
								case 'last':
									if (selectors[selectors.length-1] === e) return jCore.event.middleMark(selectors, e, name)
									break
								case 'eq(':
									matcher = select.match(/^eq\((\d+)\)$/)
									if (!matcher) return false
									let index = parseInt(matcher[1])
									if (index < 0 || index > selectors.length - 1) return false
									if (selectors[index] === e) return jCore.event.middleMark(selectors, e, name)
									break
								case 'not(':
									matcher = name.match(/^not\(([^)]+)\)$/)
									if (!matcher) return false
									if (!jCore(e).is(matcher[1])) return jCore.event.middleMark(selectors, e, name)
									break
								case 'has(':
									matcher = name.match(/^has\(([^)]+)\)$/)
									if (!matcher) return false
									if (jCore(e).has(matcher[1])) return jCore.event.middleMark(selectors, e, name)
									break
								case 'contains(':
									matcher = name.match(/^contains\(([^)]+)\)$/)
									if (!matcher) return false
									if (e.innerText.indexOf(matcher[1]) > 1) return jCore.event.middleMark(selectors, e, name)
									break
								case 'empty':
									if (e.innerHTML.length === 0) return jCore.event.middleMark(selectors, e, name)
									break
								case 'parent':
									if (e.innerHTML.length > 0) return jCore.event.middleMark(selectors, e, name)
									break
								case 'checked':
									if (jCore.inArray(e.getAttribute('type'), ['radio', 'checkbox']) > -1 && e.getAttribute('checked')) return jCore.event.middleMark(selectors, e, name)
									break
								case 'selected':
									if (e.tagName.toLowerCase() === 'select' && e.innerHTML.indexOf('selected') > -1) return jCore.event.middleMark(selectors, e, name)
									break
								case 'hidden':
									if (e.style.display === 'none' || e.getAttribute('type') === 'hidden') return jCore.event.middleMark(selectors, e, name)
									break
								case 'visible':
									if (e.style.display !== 'none' && e.getAttribute('type') !== 'hidden') return jCore.event.middleMark(selectors, e, name)
									break
								case 'enabled':
									if (e.getAttribute('enabled')) return jCore.event.middleMark(selectors, e, name)
									break
								case 'disabled':
									if (e.getAttribute('disabled')) return jCore.event.middleMark(selectors, e, name)
									break
							}
						}
					}
				} else {
					let types = ['input', 'button', 'text', 'password', 'hidden', 'radio', 'checkbox', 'file', 'submit', 'image', 'reset']
					let type = e.getAttribute('type')
					if (type) type = type.toLowerCase()
					if (jCore.inArray(type, ['tel', 'number', 'date']) > -1) type = 'text'
					for (let i = 0; i < types.length; i++) {
						if (types[i] === select) {
							if (types[i] === 'input') {
								if (jCore.inArray(type, ['text', 'password', 'hidden', 'radio', 'checkbox', 'file', 'submit', 'image', 'reset']) > -1) {
									return jCore.event.middleMark(selectors, e, name)
								}
							} else if (types[i] === 'button') {
								if (e.tagName.toLowerCase() === types[i].toLowerCase()) {
									return jCore.event.middleMark(selectors, e, name)
								}
							} else {
								if (type === types[i].toLowerCase()) {
									return jCore.event.middleMark(selectors, e, name)
								}
							}
						}
					}
				}
				break
		}
		return false
	},
	middleSpace: function(selector, context, start) {
		let index = start ? start : 0
		let contexter = typeof context === 'undefined' ? jCore(document) : jCore(context)
		let selectors = jCore.trim(selector).split(/\s+/)
		if (selectors.length > 1) {
			for (let i = index; i < selectors.length; i++) {
				if (!jCore.trim(selectors[i])) continue
				contexter = contexter.find(jCore.trim(selectors[i]))
			}
		} else {
			contexter = contexter.find(jCore.trim(selector))
		}
		return contexter
	},
	middleArror: function(selector, context) {
		let contexter
		let selectors = selector.split('>')
		if (selectors.length > 1) {
			for (let i = 0; i < selectors.length; i++) {
				if (i === 0) {
					if (!jCore.trim(selectors[i])) {
						contexter = jCore(context)
						continue
					}
					contexter = jCore.event.middleSpace(jCore.trim(selectors[i]), context)
				} else {
					if (!jCore.trim(selectors[i])) continue
					let _selects = jCore.trim(selectors[i]).split(/\s+/)
					contexter = contexter.children(jCore.trim(_selects[0]))
					if (_selects.length > 1) {
						contexter = jCore.event.middleSpace(jCore.trim(selectors[i]), contexter, 1)
					}
				}
			}
		} else {
			contexter = jCore.event.middleSpace(selector, context)
		}
		return contexter
	}
}

//初始化
const jCoreInit = jCore.fn.init = function(selector, context) {
	if (!selector) return this
	if (selector.constructor === jCore) return selector //如果是jCore对象则直接返回
	if (selector instanceof Function) return jCore(window).on('load', selector)
	this.selector = []
	if (typeof selector === 'object' && selector) {
		if (selector && typeof selector === 'object' && Object.prototype.toString.call(selector).toLowerCase() === '[object object]') {
			let keys = Object.keys(selector)
			for (let i = 0; i < keys.length; i++) this[i] = selector[keys[i]]
			this.selector = selector
		} else {
			if (selector instanceof Array) {
				for (let i = 0; i < selector.length; i++) this[i] = selector[i]
				this.selector = selector
			} else if (selector.nodeType) {
				this[0] = selector
				this.selector = [selector]
			}
		}
	} else if (typeof selector === 'string') {
		let elArray = []
		selector = jCore.trim(selector)
		let items = selector.substring(0, 1) === '<' ? [selector] : selector.split(',')
		items.forEach(select => {
			select = jCore.trim(select)
			let marks = jCore.event.lastMark(select), mark = marks.mark
			switch (mark) {
				case '<': //<name></name>
					//两个正则表达式都必须放外面,否则死循环
					let markReg = /^<(\w+)([^>]*)>(([\s\S]*?)<\/\1>)?$/ig
					let attrReg = /\b(\w+)\s*=\s*(['"])(.*?)\2/ig
					let matcher, attrs
					while ((matcher = markReg.exec(select)) !== null) {
						let item = document.createElement(matcher[1])
						if (matcher[2]) {
							while ((attrs = attrReg.exec(matcher[2])) !== null) {
								item.setAttribute(attrs[1], attrs[3])
							}
						}
						if (matcher[4]) item.innerHTML = matcher[4]
						elArray.push(item)
						break
					}
					break
				default: //#name .name [name=value] :name tagName *
					jCore.event.middleArror(select, context).selector.forEach(item => {
						if (item) {
							if (jCore.inArray(item, elArray) < 0) elArray.push(item)
						}
					})
					break
			}
		})
		this[0] = elArray[0]
		this.selector = elArray
	}
	if (selector && typeof selector === 'object' && Object.prototype.toString.call(selector).toLowerCase() === '[object object]') {
		this.length = Object.keys(selector).length
	} else {
		this.length = this.selector.length
	}
	return this
}

jCoreInit.prototype = jCore.fn

//版本
jCore.version = version

//缓存数据
jCore.cacheKey = []
jCore.cacheValue = []

//前一次的元素
jCore.prevSelector = []
jCore.setPrevSelector = function(selector) {
	jCore.prevSelector = []
	selector.forEach(item => {
		jCore.prevSelector.push(item)
	})
}

//元素绑定事件数据
jCore.eventsKey = []
jCore.eventsFunction = []
jCore.events = function(key, event) {
	for (let i = 0; i < jCore.eventsKey.length; i++) {
		if (jCore.eventsKey[i] === key) {
			if (typeof event === 'undefined') {
				return jCore.eventsFunction[i]
			} else {
				return jCore.eventsFunction[i][event]
			}
		}
	}
	return null
}
jCore.setEvents = function(key, event, callback) {
	for (let i = 0; i < jCore.eventsKey.length; i++) {
		if (jCore.eventsKey[i] === key) {
			let obj = jCore.eventsFunction[i]
			obj[event] = callback
			return
		}
	}
	jCore.eventsKey.push(key)
	let obj = {}
	obj[event] = callback
	jCore.eventsFunction.push(obj)
}
jCore.removeEvents = function(key, event) {
	for (let i = 0; i < jCore.eventsKey.length; i++) {
		if (jCore.eventsKey[i] === key) {
			if (typeof event === 'undefined') {
				delete jCore.eventsKey[i]
				delete jCore.eventsFunction[i]
			} else {
				delete jCore.eventsFunction[i][event]
				if (jCore.isEmptyObject(jCore.eventsFunction[i])) {
					delete jCore.eventsKey[i]
					delete jCore.eventsFunction[i]
				}
			}
			break
		}
	}
}
jCore._data = function(key, mark) { //模仿jQuery
	if (typeof jCore[mark] === 'undefined') {
		throw new Error(mark + 'is undefined')
	}
	return jCore[mark](key)
}

//新子元素自动绑定事件数据, key为选择器字符串
jCore.eventsNewElementParent = []
jCore.eventsNewElementKey = []
jCore.eventsNewElementFunction = []
jCore.eventsNewElement = function(parent, selector) {
	for (let i = 0; i < jCore.eventsNewElementParent.length; i++) {
		if (jCore.eventsNewElementParent[i] === parent && jCore.eventsNewElementKey[i] === selector) {
			return jCore.eventsNewElementFunction[i]
		}
	}
	return null
}
jCore.setEventsNewElement = function(parent, selector, event, callback) {
	for (let i = 0; i < jCore.eventsNewElementParent.length; i++) {
		if (jCore.eventsNewElementParent[i] === parent && jCore.eventsNewElementKey[i] === selector) {
			let obj = jCore.eventsNewElementFunction[i]
			obj[event] = callback
			return
		}
	}
	jCore.eventsNewElementParent.push(parent)
	jCore.eventsNewElementKey.push(selector)
	let obj = {}
	obj[event] = callback
	jCore.eventsNewElementFunction.push(obj)
}
jCore.removeEventsNewElement = function(parent, selector, event) {
	for (let i = 0; i < jCore.eventsNewElementParent.length; i++) {
		if (typeof event === 'undefined') {
			if (jCore.eventsNewElementParent[i] === parent) {
				delete jCore.eventsNewElementParent[i]
				delete jCore.eventsNewElementKey[i]
				delete jCore.eventsNewElementFunction[i]
			}
		} else {
			if (jCore.eventsNewElementParent[i] === parent && jCore.eventsNewElementKey[i] === selector) {
				delete jCore.eventsNewElementFunction[i][event]
				if (jCore.isEmptyObject(jCore.eventsNewElementFunction[i])) {
					delete jCore.eventsNewElementParent[i]
					delete jCore.eventsNewElementKey[i]
				}
			}
		}
	}
}
jCore.onEventsNewElement = function(parent) {
	for (let i = 0; i < jCore.eventsNewElementParent.length; i++) {
		if (jCore.eventsNewElementParent[i] === parent) {
			jCore(parent).find(jCore.eventsNewElementKey[i]).selector.forEach(item => {
				let events = jCore.events(item)
				if (events) return
				for (let key in jCore.eventsNewElementFunction[i]) {
					jCore(item).on(key, jCore.eventsNewElementFunction[i][key])
				}
			})
		}
	}
}

//判断浏览器
jCore.uaMatch = function(ua) {
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
let browser = { ua: navigator.userAgent }, uaMatch = jCore.uaMatch(browser.ua)
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
jCore.browser = browser

jCore.eventsArray = ('blur focus focusin focusout resize scroll click dblclick ' +
	'mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave ' +
	'change select submit keydown keypress keyup contextmenu ' +
	'load error tap scrollstart scrollstop touchstart touchmove touchend touchcancel').split(' ')
jCore.eventsArray.forEach(name => {
	jCore.prototype[name] = function(callback) {
		if (arguments.length > 0) {
			return this.on(name, callback)
		} else {
			if (name === 'tap') {
				let isTouch = 'ontouchend' in document.createElement('div'),
					start = isTouch ? 'touchstart' : 'mousedown',
					end = isTouch ? 'touchend' : 'mouseup'
				return this.trigger(start).trigger(end)
			} else if (name === 'scrollstart' || name === 'scrollstop') {
				return this.trigger('scroll')
			} else {
				return this.trigger(name)
			}
		}
	}
})

//历遍对象
jCore.each = jCore.fn.each = function(arr, callback) {
	if (!(arr instanceof Function)) {
		arr = jCore(arr).selector
	} else {
		callback = arr
		arr = this.selector
	}
	if (!(callback instanceof Function)) return this
	if (arr instanceof Array) {
		for (let i = 0; i < arr.length; i++) {
			let res = callback.call(arr[i], i, arr[i])
			if (typeof (res) === 'boolean' && !res) break
		}
	} else if (arr && typeof arr === 'object' && Object.prototype.toString.call(arr).toLowerCase() === '[object object]') {
		for (let key in arr) {
			let res = callback.call(arr[key], key, arr[key])
			if (typeof (res) === 'boolean' && !res) break
		}
	}
	return this
}

//使用对象扩展另一个对象
jCore.extend = jCore.fn.extend = function() {
	if (!arguments.length) return this
	if (arguments.length === 1 &&
		arguments[0] && typeof arguments[0] === 'object' && Object.prototype.toString.call(arguments[0]).toLowerCase() === '[object object]') {
		for (let key in arguments[0]) {
			jCore[key] = arguments[0][key]
		}
		return this
	}
	let args = null
	if (arguments[0] instanceof Array) {
		args = jCore.clone(arguments[0])
		if (!(args instanceof Array)) args = []
		for (let i = 1; i < arguments.length; i++) {
			if (!(arguments[i] instanceof Array)) continue
			args = args.concat(jCore.clone(arguments[i]))
		}
	} else {
		args = jCore.clone(arguments[0])
		if (!(args && typeof args === 'object' && Object.prototype.toString.call(args).toLowerCase() === '[object object]')) args = {}
		for (let i = 1; i < arguments.length; i++) {
			if (!(arguments[i] && typeof arguments[i] === 'object' && Object.prototype.toString.call(arguments[i]).toLowerCase() === '[object object]')) continue
			for (let key in arguments[i]) {
				let data = jCore.clone(arguments[i][key])
				args[key] = data
			}
		}
	}
	return args
}

//克隆对象或数组
jCore.clone = jCore.fn.clone = function(obj) {
	if (this.constructor === jCore) {
		if (typeof obj === 'undefined') return jCore(this.selector[0].outerHTML)
	}
	if (!obj) return obj
	if (obj instanceof Date) {
		return new Date(obj.valueOf())
	} else if (obj instanceof Array) {
		let newArr = []
		for (let i = 0; i < obj.length; i++) {
			newArr.push(jCore.clone(obj[i]))
		}
		return newArr
	} else if (obj && typeof obj === 'object' && Object.prototype.toString.call(obj).toLowerCase() === '[object object]') {
		let newObj = {}
		for (let key in obj) {
			newObj[key] = jCore.clone(obj[key])
		}
		return newObj
	}
	return obj
}

//获取类型
jCore.type = jCore.fn.type = function(obj) {
	if (this.constructor === jCore) {
		if (!this.selector.length) return ''
		let e = this.selector[0]
		let type = e.getAttribute('type')
		if (type) {
			type = type.toLowerCase()
			if (jCore.inArray(type, ['tel', 'number', 'date']) > -1) type = 'text'
		} else {
			type = e.tagName.toLowerCase()
		}
		return type
	}
	return Object.prototype.toString.call(obj).replace(/^\[object\s(.*?)]$/, '$1').toLowerCase()
}

jCore.extend({
	//将类数组对象转换为数组对象
	makeArray: function(obj) {
		if (!obj) return []
		if (obj.constructor === jCore) return obj.selector
		return Array.prototype.slice.call(obj)
	},
	//AJAX请求
	ajax: function(options) {
		options = jCore.extend({
			url: '', //请求网址
			type: 'GET', //请求方式
			data: null, //请求数据
			dataType: '', //预期服务器返回的数据类型
			timeout: 0, //请求超时时间(毫秒)
			async: true, //异步请求
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8', //发送信息至服务器时内容编码类型
			headers: {}, //请求头
			xhr: null, //返回 xhr 对象,用于重写或者提供一个增强的对象
			xhrFields: {}, //设置原始xhr的key、value, xhr默认是 responseType:'arraybuffer'
			withCredentials: false, //跨域设置
			beforeSend: null, //发送前执行
			success: null, //服务端成功返回数据
			error: null, //发送异常
			complete: null //发送完成
		}, options);
		let url = options.url, type = options.type ? options.type.toUpperCase() : 'POST', data = options.data
		if (!url.length) {alert('missing url');return false}
		url += (url.indexOf('?') === -1 ? '?' : '&') + 'rndnum=' + escape(Math.random())
		if (data && type !== 'POST') {
			url += (url.indexOf('?') === -1 ? '?' : '&') + (jCore.isPlainObject(data) ? Object.entries(data).map(item => item.join('=')).join('&') : jCore.trim(data, '&'))
			data = null
		}
		if (jCore.isPlainObject(data)) data = Object.entries(data).map(item => item.join('=')).join('&')
		const httpRequest = function() {
			if (window.XMLHttpRequest) return new XMLHttpRequest()
			else if (window.ActiveXObject) {
				let activeXName = ['MSXML2.XMLHttp.5.0', 'MSXML2.XMLHttp.4.0', 'MSXML2.XMLHttp.3.0', 'MSXML2.XMLHttp', 'Microsoft.XMLHttp']
				for (let i = 0; i < activeXName.length; i++) {
					try{ return new ActiveXObject(activeXName[i]) }
					catch(e){ return false }
				}
			}
		}
		let xhr = new httpRequest()
		if (jCore.isFunction(options.xhr)) options.xhr(xhr)
		xhr.open(type, url, options.async)
		if (options.timeout > 0) xhr.timeout = options.timeout
		if (!/^http/.test(options.url)) {
			const account = JSON.parse(jCore.storage('member'))
			if (account && account.sign) options.headers['Sign'] = account.sign
		}
		for (let key in options.headers) xhr.setRequestHeader(key, options.headers[key])
		if (options.contentType.length) xhr.setRequestHeader('Content-Type', options.contentType)
		for (let key in options.xhrFields) xhr[key] = options.xhrFields[key]
		if (jCore.isFunction(options.beforeSend)) options.beforeSend(xhr)
		if (options.withCredentials) xhr.withCredentials = true
		let httpData = function(r, type) {
			let target = !type
			target = (type === 'xml' || target) ? r.responseXML : r.responseText
			if (!target || !target.length) return ''
			if (type === 'script') {
				if (jCore.trim(target)) {
					(window.execScript || function(data) {
						window['eval'].call(window, data)
					})(target)
				}
			}
			if (type === 'json') {
				target = target.replace(/<pre.+?>(.+)<\/pre>/g, '$1')
				target = JSON.parse(target)
			}
			if (type === 'html') jCore('<div></div>').html(target).evalScripts()
			return target
		};
		xhr.onload = function() {
			if (xhr.status === 200) {
				if (jCore.isFunction(options.success)) options.success(httpData(xhr, options.dataType))
			} else {
				if (jCore.isFunction(options.error)) options.error(xhr)
			}
			if (jCore.isFunction(options.complete)) options.complete(xhr)
		}
		xhr.send(data)
		return xhr
	},
	//get请求
	get: function(url, data, callback) {
		if (typeof data === 'function' && typeof callback === 'undefined') {
			callback = data
			data = null
		}
		return jCore.ajax({
			url: url,
			data: data,
			success: callback
		})
	},
	//post请求
	post: function(url, data, callback) {
		return jCore.ajax({
			url: url,
			type: 'POST',
			data: data,
			success: callback
		})
	},
	//get请求, 服务器数据返回 application/json 格式
	getJSON: function(url, data, callback) {
		if (typeof data === 'function' && typeof callback === 'undefined') {
			callback = data
			data = null
		}
		return jCore.ajax({
			url: url,
			data: data,
			dataType: 'json',
			contentType: 'application/json; charset=UTF-8',
			success: callback
		})
	},
	//get请求, 载入并执行一个 JavaScript 文件
	getScript: function(url, callback) {
		return jCore.ajax({
			url: url,
			dataType: 'script',
			contentType: 'application/x-javascript; charset=UTF-8',
			success: callback
		})
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
				data = jCore.cookie(key.data)
				if (data) {
					if (Number(jCore.cookie(key.time)) > (new Date()).getTime()) {
						//value = JSON.parse(value);
						return data
					} else {
						jCore.cookie(key.data, null)
						jCore.cookie(key.time, null)
					}
				}
			} else if (data === null) {
				jCore.cookie(key.data, null)
				jCore.cookie(key.time, null)
			} else {
				if (typeof time === 'undefined') time = 1
				if (typeof data !== 'string') data = JSON.stringify(data)
				jCore.cookie(key.data, data, {expires:time})
				jCore.cookie(key.time, time, {expires:time})
			}
		}
		return null
	},
	//获取浏览器本地存储且转对象
	storageJSON: function(key) {
		let data = jCore.storage(key)
		return !data ? null : JSON.parse(data)
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
			if (jCore.isNumeric(options)) {
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
	//获取cookie且转对象
	cookieJSON: function(name) {
		let data = jCore.cookie(name)
		return !data ? null : JSON.parse(data)
	}
})

const filters = {
	//是否在数组里
	inArray: function(search, array) {
		if (!(array instanceof Array)) return -1
		let index = -1
		for (let i = 0; i < array.length; i++) {
			if (array[i] === search) {
				index = i
				break
			}
		}
		return index
	},
	//是否数组
	isArray: function(obj) {
		if (!obj) return false
		return (obj instanceof Array)
	},
	//是否对象
	isPlainObject: function(obj) {
		if (!obj) return false
		return obj && typeof obj === 'object' && Object.prototype.toString.call(obj).toLowerCase() === '[object object]'
	},
	//是否空对象
	isEmptyObject: function(obj) {
		if (!jCore.isPlainObject(obj)) return false
		return JSON.stringify(obj) === '{}'
	},
	//是否函数
	isFunction: function(func) {
		if (!func) return false
		return (func instanceof Function)
	},
	//是否数字
	isNumeric: function(str) {
		return !isNaN(str)
	},
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
		return jCore.isPlainObject(obj)
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
		return Math.round(value * Math.pow(10, prec)) / Math.pow(10, prec)
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

jCore.extend(filters)

jCore.filters = filters

window.$ = window.jCore = jCore

export default {
	$,
	jCore,
	filters
}