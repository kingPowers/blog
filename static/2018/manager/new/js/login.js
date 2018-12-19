function refreshVerify() {
    var ts = Date.parse(new Date())/1000;
    var refresh = new formOperate("loginForm",{url:"/public/captcha"});
    refresh.addFormData('refresh',ts);
    refresh.success = function (F,data) {
    	$(".img-verify").attr("src",F.url);
    }
    refresh.submitForm();
}
$(function () {
	window.document.onkeydown = function (e) {
		if (e.keyCode == 13) 
			$(".btn_login").click();
	}
	$(".btn_login").click(function () {
		var loginForm = new formOperate("loginForm",{url:"/public/login"});
		var load = layer.load(1);
		loginForm.success = function (F) {console.log(F);
			layer.close(load);
			//top.jdbox.alert(F.status,F.info);
			layer.alert(F.info);
			if (F.status) {
				window.location.href = BACKEND;
			}
		}
		//top.jdbox.alert(2);
		loginForm.submitForm();
	})
}) 