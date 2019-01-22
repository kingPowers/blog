<?php
defined("DS") or define("DS",DIRECTORY_SEPARATOR);

defined("ROOT_PATH") or define("ROOT_PATH",dirname(dirname(dirname(__FILE__))) . DS);

defined("COMMON_PATH") or define("COMMON_PATH",ROOT_PATH . "common" . DS);
//前台模块路径
defined("FRONT_PATH") or define("FRONT_PATH",ROOT_PATH . "frontend" . DS);
//后台模块路径
defined("BACK_PATH") or define("BACK_PATH",ROOT_PATH . "backend" . DS);
//接口模块路径
defined("SERVICE_PATH") or define("SERVICE_PATH",ROOT_PATH . "services" . DS);
//上传文件根目录
defined("UPLOAD_PATH") or define("UPLOAD_PATH",ROOT_PATH . 'static' . DS . "upload" .DS);

defined("VENDOR_PATH") or define("VENDOR_PATH",ROOT_PATH . "vendor" . DS);

defined("YII_PATH") or define("YII_PATH",VENDOR_PATH . "yiisoft" . DS . "yii2" . DS);

defined('YII_DEBUG') or define('YII_DEBUG', true);

defined('YII_ENV') or define('YII_ENV', 'dev');

defined("IS_CLI") or define("IS_CLI",PHP_SAPI == 'cli' ? true : false);

//定义主题
defined("THEME") or define("THEME","2018");

//静态资源地址
defined("_STATIC_") or define("_STATIC_","http://static.blog.com/" . THEME);

//上传文件地址
defined("_UPLOAD_") or define("_UPLOAD_","http://static.blog.com/upload");
//接口网址
defined("_SERVICE_") or define("_SERVICE_","http://services.blog.com");
//前台网址
defined("_FRONT_") or define("_FRONT_","http://www.blog.com");
//后台地址
defined("_BACKEND_") or define("_BACKEND_","http://manager.blog.com");