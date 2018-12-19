"use strict";
function MusicPlay (element,music,options) {
	var options = options || {};
	this.element = element;
	this.beforeInit(true);
	if (!music || music.length <= 0) {
		this.setDefaultMusicList(options);
	} else {
		this._init(options,music || []);
	}
};//创建音乐播放类
MusicPlay.prototype = {
	element:'',
	setting:{//配置
		keyAreaStyle:           {//按钮所在圆形区域样式
		                            "height":"50%",
		                        },  
		musicInfoStyle:         {
		                            "height":"25px",
		                            "width":"80px",
		                            //"color":"#E11111",
		                            "color":"#fff",
		                            "font_size":"14px",
		                        },  
		logoStyle:              {
		                            'height':"58",
		                            "width":"58",
		                        }, 
		setDiv:                 {
		                            'width':"40%",
		                            "height":"80%",
		                        },
		voiceProgress:          {"width":"50%","height":"400%"},
		totalProBg:  			"#fff",
		currProBg: 				"rgba(217, 129, 223, 0.9)",  
		listStyle:              {"width":"400%","font-size":"8px"},
		defaultLogo: 			"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1521128788181&di=c83048a66f796c292d606cad56a78f0f&imgtype=0&src=http%3A%2F%2Fnews.cnhubei.com%2Fxw%2Fyl%2F201605%2FW020160516305305050803.png", 
		defaultName: 			"未知",
		singerDisplay:          false,             
		keySize:                "15px",                     //开始暂停下一首按钮大小
		divId:                  "music-div",                //显示区域的DIVID
		width:                  360,                        //宽度
		height:                 56,                         //高度
		hasBlur:                true,                       //是否显示模糊效果
		blur:                   8,                          //模糊的数值
		isCenter:               false,                       //是否居中显示  translate
		btnBackground:          'rgba(0,0,0,0.2)',          //按钮背景色
		iconColor:              'rgba(250,250,250.0.2)',    //图标背景色
		hasSelect:              true,                       //是否可选择音乐类型
		hasAjax:                true,                       //是否是ajax请求数据
		selectClassName:        'select-type',              //选择类型按钮的className名称
		musicType:              ['纯音乐','华语','欧美','霉霉','电音','韩国','爱乐之城','网络歌曲'],         //音乐的类型  （需要随机显示）这是结合我自己后台数据库使用的 如果不是用ajax请求是不会显示这个类型的;
		source:                 [],
		bgImage:                "",
		width: 					"350",
		height: 				"50",
		//进度信息
		durationBg:             'rgba(255,255,255,0)',

		// 线性渐变的颜色
		progressBg:             [{
		                            position:0,         //0 是起点, 1为终点   范围为  0 - 1 之间
		                            color:"rgba(222,51,220,0.9)",    //起点的颜色   
		                        },{
		                            position:1,
		                            color:"rgba(222,51,220,0.9)",
		                        }],
		//滚动列表正在播放的背景色  //配合长按事件使用
		// scrollActiveBg:         'rgba(224, 189, 134, 0.298039)',
		// 播放初始化设置
		defaultVoice:  			0.05,
		defaultBackground: 		"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1521128788181&di=c83048a66f796c292d606cad56a78f0f&imgtype=0&src=http%3A%2F%2Fnews.cnhubei.com%2Fxw%2Fyl%2F201605%2FW020160516305305050803.png",
		defaultRadius	: 		"5",
		defaultColor : "#fff",
		iFontSize: "15px",
		uploadName: "upload-file",//上传文件名
		timeout : 30000,
		uploadType : "POST",
		url: "http://manager.erp2.com:8082/text/upload",
		getUrl: '',
		uploadData:{},
		getMusicParams:{},
		playType:{key:'loop',name:'循环',index:0},//当前播放模式、默认循环播放
		playTypes:[{key:'loop',name:'循环'},{key:'single',name:'单曲'},{key:'rand',name:'随机'}],//播放模式，循环、单曲循环、随机
		canvasSet:{radius:'50',ctx:null,canvasDom:null,center:null,startAngle:0,tmpAngle:0,endAngle:0,xAngle:0,width:50,height:50,bloder:3,color:'rgba(222,51,220,0.9)'}
	},
	//音频播放默认设置
	audioDefaultSetting:{
		volume:0.04,
		defaultPlaybackRate:1,
		loop:false,
		defaultMuted:true,
	},
	//音乐列表
	musicList:[],
	//音乐对象的键值
	musicKey:['name','singer','url'],
	musicCurrent:{//当前播放的音乐
	},
	error:"",
	acceptedFiles:"mp3,mp4",
	//下一首音乐
	musicNext:{},
	slider:"",//slider对象
	onloadXhr:{},
	importFile:{
		css:[],
		js:[]
	},
	/*
	可更改方法
	 */
	beforeMusicPlay:function(){},                               //音乐加载之前   可以播放之前
	duringPlay:function(){},                                    //音月播放时
	afterMusicLoading:function(){},                             //音乐加载成功  可播之后
	musicChanged:function(){},                                  //音乐切换之后，类似切歌
	getMusicInfo:function(){},                                  //获取所有音乐信息
	uploadMusic:function(){},                                   //上传音乐
	otherFunction:function(){},
	duringPlay:function(){},
	show:function () {},
	init:function () {},										//初始化，可更改
	/*
	不可更改区
	 */
	unvalidChangeSetting: {//不可更改配置 属性 方法
		prototypes:[],
		functions:['_init','on','extend','isInArray','createDom'],
	},
	musicDom: {
		container:'',//插件容器 初始化的element DOM
		cpt_music:'',//
		music_bg:"",//插件背景
		music_play:'',//播放区域
		music_blur:'',
		music_status:'',
		music_next:'',
		//进度条
		music_scroll:'',scroll_total:'',scroll_current:'',
		//音乐信息
		music_info:'',music_name:'',music_logo:'',music_img:'',music_progress:'',
		audio:'',
		/*
		设置区域
		*/
		set_div:'',
		//声音设置区域
		voice:'',voice_icno:'',voice_progress:'',voice_all:'',voice_current:'',voice_btn:'',
		//播放模式区域
		type:'',type_btn:'',
		//目录区域
		list:'',list_btn:'',music_list:'',list_ul:'',ul:'',list_dom:'',
		//上传区域
		upload_div:'',btn_div:"",upload_btn:"",upload_progress:"",upload_file:'',
	},
	oldDom:{},
	beforeInit:function (status) {
		if (false == status)return;
		var cssFiles = this.importFile.css;
		var jsFiles = this.importFile.js;
		var head = document.getElementsByTagName('head')[0];
		var cssDom,jsDom;
		for (var i = 0; i < cssFiles.length; i++) {
			cssDom = this.createBaseDom("link");
			cssDom.setAttribute("rel","stylesheet");
			cssDom.setAttribute("type","text/css");
			cssDom.setAttribute("href",cssFiles[i]);
			head.appendChild(cssDom);
		}
	},
	_init:function (options,music) {//对象初始化 不可更改
		this.setting = this.extend(this.setting,options);
		this.setMusciList(music);
		this.createDom();
		this.initAudio();
		this.setCss();
		if (this.musicList[0]) this.changeCurrentMusic(this.musicList[0]);
		this.setAttributes();
		this.init();
		this.beforeMusicPlay();
		this.setListenEvent();
	},
	setDefaultMusicList:function (options) {
		var _this = this;
		var xhr = new XMLHttpRequest();
		var url = options.getUrl; //"http://manager.erp2.com:8082/text/getMusicList";
		xhr.open("POST",url,true);
		xhr.onload = function (e) {
			var res;
			if (false === (res = _this.finishUpload(xhr,e))) return _this.handleError(res);
			_this._init(options,res.data || []);
		};
		var data = new FormData();
		if (options.getMusicParams != {}) {
			$.each(options.getMusicParams,function (e,i) {
				data.append(e,i);
			})
		}
		xhr.send(data);
	},
	/**
	 * 初始化audio对象
	 * @return {[type]} [description]
	 */
	initAudio:function () {
		
		var opt = this.setting;
		var audio = this.musicDom.audio;
		var _this = this;
		this.extend(audio,this.audioDefaultSetting);
		audio.ontimeupdate = function (currTime) {
			_this.audioTimeUpdate(currTime);
		}	
	},
	//音频播放事件
	audioTimeUpdate:function (currTime) {
		var audio = this.musicDom.audio;
		this.scrollUpdata();
		this.canvasRander();
	},
	scrollUpdata:function () {
		var audio = this.musicDom.audio;
		this.setStyle(this.musicDom.scroll_current,{width:(audio.currentTime/audio.duration)*100+"%"})
	},
	/**
	 * 设置dom监听事件
	 */
	setListenEvent:function () {
		var _this = this;
		//切换播放界面
		_this.musicDom.cpt_music.addEventListener("click",function (e) {
			_this.cptClick(this);
		});
		//播放暂停切换
		_this.musicDom.music_status.addEventListener("click",function (e) { 
			_this.forbidBubble(e);
			_this.musicPalyPause(); 
		});
		//播放下一首
		_this.musicDom.music_next.addEventListener("click",function (e) {
			_this.forbidBubble(e);
		 	_this.playNext(); 
		});
		//取消冒泡
		_this.musicDom.set_div.addEventListener("click",function (e) {
			_this.forbidBubble(e);
		});
		//调音
		_this.musicDom.voice.addEventListener("click",function (e) {
			_this.slideToggle(_this.musicDom.voice_progress,500);
			var current = _this.musicDom.voice_current;
			var voice_btn_css = {
				height:current.clientWidth*3 + "px",
				width:current.clientWidth*3 + "px",
				left:current.clientWidth + "px",
			};
			 _this.setStyle(_this.musicDom.voice_btn,voice_btn_css);
		});
		_this.musicDom.voice_progress.addEventListener("click",function (e) {
			_this.forbidBubble(e);
		})
		_this.moveProgress(_this.musicDom.voice_btn,_this.musicDom.voice_all,_this.musicDom.voice_current,"y",_this.changeVoice);
		//目录
		_this.musicDom.list.addEventListener("click",function (e) {
			_this.slideToggle(_this.musicDom.music_list,500);
		});
		//上传音乐
		_this.musicDom.btn_div.addEventListener("click",function (e) {
			//_this.forbidBubble(e);
			_this.musicDom.upload_file.click();
		});
		_this.musicDom.upload_file.addEventListener('click',function (e) {
			_this.forbidBubble(e);
		})
		_this.musicDom.upload_file.addEventListener("change",function (e) {
			_this.uploadMusic(e);
		})
		//音乐播放事件
		_this.audioPlayEvent();
		_this.musicDom.music_info.addEventListener("mouseover",function (e) {
			this.addEventListener("click",function (e) {
				_this.forbidBubble(e);
			});
			//_this.musicDom.cpt_music.style.setProperty("transition","all 0s ease");
			this.style.setProperty("cursor","move");
		})
		//切换播放模式
		_this.musicDom.type.addEventListener("click",function (e) {
			_this.setPlayType();
		})
		_this.move(_this.musicDom.music_info,_this.musicDom.cpt_music);
		
	},
	uploadMusic:function (event) {
		var file = this.musicDom.upload_file.files[0];
		if (!file) return;
		if (false == this.isAllowUpload(file)) {
			return this.handleError(this.getError());
		}
		var xhr,_this = this;
		if (window.XMLHttpRequest) {// code for all new browsers
		  xhr = new XMLHttpRequest();
		} else if (window.ActiveXObject) {// code for IE5 and IE6
		  xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
		//xhr.onreadystatechange = this.uploadUpdate(xhr);
		var method = this.setting.uploadType;
		var data = this.getUploadData();
		data.append(this.setting.uploadName,file);
		data.append("uploadName",this.setting.uploadName);
		var url = this.setting.url;
		//open(method, url, async, username, password)
		xhr.open(method,url,true);
		xhr.timeout = this.setting.timeout;
		xhr.onload = function (e) {//请求完成
			_this.musicDom.upload_progress.upload_text.innerText = "上传完成";
			_this.onloadXhr.upload = '';
			var res;
			if (false == (res = _this.finishUpload(xhr,e))) return _this.handleError(res);
			_this.uploadSuccess(res);
		}
		xhr.onerror = function (e) {
			_this.onloadXhr.upload = '';
			_this.error = e.responseText;
		}	
		var xhrSet = {
			ot:'',
			ol:0,
		};
		xhr.upload.onloadstart = function () {//上传开始方法
			xhrSet.ot = new Date().getTime();//上传开始时间
			xhrSet.ol = 0;//设置上传开始时，以上传的文件大小为0
		}
		var progressObj = xhr.upload != null ? xhr.upload : xhr;
		progressObj.onprogress = function (e) {
        	return _this.updateUploadProgress(xhr, e,xhrSet);
      	};
      	xhr.send(data);
      	this.onloadXhr.upload = xhr;
	},
	isAllowUpload:function (file) {
		if (false == this.isValidFile(file)) return false;
		if (!this.setting.url) {
			this.error = "无上传地址";
			return false;
		}
		if (this.onloadXhr.upload) {
			this.error = "有其他文件正在上传";
			return false;
		}
		return true;
	},
	isValidFile:function (file) {
		if (!file) {
			this.error = "请选择需要上传的文件";
			return false;
		}
		var validFiles = this.acceptedFiles.split(",");
		var fileType = file.type;
		var baseFileType = fileType.replace(/.*\//, "");
		var valid = false;
		for (var i = 0;i < validFiles.length;i ++) {
			if (baseFileType.toLowerCase() == validFiles[i].toLowerCase()) {
				valid = true;
				break;
			}
		}
		if (valid) return true;
		this.error = "请选择正确的音频文件";
		return false;
	},
	finishUpload:function (xhr,e) {
		var response = 0;
		if (xhr.readyState !== 4) return;
		if (xhr.responseType !== 'arraybuffer' && xhr.responseType !== 'blob') {
			response = xhr.responseText;
			if (xhr.getResponseHeader("content-type") && ~xhr.getResponseHeader("content-type").indexOf("application/json")) {
				try {
				  response = JSON.parse(response);
				} catch (error) {
				  e = error;
				  response = "Invalid JSON response from server.";
				}
			}
		}
		if (!(200 <= xhr.status && xhr.status < 300)) {
			this.error = response;
			return false;
		  //this.handleError(response);
		} else {
			return response;
		 // this.uploadSuccess(response, e);
		}
	},
	uploadSuccess:function (res) {
		if (res.status) return this.addMusic(res.data);
		this.handleError(res.info);
	},
	addMusic:function (music) {
		if (false == this.musicFilter(music)) {
			return this.handleError("后台传入音乐格式错误");
		}
		//向音乐对象添件key键值及元素
		music.key = music.url+Math.random()*10;
		music.index = this.musicList.length;
		music.bg = music.bg?music.bg:this.setting.defaultBackground,
		music.logo = music.logo?music.logo:this.setting.defaultLogo,
		this.musicList.push(music);
		this.addListDom(music);
	},
	addListDom:function (music) {
		var _this = this;
		var music_li = this.createBaseDom("li",'normal');
		music_li.setAttribute("url",music.url);
		music_li.setAttribute("index",music.index);
		music_li.setAttribute("key",music.key);
		music_li.innerText = music.name + "--" + music.singer;
		this.musicDom.ul.appendChild(music_li);
		this.musicDom.list_dom[music.key] = music_li;
		var oh = this.getStyle(this.musicDom.music_list,"height").replace(/px$/,"");
		this.musicDom.music_list.style.setProperty("height",oh*1 + music_li.clientHeight*1 + "px");
		music_li.addEventListener("click",function (e) {
			if (this.getAttribute("key") == _this.musicCurrent.key) return false;
			var index  = this.getAttribute("index");
			var toMusic = _this.musicList[index];
			_this.changeCurrentMusic(toMusic);
			_this.musicPlay();
			_this.musicDom.list.click();
		})
	},
	updateUploadProgress:function (xhr,event,xhrSet) {
		var total = event.total/1024/1024;
		var loaded = event.loaded/1024/1024;
		var rate = (loaded/total)*100;
		this.musicDom.upload_progress.upload_cur.style.setProperty("width",rate + "%"); 
		var nt = new Date().getTime();
		var pertime = (nt - xhrSet.ot)/1000;//计算时间差 单位s
		xhrSet.ot = nt;
		var perloaded = event.loaded - xhrSet.ol;
		xhrSet.ol = event.loaded;
		//计算速度
		var speed = perloaded/pertime;
		var unit = "B/s";//速度单位
		if (speed > 1024) {
			speed  = speed/1024;
			unit = "KB/s";
		} 
		if (speed > 1024) {
			speed = speed/1024;
			unit = "M/s";
		}
		var sizeText = loaded.toFixed(2) + "M/" + total.toFixed(2) + "M ";
		var rateText = rate.toFixed(2) + "% ";
		var speedText = speed.toFixed(2) + unit;
		var totalText = sizeText + rateText + speedText;
		this.musicDom.upload_progress.upload_text.innerHTML = totalText;
	},
	getUploadData:function () {
		var data = new FormData();
		for (var key in this.setting.uploadData) {
			data.append(key,this.setting.uploadData[key]);
		}
		return data;
	},
	/**
	 * 移动改变进度（调声音大小，音乐进度等）
	 * @param  {[type]} btn 调整按钮控件元素
	 * @param  {[type]} all 全部进度元素
	 * @param  {[type]} cur 目前进度元素
	 * @param  {[type]} axle 调整方向 X轴或Y轴
	 * @param  {[type]} callback 移动同时进行的操作
	 * @return {[type]} 
	 */
	moveProgress:function (btn,all,cur,axle = 'y',callback) {
		var _this = this;
		var distance = 0;
		var isY = (axle.toLowerCase() == "y")?true:false;
		var hasTrans = false;//移动目标的过渡属性
		btn.addEventListener("mousedown",function (e) {
			hasTrans = _this.getStyle(btn,"transition");
			if (hasTrans) {//去除移动目标的国度属性
				btn.style.setProperty("transition","all 0s ease");
			}
			distance = isY?(e.clientY - btn.offsetTop):(e.clientX - btn.offsetLeft);
			var move = function (e) {
				var result = isY?(e.clientY - distance):(e.clientX - distance);
				if (isY) {
					if (result < (all.offsetTop - btn.clientHeight/2)) {
						result = all.offsetTop - btn.clientHeight/2;
					} else if (result > (all.offsetTop + all.clientHeight - btn.clientHeight/2)) {
						result = all.offsetTop + all.clientHeight - btn.clientHeight/2;
					}
				} else {
					if (result < (all.offsetLeft - btn.clientWidth/2)) {
						result = all.offsetLeft - btn.clientWidth/2;
					} else if (result > (all.offsetLeft + all.clientWidth - btn.clientWidth/2)) {
						result = all.offsetLeft + all.clientWidth - btn.clientWidth/2;
					}
				}
				var curChange = isY?(result + btn.clientHeight/2 - cur.offsetTop):(result + btn.clientWidth/2 - cur.offsetLeft);
				var btn_css = isY?{top:result + "px"}:{left:result + "px"};
				var cur_css = isY?{height:curChange + "px"}:{width:curChange + "px"};
				_this.setStyle(btn,btn_css);
				_this.setStyle(cur,cur_css);
				if (callback && callback instanceof Function) {
					callback(btn,all,cur,axle,_this);
				}
			}
			this.addEventListener("mousemove",move);
			this.addEventListener("mouseout",function () {
				if (hasTrans) btn.style.setProperty("transition",hasTrans);//恢复过渡属性
				this.removeEventListener("mousemove",move);
			});
			this.addEventListener("mouseup",function (e) {
				if (hasTrans) btn.style.setProperty("transition",hasTrans);//恢复过渡属性
				this.removeEventListener("mousemove",move)
			})
		})
	},
	changeVoice:function (btn,all,cur,axle,_this) {
		var allH = all.clientHeight;
		var curH = cur.clientHeight;
		var rate = curH/allH;
		if (rate > 1) rate = 1;
		if (rate < 0) rate = 0;
		_this.musicDom.audio.volume = rate;
	},
	/**
	 * 切换播放模式
	 * @DateTime 2018-08-03T10:47:11+0800
	 */
	setPlayType:function () {
		var currentType = this.setting.playType;
		var types = this.setting.playTypes;
		var nextTypeIndex = parseInt(currentType.index) + 1;
		nextTypeIndex = (nextTypeIndex > (types.length - 1))? 0 : nextTypeIndex; 
		var nextType = this.setting.playTypes[nextTypeIndex];
		nextType.index = nextTypeIndex;
		this.setting.playType = nextType;
		this.musicDom.type.innerHTML = nextType.name;
		this.changeNextMusic(this.musicCurrent);
	},
	/**
	 * 拖拽改变元素位置
	 * @param  {[type]} eventDom 鼠标作用拖动的元素
	 * @param  {[type]} moveDom  需要拖动的元素
	 * @return {[type]}          [description]
	 */
	move:function (eventDom,moveDom) {
		var _this = this;
		var distanceY = 0;
		var distanceX = 0;
		var hasTrans = false;//移动目标的过渡属性
		eventDom.addEventListener("mousedown",function (e) {
			hasTrans = _this.getStyle(moveDom,"transition");
			if (hasTrans) {//去除移动目标的国度属性
				moveDom.style.setProperty("transition","all 0s ease");
			}
			distanceY = e.clientY - moveDom.offsetTop;
			distanceX = e.clientX - moveDom.offsetLeft;
			var move = function (e) {
				var Y = e.clientY - distanceY;
				var X = e.clientX - distanceX;
				_this.setStyle(moveDom,{top:Y + "px",left:X + "px"});
			}
			this.addEventListener("mousemove",move);
			this.addEventListener("mouseup",function (e) {
				if (hasTrans) moveDom.style.setProperty("transition",hasTrans);//恢复过渡属性
				this.removeEventListener("mousemove",move)
			})
			this.addEventListener("mouseout",function (e) {
				if (hasTrans) moveDom.style.setProperty("transition",hasTrans);
				this.removeEventListener("mousemove",move)
			})
		})
	},
	cptClick:function (element) {
		this.toggleClass(element,'circle');
		var className = element.className;
		if (className.indexOf("circle") == -1) {
			this.setStyle(element,{
				width:this.setting.width + "px",
				height:this.setting.height + "px",
				left:element.offsetLeft-(this.setting.width/2 - this.setting.height/2) + "px",
				"border-radius":this.setting.defaultRadius + "px",
				transition:"all 0.3s ease",
			});
			$(this.musicDom.music_bg).show();
		} else {
			this.setStyle(element,{
				width:this.musicDom.music_logo.clientWidth + "px",
				height:this.musicDom.music_logo.clientHeight + "px",
				left:element.offsetLeft+(element.clientWidth/2 - element.clientHeight/2) +"px",
				"border-radius":"50%",
				transition:"all 0.3s ease",
			});
			$(this.musicDom.music_bg).hide();
		}
	},
	audioPlayEvent:function () {
		var _this = this;
		//播放完全
		this.musicDom.audio.addEventListener("ended",function (e) {
			_this.musicEnd();
		});
		//播放中
		this.musicDom.audio.addEventListener("playing",function (e) {
			
		});
		this.musicDom.audio.addEventListener('canplay',function(){
        });
	},
	/**
	 * 音乐播放完全
	 * @return {[type]} [description]
	 */
	musicEnd:function () {
		var audio = this.musicDom.audio;
		if (audio.loop) {
			this.changeCurrentMusic(this.musicCurrent);
			this.musicPlay();
		} else {
			this.playNext();
		}
	},
	/**
	 * 改变当前播放音乐
	 * @param  {[type]} music 即将播放的音乐
	 * @return {[type]}       [description]
	 */
	changeCurrentMusic:function (music) {
		if (!(music instanceof Object)) throw new Error("the music changed is not a object!");
		if (false == this.musicFilter(music)) throw new Error("the conatruct of music changed is not valid!");
		this.musicCurrent = music;
		this.musicDom.audio.src = music.url;
		this.changeMusicSet(music);
		this.changeNextMusic(music);
		this.changeListCss(music);
		this.changeMusicInfo(music);
		this.changeProgress(0);
		this.initCanvas();
	},
	initCanvas:function () {
		var dom = this.musicDom.music_progress;
		var canvasSet = this.setting.canvasSet;
		var audio = this.musicDom.audio;
		//this.setStyle(this.musicDom.scroll_current,{width:(audio.currentTime/audio.duration)*100+"%"})
		canvasSet.canvasDom = dom;
		canvasSet.ctx = dom.getContext('2d');
		var height = 56;
		canvasSet.height = height;
		canvasSet.width = height;
		canvasSet.center = height/2;
		canvasSet.radius = canvasSet.center - canvasSet.bloder + 1;
		canvasSet.startAngle = -(1 / 2 * Math.PI);
	},
	canvasRander:function () {
		var set = this.setting.canvasSet;
		var audio = this.musicDom.audio;
		var tmpAngle = set.startAngle + (audio.currentTime/audio.duration) * 2 * Math.PI;
		set.ctx.clearRect(0, 0, set.width, set.height);

		//画圈
		set.ctx.beginPath();
		set.ctx.lineWidth = set.bloder;
		set.ctx.strokeStyle = set.color;
		set.ctx.arc(set.center, set.center, set.radius, set.startAngle, tmpAngle);
		set.ctx.stroke();
		set.ctx.closePath();
	},
	changeMusicSet:function (music) {
		var bg_css = {
			"background-image"	: "url(" + music.bg + ")",
		};
		this.setStyle(this.musicDom.music_bg,bg_css);
		this.musicDom.music_img.src = music.logo;
	},
	/**
	 * 改变下一首播放的音乐
	 * @param  {[type]} music 当前播放的音乐
	 * @return {[type]}       [description]
	 */
	changeNextMusic:function (music) {
		if (music.hasOwnProperty("index") == false) return false;
		var nextIndex = this.getNextIndex(music);
		this.musicNext = this.musicList[nextIndex];
	},
	/**
	 * 获取下一首歌曲的索引
	 * @DateTime 2018-08-03T09:59:32+0800
	 * @param    {[type]}                 $currentIndex [当前歌曲]
	 * @return   {[type]}                               [description]
	 */
	getNextIndex:function (currentMusic)
	{
		if (!currentMusic) return 0;
		var currentIndex = currentMusic.index;
		var playType = this.setting.playType.key;
		var musicLength = this.musicList.length;
		var nextIndex = 0;
		switch (playType) {
			case 'loop' :
				nextIndex = parseInt(currentIndex) + 1;
				break;
			case 'single' :
				nextIndex = currentIndex;
				break;
			case 'rand' :
				nextIndex = parseInt(Math.random()*parseInt(musicLength));
				break;
		}
		nextIndex = (nextIndex > (musicLength - 1)) ? 0 : nextIndex;
		return nextIndex;
	},
	//音乐播放暂停事件
	musicPalyPause () {
		var audio = this.musicDom.audio;
		if (audio.paused) {
			this.musicPlay();
		} else {
			this.musicPause();
		}
	},
	musicPlay () {
		this.musicDom.music_logo.children[0].style.setProperty("animation","rotateMusicLogo 8s linear infinite");
		this.musicDom.music_status.children[0].className = "dw-icon-pause";
		this.musicDom.audio.play();
	},
	musicPause () {
		this.musicDom.music_logo.children[0].style.setProperty("animation","");
		this.musicDom.music_status.children[0].className = "dw-icon-play";
		this.musicDom.audio.pause();
	},
	//播放下一首
	playNext:function () {
		this.changeCurrentMusic(this.musicNext || {});
		this.musicPlay();
	},
	/**
	 * 修改音乐信息DOM(music_info)的信息
	 * @param  {[type]} music [description]
	 * @return {[type]}       [description]
	 */
	changeMusicInfo:function (music) {
		if (false == this.musicFilter(music)) throw new Error("the conatruct of music changed is not valid!");
		this.musicDom.music_name.innerText = music.name;
		this.musicDom.music_singer.innerText = music.singer;
	},
	changeProgress:function (rate) {
		this.musicDom.scroll_current.style.setProperty("width",rate+"%");
	},
	setCss:function () {
		var container = this.musicDom.container;
		//cpt_music CSS
		var cpt_music_css = {
			"color"	: 	this.setting.defaultColor,
			"border-radius"		: this.setting.defaultRadius + "px",
		};
		this.setStyle(this.musicDom.cpt_music,cpt_music_css);
		// var bg_css = {
		// 	"background-image"	: "url(" + this.musicCurrent.bg + ")",
		// };
		// this.setStyle(this.musicDom.music_bg,bg_css); 
		//play-div CSS
		this.musicDom.music_play.style.setProperty("border-radius",this.setting.defaultRadius + "px");
		this.musicDom.music_status.style.setProperty("width",this.musicDom.music_status.clientHeight + "px");
		this.musicDom.music_next.style.setProperty("width",this.musicDom.music_next.clientHeight + "px") ;
		this.musicDom.set_div.style.setProperty("width",this.musicDom.cpt_music.clientWidth*0.3 + "px");
		//scroll-div CSS
		var total_css = {
			"box-shadow" : "0 0 10px 1px " + this.setting.totalProBg,
		};
		this.setStyle(this.musicDom.scroll_total,total_css);
		var current_css = {
			"box-shadow" : "0 0 10px 3px " + this.setting.currProBg,
			"border-right" : "1px solid #fff",
		}
		this.setStyle(this.musicDom.scroll_current,current_css);
		//set-div CSS
		this.musicDom.set_div.style.setProperty("height",this.musicDom.cpt_music.clientHeight + "px");
		this.musicDom.voice.style.setProperty("width",this.musicDom.voice.clientHeight + "px") ;
		this.musicDom.voice.style.setProperty("margin-top",this.musicDom.voice.clientHeight*0.5 + "px") ;
		this.musicDom.type.style.setProperty("width",this.musicDom.type.clientHeight + "px") ;
		this.musicDom.type.style.setProperty("margin-top",this.musicDom.type.clientHeight*0.5 + "px") ;
		this.musicDom.list.style.setProperty("width",this.musicDom.list.clientHeight + "px") ;
		this.musicDom.list.style.setProperty("margin-top",this.musicDom.list.clientHeight*0.5 + "px");
		this.musicDom.music_list.style.setProperty("width",this.musicDom.cpt_music.clientWidth + "px");
		var music_logo_css = {
			height:this.musicDom.cpt_music.clientHeight + "px",
			width:this.musicDom.cpt_music.clientHeight + "px",
			//left:(this.musicDom.cpt_music.clientWidth/2 - this.musicDom.cpt_music.clientHeight) + "px",
		};
		//upload-div
		//this.musicDom.btn_div.style.setProperty("width",this.musicDom.voice.clientHeight + "px");
		this.musicDom.upload_progress.upload_text.innerText = "0/0 0% 0B/s";
		this.setStyle(this.musicDom.music_logo,music_logo_css);
		if (this.setting.hasBlur) {
			var img_css = {
				//'filter' : "blur(1px)",
			};
			this.setStyle(this.musicDom.music_img,img_css);
		}
		//canvas
		this.musicDom.music_progress.height = 56;
		this.musicDom.music_progress.width = 56;
	},
	/**
	 * 播放音乐改变时修改对应的目录下的音乐样式
	 * @param  {[type]} music 播放音乐对象
	 * @return {[type]}       [description]
	 */
	changeListCss:function (music) {
		if (false == this.musicFilter(music)) throw new Error("the conatruct of music changed is not valid!");
		for (var key in this.musicDom.list_dom) {
			this.musicDom.list_dom[key].setAttribute("class","normal");
			if (key == music.key) this.musicDom.list_dom[key].setAttribute("class","checked");
		}
	},
	/**
	 * 给DOM元素设置属性
	 */
	setAttributes:function () {
		this.musicDom.audio.setAttribute("crossOrigin","anonymous");
		this.musicDom.music_progress.setAttribute("id","music_canvas");
		this.musicDom.upload_file.setAttribute("type","file");
		this.musicDom.upload_file.setAttribute("name",this.setting.uploadName);
		this.musicDom.type.innerHTML = this.setting.playType.name;
	},
	createDom:function () {//创建插件DOM
		var bodyDom = document.body;
		var opt = this.setting;
		// var script = this.createBaseDom("script");
		// script.setAttribute("src","dropzone.js");
		// script.setAttribute("type","tex/javascript");
		// bodyDom.insertBefore(script,bodyDom.firstChild);
		this.setContainer();
		this.setCptMusic();
		this.musicDom.music_bg = this.createBaseDom("div","music_bg");
		this.musicDom.music_play = this.createBaseDom("div","music-play-div");
		if(opt.hasBlur){
			this.musicDom.music_blur = this.createBaseDom("div","filterBg");
		}
		var css2 = {
			height:opt.keyAreaStyle.height,
		};
		var i_css = {
			"font-size"	: this.setting.keySize,
		};
		this.musicDom.music_status = this.createBaseDom("div","pauseplay",css2);
		this.musicDom.music_status.appendChild(this.createBaseDom("i","dw-icon-play",i_css));
		this.musicDom.music_next = this.createBaseDom("div","next",css2);
		this.musicDom.music_next.appendChild(this.createBaseDom("i","dw-icon-next",i_css));
		//进度条
		this.musicDom.music_scroll = this.createBaseDom("div","music_scroll",{width:"20%"});
		this.musicDom.scroll_total = this.createBaseDom("span","total",{width:"20%",background:opt.totalProBg});
		this.musicDom.scroll_current = this.createBaseDom("sapan","current",{background:opt.currProBg});
		//音乐信息
		this.musicDom.music_info = this.createBaseDom("div","music-info",{width:"20%"});
		this.musicDom.music_name = this.createBaseDom("p",'music-name');
		this.musicDom.music_name.textContent = opt.defaultName;
		this.musicDom.music_singer = this.createBaseDom("p","music-singer");
		//圆形转动区域
		this.musicDom.music_logo = this.createBaseDom("div","music-div-logo");
		this.musicDom.music_img = this.createBaseDom("img","music-logo");
		//canvas圆形进度条
		this.musicDom.music_progress = this.createBaseDom("canvas");
		this.musicDom.audio = this.createBaseDom("audio");
		this.musicDom.audio.id = "cpt_dw_music";
		//设置区域
		this.musicDom.set_div = this.createBaseDom("div","set-div");
		//声音设置
		this.musicDom.voice = this.createBaseDom("div","voice-div",css2);
		this.musicDom.voice_icno = this.createBaseDom("i","dw-icon-voice",i_css);
		//声音滚动条
		this.musicDom.voice_progress = this.createBaseDom("div","voice_progress",opt.voiceProgress);
		this.musicDom.voice_all = this.createBaseDom("div","all-progress",{width:"30%"});
		this.musicDom.voice_current = this.createBaseDom("div","current-progress",{height:opt.defaultVoice*100+"%"});
		var current = this.musicDom.voice_current;
		var css4 = {
			height:current.clientWidth*5 + "px",
			width:current.clientWidth*5 + "px",
			left:current.clientWidth*1.75 + "px",
			top:(current.hclientHeight-current.clientWidth) + "px",
		};
		this.musicDom.voice_btn = this.createBaseDom("span","progress-button",css4);
		//播放模式
		this.musicDom.type = this.createBaseDom('div','type-div',css2);
		//目录
		this.musicDom.list = this.createBaseDom("div","list-div",css2);
		this.musicDom.list_btn = this.createBaseDom("i","dw-icon-list",i_css);
		this.musicDom.music_list = this.createBaseDom("div","music-list",opt.listStyle);
		this.musicDom.list_ul = this.createBaseDom("div",'list-ul');
		this.musicDom.ul = this.createBaseDom("ul");
		//上传
		this.musicDom.upload_div = this.createBaseDom("div","upload-div");
		var uploadProgress = {
			upload_progress : this.createBaseDom("div","upload-progress"),
			upload_all:this.createBaseDom("div","progress-all"),
			upload_cur:this.createBaseDom("span","progress-cur"),
			upload_text:this.createBaseDom("div","progress-text"),
		}
		this.musicDom.upload_progress = uploadProgress;
		this.musicDom.btn_div = this.createBaseDom("div","btn-div");
		this.musicDom.upload_btn = this.createBaseDom("div","upload-btn");
		this.musicDom.upload_file = this.createBaseDom("input","upload-file",{display:"none"});
		//DOM链接
		var musicDom = this.musicDom;
		// bodyDom.appendChild(musicDom.container);
		musicDom.container.appendChild(musicDom.cpt_music);
		musicDom.cpt_music.appendChild(musicDom.music_bg);
		musicDom.cpt_music.appendChild(musicDom.music_play);
		musicDom.cpt_music.appendChild(musicDom.music_blur);
		musicDom.music_play.appendChild(musicDom.music_status);
		musicDom.music_play.appendChild(musicDom.music_next);
		//进度条
		musicDom.music_play.appendChild(musicDom.music_scroll);
		musicDom.music_scroll.appendChild(musicDom.scroll_total);
		musicDom.scroll_total.appendChild(musicDom.scroll_current);
		//音乐信息
		musicDom.music_play.appendChild(musicDom.music_info);
		musicDom.music_info.appendChild(musicDom.music_name);
		musicDom.music_info.appendChild(musicDom.music_singer);
		musicDom.cpt_music.appendChild(musicDom.music_logo);
		musicDom.music_logo.appendChild(musicDom.music_img);
		musicDom.music_logo.appendChild(musicDom.music_progress);
		bodyDom.appendChild(musicDom.audio);
		//设置区域
		musicDom.music_play.appendChild(musicDom.set_div);
		//声音设置
		musicDom.set_div.appendChild(musicDom.voice);
		musicDom.voice.appendChild(musicDom.voice_icno);
		musicDom.voice.appendChild(musicDom.voice_progress);
		musicDom.voice_progress.appendChild(musicDom.voice_all);
		musicDom.voice_all.appendChild(musicDom.voice_current);
		musicDom.voice_current.appendChild(musicDom.voice_btn);
		//播放模式切换
		musicDom.set_div.appendChild(musicDom.type);
		//目录
		musicDom.set_div.appendChild(musicDom.list);
		musicDom.list.appendChild(musicDom.list_btn);
		musicDom.set_div.appendChild(musicDom.music_list);
		musicDom.music_list.appendChild(musicDom.list_ul);
		musicDom.list_ul.appendChild(musicDom.ul);
		//上传
		musicDom.music_list.insertBefore(musicDom.upload_div,musicDom.music_list.firstChild);
		musicDom.upload_div.appendChild(musicDom.upload_progress.upload_progress);
		musicDom.upload_progress.upload_progress.appendChild(musicDom.upload_progress.upload_text);
		musicDom.upload_progress.upload_progress.appendChild(musicDom.upload_progress.upload_all);
		musicDom.upload_progress.upload_all.appendChild(musicDom.upload_progress.upload_cur);
		musicDom.upload_btn.appendChild(musicDom.btn_div);
		musicDom.upload_div.appendChild(musicDom.upload_btn);
		musicDom.btn_div.appendChild(this.createBaseDom("i","dw-icon-add",i_css))
		musicDom.cpt_music.appendChild(musicDom.upload_file);
		musicDom.list_dom = {};
		if (this.musicList) {
			for (var key in this.musicList) {

				this.addListDom(this.musicList[key]);
				// var music_li ;
				// music_li = this.createBaseDom("li",'normal');
				// music_li.setAttribute("url",this.musicList[key].url);
				// music_li.setAttribute("index",key);
				// music_li.setAttribute("key",this.musicList[key].key);
				// music_li.innerText = this.musicList[key].name + "--" + this.musicList[key].singer;
				// musicDom.ul.appendChild(music_li);
				// musicDom.list_dom[this.musicList[key].key] = music_li;
			}
				
		}
	},
	setMusciList:function (sourceMusci) {//将传入的music对象合并到此类中
		if (!sourceMusci) return false;
		if (!(sourceMusci instanceof Array)) throw new Error("sourceMusci need a type od Array");
		var length = sourceMusci.length;
		var musicObj;
		for (var i = 0;i < length;i ++) {
			musicObj = sourceMusci[i];
			if (false == this.musicFilter(musicObj)) break;
			//向音乐对象添件key键值及元素
			musicObj['key'] = musicObj.url+Math.random()*10;
			musicObj['index'] = i;
			musicObj['bg'] = musicObj.bg?musicObj.bg:this.setting.defaultBackground,
			musicObj['logo'] = musicObj.logo?musicObj.logo:this.setting.defaultLogo,
			this.musicList.push(musicObj);
		}
	},
	musicFilter:function (music) {//音乐对象过滤不符合条件的音乐对象
		if (!(music instanceof Object)) return false;
		var musicProperty = this.getObjecProperty(music);
		var keyLength = this.musicKey.length;
		for (var i = 0;i < keyLength;i ++) {
			if (!this.isInArray(musicProperty,this.musicKey[i]))return false;
		}
		return true;
	},
	setContainer:function () {
		var element = this.element;
		if (element.indexOf(".") === 0) {
			element = document.getElementsByClassName(element.substring(1))[0];
		} else if (element.indexOf("#") === 0) {
			element = document.getElementById(element.substring(1));
		}
		this.musicDom.container = this.element = element;
	},
	setCptMusic:function () {
		var opt = this.setting;
		var	css = {
			width:opt.width + "px",
			height:opt.height +"px",
		};
		var cpt_music = this.createBaseDom("div","cpt-dw-music music-div active",css);
		this.musicDom.cpt_music = cpt_music;
	},
	createBaseDom:function (element,className,css) {
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
	},
	on:function (name,callback) {//添加可更改方法
		if (!(callback instanceof Function)) throw new Error(name + " is not a function");
		if (!(this[name] instanceof Function)) throw new Error("MusicPlay has not a function named " + name);
		if (this.isInArray(this.unvalidChangeSetting.functions,name))throw new Error(name + " is not a valid change function");
		this[name] = callback;
	},
	extend:function (target,source) {//配置合并
		for (var key in target) {
			if (source.hasOwnProperty(key) === true) { //判断source对象
				if (!this.isInArray(this.unvalidChangeSetting.prototypes,key))
					target[key] = source[key]
			}
		}
		return target;
	},
	setStyle:function (element,css) {
		if (css instanceof Object) {
			for (var key in css) {
				if (element.style.hasOwnProperty(key)) {
					element.style.setProperty(key,css[key]);
				}
			}
		}
		return element;
	},
	/**
	 * 获取元素对象style属性 包括外部引入的属性
	 * @param  {[type]} element 元素对象
	 * @param  {[type]} name    style 属性名 可填
	 * @return {[type]}         [description]
	 */
	getStyle:function (element,name) {
		if(typeof getComputedStyle) {
			var all = getComputedStyle(element,null);
			return name?all[name]:all;
		} else {
			var all = element.currentStyle;
			return name?all[name]:all;
		}
	},
	isInArray:function (arr,value) {//判断数组中元素是否存在
		var l = arr.length;
		for (var i = 0;i < l;i ++) {
			if (arr[i] == value) {
				return true;
			}
		}
		return false;
	},
	getObjecProperty:function (object) {//获取对象的全部属性 返回对象属性数组
		if (!(object instanceof Object)) return [];
		var prototype = [];
		for (var key in object) {
			if (object.hasOwnProperty(key))
				prototype.push(key);
		}
		return prototype;
	},
	//禁止事件冒泡
	forbidBubble:function (e) {
		//如果提供了事件对象，则这是一个非IE浏览器 
		if ( e && e.stopPropagation ) 
    		//因此它支持W3C的stopPropagation()方法 
    		e.stopPropagation(); 
		else
    		//否则，我们需要使用IE的方式来取消事件冒泡 
   		 	window.event.cancelBubble = true; 
	},
	//
	/*
	模仿jquery toggleClass
	 */
	toggleClass:function (element,name) {
		if (this.hasClass(element,name)) {
			element = this.removeClass(element,name);
		} else {
			element = this.addClass(element,name);
		}
		return element;
	},
	hasClass:function (element,name) {
		return element.className.match(new RegExp('(\\s|^)' + name + '(\\s|$)'));
	},
	addClass:function (element,name) {
		if (!this.hasClass(element, name)) element.className += " " + name;  
		return element;
	},
	removeClass:function (element,name) {
		if (this.hasClass(element, name)) {  
		    var reg = new RegExp('(\\s|^)' + name + '(\\s|$)');  
		    element.className = this.trim(element.className.replace(reg, ' '));  
	 	}  
	 	return element;
	},
	trim:function (str) {
		if (typeof(str) !== "string") return str;
   		return str.replace(/^\s+|\s+$/gm,'');
	},
	getError:function () {
		return this.error;
	},
	handleError:function (error) {
		alert(error);
	},
	/*
	模仿jquery slideToggle
	 */
	initSlider:function (element,time) {
		var _this = this;
		this.slider = {};
		function TimerManager () {
			this.timers = [];
			this.args = [];
			this.isFiring = false;
		}
		TimerManager.makeInstance = function (element) {
			if (!element.__TimerManager__ || element.__TimerManager__.constructor != TimerManager) {
				element.__TimerManager__ = new TimerManager();
			}
		}
		TimerManager.prototype.add = function (timer,args) {
			this.timers.push(timer);
			this.args.push(args);
			this.fire();
		}
		TimerManager.prototype.fire = function () {
			if (!this.isFiring) {
				var timer = this.timers.shift();
				var	args = this.args.shift();
				if (timer && args) {
					timer(args[0],args[1]);
				}
			}
		}
		TimerManager.prototype.next = function () {
			this.isFiring = false;
			this.fire();
		} 
		function fnSlideDown (element,time) {
			if (element.offsetHeight == 0) {
				element.style.setProperty("display","block");
				var borderWidth = _this.getStyle(element,"border-width").replace(/px$/,"");
				var totalHeight = element.offsetHeight - (borderWidth?borderWidth*2:0);//可视高度 - 边宽*2
				element.style.setProperty("height","0px");
				var currentHeight = 0;
				var increment = totalHeight/(time/10);
				var timer = setInterval(function () {
					currentHeight = currentHeight + increment;
					if (currentHeight >= totalHeight) {
						clearInterval(timer);
						element.style.setProperty("height",totalHeight + 'px');
						if (element.__TimerManager__ && element.__TimerManager__.constructor == TimerManager) {
							element.__TimerManager__.next();
						}
					} else {
						element.style.setProperty("height",currentHeight + "px");
					}
				},10)
			} else {
				if (element.__TimerManager__ && element.__TimerManager__.constructor == TimerManager) {
					element.__TimerManager__.next();
				}
			}
		}
		function fnSlideUp (element,time) {
			if (element.offsetHeight > 0) {
				var borderWidth = _this.getStyle(element,"border-width").replace(/px$/,"");
				var totalHeight = element.offsetHeight - (borderWidth?borderWidth*2:0);//可视高度 - 边宽*2
				var currentHeight = totalHeight;
				var decrement = totalHeight / (time/10);
				var timer = setInterval(function() {
					currentHeight = currentHeight - decrement;
					
					if (currentHeight <= 0) {
						clearInterval(timer);
						element.style.setProperty("display","none");
						element.style.setProperty("height",totalHeight + 'px');
						if (element.__TimerManager__ && element.__TimerManager__.constructor == TimerManager) {
							element.__TimerManager__.next();
						}
					} else {
						element.style.setProperty("height",currentHeight + "px");
					}
				},10)
			} else {
				if (element.__TimerManager__ && element.__TimerManager__.constructor == TimerManager) {
					element.__TimerManager__.next();
				} 
			}
		}
		this.slider.slideDown = function (element,time) {
			TimerManager.makeInstance(element);
			element.__TimerManager__.add(fnSlideDown,arguments);
		}
		this.slider.slideUp = function (element,time) {
			TimerManager.makeInstance(element);
			element.__TimerManager__.add(fnSlideUp,arguments);
		}
	},
	slideToggle:function (element,time) {
		this.initSlider(element,time);
		element.style.setProperty("overflow","hidden");
		if (element.clientHeight == 0) {
			this.slider.slideDown(element,time);
		} else {	
			this.slider.slideUp(element,time)
		}
	},
	slideDown:function (element,time) {
		element.style.setProperty("display","inline-block");
	},
	slideUp:function (element,time) {
		element.style.setProperty("display","none");
	},
}
