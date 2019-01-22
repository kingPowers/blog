<!DOCTYPE html>
<html lang="{$config.language}">
<head>
    <?= $this->params['html']['meta'] ?>
</head>

<body class="inside-header inside-aside">
<div id="main" role="main">
    <div class="tab-content tab-addtabs">
        <div id="content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <section class="content-header hide">
                        <h1>
                            <?= __('Dashboard') ?>
                            <small><?= __('Control panel') ?></small>
                        </h1>
                    </section>

                    <!-- RIBBON -->
                    <div id="ribbon">
                        <ol class="breadcrumb pull-left">
                            <li><a href="<?= \yii\helpers\Url::to('fast-index/dashboard') ?>" class="addtabsit"><i class="fa fa-dashboard"></i> <?= __('Dashboard') ?></a></li>
                        </ol>
                        <ol class="breadcrumb pull-right">
<!--                            {foreach $breadcrumb as $vo}-->
<!--                            <li><a href="javascript:;" data-url="{$vo.url}">{$vo.title}</a></li>-->
<!--                            {/foreach}-->
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
</body>
</html>