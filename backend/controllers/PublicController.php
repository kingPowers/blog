<?php
namespace backend\controllers;

use backend\models\User;
use yii\web\UploadedFile;
use backend\models\LoginForm;

/**
* 后台操作基类
*/
class PublicController extends BaseController
{
	public $layout = false;
	public function init ()
	{
		parent::init();
	}
	public function actions ()
	{
		return [
			'captcha'	=>	[
				'class'	=>	'yii\captcha\CaptchaAction',
				'minLength'	=> 4,
				'maxLength'	=>4,
			],
		];
	}
	// public function actionLogin ()
	// {	
	// 	if ($this->request->isAjax && $this->request->post("is_login") == 1) return $this->doLogin($this->request->post());
	// 	return $this->render("login");
	// }
	public function doLogin ($data)
	{
		$user = new User();
		if (false == $user->login($data)) return $this->ajaxError($user->getError());
		return $this->ajaxSuccess('登录成功');
	}
	public function actionGetjsmenu ()
	{
		$menuModel = new \backend\models\Menu();
		$menu = $menuModel->getIndexMenu();
		return $this->ajaxSuccess('请求成功',$menu);
	}
	public function actionText ()
	{
		$menu = new \backend\models\Menu();
		dump($menu->getIndexMenu());
	}
	public function actionUploadFile ()
	{
		if ($this->request->post('is_uploadfile') == 1) return $this->uploadfile ();
		$this->layout = 'header';
		return $this->render('upload-file',[
			'name' => 'sss',
		]);
	}
	public function uploadfile ()
	{
		$uploadfile = UploadedFile::getInstanceByName('uploadfile');
		$path = UPLOAD_PATH . $uploadfile->baseName . '.' . $uploadfile->extension;
		if (file_exists($path))
			unlink($path);
		if (false == $uploadfile->saveAs($path))
			return $this->ajaxError('上传失败');
		return $this->ajaxSuccess('success',UploadedFile::getInstanceByName('uploadfile'));
	}
	public function actionGetmusic ()
	{
		$musicModel = new \backend\models\Music;
		$list = $musicModel->musicList();
		return $this->ajaxSuccess('成功',$list);
	}
	public function actionLogin ()
	{
		if ($this->isLogin())
			return $this->goHome();
		$model = new LoginForm();

		$post = $this->request->post();
		if (isset($post['is_login']) && $post['is_login']) {
			$model->load($post);
			if (!$model->login()) {
				return $this->ajaxError($model->getError(),$model->getErrors());
			}
			return $this->ajaxSuccess('登录成功');
		}
		return $this->render('login',[
			'model' => $model
		]);
	}
}