<?php
namespace backend\components\widgets;

/**
* 
*/
class ActiveForm extends \yii\widgets\ActiveForm
{
	
	public function init ()
	{
		parent::init();
		$this->fieldConfig = [
			'template' => "{input}\n{error}"//表单组建显示规则及顺序 原配置 {label}\n{input}\n{hint}\n{error}
		];
	}
}