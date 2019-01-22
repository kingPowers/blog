<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\Label;
use backend\models\ArticleContent;
use backend\models\Title;
use backend\models\BackFilter;
use yii\data\Pagination;
use common\services\ArticleService;
use backend\models\UserAdmin;
use yii\helpers\Url;

/**
 *
 */
class BlogController extends CommonController
{
    public $_key = ['article' => ['title' => '标题', 'names' => '作者',]];

    public function actionArticle()
    {
        $title = Title::getTitle('article');
        $query = Article::find();
        $query->where(BackFilter::whereRule('article'));
        $page = new Pagination(['totalCount' => $query->count('id')]);
        $page->pageSize = $this->pageSize;
        $query->select([
            'id', 'title', 'author', 'timeadd', 'lasttime', 'major_label', 'labels', 'is_top', 'is_new', 'status',
            'sort', 'fixed_sort',
            new \yii\db\Expression(Article::buildCaseSql('is_top', Article::$top_status, 'top_status')),
            new \yii\db\Expression(Article::buildCaseSql('is_new', Article::$new_status, 'new_status')),
            new \yii\db\Expression(Article::buildCaseSql('status', Article::$status, 'status_name'))
        ]);
        $baseList = $query->limit($page->limit)->offset($page->offset)->asArray()->orderBy('is_top DESC,id DESC')->all();
        $labelInfos = ArticleService::getLabelInfosFromArticle($baseList);
        $userInfos = UserAdmin::getInfoByIds(array_unique(array_filter(array_column($baseList, 'author'))), true, 'id');
        $list = $this->handleList($baseList, $title, ['label' => $labelInfos, 'user' => $userInfos]);
        $filterKeys = [
            'status' => BackFilter::addAllSelect(Article::$status)
            , 'new' => BackFilter::addAllSelect(Article::$new_status)
            , 'top' => BackFilter::addAllSelect(Article::$top_status)
            , 'label' => BackFilter::addAllSelect(Label::selectTree(['status' => Label::ENABLE_STATUS]))
            , 'k' => $this->_key['article']
        ];
        return $this->render("article", [
            'title' => $title
            , 'lists' => $list
            , 'filter' => BackFilter::filterHtml('article', $filterKeys)
            , 'pages' => $page
        ]);
    }

    public function actionEdit()
    {
        $id = ($this->request->post('is_edit') == 1) ? $this->request->post('id') : $this->request->get('id');
        $post = $this->request->post();
        $model = new Article();
        $model->setScenario(Article::SCENARIO_ADD);
        if ($id) {
            if ($postModel = Article::findOne($id)) {
                $model = $postModel;
                $model->setScenario(Article::SCENARIO_EDIT);
            } else {
                $model->backendError('此文章不存在');
            }

        }
        if ($post && $model->load($post) && $model->validate()) {
            $model->beforeEdit();
            if ($model->save(false)) {
                return $this->redirectMessage('操作成功', Url::to(['blog/article']));
            }
            return $this->redirectMessage('操作失败', Url::to(['blog/article']));
        }
        return $this->render('edit', [
            'model' => $model
            , 'labels' => BackFilter::addAllSelect(Label::selectTree())
            , 'vice_label' => $model->viceLabel
            , 'new_status' => Article::$new_status
            , 'top_status' => Article::$top_status
            , 'status' => Article::$status
        ]);
    }

    //置顶操作
    public function actionTop()
    {
        if ($this->request->post("is_top") != 1) return $this->ajaxError("参数错误");

        if (true !== ($res = ArticleService::changeTopStatus($this->request->post('id'))))
            return $this->ajaxError($res);
        return $this->ajaxSuccess('操作成功');
    }

    //启/禁用
    public function actionChangestatus()
    {
        if ($this->request->post('is_display') != 1) return $this->ajaxError('参数错误');

        $articleModel = new Article();
        if (false == $articleModel->changeStatus($this->request->post("articleid")))
            return $this->ajaxError($articleModel->getError());

        return $this->ajaxSuccess("操作成功");
    }

    //编辑文章内容
    public function actionContent()
    {
        if ($this->request->post("sub_content") == 1) return $this->subContent($this->request->post());
        $articleid = $this->request->get("articleid");
        $article = new Article();
        $articleModel = $article->getArticleById($articleid);

        $articleModel['content'] = ArticleContent::handContent($articleModel['content'], ['flip' => true]);
        $this->assign("articleInfo", $articleModel);
        return $this->render("content");
    }

