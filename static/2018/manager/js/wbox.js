(function($){
    var zIndex = 100;
    //class为.wBox_close为关闭
    $.fn.wBox = function(options){
        var defaults = {
            wBoxURL: STATIC+"/manager/box/",
            opacity: 0.5,//背景透明度
            callBack: null,
            noTitle: false,
            show:false,
            timeout:0,
            target:null,
            requestType:null,//iframe,ajax,img
            postdata:null,
            title: "基本",
            drag:true,
            iframeWH: {//iframe 设置高宽
                width: 300,
                height: 50
            },
            oth_close:true,
            html: '',//wBox内容
			titleClass:''
        },_this=this;
        this.YQ = $.extend(defaults, options);
        var  wBoxHtml = '<div id="wBox" style="z-index:'+(zIndex++)+'"><div class="wBox_popup"><table><tbody><tr><td><div class="wBox_body">' + (_this.YQ.noTitle ? '' : '<table class="wBox_title '+ _this.YQ.titleClass +'"><tr><td class="wBox_dragTitle" ><div class="wBox_itemTitle">' + _this.YQ.title + '</div></td><td width="45px" title="关闭"><div class="wBox_close"></div></td></tr></table> ') +
        '<div class="wBox_content" id="wBoxContent"></div></div></td></tr></tbody></table></div></div>', B = null, C = null, $win = $(window),$t=$(this);//B背景，C内容jquery div   
        var post = _this.YQ.postdata?_this.YQ.postdata:{};
        _this.YQ.postdata = null;
        var deBg = post.deBg?true:false;
        var keepIframe = post.keepIframe?true:false;
        this.showBox=function (){
            if (!keepIframe && _this.YQ.oth_close) {
                $("#wBox_overlay").remove();
                $("#wBox").remove();
            }
            B = $("<div id='wBox_overlay' class='wBox_hide'></div>").hide().addClass('wBox_overlayBG').css('opacity', _this.YQ.opacity).dblclick(function(){
            _this.close();
            }).appendTo('body').fadeIn(300);
            if (deBg) {
                B.hide();
            }
            C = $(wBoxHtml).appendTo('body');
            //dragSize(document.getElementsByClassName('wBox_popup')[0]);
            C.click(function (e) {
                $(this).css("z-index",zIndex++);
            })
            handleClick();
        }
        /*
         * 处理点击
         * @param {string} what
         */
        function handleClick(){
            var con = C.find("#wBoxContent");
            if (_this.YQ.requestType && $.inArray(_this.YQ.requestType, ['iframe', 'ajax','img'])!=-1) {
                con.html("<div class='wBox_load'><div id='wBox_loading'><img src='"+_this.YQ.wBoxURL+"loading.gif' /></div></div>");                
                if (_this.YQ.requestType === "img") {
                    var img = $("<img />");
                    img.attr("src",_this.YQ.target);
                    img.load(function(){
                        img.appendTo(con.empty());
                        setPosition();
                    });
                }
                else 
                    if (_this.YQ.requestType === "ajax") {
                        $.get(_this.YQ.target, function(data){
                            con.html(data);
                            C.find('.wBox_close').click(_this.close);
                            setPosition();
                        })
                        
                    }
                    else {
                        if(_this.YQ.postdata!==null){
                            var url = _this.YQ.target + '?' + $.param(_this.YQ.postdata);
                        }else{
                            var url = _this.YQ.target;
                        }
                        var framehtml = "<div id='boxloading' style='text-align:center;margin-top:30px;'><img src='"+STATIC+"/manager/box/blueload.gif'/></div>";
                        framehtml += "<iframe id='wBoxIframe' name='wBoxIframe' style='width:" + _this.YQ.iframeWH.width + "px;height:" + _this.YQ.iframeWH.height + "px;' scrolling='no' frameborder='0' src='" + url + "'></iframe>";
                        ifr = $(framehtml);
                        ifr.appendTo(con.empty());
                        ifr.load(function(){
                            try {
                                $('#boxloading').remove();
                                $it = $(this).contents();
                                $it.find('.wBox_close').click(_this.close);
                                $(C).find('.wBox_itemTitle').html($it.find('span:first').html());
                                $it.find('span:first').parent().remove();
                                fH = $it.height();//iframe height
                                fW = $it.width();
                                w = $win;
                                newW = Math.min(w.width() - 40, fW);
                                newH = w.height() - 25 - (_this.YQ.noTitle ? 0 : 30);
                                newH = Math.min(newH, fH);
                                newH = fH;
                                if (!newH) 
                                    return;
                                var lt = calPosition(newW,newH);
                                C.css({
                                    left: lt[0],
                                    top: lt[1]
                                });
                                
                                $(this).css({
                                    height: newH,
                                    width: newW
                                });
                            } 
                            catch (e) {
                            }
                        });
                    }
                
            }
            else 
                if (_this.YQ.target) {
                    $(_this.YQ.target).clone(true).show().appendTo(con.empty());
                    
                }
                else 
                    if (_this.YQ.html) {
                        con.html(_this.YQ.html);
                    }
                    else {
                        $t.clone(true).show().appendTo(con.empty());
                    }         
            afterHandleClick();
        }
        /*
         * 处理点击之后的处理
         */
        function afterHandleClick(){     
            setPosition();
            C.show().find('.wBox_close').click(_this.close).hover(function(){
                $(this).addClass("on");
            }, function(){
                $(this).removeClass("on");
            });
            $(document).unbind('keydown.wBox').bind('keydown.wBox', function(e){
                if (e.keyCode === 27) 
                    _this.close();
                return true
            });
            typeof _this.YQ.callBack === 'function' ? _this.YQ.callBack() : null;
            !_this.YQ.noTitle&&_this.YQ.drag?drag():null;
            if(_this.YQ.timeout){
                setTimeout(_this.close,_this.YQ.timeout);
            }
                
        }
        /*
         * 设置wBox的位置
         */
        function setPosition(){
            if (!C) {
                return false;
            }
            
            var width = C.width();
            var height= C.height();
            lt = calPosition(width,height);
            C.css({
                left: lt[0],
                top: lt[1]
            });
            var $h = $("body").height(), $wh = $win.height(),$hh=$(document).height();
            $h = Math.max($h, $wh);
            $h = Math.max($h,$hh);
            B.height($h).width($win.width())            
        }
        /*
         * 计算wBox的位置
         * @param {number} w 宽度
         */
        function calPosition(w,h){
            l = ($win.width() - w) / 2;
            //t = $win.scrollTop() + $win.height() /9;
            t = $win.scrollTop() + (h>=$win.height()?0:($win.height()-h)/2);
            if(t>30){
                t=t-30;
            }
            return [l, t];
        }
        /*
         * 拖拽函数drag
         */
        function drag(){
            var dx, dy, moveout;
            var T = C.find('.wBox_dragTitle').css('cursor', 'move');
            T.bind("selectstart", function(){
                return false;
            });
            
            T.mousedown(function(e){
                dx = e.clientX - parseInt(C.css("left"));
                dy = e.clientY - parseInt(C.css("top"));
                C.mousemove(move).mouseout(out).css('opacity', 0.8);
                T.mouseup(up);
            });
            /*
             * 移动改变生活
             * @param {Object} e 事件
             */
            function move(e){
                moveout = false;
                if (e.clientX - dx < 0) {
                    l = 0;
                }
                else 
                    if (e.clientX - dx > $win.width() - C.width()) {
                        l = $win.width() - C.width();
                    }
                    else {
                        l = e.clientX - dx
                    }
                C.css({
                    left: l,
                    top: e.clientY - dy
                });
                
            }
            /*
             * 你已经out啦！
             * @param {Object} e 事件
             */
            function out(e){
                moveout = true;
                setTimeout(function(){
                    moveout && up(e);
                }, 10);
            }
            /*
             * 放弃
             * @param {Object} e事件
             */
            function up(e){
                C.unbind("mousemove", move).unbind("mouseout", out).css('opacity', 1);
                T.unbind("mouseup", up);
            }
        }
        
        /*
         * 关闭弹出框就是移除还原
         */
        this.close=function (){
            $('.boxactiver').remove();
            $('.boxcloser').remove();
            if (C) {
                B.remove();
                C.stop().fadeOut(300, function(){
                    C.remove();
                })
            }
            if (_this.YQ.fc) {
                if (_this.YQ.fc.closeEvent) {
                    _this.YQ.fc.closeEvent();
                }
            }
                
        }
        /*
         * 触发click事件
         */     
        $win.resize(function(){
            setPosition();
        });
        _this.YQ.show?_this.showBox():$t.click(function(){
            _this.showBox();
            return false;
        });
        return this;
    };
})(jQuery);

