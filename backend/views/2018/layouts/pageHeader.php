<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

$viewParams = $this->params;
?>
<?php $this->beginPage();?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
	<script type="text/javascript">var STATIC = '_STATIC_',INCLUDE_TYPE = 'pageHeader',BACKEND = "_BACKEND_";</script>
	<script type="text/javascript" src="_STATIC_/manager/js/jquery.min.js"></script> 
	<script type="text/javascript" src="_STATIC_/public/js/public.js"></script>
	<!-- <script type="text/javascript" src="_STATIC_/public/js/include.js"></script> -->
	<link rel="stylesheet" type="text/css" href="_STATIC_/manager/assets/css/dpl-min.css">
	<link rel="stylesheet" type="text/css" href="_STATIC_/manager/assets/css/bui-min.css">
	<link rel="stylesheet" type="text/css" href="_STATIC_/manager/assets/css/main-min.css">
	<link rel="stylesheet" type="text/css" href="_STATIC_/manager/box/wbox.css">
	<link rel="stylesheet" type="text/css" href="_STATIC_/manager/new/css/header.css">
	<link rel="stylesheet" type="text/css" href="_STATIC_/public/plugins/musicplay/css/music.css">
	<link rel="stylesheet" type="text/css" href="_STATIC_/public/plugins/musicplay/fonts/fontCss.css">
	<link rel="stylesheet" type="text/css" href="_STATIC_/manager/plugins/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="_STATIC_/public/plugins/imgbrowser/style.css">
	<script type="text/javascript" src="_STATIC_/public/plugins/musicplay/js/music.js"></script>
	<script type="text/javascript" src="_STATIC_/manager/js/wbox.js"></script>
	<script type="text/javascript" src="_STATIC_/manager/assets/js/bui.js"></script>
	<script type="text/javascript" src="_STATIC_/manager/assets/js/common/main.js"></script>
	<script type="text/javascript" src="_STATIC_/manager/assets/js/config.js"></script>
	<script type="text/javascript" src="_STATIC_/public/plugins/layer/layer.js"></script>
	<script type="text/javascript" src="_STATIC_/public/plugins/imgbrowser/plug-in_module.js"></script>
    <?php $this->head();?>
    <style type="text/css" >
        a{color: #fff;}
        .str{
         width:150px;
        }
        .sdelete{
         position:absolute;
         font-size:12px;
/*         color:#999;*/
         width:10px;
         font-weight:900;
         padding:0px;
         border:none;
         cursor:pointer;
        }
        .surl{
         display:block;
         height:25px;
         line-height:25px;
         overflow:hidden;
         color:white;
        }
        .user_operate_list {position: absolute;top:20px;border-radius: 5px;background: #002b55;left: -60px;}
        .mod_user{           
           text-align: center;
           float:left;
           line-height:10px;          
           z-index:9999;  
        }
        .notice_total {
          position: relative;
          top: 3px;
          font-weight: bolder;
        }   
    </style>
    <script type="text/javascript" >
        function omover() {
//           var str = document.getElementById("mod_user");
//            str.style.background = "#d88f40";
            //  var listLink = document.getElementById("listLink");
            // listLink.style.color = "red";
            var divlist = document.getElementById("lishistory");
            divlist.style.display = "block";
        }
        function omout() {
            // var listLink = document.getElementById("listLink");
            // listLink.style.color = "white";
            var mod_user = document.getElementById("mod_user");
            mod_user.style.background = "none";
            var divlist = document.getElementById("lishistory");
            divlist.style.display = "none";
        }
// $(function () {
//   $(".str").click(function () {
//     window.location.reload();
//   })
// })
function noticeReload() {
  window.location.reload();
}
function fancyBox (element,config)
{
	element.fancybox(config);
}
    </script>

</head>
<body >
<?php $this->beginBody() ?>
    <div class="header">
        <div class="dl-title fl"></div>
        <div id="music"></div>
        <div class="dl-main-nav fl">
          <div class="dl-inform">
            <div class="dl-inform-title"><s class="dl-inform-icon dl-up"></s></div>
          </div>
          <ul id="J_Nav"  class="nav-list ks-clear">
          </ul>
        </div>     
    </div>
<div class="content">  
<?= $content ?>
</div>
<script language="javascript">
//设置菜单
$(function () {
$.post("/public/getjsmenu",{'_csrf-backend':"<?= Yii::$app->request->csrfToken ?>"},function (R) {
	console.log(R);
	var R = R.data;
	var config = [], topmenu = '',B = true;
	for(var i in R ){
		var M={},N={},O=[],I=[];
		for(var j in R[i]['child']){
			var T={};
			$(T).attr('id',R[i]['child'][j]['id']);
			$(T).attr('text',R[i]['child'][j]['title']);
			$(T).attr('href','/'+R[i]['child'][j]['name'].replace('-','/'));
			I.push(T);
		}
		$(N).attr('text',R[i]['title']);
		$(N).attr('items',I);
		O.push(N);
		$(M).attr('menu',O);
		$(M).attr('id',R[i]['id']);
		if(I.length > 0){
			$(M).attr('homePage',I[0]['id']);
		}
		config.push(M);
		topmenu += '<li class="nav-item dl-selected">';
		topmenu += '<div class="nav-item-inner ';
		topmenu += B ? 'nav-home' : 'nav-order';
		topmenu += '">' + R[i]['title'] + '</div></li>';
	}
	$('ul#J_Nav').html( topmenu );
	BUI.use('common/main',function(){
		new PageUtil.MainPage({'modulesConfig':config});
	});
},'json')
var resize = (function(){
	$(window).resize(function(){
		var w_h = $(window).height();
		var h_h = $('div#header').height();
		var n_h = $('div.dl-main-nav').height();
		var b_h = $('div.bui-nav-tab div.tab-nav-bar').height();
		$('li.dl-tab-item div.dl-second-nav').css('height',w_h-h_h-n_h+'px');
		$('div#J_28Tab div.bui-nav-tab').css('height',w_h-h_h-n_h+'px');
		$('div.bui-nav-tab div.tab-content-container').css('height',w_h-h_h-n_h-b_h+'px');
	})
}());

var reload = function()
{
	$('ul#J_NavContent > li.dl-tab-item').each(function(){
	var _self = $(this);
		if(!_self.hasClass('ks-hidden'))
		{
			_self.find('div.tab-content').each(function(){
				if($(this).is(':visible') )
				{
					var reloadurl = $(this).find('iframe').contents().find("input[name='reloadurl']").val();
					$(this).find('iframe').attr('src',reloadurl);
				}
			})
		}
	})
}

var logout = function()
{
	jdbox.alert(2,'退出中');
	$.post("",'',function(){
		window.location.href = '/publics/login.html';
	})
}
//退出
var logquit = function()
{
   	if(!confirm("您确定要退出吗？"))return false;
   	window.location.href="/Publics/logout";
}
//修改密码
$(".changePwd").click(function(){
	top.jdbox.iframe("/Publics/changePwd");
})
new MusicPlay("#music",'',{getUrl:'/public/getmusic',getMusicParams:{'_csrf-backend':"<?= Yii::$app->request->csrfToken ?>"}});
})
</script>
 <!-- <a class="btn_exit" title="退出系统" onclick="logquit()" class="dl-log-quit"></a> -->
 <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>