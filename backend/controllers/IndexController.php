<?php
namespace backend\controllers;

/**
* 后台首页
*/
class IndexController extends CommonController
{
	public function actionIndex ()
	{
		$this->layout = "pageHeader";
		return $this->render("index");
	}
	public function actionText ()
	{
		$this->layout = false;
		return $this->render('text');
	}
	public function actionSystem ()
	{
		
	}
}