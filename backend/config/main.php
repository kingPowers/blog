<?php
$params = array_merge(
    require COMMON_PATH . 'config/params.php',
    require COMMON_PATH . 'config/params-local.php',
    require BACK_PATH . 'config/params.php',
    require BACK_PATH . 'config/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => BACK_PATH,
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    "viewPath" => VIEW_PATH,
    'modules' => [
        'redactor'  =>  [
            'class' =>  'yii\redactor\RedactorModule',
            'uploadDir' =>  UPLOAD_PATH . "images" . DS . "article",
            //'uplaodUrl' =>  _UPLOAD_ . "/images/article",
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'enableCsrfValidation' => false,
        ],
        // 'user' => [
        //     'identityClass' => 'common\models\User',
        //     'enableAutoLogin' => true,
        //     'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        // ],
        'user' => [
            'identityClass' => 'backend\models\UserAdmin'
            ,'loginUrl' => ['public/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            // 'name' => 'advanced-backend',
            'class' => 'yii\redis\Session',
            'redis' => 'redis'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                // [
                //     'class' => 'yii\mongodb\log\MongoDbTarget',
                //     'levels' => ['error', 'warning'],
                //     'except' => ['yii\web\HttpException:403'],
                //     'logCollection' => 'backend_error',
                //     'logVars' => [],
                // ],
            ],
        ],
        'mongodb' => [
               'class' => '\yii\mongodb\Connection',
               'dsn' => 'mongodb://log:log123456@39.104.49.15:27017/blog',
        ],
        // 'errorHandler' => [
        //     'errorAction' => '/error',
        // ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '39.104.49.15',
            'port' => 6379,
            'database' => 0
        ],
        'i18n'=>[
            'translations'=>[
                '*'=>[
                    'class'=>'yii\i18n\PhpMessageSource',
                    'fileMap'=>[
                        'common'=>'common.php',
                    ],
                ]

            ],
        ],
    ],
    'defaultRoute'  =>  "index",
    'params' => $params,
];
