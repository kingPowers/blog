<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;

$config = json_decode($this->params['config'], true);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="zh-cn">
    <head>
        <!-- 加载部部样式及META信息 -->
        <meta charset="utf-8">
        <title>后台</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="renderer" content="webkit">

        <link rel="shortcut icon" href="/assets/img/favicon.ico"/>
        <!-- Loading Bootstrap -->
        <link href="/assets/css/backend.css?v=1.0.1" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
        <!--[if lt IE 9]>
        <script src="/assets/js/html5shiv.js"></script>
        <script src="/assets/js/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript">
            var require = {
                config:<?= $this->params['config'] ?>
            };
        </script>
    </head>
    <body class="hold-transition skin-green sidebar-mini fixed" id="tabs">
    <div class="wrapper">

        <!-- 头部区域 -->
        <header id="header" class="main-header">
            <?= require 'header.php' ?>
        </header>

        <!-- 左侧菜单栏 -->
        <aside class="main-sidebar">
            <?= require 'menu.php' ?>
        </aside>

        <!-- 主体内容区域 -->
        <div class="content-wrapper tab-content tab-addtabs">

        </div>

        <!-- 底部链接,默认隐藏 -->
        <footer class="main-footer hide">
            <div class="pull-right hidden-xs">
            </div>
            <strong>Copyright &copy; 2017-2018 <a href="https://www.fastadmin.net">Fastadmin</a>.</strong> All rights
            reserved.
        </footer>

        <!-- 右侧控制栏 -->
        <div class="control-sidebar-bg"></div>
        <?= require 'control.php' ?>
    </div>

    <!-- 加载JS脚本 -->
    <script src="/assets/js/require.js" data-main="/assets/js/require-backend.js?v=1.0.1"></script>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>