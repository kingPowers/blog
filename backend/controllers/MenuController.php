<?php

namespace backend\controllers;

use backend\models\Menu;
use backend\services\TitleService;

/**
 * 菜单管理
 */
class MenuController extends CommonController
{
    public function actionIndex()
    {
        $title = TitleService::getInstance()->getTitle('menu');
        $query = Menu::find();
//		$responseList = $this->menuModel->menuList();
//		$menuList = $this->menuList($responseList);
//		$list = $this->handleList($menuList,$this->title);
//		$this->assign("list",$list);
//		$this->assign("title",$this->title);
        return $this->render('index', [
            'title' => $title,
            'list' => []
        ]);
    }

    public function actionEdit()
    {
        if ($this->request->post("is_sub") == 1) return $this->editMenu($this->request->post());
        $menuModel = $this->menuModel;
        $menuModel->where = ['status' => $menuModel::ENABLE_STATUS, 'pid' => 0];
        $menuid = $this->request->get("menuid");
        $pList = $menuModel->allList();
        $this->assign("pList", $pList);
        $this->assign("status", $menuModel::getStatus());
        $this->assign("type", $menuModel::getType());
        if ($menuid) {
            $menu = new Menu();
            $menu->where = ['id' => $menuid];
            $menuInfo = $menu->allList()[0];
            $name = explode("-", $menuInfo['name']);
            $menuInfo['module'] = $name[0];
            $menuInfo['action'] = $name[1];//dump($menuInfo);
            $this->assign("menuInfo", $menuInfo);
            return $this->render("edit");
        }
        return $this->render('add');
    }

    /**
     * 编辑、增加菜单
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function editMenu($data)
    {
        if (empty($data['menuid'])) {
            $res = $this->menuModel->add($data);
        } else {
            $res = $this->menuModel->edit($data['menuid'], $data);
        }
        if ($res) {
            $this->ajaxSuccess("操作成功");
        }
        $this->ajaxError($this->menuModel->getError());
    }

    /**
     * 还原菜单归类
     * @param  [type] $list [description]
     * @return [type]       [description]
     */
    public function menuList($list)
    {
        $return = [];
        foreach ($list as $value) {
            $childs = $value['child'];
            unset($value['child']);
            $title = $value['title'];
            $value['title'] = "<font style='color:#0088cc'>" . $value['title'] . "</font>";
            $value['ptitle'] = $title;

            $return[] = $value;

            if (empty($childs)) continue;
            foreach ($childs as $child) {
                $child['ptitle'] = $title;
                $return[] = $child;
            }

        }
        return $return;
    }

    /**
     * 处理列表数据
     * @param  [type] $list  列表数据
     * @param  [type] $title 列表标题
     * @return [type]        [description]
     */
    public function handleList($list, $title)
    {
        if (empty($list) || empty($title)) return [];
        $return = [];

        foreach ($list as $dk => $detail) {
            foreach ($title as $tk => $tv) {
                $value = $detail[$tk];

                if ($tk == 'model') {
                    $value = explode("-", $detail['name'])[0] ?: '--';
                } elseif ($tk == 'action') {
                    $value = explode("-", $detail['name'])[1] ?: '--';
                } elseif ($tk == 'operate') {
                    $value = $this->getOperate($detail);
                }

                $return[$dk][$tk] = $value;
            }
            $return[$dk]['other']['id'] = $detail['id'];
        }

        return $return;
    }

    /**
     * 根据详情设置操作按钮
     * @param  array $detail 详情
     * @return [type]         [description]
     */
    public function getOperate($detail)
    {
        if ($detail['pid'] == 0) {
            $operate_btn = ['edit' => "编辑"];
        } else {
            $operate_btn = ['edit' => "编辑", 'up' => "上移", 'down' => "下移"];
        }
        $str = '';
        foreach ($operate_btn as $key => $value) {
            $str .= "<a onclick='operate(\"" . $key . "\"," . $detail['id'] . ")'>" . $value . "</a>&nbsp&nbsp";
        }
        return $str;
    }
}