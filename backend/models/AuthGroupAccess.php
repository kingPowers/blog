<?php
namespace backend\models;

use Yii;

class AuthGroupAccess extends Common
{
    public static function getDb()
    {
        return Yii::$app->fastDb;
    }

    public static function tableName ()
    {
        return "{{%auth_group_access}}";
    }
}