<?php

namespace backend\models;

/**
 *
 */
class Title
{
    static $un_addtime = [];
    static $un_lasttime = [];
    static $un_operate = [];

    public static function getTitle($type)
    {
        if (!$type) return [];
        $titles = self::title();
        if (!isset($titles[$type]) || !($title = $titles[$type])) return [];
        if (!in_array($type, self::$un_addtime)) $title['timeadd'] = '创建时间';
        if (!in_array($type, self::$un_lasttime)) $title['lasttime'] = '更新时间';
        if (!in_array($type, self::$un_operate)) $title['operate'] = '操作';
        return $title;
    }

    public static function title()
    {
        $title = [
            'banner' => [
                "image" => "图片", "title" => "标题", "url" => "链接", "statusName" => "状态"
            ],
            'label' => [
                'name' => "标签名", 'intro' => '介绍', 'hotStatus' => "是否热门", 'statusName' => "状态",
            ],
            'navigate' => [
                'title' => '导航名', 'url' => '链接地址', 'models' => '锁定模块', 'statusName' => '状态',
            ],
            'article' => [
                'id' => '文章ID', 'title' => '标题', 'article' => '文章内容', 'major_label_name' => '主标签', 'label_list' => '副标签',
                'sort' => '排序', 'fixed_sort' => '固定排序', 'top_status' => '置顶', 'new_status' => '最新', 'status_name' => '状态',
                'names' => '作者'
            ],
            'menu' => [
                'title' => '菜单标题', 'ptitle' => '分组', 'model' => "MODEL", 'action' => "ACTION",
                'statusName' => "状态",
            ]
        ];
        return $title;
    }
}