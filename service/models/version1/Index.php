<?php
namespace service\models\version1;

/**
* 首页接口
*/
class Index extends Base
{
	/**
	 * [首页配置数据]
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-22T14:21:09+0800
	 * @return   [type]                   [description]
	 */
	public function indexConfigApi()
	{
		$data = [];
		$bannerModel = new Banner();
		$data['banner'] = $bannerModel->indexBanner();//banner
		$articleModel = new Article();
		$data['top_article'] = $articleModel->topArticle();//置顶文章
		$siteModel = new Site();
		$data['notice']  = $siteModel->siteNotice();
		$labelModel = new Label();
		$data['hot_labels'] = $labelModel->hotLabels();
		$data['new_article'] = $articleModel->newArticle(10);
		$data['friendly_link'] = $siteModel->friendlyLink();
		return $data;
	}

}