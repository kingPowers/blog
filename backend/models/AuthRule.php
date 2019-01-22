<?php
namespace backend\models;

use Yii;

class AuthRule extends Common
{
    public static function getDb()
    {
        return Yii::$app->fastDb;
    }

    public static function tableName ()
    {
        return "{{%auth_rule}}";
    }
}