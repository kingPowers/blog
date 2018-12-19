<?php
namespace frontend\controllers;

/**
* 首页
*/
class ArticleController extends CommonController
{
	public function init () 
	{
		parent::init();
	}
	public function actionDetail()
	{
		$params['cmd'] = "Article/detail";
		$params['article_id'] = $this->request->get('id');
		$serviceRes = $this->service($params);//dump($serviceRes);
		$this->assign('article',$serviceRes['dataresult']);
		return $this->render('detail');
	}		
}