<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=39.104.49.15;dbname=blog',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'fastDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=39.104.49.15;dbname=blogFast',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'tablePrefix' => 'bg_',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'redis' =>  [
            'class' =>  'yii\redis\Connection',
            'hostname'  =>  '127.0.0.1',
            'port'  =>  6379,
            'database'  =>  0,
        ],
        'cache' =>  [
            'class' =>  'yii\redis\Cache',
            'redis' =>  [
                'hostname'  =>  '127.0.0.1',
                'port'  =>  6379,
                'database'  =>  0,
            ],
        ],
    ],
];