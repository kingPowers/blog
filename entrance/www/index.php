<?php
require __DIR__ . '/../common/define.php';
require COMMON_PATH . "function.php";
defined("CONFIG_PATH") or define("CONFIG_PATH",dirname(__FILE__) . DS . "config" . DS);
require VENDOR_PATH . 'autoload.php';
require YII_PATH . 'Yii.php';

// require __DIR__ . '/../../common/config/bootstrap.php';
// require __DIR__ . '/../config/bootstrap.php';
$config = yii\helpers\ArrayHelper::merge(
	require COMMON_PATH . "main.php",
	require CONFIG_PATH . "main.php"
);
(new yii\web\Application($config))->run();