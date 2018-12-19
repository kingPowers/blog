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
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="_STATIC_/public/images/logo.ico">
    <link rel="stylesheet" type="text/css" href="_STATIC_/public/css/public.css">
    <link rel="stylesheet" type="text/css" href="_STATIC_/public/plugins/myfocus/mf-pattern/mF_fancy.css">
    <script type="text/javascript" src="_STATIC_/public/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="_STATIC_/public/js/public.js"></script>
    <script type="text/javascript" src="_STATIC_/public/plugins/myfocus/myfocus-2.0.1.min.js"></script>
    <script type="text/javascript" src="_STATIC_/public/plugins/myfocus/mf-pattern/mF_fancy.js"></script>
    <?php $this->head();?>
</head>
<body style="background: rgb(233,233,233);">
<?php $this->beginBody() ?>
<div id="page-container">
<div id="page-header">
	<div id="page-header-container">
		<div id="page-logo">
			<a href="/index/index"><img src="_STATIC_/public/images/logo.png"></a>
		</div>
		<div id="page-top-nav">
			<ul>
				<?php foreach ($viewParams['headerNav'] as $key => $value) { ?>
					<li navkey="<?= $key ?>" class="<?= $value['class'] ?>"><a href="<?= $value['url'] ?>"><?= $value['title'] ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<div id="page-top-search-div">
			<div id="search-container">
				<input type="text" name="seach" id="search-input">
				<span id="search-span">搜索</span>
			</div>
		</div>
	</div>
</div>
<div id="page-body-content">
<?= $content ?>
<div id="page-footer">
</div>
</div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
