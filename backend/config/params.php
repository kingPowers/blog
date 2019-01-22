<?php
return [
    'adminEmail' => 'admin@example.com',
    'site' => [
        'name' => 'FastAdmin',
        'beian' => '',
        'cdnurl' => '',
        'version' => '1.0.1',
        'timezone' => 'Asia/Shanghai',
        'forbiddenip' => '',
        'languages' =>
            array (
                'backend' => 'zh-cn',
                'frontend' => 'zh-cn',
            ),
        'fixedpage' => 'dashboard',
        'categorytype' =>
            array (
                'default' => 'Default',
                'page' => 'Page',
                'article' => 'Article',
                'test' => 'Test',
            ),
        'configgroup' =>
            array (
                'basic' => 'Basic',
                'email' => 'Email',
                'dictionary' => 'Dictionary',
                'user' => 'User',
                'example' => 'Example',
            ),
        'mail_type' => '1',
        'mail_smtp_host' => 'smtp.qq.com',
        'mail_smtp_port' => '465',
        'mail_smtp_user' => '10000',
        'mail_smtp_pass' => 'password',
        'mail_verify_type' => '2',
        'mail_from' => '10000@qq.com',
    ],
    'upload' => [
        /**
         * 上传地址,默认是本地上传
         */
        'uploadurl' => 'ajax/upload',
        /**
         * CDN地址
         */
        'cdnurl'    => '',
        /**
         * 文件保存格式
         */
        'savekey'   => '/uploads/{year}{mon}{day}/{filemd5}{.suffix}',
        /**
         * 最大可上传大小
         */
        'maxsize'   => '10mb',
        /**
         * 可上传的文件类型
         */
        'mimetype'  => 'jpg,png,bmp,jpeg,gif,zip,rar,xls,xlsx',
        /**
         * 是否支持批量上传
         */
        'multiple'  => false,
    ],
    'fastadmin' => [
        //是否开启前台会员中心
        'usercenter'          => true,
        //登录验证码
        'login_captcha'       => false,
        //登录失败超过10则1天后重试
        'login_failure_retry' => true,
        //是否同一账号同一时间只能在一个地方登录
        'login_unique'        => false,
        //登录页默认背景图
        'login_background'    => "/assets/img/loginbg.jpg",
        //是否启用多级菜单导航
        'multiplenav'         => false,
        //自动检测更新
        'checkupdate'         => false,
        //版本号
        'version'             => '1.0.0.20180618_beta',
        //API接口地址
        'api_url'             => 'https://api.fastadmin.net',
    ]
];
