<?php

namespace backend\controllers;

use backend\models\Banner;
use yii\web\UploadedFile;
use \common\extra\Image;
use backend\models\Label;
use backend\models\Navigate;
use backend\models\Title;
use backend\models\BackFilter;
use yii\data\Pagination;
use yii\helpers\Url;

/**
 * 前台页面布局管理
 */
class LayoutController extends CommonController
{
    public $_key = [
        'banner' => ['id' => 'ID', 'title' => '标题']
        , 'label' => ['id' => 'ID', 'name' => '标签名']
    ];

    /*
    Banner管理
     */
    public function actionBanner()
    {
        $query = Banner::find();
        $where = BackFilter::whereRule('banner');
        $query->where($where);
        $page = new Pagination(['totalCount' => $query->count()]);
        $page->pageSize = $this->pageSize;
        $baseList = $query->limit($page->limit)->offset($page->offset)->asArray()->orderBy('id DESC')->all();
        $title = Title::getTitle('banner');
        $list = $this->handleList($baseList, $title, ['imageDomain' => Banner::$imageDomain]);
        return $this->render("banner", [
            'title' => $title
            , 'list' => $list
            , 'filter' => BackFilter::filterHtml('banner', ['status' => BackFilter::addAllSelect(Banner::$status), 'k' => $this->_key['banner']])
            , 'pages' => $page
        ]);
    }

    public function actionEditbanner()
    {
        if ($this->request->post("is_sub") == 1) return $this->editBanner($this->request->post());
        $bannerid = $this->request->get("bannerid");
        $banner = new Banner();
        $this->assign("status", $banner::getStatus());

        if ($bannerid) {
            $banner->where = ["id" => $bannerid];
            $bannerInfo = $banner->allList()[0];
            $this->assign("domain", $banner->imageDomain);
            $this->assign("bannerInfo", $bannerInfo);
            $this->assign("bannerid", $bannerid);
            return $this->render("editbanner");
        }

        return $this->render("addbanner");
    }

    public function editBanner($data)
    {
        $banner = new Banner();
        $bannerImg = UploadedFile::getInstanceByName('bannerImg');

        if ($bannerImg || !$data['bannerid']) {//上传并验证图片

            if (false == $banner->checkimg($bannerImg))
                return $this->ajaxError($banner->getError());

            $saveDir = $banner->savePath;

            if (!is_dir($saveDir)) {
                if (false == mkdir($saveDir, 0755)) return $this->ajaxError("创建目录失败");
            }

            $saveName = time() . rand(00, 99) . "." . $bannerImg->getExtension();
            $saveFile = $saveDir . $saveName;
            if (false == $bannerImg->saveAs($saveFile)) return $this->ajaxError("图片保存失败");

            //生成略缩图
            $thumbName = time() . rand(00, 99) . "_thumb." . $bannerImg->getExtension();
            $thumbFile = $saveDir . $thumbName;
            if (false == $uploadFile = Image::thumb($saveFile, $thumbFile, $bannerImg->getExtension(), 800, 335)) return $this->ajaxError("略缩图片保存失败");

            unlink($saveFile);
            $data['image'] = $thumbName;
        }

        if (!$data['bannerid']) {//新增
            if (false == $banner->add($data)) {
                return $this->ajaxError($banner->getError());
            }
        } else {
            if (false == $banner->edit($data['bannerid'], $data)) {
                return $this->ajaxError($banner->getError());
            }
        }

        return $this->ajaxSuccess("操作成功", $uploadFile);
    }

    /*
    标签管理
     */
    public function actionLabel()
    {
        $title = Title::getTitle('label');
        $query = Label::find();
        $where = BackFilter::whereRule('label');
        $query->where($where);
        $page = new Pagination(['totalCount' => $query->count()]);
        $page->pageSize = $this->pageSize;
        $baseList = $query->limit($page->limit)->offset($page->offset)->asArray()->orderBy('id DESC')->all();
        $list = $this->handleList($baseList, $title);
        return $this->render('label', [
            'pages' => $page
            , 'filter' => BackFilter::filterHtml('label', ['status' => BackFilter::addAllSelect(Label::$status), 'k' => $this->_key['label']])
            , 'lists' => $list
            , 'title' => $title
        ]);
    }

