<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
$viewParams = $this->params;
?>
<?php $this->beginPage();?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script type="text/javascript">var STATIC = "_STATIC_";</script>
	<script type="text/javascript" src="_STATIC_/public/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="_STATIC_/public/js/public.js"></script>
	<script type="text/javascript" src="_STATIC_/public/js/include.js"></script>
</head>
<script type="text/javascript">
</script>
<body>
<?php $this->beginBody() ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>