    public function subContent($data)
    {
        if (!isset($data['content']) || empty($data['content']))
            return $this->ajaxError('文章内容不能为空');
        if (!isset($data['id']) || !($model = Article::findOne($data['id'])))
            return $this->ajaxError('文章不存在');
        $af_content = pickSubstr($data['content'], '<pre', '</pre>');
        $pick_strs = [];
        foreach ($af_content['pick_strs'] as $value) {
            $pick_strs[] = ['pick_key' => $value['pick_key'], 'pick_str' => ArticleContent::handContent($value['pick_str'])];
        }
        $content = recoverPickStr($af_content['af_pick'], $pick_strs);
        $model->content = $content;
        if (false == $model->save(false))
            return $this->ajaxError('保存失败');

        return $this->ajaxSuccess("保存成功");
    }

    public function actionContentbrowse()
    {
        $articleid = $this->request->get("articleid");
        $articleModel = Article::findOne($articleid);

        if ($articleModel)
            $this->assign("articleInfo", $articleModel->attributes);

        return $this->render("contentbrowse");
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
                switch ($tk) {
                    case 'operate':
                        $value = $this->getOperate($detail);
                        break;
                    case 'major_label_name':
                        $value = isset($params['label'][$detail['major_label']]) ? $params['label'][$detail['major_label']]['name'] : '';
                        break;
                    case 'article':
                        $value = "<a onclick=\"operate('content'," . $detail['id'] . ")\" class='operate'>查看</a>";
                        break;
                    case 'names':
                        $value = isset($params['user'][$detail['author']]) ? $params['user'][$detail['author']]['names'] : '';
                        break;
                    case 'label_list':
                        $labels = ArticleService::getViceLabels($detail['labels'], $params['label']);
                        $labels = array_column($labels, 'name');
                        $value = implode(',', $labels);
                        break;
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
        $token = \Yii::$app->request->csrfToken;
        $ajax_data = [
            'id' => $detail['id'],
            '_csrf-backend' => $token
        ];
        if (strtolower($this->actionName) == 'article') {
            $btn['edit'] = ['type' => 'url', 'name' => '编辑', 'url' => '/blog/edit/?id=' . $detail['id'], 'class' => 'operate', 'tin_name' => '', 'confirm' => ''];

            $btn['top'] = ['type' => 'operate', 'name' => ($detail['is_top'] == Article::TOP_STATUS) ? '取消置顶' : '置顶', 'url' => '/blog/top', 'confirm' => '是否操作', 'class' => 'operate',
                'ajax_data' => $this->getAjaxData(array_merge($ajax_data, ['is_top' => 1]))];
            $btn['content'] = ['type' => 'url', 'name' => '文章', 'class' => 'operate', 'url' => '/blog/content?articleid=' . $detail['id']];
            $btn['display'] = ['type' => 'operate', 'class' => 'operate', 'name' => ($detail['status'] == 1) ? '禁用' : '启用', 'url' => '/blog/changestatus', 'ajax_data' => $this->getAjaxData(array_merge($ajax_data, ['is_display' => 1]))];
        }

        $detail['id'] = implode('&&', ['id=' . $detail['id'], 'is_top=1']);
        return $this->createBtnHtml($btn, $detail);
    }

    public function getAjaxData($data)
    {
        $ajax_data = [];

        foreach ($data as $key => $value) {
            $ajax_data[] = $key . '=' . $value;
        }

        return implode('&&', $ajax_data);
    }

    public function createBtnHtml($btn = [], $data = [])
    {
        if (empty($btn) || empty($data)) return '';

        $str = '';

        foreach ($btn as $key => $value) {
            $value = $this->btnModel($value);
            if ($value['type'] == 'url') {
                $str .= '&nbsp;<a href="' . $value['url'] . '"  class="' . $value['class'] . '"  title="' . $value['name'] . '">' . $value['name'] . '</a>';
            } else {
                $str .= '&nbsp;<a href="javascript:;" class="' . $value['class'] . '" onclick="doOperate(\'' . $value['ajax_data'] . '\',\'' . $value['url'] . '\',\'' . $value['confirm'] . '\')" title="' . $value['name'] . '">' . $value['name'] . '</a>';
            }
        }

        return $str;
    }
}