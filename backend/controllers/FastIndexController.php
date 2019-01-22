<?php

namespace backend\controllers;

use backend\models\AuthAdmin;
use Yii;
use backend\components\Auth;
use yii\i18n\PhpMessageSource;

/**
 * 后台首页
 */
class FastIndexController extends FastCommonController
{

    public function actionIndex()
    {
        $this->layout = "fast-index";
        list($menulist, $navlist) = Auth::getInstance()->getSidebar([
            'dashboard' => 'hot',
            'addon' => ['new', 'red', 'badge'],
            'auth/rule' => __('Menu'),
            'general' => ['new', 'purple'],
        ], 1);

        $this->view->params['navlist'] = $navlist;
        $this->view->params['menulist'] = $menulist;
        $this->view->params['admin'] = AuthAdmin::getInfoById(1);
        $this->view->params['html'] = [
            'menu' => $this->renderPartial('/layouts/menu'),
            'header' => $this->renderPartial('/layouts/fast-header'),
            'control' => $this->renderPartial('/layouts/control'),
            'meta' => $this->renderPartial('/layouts/meta')
        ];
        return $this->render("index");
    }

    public function actionDashboard()
    {
        return $this->render("dashboard");
    }

    public function getJsPath($actionName)
    {
        $path = [
            'dashboard' => 'dashboard',
            'index' => 'index',
        ];

        return $path[$actionName] ?? '';
    }
}