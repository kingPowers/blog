<?php
namespace service\models\version1;

/**
* 标签管理
*/
class Label extends Base
{
	const HOT_STATUS = 2;const HOT_STATUS_NAME = "热门";
	const UNHOT_STATUS = 1;const UNHOT_STATUS_NAME = "非热门";
	const RKEY_PRE = 'service::label';
	public $whereFilter = [
		'status' => ['=',"status","_value_"],
		'is_hot' => ['=','is_hot',"_value_"],
	];
	static function tableName ()
	{
		return "{{label}}";
	}
	/**
	 * 热门标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-22T14:53:17+0800
	 * @return   [type]                   [description]
	 */
	public function hotLabels()
	{
		$rkey = self::RKEY_PRE . __METHOD__ .serialize(func_get_args());

		$data = $this->redisGet($rkey,function () {
			$where = [];
			$where[] = 'status=' . self::ENABLE_STATUS;
			$where[] = 'is_hot=' . self::HOT_STATUS;
			$where[] = 'id >= ((SELECT MAX(id) FROM label)-(SELECT MIN(id) FROM label)) * RAND() + (SELECT MIN(id) FROM label)';
			$fields = 'name,id';
			$sql = 'SELECT ' . $fields . ' FROM  label WHERE ' . implode(' AND ',$where) . ' LIMIT 10';
			//随机获取10条
			$command = \Yii::$app->db->createCommand($sql);
			$res = $command->queryAll();
			return $res?:[];
		},self::REDIS_LIFE);

		return $data;
	}
	/**
	 * 获取某个标签以及此标签下的所有子标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-29T10:29:09+0800
	 * @param    [type]                   $labelid [description]
	 * @return   [type]                            [description]
	 */
	public function getLabelTree ($labelid)
	{
		if (!$labelid || !$labelInfo = $this->getByid($labelid)) return [];

		$this->where = [
			'status' => self::ENABLE_STATUS,
		];
		$this->addDbWhere([
			'or',
			['=','level',$labelInfo['level'] . '-' . $labelInfo['id']],
			['like','level',$labelInfo['level'] . '-' . $labelInfo['id'] . '-' . '%',false]
		]);
		$this->fields = ['id','name'];
		$list = $this->allList();
		$pTree = [['id' => $labelInfo['id'],'name' => $labelInfo['name']]];
		$return = $list?array_merge($pTree,$list):$pTree;
		return $return;
	}
	/**
	 * 热门标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-29T14:54:45+0800
	 * @return   [type]                   [description]
	 */
	public function hotList ()
	{
		$this->where = [
			'status'	=>	self::ENABLE_STATUS,
			'is_hot'	=>	self::HOT_STATUS,
		];
		$db = self::find();
		$list = $this->allList($db);
		//$list[0]['name'] = $list[0]['name'].$this->getLastSql();
		return $list;
	}
}