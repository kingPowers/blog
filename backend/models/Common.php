<?php

namespace backend\models;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Yii;
use yii\data\Pagination;

/**
 *
 */
class Common extends \common\models\Common
{
    public $backend_err_msg;

    /**
     * 设置后台验证器之外的错误信息
     * @param $message
     */
    public function backendError($message)
    {
        $this->backend_err_msg = $message;
        $this->addError('backend_err_msg', $message);
    }

    public static function buildCaseSql ($field,$case,$alias)
    {
        if (!$field || !$case || !$alias) return '';
        $sql = ' CASE `'.$field . '`';
        foreach ($case as $key => $value) {
            $sql .= ' WHEN ' . $key . " THEN '" . $value . "'";
        }
        $sql .= ' END AS `' . $alias . '`';
        return $sql;
    }
}