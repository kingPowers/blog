<?php
namespace common\models;

Trait BaseModel 
{
	public $error;
	public function getError ($single = true)
	{
		$errors = $this->getErrors();
		if (false == $single) return $errors;
		$error = $this->firstArrValue($errors);
		if (empty($error)) $error = $this->error;
		return $error;
	}
	public function firstArrValue ($arr) 
	{
		if (!is_array($arr)) return $arr;
		return $this->firstArrValue(current($arr));
	}
}