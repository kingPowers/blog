<?php
namespace frontend\controllers;

/**
* 公共方法操作所在类
*/
class PublicController extends CommonController
{
	public function actionErrorPage ($request = "")
	{
		$exception = $this->app->errorHandler->exception;
		if ($exception->statusCode == '404') return $this->redirect(["/public/empty"]);
	}
	public function actionEmpty ()
	{
		return $this->render("empty");
	}
}