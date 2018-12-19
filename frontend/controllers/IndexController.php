<?php
namespace frontend\controllers;

/**
* 首页
*/
class IndexController extends CommonController
{
	public function init () 
	{
		parent::init();
	}
	public function actionIndex ()
	{
		$indexConfig = $this->getIndexConfig();//dump($indexConfig);
		$this->assign("banner",$indexConfig['banner']);
		$this->assign("topEssay",$indexConfig['top_article']);
		$this->assign("essayList",$this->essayList());
		$this->assign("notice",$indexConfig['notice']);
		$this->assign("labelList",$indexConfig['hot_labels']);
		$this->assign("hotList",$indexConfig['new_article']);
		$this->assign("linkList",$indexConfig['friendly_link']);
		return $this->render('index');
	}
	public function getIndexConfig()
	{
		$params['cmd'] = "Index/indexConfig";
		$serviceRes = $this->service($params);
		$data = ($serviceRes['errorCode'] === 0)?$serviceRes['dataresult']:[]; 
		return $data;
	}
	public function actionAbout ()
	{
		return $this->render("about");
	}
	public function actionText ()
	{
		$this->layout = false;
		$file = UPLOAD_PATH . '1.html';dump($file);
		if (!file_exists($file)) exit("file not exists");
		$a = include $file;
	}
	
	public function essayList ()
	{
		$params['cmd'] = "Article/list";
		//$params['label'] = '7';
		$serviceRes = $this->service($params);
		$data = ($serviceRes['errorCode'] === 0)?$serviceRes['dataresult']:[]; 
		return $data;
	}
	public function getBanner ()
	{
		$params['cmd'] = "Banner/indexList";
		$serviceRes = $this->service($params);
		$banner = ($serviceRes['errorCode'] === 0)?$serviceRes['dataresult']:[]; 
		return $banner;
	}
}