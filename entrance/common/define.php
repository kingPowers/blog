<?php
define("DS",DIRECTORY_SEPARATOR);

define('YII_DEBUG', true);

define("YII_ENV_TEST",false);

define('YII_ENV', 'dev');

define("ROOT_PATH",dirname(dirname(dirname(__FILE__))) . DS);

define("VENDOR_PATH",ROOT_PATH . "vendor" . DS);

define("ENTRANCE_PATH",ROOT_PATH . "entrance" . DS);

define("YII_PATH",VENDOR_PATH . "yiisoft" . DS . "yii2" . DS);

define("COMMON_PATH",dirname(__FILE__) . DS);

define('IS_CLI', PHP_SAPI == 'cli' ? true : false);