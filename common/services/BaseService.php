<?php

namespace common\services;

use yii\base\BaseObject;

/**
 * 事务类基类
 * Created by PhpStorm
 * User: Quanjiaxin
 * Date: 2019/1/8
 * Time: 10:59
 */
class BaseService extends BaseObject
{
    protected static $instance;

    public static function getInstance($params = [], $isNew = false)
    {
        if ($isNew) {
            return new static($params);
        }

        if (!isset(self::$instance)) {
            self::$instance = new static($params);
        }

        return self::$instance;
    }
}

