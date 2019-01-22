<?php $config = $this->params['config']; ?>
<!-- Logo -->
<a href="javascript:;" class="logo">
    <!-- 迷你模式下Logo的大小为50X50 -->
    <span class="logo-mini"><?= $config['site']['name'] ?></span>
    <!-- 普通模式下Logo -->
    <span class="logo-lg"><b><?= mb_substr($config['site']['name'], 0, 4, 'utf-8') ?></b><?= mb_substr($config['site']['name'], 4, null, 'utf-8') ?></span>
</a>

<!-- 顶部通栏样式 -->
<nav class="navbar navbar-static-top">

    <!--第一级菜单-->
    <div id="firstnav">
        <!-- 边栏切换按钮-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only"><?= __('Toggle navigation') ?></span>
        </a>

        <!--如果不想在顶部显示角标,则给ul加上disable-top-badge类即可-->
        <ul class="nav nav-tabs nav-addtabs disable-top-badge hidden-xs" role="tablist">
            <?= $this->params['navlist'] ?>
        </ul>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li>
                    <a href="" target="_blank"><i class="fa fa-home" style="font-size:14px;"></i></a>
                </li>

                <li class="dropdown notifications-menu hidden-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header"><?= __('Latest news') ?></li>
                        <li>
                            <!-- FastAdmin最新更新信息,你可以替换成你自己站点的信息,请注意修改public/assets/js/backend/index.js文件 -->
                            <ul class="menu">

                            </ul>
                        </li>
                        <li class="footer"><a href="#" target="_blank"><?= __('View more') ?></a></li>
                    </ul>
                </li>

                <!-- 账号信息下拉框 -->
                <li class="hidden-xs">
                    <a href="javascript:;" data-toggle="checkupdate" title="<?= __('Check for updates') ?>">
                        <i class="fa fa-refresh"></i>
                    </a>
                </li>

                <!-- 清除缓存 -->
                <li>
                    <a href="javascript:;" data-toggle="dropdown" title="<?= __('Wipe cache') ?>">
                        <i class="fa fa-trash"></i>
                    </a>
                    <ul class="dropdown-menu wipecache">
                        <li><a href="javascript:;" data-type="all"><i class="fa fa-trash"></i> <?= __('Wipe all
                                cache') ?></a></li>
                        <li class="divider"></li>
                        <li><a href="javascript:;" data-type="content"><i class="fa fa-file-text"></i> <?= __('Wipe
                                content cache') ?></a></li>
                        <li><a href="javascript:;" data-type="template"><i class="fa fa-file-image-o"></i> <?= __('Wipe
                                template cache') ?></a></li>
                        <li><a href="javascript:;" data-type="addons"><i class="fa fa-rocket"></i> <?= __('Wipe addons
                                cache') ?></a></li>
                    </ul>
                </li>

                <!-- 多语言列表 -->
                <li class="hidden-xs">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-language"></i></a>
                    <ul class="dropdown-menu">
                        <li class="active">
                            <a href="?ref=addtabs&lang=zh-cn">简体中文</a>
                        </li>
                        <li class="">
                            <a href="?ref=addtabs&lang=en">English</a>
                        </li>
                    </ul>
                </li>

                <!-- 全屏按钮 -->
                <li class="hidden-xs">
                    <a href="#" data-toggle="fullscreen"><i class="fa fa-arrows-alt"></i></a>
                </li>

                <!-- 账号信息下拉框 -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $this->params['admin']['avatar'] ?>" class="user-image"
                             alt="<?= $this->params['admin']['nickname'] ?>">
                        <span class="hidden-xs"><?= $this->params['admin']['nickname'] ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $this->params['admin']['avatar'] ?>" class="img-circle" alt="">

                            <p>
                                <?= $this->params['admin']['nickname'] ?>
                                <small><?= date('Y-m-d H:i:s', $this->params['admin']['logintime']) ?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <a href="https://www.fastadmin.net" target="_blank"><?= __('FastAdmin') ?></a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="https://forum.fastadmin.net" target="_blank"><?= __('Forum') ?></a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="https://doc.fastadmin.net" target="_blank"><?= __('Docs') ?></a>
                                </div>
                            </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="general/profile" class="btn btn-primary addtabsit"><i class="fa fa-user"></i>
                                    <?= __('Profile') ?></a>
                            </div>
                            <div class="pull-right">
                                <a href="<?= \yii\helpers\Url::to(['index/logout']) ?>" class="btn btn-danger"><i class="fa fa-sign-out"></i>
                                    <?= __('Logout') ?></a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- 控制栏切换按钮 -->
                <li class="hidden-xs">
                    <a href="javascript:;" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </div>


<!--    第二级菜单,只有在multiplenav开启时才显示-->
<!--    <div id="secondnav">-->
<!--        <ul class="nav nav-tabs nav-addtabs disable-top-badge" role="tablist">-->
<!--        </ul>-->
<!--    </div>-->

</nav>