"use strict";
if (typeof(jQuery) == 'undefined')throw new Error("juqery is not require");
/**
 * 判断函数是否存在
 * @param  {string} functionName 函数名
 * @return {[type]}              [description]
 */
var function_exist  = function (functionName) {
	try {
	    if (typeof(eval(functionName)) == "function") {
	      return true;
	    }
	} catch(e) {}
	return false;
}
/**
 * post方式提交ajax数据
 * @param  {string}   url      提交地址
 * @param  {json}   data    json数据
 * @param  {Function} callback 回调函数
 * @param  {[type]}   type     接受数据类型
 * @return {[type]}            [description]
 */
if (!function_exist('ajaxPost')) {
	var ajaxPost = function (url,data,callback,type) {
		if (callback && !(callback instanceof Function))throw new Error("callback is not a function");
		type = type?type:'json';
		$.post(url,data,function (F) {
			if (callback) eval(callback(F));
		},type)
	}
}
	
/**
 * 判断是否是IE浏览器
 * @return {Boolean} [description]
 */
if (!function_exist('isIE')) {
	function isIE ()
	{
	  if (!!window.ActiveXObject || "ActiveXObject" in window) {
	  	return true;
	  }
	  return false;
	}
}
if (!function_exist('inArray')) {
/**
 * 判断元素是否在数组里面
 * @param  {array} arr   数组
 * @param  {array|string} value 元素值
 * @return {[type]}       [description]
 */
	var inArray = function (arr,value) {
		var l = arr.length;
		for (var i = 0;i < l;i ++) {
			if (arr[i] == value) {
				return true;
			}
		}
		return false;
	}
}
if (!function_exist('createBaseDom')) {
	/**
	 * 创建基本dom
	 * @param  {[type]} element   标签名
	 * @param  {[type]} className class类名
	 * @param  {[type]} css       [description]
	 * @return {[type]}           [description]
	 */
	var createBaseDom = function (element,className,css) {
		var dom = document.createElement(element);
		if (className) dom.className = className;
		if (css) {
			for (var key in css) {
				if (dom.style.hasOwnProperty(key)) {
					dom.style.setProperty(key,css[key]);
				}
			}
		}
		return dom;
	}	
}
if (!function_exist('extend')) {
	/**
	 * 合并对象
	 * @param  {object} target 合并目标
	 * @param  {object} source 合并源
	 * @return {[type]}        [description]
	 */
	var extend = function (target,source) {
		if (!(target instanceof Object)) throw new Error("target is not a object");
		if (!(source instanceof Object)) throw new Error("source is not a object");
		for (var key in target) {
			if (source.hasOwnProperty(key) === true) { //判断source对象
					target[key] = source[key];
			}
		}
		return target;
	}
}
if (!function_exist('alertInfo')) {
	var alertInfo = function (message,type) {
		var a_icon = (type == 1)?1:2;
		window.parent.layer.alert(message,{icon:a_icon});
	}
}	
if (!function_exist('includeFile')) {
	/**
	 * 引入js或者css文件
	 * @param  {[type]} path 文件路径
	 * @param  {[type]} type 文件类型 js or css
	 * @return {[type]}      [description]
	 */
	var includeFile = function (path,type) {
		var head = document.getElementsByTagName('head')[0];
		if (type == 'css') {
			var link = createBaseDom("link");
			link.setAttribute('rel','stylesheet');
			link.setAttribute('type','text/css');
			link.setAttribute('href',path);
			head.appendChild(link);
		} else {
			var script = createBaseDom('script');
			script.setAttribute('type','text/javascript');
			script.setAttribute('src',path);
			head.appendChild(script);
		}
			
	}
}	
if (!function_exist('trim')) {
	var trim = function (str) {
		if (typeof(str) !== "string") return str;
	   	return str.replace(/^\s+|\s+$/gm,'');
	} 
}	
if (!function_exist('isArray')) {
	var isArray = function (variable) {
		return Object.prototype.toString.call(variable) == '[object Array]';
	}
}
var cloneObj = function(obj){
    var str, newobj = obj.constructor === Array ? [] : {};
    if(typeof obj !== 'object'){
        return;
    } else if(window.JSON){
        str = JSON.stringify(obj), //序列化对象
        newobj = JSON.parse(str); //还原
    } else {
        for(var i in obj){
            newobj[i] = typeof obj[i] === 'object' ? cloneObj(obj[i]) : obj[i]; 
        }
    }
    return newobj;
}
var cloneFuncObj = function (obj) {
	alert(obj instanceof Function);
}
/**
 * 序列化表单并通过ajax提交
 * @param  {[type]} formId [description]
 * @return {[type]}        [description]
 */
