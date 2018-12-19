"use strict";
var includeCss = [],includeJs = [];
if (INCLUDE_TYPE == 'login') {
	includeCss = [
		STATIC + "/manager/Public/Css/bootstrap.css",
		STATIC + "/manager/Public/Css/bootstrap-responsive.css",
		STATIC + "/manager/Public/Css/style.css",
		STATIC + "/manager/box/wbox.css",
	];
	includeJs = [
		STATIC + "/manager/js/wbox.js",
		STATIC + "/manager/js/jquery.form.js",
		STATIC + "/manager/Public/Js/bootstrap.js",
		STATIC + "/manager/Public/Js/ckform.js",
		STATIC + "/manager/new/js/login.js",
	];
} else if (INCLUDE_TYPE == 'header') {
	includeCss = [
		STATIC + "/manager/plugins/bootstrap/css/bootstrap.css",
		STATIC + "/manager/plugins/bootstrap/css/bootstrap-responsive.css",
		STATIC + "/manager/Public/Css/style.css",
		STATIC + "/manager/plugins/font-awesome/css/font-awesome.min.css",
		STATIC + "/manager/box/wbox.css",
		STATIC + "/manager/new/css/header.css",
	];
	includeJs = [
		STATIC + "/manager/new/js/header.js",
		STATIC + "/manager/js/wbox.js",
		STATIC + "/manager/plugins/wdate/WdatePicker.js",
		STATIC + "/manager/Public/Js/bootstrap.js",
	];
}
for (var i = includeCss.length - 1; i >= 0; i--) {
	includeFile(includeCss[i],'css');
}
for (var i = includeJs.length - 1; i >= 0; i--) {
	includeFile(includeJs[i]);
}