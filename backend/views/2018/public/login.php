<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\widgets\ActiveForm;

AppAsset::register($this);
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
	<script type="text/javascript">var STATIC = '_STATIC_',INCLUDE_TYPE = 'login',BACKEND = "_BACKEND_";</script>
	<script type="text/javascript" src="_STATIC_/manager/js/jquery.min.js"></script> 
	<script type="text/javascript" src="_STATIC_/public/js/public.js"></script>
	<script type="text/javascript" src="_STATIC_/public/js/include.js"></script>
	<script type="text/javascript" src="_STATIC_/public/plugins/layer/layer.js"></script>
    <?php $this->head();?>
</head>
<style type="text/css">
:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
    color: #fff;
}

::-moz-placeholder { /* Mozilla Firefox 19+ */
    color: #fff;
}

input:-ms-input-placeholder{
    color: #fff;
}

input::-webkit-input-placeholder{
    color: #fff;
}
body {
	padding-top: 40px;
	padding-bottom: 40px;
	background:url("_STATIC_/manager/images/bg_login.jpg") #525866;
}
.form-signin {
	max-width: 450px;
	padding: 39px 50px;
	margin: 20% auto 0;
	background: url('_STATIC_/manager/images/bg_login_form.png');	
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	color: #fff;	
}
.form-signin h1{font-size: 50px; text-align:center;}
.form-signin .form-signin-heading,  .form-signin .checkbox {
	margin-bottom: 10px;
}
.form-signin input[type="text"],  .form-signin input[type="password"] {
	font-size: 16px;
	height: 40px;
	margin: 40px 0 0;
	padding: 7px 20px;
 	border: 1px solid #fff;
 	background: transparent;
	color: #fff;
}
#form-yzm{width: 218px; height:24px;}
.form-signin img{
	height: 40px;
	width: 170px;
	margin: 40px 0 0 15px;
	vertical-align: top;
	cursor: pointer;
}
.btn_login{background: #0f1934; color: #fff; border: 0; width: 450px; margin-top: 40px; box-shadow:0 0 #0f1934;}
</style>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container">
<!--   <form class="form-signin manager-login-form" id="loginForm" method="post" action="/publics/login">
	<h1 class="form-signin-heading">后台管理系统2.0</h1>
	<input type="hidden" name="_csrf-backend" value="">
	<input type="hidden" name="is_login" value="1">
	<input type="text" name="username" class="input-block-level input" placeholder="账号:">
	<input type="password" name="password" class="input-block-level input" placeholder="密码:" style="width: 100%;">
	<input type="text" name="verify" id="form-yzm" placeholder="验证码:">
	<img src="/public/captcha" class="img-verify" title="点击更换验证码" onclick="refreshVerify()"/>
	<p>
	  <button class="btn btn-large manager-login-btn btn_login" type="button">登录</button>
	</p>
  </form> -->
  <?php $form = ActiveForm::begin(['id' => 'loginForm','method' => 'post', 'options' => ['class' => 'form-signin manager-login-form']]) ?>
  	<h1 class="form-signin-heading">后台管理系统2.0</h1>
	<input type="hidden" name="is_login" value="1">
	<input type="text" name="LoginForm[username]" class="input-block-level input" placeholder="账号:">
	<input type="password" name="LoginForm[password]" class="input-block-level input" placeholder="密码:" style="width: 100%;">
	<input type="text" name="LoginForm[verify]" id="form-yzm" placeholder="验证码:">
	<img src="/public/captcha" class="img-verify" title="点击更换验证码" onclick="refreshVerify()"/>
	<p>
	  <button class="btn btn-large manager-login-btn btn_login" type="button">登录</button>
	</p>
  <?php ActiveForm::end(); ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>