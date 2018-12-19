<?php
namespace backend\models;

/**
* 后台用户管理
*/
class User extends Common
{
	const SCENARIO_LOGIN = 'login';
	private $_passwordKey = "@453sa&69";
	public $verify;
	public static function tableName () 
	{
		return "{{m_user}}";
	}
	 public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'verify' => "验证码"	
        ];
    }
	public function rules () 
	{
		return [
			[['username','password','verify'],'required','on' => self::SCENARIO_LOGIN,'message' => "{attribute}不能为空"],
			[['username'],'checkUsername','on' => 'add'],
			//[['verify'],'captcha','captchaAction' => '/public/captcha','message' => '验证码不正确'],	
		];
	}
	public function scenarios ()
	{
		return [
			self::SCENARIO_LOGIN	=>	['lasttime','login_count','username','password','lastip','verify'],//设置安全属性
			self::SCENARIO_ADD	=>	['username','password','mobile'],
			self::SCENARIO_EDIT	=>	['username','password','mobile'],
		];
	}
	public function add ($data)
	{
		$this->scenario = self::SCENARIO_ADD;
		$this->attributes = $data;

		if (false == $this->validate()) return false;

		$userInfo = self::findOne(['username' => $this->username]);

		if ($userInfo) 
			return $this->error("该用户名已注册");
	}
	public function edit ($userid,$data,$scenario = self::SCENARIO_EDIT)
	{
		if (false == $this->checkMyValidate($data,self::SCENARIO_EDIT)) return false;

		$userModel = self::findOne($userid);//dump($userModel);
		$userModel->scenario = $scenario;

		if (false == $userModel) 
			return $this->error("该用户尚未注册");

		$userModel->attributes = $data;

		if (false == $userModel->save(false)) 
			return $this->error("更新用户数据失败");

		return $userModel;	
	}
	/**
	 * 后台用户登陆操作
	 * @param  [type] $data 登陆数据
	 * @return [type]       [description]
	 */
	public function login ($data) 
	{
		if (false == $this->checkMyValidate($data,self::SCENARIO_LOGIN)) return false;

		$userInfo = self::findOne(['username' => $data['username']]);

		if (false == $userInfo) 
			return $this->error("该用户尚未注册");

		if ($this->encryptPassword($data['password']) !== $userInfo->password)
			return $this->error("密码错误");

		$data = array_merge(['login_count' => new \yii\db\Expression('login_count+1'),'lastip' => $this->request->userIP]);

		if (false == ($res = $this->edit($userInfo->id,$data,self::SCENARIO_LOGIN))) return false;

		\Yii::$app->session['user'] = $userInfo->attributes;
		return true;
	}
	/**
	 * 获取登录用户信息
	 * @param  string $name 信息字段名 例如：names username 
	 * @return [type]       [description]
	 */
	public static function getUserInfo ($name = '')
	{
		$userInfo = \Yii::$app->session['user'];
		return $name?$userInfo[$name]:$userInfo;
	}
	/**
	 * 用户密码加密
	 * @param  [type] $password 密码
	 * @return [type]           [description]
	 */
	public function encryptPassword ($password)
	{
		return md5("{$this->_passwordKey}|12345|{$password}");
	}
	/**
	 * 验证用户名
	 * @param  [type] $attribute [description]
	 * @param  [type] $params    [description]
	 * @return [type]            [description]
	 */
	public function checkUsername ($attribute,$params) 
	{
		if (false == preg_match("/^[0-9a-zA-Z]{6,20}$/", $this->username)) 
			$this->addError($attribute,"用户名6-20个数字或字母");
	}
}