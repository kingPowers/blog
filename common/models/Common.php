<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 *
 */
class Common extends ActiveRecord
{
    /*
     * 场景
     */
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_DEFAULT = 'add';

    /*
     * 状态
     */
    const ENABLE_STATUS = 1;
    const DISABLE_STATUS = 2;
    public static $status = [
        self::ENABLE_STATUS => '启用',
        self::DISABLE_STATUS => '禁用'
    ];

    /*
     * 在最近一次错误
     */
    public $error = '';

    public $request;
    protected $finalSql;
    protected $joinTables;
    protected $lastDb;
    protected $fields = '';
    public $where;
    public $whereFilter;
    public $dbWhere = [];
    public $orWhere = [];
    public $page;
    public $pageNum = 10;

    /**
     * 获取请求数据
     * @return \yii\console\Request|\yii\web\Request
     */
    public function getRequest()
    {
        return \Yii::$app->getRequest();
    }

    /**
     * 获取错误信息
     * @param  boolean $single 是否获取第一条错误信息
     * @return [type]          [description]
     */
    public function getError($single = true)
    {
        $errors = $this->getErrors();
        if (false == $single) {
            return $errors;
        }

        $error = $this->firstArrValue($errors);
        if (empty($error)) {
            $error = $this->error;
        }

        return $error;
    }

    public function firstArrValue($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }

        return $this->firstArrValue(current($arr));
    }

    /**
     * 设置自动添加或者更新时间
     * 若覆盖此方法并返回空数组即可
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'timeadd',
                'updatedAtAttribute' => 'lasttime',
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * 获取状态数组或者状态名 子类可覆盖
     * @param  string || int $status 状态
     * @return [type]         [description]
     */
    public static function getStatus($status = '')
    {
        $statusName = static::$status;
        return $status ? $statusName[$status] : $statusName;
    }

    public function getByCache($key, $nullVallCallBack = null, $lifetime = 15)
    {
        if (CACHE_ENABLE === false)
            return call_user_func($nullVallCallBack);

        $value = \Yii::$app->cache->get($key);

        if (false === $value) {
            if ($nullVallCallBack === null) {
                $value = null;
            } else {
                $value = call_user_func($nullVallCallBack);
                if (!isset($value)) $value = null;

                \Yii::$app->cache->set($key, $value, $lifetime);
            }
        }
        return $value;
    }

    /**
     * 根据主键ID获取详情
     * @param $id 主键ID
     * @param bool $isArray 是否返回数组
     * @param string $condition 额外条件
     * @return array|ActiveRecord|null
     */
    public static function getInfoById($id, $isArray = true, $condition = '1=1')
    {
        if (!$id) {
            return [];
        }

        $query = static::find()->where(['id' => $id])->andWhere($condition);
        $isArray && $query->asArray();

        return $query->limit(1)->one();
    }

    /**
     * 根据多个主键ID获取详情
     * @param $ids 多个主键ID
     * @param bool $isArray 是否返回字符串
     * @param string $indexBy 数组键值
     * @param string $condition 额外条件
     * @return array|ActiveRecord[]
     */
    public static function getInfoByIds($ids, $isArray = true, $indexBy = '', $condition = '1=1')
    {
        if (!$ids) {
            return [];
        }

        is_string($ids) && $ids = implode(',', $ids);
        $query = static::find()->where(['id' => $ids])->andWhere($condition);
        $indexBy && $query->indexBy($indexBy);
        $isArray && $query->asArray();

        return $query->all();
    }
}