var formOperate = function (formId,options) {
	this.options = {
		url:'',
		type:'json',
	};
	this.formId = '';
	this.formDom = '',
	this.elements = [],
	this.elementsType = ['input','file','select','textarea'];
	this.errorMsg = '';
	this.formData = {};
	this.init(formId,options || {});
}
formOperate.prototype = {
	init:function (formId,options) {
		this.options = extend(this.options,options);
		this.formId = formId;
		this.setElements();
		this.serializeForm();
	},
	setElements:function () {
		var formDom = this.formDom = document.getElementById(this.formId);
		if (!formDom)return;
		for (var i = 0;i < this.elementsType.length;i ++) {
			var tagElements = formDom.getElementsByTagName(this.elementsType[i]);
			for (var j = tagElements.length - 1; j >= 0; j --) {
				this.elements.push(tagElements[j]);
			}
		}
	},
	serializeForm:function () {
		if (!this.formDom)return;
		var elements = this.elements;
		if (elements) {
			for (var key in elements) {
				var item = elements[key];
				switch (item.type.toLowerCase()) {
					case 'checkbox': {
						if (item.checked)this.addFormData(item.name,[item.value]);
					}
					case 'radio': {
						if (item.checked)this.addFormData(item.name,item.value);
						break;
					}
					default: 
						this.addFormData(item.name,item.value);

				}
			}
		}
	},
	addFormData:function (key,value) {
		if (!this.formData[key]) {
			return this.formData[key] = value;
		} else if (isArray(value)) {
			this.formData[key].push(value[0]);
		}
	},
	submitForm:function () {
		if (true !== this.beforeSend()) return this.handleError();
		var _this = this;
		var xhr = $.post(this.options.url,this.formData,function (F) {
			_this.success(F,_this.formData,_this);
		},this.options.type)
		return xhr;
	},
	beforeSend:function () {
		return true;
	},
	success:function (F) {

	},
	error:function (object) {

	},
	handleError:function () {
		this.error(this);
	},
}
// var yw = 750; //原始图片宽度
// var ys = 1294; //原始图片高度
// var w = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
// var h = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

// function jsCSS() {
// 	var d = document;
// 	var dd = d.getElementsByClassName("d")[0];
// 	//dd.style.height = jsh(100) + "px";
// 	//this.ddCSS(dd);
// 	onload = function() {
// 		window.setTimeout(function() {
// 			lodingEND();
// 		}, 2000);
// 	}
// }

// jsCSS.prototype = {
// 	ddCSS: function(dd) {
// 		for (var i = 0; i < dd.getElementsByClassName("page1").length; i++) {
// 			var page1 = dd.getElementsByClassName("page1")[i];
// 			page1.style.top = jsh(15) + "px";
// 		}
// 		for (var i = 0; i < dd.getElementsByClassName("p1").length; i++) {
// 			var p1 = dd.getElementsByClassName("p1")[i];
// 			p1.style.fontSize = zk(0.03) + "px";
// 		}
// 	},
// }

// function jsh(a) {
// 	var j = (parseInt(w) * (parseInt(a) / yw));
// 	return j;
// }

// function zk(a) {
// 	var j = parseInt(w) * a;
// 	return j;
// }

// function lodingEND() {
// 	var outer = document.getElementsByClassName("outer");
// 	//document.getElementsByClassName("lodingICON")[0].style.opacity = "0";
// 	for (var i = 0; i < outer.length; i++) {
// 		outer[i].style.opacity = "1";
// 	}

// }

//new jsCSS();
var request_ajax = function (url,data,callback)
{
	if (!url) throw new Error('url is not be null');
	var load = window.parent.layer.load(1);
	window.parent.layer.style(load,{opacity:0.1});
	data = data?data:{};
	$.post(url,data,function (F) {
		window.parent.layer.close(load);
		if (callback)
			return eval(callback + '(' + F + ')');
		alertInfo(F.info,F.status);
		if (F.status) {
			if (F.data.url)
				return window.location.href = F.data.url;

			return window.location.reload();
		}
	},'json')
}
/**
 * 操作调用函数
 * @param  {string} type 调用的函数名
 * @param  {[type]} id   参数
 * @return {[type]}      [description]
 */
var operate = function (type,params)
{
  if (type == '') return false;
  if (!function_exist(type))throw new Error(type + "is not a function");
  var fun = type + "(" + params + ")";
  eval(fun);
}
var doOperate = function (data,url,confirmCont)
{
	function ensure (data,url) {
		var request_data = {};

		if (data) {
			data = data.split('&&');
			var key_value = [];

			for (var i = data.length - 1; i >= 0; i--) {
				key_value = data[i].split('=');
				request_data[key_value[0]] = key_value[1];
			}
		}
			
		return request_ajax(url,request_data);
	}
	if (confirmCont) {
		window.parent.layer.confirm(confirmCont,{icon:3,title:'提醒'},function (index) {
			window.parent.layer.close(index);
			ensure (data,url);

		})
	} else {
		ensure (data,url);
	}
}
