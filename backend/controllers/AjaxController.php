<?php

namespace backend\controllers;

use backend\models\AuthAdmin;
use Yii;
use backend\components\Auth;

/**
 * ajax请求
 */
class AjaxController extends FastCommonController
{
    public function actionLang()
    {
       return $this->ajaxJsonp($this->lang);
    }
}