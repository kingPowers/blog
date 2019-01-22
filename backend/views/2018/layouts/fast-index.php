<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;

$config = json_encode($this->params['config']);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="zh-cn">
    <head>
        <!-- 加载部部样式及META信息 -->
        <?= $this->params['html']['meta'] ?>
    </head>
    <body class="hold-transition skin-green sidebar-mini fixed" id="tabs">
    <div class="wrapper">

        <!-- 头部区域 -->
        <header id="header" class="main-header">
            <?= $this->params['html']['header'] ?>
        </header>

        <!-- 左侧菜单栏 -->
        <aside class="main-sidebar">
            <?= $this->params['html']['menu'] ?>
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
        <?= $this->params['html']['control'] ?>
    </div>

    <!-- 加载JS脚本 -->
    <script src="/assets/js/require.js" data-main="/assets/js/require-backend.js?v=1.0.1"></script>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>