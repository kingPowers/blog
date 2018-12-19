<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
* 
*/
class UserAdmin extends Common implements IdentityInterface
{
	static function tableName ()
	{
		return '{{m_user}}';
	}
	public static function getUserInfoByIds ($user_ids,$condition = '1=1')
	{
		if (!$user_ids) return [];
		return static::find()->where(['id' => $user_ids])->andWhere($condition)->asArray()->indexBy('id')->all();
	}
	public static function findIdentity($id)
	{
		return static::findOne($id);
	}
	public static function findIdentityByAccessToken($token,$type = null)
	{
		return false;
	}
	public function getId ()
	{
		return $this->getPrimaryKey();
	}
	public function getAuthKey ()
	{
		return null;
	}
	public function validateAuthKey ($authKey)
	{
		return false;
	}
	static function getByUsername ($username)
	{
		if (!$username) return false;
		return static::findOne(['username' => $username]);
	}
	public function validatePassword ($password)
	{
		return \Yii::$app->security->validatePassword($password, $this->password);
	}
	public static function getUserIdsByNames ($name)
	{
		if (!$name) return [];
		return static::find()->andWhere(['like','names',$name])->asArray()->column();
	}
}