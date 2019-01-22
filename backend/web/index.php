<?php
$s_time = microtime();
require __DIR__ . '/../../common/init/define.php';//公共常量
$require = require BACK_PATH . "config" . DS . "require.php";	
foreach ($require as $value) {
	require $value;
}
$config = yii\helpers\ArrayHelper::merge(
    require COMMON_PATH . 'config/main.php',
    require COMMON_PATH . 'config/main-local.php',
    require BACK_PATH . 'config/main.php',
    require BACK_PATH . 'config/main-local.php'
);

(new yii\web\Application($config))->run();
