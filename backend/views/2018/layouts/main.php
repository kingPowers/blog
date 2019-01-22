<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>

<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>后台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="renderer" content="webkit">

    <link rel="shortcut icon" href="/assets/img/favicon.ico" />
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

<body class="inside-header inside-aside is-dialog">
<div id="main" role="main">
    <div class="tab-content tab-addtabs">
        <div id="content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <section class="content-header hide">
                        <h1>
                            控制台
                            <small>Control panel</small>
                        </h1>
                    </section>
                    <!-- RIBBON -->
                    <div id="ribbon">
                        <ol class="breadcrumb pull-left">
                            <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> 控制台</a></li>
                        </ol>
                        <ol class="breadcrumb pull-right">

                        </ol>
                    </div>
                    <!-- END RIBBON -->
                    <div class="content">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/require.js" data-main="/assets/js/require-backend.js?v=1.0.1"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>