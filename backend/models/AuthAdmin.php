<?php
namespace backend\models;

use Yii;

class AuthAdmin extends Common
{
    public static function getDb()
    {
        return Yii::$app->fastDb;
    }

    public static function tableName ()
    {
        return "{{%admin}}";
    }
}