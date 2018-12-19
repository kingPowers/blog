<?php
namespace backend\extend;

/**
* 
*/
class ListFilter
{
	public $k;
	public $request;
	public $strArr = [];
	public $params;
	public $validateKey = ['v','k','status'];
	public function __construct($request,$params)
	{
		$this->request = $request;
		$this->serializeParams($params);
	}
	protected function serializeParams ($params)
	{
		if (!is_array($params))return;
		foreach ($params as $key => $param) {
			if (!in_array())
		}
	}
	public function output ()
	{
		echo $this->tidyStr();
	}
	protected function tidyStr ()
	{
		$str = '';
		foreach ($this->statusArr as $value) {
			$str .= $value;
		}
		return $str;
	}
}