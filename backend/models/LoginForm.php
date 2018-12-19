<?php
namespace backend\models;

use yii\base\Model;

/**
* 
*/
class LoginForm extends Model
{
	use \common\models\BaseModel;
	public $username;
	public $password;
	public $verify;
	public $_user;
	public function attributeLabels ()
	{
		return [
			'username' => '用户名'
			,'password' => '密码'
			,'verify' => '验证码'
		];
	}
	public function rules ()
	{
		return [
			[['username','password','verify'],'required','message' => '请填写{attribute}']
			,['password','validatePassword']
			//,['verify','captcha','captchaAction' => '/public/captcha','message' => '验证码不正确'],
		];
	}
	public function validatePassword ($attribute,$params)
	{
		if (!$this->hasErrors()) {
			$user = $this->getUser();
			if (!$user)
				return $this->addError($attribute,'用户名错误');
			if (!$user->validatePassword($this->password))
				return $this->addError($attribute,'密码错误');
		}
	}
	public function login ()
	{
		if ($this->validate()) {
			return \Yii::$app->user->login($this->getUser(),60*60*24);
		} 	
		return false;
	}
	public function getUser ()
	{
		if ($this->_user) return $this->_user;
		$this->_user = UserAdmin::getByUsername($this->username);
		return $this->_user;
	}
}