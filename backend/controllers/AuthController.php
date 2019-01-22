<?php

namespace backend\controllers;

use backend\models\AuthAdmin;
use Yii;
use backend\components\Auth;

/**
 * 后台首页
 */
class AuthController extends FastCommonController
{
    public $layout = "public";
    public function actionAdmin()
    {

        return $this->render("admin");
    }
    public function getJsPath($actionName)
    {
        $path = [
            'admin' => 'admin'
        ];

        return $path[$actionName] ?? '';
    }
}