var jdbox = {
    iframe : function(url,data,fc){
        $("body").append('<input class="boxactiver" style="display:none;"><input class="wBox_close boxcloser" style="display:none;">');
        return $(".boxactiver:last").wBox({requestType: "iframe",target:url,postdata:data,fc:fc}).click();
    },
    alert : function(type,content,back,oth_close){
        var title ='',msg ='',bwp = '',titleClass='default',back=back||'';
        if(type==0){
            title = '错误';
            msg = '<div class="error-wp"><span class="icon"></span><span>'+content+'</span></div>';
            bwp = '<a href="javascript:;" onclick="jdbox.close();'+back+'" class="bt-error">确定</a>';
            titleClass = 'error';
        }else if(type==1){
            title = '成功';
            msg = '<div class="success-wp"><span class="icon"></span><span>'+content+'</span></div>';
            bwp = '<a href="javascript:;" onclick="jdbox.close();'+back+'" class="bt-success">确定</a>';
        }else if(type==2){
            title = '加载中...';
            msg = '<div class="wait-wp"><p><span class="icon"></span></p><p>'+(content?content:'')+'</p></div>';
        }
        var html  = '<div class="alert-wp">'+msg+'</div><div class="alert-button-wp">'+bwp+'</div>';
        $("body").append('<input class="boxactiver" style="display:none;"><input class="wBox_close boxcloser" style="display:none;">');
        return $(".boxactiver:last").wBox({'title':title,'html':html,'titleClass':titleClass,oth_close:oth_close}).click();
    },
    info : function(title,content){
        title = title||'详情';
        content = content||'内容';
        var html = '<div style="width:360px;padding:20px;height:auto;overflow:hidden;line-height:25px;color:#444444;font-size:12px;text-align:center;">'+content+'</div>';
        $("body").append('<input class="boxactiver" style="display:none;"><input class="wBox_close boxcloser" style="display:none;">');
        return $(".boxactiver:last").wBox({'title':title,'html':html}).click();
    },
    close : function(){
        $(".wBox_close:last").click();
    }
}