    public function actionEditlabel()
    {
        $labelid = $this->request->get("labelid") ?: $this->request->post('id');
        $labelModel = new Label();
        $labelModel->setScenario(Label::SCENARIO_ADD);
        $post = $this->request->post();

        if ($labelid) {
            $model = Label::findOne($labelid);
            $model->setScenario(Label::SCENARIO_EDIT);
            $model && $labelModel = $model;
        }

        if ($post && $labelModel->load($post) && $labelModel->validate()) {
            $labelModel->setLevel();
            if (false == $labelModel->save(false)) {
                return $this->redirectMessage('操作成功', Url::to(['layout/label']));
            }

            return $this->redirectMessage('操作失败', Url::to(['layout/label']));
        }

        return $this->render('edit-label', [
            'p_label' => array_merge(['0' => '一级标签'], Label::selectTree()),
            'status' => Label::$status,
            'hotStatus' => Label::getHotStatus(),
            'model' => $labelModel
        ]);
    }

    public function editlabel($data)
    {
        $labelModel = new Label();
        if ($data['labelid']) {
            $res = $labelModel->edit($data['labelid'], $data);
        } else {
            $res = $labelModel->add($data);
        }
        return $res ? $this->ajaxSuccess("操作成功") : $this->ajaxError($labelModel->getError());
    }

    /*
    首页导航
     */
    public function actionNavigate()
    {
        $title = Title::getTitle('navigate');
        $query = Navigate::find();
        $where = BackFilter::whereRule('navigate');
        $query->where($where);
        $page = new Pagination(['totalCount' => $query->count()]);
        $page->pageSize = $this->pageSize;
        $baseList = $query->limit($page->limit)->offset($page->offset)->asArray()->orderBy('id DESC')->all();
        $list = $this->handleList($baseList, $title);
        return $this->render('navigate', [
            'pages' => $page
            , 'filter' => BackFilter::filterHtml('navigate')
            , 'lists' => $list
            , 'title' => $title
        ]);
    }

    public function actionEditnavigate()
    {
        if ($this->request->post("is_sub") == 1) return $this->editnavigate($this->request->post());
        $navigateid = $this->request->get("navigateid");
        $navigateModel = new Navigate();
        $this->assign("status", $navigateModel::getStatus());
        $this->assign("models", $navigateModel::getModels());

        if ($navigateid) {
            $navigateModel->where = ['id' => $navigateid];
            $navigateInfo = $navigateModel->allList()[0];
            $this->assign("navigateInfo", $navigateInfo);
            return $this->render("editnavigate");
        }

        return $this->render("addnavigate");
    }

    public function editnavigate($data)
    {
        $navigateModel = new Navigate();
        if ($data['navigateid']) {
            $res = $navigateModel->edit($data['navigateid'], $data);
        } else {
            $res = $navigateModel->add($data);
        }
        return $res ? $this->ajaxSuccess("操作成功") : $this->ajaxError($navigateModel->getError());
    }

    /**
     * 处理列表数据
     * @param  [type] $list  列表数据
     * @param  [type] $title 列表标题
     * @return [type]        [description]
     */
    public function handleList($list, $title, $params = [])
    {
        if (empty($list) || empty($title)) return [];
        $return = [];

        foreach ($list as $dk => $detail) {
            foreach ($title as $tk => $tv) {
                $value = isset($detail[$tk]) ? $detail[$tk] : '';
                if ($tk == 'image') {
                    $value = $this->getImgHtml($params['imageDomain'] . $detail['image']);
                } elseif ($tk == 'operate') {
                    $value = $this->getOperate($detail);
                } elseif ($tk == 'hotStatus') {
                    $value = Label::getHotStatus($detail['is_hot']);
                } elseif ($tk == 'statusName') {
                    $value = Banner::$status[$detail['status']];
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
        if (strtolower($this->actionName) == 'banner') {
        }
        $operate_btn = ['edit' => "编辑"];
        $str = '';
        foreach ($operate_btn as $key => $value) {
            $str .= "<a class='operate' onclick='operate(\"" . $key . "\"," . $detail['id'] . ")'>" . $value . "</a>&nbsp&nbsp";
        }
        return $str;
    }

    public function filterWhere()
    {
        $where = [];
        $get = $this->request->get();
        //id
        if (isset($get['id']) && $get['id']) {
            $where[] = ['=', 'id', $get['id']];
        }
        //标题
        if (isset($get['title']) && $get['title']) {
            $where[] = ['like', 'title', $get['title']];
        }
        //置顶状态
        if (isset($get['top_status']) && $get['top_status']) {
            $where[] = ['=', 'top_status', $get['top_status']];
        }
        //置顶状态
        if (isset($get['location']) && $get['location']) {
            $where[] = ['=', 'location', $get['location']];
        }
        //类型
        if (isset($get['item_type']) && $get['item_type']) {
            $where[] = ['=', 'item_type', $get['item_type']];
        }
        return $where;
    }
}