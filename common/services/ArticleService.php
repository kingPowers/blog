<?php
namespace common\services;

use yii\base\Object;
use backend\models\Label;
use backend\models\Article;
/**
* 
*/
class ArticleService extends Object
{
	/**
	 * 根据文章列表获取全部的标签详情
	 * @DateTime 2018-11-12T15:43:05+0800
	 * @param    [type]                   $lists [文章列表]
	 * @return   [type]                          [description]
	 */
	public static function getLabelInfosFromArticle ($lists)
	{
		if (!$lists) return [];
		$major_ids = array_column($lists, 'major_label');
		$labels = explode(',',implode(',', array_column($lists,'labels')));
		$label_ids = array_filter(array_unique(array_merge($major_ids,$labels)));
		return Label::getInfoByIds($label_ids,true,'id');
	}
	public static function getViceLabels ($vice_label,$labels)
	{
		if (!$vice_label || !$labels) return [];
		if (is_string($vice_label)) $vice_label = explode(',', $vice_label);
		$return = [];
		foreach ($vice_label as $value) {
			if (isset($labels[$value]))
				$return[$value] = $labels[$value];
		}
		return $return;
	}
	public static function getMajorLabelTree ($label_id)
	{
		$labelTree = Label::labelTree($label_id);
		return array_column($labelTree, 'id');
	}
	public static function changeTopStatus ($articleId,$status = '')
	{
		if (!$articleId || !($model = Article::findOne($articleId)))
			return '文章不存在';

		if (!$status){
			$status = Article::TOP_STATUS;
			if ($model->is_top == Article::TOP_STATUS) $status = Article::UNTOP_STATUS;
		}
		$model->is_top = $status;
		if (!$model->save(false)) return '修改失败';
		return true;
	}
}