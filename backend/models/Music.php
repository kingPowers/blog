<?php
namespace backend\models;

/**
* 音乐管理
*/
class Music extends Common
{
	public $whereFilter = [
		'status'	=>	['=','status','_value_']	
	];
	public $fileDomain = _STATIC_ . 'public/plugins/musicplay/music/'; 
	public static function tableName () 
	{
		return "{{music}}";
	}
	public function musicList()
	{
		$this->where['status'] = 1;
		$list = $this->allList();
		foreach ($list as $key => $value) {
			$list[$key]['url'] = $this->fileDomain . $value['file'];
		}
		return $list;
	}
}