<?php

namespace common\models;

/**
 * 标签管理
 */
class Label extends Common
{
    /*
     * 热门状态
     */
    const HOT_STATUS = 2;
    const UNHOT_STATUS = 1;
    public static $hotStatus = [
        self::HOT_STATUS => '热门',
        self::UNHOT_STATUS => '非热门'
    ];
    public $whereFilter = [
        'status' => ['=', 'status', '_value_'],
        'id' => ['=', 'id', '_value_'],
        'pid' => ['=', 'pid', '_value_'],
        'name' => ['like', 'name', "%_value_%", false],
        'hot' => ['=', "is_hot", "_value_"],
    ];

    public static function tableName()
    {
        return "{{label}}";
    }

    public function attributeLabels()
    {
        return [
            'name' => "标签名",
            'status' => '状态',
            'is_hot' => "热门状态",
            'intro' => '标签介绍'
        ];
    }

    public function rules()
    {
        return [
            [['name', 'status', 'is_hot', 'intro'], 'required', 'on' => [self::SCENARIO_ADD, self::SCENARIO_EDIT], 'message' => "请填写或选择{attribute}"],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_ADD => ['name', 'status', 'is_hot', 'pid', 'level', 'intro'],
            self::SCENARIO_EDIT => ['name', 'status', 'is_hot', 'pid', 'level', 'intro']
        ];
    }

    /**
     * 编辑/添加保存时设置level字段
     * @return int
     */
    public function setLevel()
    {
        if ($this->pid) {
            return $this->level = 0;
        }

        $pLabel = self::getInfoById($this->pid);
        if (!$pLabel) {
            return $this->level = 0;
        }

        $this->level = $pLabel['level'] . $this->pid . '-';
        if (substr($this->level, 0, 1) != '-') {
            $this->level = '-' . $this->level;
        }
    }

    /**
     * 标签按梯次 归类
     * @Author   Quanjiaxin
     * @DateTime 2018-06-21T15:23:52+0800
     * @param    [type]                   $data  [description]
     * @param    integer $pid [description]
     * @param    integer $depth [description]
     * @return   [type]                          [description]
     */
    public function classifyLabel($data, $pid = 0, $depth = 1)
    {
        if (empty($data)) return [];
        $return = [];
        foreach ($data as $key => $value) {
            if ($value['pid'] == $pid) {
                $value['name'] = str_repeat('----', count(explode('-', $value['level'])) - 1) . $value['name'];
                $return[] = $value;
                unset($data[$key]);
                if ($child = $this->classifyLabel($data, $value['id'], $depth++))
                    $return = array_merge($return, $child);
            }
        }
        return $return;
    }
}