<?php
namespace service\models\version1;

/**
* 
*/
class Banner extends Base
{
	const ENABLE_STATUS = 1;const ENABLE_NAME = '启用';
	const FORBID_STATUS = 2;const FORBID_NAME = '禁用';
	const RKEY_PRE = 'services:banner';
	const REDIS_LIFE = 60;
	public $whereFilter = [
		'status' => ['=','status',"_value_"],
	];
	public function init ()
	{
		$this->imageDomain = _UPLOAD_ . "/images/banner/";
	}
	public static function tableName ()
	{
		return "{{banner}}";
	}
	public function indexListApi ()
	{
		$rkey = self::RKEY_PRE . __METHOD__ . serialize(func_get_args());
		$data = $this->redisGet($rkey,function () {
			$db = self::find();
			$this->where['status'] = self::ENABLE_STATUS;
			$this->fields = ['id','image','title','url'];
			$list = $this->allList($db);

			return $list?:[];
		},self::REDIS_LIFE);
		
		return $data;
	}
	public function indexBanner ()
	{
		$rkey = self::RKEY_PRE . __METHOD__ . serialize(func_get_args());
		$data = $this->redisGet($rkey,function () {
			$db = self::find();
			$this->where['status'] = self::ENABLE_STATUS;
			$this->fields = ['id','image','title','url'];
			$list = $this->allList($db);

			return $list?:[];
		},self::REDIS_LIFE);
		
		return $data;
	}
	public function handleValue($value)
	{
		$value['image'] = $this->imageDomain . $value['image'];
		return $value;
	}
	public static function getStatus ($status)
	{
		$statusArr = [
			self::ENABLE_STATUS	=>	self::ENABLE_NAME,
			self::FORBID_STATUS	=>	self::FORBID_NAME,
		];
		return $status?$statusArr[$status]:$statusArr;
	}

}