<?php
$params = array_merge(
    require COMMON_PATH . 'config/params.php',
    require COMMON_PATH . 'config/params-local.php',
    require SERVICE_PATH . 'config/params.php',
    require SERVICE_PATH . 'config/params-local.php'
);
return [
    'id' => 'app-service',
    'basePath' => SERVICE_PATH,
    'bootstrap' => ['log'],
    'controllerNamespace' => 'service\controllers',
    "viewPath" => VIEW_PATH,
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-fronend',
            "enableCsrfValidation" => false,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-service',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'index/error',
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        
    ],
    'params' => $params,
    'defaultRoute' => "index",//默认首页
];
