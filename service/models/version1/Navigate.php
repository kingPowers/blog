<?php
namespace service\models\version1;

/**
* 
*/
class Navigate extends Base
{
	const RKEY_PRE = 'service:navigate';
	const REDIS_LIFE = 300;
	public $whereFilter = [
		'status' => ['=','status',"_value_"],
		'is_top' => ['=','is_top',"_value_"],
	];
	public static function tableName ()
	{
		return "{{navigate}}";
	}
	/**
	 * 首页导航列表
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-25T10:39:22+0800
	 * @return   [type]                   [description]
	 */
	public function indexApi ()
	{
		$rkey = static::RKEY_PRE . __METHOD__;
		$data = $this->redisGet($rkey,function () {
			$this->where['status'] = static::ENABLE_STATUS;
			$this->fields = ['title','url','models'];
			$list = $this->allList();
			return $list?:[];
		});
		return $data;
	}
}