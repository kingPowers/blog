<?php
namespace backend\models;

/**
* 首页导航管理
*/
class Navigate extends Common
{
	public $whereFilter = [
		'id'		=>	['=','id','_value_'],
		'status'	=>	['=','status','_value_'],
	];
	public $modelsArr;
	public static function tableName ()
	{
		return "{{navigate}}";
	}
	public function attributeLabels ()
	{
		return [
			'title'	=>	'导航名',
			'url'	=>	'链接',
			'models'=>	'锁定模块',
			'status'=>	'状态',
		];
	}
	public function rules ()
	{
		return [
			[['title','url','status'],'required','on' => [self::SCENARIO_ADD,self::SCENARIO_EDIT],'message' => "请填写或选择{attribute}"],
			[['modelsArr'],"checkModel",'on' => [self::SCENARIO_ADD,self::SCENARIO_EDIT]],
		];
	}
	public function scenarios ()
	{
		return [
			self::SCENARIO_ADD => ['title','url','models','status'],
			self::SCENARIO_EDIT => ['title','url','models','status'],
		];
	}
	public function add ($data)
	{
		if (false == $this->checkMyValidate($data,self::SCENARIO_ADD))return false;
		$this->models = implode(",", $data['modelsArr']);
		if (false == $this->save(false))return $this->error("添加首页导航失败");
		return $this->id;
	}
	public function edit ($navigateid,$data)
	{
		if (empty($navigateid) || (false == ($navigateModel = self::findOne($navigateid))))return $this->error("首页导航id错误");
		if (false == $this->checkMyValidate($data,self::SCENARIO_EDIT))return false;
		$navigateModel->scenario = self::SCENARIO_EDIT;
		$navigateModel->attributes = $data;
	    $navigateModel->models = implode(",", $data['modelsArr']);
		if (false == $navigateModel->save(false))return $this->error("修改首页导航失败");
		return true;
	}
	public function checkModel ($attribute,$params)
	{
		if (!is_array($this->modelsArr))$this->addError($attribute,"锁定模块格式错误【数组】");
		$models = self::getModels();
		foreach ($this->modelsArr as $value) {
			if (!in_array($value,$models))
				$this->addError($attribute,"锁定模块".$value."不存在");
		}
	}
	public static function getModels ()
	{
		$models = ['index','label','article'];
		return $models;
	}
}