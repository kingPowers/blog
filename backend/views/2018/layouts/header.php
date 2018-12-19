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
    <link rel="stylesheet" type="text/css" href="_STATIC_/manager/plugins/editer/themes/default/css/ueditor.css">
	<script type="text/javascript">var STATIC = '_STATIC_',INCLUDE_TYPE = 'header',BACKEND = "_BACKEND_";</script>
	<script type="text/javascript" src="_STATIC_/manager/js/jquery.min.js"></script> 
	<script type="text/javascript" src="_STATIC_/public/js/public.js"></script>
	<script type="text/javascript" src="_STATIC_/public/plugins/layer/layer.js"></script>
	<script type="text/javascript" src="_STATIC_/public/js/include.js"></script>
<script type="text/javascript">
var addCsrf = function (form) {
	form.addFormData("_csrf-backend","<?= Yii::$app->request->csrfToken?>")
}
// 跳转
function redirect(url) {
	window.location.replace(url);
}
function parentRedirect (url)
{
	window.parent.location.replace(url);
}
var parLayer = window.parent.layer;
</script>
<style type="text/css">
	.table-hover tr td{max-width: 200px;}
	.layui-form-item {display: inline-block;width: 100%;}
	.layui-form-item .layui-form-label {min-width: 20px;display: inline-block;}
	.layui-form-item .layui-input-inline {display: inline-block;max-width: 200px;}
	.layui-form-item .short {width: 50px}
	.layui-form-item .mid {width: 100px}
	.layui-form-item .long {width: 200px}
	.table-bordered tr td label {display: inline-block;}
	.form-group .help-block {color: red;}
</style>
</head>
<body>
<?= $content ?>
 <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>